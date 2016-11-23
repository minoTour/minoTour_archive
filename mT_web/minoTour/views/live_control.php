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
        <section class="content">

            <?php include 'includes/run_check.php';?>

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
    <li v-for="(key,minion) in minions | orderBy 'name'" role="presentation" ><a href="#{{minion.name}}" role="tab" data-toggle="tab"><div v-if='minion.livedata.current_script.result.length>0'>Sequencing:{{minion.livedata.machine_id.result}}/{{minion.name}}</div><div v-else><div v-if='minion.livedata.machine_id.result!=""'>On:{{minion.livedata.machine_id.result}}/{{minion.name}}</div><div v-else>Off:{{minion.name}}</div></div></a></li>
  </ul>

  <!-- Tab panes-->
  <div class="tab-content">
    <div v-for="(key,minion) in minions | orderBy 'name'" role="tabpanel" class="tab-pane" id="{{minion.name}}">
        <div v-if="minion.state==1">

            <div class="panel panel-info">
                <div class="panel-heading">
                <h3 class="panel-title">minKNOW Details - {{minion.livedata.dataset.result}}</h3>
                </div>

              <div class="panel-body">
                  <!--<div v-if='minion.engine_states.status!="ready"'>-->
                  <div v-if="minion.livedata.current_script.result.length>0">
                  <div class="row">
                      <div class="col-md-8"><p><b></i>Experiment Started</i>: {{ (minion.engine_states.daq_start_time*1000) | date '%Y-%m-%d %I:%M:%s %p' }}</b></p></div>
                      <div class="col-md-4"><p><i>(Last Update</i>: {{minion.timestamp}})</p></div>
                  </div>
                      <div class="row">
                      <div class="col-md-2"><p><i>MinKNOW version</i>: {{minion.engine_states.version_string}}</p></div>
                      <div class="col-md-2"><p><i>Flow Cell ID</i>: {{minion.livedata.flow_cell_id.result}}</p></div>
                      <div class="col-md-2"><p><i>minION ID</i>: {{minion.name}}</p></div>
                      <div class="col-md-2"><p><i>ASIC ID</i>: {{minion.engine_states.asic_id_full}}/{{minion.engine_states.asic_id}}</p></div>
                  </div>
                  <div class="row">
                      <div class="col-md-8"><p><i>Run Name</i>: {{minion.livedata.dataset.result}}</p></div>
                      <div class="col-md-4"><p><i>Status</i>: {{minion.engine_states.status}}</p></div>
                  </div>
                  <div class="row">
                      <div class="col-md-2"><p><i>Yield</i>: {{minion.engine_states.yield}}/{{minion.statistics.read_event_count}}</p></div>
                      <div class="col-md-2"><p><i>Channels with Reads</i>: {{minion.statistics.channels_with_read_event_count}}</p></div>
                      <div class="col-md-2"><p><i>Read Event Count</i>: {{minion.statistics.read_event_count}}</p></div>
                      <div class="col-md-2"><p><i>Completed Read Count</i>: {{minion.statistics.selected_completed_count}}</p></div>
                  </div>
                      <div class="row">
                          <div class="col-lg-12">
                              <div class="col-lg-2" id="{{minion.name}}"><div is="container-avg" :title="minion.name" :key="key" :datain="minion.statistics.read_event_count" :datain2="minion.statistics.selected_completed_count"></div></div>
                              <div class="col-lg-2" id="{{minion.name}}"><div is="container-temp" :title="minion.name" :key="key" :datain="minion.engine_states.minion_heatsink_temperature"></div></div>
                              <div class="col-lg-2" id="{{minion.name}}"><div is="container-chan" :title="minion.name" :key="key" :datain="minion.statistics.channels_with_read_event_count"></div></div>
                              <div class="col-lg-2" id="{{minion.name}}"><div is="container-strand" :title="minion.name" :key="key" :datain="minion.simplesummary"></div></div>
                              <div class="col-lg-2" id="{{minion.name}}"><div is="container-perc" :title="minion.name" :key="key" :datain="minion.simplesummary"></div></div>
                          </div>
                      </div>
                      <div class="row">
                  </div>

                  <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-12" id="{{minion.name}}"><div is="chartreadhist" :title="minion.name" :key="key" :datain="minion.statistics.read_event_count_weighted_hist" :datain2="minion.statistics.read_event_count_weighted_hist_bin_width"></div></div>
                            <div class="col-lg-6" id="{{minion.name}}"><div is="chartporehist" :title="minion.name" :key="key" :datain="minion.channelstuff" :datain2="minion.simplesummary"></div></div>
                            <div class="col-lg-6" id="{{minion.name}}"><div is="chartyield" :title="minion.name" :key="key" :datain="minion.engine_states.yield" :datain2="minion.yield_history"></div></div>
                            <div class="col-lg-6" id="{{minion.name}}"><div is="porehistory" :title="minion.name" :key="key" :datain2="minion.pore_history"></div></div>
                            <div class="col-lg-6" id="{{minion.name}}"><div is="perchistory" :title="minion.name" :key="key" :datain2="minion.pore_history"></div></div>
                            <div class="col-lg-6" id="{{minion.name}}"><div is="temphistory" :title="minion.name" :key="key" :datain2="minion.temp_history"></div></div>
                            <div class="col-lg-6" id="{{minion.name}}"><div is="volthistory" :title="minion.name" :key="key" :datain2="minion.temp_history"></div></div>

                        </div>
                    </div>
                    <!--  <div v-for="(key,value) in minion.engine_states">{{key}}:{{value}}</div>-->
                      </div>


                  <div v-else>
                      <p>This minION is not currently running.</p>
                      <button id='inactivateminion' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#{{minion.name}}offminionmodal'>
                        <i class='fa fa-stop'></i> Switch Off minION
                      </button>

                      <!-- Modal -->
                      <div class='modal fade' id='{{minion.name}}offminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                          <div class='modal-dialog'>
                              <div class='modal-content'>
                                  <div class='modal-header'>
                                      <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                      <h4 class='modal-title' id='myModalLabel'>Stop your minION</h4>
                                  </div>
                                  <div class='modal-body'>
                                      <div id='{{minion.name}}offminioninfo'>
                                          <p>This action will switch the minION to an inactive state. It should be possible to reactivate the minION remotely but software crashes on the minION controlling device may cause problems. You should only inactivate your minION device remotely if you are certain you wish to do so and <strong> at your own risk</strong>.</p>

                                          <p>If you are sure you wish to do this, click 'Inactivate minION' below. Otherwise close this window.</p>
                                      </div>
                                      <div class='modal-footer'>
                                          <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                          <button v-on:click="inactivateminion" id='{{minion.name}}' type='button' class='btn btn-danger' data-dismiss='modal'>Switch Off minION</button>
                                      </div>
                                  </div><!-- /.modal-content -->
                              </div><!-- /.modal-dialog -->
                          </div><!-- /.modal -->
                      </div>
                  </div>

          </div>
          </div>

            <div class="col-md-4">
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
                                              <p>You can rename a run if you wish to do so. Note there is no need to do this unless you wish to change the sample ID for some reason.</p>
                                              <input type="text" id="{{minion.name}}newname" class="form-control" placeholder="New Run Name">
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
                          <h5>Rename Flow Cell:</h5>
                          <!--<button id='renamerun' type='button' class='btn btn-info btn-sm'><i class='fa fa-magic'></i> Rename Run</button>-->
                          <!-- Indicates a dangerous or potentially negative action -->
                          <!-- Button trigger modal -->
                          <button id='renameflowcell' class='btn btn-info btn-sm' data-toggle='modal' data-target='#{{minion.name}}renameflowcell'>
                            <i class='fa fa-magic'></i> Rename Flowcell
                          </button>
                          <!-- Modal -->
                          <div class='modal fade' id='{{minion.name}}renameflowcell' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                              <div class='modal-dialog'>
                                  <div class='modal-content'>
                                      <div class='modal-header'>
                                          <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                          <h4 class='modal-title' id='myModalLabel'>Rename Your Flowcell</h4>
                                      </div>
                                      <div class='modal-body'>
                                          <div id='{{minion.name}}renameflowcellinfo'>
                                              <p>You can rename a flowcell if you wish to do so. Note there is no need to do this unless you wish to change the Flowcell ID for some reason.</p>
                                              <input type="text" id="{{minion.name}}newflowcellname" class="form-control" placeholder="New Flowcell ID">
                                              <p>If you are sure you wish to do this enter your new Flowcell ID above and click 'Rename Run' below. Otherwise close this window.</p>
                                              <p> We dont recommend doing this when a run is in progress!</p>
                                          </div>
                                          <div class='modal-footer'>
                                              <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                              <button v-on:click="renameflowcellnow" id='{{minion.name}}' type='button' class='btn btn-danger' data-dismiss='modal'>Rename Flowcell</button>
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

            <div class="col-md-4">
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
                        <td>Flow Cell ID</td>
                        <td>{{minion.livedata.flow_cell_id.result}}</td>
                    </tr>
                    <tr>
                        <td>Run Name</td>
                        <td>{{minion.livedata.dataset.result}}</td>
                    </tr>
                    <tr>
                        <td>Voltage Offset</td>
                        <td>{{minion.livedata.biasvoltageget.result.bias_voltage * minion.livedata.bias_voltage_gain.result}} mV</td>
                    </tr>
                    <tr>
                        <td>Yield</td>
                        <td>{{minion.livedata.yield_res.result}} / {{minion.statistics.read_event_count}}</td>
                    </tr>
				</table>
				</div>
				</div>

            </div>


			</div>

            <div class="col-lg-4" id="{{minion.name}}"><div is="diskusage" :title="minion.livedata.machine_id.result" :key="key" :datain="minion.livedata.disk_space.result"></div></div>



      <div class = "col-md-12">
          <br>

