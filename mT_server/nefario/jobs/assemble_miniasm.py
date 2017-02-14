#! /usr/bin/python

import sys, re, os, io
import subprocess
import argparse
import tempfile
import time
import MySQLdb
import memcache
from Bio import SeqIO

class Assemble():

    def __init__(self):
        self.tmpfile = tempfile.NamedTemporaryFile(suffix=".fa")
        db

    def write_seqs(self, seqs):
        self.tmpfile.write("\n".join(seqs))


    def run(self):
        print 'minimap -Sw5 -L100 -t8 '+self.tmpfile.name+' '+self.tmpfile.name+' | miniasm -f '+self.tmpfile.name+' - | awk \'/^S/{print \">\"$2\"\\n\"$3}\' | fold '
        p1 = subprocess.Popen('minimap -Sw5 -L100 -t8 '+self.tmpfile.name+' '+self.tmpfile.name+' | miniasm -f '+self.tmpfile.name+' - | awk \'/^S/{print \">\"$2\"\\n\"$3}\' | fold ', shell=True, stdout=subprocess.PIPE)
        (out, err) = p1.communicate()
        return out

    def finish(self):
        self.tmpfile.close()

class DB:
  conn = None

  def connect(self):
    #print "trying to connect to ",args.dbhost
    self.conn = MySQLdb.connect(host=args.dbhost, user=args.dbusername,passwd=args.dbpass, port=int(args.dbport))
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



if __name__ == "__main__":

    f = open('mT_param.conf', 'r')

    mT_params = {}
    for line in f:
        k,v = line[:-1].split('=')
        mT_params[k] = v


    parser = argparse.ArgumentParser(description='Run an iterative miniasm assembly', usage='%(prog)s [options] database')
    #parser.add_argument('fasta', metavar = 'reads.fa', help='FASTA reads, file will be split to simulate iterative assembly')
    parser.add_argument('database', metavar= 'database', help='MySQL database to deposit results into.')
    parser.add_argument("-dbh", "--dbhost", nargs='?', default='localhost')
    parser.add_argument("-pw", "--dbpass", nargs='?', default='')
    parser.add_argument("-dbu", "--dbusername", nargs='?', default='root')
    parser.add_argument("-dbp", "--dbport", nargs='?', default=3306)

    args = parser.parse_args()

    print args.database

    memc = memcache.Client([mT_params['memcache']], debug=0)

    db = DB()
    query = "CREATE TABLE IF NOT EXISTS "+args.database+".assembly_metrics (timeid INT(11) NOT NULL AUTO_INCREMENT, timeset TIMESTAMP, no_reads INT(11), no_contigs INT(11), maxlen INT(20), minlen INT(20), totallen INT(20), n50 INT(20), PRIMARY KEY (timeid))"
    result = runSQL(db, query)
    runSQL(db, "CREATE TABLE IF NOT EXISTS "+args.database+".assembly_seq (timeid INT(11) NOT NULL, contigid INT(11) NOT NULL, contigname CHAR(30), length INT(20), fasta LONGTEXT, PRIMARY KEY (timeid, contigid))")

    chunks = dict()
    seqcount = 0
    partcount = 1

    fetch_read = "select g_1minwin,basename_id,sequence from "+args.database+".basecalled_template where pass=1 order by g_1minwin asc"
    reads = runSQL(db,fetch_read)
    #print reads
    assrun = Assemble()


    results = dict()


    for row in reads:
        seqcount += 1
        #if seqcount%5000 == 1:
        #    partcount += 1
        if partcount not in chunks:
            chunks[partcount]=[]
        chunks[partcount].append(">"+str(row[1])+"\n"+str(row[2])+"\n")



    for c in chunks:
        assrun.write_seqs(chunks[c])
        prev = runSQL(db, "SELECT timeid,no_reads FROM "+args.database+".assembly_metrics ORDER BY timeid DESC LIMIT 1")
        #print prev[0][0],prev[0][1]
        if len(prev) == 0 or len(chunks[c]) > prev[0][1]:
            output = assrun.run()
            #print output

            out_io = io.StringIO(unicode(output))

            lengths = []

            prev = runSQL(db, "SELECT timeid FROM "+args.database+".assembly_metrics ORDER BY timeid DESC LIMIT 1")
            print prev
            if len(prev) == 0:
                prev = 0
            else:
                prev = prev[0][0]
                print prev


            count = 0
            for record in SeqIO.parse(out_io, "fasta"):
                print record.id
                count += 1
                runSQL(db, "INSERT INTO "+args.database+".assembly_seq (timeid, contigid, contigname, length, fasta) VALUES ("+str(prev+1)+", "+str(count)+", '"+record.name+"', "+str(len(record.seq))+", '"+str(record.seq)+"')")
                lengths.append(len(record.seq))

            if len(lengths)>0:
                lengths = sorted(lengths)
                print lengths

                print max(lengths)
                print min(lengths)
                print sum(lengths)

                half = float(sum(lengths))/2.0
                n50 = 0

                cumlen = 0;
                for l in lengths:
                    cumlen += l
                    if cumlen >= half:
                        n50 = l
                        break

                print n50

                runSQL(db, "INSERT INTO "+args.database+".assembly_metrics (no_contigs, no_reads, maxlen, minlen, totallen, n50) VALUES ("+str(len(lengths))+", "+str(len(chunks[c]))+", "+str(max(lengths))+", "+str(min(lengths))+", "+str(sum(lengths))+", "+str(n50)+")")
        else:
            print "No new reads."
    assrun.finish()
    print "Assembly Finished"
    time.sleep(60*15)
    jobtestname = "%s_%s" % ("align",args.database)
    jobtestrun  = jobtestname+"_run"
    memc.delete(jobtestrun)
