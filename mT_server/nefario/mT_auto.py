from autobahn.twisted.websocket import WebSocketServerProtocol, \
    WebSocketServerFactory
#import time


class MyServerProtocol(WebSocketServerProtocol):

    def onConnect(self, request):
        print("Client connecting: {0}".format(request.peer))

    def onOpen(self):
        print("WebSocket connection open.")
        self.sendMessage("Connection Made")
        #print self.peer_address
        print self.peer
        print self
        #print self.get_channel_id()
        #print self.sock
        #print self.sock.family
        #print self.sock.fileno()
        self.holdingdict=dict()

    def onMessage(self, m, isBinary):
        if isBinary:
            #print("Binary message received: {0} bytes".format(len(m)))
            #print self.peer
            pass
        else:
            #print("Text message received: {0}".format(m.decode('utf8')))
            try:
                #self.sendMessage("Connected - waiting for messages.")
                selfminIONdict = json.loads(str(m))
                if type(selfminIONdict) is dict:
                    for job in selfminIONdict.keys():
                        #print "message",str(m)
                        if job == "SUBSCRIBE":
                            #print "subscriber message"
                            user = selfminIONdict[job]
                            #for user in selfminIONdict[job]:
                            if 1:
                                #print "user is", user
                                socketid = self.peer
                                if socketid not in subscriber_dict:
                                    subscriber_dict[socketid]=dict()
                                subscriber_dict[socketid]["user_name"]=user
                                subscriber_dict[socketid]["minion"]=set()
                                subscriber_dict[socketid]["connect"]=self
                                #print selfminIONdict
                        if job =="INSTRUCTION":
                            print "Instruction message received"
                            print selfminIONdict[job]
                            #print selfminIONdict[job]["JOB"]
                            user=selfminIONdict[job]["USER"]
                            minion=selfminIONdict[job]["minion"]
                            #print user,minion,tracker_dict[user][minion]["comms"]
                            #server.manager.websockets[tracker_dict[user][minion]["comms"]].send(json.dumps(selfminIONdict[job], ensure_ascii=False))
                            try:
                                print selfminIONdict[job]
                                print control_dict[user][minion]
                                #control_dict[user][minion].sendMessage(json.dumps(selfminIONdict[job], ensure_ascii=False))
                                control_dict[user][minion].sendMessage(json.dumps(selfminIONdict[job]))
                            except Exception, err:
                                print "Error",err

                        if job == "DETAILS":
                            #print "DETAILS MESSAGE"
                            #print "message",str(m)
                            for user in selfminIONdict[job].keys():
                                #print "USER",user
                                socketid = self.peer
                                if socketid not in publisher_dict:
                                    publisher_dict[socketid]=dict()
                                    publisher_dict[socketid]["user_name"]=user
                                    publisher_dict[socketid]["minion"]=set()

                                if user not in tracker_dict:
                                    tracker_dict[user]=dict()
                                if user not in test_dict:
                                    test_dict[user]=dict()
                                if user not in message_dict:
                                    message_dict[user]=dict()
                                if user not in control_dict:
                                    control_dict[user]=dict()

                                active = 0
                                inactive = 0
                                #print message_dict[user]
                                patched=self.reassemble_dict(selfminIONdict[job][user],message_dict[user])
                                #print patched
                                message_dict[user]=patched

                                #for minION in selfminIONdict[job][user].keys():
                                for minION in message_dict[user].keys():
                                    #print minION
                                    if minION[0]=="M":
                                        #if minION not in publisher_dict[socketid]["minion"]:
                                        #    publisher_dict[socketid]["minion"].add(minION)
                                        if minION not in tracker_dict[user]:
                                            tracker_dict[user][minION]=dict()
                                        if minION not in test_dict[user].keys():
                                            test_dict[user][minION]=dict()
                                        if "comms" not in tracker_dict[user][minION].keys():
                                            tracker_dict[user][minION]["comms"]=self.peer
                                            publisher_dict[socketid]["minion"].add(minION)
                                        if minION not in control_dict[user].keys():
                                            print "SETTING THE CONTROL DICT",self.peer
                                            control_dict[user][minION]=self

                                        #print user,minION,tracker_dict[user][minION]["comms"]
                                        #if selfminIONdict[job][user][minION]["state"]=="active":
                                        if message_dict[user][minION]["state"]=="active":

                                            tracker_dict[user][minION]["state"]=1
                                            try:
                                                #tracker_dict[user][minION]["scripts"]=selfminIONdict[job][user][minION]["scripts"]["result"]["items"]
                                                #print message_dict[user][minION]["scripts"]
                                                #tracker_dict[user][minION]["scripts"]=message_dict[user][minION]["scripts"]["result"]["items"]
                                                tracker_dict[user][minION]["scripts"]=message_dict[user][minION]["scripts"]

                                            except Exception, err:
                                                #print "tracker dict 191 problem"
                                                print "Except 117",err

                                            #if "channelstuff" in selfminIONdict[job][user][minION].keys():
                                            if "channelstuff" in message_dict[user][minION].keys():
                                                #channelstatedetails = selfminIONdict[job][user][minION]["channelstuff"]
                                                #tracker_dict[user][minION]["channelstuff"]=selfminIONdict[job][user][minION]["channelstuff"]
                                                channelstatedetails = message_dict[user][minION]["channelstuff"]
                                                tracker_dict[user][minION]["channelstuff"]=message_dict[user][minION]["channelstuff"]
                                            else:
                                                try:
                                                    tracker_dict[user][minION]["channelstuff"]=channelstatedetails
                                                except Exception, err:
                                                    print "Except 199",err

                                            if "messages" in message_dict[user][minION].keys():
                                                print "message received"
                                                tracker_dict[user][minION]["messages"]=message_dict[user][minION]["messages"]
                                            if "livedata" in message_dict[user][minION].keys():
                                                tracker_dict[user][minION]["livedata"]=message_dict[user][minION]["livedata"]
                                                #print selfminIONdict[job][user][minION]["livedata"]["yield_res"]["result"]
                                            if "yield_history" in message_dict[user][minION].keys():
                                                tracker_dict[user][minION]["yield_history"]=message_dict[user][minION]["yield_history"]
                                            if "temp_history" in message_dict[user][minION].keys():
                                                tracker_dict[user][minION]["temp_history"]=message_dict[user][minION]["temp_history"]
                                            if "pore_history" in message_dict[user][minION].keys():
                                                tracker_dict[user][minION]["pore_history"]=message_dict[user][minION]["pore_history"]
                                            #print "Original"
                                            #print message_dict[user][minION]["pore_history"]
                                            #print "Copy"
                                            #print tracker_dict[user][minION]["pore_history"]
                                            if "simplechanstats" in message_dict[user][minION].keys():
                                                tracker_dict[user][minION]["simplechanstats"]=message_dict[user][minION]["simplechanstats"]
                                            if "simplesummary" in message_dict[user][minION].keys():
                                                tracker_dict[user][minION]["simplesummary"]=message_dict[user][minION]["simplesummary"]

                                            if "detailsdata" in message_dict[user][minION].keys():
                                                for thing in message_dict[user][minION]["detailsdata"].keys():

                                                    if "detailsdata" not in tracker_dict[user][minION]:
                                                        tracker_dict[user][minION]["detailsdata"]=dict()

                                                    if thing not in mungejson(tracker_dict[user][minION]["detailsdata"]):
                                                        tracker_dict[user][minION]["detailsdata"][thing]=dict()
                                                    if thing == "statistics":
                                                        for section in mungejson(message_dict[user][minION]["detailsdata"][thing]):
                                                            tempholder = tracker_dict[user][minION]["detailsdata"][thing]
                                                            if section not in tempholder:
                                                                tracker_dict[user][minION]["detailsdata"][thing][section]=dict()
                                                        context = dict(list(tracker_dict[user][minION]["detailsdata"][thing].items()) + list(message_dict[user][minION]["detailsdata"][thing].items()))
                                                        tracker_dict[user][minION]["detailsdata"][thing]=context
                                                    if thing == "channels":
                                                        for section in mungejson(message_dict[user][minION]["detailsdata"][thing]):
                                                            tempholder = tracker_dict[user][minION]["detailsdata"][thing]
                                                            if section not in tempholder:
                                                                tracker_dict[user][minION]["detailsdata"][thing][section]=dict()
                                                        context = dict(list(tracker_dict[user][minION]["detailsdata"][thing].items()) + list(message_dict[user][minION]["detailsdata"][thing].items()))
                                                        tracker_dict[user][minION]["detailsdata"][thing]=context
                                                    if thing == "channel_info":
                                                        for section in mungejson(message_dict[user][minION]["detailsdata"][thing]):
                                                            tempholder = tracker_dict[user][minION]["detailsdata"][thing]
                                                            if section not in tempholder:
                                                                tracker_dict[user][minION]["detailsdata"][thing][section]=dict()
                                                        context = dict(list(tracker_dict[user][minION]["detailsdata"][thing].items()) + list(message_dict[user][minION]["detailsdata"][thing].items()))
                                                        tracker_dict[user][minION]["detailsdata"][thing]=context
                                                    else:
                                                        try:
                                                            tracker_dict[user][minION]["detailsdata"][thing]=message_dict[user][minION]["detailsdata"][thing]
                                                        except Exception, err:
                                                            print "Error 179",err
                                            active += 1

                                        else:
                                            inactive += 1
                                            tracker_dict[user][minION]["state"]=0
                                            tracker_dict[user][minION]["scripts"]=[]
                                            tracker_dict[user][minION]["livedata"]=[]
                                            tracker_dict[user][minION]["detailsdata"]={}
                                print "There are %s minIONs available. %s active, %s inactive." % (len(message_dict[user]),active, inactive)
                else:
                    pass
                    #print selfminIONdict
            except Exception, err:
                print "unrecognised input", err
        # echo back message verbatim
        #self.sendMessage(payload, isBinary)

    def reassemble_dict(self,difference,olddict):
        patched = patch(difference, olddict)
        return(patched)

    def onClose(self, wasClean, code, reason):
        print("WebSocket connection closed: {0}".format(reason))
        if self.peer in publisher_dict:
            userwholeft = publisher_dict[self.peer]["user_name"]
            print "A publisher has gone - namely %s!" %(userwholeft)
            ###if this user is viewing the website, we need to clear their view. The data has gone/
            #if userwholeft in subscriber_dict.values():
            print publisher_dict
            #print subscriber_dict.keys()[subscriber_dict.values().index(userwholeft)]
            for minion in publisher_dict[self.peer]["minion"]:
                tracker_dict[userwholeft].pop(minion)
                message_dict[userwholeft].pop(minion)
                control_dict[userwholeft].pop(minion)
                #tracker_dict[userwholeft]=dict()
            #self.send
            #testdifference = '{}'
            #server.manager.websockets[subscriber].send(json.dumps(testdifference, ensure_ascii=False))
            #tracker_dict.pop(userwholeft,None)
            #publisher_dict.pop(self.sock.fileno(),None)
            #message_to_send.pop(self.sock.fileno(),None)
        if self.peer in subscriber_dict:
            userwholeft = subscriber_dict[self.peer]["user_name"]
            print "A subscriber has gone - namely %s!" %(userwholeft)
            subscriber_dict.pop(self.peer,None)
            message_to_send.pop(self.peer,None)



