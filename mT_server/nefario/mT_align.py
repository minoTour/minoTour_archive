#!/usr/bin/python
# -*- coding: utf-8 -*-

# --------------------------------------------------
# File Name: mT_align.py
# Purpose:
# Creation Date: 10-06-2016
# Last Modified: Thu, Oct  6, 2016  2:44:46 PM
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


import warnings
warnings.filterwarnings("ignore")


dbname = sys.argv[1]

# No buffering on stdout ...

sys.stdout = os.fdopen(sys.stdout.fileno(), 'w', 0)

verbose = False # True
def output(s):
    if verbose is True: print s
def output2(s,s_):
    if verbose is True: print s, s_

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
checking = memd.get(checkvar)
readtypes = ("template","complement","2d")


#-------------------------------------------------------------------------------
# Setup Database connections ...

try:
    conn = MySQLdb.connect(host = mT_params['dbhost'],
                            user = mT_params['dbuser'],
                            passwd = mT_params['dbpass'],
                            db = dbname)
except MySQLdb.Error, e:
     output("Error %d: %s" % (e.args[0], e.args[1]))
     sys.exit (1)

cursor = conn.cursor()

#-------------------------------------------------------------------------------
# Utility Functions ....

def executeSql(sql):
    output(sql)
    try: cursor.execute(sql)
    except: pass


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

def hash2array(d, valsArray):
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
    if type(df) is str:
        output2("DF", df)
        #return 1
    return df.shape[0]
def numCols(df): return df.shape[1]

def selectDF(qry):
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

def insertDF(df, tname):
  output("Inserting into: %s ... " % (tname))
  if numRows(df) != 0:
    try:
        df.to_sql(con=conn, name=tname, if_exists='append', flavor='mysql')
        #print tname
        #print df.T
        #sys.stdout.flush()
        print "... %d rows inserted." % (numRows(df) )
        #df = selectDF("SELECT * FROM " + tname)
        #print df.T
        #print "="*80
        #sys.stdout.flush()
    except Exception,e:
        print "EXCEPTION", tname, str(e)
        #sys.exit()

#-------------------------------------------------------------------------------
# Cigar Processing ...

def processCigar(ref, i):

    #output(ref.flag  )
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
    q_array=()
    r_array=()
    q_string=""

    cigpatsA = re.findall('[^A-Z]+', cigar)
    cigpatsB = re.findall('[A-Z]+', cigar)
    cigparts = zip(map(int, cigpatsA), cigpatsB)

    readbases=ref.seq[i] # '//'.split(ref.seq[i])

    # splice ~= take()
    for cigarpartbasecount, cigartype in cigparts:
        ####
        if cigartype == "S":
        # not aligned read section
            q_pos=q_pos+cigarpartbasecount
            #output("q_pos", q_pos)

        if cigartype == "M":
        # so its not a deletion or insertion. Its 0:M
            q_array = readbases[q_pos:q_pos+cigarpartbasecount]
            r_array = [ "X" for _ in xrange(r_pos,r_pos+cigarpartbasecount)]

            q_pos=q_pos+cigarpartbasecount
            r_pos=r_pos+cigarpartbasecount

        if cigartype == "I":
            q_array=readbases[q_pos:q_pos+cigarpartbasecount]
            r_array = [ "-" for _ in xrange(r_pos,r_pos+cigarpartbasecount)]
            q_pos=q_pos+cigarpartbasecount

        if cigartype == "D":
            q_array=["-" for _ in xrange(cigarpartbasecount)]
            r_array = [ "o" for _ in xrange(r_pos,r_pos+cigarpartbasecount)]
            r_pos=r_pos+cigarpartbasecount


    for j in xrange(len(r_array)):
        #output(r_array[j],q_array[j])
        if q_array[j] != "-" and  r_array[j] != "-":
            r_array[j]=q_array[j]

    a=0
    mdparts=re.split('(\d+)|MD:Z:', m_d)
    for m in mdparts:
        #output("m,")
        if m is True:
            if 1:
                m="~/^\^(.+:/:"
                tmp=m # '//'.split(m)
                # ????
                for x in xrange(len(tmp)):
                    r_array[x]=tmp[x]

            else:
              if m in ["A","T","C","G"] :
                if r_array[a] == "-":
                    while r_array[a] == "-" :
                        a+=1

                r_array[a]=m
                #r_array[a]="^"
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

