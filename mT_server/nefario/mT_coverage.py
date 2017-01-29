#!/usr/bin/python
# -*- coding: utf-8 -*-

# --------------------------------------------------
# File Name: mT_coverage.py
# Purpose:
# Creation Date: 10-06-2016
# Last Modified: Sun, Jan 15, 2017 10:55:48 PM
# Author(s): The DeepSEQ Team, University of Nottingham UK
# Copyright 2016 The Author(s) All Rights Reserved
# Credits:
# --------------------------------------------------

import sys,os
import time
import MySQLdb
import memcache
import numpy as np
from pandas.io.sql import read_sql, to_sql
import pandas as pd
import re
#import mysql.connector
#from sqlalchemy import create_engine


import warnings
warnings.filterwarnings("ignore")

ROWLIMIT = "100"


verbose = True

dbname = sys.argv[1]

# No buffering on stdout ...
sys.stdout = os.fdopen(sys.stdout.fileno(), 'w', 0)



def output(s):
    if verbose is True: print s
def output2(s,s_):
    if verbose is True: print s, s_

#-------------------------------------------------------------------------------
# Utility Functions ....

def executeSQL(sql, conn):
    output(sql)
    cursor = conn.cursor()
    try:
        cursor.execute(sql)
    except Exception,e:
        print "EXCEPTION", sql, str(e)
        print "... trying again..."
        time.sleep(1)
        #executeSQL(sql, conn)
        cursor.execute(sql)



def incrementHash(_hash, valsarray, refid, refid_, posn, base):

        if refid_ not in _hash.keys(): # == []:
            _hash[refid_]={}


        if posn not in _hash[refid_].keys():
            _hash[refid_][posn]={}
            _hash[refid_][posn]['reference'] = base
            for val in valsarray:
                _hash[refid_][posn][val] = 0


        _hash[refid_][posn][base] +=1

        return _hash

def hash2array(d, valsarray):
    array = []
    for refid in sorted(d.keys())  :
       for posn in sorted(d[refid].keys()):
            row = [refid, posn]
            for val in valsarray:
                row.append(d[refid][posn][val])
            array.append(row)
    return np.array(array)


#-------------------------------------------------------------------------------
# Pandas dataframe utils ...

def numRows(df):
    if type(df) is str: output2("DF", df)
    return df.shape[0]

def numCols(df):
    return df.shape[1]

def selectDF(qry, conn):
    output(qry)
    df = read_sql(qry, conn)
    output(df[:2].T)
    output("-"*80)
    return df

def array2frame(array, colNames, indexes):
    df = pd.DataFrame(array)
    output(colNames)
    if len(df.columns) >1 :
        df.columns = colNames
        df = df.set_index(indexes)
    output(df)
    return df


def insertDF(df, tname, autoinc, conn):
  output("Inserting into: %s ... " % (tname))
  sys.stdout.flush()
  if numRows(df) != 0:
     try:
        if autoinc is True:
            df.to_sql(con=conn, name=tname, if_exists='append', flavor='mysql', index=False)
        else:
            df.to_sql(con=conn, name=tname, if_exists='append', flavor='mysql')
     except Exception,e:
        print "EXCEPTION", tname, str(e)
        sys.exit()

#-------------------------------------------------------------------------------
# Cigar Processing ...

