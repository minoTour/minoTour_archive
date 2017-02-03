#!/usr/bin/python
# -*- coding: utf-8 -*-

# --------------------------------------------------
# File Name: mT_control.py
# Purpose:
# Creation Date: 09-06-2016
# Last Modified: Mon, Jan 16, 2017  3:06:50 PM
# Author(s): The DeepSEQ Team, University of Nottingham UK
# Copyright 2016 The Author(s) All Rights Reserved
# Credits:
# --------------------------------------------------


import sys,os
import time
import MySQLdb
import memcache
import numpy as np
import pandas as pd
import subprocess

sys.stdout = os.fdopen(sys.stdout.fileno(), 'w', 0)

sleeptime =10

# Execute a numbe of jobs every x period of time

# Requires variable from mt params.conf file
# 1. Read this in

f = open('mT_param.conf', 'r')

mT_params = {}
for line in f:
    k,v = line[:-1].split('=')
    mT_params[k] = v

#print mT_params


#-------------------------------------------------------------------------------
# Read command line options
import argparse

parser = argparse.ArgumentParser(description='mT_control')

parser.add_argument('-v', '--verbose', action="store_true", default=False)
parser.add_argument('-hb', '--heartbeat', action="store_true", default=False)
parser.add_argument('-d', '--development', action="store_true", default=False)
parser.add_argument('-t', '--twitter', action="store_true", default=False)


for p,v in mT_params.items():
    if p=="dbhost":
        parser.add_argument("-dbh", "--dbhost", action="store_true", default=v)
    if p=="dbpass":
        parser.add_argument("-pw", "--dbpass", action="store_true", default=v)
    if p=="dbuser":
        parser.add_argument("-dbu", "--dbusername", action="store_true", default=v)
parser.add_argument("-dbp", "--dbport", action="store_true", default=3306)

args = parser.parse_args()
#print args

#-------------------------------------------------------------------------------

class DB:
  conn = None

  def connect(self):
    #print "trying to connect to ",args.dbhost
    self.conn = MySQLdb.connect(host=args.dbhost, user=args.dbusername,
                         passwd=args.dbpass, port=args.dbport)
    #print "yay"

  def query(self, sql):
    try:
      #print "trying out ",sql
      cursor = self.conn.cursor()
      #print "Got here!"
      cursor.execute(sql)
      self.conn.commit()
      #print "here?"
    except (AttributeError, MySQLdb.OperationalError):
      self.connect()
      cursor = self.conn.cursor()
      #print "cursor type",type(cursor)
      cursor.execute(sql)
      self.conn.commit()
    #print "Return",type(cursor)
    return cursor

db = DB()

#-------------------------------------------------------------------------------
# UTILITIES

def getTable(db, sql, t):
    xs = runSQL(db, sql)
    colNames = [ x[0] for x in runSQL(db, "DESC "+t) ]
    df = pd.DataFrame(np.array(xs).T).T
    if len(xs)>0: df.columns = colNames
    #print t
    #print df.T
    #print "-"*80
    return df


def runSQL(db, sql):
    #print sql
    cur=db.query(sql)
    return cur.fetchall()

#-------------------------------------------------------------------------------

memc = memcache.Client([mT_params['memcache']], debug=1)
# Here we define a series of sub routines which will be run to write data to json and store it in memcache for access by the php scripts on the server. These will run at three different rates. Rapidly updating material will be written frequently (every 10 seconds), intermediate datasets every 60 seconds and complex analysis every 180 seconds. We will write a second set of subroutines to manipulate data from table to table whilst still keeping results available in memcache.

#As standard we pass variables as database_name,
def jobs(args, mT_params, dbname, jobname, reflength, minupversion):
        checkvar = dbname + jobname
        checkrunning = dbname + jobname + "status"
        checkingrunning = memc.get(checkrunning)
        try:
             checking = memc.get(checkvar)
             if args.verbose is True:
                        print "already running checkvar"
        except:
        #if not checkingrunning is None:
                if args.verbose is True:
                        print "replacing checkvar"

                ##At the moment waits for script to complete before calculating next - need to check if process still running and not execute new version until it has finished...
                params = "dbname=%s jobname=%s reflength=%s prev=0 minupversio=%s &" % (dbname , jobname , reflength , minupversion)
                command = mT_params['phploc'] \
                    + "php mT_control_scripts.php " \
                    + params
                if args.verbose is True:
                    print command

                subprocess.Popen(command, shell=True)


