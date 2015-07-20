#!/usr/bin/env python
import sys, os, re
import time
import errno
from socket import error as socket_error
import threading
import MySQLdb
import configargparse
import urllib2
import json
#import memcache
import hashlib


config_file = script_dir = os.path.dirname(os.path.realpath('__file__')) +'/'+'minup_posix.config'
parser = configargparse.ArgParser(description='interaction: A program to provide real time interaction for minION runs.', default_config_files=[config_file])
parser.add('-dbh', '--mysql-host', type=str, dest='dbhost', required=False, default='localhost', help="The location of the MySQL database. default is 'localhost'.")
parser.add('-dbu', '--mysql-username', type=str, dest='dbusername', required=True, default=None,  help="The MySQL username with create & write privileges on MinoTour.")
parser.add('-dbp', '--mysql-port', type=int, dest='dbport', required=False, default=3306,  help="The MySQL port number, else the default port '3306' is used.")
parser.add('-pw', '--mysql-password', type=str, dest='dbpass', required=True, default=None,  help="The password for the MySQL username with permission to upload to MinoTour.")
parser.add('-db', '--db-name', type=str, dest='dbname', required=True, default=None,  help="The database being monitored.")
parser.add('-pin', '--security-pin', type=str, dest='pin',required=True, default=None, help="This is a security feature to prevent unauthorised remote control of a minION device. You need to provide a four digit pin number which must be entered on the website to remotely control the minION.")
parser.add('-ip', '--ip-address', type=str ,dest='ip',required=True,default=None, help="The IP address of the minKNOW machine.")

args = parser.parse_args()

version="0.1" # 9th June 2015
### test which version of python we're using 

###Machine to connect to address
ipadd = args.ip

def _urlopen(url, *args):

    """Open a URL, without using a proxy for localhost.

    While the no_proxy environment variable or the Windows "Bypass proxy

    server for local addresses" option should be set in a normal proxy

    configuration, the latter does not affect requests by IP address. This

    is apparently "by design" (http://support.microsoft.com/kb/262981).

    This method wraps urllib2.urlopen and disables any set proxy for

    localhost addresses.

    """

    try:

        host = url.get_host().split(':')[0]

    except AttributeError:

        host = urlparse.urlparse(url).netloc.split(':')[0]

    import socket

    # NB: gethostbyname only supports IPv4

    # this works even if host is already an IP address

    addr = socket.gethostbyname(host)

    if addr.startswith('127.'):

        return _no_proxy_opener.open(url, *args)

    else:

        return urllib2.urlopen(url, *args)
        
def execute_command_as_string( data, host = None, port = None):

        host_name = host

        port_number = port



        url = 'http://%s:%s%s' % (host_name, port_number, "/jsonrpc")
        #print url

        req = urllib2.Request(url, data=data, headers={'Content-Length': str(len(data)), 'Content-Type': 'application/json'})

        try:
            f = _urlopen(req)
        except Exception, err:
            err_string = "Fail to initialise mincontrol. Likely reasons include minKNOW not running, the wrong IP address for the minKNOW server or firewall issues."
            print err_string, err
        json_respond = json.loads(f.read())

        f.close()

        return json_respond