def processCigar(ref, i):

    qname=ref.qname[i]
    flag=ref.flag[i]
    rname=ref.rname[i]
    pos=ref.pos[i]
    mapq=ref.mapq[i]
    cigar=ref.cigar[i]
    seq=ref.seq[i]
    m_d=ref.m_d[i]
    rstring=""
    qstring=""

    q_pos=0
    r_pos=pos-1
    q_array = []
    r_array = []
    q_string=""

    cigpatsA = re.findall('[^A-Z]+', cigar)
    cigpatsB = re.findall('[A-Z]+', cigar)
    cigparts = zip(map(int, cigpatsA), cigpatsB)
    readbases=ref.seq[i]

    for cigarpartbasecount, cigartype in cigparts:
        ####
        if cigartype == "S":
        # not aligned read section
            q_pos += cigarpartbasecount

        if cigartype == "M":
        # so its not a deletion or insertion. Its 0:M
            q_array += readbases[q_pos:q_pos+cigarpartbasecount]
            r_array += "X"*cigarpartbasecount
            q_pos += cigarpartbasecount
            r_pos += cigarpartbasecount

        if cigartype == "I":
            q_array += readbases[q_pos:q_pos+cigarpartbasecount]
            r_array +=  "-"*cigarpartbasecount
            q_pos += cigarpartbasecount

        if cigartype == "D":
            q_array += "-"*cigarpartbasecount
            r_array += "o"*cigarpartbasecount
            r_pos += cigarpartbasecount

    q_array = list(''.join(q_array))
    r_array = list(''.join(r_array))

    for j in xrange(len(r_array)):
        if q_array[j] != "-" and  r_array[j] != "-":
            r_array[j] = q_array[j]

    a=0
    mdparts=re.split('(\d+)|MD:Z:', m_d)
    for m in mdparts:
        if m is True:
            if 1:
                m = "~/^\^(.+:/:"
                tmp = m
                for x in xrange(len(tmp)):
                    r_array[x]=tmp[x]

            else:
              if m in ["A","T","C","G"] :
                if r_array[a] == "-":
                    while r_array[a] == "-" :
                        a+=1

                r_array[a]=m
                a+=1

              else:
                 if  m == int(m) :
                    for j in xrange(m):
                        if r_array[a+j:] == "-":
                            while r_array[a+j] == "-" :
                                a+=1

                    a=a+m

    return q_array, r_array

#-------------------------------------------------------------------------------
# Convert SQL results table/dataframe to Hash ....
# Collating coverage data ....

def processDF(bc, ref, checkreads, tbl_check_barcode, memd):
    _hash={}

    for i in xrange(numRows(ref)):

        # Process the Cigar ...
        q_array, r_array = processCigar(ref, i)

        # Process ref attributes ...
        refid = ref.refid[i]

        if bc=="barcode_" and numRows(tbl_check_barcode)>0:
           output2("We have spotted a barcode", ref.barcode_arrangement[i])
           try:
              refid_ = str(refid)+"_"+ref.barcode_arrangement[i]
              output(refid_)
           except:
              refid_ = str(refid)+"_UC"
        else: refid_ = refid

        flag=ref.flag[i]
        refstart = (ref.pos[i])-1

        basenameid = ref.basename_id[i]

        qstring=''.join(q_array)
        rstring=''.join(r_array)
        refstring_orig = rstring
        refstring = refstring_orig
        querystring_orig = qstring
        querystring = querystring_orig

        output2("The ref id is ",refid )

        reflength = len(refstring_orig)

        genreflength = len(ref.seq[i])


        output2("The reflength is ", genreflength )
        # Need to get the position in the reference.
        # posn = refstart - 1

        # We need to fix the situation where we are mapping reversed reads.
        # So we need to look at the flag ...

        posn=0

        if flag == 0 or flag == 2048:
            posn = refstart ##Idiot fixes
        else:
            #posn =     genreflength - refstart
            posn = refstart ##Idiot fixes
        output2("The refstart is ", refstart )
        output2("The reflength is" , reflength)
        output2("The refstring is",refstring)
        for x in xrange(reflength):
            if refstring[x] != "-":
                posn+=1

            # Check if the strings match ...
            inc = lambda x: incrementHash(_hash, valsarray, refid, refid_, posn, x)

            if refstring[x] == querystring[x]:
                 _hash = inc(refstring[x])
            elif refstring[x] == "-":
                 _hash = inc("i")
            elif querystring[x] == "-":
                 _hash = inc("d")
            else:
                 _hash = inc(querystring[x])

        output2("378 The refend is ", posn )

        if verbose is True and posn > 49000:  output("OH DEAR!")
        output2("Flag is ", flag  )


        memd.set(checkreads,ref.ID )
    return _hash

#-------------------------------------------------------------------------------

def quote(dbname, tname):
    return "'" + dbname + "'.'" + tname + "'"