def processDF(ref):
    _hash={}

    for i in xrange(numRows(ref)):

        # Process the Cigar ...
        q_array, r_array = processCigar(ref, i)

        # Process ref attributes ...
        refid = ref.refid[i]


        try:
          #if len(ref.barcode_arrangement[i]) > 0:
          if numRows(tbl_check_barcode)>0:
              if len(ref.barcode_arrangement[i]) > 0:
                  output2("We have spotted a barcode", ref.barcode_arrangement[i])
                  refid_ = str(refid)+"_"+ref.barcode_arrangement[i]
                  output(refid_)
              else: refid_ = str(refid)+"_UC"
          else: refid_ = refid
        except: pass

        flag=ref.flag[i]
        refstart = (ref.pos[i])-1

        basenameid = ref.basename_id[i]
        #print "line 283", refid, refid_, basenameid
        #if not basenameid is True: basenameid = 1

        qstring=''.join(q_array)
        rstring=''.join(r_array)
        refstring_orig = rstring
        refstring = refstring_orig
        querystring_orig = qstring
        querystring = querystring_orig

        output2("basenamehash.keys()", basenamehash.keys())

        # MS Hack to check ...
        #if not basenameid in basenamehash.keys():
        #    basenamehash[basenameid] = 1

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

        for x in xrange(reflength):

            if refstring[x] != "-":
                posn+=1
                #output("posn", posn)


            output2(posn, refid)
            output2(refstring[x], querystring[x])

            # Check if the strings match ...
            inc = lambda x: incrementHash(_hash, valsarray, refid, refid_, posn, x)

            valsarray=('A','T','G','C','i','d')
            #print refstring[x],querystring[x]
            #print _hash

            if refstring[x] == querystring[x]:
                # print "hello"
                 _hash = inc(refstring[x])
                # print "world"
                 #output(m)
            elif refstring[x] == "-":
                 _hash = inc("i")
                 output("ins")
            elif querystring[x] == "-":
                 _hash = inc("d")
                 output("del")
            else:
                 _hash = inc(querystring[x])
                 output("mm")
            #print _hash

        output2("The refend is ", posn )

        if verbose is True and posn > 49000:  output("OH DEAR!")
        output2("Flag is ", flag  )


        memd.set(checkreads,ref.ID )
    return _hash

#-------------------------------------------------------------------------------

def quote(dbname, t):
    return "'" + dbname + "'.'" + t + "'"

def createCoverageTable(dbanme, t, cursor):

    #t_ = quote(dbname, t)

    sql = \
        '''
        CREATE TABLE IF NOT EXISTS ''' + t + '''
            (
                `ref_id` INT NOT NULL,
                `ref_pos` INT NOT NULL,
                `ref_seq` TINYTEXT NOT NULL,
                `A` INT,
                `T` INT,
                `G` INT,
                `C` INT,
                `D` INT,
                `I` INT,
                PRIMARY KEY (`ref_id`,`ref_pos`)
            )
        CHARACTER SET utf8
        '''
    #print sql
    cursor.execute(sql)

#def dropTrigger(t1):
#      return "DROP TRIGGER IF EXISTS " + t1 + "_trigger"

def mkTriggerTable(dbname, t1, t2, cursor):

    #t1_ = quote(dbname, t1)
    #t2_ = quote(dbname, t2)

    trigger_name = t1 + "_trigger"

    cond = " ref_id = NEW.ref_id and ref_pos = NEW.ref_pos"

    trigger = \
       '''
       CREATE TRIGGER ''' + trigger_name + ''' AFTER INSERT ON ''' + t1 + '''
        FOR EACH ROW
            BEGIN
             IF NOT EXISTS (SELECT 1 FROM ''' + t2 + ''' WHERE ''' + cond + ''' ) THEN
               INSERT INTO ''' + t2 + ''' (ref_id, ref_pos, ref_seq, A, T, C, G, D, I) VALUES
                 (NEW.ref_id, NEW.ref_pos, NEW.ref_seq,  NEW.A, NEW.T, NEW.C, NEW.G, NEW.D, NEW.I);
             ELSE
               UPDATE ''' + t2 + ''' SET A = A + NEW.A WHERE ''' + cond + ''' ;
               UPDATE ''' + t2 + ''' SET T = T + NEW.T WHERE ''' + cond + ''' ;
               UPDATE ''' + t2 + ''' SET C = C + NEW.C WHERE ''' + cond + ''' ;
               UPDATE ''' + t2 + ''' SET G = G + NEW.G WHERE ''' + cond + ''' ;
               UPDATE ''' + t2 + ''' SET D = D + NEW.D WHERE ''' + cond + ''' ;
               UPDATE ''' + t2 + ''' SET I = I + NEW.I WHERE ''' + cond + ''' ;
             END if;
            END
        '''


    # Test if table exists ...
    sql = "SHOW TABLES LIKE '"+ t1 +"'"
    df = selectDF(sql)
    if numRows(df) == 0:

        # Create table ...
        createCoverageTable(dbname, t1, cursor)

        # Add trigger ...
        output(trigger)
        cursor.execute(trigger)

