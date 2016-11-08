from wsgiref.simple_server import make_server
from ws4py.websocket import EchoWebSocket
from ws4py.server.wsgirefserver import WSGIServer, WebSocketWSGIRequestHandler
from ws4py.server.wsgiutils import WebSocketWSGIApplication
import json
import copy

import threading
import time
import collections
import sys
from dictdiffer import diff, patch, swap, revert
#import json_delta
import jsonpatch

def update(d, u):
    for k, v in u.iteritems():
        if isinstance(v, collections.Mapping):
            r = update(d.get(k, {}), v)
            d[k] = r
        else:
            d[k] = u[k]
    return d

def merge(a, b, path=None):
    "merges b into a"
    if path is None: path = []
    for key in b:
        if key in a:
            if isinstance(a[key], dict) and isinstance(b[key], dict):
                merge(a[key], b[key], path + [str(key)])
            elif a[key] == b[key]:
                pass # same leaf value
            else:
                raise Exception('Conflict at %s' % '.'.join(path + [str(key)]))
        else:
            a[key] = b[key]
    return a

def merge2(a, b, path=None):
    "merges b into a - if a already exists, updates with value in b"
    if path is None: path = []
    for key in b:
        if key in a:
            if isinstance(a[key], dict) and isinstance(b[key], dict):
                merge2(a[key], b[key], path + [str(key)])
            elif a[key] == b[key]:
                pass # same leaf value
            else:
                a[key] = b[key]
                #raise Exception('Conflict at %s' % '.'.join(path + [str(key)]))
        else:
            a[key] = b[key]
    return a


class ThreadingExample(object):
    """ Threading example class
    The run() method will be started and it will run in the background
    until the application exits.
    """

    def __init__(self, server, interval=2):
        """ Constructor
        :type interval: int
        :param interval: Check interval, in seconds
        """
        self.interval = interval
        self.server=server
        #print self.server

        thread = threading.Thread(target=self.run, args=())
        thread.daemon = True                            # Daemonize thread
        thread.start()                                  # Start the execution

    def run(self):
        """ Method that runs forever """
        while True:
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
                            print "SENDING FIRST CONNECT SHIZZLE"
                            blankdict=dict()
                            difference = jsonpatch.JsonPatch.from_diff(tracker_dict[subscriber_dict[subscriber]["user_name"]],blankdict)
                            testdifference = '{}'
                            server.manager.websockets[subscriber].send(json.dumps(testdifference, ensure_ascii=False))
                        except:
                            pass
                    #print "sub dict",subscriber_dict[subscriber]

                    try:
            #            print "tracker dict",tracker_dict[subscriber_dict[subscriber]]
                        pass
                        try:

                            deepcopydict=copy.deepcopy(tracker_dict[subscriber_dict[subscriber]["user_name"]])
                            #difference = diff(message_to_send[subscriber],deepcopydict)
                            #difference=list(difference)
                        #    difference = json_delta.diff(message_to_send[subscriber],deepcopydict,verbose=False,array_align=False)
                            difference = jsonpatch.JsonPatch.from_diff(message_to_send[subscriber],deepcopydict)
                            #difference = jsonpatch.JsonPatch.from_diff(message_to_send[subscriber],tracker_dict[subscriber_dict[subscriber]["user_name"]])
                        #    print "DIFFERENCE",difference
                        #    message_to_send[subscriber]=deepcopydict
                            #server.manager.websockets[subscriber].send(json.dumps(tracker_dict[subscriber_dict[subscriber]], ensure_ascii=False))
                            #server.manager.websockets[subscriber].send(json.dumps(str(difference), ensure_ascii=False))
                            server.manager.websockets[subscriber].send(str(difference))
                            message_to_send[subscriber]=copy.deepcopy(deepcopydict)
                            #message_to_send[subscriber]=tracker_dict[subscriber_dict[subscriber]["user_name"]]

                        except Exception, err:
                            print "problemo", err
                            #difference = jsonpatch.JsonPatch.from_diff(message_to_send[subscriber],[])
                            testdifference = '{}'
                            server.manager.websockets[subscriber].send(json.dumps(testdifference, ensure_ascii=False))

                    except:
                        try:
                            server.manager.websockets[subscriber].send(json.dumps("no one connected", ensure_ascii=False))
                        except:
                            pass
            except:
                print "We might have hit a problem here."
            time.sleep(self.interval)

