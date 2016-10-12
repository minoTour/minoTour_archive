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
        <section class="content"><?php include 'includes/run_check.php';?>


                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Live Interaction</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="modal fade" id="pincheck" tabindex="-1"  data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                                    <h4 class="modal-title" id="myModalLabel">Warning: Pin Check</h4>
                                  </div>
                                  <div class="modal-body">
                                    This page is in development to exploit both Run Until and ultimately Read Until features in the minION platform.<br>These features provide two way interaction between minoTour and your minION device via minKNOW. You can stop or start runs remotely and interact with the minION device in other ways. This is an alpha service relying on the API developed by Oxford Nanopore.<br><br> We, the developers of minoTour, <strong>take no responsibility for any loss of sequencing data</strong> as a consequence of your use of these features. <br><br><strong>They are used at your own risk.</strong><br>
                                   In order to proceed you must provide the pin number you used to configure these features.
                                   <br>
                                   <div class="form-group">
                                		<label for="inputPassword3" class="control-label">Enter Pin</label>

                                		  <input type="password" class="form-control" id="pincheckfield" placeholder="pin">

                                  	</div>
                                  	<div class="modal-footer">
                                    <button type="button" id="pinback" class="btn btn-default">Cancel</button>
                                    <button type="button" id="pincheckbutton" class="btn btn-primary">Check Pin</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                                            <?php


                            	//Check if INTERACTION Table already exists:
                            		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
                            		$query = "SHOW TABLES LIKE 'interaction';";
                            		$sql = $mindb_connection->query($query);
                            		$result = $sql->num_rows;
                            		//echo $query . "\n";
                            		//echo $result . "\n";

                            		if ($result >= 1){
                            			//echo "Table exists";
                            			//$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jsonjobname . "','".$jsonstring . "');";
                            			//$go = $mindb_connection->query($insertresult);
                            		}else{
                            			//echo "Table needs creating";
                            			$create_table =
                            			"CREATE TABLE `interaction` (
                              `job_index` INT NOT NULL AUTO_INCREMENT,
                              `instruction` MEDIUMTEXT NOT NULL,
                              `target` MEDIUMTEXT NOT NULL,
                              `param1` MEDIUMTEXT,
                              `param2` MEDIUMTEXT,
                             `complete` INT NOT NULL,
                              PRIMARY KEY (`job_index`)
                            )
                            CHARACTER SET utf8;";
                            			$create_tbl = $mindb_connection->query($create_table);
                            		}

                            			//Check if messages Table already exists:
                            		$query = "SHOW TABLES LIKE 'messages';";
                            		$sql = $mindb_connection->query($query);
                            		$result = $sql->num_rows;
                            		#echo $result;
                            		if ($result >= 1){
                            			}else{
                            				#echo "WE need to make a table...";
                            				$create_table2 =
                            			"CREATE TABLE `messages` (
                              `message_index` INT NOT NULL AUTO_INCREMENT,
                              `message` MEDIUMTEXT NOT NULL,
                              `target` MEDIUMTEXT NOT NULL,
                              `param1` MEDIUMTEXT,
                              `param2` MEDIUMTEXT,
                             `complete` INT NOT NULL,
                              PRIMARY KEY (`message_index`)
                            )
                            CHARACTER SET utf8;";
                            			#echo $create_table2;
                            			$create_tbl2 = $mindb_connection->query($create_table2);
                            			#echo $create_tbl2;

                            		}

                            		if ($_SESSION['currentbarcode'] >= 1){
                            			//Check if BARCODE_CONTROL table already exists
                            			$query = "SHOW TABLES LIKE 'barcode_control';";
                            			$sql = $mindb_connection->query($query);
                            			$result = $sql->num_rows;
                            			//echo $query . "\n";
                            			//echo $result . "\n";

                            			if ($result >= 1){
                            				//echo "Table exists";
                            				//$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jsonjobname . "','".$jsonstring . "');";
                            				//$go = $mindb_connection->query($insertresult);
                            			}else{
                            				//echo "Table needs creating";
                            				$create_table =
                            				"CREATE TABLE `barcode_control` (
                              `job_index` INT NOT NULL AUTO_INCREMENT,
                              `barcodeid` MEDIUMTEXT NOT NULL,
                             `complete` INT NOT NULL,
                              PRIMARY KEY (`job_index`)
                            )
                            CHARACTER SET utf8;";
                            				$create_tbl = $mindb_connection->query($create_table);
                            				$barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12"];
                            				foreach ($barcodes as $barcode) {
                            					$sqlinsert = "insert into barcode_control (barcodeid,complete) values (\"" . $barcode .	"\",0);";
                            					//echo $sqlinsert;
                            					$sqlinsertexecute = $mindb_connection->query($sqlinsert);
                            			}




                            		}
                            		}

                            ?>

                                            <!-- /.col-lg-12 -->
                                        </div>

                                        <p>Remote control of your minION device is available via these pages. To ensure security a matching pin number must be entered to access this page and on the minUP script controlling the minION device. If minUP is not running on the same machine as the minION device control of your sequencer is not possible via minoTour.</p>
                                        <div class="row">
                                            <div id="cumulativeyield" style="width:100%; height:300px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Cumulative Reads</div>



                                        </div>



                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-6">
                                                    <div class="col-md-12">
                                                		<div class="panel panel-default">
                                                			<div class="panel-heading">
                                                			<h3 class="panel-title">
                                                			<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modaldiskmonitor">
                                                			<i class="fa fa-info-circle"></i> Disk Space Monitoring
                                                			</button></h3>
                                                			<!-- Modal -->
                                                				<div class="modal fade" id="modaldiskmonitor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                					<div class="modal-dialog">
                                                						<div class="modal-content">
                                                							<div class="modal-header">
                                        									<button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        									<h4 class="modal-title" id="myModalLabel">Disk Space Monitoring</h4>
                                        			      					</div>
                                        			      						<div class="modal-body">
                                        										<p>Here you can choose to monitor the amount of hard drive space remaining for a given run.</p>
                                                                                <p>As standard you will recieve a warning via whichever communication method you have set if minKNOW itself believes disk space is likely to be a problem.</p>
                                        										</div>
                                        											<div class="modal-footer">
                                        											<button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>													</div>
                                        										</div>
                                        									</div>
                                        								</div>
                                                			</div>
                                                			<div id="coverage">
                                        						<div class="panel-body">
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                        Choose a minimum amout of space on your hard drive to be warned about.
                                                            			<br>
                                                                        <h4>Warn when space is dropping.</h4>
                                                                            <form class="form-inline">
                                                                			<label class="radio-inline">
                                                              					<input type="radio" name="diskspace" id="inlineRadio1" value="10" checked="checked"> Less than 10 gigs remaining.
                                                            				</label>
                                                            				<label class="radio-inline">
                                                            					<input type="radio" name="diskspace" id="inlineRadio2" value="20"> Less than 20 gigs remaining.
                                                            				</label>
                                                            				<label class="radio-inline">
                                                              					<input type="radio" name="diskspace" id="inlineRadio3" value="50"> Less than 50 gigs remaining.
                                                            				</label>
                                                            				<label class="radio-inline">
                                                              					<input type="radio" name="diskspace" id="inlineRadio4" value="100"> Less than 100 gigs remaining.
                                                            				</label>
                                                                            <br><br>
                                                                            <div class="form-group">
                                                                                <label for="disk_space_alert">Set Alert</label>
                                            									<span class="form-group-btn">
                                            									<button class="btn btn-default" id="disk_space_alert" type="button">Set</button>
                                            									</span>
                                            								</div>
                                                                        </form>
                                                              		  	</div><!-- /.col-lg-6 -->

                                                            		</div>
                                        						</div>
                                        					</div>
                                                		</div>
                                                	</div>
                                                    <div class="col-md-12">
                                                		<div class="panel panel-default">
                                                			<div class="panel-heading">
                                                			<h3 class="panel-title">
                                                			<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalsimpleexamples">
                                                			<i class="fa fa-info-circle"></i> Simple Examples
                                                			</button></h3>
                                                			<!-- Modal -->
                                                				<div class="modal fade" id="modalsimpleexamples" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                					<div class="modal-dialog">
                                                						<div class="modal-content">
                                                							<div class="modal-header">
                                        									<button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        									<h4 class="modal-title" id="myModalLabel">Simple Examples</h4>
                                        			      					</div>
                                        			      						<div class="modal-body">
                                        										<p>These are a number of simple alerts that can be configured to tell you about coverage depth or yield.</p>
                                                                                <p>These alerts will not trigger the run to end at completion, but will obey email or twitter notification rules as configured in the settings menu.</p>
                                        										</div>
                                        											<div class="modal-footer">
                                        											<button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>													</div>
                                        										</div>
                                        									</div>
                                        								</div>
                                                			</div>
                                                			<div id="coverage">
                                        						<div class="panel-body">
                                                                    <div class="row">
                                                                        <!--<div class="col-lg-12">

                                                                        <h4>Coverage Depth</h4>
                                                                			<div class="input-group">
                                                                  				<input id="foldchange" type="text" class="form-control">
                                                                  			  	<span class="input-group-btn">
                                                                    				<button class="btn btn-default" id="gen_coverage" type="button">Set</button>
                                                                  			  	</span>
                                                            				</input>
                                                            				</div>

                                                            				<label class="radio-inline">
                                                              					<input type="radio" name="coveragenoticeradio" id="inlineRadio1" value="All" checked="checked"> All
                                                            				</label>
                                                            				<label class="radio-inline">
                                                            					<input type="radio" name="coveragenoticeradio" id="inlineRadio2" value="Template"> Template
                                                            				</label>
                                                            				<label class="radio-inline">
                                                              					<input type="radio" name="coveragenoticeradio" id="inlineRadio3" value="Complement"> Complement
                                                            				</label>
                                                            				<label class="radio-inline">
                                                              					<input type="radio" name="coveragenoticeradio" id="inlineRadio4" value="2D"> 2D
                                                            				</label>
                                                                			  		  	</div>-->


                                                            			<div class="col-lg-12">
                                                                        As an example: set an alert for every X bases sequenced (again with reference to the template). This alert is non-persistent - it disappears.
                                                            			<br>
                                                                        <h4>Base Notification (strongly suggest minimum setting of 100000)</h4>
                                                                			<div class="input-group">
                                                                  				<input id="basenotification" type="text" class="form-control">
                                                                  			  	<span class="input-group-btn">
                                                                    				<button class="btn btn-default" id="base_notification" type="button">Set</button>
                                                                  			  	</span>
                                                                			</div><!-- /input-group -->
                                                                			<label class="radio-inline">
                                                              					<input type="radio" name="basenoticeradio" id="inlineRadio1" value="All" checked="checked"> All
                                                            				</label>
                                                            				<label class="radio-inline">
                                                            					<input type="radio" name="basenoticeradio" id="inlineRadio2" value="Template"> Template
                                                            				</label>
                                                            				<label class="radio-inline">
                                                              					<input type="radio" name="basenoticeradio" id="inlineRadio3" value="Complement"> Complement
                                                            				</label>
                                                            				<label class="radio-inline">
                                                              					<input type="radio" name="basenoticeradio" id="inlineRadio4" value="2D"> 2D
                                                            				</label>
                                                              		  	</div><!-- /.col-lg-6 -->

                                                            		</div>
                                        						</div>
                                        					</div>
                                                		</div>
                                                	</div>

                                                    <div class="col-md-12">
                                                		<div class="panel panel-default">
                                                			<div class="panel-heading">
                                                			<h3 class="panel-title">
                                                			<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalcoveragedepth">
                                                			<i class="fa fa-info-circle"></i> Coverage Depth
                                                			</button></h3>
                                                			<!-- Modal -->
                                                				<div class="modal fade" id="modalcoveragedepth" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                					<div class="modal-dialog">
                                                						<div class="modal-content">
                                                							<div class="modal-header">
                                        									<button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        									<h4 class="modal-title" id="myModalLabel">Coverage Summary</h4>
                                        			      					</div>
                                        			      						<div class="modal-body">
                                        										<p>  Here you can monitor the depth of coverage for a specific reference or region of a reference sequence. Set the desired coverage depth for the reference you wish to monitor coverage on. Optionally you can set one or more regions to monitor. The run will only be stopped when all the coverage criteria are satisfied. In all cases we monitor 2D coverage as determined by Last/BWA.<br>
                                        										<strong>Run Until Features:</strong><br>
                                        										Ticking the check box will instruct minoTour to switch off your sequencer when the set tasks are complete. If you use these features they are at <strong>your own risk</strong>.<br>
                                        										</div>
                                        											<div class="modal-footer">
                                        											<button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>													</div>
                                        										</div>
                                        									</div>
                                        								</div>
                                                			</div>
                                                			<div id="coverage">
                                        						<div class="panel-body">
                                        							<div class="row">
                                        								<div class = "col-lg-12">
                                        								Select the reference sequence, optional region and desired coverage depth.
                                        								<form class="form-inline">
                                        								<div class="input-group">
                                        									<label for="refselect">Choose Reference:</label>
                                        									<select class="form-control" id="refselect">
                                        										<?php foreach ($_SESSION['activerefnames'] as $reference) {?>
                                        											<option><?php echo $reference;?></option>
                                        										<?php }?>
                                        									</select>
                                        								</div>
                                        								<div class="input-group">
                                        									<label for="refcoveragedepth">Coverage Depth:</label>
                                        									<input type="text" class="form-control" value=0 id="refcoveragedepth"></input>
                                        								</div>
                                        								<div class="input-group">
                                        									<label for="refcoveragedepthstart">Optional Start:</label>
                                        									<input type="text" class="form-control" value=0 id="refcoveragedepthstart"></input>
                                        								</div>
                                        								<div class="input-group">
                                        									<label for="refcoveragedepthend">Optional End:</label>
                                        									<input type="text" class="form-control" value=0 id="refcoveragedepthend"></input>
                                        								</div>
                                        								<br>
                                        								<div class="form-group">
                                        									<label for="reference_coverage_button">Auto finish run</label>
                                        									<input type="checkbox" id="reference_coverage_stop" value="1"></input>
                                        									<span class="form-group-btn">
                                        									<button class="btn btn-default" id="reference_coverage_button" type="button">Set</button>
                                        									</span>
                                        								</div>
                                        								</form>

                                        								</div>
                                        							</div>
                                        						</div>
                                        					</div>
                                                		</div>
                                                	</div>

                                                    <?php if ($_SESSION['currentbarcode'] >= 1) {?>
                                            		<?php //We want to get the current values for the barcodes if they exists ?>
                                            		<?php
                                            			$barcodearray;
                                            			$barcodecheck = "SELECT * FROM alerts where name='barcodecoverage';";
                                            			$barcodecheckresult = $mindb_connection->query($barcodecheck);
                                            			foreach ($barcodecheckresult as $row) {
                                            				//echo $row['name'] . "\t" . $row['reference'] . "\t" . $row['threshold'] . "<br>";
                                            				$barcodearray[$row['reference']]=$row['threshold'];
                                            			}
                                            			$genbarcodecheck = "SELECT * FROM alerts where name = 'genbarcodecoverage';";
                                            			$genbarcodecheckresult = $mindb_connection->query($genbarcodecheck);
                                            			foreach ($genbarcodecheckresult as $row) {
                                            				//echo $row['name'] . "\t" . $row['reference'] . "\t" . $row['threshold'] . "<br>";
                                            				$barcodearray['genbarcodecoverage']=$row['threshold'];
                                            			}


                                            			?>
                                            		<div class="col-md-12">
                                            				<div class="panel panel-default">
                                            					<div class="panel-heading">
                                            					<h3 class="panel-title"><!-- Button trigger modal -->
                                            					<button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#modalbarcode">
                                            					<i class="fa fa-info-circle"></i> Barcode Interaction
                                            					</button></h3>
                                            					<!-- Modal -->
                                            						<div class="modal fade" id="modalbarcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            							<div class="modal-dialog">
                                            								<div class="modal-content">
                                            									<div class="modal-header">
                                            									<button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            									<h4 class="modal-title" id="myModalLabel">Barcoding Summary</h4>
                                            			      					</div>
                                            			      						<div class="modal-body">
                                            										<p>  Here you can monitor the depth of coverage for individual barcodes against a reference sequence. This method is designed for matching to a single reference sequence at this time. We will be introducing the ability to monitor over multipe references in the future.<br>Users can either set global thresholds for all barcodes or specify coverage on a barcode by barcode basis.<br>
                                            										<p>Note that a 0 threshold setting is ignored - i.e no threshold is set.</p>
                                            										<strong>Run Until Features:</strong><br>
                                            										Ticking the check box will instruct minoTour to switch off your sequencer when the set tasks are complete. For global coverage minoTour will only switch off the run if all the barcodes are at the set threshold for 2D reads. For instances where you set coverage on a barcode by barcode basis the run will be switched off once the set tasks are complete. If you are sequencing 12 barcodes but only set thresholds for three of them, the run will be switched off once the three are complete regardless of the state of the other 12. If you use these features they are at <strong>your own risk</strong>.<br>
                                            										</div>
                                            											<div class="modal-footer">
                                            											<button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>													</div>
                                            										</div>
                                            									</div>
                                            								</div>
                                            							</div>
                                            							<div id="barcoding">
                                            								<div class="panel-body">
                                            									<div class="row">
                                            										<div class = "col-lg-12">
                                            										<p>Checking these boxes may lead to your sequencer being switched off remotely in response to a depth of coverage alert. Ticking a box is <strong>at your own risk</strong>.</p>
                                            										<strong>Set Global barcode coverage threshold.</strong><br>
                                            										</div>
                                            									</div>
                                            										<div class="col-lg-12">
                                            								        Enter Desired Coverage Depth
                                            											<div class="input-group">
                                            											<span class="input-group-addon">
                                            											<input type="checkbox" id="genbarcode_coverage_stop" value="1">
                                            											</span>
                                            											<input id="genbarcodecov" value=<?php if ($barcodearray['genbarcodecoverage']>0){echo $barcodearray['genbarcodecoverage'];}else{echo "0";}?> type="text" class="form-control">
                                            											<span class="input-group-btn">
                                            											<button class="btn btn-default" id="genbarcode_coverage" type="button">Set</button>
                                            											</span>
                                            											</input>
                                            											</div>
                                            										</div>
                                            											<div class="row">
                                            												<div class = "col-lg-12">
                                            												<br>
                                            												<strong>Set coverage threshold/barcode.</strong><br>
                                            												<br>
                                            												</div>
                                            											</div>
                                            												<div class="col-lg-12">
                                            												<form class="form-inline">
                                            												<?php $barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12"];?>
                                            												<?php foreach ($barcodes as $barcode) {?>
                                            													<div class="form-group">
                                            													<label for="barcode_coverage_<?php echo $barcode; ?>"><?php echo $barcode; ?></label>
                                            													<input type="text" class="form-control" value=<?php if ($barcodearray[$barcode]>0){echo $barcodearray[$barcode];}else{echo "0";}?>  id="barcodecov<?php echo $barcode; ?>"></input>
                                            													</div>
                                            													<?php } ?>
                                            													<br><br>
                                            														<div class="form-group">
                                            														<label for="barcode_coverage_stop">Auto finish run</label>
                                            														<input type="checkbox" id="barcode_coverage_stop"></input>
                                            														<span class="form-group-btn">
                                            														<button class="btn btn-default" id="barcode_coverage_button" type="button">Set</button>
                                            														</span>
                                            														</div>
                                            													</div>
                                            													</form>
                                            												</div>
                                            											</div>
                                            									</div>
                                            								</div>
                                            								<?php }; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="col-md-12">
                                                		<div class="panel panel-warning">
                                                			<div class="panel-heading">
                                                			<h3 class="panel-title">
                                                			<button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalrunningqueries">
                                                			<i class="fa fa-info-circle"></i> Queries
                                                			</button></h3>

                                                			<!-- Modal -->
                                                				<div class="modal fade" id="modalrunningqueries" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                					<div class="modal-dialog">
                                                						<div class="modal-content">
                                                							<div class="modal-header">
                                        									<button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        									<h4 class="modal-title" id="myModalLabel">Queries Summary</h4>
                                        			      					</div>
                                        			      						<div class="modal-body">
                                        										<p> Here you can see all the current logic being processed by minoTour to decide if it should stop your run. It is possible to establish contradictory and overlapping logic. minoTour will switch off a run when all tasks in an individual category are complete. You should make sure that your run has the minimum constraints on it that are possible. You sould also delete constraints that you think are no longer valid.<br>
                                        										<strong>Run Until Features:</strong><br>
                                        										If you use these features they are at <strong>your own risk</strong>.<br>
                                        										</div>
                                        											<div class="modal-footer">
                                        											<button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>													</div>
                                        										</div>
                                        									</div>
                                        								</div>
                                        		        			</div>
                                        								<div id="coverage">
                                        						<div class="panel-body">
                                                                    <div id="messages"></div>

                                        							<div class="row">
                                        							<div class = "col-lg-12">

                                                                        <div id = "alertdat">
                                                                            <div v-for="(run,name) in basenotificationmaster.<?php echo $_SESSION['active_run_name']?>"></div>

                                                                            <br><Strong>Disk Use Notices.</strong><br>
                                                                                <div v-if="basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.disknotify">
                                                                            <div class = "row">
                                                                                <div class = "col-lg-12">

                                                                        <div class = "col-lg-3">
                                        									Gigabyte Alert
                                                                        </div>
                                                                        <div class = "col-lg-3">
                                        									Complete</strong>
                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div v-for="thing in basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.disknotify">
                                                                                <div class = "row">
                                                                                    <div class = "col-lg-12">

                                                                            <div class = "col-lg-3">
                                            									{{thing.threshold}} GB
                                                                            </div>

                                                                            <div class = "col-lg-3">
                                            									{{thing.complete}}
                                                                            </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <button v-on:click='remdisknotice' id="remdisknotice" type='button' class='btn btn-danger btn-xs'>Remove Disk Notification</button><br><br>
                                                                        </div>
                                                                        <div v-else><em>No specific space monitoring set..</em><br><br></div>


                                                                            <br><Strong>Global Barcode Theshold Set.</strong><br><br>
                                                                                <div v-if="basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.genbarcodecoverage">
                                                                            <!--<div class='table-responsive'>-->
                                        									<div class = "row">
                                                                                <div class = "col-lg-12">
                                        									<div class = "col-lg-3"><strong>
                                        									Reference
                                                                        </div>
                                                                        <div class = "col-lg-3">
                                        									Threshold
                                                                        </div>
                                                                        <div class = "col-lg-3">
                                        									Stop Run (1=Yes)
                                                                        </div>
                                                                        <div class = "col-lg-3">
                                        									Complete</strong>
                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div v-for="thing in basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.genbarcodecoverage">
                                                                                <div class = "row">
                                                                                    <div class = "col-lg-12">
                                            									<div class = "col-lg-3">
                                            									All Barcodes
                                                                            </div>
                                                                            <div class = "col-lg-3">
                                            									{{thing.threshold}}
                                                                            </div>
                                                                            <div class = "col-lg-3">
                                            									{{thing.control}}
                                                                            </div>
                                                                            <div class = "col-lg-3">
                                            									{{thing.complete}}
                                                                            </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <button v-on:click='removeglobthreshold' type='button' class='btn btn-danger btn-xs'>Remove Global Threshold</button><br><br>
                                                                        </div>
                                                                        <div v-else><em>No Global Barcode Theshold Set.</em><br><br></div>

                                                                            <br><br><Strong>Individual Barcode Theshold Set.</strong><br><br>
                                                                                <div v-if="basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.barcodecoverage">
                                                                            <!--<div class='table-responsive'>-->
                                        									<div class = "row">
                                                                                <div class = "col-lg-12">
                                        									<div class = "col-lg-3"><strong>
                                        									Reference
                                                                        </div>
                                                                        <div class = "col-lg-3">
                                        									Threshold
                                                                        </div>
                                                                        <div class = "col-lg-3">
                                        									Stop Run (1=Yes)
                                                                        </div>
                                                                        <div class = "col-lg-3">
                                        									Complete</strong>
                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div v-for="thing in basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.barcodecoverage">
                                                                                <div class = "row">
                                                                                    <div class = "col-lg-12">
                                            									<div class = "col-lg-3">
                                            									{{thing.reference}}
                                                                            </div>
                                                                            <div class = "col-lg-3">
                                            									{{thing.threshold}}
                                                                            </div>
                                                                            <div class = "col-lg-3">
                                            									{{thing.control}}
                                                                            </div>
                                                                            <div class = "col-lg-3">
                                            									{{thing.complete}}
                                                                            </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <button v-on:click="removethresholds" type='button' class='btn btn-danger btn-xs'>Remove Thresholds</button><br><br>
                                                                        </div>
                                                                        <div v-else><em>No Individual Barcode Thresholds Set.</em><br><br></div>

                                                                        <br><br><Strong>Reference Coverage Thresholds Set.</strong><br><br>
                                                                            <div v-if="basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.referencecoverage">
                                                                        <!--<div class='table-responsive'>-->
                                                                        <div class = "row">
                                                                            <div class = "col-lg-12">
                                                                        <div class = "col-lg-5"><strong>
                                                                        Reference
                                                                    </div>
                                                                    <div class = "col-lg-1">
                                                                        Limit
                                                                    </div>
                                                                    <div class = "col-lg-1">
                                                                        Start
                                                                    </div>
                                                                    <div class = "col-lg-1">
                                                                        End
                                                                    </div>
                                                                    <div class = "col-lg-1">
                                                                        Stop (1=Yes)
                                                                    </div>
                                                                    <div class = "col-lg-1">
                                                                        Done
                                                                    </div>
                                                                    <div class = "col-lg-1">
                                                                        Delete</strong>
                                                                    </div>
                                                                            </div>
                                                                        </div>
                                                                        <div v-for="thing in basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.referencecoverage">
                                                                            <div class = "row">
                                                                                <div class = "col-lg-12">
                                                                            <div class = "col-lg-5">
                                                                            {{thing.reference}}
                                                                        </div>
                                                                        <div class = "col-lg-1">
                                                                            {{thing.threshold}}
                                                                        </div>
                                                                        <div class = "col-lg-1">
                                                                            {{thing.start}}
                                                                        </div>
                                                                        <div class = "col-lg-1">
                                                                            {{thing.end}}
                                                                        </div>
                                                                        <div class = "col-lg-1">
                                                                            {{thing.control}}
                                                                        </div>
                                                                        <div class = "col-lg-1">
                                                                            {{thing.complete}}</strong>
                                                                        </div>
                                                                        <div class = "col-lg-1">
                                                                            <button v-on:click="removeref(thing.alert_index)" id='removeref{{thing.alert_index}}' type='button' value='{{thing.alert_index}}' class='btn btn-danger btn-xs'>Remove</button>

                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div v-else><em>No Reference Coverage Thresholds Set.</em><br><br></div>


                                                                <br><br><Strong>Base Notifications Set.</strong><br><br>
                                                                    <div v-if="basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.basenotification">
                                                                <!--<div class='table-responsive'>-->
                                                                <div class = "row">
                                                                    <div class = "col-lg-12">
                                                                <div class = "col-lg-5"><strong>
                                                                Type
                                                            </div>
                                                            <div class = "col-lg-2">
                                                                Base Number
                                                            </div>

                                                            <div class = "col-lg-1">
                                                                Delete</strong>
                                                            </div>
                                                                    </div>
                                                                </div>
                                                                <div v-for="thing in basenotificationmaster.<?php echo $_SESSION['active_run_name']?>.basenotification">
                                                                    <div class = "row">
                                                                        <div class = "col-lg-12">
                                                                    <div class = "col-lg-5">
                                                                    {{thing.type}}
                                                                </div>
                                                                <div class = "col-lg-2">
                                                                    {{thing.threshold}}
                                                                </div>

                                                                <div class = "col-lg-1">
                                                                    <button v-on:click="removeref(thing.alert_index,thing.type)" id='removeref{{thing.alert_index}}' type='button' value='{{thing.alert_index}}' class='btn btn-danger btn-xs'>Remove</button>

                                                                </div>
                                                                </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            <div v-else><em>No Base Thresholds Set.</em><br><br></div>
                                                        </div>

                                                			</div>
                                                			</div></div></div>


                                                		</div>
                                                	</div>
                                                </div>
                                            </div>
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
  	    $(function(){
  	    	$('#stopworking').hide();
  	        $('#stopnow').on('click', function(e){
  	        	$('#stopminioninfo').hide();
         		    $('#stopworking').show();
         		    $('#stopnow').addClass('disabled');
  	            e.preventDefault(); // preventing default click action
  	            $.ajax({
  	                url: 'jsonencode/interaction.php?prev=0&job=stopminion',
  	                success: function(data){
  						//alert ('success');
  	                    $('#stopminionmodal').modal('hide')
  						//alert(data);
  						$("#messages").html(data);
  						$('#stopminion').addClass('disabled');
  						$('#startminion').removeClass('disabled');
                          $('#stopminioninfo').show();
                 		    $('#stopworking').hide();
                          $('#startnow').removeClass('disabled');
  	                }, error: function(){
  	                    alert('ajax failed');
  	                },
  	            })
  				//alert ("button clicked");
  	        })
  	    })
  		$(function(){
  	    	$('#startworking').hide();
  	        $('#startnow').on('click', function(e){
  	        	$('#startminioninfo').hide();
         		    $('#startworking').show();
         		    $('#startnow').addClass('disabled');
                  var script = $("input[type='radio'][name='scriptRadios']:checked").val();
  	            e.preventDefault(); // preventing default click action
  	            $.ajax({
  	                url: 'jsonencode/interaction.php?prev=0&job=startminion&script='+script,
  	                success: function(data){
  						//alert ('success');
  	                    $('#startminionmodal').modal('hide')
  						//alert(data);
  						$("#messages").html(data);
  						$('#startminion').addClass('disabled');
  						$('#stopminion').removeClass('disabled');
                          $('#stopnow').removeClass('disabled');
                          $('#startminioninfo').show();
                 		    $('#startworking').hide();
  	                }, error: function(){
  	                    alert('ajax failed');
  	                },
  	            })
  				//alert ("button clicked");
  	        })
  	    })

  	</script>
      <script>
      $(function(){
          $('#renameworking').hide();
          $('#renamenow').on('click', function(e){
              $('#reaneminfo').hide();
              $('#renameworking').show();
              var script = $("#newname").val();
              e.preventDefault(); // preventing default click action
              $.ajax({
                  url: 'jsonencode/interaction.php?prev=0&job=renamerun&name='+script,
                  success: function(data){
                      //alert ('success');
                      $('#renamemodal').modal('hide')
                      //alert(data);
                      $("#messages").html(data);
                      $('#reaneminfo').show();
                      $('#renameworking').hide();

                  }, error: function(){
                      alert('ajax failed');
                  },
              })
              //alert ("button clicked");
          })
      })

  	</script>
  	<script>
  	$(function(){
  		$('#testmessage').on('click', function(e) {
  			//alert('spam');
  			e.preventDefault();
  			$.ajax({
  				url: 'jsonencode/interaction.php?prev=0&job=testminion',
  					success: function(data){
  					$("#messages").html(data);
  				}, error: function() {
  					alert('ajax failed');
  				},
  			})
  		})
  	})
  	$(function(){
  		$('#biasvoltageget').on('click', function(e) {
  			//alert('spam');
  			e.preventDefault();
  			$.ajax({
  				url: 'jsonencode/interaction.php?prev=0&job=biasvoltageget',
  					success: function(data){
  					$("#messages").html(data);
  				}, error: function() {
  					alert('ajax failed');
  				},
  			})
  		})
  	})
  	$(function(){
  		$('#biasvoltageinc').on('click', function(e) {
  			//alert('spam');
  			e.preventDefault();
  			$.ajax({
  				url: 'jsonencode/interaction.php?prev=0&job=biasvoltageinc',
  					success: function(data){
  					$("#messages").html(data);
  				}, error: function() {
  					alert('ajax failed');
  				},
  			})
  		})
  	})
  	$(function(){
  		$('#biasvoltagedec').on('click', function(e) {
  			//alert('spam');
  			e.preventDefault();
  			$.ajax({
  				url: 'jsonencode/interaction.php?prev=0&job=biasvoltagedec',
  					success: function(data){
  					$("#messages").html(data);
  				}, error: function() {
  					alert('ajax failed');
  				},
  			})
  		})
  	})
  	</script>
  	<script>
  	$("#minknowinfo").load("minknowinfo.php").fadeIn("slow");
  	    var auto_refresh = setInterval(function ()
              {
              $( "#minknowinfo").load("minknowinfo.php").fadeIn("slow");
              //eval(document.getElementById("infodiv").innerHTML);
              }, 1000); // refresh every 1000 milliseconds

  	</script>
      <script>
  	$("#minknowinfodetail").load("minknowinfodetail.php").fadeIn("slow");
  	    var auto_refresh = setInterval(function ()
              {
              $( "#minknowinfodetail").load("minknowinfodetail.php").fadeIn("slow");
              //eval(document.getElementById("infodiv").innerHTML);
              }, 1000); // refresh every 1000 milliseconds

  	</script>
       <script>
          $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
          var auto_refresh = setInterval(function ()
              {
              $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
              //eval(document.getElementById("infodiv").innerHTML);
              }, 10000); // refresh every 5000 milliseconds
      </script>

      <script>
      	$(function(){
              $('#barcode_coverage_button').on('click', function(e){
                  e.preventDefault(); // preventing default click action
                  var checkval = $("#barcode_coverage_stop:checked").val();
                  //alert (checkval);
              	<?php $barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12"];?>
  				<?php foreach ($barcodes as $barcode) {?>
  				var idVal = $("#barcodecov<?php echo $barcode; ?>").val();
                  //alert('were getting there ' + idClicked + ' is ' + idVal);
          		//alert ($("input:radio[name='coveragenoticeradio']:checked").val());
          		//var type = $("input:radio[name='coveragenoticeradio']:checked").val();
                  if (checkval == 'on'){
  					var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&rununtil=1&reference=<?php echo $barcode; ?>&task=barcodecoverage&threshold='+idVal;
  					//alert ('yes');
                  }else{
  	            	var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&rununtil=0&reference=<?php echo $barcode; ?>&task=barcodecoverage&threshold='+idVal;
                  }
                  //alert (monkey);
                  $.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
  				<?php };?>

              })
      	})


      </script>
      <script>
        $(function(){
            $('#disk_space_alert').on('click', function(e){
                e.preventDefault();
                var space = $("input:radio[name='diskspace']:checked").val();
                var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=Template&rununtil=0&reference=&start=&end=&task=disknotify&threshold='+space;
                $.ajax({
                      url: monkey,
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
            })
        })
      </script>
      <script>
      	$(function(){
      		$('#reference_coverage_button').on('click', function(e) {
      			e.preventDefault();
      			//alert ("help");
      			var checkval = $("#reference_coverage_stop:checked").val();
      			//alert (checkval);
      			var idVal = $("#refcoveragedepth").val();
      			var idVal2 = $("#refcoveragedepthstart").val();
      			var idVal3 = $("#refcoveragedepthend").val();
      			var idVal4 = $("#refselect").val();
      			if (checkval == '1'){
      				var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&rununtil=1&reference='+idVal4+'&start='+idVal2+'&end='+idVal3+'&task=referencecoverage&threshold='+idVal;
      				//alert (monkey);
      			}else{
      				var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&rununtil=0&reference='+idVal4+'&start='+idVal2+'&end='+idVal3+'&task=referencecoverage&threshold='+idVal;
      			}
      			$.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
      		})
      	})
      </script>
      <script>
        $(function(){
            //alert("hello b");
            $('#remdisknotice').on('click', function(e) {
                e.preventDefault();
                //alert("click clik");
                var monkey = 'jsonencode/set_alerts.php?task=removedisknotice&type=Template';
                $.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide');
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                 })
            })
        })
      </script>
  <script>
      	$(function(){
      		$('#removethresholds').on('click', function(e) {
      			//alert ("spam");
      			e.preventDefault();
      			var monkey = 'jsonencode/set_alerts.php?task=barcodecoveragedelete&type=2D';
      			$.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide');
                          //alert(data);
                          $("#messages").html(data);
                          //location.reload();
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
      		})
      	})
      </script>
  <script>
      	$(function(){
      		$('#removeglobthreshold').on('click', function(e) {
      			//alert ("spam");
      			e.preventDefault();
      			var monkey = 'jsonencode/set_alerts.php?task=genbarcodecoveragedelete&type=2D';
      			$.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide');
                          //alert(data);
                          $("#messages").html(data);
                          //location.reload();
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
      		})
      	})
      </script>
      <script>

              $(function(){
              $('#gen_coverage').on('click', function(e){
                  e.preventDefault(); // preventing default click action
                  var idClicked = e.target.id;
                  var idVal = $("#foldchange").val();
                  //alert('were getting there ' + idClicked + ' is ' + idVal);
          		//alert ($("input:radio[name='coveragenoticeradio']:checked").val());
          		var type = $("input:radio[name='coveragenoticeradio']:checked").val();
                   var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type='+type+'&task=gencoverage&threshold='+idVal;
                  //alert (monkey);
                  $.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
                  //alert ("button clicked");
              })
          })
      </script>
      <script>

              $(function(){
              $('#base_notification').on('click', function(e){
                  e.preventDefault(); // preventing default click action
                  var idClicked = e.target.id;
                  var idVal = $("#basenotification").val()
                  //alert('were getting there ' + idClicked + ' is ' + idVal);
          		var type = $("input:radio[name='basenoticeradio']:checked").val();
                   var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type='+type+'&task=basenotification&threshold='+idVal;
                  //alert (monkey);
                  $.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
                  //alert ("button clicked");
              })
          })
      </script>
  <script>
  	$(function(){
      		$('[id^=removeref]').on('click', function(e) {
      			//alert ("spam");
      			e.preventDefault();
      			//alert ("BUTTON"+this.id);
      			var monkey = 'jsonencode/set_alerts.php?task=referencecoveragedelete&type=2D&reference='+this.id;
      			$.ajax({
                      url: monkey,
                    // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                          //location.reload();
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
      		})
      	})
     </script>
      <!--<?php $barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12"];?>
  				<?php foreach ($barcodes as $barcode) {?>
  				<script>

              $(function(){
              $('#barcode_coverage_<?php echo $barcode; ?>_button').on('click', function(e){
                  e.preventDefault(); // preventing default click action
                  var idClicked = e.target.id;
                  var idVal = $("#barcodecov<?php echo $barcode; ?>").val();
                  //alert('were getting there ' + idClicked + ' is ' + idVal);
          		//alert ($("input:radio[name='coveragenoticeradio']:checked").val());
          		//var type = $("input:radio[name='coveragenoticeradio']:checked").val();
                   var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&reference=<?php echo $barcode; ?>&task=barcodecoverage&threshold='+idVal;
                  //alert (monkey);
                  $.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
                  //alert ("button clicked");
              })
          })
      </script>


  				<?php };?>-->

      <script>

              $(function(){
              $('#genbarcode_coverage').on('click', function(e){
                  e.preventDefault(); // preventing default click action
                  var idClicked = e.target.id;
                  var idVal = $("#genbarcodecov").val();
                  var checkval = $("#genbarcode_coverage_stop:checked").val();
                  //alert('were getting there ' + idClicked + ' is ' + idVal);
          		//alert ($("#genbarcode_coverage_stop:checked").val());
          		//var type = $("input:radio[name='coveragenoticeradio']:checked").val();
                  if (checkval == 1){
                  	var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&task=genbarcodecoverage&rununtil=1&threshold='+idVal;
                  }else{
  	                var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&task=genbarcodecoverage&rununtil=0&threshold='+idVal;
                  }
                  //alert (monkey);
                  $.ajax({
                      url: monkey,
                     // alert ('url'),
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
                  //alert ("button clicked");
              })
          })
      </script>
  	<script>
  	$(function(){
  		$('#pincheck').modal('show')
  	})
  	$(function(){
  		$('#pinback').click(function(){
          parent.history.back();
          return false;
      });
  		$('#pincheckbutton').on('click', function(e) {
  			e.preventDefault();
  			var pincheck = $('#pincheckfield').val();
  			var monkey = 'jsonencode/checkpin.php?prev=0&pass='+pincheck;
  			$.ajax({
  				url: monkey,
  				success: function (data){
  					if (data == 'pass') {
  						$('#pincheck').modal('hide')
  					}else{
  						alert ('Pin Failed - Try Again');
  					}
  				}, error: function() {
  					alert('ajax failed');
  				},
  			}
  			)
  		})
  	})
  	</script>


      <script>
      		$(document).ready(function() {
      		    var options = {
      		        chart: {
      		            renderTo: 'cumulativeyield',
      					zoomType: 'x',
      		            type: 'spline',
      		        },
      		        title: {
      		          text: 'Cumulative Reads'
      		        },
      		        resetZoomButton: {
                      position: {
                          // align: 'right', // by default
                          // verticalAlign: 'top', // by default
                          x: -10,
                          y: 10
                      },
                      relativeTo: 'chart'
                  },
      		        plotOptions: {
      		        	spline: {
      					                animation: false,
      									marker: {
      							            enabled: false
      							        }

      				},



              },
      				xAxis: {
      					type: 'datetime',
      			            dateTimeLabelFormats: { // don't display the dummy year
                     				month: '%e. %b',
                 				    year: '%b'
      				            },
      				            title: {
      				                text: 'Time/Date'
      				            }
      				        },
      						yAxis: [{
      				                labels: {
                  				        align: 'right',
                  	    			    x: -3
                  	   				},

                  	    			title: {
                  	        			text: 'Cumulative Reads'
      				                },
      				                height: '100%',
      				                lineWidth: 1,
      				                min: 0
      				            }],
      								credits: {
      								    enabled: false
      								  },
      		        legend: {
      		        	title: {
                      text: 'Read Type <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
                      style: {
                          fontStyle: 'italic'
                      }
                  },

      		            layout: 'horizontal',
      		            align: 'center',
      		            //verticalAlign: 'middle',
      		            borderWidth: 0
      		        },
      		        series: []
      		    };
      			function loadchirpcy() {

      					if(1 == 1) {
         										   $.getJSON('jsonencode/cumulativeyield.php?prev=0&callback=?', function(data) {

                                              options.series = data; // <- just assign the data to the series property.

                                                      setTimeout(loadchirpcy,<?php echo $_SESSION['pagerefresh'] ;?>);

                                              //options.series = JSON2;
                                                      var chart = new Highcharts.Chart(options);
                                                      });} else {
         setTimeout(loadchirpcy,<?php echo $_SESSION['pagerefresh'] ;?>);
      }

                                              }


      				        loadchirpcy();

      			});

      				//]]>

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