def createCoverageTable(dbanme, tname, conn):

    #tname_ = quote(dbname, tname)

    sql = \
        '''
        CREATE TABLE IF NOT EXISTS ''' + tname + '''
            (
                `ref_id` TEXT NOT NULL,
                `ref_pos` INT NOT NULL,
                `ref_seq` TINYTEXT NOT NULL,
                `A` INT,
                `T` INT,
                `G` INT,
                `C` INT,
                `D` INT,
                `I` INT,
                PRIMARY KEY (`ref_id`(20),`ref_pos`)
            )
        CHARACTER SET utf8
        '''

    executeSQL(sql, conn)

#def dropTrigger(tname):
#      return "DROP TRIGGER IF EXISTS " + tname + "_trigger"

def mkTriggerTable(dbname, target, conn):

    tmp = target + "_tmp"
    #tmp_ = quote(dbname, tmp)
    #target_ = quote(dbname, target)

    trigger_name = tmp + "_trigger"

    cond = " ref_id = NEW.ref_id and ref_pos = NEW.ref_pos"

    trigger = \
       '''
       CREATE TRIGGER ''' + trigger_name + ''' AFTER INSERT ON ''' + tmp + '''
        FOR EACH ROW
            BEGIN
             IF NOT EXISTS (SELECT 1 FROM ''' + target + ''' WHERE ''' + cond + ''' ) THEN
               INSERT INTO ''' + target + ''' (ref_id, ref_pos, ref_seq, A, T, C, G, D, I) VALUES
                 (NEW.ref_id, NEW.ref_pos, NEW.ref_seq,  NEW.A, NEW.T, NEW.C, NEW.G, NEW.D, NEW.I);
             ELSE
               UPDATE ''' + target + ''' SET
                        A = A + NEW.A ,
                        T = T + NEW.T ,
                        C = C + NEW.C ,
                        G = G + NEW.G ,
                        D = D + NEW.D ,
                        I = I + NEW.I
                             WHERE ''' + cond + ''' ;
             END if;
            END
        '''


    # Test if table exists ...
    sql = "SHOW TABLES LIKE '" + tmp +"'"
    df = selectDF(sql, conn)
    if numRows(df) == 0:

        # Create table ...
        createCoverageTable(dbname, target, conn)
        createCoverageTable(dbname, tmp, conn)

        # Add trigger ...
        executeSQL(trigger, conn)


#-------------------------------------------------------------------------------
# Process records and insert _hash into database tables ....

def processTable(bc, table, readtype, checkreads, tbl_check_barcode, conn, memd):


  if numCols(table)>0:
      if 1: # try:

        _hash = processDF(bc, table, checkreads, tbl_check_barcode, memd)

        # Convert hash to array ...
        array = hash2array(_hash, valsarray)

        # Convert array to dataframe ...
        colNames=['ref_id','ref_pos','ref_seq'
                    ,'A','T','G','C','i','d']
        indexes = colNames[:2]
        df = array2frame(array,colNames,indexes)

        tname = "reference_coverage_" + bc + readtype+ "_tmp"

        # Ensure tmp table is empty...
        sql = "DELETE FROM "+tname
        executeSQL(sql, conn)

        # Insert dataframe into db table ...
        insertDF(df, tname, False, conn)


        # Book keeping ...
        df = table[['basename_id']]

        tname = "read_tracking_" + bc + readtype
        insertDF(df, tname, True, conn)

        tname = "read_tracking_pre_" + readtype
        insertDF(df, tname, True, conn)

        output("-"*80)

      #except: pass