def mungejson(obj):
    if type(obj) is dict:
        return obj.keys()
    elif type(obj) is list:
        return obj
    else:
        print "ARGGGGGH"
        sys.exit()


class BroadcastWebSocket(EchoWebSocket):
    #def __init__(self, *args, **kwargs):
    #    print "initialised"
    #    self.users = []
    #    self.masterminIONdict=dict()

    def opened(self):
        print "Hello Sausage!"
        self.send("Connection Made")
        print self.peer_address
        print self.sock
        print self.sock.family
        print self.sock.fileno()
        self.holdingdict=dict()


    def send_message(self):
        self.send("its 10 seconds")

    def reassemble_dict(self,difference,olddict):
        patched = patch(difference, olddict)
        return(patched)

    def received_message(self, m):
        # self.clients is set from within the server
        # and holds the list of all connected servers
        # we can dispatch to
        selfminIONdict = json.loads(str(m))
        #tempdict = patch(selfminIONdict, self.holdingdict)
        ### We need to determine what type of message we are getting. We will either have a minION update from
        ### a remote client, a request for info from the server or an instruction. So we need to catch at
        ### least three distinct instruction sets.
        ### Message Type - DETAILS - info update from mincontrol
        ### Message Type - REQUEST - request for information on a specific topic
        ### Message Type - INSTRUCTION - an instruction to do something.
        ### Message Type - SUBSCRIBE - an instruction to add a webclient to a dictionary to receive data
        self.send("Connected - waiting for messages.")
        #print m
        if type(selfminIONdict) is dict:
            for job in selfminIONdict.keys():
                #print "message",str(m)
                if job == "SUBSCRIBE":
                    print "subscriber message"
                    user = selfminIONdict[job]
                    #for user in selfminIONdict[job]:
                    if 1:
                        print "user is", user
                        socketid = self.sock.fileno()
                        if socketid not in subscriber_dict:
                            subscriber_dict[socketid]=dict()
                        subscriber_dict[socketid]["user_name"]=user
                        subscriber_dict[socketid]["minion"]=set()
                        #print selfminIONdict
                if job =="INSTRUCTION":
                    print "Instruction message received"
                    print selfminIONdict[job]
                    #print selfminIONdict[job]["JOB"]
                    user=selfminIONdict[job]["USER"]
                    minion=selfminIONdict[job]["minion"]
                    #print user,minion,tracker_dict[user][minion]["comms"]
                    server.manager.websockets[tracker_dict[user][minion]["comms"]].send(json.dumps(selfminIONdict[job], ensure_ascii=False))

                if job == "DETAILS":
                    print "DETAILS MESSAGE"
                    #print "message",str(m)
                    for user in selfminIONdict[job].keys():
                        print "USER",user
                        socketid = self.sock.fileno()
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

                        active = 0
                        inactive = 0
                        #print message_dict[user]
                        patched=self.reassemble_dict(selfminIONdict[job][user],message_dict[user])
                        #print patched
                        message_dict[user]=patched
                        #for minION in selfminIONdict[job][user].keys():
                        for minION in message_dict[user].keys():
                            #print minION
                            #if minION not in publisher_dict[socketid]["minion"]:
                            #    publisher_dict[socketid]["minion"].add(minION)
                            if minION not in tracker_dict[user]:
                                tracker_dict[user][minION]=dict()
                            if minION not in test_dict[user].keys():
                                test_dict[user][minION]=dict()
                            if "comms" not in tracker_dict[user][minION].keys():
                                tracker_dict[user][minION]["comms"]=self.sock.fileno()
                                publisher_dict[socketid]["minion"].add(minION)
                            print user,minION,tracker_dict[user][minION]["comms"]
                            #if selfminIONdict[job][user][minION]["state"]=="active":
                            if message_dict[user][minION]["state"]=="active":
                                tracker_dict[user][minION]["state"]=1
                                try:
                                    #tracker_dict[user][minION]["scripts"]=selfminIONdict[job][user][minION]["scripts"]["result"]["items"]
                                    tracker_dict[user][minION]["scripts"]=message_dict[user][minION]["scripts"]["result"]["items"]
                                except:
                                    print "tracker dict 191 problem"
                                #if "channelstuff" in selfminIONdict[job][user][minION].keys():
                                if "channelstuff" in message_dict[user][minION].keys():
                                    #channelstatedetails = selfminIONdict[job][user][minION]["channelstuff"]
                                    #tracker_dict[user][minION]["channelstuff"]=selfminIONdict[job][user][minION]["channelstuff"]
                                    channelstatedetails = message_dict[user][minION]["channelstuff"]
                                    tracker_dict[user][minION]["channelstuff"]=message_dict[user][minION]["channelstuff"]
                                else:
                                    try:
                                        tracker_dict[user][minION]["channelstuff"]=channelstatedetails
                                    except:
                                        print "199 problem"
                                if "livedata" in message_dict[user][minION].keys():
                                    tracker_dict[user][minION]["livedata"]=message_dict[user][minION]["livedata"]
                                    #print selfminIONdict[job][user][minION]["livedata"]["yield_res"]["result"]
                                if "yield_history" in message_dict[user][minION].keys():
                                    tracker_dict[user][minION]["yield_history"]=message_dict[user][minION]["yield_history"]
                                if "temp_history" in message_dict[user][minION].keys():
                                    tracker_dict[user][minION]["temp_history"]=message_dict[user][minION]["temp_history"]
                                if "pore_history" in message_dict[user][minION].keys():
                                    tracker_dict[user][minION]["pore_history"]=message_dict[user][minION]["pore_history"]

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
                                            except:
                                                print "Later tracker add failed"
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

    def closed(self, code, reason="A client left the room without a proper explanation."):
        if self.sock.fileno() in publisher_dict:
            userwholeft = publisher_dict[self.sock.fileno()]["user_name"]
            print "A publisher has gone - namely %s!" %(userwholeft)
            ###if this user is viewing the website, we need to clear their view. The data has gone/
            #if userwholeft in subscriber_dict.values():
            print publisher_dict
            #print subscriber_dict.keys()[subscriber_dict.values().index(userwholeft)]
            for minion in publisher_dict[self.sock.fileno()]["minion"]:
                tracker_dict[userwholeft].pop(minion)
                message_dict[userwholeft].pop(minion)
                #tracker_dict[userwholeft]=dict()
            #self.send
            #testdifference = '{}'
            #server.manager.websockets[subscriber].send(json.dumps(testdifference, ensure_ascii=False))
            #tracker_dict.pop(userwholeft,None)
            #publisher_dict.pop(self.sock.fileno(),None)
            #message_to_send.pop(self.sock.fileno(),None)
        if self.sock.fileno() in subscriber_dict:
            userwholeft = subscriber_dict[self.sock.fileno()]["user_name"]
            print "A subscriber has gone - namely %s!" %(userwholeft)
            subscriber_dict.pop(self.sock.fileno(),None)
            message_to_send.pop(self.sock.fileno(),None)