</div>
        </div>

        <div v-else>
            <h5>You are interacting with minION: {{minion.name}}</h5>
            It is currently <i>inactive</i>.

            <!-- Indicates a dangerous or potentially negative action -->
            <!-- Button trigger modal -->
            <button id='initminion' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#{{minion.name}}initminionmodal'>
              <i class='fa fa-stop'></i> Initialise minION
            </button>

            <!-- Modal -->
            <div class='modal fade' id='{{minion.name}}initminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                            <h4 class='modal-title' id='myModalLabel'>Start your minION</h4>
                        </div>
                        <div class='modal-body'>
                            <div id='{{minion.name}}initminioninfo'>
                                <p>This action will switch the minION to the active state.</p>

                                <p>If you are sure you wish to do this, click 'Initialise minION' below. Otherwise close this window.</p>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                <button v-on:click="initminion" id='{{minion.name}}' type='button' class='btn btn-warning' data-dismiss='modal'>Initialise minION</button>
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
<label id="merged_message"></label><br />
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
      <script src="https://code.highcharts.com/highcharts-more.js"></script>
      <script src="https://code.highcharts.com/modules/solid-gauge.src.js"></script>
      <script src="js/json-patch.min.js"></script>



      <script>
  $(document).ready(function () {
    //creating useful functions
    function tohistogram(readeventcountweightedhist,readeventcountweightedhistbinwidth) {
        var results =[];
        var categories = [];
        //var counter = 0;
        //console.log(minionsthings.minions[minion].statistics.read_event_count_weighted_hist);
        for (var i = 0; i < readeventcountweightedhist.length; i++) {

            //if (readeventcountweightedhist[i] > 0){
                //counter+=1;
                //console.log(readeventcountweightedhistbinwidth);
                //console.log(i);
                //console.log(i*readeventcountweightedhistbinwidth);
                var category = String((i) * readeventcountweightedhistbinwidth) + " - " + String((i+1) * readeventcountweightedhistbinwidth) + " bp";
                categories.push(category);
                results.push({ "name": category, "y": readeventcountweightedhist[i] });
            //}
        }
        //console.log(results);
        return [results,categories];
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

    function formatBytes(bytes,decimals) {
       if(bytes == 0) return '0 Byte';
       var k = 1000;
       var dm = decimals + 1 || 3;
       var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
       var i = Math.floor(Math.log(bytes) / Math.log(k));
       return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }


    var ws = null;

    //change example.com with your IP or your host
    function start() {
        var jsonholder= {};
        //ws = new ReconnectingWebSocket("ws://127.0.0.1:8080/ws");
        ws = new ReconnectingWebSocket("ws://<?php echo $_SERVER['SERVER_NAME']; ?>:8080/ws");

        //alert ("<?php echo $_SESSION['user_name'];?>");
        ws.onopen = function(evt) {
            //alert ("weve connected");
          var conn_status = document.getElementById('conn_text');
          conn_status.innerHTML = "Connection status: Connected!";
          var subscribemessage={"SUBSCRIBE":"<?php echo $_SESSION['user_name'];?>"};
          ws.send(JSON.stringify(subscribemessage));
        };
        ws.onmessage = function(evt) {
          var newMessage = document.createElement('p');
          newMessage.textContent = "Server: " + evt.data;
          var message_status = document.getElementById('server_message');
          message_status.innerHTML = evt.data;
          var jsonreturn = JSON.parse(evt.data);
          jsonpatch.apply(jsonholder,jsonreturn);
          jsonreturn=jsonholder;
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
              var adder=0;
              for (var thing in minionsthings.minions) {
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
                      if (minionsthings.minions[thing].temp_history != jsonreturn[prop].temp_history){
                          minionsthings.minions[thing].temp_history = jsonreturn[prop].temp_history;
                      }
                      if (minionsthings.minions[thing].pore_history != jsonreturn[prop].pore_history){
                          minionsthings.minions[thing].pore_history = jsonreturn[prop].pore_history;
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
                  minionsthings.minions.push({ name: prop ,simplechanstats: jsonreturn[prop].simplesummary,simplesummary: jsonreturn[prop].simplesummary,channel_info: jsonreturn[prop].detailsdata.channel_info, yield_history: jsonreturn[prop].yield_history, temp_history: jsonreturn[prop].temp_history, pore_history: jsonreturn[prop].pore_history, timestamp: jsonreturn[prop].detailsdata.timestamp, channelstuff: jsonreturn[prop].channelstuff,statistics: jsonreturn[prop].detailsdata.statistics,multiplex_states: jsonreturn[prop].detailsdata.multiplex_states, engine_states: jsonreturn[prop].detailsdata.engine_states, state: jsonreturn[prop].state ,scripts: jsonreturn[prop].scripts , livedata: jsonreturn[prop].livedata, comms: jsonreturn[prop].comms});
              }


              //console.log(minionsthings.minions);

          }

        };
        ws.onclose = function(evt) {
          //alert ("Connection closed");
          var conn_status = document.getElementById('conn_text');
          conn_status.innerHTML = "Connection status: Closed. <br>This page will try to reconnect automatically. <br>You do not need to refresh.";
          minionsthings.minions=[];
          //check();

        };
    }
    function round(value, decimals) {
        return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
    }

    start();

    Vue.filter('reverse', function(value){
        return value;
    })
    Vue.component('channelstatescalc',{
        template:"<hr><div v-for='item in countdata'><div class='col-md-1'><div class='row'><font size='6' color='{{item.colour}}'>&#x25CF</font> {{item.count}} </div><div class='row'>{{item.label}}</div></div></div></div>",
        props:['title','key','datain2','counts'],
        data: function(){
            var countsobject = {};
            var finalobject={};
            for (var key in this.counts){
                countsobject[key]=this.counts[key];
            }
            for (var key in this.datain2){
                for (var key2 in this.datain2[key]){
                    if (key2=="name"){
                        finalobject[this.datain2[key][key2]]={}
                        if (this.datain2[key][key2] in countsobject){
                            finalobject[this.datain2[key][key2]]["count"]=countsobject[this.datain2[key][key2]];
                            finalobject[this.datain2[key][key2]]["colour"]=this.datain2[key]["style"]["colour"];
                            finalobject[this.datain2[key][key2]]["label"]=this.datain2[key]["style"]["label"];
                            finalobject[this.datain2[key][key2]]["name"]=this.datain2[key][key2];
                        }else{
                            finalobject[this.datain2[key][key2]]["count"]=0;
                            finalobject[this.datain2[key][key2]]["name"]=this.datain2[key][key2];
                            if ("style" in this.datain2[key]){
                                finalobject[this.datain2[key][key2]]["colour"]=this.datain2[key]["style"]["colour"];
                                finalobject[this.datain2[key][key2]]["label"]=this.datain2[key]["style"]["label"];
                            }
                        }
                    }
                }
            }
            return {countdata:finalobject}
        },
        ready:function () {
            this.$nextTick(function() {
                setInterval(function () {
                    var countsobject = {};
                    var finalobject={};
                    for (var key in this.counts){
                        countsobject[key]=this.counts[key];
                    }
                    for (var key in this.datain2){
                        for (var key2 in this.datain2[key]){
                            if (key2=="name"){
                                finalobject[this.datain2[key][key2]]={}
                                if (this.datain2[key][key2] in countsobject){
                                    finalobject[this.datain2[key][key2]]["count"]=countsobject[this.datain2[key][key2]];
                                    finalobject[this.datain2[key][key2]]["colour"]=this.datain2[key]["style"]["colour"];
                                    finalobject[this.datain2[key][key2]]["label"]=this.datain2[key]["style"]["label"];
                                    finalobject[this.datain2[key][key2]]["name"]=this.datain2[key][key2];
                                }else{
                                    finalobject[this.datain2[key][key2]]["count"]=0;
                                    finalobject[this.datain2[key][key2]]["name"]=this.datain2[key][key2];
                                    if ("style" in this.datain2[key]){
                                        finalobject[this.datain2[key][key2]]["colour"]=this.datain2[key]["style"]["colour"];
                                        finalobject[this.datain2[key][key2]]["label"]=this.datain2[key]["style"]["label"];
                                    }
                                }
                            }
                        }
                    }
                    this.countdata=finalobject;
                }.bind(this), 5000);
            });
        }
    })
    Vue.component('diskusage',{
        template:"<div class='panel panel-info'><div class='panel-heading'><h3 class='panel-title'>Disk Space</h3></div><div class='panel-body'><div class='table-responsive'><table class='table table-condensed' ><tr><th>Category</td><th>Info</td></tr><tr><td>minKNOW computer name</td><td>{{title}}</td></tr><tr><td>Total Drive Capacity</td><td>{{capacity}}</td></tr><tr><td>Free Drive Space</td><td>{{space}} / {{percent}}%</td></tr><tr><td>Disk Space till Shutdown</td><td>{{bytealert}}</td></tr><tr><td>Warnings?</td><td>{{recalert}}</td></tr></table></div></div></div>",
        props:['title','key','datain'],
        data: function(){
            var bytes_available = formatBytes(this.datain[0].bytes_available);
            var drive_capacity = formatBytes(this.datain[0].bytes_capacity);
            var percentage = (this.datain[0].bytes_available/this.datain[0].bytes_capacity * 100).toFixed(2);
            var bytes_to_alert = formatBytes(this.datain[0].bytes_when_alert_issued);
            var recommend_alert = this.datain[0].recommend_alert;
            return {space:bytes_available,capacity:drive_capacity,percent:percentage,bytealert:bytes_to_alert,recalert:recommend_alert}

        },
        ready:function () {
            this.$nextTick(function() {
                setInterval(function () {
                    this.space = formatBytes(this.datain[0].bytes_available);
                    this.capacity = formatBytes(this.datain[0].bytes_capacity);
                    this.percent = (this.datain[0].bytes_available/this.datain[0].bytes_capacity * 100).toFixed(2);
                    this.bytealert = formatBytes(this.datain[0].bytes_when_alert_issued);
                    this.recalert = this.datain[0].recommend_alert;
                }.bind(this), 5000);
            });
        }
    })
    Vue.component('chartporehist', {
	template: '<div id="container-pore{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain','datain2'],
    data: function() {
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'container-pore'+this.title,
                    type:'column',
                    zoomType: 'xy',
                    height: 300,
                    animation: false
	        	},
    	    	title: {
        	    	text: 'Pore States'
	        	},
                xAxis: {
                    categories: []
                },
                //colors:[],
                yAxis: {
                    title: {
                        //text: 'Pore Type'
                    }
                },
                legend: {
            enabled: false
        },
                plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    formatter:function() {
                        return this.y;
                    }
                }
            }
        },
                credits: {
                    enabled: false
                },
                series: [{
                    name: 'porestates',
                    data:[],
                }
                ],

         			}
    	    }
    }
    ,


    created: function() {
    },
    ready: function() {
        //var returndata=parsechanstats(this.datain,this.datain2);
        //console.log(returndata);
        //var returndata = tohistogram(this.datain,parseInt(this.datain2));
        //this.chart.series[0].setData(returndata[3]);
        //console.log(returndata[2]);
        //this.chart.xAxis[0].setCategories(returndata[2]);
        //var returndata=parsechanstats(this.datain,this.datain2);
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            //minion=this.key;
            setInterval(function () {
                //console.log(this.datain);
                //console.log(this.datain2);
                var returndata=parsechanstats(this.datain,this.datain2);
                //console.log(returndata);
                //var returndata = tohistogram(this.datain,parseInt(this.datain2));
                this.chart.series[0].setData(returndata[3]);
                //console.log(returndata[2]);
                this.chart.xAxis[0].setCategories(returndata[2]);
                //while(this.chart.series.length > 0)
                //    this.chart.series[0].remove(true);
                //for (var i = 0; i < returndata[0].length; i++) {
                //    this.chart.addSeries(returndata[0][i]);
                //}
                //this.chart.colors=returndata[1];
                //this.chart.redraw();
                //console.log(returndata[1]);
        }.bind(this), 5000);
            });
        }
    })

    function parsechanstats(descriptions,counts) {
        var results =[];
        var colors = [];
        var categories = [];
        var datam = [];
        //console.log(descriptions);
        //console.log(counts);

        //for (var i = 0; i < descriptions.length; i++) {
        for (var thing in descriptions) {
            if (descriptions.hasOwnProperty(thing)) {
                //console.log(thing);
            //    console.log(descriptions[thing]);
                if (descriptions[thing].hasOwnProperty("style")){
            //        console.log(descriptions[thing]["style"]["label"]);
            //        console.log(descriptions[thing]["style"]["colour"]);

                    if(counts.hasOwnProperty(descriptions[thing]["name"])){
                        var porenumber = counts[descriptions[thing]["name"]];
                        //console.log(counts[descriptions[thing]["name"]]);
                    }else{
                        var porenumber = 0;
                        //console.log("0");
                    }
                    results.push({"name":descriptions[thing]["style"]["label"], "data":[{"y":porenumber}],"color":"#"+descriptions[thing]["style"]["colour"]});
                    colors.push("#"+descriptions[thing]["style"]["colour"]);
                    categories.push(descriptions[thing]["style"]["label"]);
                    datam.push({"y":porenumber,"color":"#"+descriptions[thing]["style"]["colour"]});
                }
            }
        }
        return [results,colors,categories,datam];
    }

    Vue.component('chartreadhist', {
	template: '<div id="container{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain','datain2'],
    data: function() {
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'container'+this.title,
                    type:'column',
                    zoomType: 'x'
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
                //data: this.datain
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
                //console.log(this.datain2);
                var returndata = tohistogram(this.datain,parseInt(this.datain2));
                this.chart.series[0].setData(returndata[0]);
                this.chart.xAxis[0].setCategories(returndata[1]);
                //this.chart.series[0].setData(this.datain);
                //this.chart.redraw();
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

    Vue.component('container-avg', {
        template: '<div id="container-avg{{title}}" style="height: 140px; margin: 0 auto"</div>',
        props: ['title','key','datain','datain2'],
        data: function() {
            return {

                opts: {
                    chart: {
                        renderTo: 'container-avg'+this.title,
                        type: 'solidgauge'
                    },

                    title: "Average Read Length (Events)",

                    pane: {
                        center: ['50%', '85%'],
                        size: '100%',
                        startAngle: -90,
                        endAngle: 90,
                        background: {
                            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                            innerRadius: '60%',
                            outerRadius: '100%',
                            shape: 'arc'
                        }
                    },

                    tooltip: {
                        enabled: false
                    },

                    // the value axis
                    yAxis: {
                        type: 'logarithmic',
                        stops: [
                            [0.3, '#0000FF'], // blue
                            [0.37, '#DDDF0D'], // green
                            [0.43, '#DF5353'], // red
                        ],
                        lineWidth: 0,
                        minorTickInterval: null,
                        tickPixelInterval: 400,
                        tickWidth: 0,
                        title: {
                            y: -70
                        },
                        labels: {
                            y: 16
                        },
                        min: 0.1,
                        max: 100000,
                        title: {
                            text: null
                        }
                    },

                    plotOptions: {
                        solidgauge: {
                            dataLabels: {
                                y: -30,
                                borderWidth: 0,
                                useHTML: true
                            }
                        }
                    },


       credits: {
           enabled: false
       },

       series: [{
           name: 'Events',
           data: [0],
           dataLabels: {
               format: '<div style="text-align:center"><span style="font-size:15px;color:' +
               ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
               '<span style="font-size:12px;color:silver"> Avg event len</span></div>'
           },
           tooltip: {
               valueSuffix: ' events'
           }
       }],

                    plotOptions: {
                        solidgauge: {
                            dataLabels: {
                                y: 30,
                                borderWidth: 0,
                                useHTML: true
                            }
                        }
                    }
                }
            }
        }


        ,
        ready: function() {
            //alert(this.datain);

          this.$nextTick(function() {
          		this.chart = new Highcharts.Chart(this.opts);
                //this.chart.series[0].setData(this.datain);
                if (this.chart) {
                    //point = this.chart.series[0].points[0];
                    this.chart.series[0].points[0].update(round(parseFloat(this.datain/this.datain2),0));
                    //point.update(this.datain);
                    //alert("camel");
                }

                setInterval(function () {
                //    this.chart.series[0].setData(this.datain);
                //    this.chart.redraw();
                if (this.chart) {
                    point = this.chart.series[0].points[0];

                    point.update(round(parseFloat(this.datain/this.datain2),0));
                    //this.chart.redraw();


                }
            }.bind(this), 5000);
                });
            }
        }),

    Vue.component('container-temp', {
        template: '<div id="container-temp{{title}}" style="height: 140px; margin: 0 auto"</div>',
        props: ['title','key','datain'],
        data: function() {
            return {

                opts: {
                    chart: {
                        renderTo: 'container-temp'+this.title,
                        type: 'solidgauge'
                    },

                    title: "Temperature",

                    pane: {
                        center: ['50%', '85%'],
                        size: '100%',
                        startAngle: -90,
                        endAngle: 90,
                        background: {
                            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                            innerRadius: '60%',
                            outerRadius: '100%',
                            shape: 'arc'
                        }
                    },

                    tooltip: {
                        enabled: false
                    },

                    // the value axis
                    yAxis: {
                        stops: [
                            [0.3, '#0000FF'], // blue
                            [0.37, '#DDDF0D'], // green
                            [0.43, '#DF5353'], // red
                        ],
                        lineWidth: 0,
                        minorTickInterval: null,
                        tickPixelInterval: 400,
                        tickWidth: 0,
                        title: {
                            y: -70
                        },
                        labels: {
                            y: 16
                        },
                        min: 0,
                        max: 70,
                        title: {
                            text: null
                        }
                    },

                    plotOptions: {
                        solidgauge: {
                            dataLabels: {
                                y: -30,
                                borderWidth: 0,
                                useHTML: true
                            }
                        }
                    },


       credits: {
           enabled: false
       },

       series: [{
           name: 'Temp °C',
           data: [0],
           dataLabels: {
               format: '<div style="text-align:center"><span style="font-size:15px;color:' +
               ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
               '<span style="font-size:12px;color:silver"> °C</span></div>'
           },
           tooltip: {
               valueSuffix: ' °C'
           }
       }],

                    plotOptions: {
                        solidgauge: {
                            dataLabels: {
                                y: 30,
                                borderWidth: 0,
                                useHTML: true
                            }
                        }
                    }
                }
            }
        }


        ,
        ready: function() {
            //alert(this.datain);

          this.$nextTick(function() {
          		this.chart = new Highcharts.Chart(this.opts);
                //this.chart.series[0].setData(this.datain);
                if (this.chart) {
                    //point = this.chart.series[0].points[0];
                    this.chart.series[0].points[0].update(this.datain);
                    //point.update(this.datain);
                    //alert("camel");
                }

                setInterval(function () {
                //    this.chart.series[0].setData(this.datain);
                //    this.chart.redraw();
                if (this.chart) {
                    point = this.chart.series[0].points[0];

                    point.update(round(parseFloat(this.datain),2));
                    //this.chart.redraw();


                }
            }.bind(this), 5000);
                });
            }
        }),

        Vue.component('container-chan', {
            template: '<div id="container-chan{{title}}" style="height: 140px; margin: 0 auto"</div>',
            props: ['title','key','datain'],
            data: function() {
                return {
                    opts: {
                        chart: {
                            renderTo: 'container-chan'+this.title,
                            type: 'solidgauge'
                        },

                        title: null,

                        pane: {
                            center: ['50%', '85%'],
                            size: '100%',
                            startAngle: -90,
                            endAngle: 90,
                            background: {
                                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                                innerRadius: '60%',
                                outerRadius: '100%',
                                shape: 'arc'
                            }
                        },

                        tooltip: {
                            enabled: false
                        },

                        // the value axis
                        yAxis: {
                            stops: [


                                [0.5, '#DF5353'], // red
                                [0.75, '#DDDF0D'], // yellow
                                [0.9, '#55BF3B'], // green
                            ],
                            lineWidth: 0,
                            minorTickInterval: null,
                            tickPixelInterval: 400,
                            tickWidth: 0,
                            title: {
                                y: -70
                            },
                            labels: {
                                y: 16
                            },
                            min: 0,
                            max: 512,
                            title: {
                                text: null
                            }
                        },

                        plotOptions: {
                            solidgauge: {
                                dataLabels: {
                                    y: -30,
                                    borderWidth: 0,
                                    useHTML: true
                                }
                            }
                        },


           credits: {
               enabled: false
           },

           series: [{
               name: 'Used Channels',
               data: [0],
               dataLabels: {
                   format: '<div style="text-align:center"><span style="font-size:15px;color:' +
                   ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                   '<span style="font-size:12px;color:silver"> Used Channels</span></div>'
               },
               tooltip: {
                   valueSuffix: ' Channel Count'
               }
           }],

                        plotOptions: {
                            solidgauge: {
                                dataLabels: {
                                    y: 30,
                                    borderWidth: 0,
                                    useHTML: true
                                }
                            }
                        }
                    }
                }
            }
            ,
            ready: function() {
                //alert(this.datain);

              this.$nextTick(function() {
              		this.chart = new Highcharts.Chart(this.opts);
                    //this.chart.series[0].setData(this.datain);
                    if (this.chart) {
                        //point = this.chart.series[0].points[0];
                        this.chart.series[0].points[0].update(this.datain);
                        //point.update(this.datain);
                        //alert("camel");
                    }

                    setInterval(function () {
                    //    this.chart.series[0].setData(this.datain);
                    //    this.chart.redraw();
                    if (this.chart) {
                        point = this.chart.series[0].points[0];

                        point.update(parseFloat(this.datain));
                        //this.chart.redraw();


                    }
                }.bind(this), 5000);
                    });
                }
            }),

            Vue.component('container-strand', {
                template: '<div id="container-strand{{title}}" style="height: 140px; margin: 0 auto"</div>',
                props: ['title','key','datain'],
                data: function() {
                    return {
                        opts: {
                            chart: {
                                renderTo: 'container-strand'+this.title,
                                type: 'solidgauge'
                            },

                            title: null,

                            pane: {
                                center: ['50%', '85%'],
                                size: '100%',
                                startAngle: -90,
                                endAngle: 90,
                                background: {
                                    backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                                    innerRadius: '60%',
                                    outerRadius: '100%',
                                    shape: 'arc'
                                }
                            },

                            tooltip: {
                                enabled: false
                            },

                            // the value axis
                            yAxis: {
                                stops: [


                                    [0.5, '#DF5353'], // red
                                    [0.75, '#DDDF0D'], // yellow
                                    [0.9, '#55BF3B'], // green
                                ],
                                lineWidth: 0,
                                minorTickInterval: null,
                                tickPixelInterval: 400,
                                tickWidth: 0,
                                title: {
                                    y: -70
                                },
                                labels: {
                                    y: 16
                                },
                                min: 0,
                                max: 512,
                                title: {
                                    text: null
                                }
                            },

                            plotOptions: {
                                solidgauge: {
                                    dataLabels: {
                                        y: -30,
                                        borderWidth: 0,
                                        useHTML: true
                                    }
                                }
                            },


               credits: {
                   enabled: false
               },

               series: [{
                   name: 'In Strand',
                   data: [0],
                   dataLabels: {
                       format: '<div style="text-align:center"><span style="font-size:15px;color:' +
                       ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                       '<span style="font-size:12px;color:silver"> In Strand</span></div>'
                   },
                   tooltip: {
                       valueSuffix: ' In Strand'
                   }
               }],

                            plotOptions: {
                                solidgauge: {
                                    dataLabels: {
                                        y: 30,
                                        borderWidth: 0,
                                        useHTML: true
                                    }
                                }
                            }
                        }
                    }
                }
                ,
                ready: function() {
                    //alert(this.datain);

                  this.$nextTick(function() {
                  		this.chart = new Highcharts.Chart(this.opts);
                        //this.chart.series[0].setData(this.datain);
                        if (this.chart) {
                            //point = this.chart.series[0].points[0];
                            this.chart.series[0].points[0].update(this.datain);
                            //point.update(this.datain);
                            //alert("camel");
                        }

                        setInterval(function () {
                        //    this.chart.series[0].setData(this.datain);
                        //    this.chart.redraw();
                        if (this.chart) {
                            point = this.chart.series[0].points[0];
                            //console.log(this.datain);

                            point.update(parseFloat(this.datain["strand"]));
                            //this.chart.redraw();


                        }
                    }.bind(this), 5000);
                        });
                    }
                }),

                Vue.component('container-perc', {
                    template: '<div id="container-perc{{title}}" style="height: 140px; margin: 0 auto"</div>',
                    props: ['title','key','datain'],
                    data: function() {
                        return {
                            opts: {
                                chart: {
                                    renderTo: 'container-perc'+this.title,
                                    type: 'solidgauge'
                                },

                                title: null,

                                pane: {
                                    center: ['50%', '85%'],
                                    size: '100%',
                                    startAngle: -90,
                                    endAngle: 90,
                                    background: {
                                        backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                                        innerRadius: '60%',
                                        outerRadius: '100%',
                                        shape: 'arc'
                                    }
                                },

                                tooltip: {
                                    enabled: false
                                },

                                // the value axis
                                yAxis: {
                                    stops: [


                                        [0.5, '#DF5353'], // red
                                        [0.75, '#DDDF0D'], // yellow
                                        [0.9, '#55BF3B'], // green
                                    ],
                                    lineWidth: 0,
                                    minorTickInterval: null,
                                    tickPixelInterval: 400,
                                    tickWidth: 0,
                                    title: {
                                        y: -70
                                    },
                                    labels: {
                                        y: 16
                                    },
                                    min: 0,
                                    max: 100,
                                    title: {
                                        text: null
                                    }
                                },

                                plotOptions: {
                                    solidgauge: {
                                        dataLabels: {
                                            y: -30,
                                            borderWidth: 0,
                                            useHTML: true
                                        }
                                    }
                                },


                   credits: {
                       enabled: false
                   },

                   series: [{
                       name: '% Occupancy',
                       data: [0],
                       dataLabels: {
                           format: '<div style="text-align:center"><span style="font-size:15px;color:' +
                           ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                           '<span style="font-size:12px;color:silver"> % Occupancy</span></div>'
                       },
                       tooltip: {
                           valueSuffix: ' % Occupancy'
                       }
                   }],

                                plotOptions: {
                                    solidgauge: {
                                        dataLabels: {
                                            y: 30,
                                            borderWidth: 0,
                                            useHTML: true
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ,
                    ready: function() {
                        //alert(this.datain);

                      this.$nextTick(function() {
                            this.chart = new Highcharts.Chart(this.opts);
                            //this.chart.series[0].setData(this.datain);
                            if (this.chart) {
                                //point = this.chart.series[0].points[0];
                                this.chart.series[0].points[0].update(this.datain);
                                //point.update(this.datain);
                                //alert("camel");
                            }

                            setInterval(function () {
                            //    this.chart.series[0].setData(this.datain);
                            //    this.chart.redraw();
                            if (this.chart) {
                                point = this.chart.series[0].points[0];
                                console.log(this.datain);
                                var single = 0;
                                if (parseFloat(this.datain["good_single"]) > 0) {
                                    single = parseFloat(this.datain["good_single"]);
                                }
                                round()
                                point.update(round(((parseFloat(this.datain["strand"])/(parseFloat(this.datain["strand"])+single))*100),2));
                                //this.chart.redraw();


                            }
                        }.bind(this), 5000);
                            });
                        }
                    }),

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
                    zoomType: 'x',
                    height: 300,
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
                }],
                min: 0,
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


    ready: function() {
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            this.chart.series[0].setData(this.datain2);
            setInterval(function () {
                //console.log(this.datain2);
                this.chart.series[0].setData(this.datain2);
                this.chart.redraw();
        }.bind(this), 5000);
            });
        }
    })

    Vue.component('perchistory', {
	template: '<div id="perchistory{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain2'],
    data: function() {
        //var d = new Date();
        //var t = d.getTime();
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'perchistory'+this.title,
                    type:'spline',
                    zoomType: 'x',
                    height: 300,
	        	},
    	    	title: {
        	    	text: '% Occupancy Over Time'
	        	},
                xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: {
                    text: '% Occupancy'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                min: 0,
                max: 100,
            },
            credits: {
                enabled: false
            },
            series: [{
                name: '% Occupancy',
                data: []
            }
            ]
         			}
    	    }
    }
    ,
    ready: function() {
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            this.chart.series[0].setData(this.datain2["percent"]);
            setInterval(function () {
                //console.log(this.datain2);
                this.chart.series[0].setData(this.datain2["percent"]);
                this.chart.redraw();
        }.bind(this), 5000);
            });
        }
    })

    Vue.component('porehistory', {
	template: '<div id="porehistory{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain2'],
    data: function() {
        //var d = new Date();
        //var t = d.getTime();
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'porehistory'+this.title,
                    type:'spline',
                    zoomType: 'x',
                    height: 300,
	        	},
    	    	title: {
        	    	text: 'In Strand Counts'
	        	},
                xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: {
                    text: 'Number of Pores In Strand/Single'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                min: 0,
            },
            credits: {
                enabled: false
            },
            series: [
                {
                    name: 'In Strand',
                    data: []
                },{
                    name: 'Single Pore',
                    data: []
                }
            ]
         			}
    	    }
    }
    ,
    ready: function() {
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            this.chart.series[0].setData(this.datain2["strand"]);
            this.chart.series[1].setData(this.datain2["single"]);
            setInterval(function () {
                //console.log(this.datain2);
                this.chart.series[0].setData(this.datain2["strand"]);
                this.chart.series[1].setData(this.datain2["single"]);
                this.chart.redraw();
        }.bind(this), 5000);
            });
        }
    })

    Vue.component('temphistory', {
	template: '<div id="temphistory{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain2'],
    data: function() {
        //var d = new Date();
        //var t = d.getTime();
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'temphistory'+this.title,
                    type:'spline',
                    zoomType: 'x',
                    height: 300,
	        	},
    	    	title: {
        	    	text: 'Temperature over time '
	        	},
                xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: {
                    text: 'Temperature Celcius'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                min: 0,
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Asic Temperature',
                data: []
            },{
                name: 'Heat Sink Temperature',
                data: []
            }
            ]
         			}
    	    }
    }
    ,
    ready: function() {
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            this.chart.series[0].setData(this.datain2["asictemp"]);
            this.chart.series[1].setData(this.datain2["heatsinktemp"]);
            setInterval(function () {
                //console.log(this.datain2["asictemp"]);
                this.chart.series[0].setData(this.datain2["asictemp"]);
                this.chart.series[1].setData(this.datain2["heatsinktemp"]);
                this.chart.redraw();
        }.bind(this), 5000);
            });
        }
    })

    Vue.component('volthistory', {
	template: '<div id="volthistory{{title}}" style="margin: 0 auto"</div>',
    props: ['title','key','datain2'],
    data: function() {
        //var d = new Date();
        //var t = d.getTime();
        return {
        	opts: {
		        chart: {
        	    	renderTo: 'volthistory'+this.title,
                    type:'spline',
                    zoomType: 'x',
                    height: 300,
	        	},
    	    	title: {
        	    	text: 'Global Voltage over time '
	        	},
                xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: {
                    text: 'Voltage'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                //min: 0,
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Global Voltage',
                data: []
            }
            ]
         			}
    	    }
    }
    ,
    ready: function() {
      this.$nextTick(function() {
      		this.chart = new Highcharts.Chart(this.opts);
            this.chart.series[0].setData(this.datain2["voltage"]);
            //this.chart.series[1].setData(this.datain2["heatsinktemp"]);
            setInterval(function () {
                //console.log(this.datain2["asictemp"]);
                this.chart.series[0].setData(this.datain2["voltage"]);
                //this.chart.series[1].setData(this.datain2["heatsinktemp"]);
                this.chart.redraw();
        }.bind(this), 5000);
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
                //alert(event.target.id);
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"rename","NAME":$("#"+event.target.id+"newname").val()}};
                ws.send(JSON.stringify(instructionmessage));
                //$('#renamemodal').modal('hide');
            },
            renameflowcellnow: function(event){
                var instructionmessage={"INSTRUCTION":{"USER":"<?php echo $_SESSION['user_name'];?>","minion":event.target.id,"JOB":"nameflowcell","NAME":$("#"+event.target.id+"newflowcellname").val()}};
                ws.send(JSON.stringify(instructionmessage));

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
                //alert("hello");
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
