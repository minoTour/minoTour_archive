<?php
// load the functions
require_once("includes/functions.php");

?>
<!DOCTYPE html>
<html>

<?php include "includes/head.php";?>
<body>

    <div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">

			<?php include 'navbar-header.php' ?>
            <!-- /.navbar-top-links -->
			<?php include 'navbar-top-links.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
						<?php include 'includes/run_check.php';?>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Live Control - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></h1>
                </div>


<!-- Modal -->
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
            </div>
            <p>Remote control of your minION device is available via these pages. To ensure security a matching pin number must be entered to access this page and on the minUP script controlling the minION device. If minUP is not running on the same machine as the minION device control of your sequencer is not possible via minoTour.</p>
            <div class="row">
                <div id="cumulativeyield" style="width:100%; height:300px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Cumulative Reads</div>
                <div class="col-md-6">
            		<div id="minknowinfo"></div>
            	</div>
            	<div class="col-md-6">
            		<div class="panel panel-warning">
  						<div class="panel-heading">
					    <h3 class="panel-title">minKNOW Control options</h3>
						  </div>
					  <div class="panel-body">
						    <h5>To test if you have a connection to minKNOW:</h5>
		<button id='testmessage' type='button' class='btn btn-info btn-sm'><i class='fa fa-magic'></i> Test Communication</button>
		<br>
		<h5>Trigger a mux switch:</h5>
		<button id='muxswitch' type='button' disabled="disabled" class='btn btn-info btn-sm'><i class='fa fa-magic'></i> Get Mux Switch</button>
		<br>
        <h5>To increase/decrease the current bias voltage offset in minKNOW by 10 mV:</h5>
		<button id='biasvoltageinc' type='button' class='btn btn-info btn-sm'><i class='fa fa-arrow-circle-up'></i> Inc Bias Voltage</button>
		<button id='biasvoltagedec' type='button' class='btn btn-info btn-sm'><i class='fa fa-arrow-circle-down'></i> Dec Bias Voltage</button>
		<br>
        <h5>Rename Your Run:</h5>
		<!--<button id='renamerun' type='button' class='btn btn-info btn-sm'><i class='fa fa-magic'></i> Rename Run</button>-->
        <!-- Indicates a dangerous or potentially negative action -->
        <!-- Button trigger modal -->
        <button id='renamerun' class='btn btn-info btn-sm' data-toggle='modal' data-target='#renamemodal'>
          <i class='fa fa-magic'></i> Rename Run
        </button>

        <!-- Modal -->
        <div class='modal fade' id='renamemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title' id='myModalLabel'>Rename Your Run</h4>
                    </div>
                    <div class='modal-body'>
                        <div id='renameinfo'>
                            <p>You can rename a run if you wish to do so. Note there is no need to do this unless you wish to change the smaple ID for some reason.</p>
                            <input type="text" id="newname" class="form-control" placeholder="New Run Name">
                            <p>If you are sure you wish to do this enter your new name above and click 'Rename Run' below. Otherwise close this window.</p>
                            <p> We dont recommend doing this when a run is in progress!</p>
                        </div>
                        <div id='renameworking'>
                            <p class='text-center'>We're working to rename your run. Please be patient and don't navigate away from this page.</p>
                            <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                            <button id='renamenow' type='button' class='btn btn-danger'>Rename Run</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>

        <br>
        <h5>Remote Start/Stop</h5>

				<!-- Indicates a dangerous or potentially negative action -->
				<!-- Button trigger modal -->
				<button id='stopminion' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#stopminionmodal'>
				  <i class='fa fa-stop'></i> Stop minION
				</button>

				<!-- Modal -->
				<div class='modal fade' id='stopminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
					<div class='modal-dialog'>
						<div class='modal-content'>
							<div class='modal-header'>
								<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
								<h4 class='modal-title' id='myModalLabel'>Stop your minION</h4>
							</div>
							<div class='modal-body'>
								<div id='stopminioninfo'>
									<p>This will attempt to stop your minION sequencer remotely. It should be possible to restart sequencing remotely but software crashes on the minION controlling device may cause problems. You should only stop your minION device remotely if you are certain you wish to do so and <strong> at your own risk</strong>.</p>

									<p>If you are sure you wish to do this, click 'Stop minION' below. Otherwise close this window.</p>
								</div>
								<div id='stopworking'>
									<p class='text-center'>We're working to stop your minION device. Please be patient and don't navigate away from this page.</p>
								    <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
								</div>
								<div class='modal-footer'>
									<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
								    <button id='stopnow' type='button' class='btn btn-danger'>Stop minION</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
        		</div>


				<!-- Indicates a dangerous or potentially negative action -->
				<!-- Button trigger modal -->
				<button id='startminion' class='btn btn-success btn-sm' data-toggle='modal' data-target='#startminionmodal'>
				  <i class='fa fa-play'></i> Start minION
				</button>

				<!-- Modal -->
				<div class='modal fade' id='startminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
					<div class='modal-dialog'>
						<div class='modal-content'>
							<div class='modal-header'>
								<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
								<h4 class='modal-title' id='myModalLabel'>Start your minION</h4>
							</div>
							<div class='modal-body'>
								<div id='startminioninfo'>
									<p>This will attempt to restart your minION sequencer remotely.</p>

									<p>If you are sure you wish to do this select an available run script and click 'Start minION' below. Otherwise close this window.</p>
                                    <?php
                                    $availablescripts = "SELECT * FROM messages where message = 'runscript' order by message_index;";

                                	$availablescriptsare = $mindb_connection->query($availablescripts);
                                	if ($availablescriptsare->num_rows > 0) {
                                		foreach ($availablescriptsare as $row) {
                                			echo "
                                            <div class='radio'>
                                                <label>
                                                    <input type='radio' name='scriptRadios' id='" . $row['param1'] . "' value='" . $row['param1'] . "' >
                                                    " . cleanname($row['param1']) . ".py
                                                </label>
                                            </div>
                                            ";

                                		}
                                	}else {
                                		echo "Not Available.";
                                	}
                                    ?>



								</div>
								<div id='startworking'>
									<p class='text-center'>We're working to restart your minION device. Please be patient and don't navigate away from this page.</p>
								    <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
								</div>
								<div class='modal-footer'>
									<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
								    <button id='startnow' type='button' class='btn btn-success'>Start minION</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
        		</div>
					  </div>
					</div>
            	</div>

            </div>
			 	<div id="messages"></div>
        <div class="row">
        	<div class="col-md-6">
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



        	<div class="col-md-6">
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

							<div class="row">
							<div class = "col-lg-12">
							<?php
							$queryarray;
							$querycheck = "SELECT * from alerts;";
							$querycheckresult = $mindb_connection->query($querycheck);
							foreach ($querycheckresult as $row) {
								//echo $row['name'] . "<br>";
								$queryarray[$row['name']][$row['alert_index']]['reference']=$row['reference'];
								$queryarray[$row['name']][$row['alert_index']]['threshold']=$row['threshold'];
								$queryarray[$row['name']][$row['alert_index']]['control']=$row['control'];
								$queryarray[$row['name']][$row['alert_index']]['complete']=$row['complete'];
								$queryarray[$row['name']][$row['alert_index']]['start']=$row['start'];
								$queryarray[$row['name']][$row['alert_index']]['end']=$row['end'];
								$queryarray[$row['name']][$row['alert_index']]['alert_index']=$row['alert_index'];

							}
							if ($_SESSION['currentbarcode'] >= 1) {
								if (isset($queryarray["barcodecoverage"])){
									echo "<Strong>Individual Barcode Thesholds Set.</strong><br><br>";
                                    echo "<div class='table-responsive'>";
									echo "<table class='table table-condensed'>";
									echo "<tr>";
									echo "<th>Reference</th>";
									echo "<th>Threshold</th>";
									echo "<th>Control</th>";
									echo "<th>Complete</th>";
									//echo "<th>Remove</th>";
									echo "</tr>";
									foreach ($queryarray["barcodecoverage"] as $entry) {
										echo "<tr>";
										echo "<td style='word-wrap: break-word'>";
										echo $entry['reference'];
										echo "</td>";
										echo "<td>";
										echo $entry['threshold'];
										echo "</td>";
										echo "<td>";
										echo $entry['control'];
										echo "</td>";
										echo "<td>";
										echo $entry['complete'];
										echo "</td>";
										//echo "<td>";
										//echo "Remove";
										//echo "</td>";

									}
									echo "</table>";
                                    echo "</div>";
									echo "<button id='removethresholds' type='button' class='btn btn-danger btn-xs'>Remove Thresholds</button><br><br>";

								}else{
									echo "<em>No Individual Barcode Thresholds Set.</em><br><br>";
								}
								if (isset($queryarray["genbarcodecoverage"])){
									echo "<Strong>Global Barcode Theshold Set.</strong><br><br>";
                                    echo "<div class='table-responsive'>";
									echo "<table class='table table-condensed'>";
									echo "<tr>";
									echo "<th>Reference</th>";
									echo "<th>Threshold</th>";
									echo "<th>Control</th>";
									echo "<th>Complete</th>";
									echo "</tr>";
									foreach ($queryarray["genbarcodecoverage"] as $entry) {
										echo "<tr>";
										echo "<td>";
										//echo $entry['reference'];
										echo "All Barcodes";
										echo "</td>";
										echo "<td>";
										echo $entry['threshold'];
										echo "</td>";
										echo "<td>";
										echo $entry['control'];
										echo "</td>";
										echo "<td>";
										echo $entry['complete'];
										echo "</td>";
									}
									echo "</table>";
                                    echo "</div>";
									echo "<button id='removeglobthreshold' type='button' class='btn btn-danger btn-xs'>Remove Global Threshold</button><br><br>";
								}else{
									echo "<em>No Global Barcode Theshold Set.</em><br><br>";
								}
							}
							if (isset($queryarray["referencecoverage"])){
								echo "<Strong>Reference Coverage Thesholds Set.</strong><br><br>";
                                echo "<div class='table-responsive'>";
								echo "<table class='table table-responsive'>";
								echo "<tr>";
								echo "<th>Ref</th>";
								echo "<th>Limit</th>";
								echo "<th>Start</th>";
								echo "<th>End</th>";
								echo "<th>Cont.</th>";
								echo "<th>Done</th>";
								echo "<th>Del</th>";
								echo "</tr>";
								foreach ($queryarray["referencecoverage"] as $entry) {
									echo "<tr>";
									echo "<td>";
									echo $entry['reference'];
									echo "</td>";
									echo "<td>";
									echo $entry['threshold'];
									echo "</td>";
									echo "<td>";
									echo $entry['start'];
									echo "</td>";
									echo "<td>";
									echo $entry['end'];
									echo "</td>";
									echo "<td>";
									echo $entry['control'];
									echo "</td>";
									echo "<td>";
									echo $entry['complete'];
									echo "</td>";
									echo "<td>";
									echo "<button id='removeref";
									echo $entry['alert_index'];
									echo "' type='button' value='" . $entry['alert_index'] . "' class='btn btn-danger btn-xs'>Remove</button><br><br>";
									echo "</td>";

								}
								echo "</table>";
                                echo "</div>";
							}else{
								echo "<em>No Reference Coverage Thesholds Set.</em><br><br>";
							}
							//var_dump ($queryarray);
							?>

        			</div>
        			</div></div></div>


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
		<div class="row">
			<div class="col-md-6">
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
								</div>
								<?php }; ?>
								<!--NEW BLOCK-->
							</div>




        </div>


        <!-- /#page-wrapper -->
		<!-- Checking for table existence -->

    </div>


    <!-- /#wrapper -->


    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>




    <!-- Page-Level Plugin Scripts - Dashboard -->
			    <script type="text/javascript" src="js/pnotify.custom.min.js"></script>
			    <script type="text/javascript">
				PNotify.prototype.options.styling = "fontawesome";
				</script>


	<!-- Highcharts Addition -->
	<script src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>



    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>


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
    		$('#removethresholds').on('click', function(e) {
    			//alert ("spam");
    			e.preventDefault();
    			var monkey = 'jsonencode/set_alerts.php?task=barcodecoveragedelete&type=2D';
    			$.ajax({
                    url: monkey,
                   // alert ('url'),
                    success: function(data){
                        //alert ('success');
                        $('#resetmodal').modal('hide')
                        //alert(data);
                        $("#messages").html(data);
                        location.reload();
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
                        $('#resetmodal').modal('hide')
                        //alert(data);
                        $("#messages").html(data);
                        location.reload();
                    }, error: function(){
                        alert('ajax failed');
                    },
                })
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
                        location.reload();
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



<?php include "includes/reporting.php";?>
</body>

</html>