def run_analysis():
		keeprunning = 1
		status = '{"id":"1", "method":"get_engine_state","params":{"state_id":"status"}}'
		dataset = '{"id":"1", "method":"get_engine_state","params":{"state_id":"data_set"}}'
		startmessage = '{"id":"1", "method":"set_engine_state","params":{"state_id":"user_message","value":"minoTour is now interacting with your run. This is done at your own risk. To stop minoTour interaction with minKnow disable upload of read data to minoTour."}}'
		testmessage = '{"id":"1", "method":"set_engine_state","params":{"state_id":"user_message","value":"minoTour is checking communication status."}}'
		incmessage = '{"id":"1", "method":"set_engine_state","params":{"state_id":"user_message","value":"minoTour shifted the bias voltage by +10 mV."}}'
		decmessage = '{"id":"1", "method":"set_engine_state","params":{"state_id":"user_message","value":"minoTour shifted the bias voltage by -10 mV."}}'
		startrun = '{"id":"1", "method":"start_script","params":{"name":"MAP_48Hr_Sequencing_Run.py"}}'
		stoprun = '{"id":"1", "method":"stop_experiment","params":"null"}'
		stopprotocol = '{"id":"1", "method":"stop_script","params":{"name":"MAP_48Hr_Sequencing_Run.py"}}'
		startrunmessage = '{"id":"1", "method":"set_engine_state","params":{"state_id":"user_message","value":"minoTour sent a remote run start command."}}'
		stoprunmessage = '{"id":"1", "method":"set_engine_state","params":{"state_id":"user_message","value":"minoTour sent a remote run stop command."}}'
		biasvoltageget = '{"id":"1","method":"board_command_ex","params":{"command":"get_bias_voltage"}}'
		
		bias_voltage_gain = '{"id":"1","method":"get_engine_state","params":{"state_id":"bias_voltage_gain"}}'
		
		bias_voltage_set = '{"id":"1","method":"board_command_ex","params":{"command":"set_bias_voltage","parameters":"-120"}}'
		
		machine_id = '{"id":"1","method":"get_engine_state","params":{"state_id":"machine_id"}}'
		machine_name = '{"id":"1","method":"get_engine_state","params":{"state_id":"machine_name"}}'
		sample_id = '{"id":"1","method":"get_engine_state","params":{"state_id":"sample_id"}}'
		user_error = '{"id":"1","method":"get_engine_state","params":{"state_id":"user_error"}}'
		sequenced_res = '{"id":"1","method":"get_engine_state","params":{"state_id":"sequenced"}}'
		yield_res = '{"id":"1","method":"get_engine_state","params":{"state_id":"yield"}}'
		current_script = '{"id":"1","method":"get_engine_state","params":{"state_id":"current_script"}}'
		

		sqldelete = "delete from messages"
		cursor.execute(sqldelete)
		db.commit()
		
		try:
			results = execute_command_as_string(dataset,ipadd,8000)
			for key in results.keys():
				print "mincontrol:", key, results[key]
			results = execute_command_as_string(startmessage,ipadd,8000)
			for key in results.keys():
				print "mincontrol:", key, results[key]
		except Exception, err:
			print >>sys.stderr, err
		## We're going to collect some data about the minKNOW installation that we are connecting to:
		try:
			pininsert = "insert into messages (message,target,param1,complete) VALUES ('pin','all','%s','1')" % (hashlib.md5(args.pin).hexdigest())
			cursor.execute(pininsert)
			db.commit()
			statusis = execute_command_as_string(status,ipadd,8000)
			datasetis = execute_command_as_string(dataset,ipadd,8000)
			machineid = execute_command_as_string(machine_id,ipadd,8000)
			machinename = execute_command_as_string(machine_name,ipadd,8000)
			sampleid = execute_command_as_string(sample_id,ipadd,8000)
			usererror = execute_command_as_string(user_error,ipadd,8000)
			sequenced = execute_command_as_string(sequenced_res,ipadd,8000)
			yieldres = execute_command_as_string(yield_res,ipadd,8000)
			currentscript = execute_command_as_string(current_script,ipadd,8000)
			sqlinsert = "insert into messages (message,target,param1,complete) VALUES ('Status','all','%s','1'),('Dataset','all','%s','1'),('Functioning','all','1','1'),('machinename','all','%s','1'),('sampleid','all','%s','1'),('usererror','all','%s','1'),('sequenced','all','%s','1'),('yield','all','%s','1'),('currentscript','all','%s','1')" % (statusis["result"],datasetis["result"],machinename["result"],sampleid["result"],usererror["result"],sequenced["result"],yieldres["result"],currentscript["result"])
			cursor.execute(sqlinsert)
			db.commit()
			biasresultmessage = execute_command_as_string(biasvoltageget,ipadd,8000)
			biasvoltageoffset = execute_command_as_string(bias_voltage_gain,ipadd,8000)
			curr_voltage = int(biasresultmessage["result"]["bias_voltage"]) * int(biasvoltageoffset["result"])
			sqlvoltage = "INSERT into messages (message,target,param1,complete) VALUES ('biasvoltage', 'all','%s','1')" % (curr_voltage)
			#print sqlvoltage
			cursor.execute(sqlvoltage)
			db.commit()
			
		except Exception, err:
			print >>sys.stderr, err
					#	sys.exit()
		#print "We're in bad boy"
		while keeprunning==1:
			###Background updates
			statusis = execute_command_as_string(status,ipadd,8000)
			datasetis = execute_command_as_string(dataset,ipadd,8000)
			machineid = execute_command_as_string(machine_id,ipadd,8000)
			machinename = execute_command_as_string(machine_name,ipadd,8000)
			sampleid = execute_command_as_string(sample_id,ipadd,8000)
			usererror = execute_command_as_string(user_error,ipadd,8000)
			sequenced = execute_command_as_string(sequenced_res,ipadd,8000)
			yieldres = execute_command_as_string(yield_res,ipadd,8000)
			currentscript = execute_command_as_string(current_script,ipadd,8000)
			sqlupdate = "UPDATE messages set param1 = '%s' where message='Status'" % (statusis["result"])
			cursor.execute(sqlupdate)
			sqlupdate = "UPDATE messages set param1 = '%s' where message='Dataset'" % (datasetis["result"])
			cursor.execute(sqlupdate)
			sqlupdate = "UPDATE messages set param1 = '%s' where message='machinename'" % (machinename["result"])
			cursor.execute(sqlupdate)
			sqlupdate = "UPDATE messages set param1 = '%s' where message='sampleid'" % (sampleid["result"])
			cursor.execute(sqlupdate)
			sqlupdate = "UPDATE messages set param1 = '%s' where message='usererror'" % (usererror["result"])
			cursor.execute(sqlupdate)
			sqlupdate = "UPDATE messages set param1 = '%s' where message='sequenced'" % (sequenced["result"])
			cursor.execute(sqlupdate)
			sqlupdate = "UPDATE messages set param1 = '%s' where message='yield'" % (yieldres["result"])
			cursor.execute(sqlupdate)
			sqlupdate = "UPDATE messages set param1 = '%s' where message='currentscript'" % (currentscript["result"])
			cursor.execute(sqlupdate)
			db.commit()
			
			biasresultmessage = execute_command_as_string(biasvoltageget,ipadd,8000)
			biasvoltageoffset = execute_command_as_string(bias_voltage_gain,ipadd,8000)
			curr_voltage = int(biasresultmessage["result"]["bias_voltage"]) * int(biasvoltageoffset["result"])
			sqlvoltage = "UPDATE messages set param1 = '%s' where message='biasvoltage'" % (curr_voltage)
			#print sqlvoltage
			cursor.execute(sqlvoltage)
			db.commit()
			#print "Checking commands"
			
			
			sqlstart = "SELECT * FROM interaction where complete != 1"
			cursor.execute(sqlstart)
			db.commit()
			#print "Executing fresh query"
			#print cursor.rowcount
			#for x in xrange(0,cursor.rowcount):
			rows = cursor.fetchall()
			for row in rows:	
				print row[0], "-->", row[1], "-->", row[2], "-->", row[3], "-->", row[4], "-->", row[5]
				if row[1] == "start":
					print "Starting the minION device."
					try:
						startresult = execute_command_as_string(startrun,ipadd,8000)
						startresultmessage = execute_command_as_string(startrunmessage,ipadd,8000)
					except Exception, ett:
						print >>sys.stderr, err
					print "minION device started."
					sqlstart = "UPDATE interaction SET complete=1 WHERE job_index=\"%s\" " % (row[0])
					cursor.execute(sqlstart)
					db.commit()
					#break
					#except Exception, err:
					#	print >>sys.stderr, err
					#	sys.exit()
						
				elif row[1] == "stop":
					print "Stopping the minION device."
					try:
						stopresult = execute_command_as_string(stoprun,ipadd,8000)
						stopprotocolresult = execute_command_as_string(stopprotocol,ipadd,8000)
						stopresultmessage = execute_command_as_string(stoprunmessage,ipadd,8000)
					except Exception, err:
						print >>sys.stderr, err

					print "minION device stopped."
					sqlstop = "UPDATE interaction SET complete=1 WHERE job_index=\"%s\" " % (row[0])
					cursor.execute(sqlstop)
					db.commit()
					#break
					#except Exception, err:
					#	print >>sys.stderr, err
					#	sys.exit()
				elif row[1] == "test":
					print "Sending a test message to minKNOW."
					try:
						testresultmessage = execute_command_as_string(testmessage,ipadd,8000)
					except Exception, ett:
						print >>sys.stderr, err
					print "Test message sent."
					sqlstop = "UPDATE interaction SET complete=1 WHERE job_index=\"%s\" " % (row[0])
					cursor.execute(sqlstop)
					db.commit()
				
				elif row[1] == "biasvoltageget":
					print "Fetching Bias Voltage"
					try:
						biasresultmessage = execute_command_as_string(biasvoltageget,ipadd,8000)
						biasvoltageoffset = execute_command_as_string(bias_voltage_gain,ipadd,8000)
						curr_voltage = int(biasresultmessage["result"]["bias_voltage"]) * int(biasvoltageoffset["result"])
						sqlvoltage = "UPDATE messages set param1 = '%s' where message='biasvoltage'" % (curr_voltage)
						#print sqlvoltage
						cursor.execute(sqlvoltage)
						db.commit()
						#print curr_voltage
						#biasvoltagereset = execute_command_as_string(bias_voltage_set,ipadd,8000)
						#biasresultmessage2 = execute_command_as_string(biasvoltageget,ipadd,8000)
						#biasvoltageoffset2 = execute_command_as_string(bias_voltage_gain,ipadd,8000)
					except Exception, err:
						print >>sys.stderr,err
						continue
					print biasresultmessage
					print biasvoltageoffset
					#print biasvoltagereset
					#print biasresultmessage2
					#print biasvoltageoffset2
					sqlstop = "UPDATE interaction SET complete=1 WHERE job_index=\"%s\" " % (row[0])
					cursor.execute(sqlstop)
					db.commit()
				elif row[1] == "biasvoltageinc":
					print "Incrementing Bias Voltage"
					try:
						biasresultmessage = execute_command_as_string(biasvoltageget,ipadd,8000)
						biasvoltageoffset = execute_command_as_string(bias_voltage_gain,ipadd,8000)
						curr_voltage = (int(biasresultmessage["result"]["bias_voltage"]) * int(biasvoltageoffset["result"])) + 10
						bias_voltage_inc = '{"id":"1","method":"board_command_ex","params":{"command":"set_bias_voltage","parameters":"%s"}}' % (curr_voltage)
						biasvoltagereset = execute_command_as_string(bias_voltage_inc,ipadd,8000)
						biasresultmessage = execute_command_as_string(biasvoltageget,ipadd,8000)
						biasvoltageoffset = execute_command_as_string(bias_voltage_gain,ipadd,8000)
						curr_voltage = int(biasresultmessage["result"]["bias_voltage"]) * int(biasvoltageoffset["result"])
						sqlvoltage = "UPDATE messages set param1 = '%s' where message='biasvoltage'" % (curr_voltage)
						#print sqlvoltage
						cursor.execute(sqlvoltage)
						db.commit()
						incresultmessage = execute_command_as_string(incmessage,ipadd,8000)
					except Exception, err:
						print >>sys.stderr,err
						continue
					sqlstop = "UPDATE interaction SET complete=1 WHERE job_index=\"%s\" " % (row[0])
					cursor.execute(sqlstop)
					db.commit()
				elif row[1] == "biasvoltagedec":
					print "Decreasing Bias Voltage"
					try:
						biasresultmessage = execute_command_as_string(biasvoltageget,ipadd,8000)
						biasvoltageoffset = execute_command_as_string(bias_voltage_gain,ipadd,8000)
						curr_voltage = (int(biasresultmessage["result"]["bias_voltage"]) * int(biasvoltageoffset["result"])) - 10
						bias_voltage_dec = '{"id":"1","method":"board_command_ex","params":{"command":"set_bias_voltage","parameters":"%s"}}' % (curr_voltage)
						biasvoltagereset = execute_command_as_string(bias_voltage_dec,ipadd,8000)
						biasresultmessage = execute_command_as_string(biasvoltageget,ipadd,8000)
						biasvoltageoffset = execute_command_as_string(bias_voltage_gain,ipadd,8000)
						curr_voltage = int(biasresultmessage["result"]["bias_voltage"]) * int(biasvoltageoffset["result"])
						sqlvoltage = "UPDATE messages set param1 = '%s' where message='biasvoltage'" % (curr_voltage)
						#print sqlvoltage
						cursor.execute(sqlvoltage)
						db.commit()
						decresultmessage = execute_command_as_string(decmessage,ipadd,8000)
					except Exception, err:
						print >>sys.stderr,err
						continue
					sqlstop = "UPDATE interaction SET complete=1 WHERE job_index=\"%s\" " % (row[0])
					cursor.execute(sqlstop)
					db.commit()
				
				else:
					print "We don't know what to do here"
					break
			#db.commit()

			time.sleep(3)
		print "...unblock loop ended. Connection closed."
 
if __name__ == "__main__":
    # A few extra bits here to automatically reconnect if the server goes down
    # and is brought back up again.
	try:	
		db = MySQLdb.connect(host=args.dbhost, user=args.dbusername, passwd=args.dbpass, port=args.dbport, db=args.dbname)
		cursor = db.cursor()
	except Exception, err:
		print >>sys.stderr, "Can't connect to MySQL: %s" % (err)
		sys.exit()
	try:
		while 1:
			try:
				run_analysis()
			except socket_error as serr:
				if serr.errno != errno.ECONNREFUSED:
					raise serr
				print "Hanging around, waiting for the server..."
				time.sleep(5) # Wait a bit and try again

	except (KeyboardInterrupt, SystemExit):
		print "stopped mincontrol."
		time.sleep(1)
		sys.exit()