class RepeatedTimer(object):
    def __init__(self,interval,function,*args,**kwargs):
        self._timer = None
        self.interval = interval
        self.function = function
        self.args = args
        self.kwargs = kwargs
        self.is_running = False
        self.start()

    def _run(self):
        self.is_running = False
        self.start()
        self.function(*self.args,**self.kwargs)

    def start(self):
        if not self.is_running:
            self._timer = threading.Timer(self.interval,self._run)
            self._timer.start()
            self.is_running = True

    def stop(self):
        self._timer.cancel()
        self.is_running = False



def process_tracked_yield():
    print "Process_tracked_yield"
    #print tracker_dict
    for user in tracker_dict:
        #print user
        for minion in tracker_dict[user]:
            if "yield_res" in tracker_dict[user][minion]["livedata"]:
                if "result" in tracker_dict[user][minion]["livedata"]["yield_res"]:
                    print "Yield found", tracker_dict[user][minion]["livedata"]["yield_res"]["result"]
                    yieldval = int(tracker_dict[user][minion]["livedata"]["yield_res"]["result"])
                    if "yield_history" not in tracker_dict[user][minion]:
                        tracker_dict[user][minion]["yield_history"]=[]
                    if len(tracker_dict[user][minion]["yield_history"]) >1:
                        if yieldval > tracker_dict[user][minion]["yield_history"][-1][0]:
                            tracker_dict[user][minion]["yield_history"].append((tracker_dict[user][minion]["detailsdata"]["timestamp"]*1000,yieldval))
                        if yieldval < tracker_dict[user][minion]["yield_history"][-1][0]:
                            tracker_dict[user][minion]["yield_history"]=[]
                    else:
                        tracker_dict[user][minion]["yield_history"].append((tracker_dict[user][minion]["detailsdata"]["timestamp"]*1000,yieldval))
                    print tracker_dict[user][minion]["yield_history"]
                else:
                    print "No valid yield data found"
            else:
                print "No Yield Data"
                tracker_dict[user][minion]["yield_history"]=[]
            #for thing in tracker_dict[user][minion]["livedata"]["yield_res"]:
            #    print user,minion,thing

