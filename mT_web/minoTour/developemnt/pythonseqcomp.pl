#!/usr/bin/python

import MySQLdb
import json

# Open database connection
db = MySQLdb.connect("localhost","minion","nan0p0re","minion_PLSP57501_20140909_JA_defA_4434" )

# prepare a cursor object using cursor() method
cursor = db.cursor()

# execute SQL query using execute() method.
cursor.execute("SELECT VERSION()")

# Fetch a single row using fetchone() method.
data = cursor.fetchone()

print "Database version : %s " % data

#declare empty hash
mafhash = dict()

#make a new sql query:
cursor.execute("select refid,r_align_string,r_start,q_align_string from minion_PLSP57501_20140909_JA_defA_4434.last_align_maf_basecalled_template where refid = 1")

#python get all data from query
data = cursor.fetchall()

for record in data:
	r_align_string = list(record[1])
	q_align_string = list(record[3])
	#print len(r_align_string)
	counter = int(record[2])
	refid = int(record[0])
	position = int(1)
	#print counter
	#print "monkey ",line
	#for element in record:
		#print "camel",element
	for position in xrange(len(r_align_string)):
		if r_align_string[position] is not "-":
			#print "ok"
			counter+=1
			#print counter
			position=0
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

				

#for refid in sorted (mafhash):
#	print refid
#	for counter in sorted (mafhash[refid]):
#		print counter
# disconnect from server
#print json.dumps(mafhash, sort_keys=True)

db.close()