#-------------------------------------------------------------------------------
# OK Main code starts here ....

#unless ( checkingrunning)
output("mT_align.py")
if not checkingrunning == "1" : # try:
    output("Checkingrunning is not TRUE...")
    output("-"*80)
    memd.set(checkrunning,"1")
else:


# 1. We want to check if this is a barcoded run. If it is we need to run a special barcoding mapping algorithm
    sql = "SELECT table_name FROM information_schema.tables \
            WHERE table_schema = '" + dbname + "' \
            AND table_name = 'barcode_assignment'"
    tbl_check_barcode = selectDF(sql)

# 2. We want to check if this database contain prebasecalled analysis. If it does we're going to need to create some tables and process this data.
    sql = "SELECT table_name FROM information_schema.tables \
            WHERE table_schema = '" + dbname + "' \
            AND table_name = 'pre_tracking_id'"
    tbl_check_presquiggle = selectDF(sql)

# 3. We have to check if the last_align_maf_basecalled table exists. If it doesn't then we don't want to run this again.
    sql = "SELECT table_name FROM information_schema.tables WHERE \
            table_schema = '" + dbname + "' AND \
            (table_name = 'last_align_maf_basecalled_template' \
                or table_name = 'pre_align_template' \
                or table_name = 'align_sam_basecalled_template')"
    tbl_check_mode = selectDF(sql)

#-------------------------------------------------------------------------------
# Create Tables ....

    output("CREATING TABLES ... ")

    if numRows(tbl_check_mode)==0:
      output("We don't have a table")
    else:
      output("We do have a table")
      for tabletype in tbl_check_mode['table_name']:

            output2("tabletype ", tabletype)

            for readtype in readtypes :


                # Create a new table if one doesn't already exist...

                # read_tracking ...
                sql = "CREATE TABLE IF NOT EXISTS `" + \
                    dbname + "`.`read_tracking_" + readtype + "` (  \
                   `readtrackid` INT NOT NULL AUTO_INCREMENT, \
                   `basename_id` INT NOT NULL, \
                   PRIMARY KEY (`readtrackid`) ) CHARACTER SET utf8"
                cursor.execute(sql)


                # reference_coverage_ ...
                t = "reference_coverage_" + readtype
                createCoverageTable(dbname, t, cursor)

                # reference_coverage_ ... tmp_ ...
                target = "reference_coverage_" + readtype
                tmp = target + "_tmp"
                mkTriggerTable(dbname, tmp, target, cursor)

                # Ensure tmp table is empty...
                sql = "DELETE FROM " + tmp + " WHERE ref_id >= 0 "
                output(sql)
                cursor.execute(sql)



                ## BARCODING TABLES ...
                # read_tracking_barcode_ ...
                sql_1 = "CREATE TABLE IF NOT EXISTS `" + \
                    dbname + "`.`read_tracking_barcode_" + readtype + "` ( \
                   `readtrackid` INT NOT NULL AUTO_INCREMENT, \
                   `basename_id` INT NOT NULL, \
                   PRIMARY KEY (`readtrackid`) \
                     ) \
                     CHARACTER SET utf8"
                if readtype == "2d" and numRows(tbl_check_barcode) != 0:
                        cursor.execute(sql_1)

                # reference_coverage_barcode_ ...
                t = "reference_coverage_barcode_" + readtype
                createCoverageTable(dbname, t, cursor)

                # reference_coverage_barcode_ tmp ...
                target = "reference_coverage_barcode_" + readtype
                tmp = target + "_tmp"
                mkTriggerTable(dbname, tmp, target, cursor)

                # Ensure tmp table is empty...
                sql = "DELETE FROM " + tmp + " WHERE ref_id >= 0 "
                output(sql)
                cursor.execute(sql)


                # PRE ALIGN TABLES ...
                # read_tracking_pre_ ...
                sql_1 = "CREATE TABLE IF NOT EXISTS `" + \
                    dbname + "`.`read_tracking_pre_" + readtype + "` (\
                   `readtrackid` INT NOT NULL AUTO_INCREMENT,\
                   `basename_id` INT NOT NULL,\
                   PRIMARY KEY (`readtrackid`)\
                     )\
                     CHARACTER SET utf8"

                # reference_pre_coverage_ ...
                sql_2 = "CREATE TABLE IF NOT EXISTS `" + \
                  dbname + "`.`reference_pre_coverage_" + readtype + "` ( \
                  `ref_id` INT NOT NULL, \
                  `ref_pos` INT NOT NULL, \
                  `count` INT, \
                  PRIMARY KEY (`ref_id`,`ref_pos`) \
                ) \
                CHARACTER SET utf8"

                # reference_pre_coverage_ ...
                sql_2_tmp = "CREATE TABLE IF NOT EXISTS `" + \
                  dbname + "`.`reference_pre_coverage_" + readtype + "_tmp` ( \
                  `ref_id` INT NOT NULL, \
                  `ref_pos` INT NOT NULL, \
                  `count` INT, \
                  PRIMARY KEY (`ref_id`,`ref_pos`) \
                ) \
                CHARACTER SET utf8"


                if numRows(tbl_check_presquiggle) != 0:
                    cursor.execute(sql_1)
                    cursor.execute(sql_2)
                    cursor.execute(sql_2_tmp)


                '''
                target = "reference_pre_coverage_" + readtype
                tmp = target + "_tmp"

                sql = dropTrigger(tmp)
                print sql
                cursor.execute(sql)
                mkTrigger(tmp, target, fs)
                sql = "DELETE FROM "+ tmp
                print sql
                cursor.execute(sql)
                '''

                checkreads = dbname + "checkreads" + readtype
                output2("replacing checkvar ",readtype)

