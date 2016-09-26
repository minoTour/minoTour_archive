<?php

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("config/db.php");

// load the login class
require_once("classes/Login.php");

// load the functions
require_once("includes/functions.php");



// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");
	?>

<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<!--
Import the header.
-->
<?php
include 'includes/head-new.php';
?>
<script src="js/vue.min.js"></script>
  <!--
  BODY TAG OPTIONS:
  =================
  Apply one or more of the following classes to get the
  desired effect
  |---------------------------------------------------------|
  | SKINS         | skin-blue                               |
  |               | skin-black                              |
  |               | skin-purple                             |
  |               | skin-yellow                             |
  |               | skin-red                                |
  |               | skin-green                              |
  |---------------------------------------------------------|
  |LAYOUT OPTIONS | fixed                                   |
  |               | layout-boxed                            |
  |               | layout-top-nav                          |
  |               | sidebar-collapse                        |
  |               | sidebar-mini                            |
  |---------------------------------------------------------|
  -->
  <body class="hold-transition skin-blue sidebar-mini fixed">
    <div class="wrapper">


        <!--Import the header-->
        <?php
        include 'navbar-header-new.php';
        ?>

        <!--Import the left hand navigation-->
        <?php
        include 'navbar-top-links-new.php';
        #include 'test.php';
        ?>


      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">
            <h1>Live Control <small> - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></small></h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>


                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Live Interaction</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <p>This page allows you to remotely control minKNOW. You use it at your own risk.</p>
                            <p>Once connected you will see the minION id below. If you have multiple minIONs connected to one account and all active, they can all be controlled from this page. Further options to implement 'Run Until' are available within the Current Sequencing Run folder.</p>
                            <label id="conn_text"></label><br />

                                <div id="messages_txt" />






                            <div>




  <div id="app">


  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist" >
    <li v-for="(key,minion) in minions | orderBy 'name'" role="presentation" ><a href="#{{minion.name}}" role="tab" data-toggle="tab"><div v-if='minion.engine_states.status!="ready"'>Sequencing:{{minion.livedata.machine_id.result}}/{{minion.name}}</div><div v-else><div v-if='minion.livedata.machine_id.result!=""'>On:{{minion.livedata.machine_id.result}}/{{minion.name}}</div><div v-else>Off:{{minion.name}}</div></div></a></li>
  </ul>

  <!-- Tab panes-->
  <div class="tab-content">
    <div v-for="(key,minion) in minions | orderBy 'name'" role="tabpanel" class="tab-pane" id="{{minion.name}}">
        <h3>You are interacting with minION: {{minion.name}}</h3>


        <div v-if="minion.state==1">
            <p
            <h3>It is currently <b>active</b>.</h3>
            <p>{{minion.histogram}}</p>
            <div class="col-md-6">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                    <h3 class="panel-title">minKNOW Control options</h3>
                    </div>
                  <div class="panel-body">
                      <h5>To test if you have a connection to minKNOW:</h5>
                        <button v-on:click="testmessage" id='{{minion.name}}' type='button' class='btn btn-info btn-sm'><i class='fa fa-magic'></i> Test Communication</button>
                          <br>
                          <!--<h5>To increase/decrease the current bias voltage offset in minKNOW by 10 mV:</h5>
                          <button v-on:click="biasvoltageinc" id='{{minion.name}}' type='button' class='btn btn-info btn-sm'><i class='fa fa-arrow-circle-up'></i> Inc Bias Voltage</button>
                          <button v-on:click="biasvoltagedec" id='{{minion.name}}' type='button' class='btn btn-info btn-sm'><i class='fa fa-arrow-circle-down'></i> Dec Bias Voltage</button>
                          <br>-->

                          <h5>Rename Your Run:</h5>
                          <!--<button id='renamerun' type='button' class='btn btn-info btn-sm'><i class='fa fa-magic'></i> Rename Run</button>-->
                          <!-- Indicates a dangerous or potentially negative action -->
                          <!-- Button trigger modal -->
                          <button id='renamerun' class='btn btn-info btn-sm' data-toggle='modal' data-target='#{{minion.name}}renamemodal'>
                            <i class='fa fa-magic'></i> Rename Run
                          </button>

                          <!-- Modal -->
                          <div class='modal fade' id='{{minion.name}}renamemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                              <div class='modal-dialog'>
                                  <div class='modal-content'>
                                      <div class='modal-header'>
                                          <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                          <h4 class='modal-title' id='myModalLabel'>Rename Your Run</h4>
                                      </div>
                                      <div class='modal-body'>
                                          <div id='{{minion.name}}renameinfo'>
                                              <p>You can rename a run if you wish to do so. Note there is no need to do this unless you wish to change the smaple ID for some reason.</p>
                                              <input type="text" id="newname" class="form-control" placeholder="New Run Name">
                                              <p>If you are sure you wish to do this enter your new name above and click 'Rename Run' below. Otherwise close this window.</p>
                                              <p> We dont recommend doing this when a run is in progress!</p>
                                          </div>
                                          <div class='modal-footer'>
                                              <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                              <button v-on:click="renamenow" id='{{minion.name}}' type='button' class='btn btn-danger' data-dismiss='modal'>Rename Run</button>
                                          </div>
                                      </div><!-- /.modal-content -->
                                  </div><!-- /.modal-dialog -->
                              </div><!-- /.modal -->
                          </div>



                          <br>
                          <h5>Remote Start/Stop Sequencing:</h5>

                                  <!-- Indicates a dangerous or potentially negative action -->
                                  <!-- Button trigger modal -->
                                  <button id='stopminion' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#{{minion.name}}stopminionmodal'>
                                    <i class='fa fa-stop'></i> Stop minION
                                  </button>

                                  <!-- Modal -->
                                  <div class='modal fade' id='{{minion.name}}stopminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                  <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                  <h4 class='modal-title' id='myModalLabel'>Stop your minION</h4>
                                              </div>
                                              <div class='modal-body'>
                                                  <div id='{{minion.name}}stopminioninfo'>
                                                      <p>This will attempt to stop your minION sequencer remotely. It should be possible to restart sequencing remotely but software crashes on the minION controlling device may cause problems. You should only stop your minION device remotely if you are certain you wish to do so and <strong> at your own risk</strong>.</p>

                                                      <p>If you are sure you wish to do this, click 'Stop minION' below. Otherwise close this window.</p>
                                                  </div>
                                                  <div class='modal-footer'>
                                                      <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                                      <button v-on:click="stopminion" id='{{minion.name}}' type='button' class='btn btn-danger' data-dismiss='modal'>Stop minION</button>
                                                  </div>
                                              </div><!-- /.modal-content -->
                                          </div><!-- /.modal-dialog -->
                                      </div><!-- /.modal -->
                                  </div>


                                  <!-- Indicates a dangerous or potentially negative action -->
                                  <!-- Button trigger modal -->
                                  <button id='startminion' class='btn btn-success btn-sm' data-toggle='modal' data-target='#{{minion.name}}startminionmodal'>
                                    <i class='fa fa-play'></i> Start minION
                                  </button>

                                  <!-- Modal -->
                                  <div class='modal fade' id='{{minion.name}}startminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                  <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                  <h4 class='modal-title' id='myModalLabel'>Start your minION</h4>
                                              </div>
                                              <div class='modal-body'>
                                                  <div id='{{minion.name}}startminioninfo'>
                                                      <p>This will attempt to restart your minION sequencer remotely.</p>

                                                      <p>If you are sure you wish to do this select an available run script and click 'Start minION' below. Otherwise close this window.</p>
                                                      <div v-for="script in minion.scripts" class='radio'>
                                                          <label>
                                                              <input type='radio' name='scriptRadios' id='{{script.name}}' value='{{script.name}}' >{{script.name}}.py</label>
                                                      </div>
                                                  </div>
                                                  <div class='modal-footer'>
                                                      <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Close</button>
                                                      <button v-on:click="startminion" id='{{minion.name}}' type='button' class='btn btn-success' data-dismiss='modal'>Start minION</button>
                                                  </div>
                                              </div><!-- /.modal-content -->
                                          </div><!-- /.modal-dialog -->
                                      </div><!-- /.modal -->
                                  </div>
                                  <br><br>
                                  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample{{minion.name}}" aria-expanded="false" aria-controls="collapseExample">
                                    Available Scripts:
                                  </button>
                                  <div class="collapse" id="collapseExample{{minion.name}}">
                                    <div class="well">
                                      <div v-for="script in minion.scripts">{{script.name}}</div>
                                    </div>
                                  </div>


                  </div>
              </div>
          </div>

            <div class="col-md-6">
            <div class='panel panel-info'>
  				<div class='panel-heading'>
    				<h3 class='panel-title'>minKNOW real time data</h3>
				</div>
  				<div class='panel-body'>
  				<div class='table-responsive'>
  				<table class='table table-condensed' >
 					<tr>
                        <th>Category</td>
                        <th>Info</td>
                    </tr>
                    <tr>
                        <td>minKNOW computer name</td>
                        <td>{{minion.livedata.machine_id.result}}</td>
                    </tr>
                    <tr>
                        <td>minKNOW Status</td>
                        <td>{{minion.livedata.status.result}}</td>
                    </tr>
                    <tr>
                        <td>Current Script</td>
                        <td>{{minion.livedata.current_script.result}}</td>
                    </tr>
                    <tr>
                        <td>Sample Name</td>
                        <td>{{minion.livedata.sample_id.result}}</td>
                    </tr>
                    <tr>
                        <td>Run Name</td>
                        <td>{{minion.livedata.dataset.result}}</td>
                    </tr>
                    <tr>
                        <td>Voltage Offset</td>
                        <td>{{minion.livedata.biasvoltageget.result.bias_voltage}} mV</td>
                    </tr>
                    <tr>
                        <td>Yield</td>
                        <td>{{minion.livedata.yield_res.result}}</td>
                    </tr>
				</table>
				</div>
				</div>
            </div>
			</div>





      <div class = "col-md-12">
          <br>
      <div class="panel panel-info">
          <div class="panel-heading">
          <h3 class="panel-title">minKNOW Details</h3>
          </div>

        <div class="panel-body">
            <div v-if='minion.engine_states.status!="ready"'>
              <div class="col-md-2"><p><i>Last Update</i>: {{minion.timestamp}}</p></div>
              <div class="col-md-2"><p><i>MinKNOW version</i>: {{minion.engine_states.version_string}}</p></div>
              <div class="col-md-2"><p><i>minION heatsink temperature</i>: {{minion.engine_states.minion_heatsink_temperature}}</p></div>
              <div class="col-md-2"><p><i>minION ID</i>: {{minion.engine_states.minion_id}}</p></div>
              <div class="col-md-2"><p><i>ASIC ID</i>: {{minion.engine_states.asic_id_full}}/{{minion.engine_states.asic_id}}</p></div>
              <div class="col-md-2"><p><i>minION ASIC temperature</i>: {{minion.engine_states.minion_asic_temperature}}</p></div>
              <div class="col-md-2"><p><i>Yield</i>: {{minion.engine_states.yield}}</p></div>
              <div class="col-md-2"><p><i>Experiment Start Time</i>: {{minion.engine_states.daq_start_time}}</p></div>
              <div class="col-md-2"><p><i>Experiment End Time</i>: {{minion.engine_states.daq_stop_time}}</p></div>
              <div class="col-md-2"><p><i>Status</i>: {{minion.engine_states.status}}</p></div>
              <div class="col-md-2"><p><i>Flow Cell ID</i>: {{minion.engine_states.flow_cell_id}}</p></div>
              <div class="col-md-2"><p><i>Channels with Reads</i>: {{minion.statistics.channels_with_read_event_count}}</p></div>
              <div class="col-md-2"><p><i>Read Event Count</i>: {{minion.statistics.read_event_count}}</p></div>
              <div class="col-md-2"><p><i>Completed Read Count</i>: {{minion.statistics.selected_completed_count}}</p></div>
              <div class="row">
                  <div class="col-lg-12">
                      <div class="col-lg-6" id="{{minion.name}}"><div is="chartreadhist" :title="minion.name" :key="key" :datain="minion.statistics.read_event_count_weighted_hist" :datain2="minion.statistics.read_event_count_weighted_hist_bin_width"></div>

                      <div id="{{minion.name}}"><div is="chartyield" :title="minion.name" :key="key" :datain="minion.engine_states.yield" :datain2="minion.yield_history"></div>
                  </div> </div>
                  <div class = "col-lg-6">
                          <div v-for="state in minion.channelstuff" class="col-md-2">{{state.style.label}}<br><p style="background-color:#{{state.style.colour}};">.</p></div>
                          <div id="{{minion.name}}"><div is="chartheat" :title="minion.name" :key="key" :datain="minion.channel_info.channels" :datain2="minion.channelstuff"></div>
                  </div>

                  </div>
              </div>
              <!--  <div v-for="(key,value) in minion.engine_states">{{key}}:{{value}}</div>-->
                </div>
                <div v-for="channel in minion.multiplex_states"><div style="width:50px;height:10px;border:1px solid #000;float:right;">{{minion.channel_info.channel.name}}<div><span v-text="channel.name | reverse"></span></div></div></div>

            </div>
            <div v-else>
                <p>This minION is not currently running.</p>
            </div>

    </div>
    </div>