def mungejson(obj):
    if type(obj) is dict:
        return obj.keys()
    elif type(obj) is list:
        return obj
    else:
        print "ARGGGGGH"
        sys.exit()


class ThreadingExample():
    """ Threading example class
    The run() method will be started and it will run in the background
    until the application exits.
    """

    def __init__(self, server, interval=10):
        """ Constructor
        :type interval: int
        :param interval: Check interval, in seconds
        """
        self.interval = interval
        self.server=server
        self.living=True
        #print self.server

        thread = threading.Thread(target=self.run, args=())
        thread.daemon = True                            # Daemonize thread
        thread.start()                                  # Start the execution

    def run(self):
        """ Method that runs forever """
        while self.living:
            # Do something

            #print server.manager.websockets

            ## We're going to send information to subscriber clients:
            #print "subscriber dict", subscriber_dict
            try:
                for subscriber in subscriber_dict:
                    print "subscriber", subscriber


                    if subscriber not in message_to_send:
                        message_to_send[subscriber] = dict()
                        try:
                            print "SENDING FIRST CONNECT"
                            blankdict=dict()
                            difference = jsonpatch.JsonPatch.from_diff(tracker_dict[subscriber_dict[subscriber]["user_name"]],blankdict)
                            testdifference = '{}'
                            #server.manager.websockets[subscriber].send(json.dumps(testdifference, ensure_ascii=False))
                            subscriber_dict[subscriber]["connect"].sendMessage(json.dumps(testdifference, ensure_ascii=False))
                        except Exception, err:
                            print "Error 283", err
                    #print "sub dict",subscriber_dict[subscriber]

                    try:
            #            print "tracker dict",tracker_dict[subscriber_dict[subscriber]]
                        pass
                        try:
                            if subscriber_dict[subscriber]["user_name"] in tracker_dict:
                                deepcopydict=copy.deepcopy(tracker_dict[subscriber_dict[subscriber]["user_name"]])
                                #difference = diff(message_to_send[subscriber],deepcopydict)
                                #difference=list(difference)
                            #    difference = json_delta.diff(message_to_send[subscriber],deepcopydict,verbose=False,array_align=False)
                                #tic=time.time()
                                difference = jsonpatch.JsonPatch.from_diff(message_to_send[subscriber],deepcopydict)
                                #print (time.time()-tic)
                                #difference = jsonpatch.JsonPatch.from_diff(message_to_send[subscriber],tracker_dict[subscriber_dict[subscriber]["user_name"]])
                            #    print "DIFFERENCE",difference
                            #    message_to_send[subscriber]=deepcopydict
                                #server.manager.websockets[subscriber].send(json.dumps(tracker_dict[subscriber_dict[subscriber]], ensure_ascii=False))
                                #server.manager.websockets[subscriber].send(json.dumps(str(difference), ensure_ascii=False))
            #                    server.manager.websockets[subscriber].send(str(difference))
                                if str(difference) != "[]":
                                    subscriber_dict[subscriber]["connect"].sendMessage(str(difference))
                                    print "XXXXXXXX_new",sys.getsizeof(json.dumps(str(difference)))
                                else:
 				    print "no change"
                                message_to_send[subscriber]=copy.deepcopy(deepcopydict)
                                #print type(difference)
                                #print len(difference)
                                #print str(difference)

                                #message_to_send[subscriber]=tracker_dict[subscriber_dict[subscriber]["user_name"]]

                        except Exception, err:
                            print "problemo", err
                            #difference = jsonpatch.JsonPatch.from_diff(message_to_send[subscriber],[])
                            testdifference = '{}'
            #                server.manager.websockets[subscriber].send(json.dumps(testdifference, ensure_ascii=False))


                    except Exception, err:
                        print "Error 314",err
            except Exception, err:
                print "We might have hit a problem here.",err
            time.sleep(self.interval)

if __name__ == '__main__':

    import sys
    import json
    import copy

    import threading
    import time
    import collections
    from dictdiffer import diff, patch, swap, revert

    import jsonpatch

    from twisted.python import log
    from twisted.internet import reactor

    log.startLogging(sys.stdout)


    global tracker_dict
    tracker_dict=dict()
    # A dictionary to store old messages in for subsequent assembly
    global message_dict
    message_dict=dict()
    # A dictionary to store incremental messages to send to the end user.
    global message_to_send
    message_to_send=dict()
    global test_dict
    test_dict=dict()
    global subscriber_dict
    subscriber_dict=dict()
    global publisher_dict
    publisher_dict=dict()
    global control_dict
    control_dict=dict()
    global lock
    lock = threading.Lock()
    global channelstatedetails



    factory = WebSocketServerFactory("ws://127.0.0.1:8080")
    factory.protocol = MyServerProtocol
    # factory.setProtocolOptions(maxConnections=2)

    example = ThreadingExample(factory)

    # note to self: if using putChild, the child must be bytes...

    reactor.listenTCP(8080, factory)
    reactor.run()