#-------------------------------------------------------------------------------
# Create two arrays of jobs to run thru

# Define an array with a list of tasks that need to be completed for each database if the reference length is greater than 0
alignjobarray = ["depthcoverage","percentcoverage","readlengthqual","readnumberlength","mappabletime"]

# Define an array of jobs regardless of reflength
jobarray = ["readnumber","maxlen","avelen","bases","histogram","histogrambases","reads_over_time2","average_time_over_time2","active_channels_over_time","readsperpore","average_length_over_time","lengthtimewindow","cumulativeyield","sequencingrate","ratiopassfail","ratio2dtemplate","readnumber2"]
#"whatsinmyminion2"

# Define an array of characters to print to the screen as a heartbeat...
heartbeat = [".","!","#","!"]
heartcount=0
# This is our master loop which will run endlessley checking for changes to the databases;

#-------------------------------------------------------------------------------

while 42:
# If you have to ask the significance of 42 you shouldn't be reading computer code.
        memc.set("perl_mem_cache_connection"
                    , "We are fully operational.",sleeptime)
        #print "."
        #Build in a sleep time to stop the processor going mental on an empty while loop... This number should be set fairly long on the production verion...
        time.sleep(sleeptime)
        #print ">>>>", args.verbose, type(args.verbose)
        if not args.verbose is True:
                sys.stdout.write(heartbeat[heartcount])
                sys.stdout.write('\r')
        heartcount+=1
        if heartcount == 4:
                heartcount = 0

#-------------------------------------------------------------------------------
        # Run the twitter script to send background notifications
        if args.twitter is True:
               command = mT_params['phploc'] \
                                + "php " \
                                + mT_params['directory'] \
                                + "views/alertcheck_background.php"
               print command
               subprocess.Popen(command, shell=True)

#-------------------------------------------------------------------------------

        # Query the database to see if there are any active minION runs that need processing
        query = "SELECT * FROM Gru.minIONruns where activeflag = 1;"
        results_df = getTable(db, query, 'Gru.minIONruns')
        numRows,numCols = results_df.shape # runname[0]
        ref = results_df
        if numCols>0:
            print "Active Runs: "
            for r in ref['runname']: print "\t"+r
        else: print "No Active Runs."

#-------------------------------------------------------------------------------
        #Loop through results and if we have any, set a memcache variable containing a list of database names:
        run_counter = 0 # Set counter for number of active runs.
        if numCols>0:
            for x in xrange(numRows):
                run_counter+=1

                if args.verbose is True:
                    print str(run_counter) \
                         + "\t" + ref.runname[x]
                runname = "perl_active_" + str(run_counter)
                print runname,ref.runname[x]
                memc.set(runname, ref.runname[x], sleeptime)


                #for j in jobarray:
                #        #print j
                #        jobs(args, mT_params
                #            , ref.runname[run_counter]
                #            , j
                #            , ref.reflength[run_counter]
                #            , ref.minup_version[run_counter]
                #            )

                if ref.reflength[x] > 0:
                        #for j in alignjobarray:
                        #    jobs(args, mT_params
                        #        , ref.runname[run_counter]
                        #        , j
                        #        , ref.reflength[run_counter]
                        #        , ref.minup_version[run_counter]
                        #        )
                        ##proc_align($ref->{runname},$dbh);
                        #aligncommand = "c:/Perl64/bin/perl win_mT_align.pl " + ref.runname[run_counter] #+ " &"
                        aligncommand = "perl mT_align.pl " + ref.runname[x] #+ " &"
                        #aligncommand = "python mT_coverage.py " + ref.runname[run_counter] #+ " &"
                        if args.verbose is True:
                            print "ALIGNCOMMAND: ", aligncommand
                        subprocess.Popen(aligncommand, shell=True)
                if args.verbose is True:
                        print "Executed..."
        #print run_counter
        memc.set("perl_proc_active", str(run_counter) ,sleeptime);