def processBarcodeCoverageData(dbname, readtype, tabletype, checkreads, tbl_check_barcode, conn, memd):

    if numRows(tbl_check_barcode)>0:
        if tabletype == "last_align_maf_basecalled_template":
            output("parsing barcodes")

            t1 = dbname+".last_align_maf_basecalled_"+readtype
            t2 = dbname + ".barcode_assignment"
            t3 = dbname + ".read_tracking_barcode_" + readtype
            q1 = "select basename_id from " + t3
            query = "SELECT * FROM " + t1 + \
                         " LEFT JOIN " + t2 + \
                         " USING (basename_id) where \
                            basename_id NOT IN ("+ q1 + ") \
                            AND alignnum = 1 " + \
                            "ORDER BY ID LIMIT " + ROWLIMIT
        else:
          if tabletype == "align_sam_basecalled_template":
            t1 = dbname + ".align_sam_basecalled_" + readtype
            t2 = dbname + ".reference_seq_info"
            t3 = dbname + ".barcode_assignment"
            t4 = dbname + ".read_tracking_barcode_" + readtype
            q1 = "select basename_id from " + t4

            query = "SELECT * FROM " + t1 + \
                        " INNER JOIN " + t2 + \
                        " LEFT JOIN " + t3 + \
                        " USING (basename_id) WHERE \
                        rname = refname AND \
                        flag != '2048' AND \
                        flag != '2064' AND \
                        basename_id NOT IN ("+q1+")" + \
                        " ORDER BY ID LIMIT " + ROWLIMIT

        output(query)
        try:
            table = selectDF(query, conn)
        except:
            table = pd.DataFrame()

        processTable("barcode_", table, readtype, checkreads, tbl_check_barcode, conn, memd)
        output("="*80)


#-------------------------------------------------------------------------------
# Translate MAF or SAM alignments into a reference coverage plot data...
# are we dealing with SAM or MAF formatted data?

def processCoverageData(dbname, readtype, tabletype, checkreads, conn, memd):

    if tabletype == "last_align_maf_basecalled_template":
    # Process LAST align ....

        ## Note that we need to deal with multiply aligned sequences still
        ## - could do using the alignnum=1 but it doesn't really work...

        t1 = dbname + ".last_align_maf_basecalled_" + readtype
        t2 = dbname + ".read_tracking_" + readtype
        q1 = "select basename_id from " + t2
        query = "SELECT * FROM " + t1 + " where \
                    basename_id NOT IN (" + q1 + ") AND \
                    alignnum = 1 " + \
                    " ORDER BY ID LIMIT " + ROWLIMIT

    else:
      if tabletype == "align_sam_basecalled_template":

      # Process SAM align  ....

        # Note that we need to deal with multiply aligned
        # sequences still - could do using the alignnum=1
        # but it doesn't really work...

        t1 = dbname + ".align_sam_basecalled_" + readtype
        t2 = dbname + ".reference_seq_info"
        t3 = dbname + ".read_tracking_" + readtype
        q1 = "select basename_id from "+ t3
        query = "SELECT * FROM " + t1 + \
                    " INNER JOIN " + t2 + \
                    " WHERE refname=rname AND \
                        flag != '2048' AND \
                        flag != '2064' AND \
                        basename_id NOT IN (" + q1 + ")" + \
                        " ORDER BY ID LIMIT " + ROWLIMIT

    try:
        table = selectDF(query, conn)
    except:
        table = pd.DataFrame()

    processTable("", table, readtype, checkreads, None, conn, memd)

    output("="*80)

#-------------------------------------------------------------------------------
# OK Main code starts here ....

valsarray=('reference','A','T','G','C','i','d')