def process_channel_information():
    """
    This function maintains the state of the channels in a simple to read format
    """
    print "process_channel_information running"

    """

    colourlookup={}
    statesummarydict={}
    for minion in minIONdict:
        if "detailsdata" in minIONdict[minion]:
            if "channelstuff" in minIONdict[minion]:
                for item in minIONdict[minion]["channelstuff"]:
                    if 'style' in minIONdict[minion]["channelstuff"][item]:
                        colourlookup[minIONdict[minion]["channelstuff"][item]['style']['label']]=minIONdict[minion]["channelstuff"][item]['style']['colour']
                if "cust_chan_dict" not in minIONdict[minion]:
                    minIONdict[minion]["cust_chan_dict"]=dict()
                for channel in minIONdict[minion]["detailsdata"]["channel_info"]["channels"]:
                    if minion not in statedict:
                        statedict[minion]=dict()
                    #print "Channel"
                    #print channel
                    if "state_group" in channel:
                        if channel["state"] in colourlookup:
                            statedict[minion].update({channel["name"]:{'state':channel["state"],"state_group":channel["state_group"],"colour":colourlookup[channel["state"]]}})
                        elif channel["state_group"] in colourlookup:
                            statedict[minion].update({channel["name"]:{'state':channel["state"],"state_group":channel["state_group"],"colour":colourlookup[channel["state_group"]]}})
                        else:
                            pass
                ##print "statedict"
                ##print statedict
                for state in statedict[minion]:
                    if minion not in statesummarydict:
                        #print "minion not in statesummarydict"
                        statesummarydict[minion]=dict()
                    if statedict[minion][state]["state"] not in statesummarydict[minion]:
                        #print "statedict state not in statesummarydict"
                        statesummarydict[minion][statedict[minion][state]["state"]]=1
                    else:
                        ##print "state summary dict is in statsummarydict"
                        statesummarydict[minion][statedict[minion][state]["state"]]+=1
                if "simplechanstats" not in minIONdict[minion]:
                    minIONdict[minion]["simplechanstats"]={}
                #minIONdict[minion]["simplechanstats"].update(statedict[minion])
                ##print statesummarydict[minion]
                try:
                    minIONdict[minion]["simplesummary"]=statesummarydict[minion]
                except:
                    pass
                #print "!!!!!!!!!!"
                #print minIONdict[minion]["simplesummary"]
    """

if __name__ == '__main__':
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
    global lock
    lock = threading.Lock()
    global channelstatedetails

    #rt = RepeatedTimer(5, process_tracked_yield) # it auto-starts, no need of rt.start()


    server = make_server('', 8080, server_class=WSGIServer,
                     handler_class=WebSocketWSGIRequestHandler,
                     app=WebSocketWSGIApplication(handler_cls=BroadcastWebSocket))
    server.initialize_websockets_manager()
    example = ThreadingExample(server)
    try:
        server.serve_forever()
    except (KeyboardInterrupt,Exception) as err:
        print "ctrl-c detected at top level",err
        print "bye bye"
        #rt.stop()
        server.server_close()

        sys.exit()