#-------------------------------------------------------------------------------
# Process PRE-ALIGNED reads from DTW ...

                # Create a hash to store alignment info
                prehash = {}

                # Create a hash of basename_ids
                prebasenamehash = {}

                valsarray = ['count']

                if numRows(tbl_check_presquiggle) > 0:
                    output("We have found raw data to process.")

                    query = "SELECT * FROM %s.pre_align_%s where basename_id not in (select basename_id from %s.read_tracking_pre_%s) order by ID limit 100" % (dbname, readtype, dbname, readtype)

                    table = selectDF(query)
                    ref = table
                    for i in xrange(numRows(table)):
                        output(ref.ID)

                        refstart = ref.r_start[i]
                        reflength = ref.r_align_len[i]
                        querystart = ref.q_start[i]
                        refid = ref.refid[i]
                        basenameid = ref.basename_id[i]
                        if basenameid not in prebasenamehash.keys() :
                            prebasenamehash[basenameid] = 1

                        output("The read start is refstart and it is reflength bases long.")
                        for x in range(refstart, refstart+reflength-1):
                            incrementHash(prehash,valsarray,refid,x,'count')
                        output(prehash[refid][x]['count'])

                premafhash = prehash # {}
                '''
# we now need to convert the premafhash to something we can use in the final sql statements
                for refid in sorted(prehash.keys()):
                    for posn in sorted(prehash[refid].keys()):
                        for val in (valsarray2)  :
                            if val in prehash[prefid][posn]:
                                premafhash[refid][posn][val] = \
                                        prehash[refid][posn][val]
                            else:
                                premafhash[refid][posn][val] = 0
                for refid in sorted(premafhash.keys()):
                    for posn in sorted(premafhash[refid].keys()):
                        count = premafhash[refid][posn]['count']
                        reference = premafhash[refid][posn]['reference' ]
                '''

                # TODO MS ...
                '''
                valsarray2=['count']
                array = hash2array(premafhash, valsarray2)

                colNames = ['ref_id', 'ref_pos', 'count']
                indexes = colNames[:2]
                df = array2frame(array,colNames, indexes)

                tname = "reference_pre_coverage_" + readtype
                insertDF(df, tname)
                '''