def main():

    output("mT_align.py")

    if len(sys.argv) >2:
        output("This script only requires 1 database name variable.")
        #sys.exit()

    #-------------------------------------------------------------------------------
    # Import variables from mT_param.conf (global parameters) ...

    f = open('mT_param.conf', 'r')

    mT_params = {}
    for line in f:
        k,v = line[:-1].split('=')
        mT_params[k] = v

    output(mT_params)

    #-------------------------------------------------------------------------------
    # Set up a connection to memcache to upload data and process stuff

    memd = memcache.Client([mT_params['memcache']], debug=1)

    checkvar = dbname + "alignmax"
    checkrunning = dbname + "alignmax" + "status"
    checkingrunning = memd.get(checkrunning)
    #checking = memd.get(checkvar)
    readtypes = ("template","complement","2d")


    #-------------------------------------------------------------------------------
    # Setup Database connections ...

    # for pandas-0.1.9.1  ...
    #engine = create_engine('mysql+mysqlconnector://[user]:[pass]@[host]:[port]/[schema]', echo=False)
    #engine = create_engine('mysql+mysqlconnector://'+mT_params['dbuser']+':'+mT_params['dbpass']+'@'+mT_params['dbhost']+'/'+dbname, echo=False)

    try:
        conn = MySQLdb.connect(host = mT_params['dbhost'],
                                user = mT_params['dbuser'],
                                passwd = mT_params['dbpass'],
                                db = dbname)
        '''
        conn = engine
        '''

    except MySQLdb.Error, e:
         output("Error %d: %s" % (e.args[0], e.args[1]))
         sys.exit (1)


    # TODO FOCUS HERE ....
    if checkingrunning is None or not checkingrunning == "1" :
        output("Checkingrunning is not set ...")
        output("-"*80)
        memd.set(checkrunning,"1")
    #else:

        # 1. We want to check if this is a barcoded run.
        # If it is we need to run a special barcoding mapping algorithm
        sql = "SELECT table_name FROM information_schema.tables \
                WHERE table_schema = '" + dbname + "' \
                AND table_name = 'barcode_assignment'"
        tbl_check_barcode = selectDF(sql, conn)

        '''
        # 2. We want to check if this database contain prebasecalled analysis.
        # If it does we're going to need to create some tables and process this data.
        sql = "SELECT table_name FROM information_schema.tables \
                WHERE table_schema = '" + dbname + "' \
                AND table_name = 'pre_tracking_id'"
        tbl_check_presquiggle = selectDF(sql, conn)
        '''

        # 3. We have to check if the last_align_maf_basecalled table exists.
        # If it doesn't then we don't want to run this again.
        sql = "SELECT table_name FROM information_schema.tables WHERE \
                table_schema = '" + dbname + "' AND \
                (table_name = 'last_align_maf_basecalled_template' \
                    or table_name = 'pre_align_template' \
                    or table_name = 'align_sam_basecalled_template')"
        tbl_check_mode = selectDF(sql, conn)

        #--------------------------------------------------------------------
        # Create Tables ....
        output("Creating Tables ... ")

        if numRows(tbl_check_mode)==0:
          output("We don't have a table")
        else:
          output("We do have a table")
          for tabletype in tbl_check_mode['table_name']:

                output2("tabletype ", tabletype)

                for readtype in readtypes :


                    # read_tracking ...
                    sql = "CREATE TABLE IF NOT EXISTS `" + \
                        dbname + "`.`read_tracking_" + readtype + "` (  \
                       `readtrackid` INT NOT NULL AUTO_INCREMENT, \
                       `basename_id` INT NOT NULL, \
                       PRIMARY KEY (`readtrackid`) ) CHARACTER SET utf8"
                    executeSQL(sql, conn)

                    # reference_coverage_ ...
                    target = "reference_coverage_" + readtype
                    mkTriggerTable(dbname, target, conn)



                    # read_tracking_barcode_ ...
                    sql = "CREATE TABLE IF NOT EXISTS `" + \
                        dbname + "`.`read_tracking_barcode_" + readtype + "` ( \
                       `readtrackid` INT NOT NULL AUTO_INCREMENT, \
                       `basename_id` INT NOT NULL, \
                       PRIMARY KEY (`readtrackid`) ) CHARACTER SET utf8"
                    executeSQL(sql, conn)

                    # reference_coverage_barcode_ ...
                    target = "reference_coverage_barcode_" + readtype
                    mkTriggerTable(dbname, target, conn)


                    # checkreads ...
                    checkreads = dbname + "checkreads" + readtype
                    output2("replacing checkvar ",readtype)
                    try: checkreadsval = memd.get(checkreads)
                    except:
                        output("FAIL 252")

                    output("The value of barcode check is %d for run %s at %s" \
                                % (numRows(tbl_check_barcode), dbname, readtype))


                    # OK. Lets build the coverage tables ...
                    processCoverageData(dbname, readtype, tabletype, checkreads, conn, memd)
                    processBarcodeCoverageData(dbname, readtype, tabletype, checkreads, tbl_check_barcode, conn, memd)

        memd.delete(checkrunning)

# main
if __name__ == '__main__':
    main()
