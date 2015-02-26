#!/usr/bin/python

import MySQLdb
import json
from Queue import Queue
import time
#from threading import Thread
import threading
# Open database connection

global mafhash
mafhash = dict()
global lock
lock = threading.Lock()

def main():

	dbname = "minion_LomanLabz_PC_Ecoli_MG1655_ONI_3058"
	db = MySQLdb.connect("localhost","minion","nan0p0re", dbname)

	# prepare a cursor object using cursor() method
	cursor = db.cursor()
	# execute SQL query using execute() method.
	cursor.execute("SELECT VERSION()")
	# Fetch a single row using fetchone() method.
	data = cursor.fetchone()
	print "Database version : %s " % data
	#declare empty hash
	
	#make a new sql query:
	#sql = "select refid,r_align_string,r_start,q_align_string from %s.last_align_maf_basecalled_template where refid = 1" % (dbname)
	sql = "select refid,r_align_string,r_start,q_align_string from %s.last_align_maf_basecalled_template where refid = 1" % (dbname)
	cursor.execute(sql)
	#python get all data from query
	data = cursor.fetchall()

	begin=time.clock()
	#print "BEGIN", begin
	mafhash = dict()
	q = Queue(maxsize=0)
	num_threads = 16

	for i in range(num_threads):
  		worker = threading.Thread(target=do_stuff, args=(q,))
  		worker.setDaemon(True)
  		worker.start()


	for record in data:
		q.put(record)
		q.join()
	
	end=time.clock()

	print "DURRATION", (end-begin)

	db.close()
	
	


def do_stuff(q,):
	#print "START THREAD", time.time()
	while True:
		#mafhash=dict()
		record=q.get()
		r_align_string = list(record[1])
		q_align_string = list(record[3])
		counter = int(record[2])
		refid = int(record[0])

		#print "REFID", refid, "COUNTER", counter
		
		#print counter
		position = int(1)
		for position in xrange(len(r_align_string)):
			if r_align_string[position] is not "-":
				#print "ok"
				counter+=1
				#print counter
				position=0
			with lock:
				if (refid not in mafhash):
					mafhash[refid]=dict()
				if (counter not in mafhash[refid]):
					mafhash[refid][counter]=dict()
				if (position not in mafhash[refid][counter]):
					mafhash[refid][counter][position]=dict()
					mafhash[refid][counter][position]['refposition']=r_align_string[position]

				if (q_align_string[position] not in mafhash[refid][counter][position]):
					mafhash[refid][counter][position][q_align_string[position]]=1
				else:
					mafhash[refid][counter][position][q_align_string[position]]+=1
			
		q.task_done()
		#print "STOP THREAD", time.time()

if __name__ == "__main__":
	main()