#-------------------------------------------------------------------------------
# Select the reads that need processing from the last_align_maf_basecalled_template table ....

                try: checkreadsval = memd.get(checkreads)
                except:
                    output("FAIL 252")

                output("The value of barcode check is %d for run %s at %s" \
                            % (numRows(tbl_check_barcode), dbname, readtype)
                        )



                basenamehash = {}

                # 2D ....
                #if readtype == "2d" and numRows(tbl_check_barcode)>0:
                if readtype == "2d" and numRows(tbl_check_barcode)>0:
                    if tabletype == "last_align_maf_basecalled_template":
                        output("parsing barcodes")

                        t1 = dbname+".last_align_maf_basecalled_"+readtype
                        t2 = dbname + ".barcode_assignment"
                        t3 = dbname + ".read_tracking_barcode_" + readtype
                        q1 = "select basename_id from " + t3
                        query = "SELECT * FROM " + t1 + \
                                     " left join " + t2 + \
                                     " using (basename_id) where \
                                        basename_id not in ("+ q1 + ") \
                                        and alignnum = 1 order by ID \
                                        limit 100"
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
                                    flag != ('2048' or '2064') AND \
                                    basename_id NOT IN ("+q1+")" + \
                                    " ORDER BY ID LIMIT 100"

                    output(query)

                    # MS TODO Check this .....
                    output( "line 700")

                    barquerygo = selectDF(query)
                    barmafhash = processDF(barquerygo)

                    tname = "read_tracking_barcode_" + readtype
                    insertDF(df, tname)
                    
                    _hash = barmafhash

                    # Convert hash to array ...
                    valsarray=('reference','A','T','G','C','i','d')
                    array = hash2array(_hash, valsarray)


                    #barmafhash = processDF(query)
                    colNames = ['ref_id','ref_pos','ref_seq'
                                ,'A','T','G','C','i','d']
                    indexes = colNames[:2]

                    df = array2frame(array,colNames,indexes)
                    # print df
                    tname = "reference_coverage_barcode_" +readtype
                    output( tname)
                    insertDF(df, tname)

#-------------------------------------------------------------------------------
# Translate MAF or SAM alignments into a reference coverage plot data...
# are we dealing with SAM or MAF formatted data?

                if tabletype == "last_align_maf_basecalled_template":
                # Process LAST align ....

                    output("We have found maf data to process.")

                    ## Note that we need to deal with multiply aligned sequences still - could do using the alignnum=1 but it doesn't really work...

                    t1 = dbname + ".last_align_maf_basecalled_" + readtype
                    t2 = dbname + ".read_tracking_" + readtype
                    q1 = "select basename_id from " + t2
                    query = "SELECT * FROM " + t1 + " where \
                                basename_id not in (" + q1 + ") and \
                                alignnum = 1 order by ID limit 100"

                elif tabletype == "align_sam_basecalled_template":
                # Process SAM align  ....
                    output("We have found sam data to process.")
                    # Note that we need to deal with multiply aligned
                    # sequences still - could do using the alignnum=1
                    # but it doesn't really work...

                    t1 = dbname + ".align_sam_basecalled_" + readtype
                    t2 = dbname + ".reference_seq_info"
                    t3 = dbname + ".read_tracking_" + readtype
                    q1 = "select basename_id from "+ t3
                    query = "SELECT * FROM " + t1 + \
                                " inner join " + t2 + \
                                " where refname=rname and \
                                    flag != ('2048' or '2064') and \
                                    basename_id not in \
                                    (" + q1 + ") order by ID limit 100"

                output("="*80)

# Process records and insert _hash into database tables ....

                try:
                    table = selectDF(query)
                except:
                    table = pd.DataFrame()

                if numCols(table)>0:
                  try:
                    output( "line 779")

                    _hash = processDF(table)
                    output( "line 788")

                    #output( "insert done"
                    # Convert hash to array ...
                    valsarray=('reference','A','T','G','C','i','d')
                    array = hash2array(_hash, valsarray)
                    output( "line 793")

                    # Convert array to dataframe ...
                    colNames=['ref_id','ref_pos','ref_seq'
                                ,'A','T','G','C','i','d']
                    indexes = colNames[:2]
                    df = array2frame(array,colNames,indexes)

                    output( "line 793")
                    #print df.T
                    #sys.stdout.flush()

                    # Insert dataframe into db table ...
                    tname = "reference_coverage_" +readtype+ "_tmp"

                    insertDF(df, tname)

                    output("-"*80)

                    # Book keeping ...
                    df = table['basename_id']
                    colNames = ['readtrackid','basename_id']
                    df.columns = colNames

                    '''
                    tname = "read_tracking_" + readtype
                    insertDF(df, tname)
                    '''

                    # MS TODO
                    '''
                    tname = "read_tracking_pre_" + readtype
                    insertDF(df, tname)
                    '''
                  except: pass

    memd.delete(checkrunning)

if 0: # except:
    output("Exception so ending....")
    sys.exit()