</div>
        </div>

        <div v-else>
            It is currently <i>inactive</i>.

            <!-- Indicates a dangerous or potentially negative action -->
            <!-- Button trigger modal -->
            <button id='initminion' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#{{minion.name}}initminionmodal'>
              <i class='fa fa-stop'></i> Initialise minION
            </button>

            <!-- Modal -->
            <div class='modal fade' id='{{minion.name}}initminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                            <h4 class='modal-title' id='myModalLabel'>Stop your minION</h4>
                        </div>
                        <div class='modal-body'>
                            <div id='{{minion.name}}initminioninfo'>
                                <p>This action will switch the minION to inactive status. It should be possible to reactivate the minION remotely but software crashes on the minION controlling device may cause problems. You should only inactivate your minION device remotely if you are certain you wish to do so and <strong> at your own risk</strong>.</p>

                                <p>If you are sure you wish to do this, click 'Inactivate minION' below. Otherwise close this window.</p>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                <button v-on:click="initminion" id='{{minion.name}}' type='button' class='btn btn-danger' data-dismiss='modal'>Initialise minION</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div>


        </div>
    </div>

  </div>
          </div>
</div>

<button data-toggle="collapse" data-target="#demo">Debugging Info</button>
<div id="demo" class="collapse">
<label id="server_message"></label><br />
</div>

                            								<!--NEW BLOCK-->
                            							</div>
                            </div>
                        </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>


      <script>
  $(document).ready(function () {
    //creating useful functions
    function tohistogram(readeventcountweightedhist,readeventcountweightedhistbinwidth) {
        var results =[];
        //var counter = 0;
        //console.log(minionsthings.minions[minion].statistics.read_event_count_weighted_hist);
        for (var i = 0; i < readeventcountweightedhist.length; i++) {
            if (readeventcountweightedhist[i] > 0){
                //counter+=1;
                //console.log(readeventcountweightedhistbinwidth);
                results.push({ "name": (i * readeventcountweightedhistbinwidth), "y": readeventcountweightedhist[i] });
            }
        }
        return (results);
    }

    function gety(value){
    			value = value-1;
    			xval=31-((value - (value % 4))/4 % 32);
    			return(xval+1+1);
    }


    function getx(value){
    	value = value-1;
    	ad36 = value % 4;
    	ab37 = (value - ad36)/4;
    	ad37 = (ab37 % 32);
    	ab38 = ((ab37-ad37)/32);
    	ad38 = (ab38 % 4);
    	ag38 = (ad36+(4*ad38));
    	yval=(15 - ag38);
    	return(yval+1+1);
    }

    //function check(){
        //alert ('check called');
    //    var timerId = setInterval(function(){
    //        if(!ws || ws.readyState == 3) {
    //            clearInterval(timerId);
    //            start();

    //        }else{
    //            clearInterval(timerId);
    //        }

    //    },1000);
    //}

    function formatdatetime(timetoconvert){
        var date = new Date(timetoconvert*1000);
        // Hours part from the timestamp
        var hours = date.getHours();
        // Minutes part from the timestamp
        var minutes = "0" + date.getMinutes();
        // Seconds part from the timestamp
        var seconds = "0" + date.getSeconds();

        // Will display time in 10:30:23 format
        var formattedTime = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
        return (formattedTime);
    }


    var ws = null;
    //change example.com with your IP or your host
    function start() {
        ws = new WebSocket("ws://localhost:8080/ws");
        ws.onopen = function(evt) {
          var conn_status = document.getElementById('conn_text');
          conn_status.innerHTML = "Connection status: Connected!";
          var subscribemessage={"SUBSCRIBE":"<?php echo $_SESSION['user_name'];?>"};
          ws.send(JSON.stringify(subscribemessage));
        };
        ws.onmessage = function(evt) {
          var newMessage = document.createElement('p');
          //console.log(evt);
          newMessage.textContent = "Server: " + evt.data;
          var message_status = document.getElementById('server_message');
          message_status.innerHTML = evt.data;
          var jsonreturn = JSON.parse(evt.data);
          var minion_select = document.getElementById('minions');
          var miniondict;
          for (var thing in minionsthings.minions) {
              var adder=0;
              for (var prop in jsonreturn) {
                  if (prop != minionsthings.minions[thing].name){
                  }else{
                      adder ++;
                  }
              }
              if (adder == 0){
                  minionsthings.minions.splice([thing]);
              }

          }
          for (var prop in jsonreturn) {
              //console.log(jsonreturn[prop].state);
              var adder=0;
              for (var thing in minionsthings.minions) {
                  //console.log(thing);
                  if (prop != minionsthings.minions[thing].name){

                  }else{
                      if (minionsthings.minions[thing].state != jsonreturn[prop].state){
                            minionsthings.minions[thing].state = jsonreturn[prop].state;
                      }
                      if (minionsthings.minions[thing].scripts.length != jsonreturn[prop].scripts.length) {
                          minionsthings.minions[thing].scripts = jsonreturn[prop].scripts;
                      }
                      if (minionsthings.minions[thing].livedata != jsonreturn[prop].livedata){
                          minionsthings.minions[thing].livedata = jsonreturn[prop].livedata;
                      }
                      if (minionsthings.minions[thing].channelstuff != jsonreturn[prop].channelstuff){
                          minionsthings.minions[thing].channelstuff = jsonreturn[prop].channelstuff;
                      }
                      if (minionsthings.minions[thing].comms != jsonreturn[prop].comms){
                          minionsthings.minions[thing].comms = jsonreturn[prop].comms;
                      }
                      if (minionsthings.minions[thing].engine_states != jsonreturn[prop].detailsdata.engine_states){
                          minionsthings.minions[thing].engine_states = jsonreturn[prop].detailsdata.engine_states;
                    }
                      if (minionsthings.minions[thing].multiplex_states != jsonreturn[prop].detailsdata.multiplex_states){
                          minionsthings.minions[thing].multiplex_states = jsonreturn[prop].detailsdata.multiplex_states;
                      }
                      if (minionsthings.minions[thing].channel_info != jsonreturn[prop].detailsdata.channel_info){
                          minionsthings.minions[thing].channel_info = jsonreturn[prop].detailsdata.channel_info;
                      }
                      if (minionsthings.minions[thing].statistics != jsonreturn[prop].detailsdata.statistics){
                          minionsthings.minions[thing].statistics = jsonreturn[prop].detailsdata.statistics;
                      }
                      if (minionsthings.minions[thing].timestamp != jsonreturn[prop].detailsdata.timestamp){
                          minionsthings.minions[thing].timestamp = formatdatetime(jsonreturn[prop].detailsdata.timestamp);
                      }
                      if (minionsthings.minions[thing].yield_history != jsonreturn[prop].yield_history){
                          minionsthings.minions[thing].yield_history = jsonreturn[prop].yield_history;
                      }
                      if (minionsthings.minions[thing].simplechanstats != jsonreturn[prop].simplechanstats){
                          minionsthings.minions[thing].simplechanstats = jsonreturn[prop].simplechanstats;
                      }
                      if (minionsthings.minions[thing].simplesummary != jsonreturn[prop].simplesummary){
                          minionsthings.minions[thing].simplesummary = jsonreturn[prop].simplesummary;
                      }
                      //minionsthings.minions[thing].starttimething = formatdatetime(jsonreturn[prop].detailsdata.engine_states.daq_start_time);
                      adder ++;
                  }
              }
              if (adder == 0){
                  minionsthings.minions.push({ name: prop ,simplechanstats: jsonreturn[prop].simplesummary,simplesummary: jsonreturn[prop].simplesummary,channel_info: jsonreturn[prop].detailsdata.channel_info, yield_history: jsonreturn[prop].yield_history, timestamp: jsonreturn[prop].detailsdata.timestamp, channelstuff: jsonreturn[prop].channelstuff,statistics: jsonreturn[prop].detailsdata.statistics,multiplex_states: jsonreturn[prop].detailsdata.multiplex_states, engine_states: jsonreturn[prop].detailsdata.engine_states, state: jsonreturn[prop].state ,scripts: jsonreturn[prop].scripts , livedata: jsonreturn[prop].livedata, comms: jsonreturn[prop].comms});
              }


              //console.log(minionsthings.minions);

          }

        };
        ws.onclose = function(evt) {
          //alert ("Connection closed");
          var conn_status = document.getElementById('conn_text');
          conn_status.innerHTML = "Connection status: Closed. This page will require refreshing to try to reconnect.";
          minionsthings.minions=[];
          //check();

        };
    }

    start();

    Vue.filter('reverse', function(value){
        //console.log(value);
        //for (var thing in value){
        //    //console.log(value[thing]);
        //    for (var channel in value[thing] ){
        //        //console.log(channel);
        //    }
        //}
        return value;
    })

    Vue.component('chartreadhist', {
	template: '<div id="container{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain','datain2'],
    data: function() {
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'container'+this.title,
                    type:'column',
	        	},
    	    	title: {
        	    	text: 'Read length Histograms'
	        	},
                xAxis: {
                    categories: []
                },
                yAxis: {
                    title: {
                        text: 'Total Event Length'
                    }
                },
                credits: {
      enabled: false
  },
            series: [{
                name: 'Read Histogram',
                data: this.datain
            }]
         			}
    	    }
    }
    ,


    created: function() {
    },
    ready: function() {
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            //minion=this.key;
            setInterval(function () {
                //console.log(this.datain);
                this.chart.series[0].setData(tohistogram(this.datain,parseInt(this.datain2)));
                //this.chart.series[0].setData(this.datain);
                this.chart.redraw();
                //console.log("chart in",this.datain);
            //        console.log(this.datain);
                    //var x = (new Date()).getTime(), // current time
                    //    y = Math.random();
                    //series.addPoint([x, y], true, true);
                    //series[0].data = [parseInt(this.datain)];
            //        chart.series[0].setData(parseInt(this.datain), true);
        }.bind(this), 5000);
            });
        }
    })
    Vue.component('chartyield', {
	template: '<div id="containeryield{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain','datain2'],
    data: function() {
        //var d = new Date();
        //var t = d.getTime();
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'containeryield'+this.title,
                    type:'spline',
	        	},
    	    	title: {
        	    	text: 'Yield over time '
	        	},
                xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: {
                    text: 'Cumulative Events'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Event Counts',
                data: []
            }]
         			}
    	    }
    }
    ,


    created: function() {
    },
    ready: function() {
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            //console.log(this.datain2);
            //this.chart.series[0].data = this.datain2;
            this.chart.series[0].setData(this.datain2);

            //minion=this.key;
            setInterval(function () {
                //console.log(this.datain);
                var d = new Date();
                var t = d.getTime();
                //console.log(this.chart.yAxis[0].series[0].processedYData[this.chart.yAxis[0].series[0].processedYData.length-1]);
                //if (parseInt(this.datain)<this.chart.yAxis[0].series[0].processedYData[this.chart.yAxis[0].series[0].processedYData.length-1]){
                    //console.log(this.datain2);
                    //this.chart.series[0].remove(true);
                    //this.chart.series[0].data = [parseInt(this.datain2)];
                    //this.chart.series[0].addPoint([t,0]);
                //}
                if (parseInt(this.datain)>this.chart.yAxis[0].series[0].processedYData[this.chart.yAxis[0].series[0].processedYData.length-1]){
                    this.chart.series[0].addPoint([t,parseInt(this.datain)]);
                }
                //console.log(this.chart.series[0]);
                //this.chart.redraw();
                //console.log(this.chart.series[0])
            //        console.log(this.datain);
                    //var x = (new Date()).getTime(), // current time
                    //    y = Math.random();
                    //series.addPoint([x, y], true, true);
                    //series[0].data = [parseInt(this.datain)];
            //        chart.series[0].setData(parseInt(this.datain), true);
        }.bind(this), 5000);
            });
        }
    })

    Vue.component('chartheat', {
	template: '<div id="containerheat{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain','datain2'],
    data: function() {
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'containerheat'+this.title,
                    type:'heatmap',
	        	},
    	    	title: {
        	    	text: 'Channel Status'
	        	},
                xAxis: {
                    gridLineWidth: 0,
                minorGridLineWidth: 0,
                lineWidth: 0,
                lineColor: 'transparent',
                minorTickLength: 0,
                tickLength: 0,
                    labels:
                    {
                    enabled: false
                    }

            },
            yAxis:{
                gridLineWidth: 0,
                minorGridLineWidth: 0,
                labels:
                {
                enabled: false
            },
            title: {
                text: null
            }
            },
            plotOptions: {
                series: {
                    animation: false,
                }
            },
            legend: {
            enabled: false
            },
            credits: {
      enabled: false
  },
            series: [{
                name: 'Channel State',
                borderWidth: 0.1,
                data: []
            }]
         			}
    	    }
    }
    ,


    created: function() {
    },
    ready: function() {
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            var results=[];
            var colourlookup={};
            for (var colour in this.datain2){
                if (typeof this.datain2[colour].style !== "undefined") {
                //console.log(this.datain2[colour].style.label);
                //console.log(this.datain2[colour].style.colour);
                    var label = this.datain2[colour].style.label;
                    var hexcode = this.datain2[colour].style.colour;
                    colourlookup[label] = hexcode;
                }
            }
            //console.log(colourlookup);
            for (var i = 1; i < 513; i++){
                results.push({ "x": getx(i), "y": gety(i),"z":0, "color":'#'+colourlookup["zero"] });
            }
            this.chart.series[0].setData(results);
            this.chart.redraw();
            //minion=this.key;
            setInterval(function () {
                for (var item in this.datain){
                    //this.chart.series[0].data[this.datain[item].name].update({ "x": getx(this.datain[item].name), "y": gety(this.datain[item].name),"z":0, "color":'#'+colourlookup[this.datain[item].state] });
                }
                this.chart.redraw();
        }.bind(this), 10000);
            });
        }
    })



    var minionsthings = new Vue({
        el: '#app',
        data: {
            minions: [ ]
        },
        methods: {
            testmessage: function(event) {
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"testmessage"}};
                ws.send(JSON.stringify(instructionmessage));
            },
            biasvoltageinc: function(event) {
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"biasvoltageinc"}};
                ws.send(JSON.stringify(instructionmessage));
            },
            biasvoltagedec: function(event) {
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"biasvoltagedec"}};
                ws.send(JSON.stringify(instructionmessage));
            },
            renamenow: function(event){
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"rename","NAME":$("#newname").val()}};
                ws.send(JSON.stringify(instructionmessage));
                //$('#renamemodal').modal('hide');
            },
            startminion: function(event){
                var script = $("input[type='radio'][name='scriptRadios']:checked").val();
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"startminion","SCRIPT":script}};
                ws.send(JSON.stringify(instructionmessage));
                //$('#startminionmodal').modal('hide');
            },
            stopminion: function(event){
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"stopminion"}};
                ws.send(JSON.stringify(instructionmessage));
                //$('#stopminionmodal').modal('hide');
            },
            inactivateminion: function(event){
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"shutdownminion"}};
                ws.send(JSON.stringify(instructionmessage));
                //$('#stopminionmodal').modal('hide');
            },
            initminion: function(event){
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"initialiseminion"}};
                ws.send(JSON.stringify(instructionmessage));

            },
        }
    });




  });

</script>


  </body>
</html>
<?php
} else {

	    // the user is not logged in. you can do whatever you want here.
	    // for demonstration purposes, we simply show the "you are not logged in" view.
	    include("views/not_logged_in.php");
	}

	?>
