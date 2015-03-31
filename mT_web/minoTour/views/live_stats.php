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
            
            <!-- /.navbar-header -->
			<?php include 'navbar-header.php' ?>
            <!-- /.navbar-top-links -->
			<?php include 'navbar-top-links.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
			<?php include 'includes/run_check.php';?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Live Stats Report: <?php echo cleanname($_SESSION['active_run_name']); ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
				<h4>Basic Run Statistics</h4>
								
				<?php
				date_default_timezone_set('UTC');
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
				$mindb2_connection = new mysqli(DB_HOST,DB_USER,DB_PASS);
				if (!$mindb_connection->connect_errno) {
					//Query to generate basic run report:
				
					$basicrunsql = "SELECT asic_id,AVG(asic_temp) as asic_temp_avg,std(asic_temp) as asic_temp_std,exp_script_purpose,exp_start_time,flow_cell_id,AVG(heatsink_temp) as heatsink_temp_avg,std(heatsink_temp) as heatsink_temp_std,run_id,version_name FROM tracking_id group by device_id,flow_cell_id,asic_id;";
					$basicrunresults = $mindb_connection->query($basicrunsql);	
					echo "<div class='panel panel-default'>";
					echo "<div class='panel-heading'>";
					echo "<h5>MinKNOW run reporting:</h5>";
					echo "</div>";
					if ($basicrunresults->num_rows == 1){
						echo "<div class='panel-body'>";
						$basicrunresults_row = $basicrunresults->fetch_object();
						echo "<table class=\"table table-condensed\">";
						echo "<thead>";
						echo "<tr>";
						echo "<th>Parameter</th>";
						echo "<th>Value</th>";
						echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						echo "<tr>";
						echo "<td>Experiment Script Purpose</td>";
						echo "<td>" . $basicrunresults_row->exp_script_purpose . "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>Experiment Start Date/Time</td>";
						echo "<td>" . gmdate("H:i:s Y-m-d", $basicrunresults_row->exp_start_time) . "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>ASIC ID</td>";
						echo "<td>" . $basicrunresults_row->asic_id . "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>Average ASIC Temp (stand var)</td>";
						echo "<td>" . $basicrunresults_row->asic_temp_avg . " (" . $basicrunresults_row->asic_temp_std . ")</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>Average Heatsink Temp (stand var)</td>";
						echo "<td>" . $basicrunresults_row->heatsink_temp_avg . " (" . $basicrunresults_row->heatsink_temp_std . ")</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>Run ID</td>";
						echo "<td>" . $basicrunresults_row->run_id .  "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>MinKNOW Version</td>";
						echo "<td>" . $basicrunresults_row->version_name .  "</td>";
						echo "</tr>";
						echo "</tbody>";
						echo "</table>";
						echo "</div>";
						echo "</div>";
					}
					
					
					//Query to generate metrichor run information:
				
					$metrichorinfosql = "SELECT *, max(metrichor_time_stamp) as max, min(metrichor_time_stamp) as min FROM config_general group by workflow_name;";
					$metrichorinforesults = $mindb_connection->query($metrichorinfosql);
					echo "<div class='panel panel-default'>";
					echo "<div class='panel-heading'>";
					echo "<h5>Metrichor reporting:</h5>";
					echo "</div>";
					if ($metrichorinforesults->num_rows >= 1){
						echo "<div class='panel-body'>";
						echo "<table class=\"table table-condensed\">";
						echo "<thead>";
						echo "<tr>";
						echo "<th>Workflow_Name</th>";
						echo "<th>Parameter</th>";
						echo "<th>Value</th>";
						echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						foreach ($metrichorinforesults as $row) {
							echo "<tr>";
							echo "<td>".$row['workflow_name']."</td>";
							echo "<td>Workflow Script</td>";
							echo "<td>" . $row['workflow_script']. "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>"."</td>";
							echo "<td>Config</td>";
							echo "<td>" . $row['config']. "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>"."</td>";
							echo "<td>Metrichor Version</td>";
							echo "<td>" . $row['metrichor_version']. "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>"."</td>";
							echo "<td>First Called Read</td>";
							echo "<td>" . $row['min']. "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>"."</td>";
							echo "<td>Last Called Read</td>";
							echo "<td>" . $row['max']. "</td>";
							echo "</tr>";							
						}
						echo "</tbody>";
						echo "</table>";
						echo "</div>";
						echo "</div>";
					}
					//Query to generate reference information:
					$referencesql = "select * from reference_seq_info;";
					$referenceresults = $mindb_connection->query($referencesql);
					echo "<div class='panel panel-default'>";
					echo "<div class='panel-heading'>";
					echo "<h5>Reference reporting:</h5>";
					echo "</div>";
					if ($referenceresults->num_rows >= 1){
						echo "<div class='panel-body'>";
						echo "<table class=\"table table-condensed\">";
						echo "<thead>";
						echo "<tr>";
						echo "<th>Reference File</th>";
						echo "<th>Sequence Name</th>";
						echo "<th>Sequence Length</th>";
						echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						foreach ($referenceresults as $row) {
							echo "<tr>";
							echo "<td>".$row['reffile']."</td>";
							echo "<td>".$row['refname']."</td>";
							echo "<td>" . $row['reflen']. "</td>";
							echo "</tr>";
						}
						echo "</tbody>";
						echo "</table>";
						echo "</div>";
						echo "</div>";
					}else {
						echo "<div class='panel-body'>";
						echo "<p>This run has not been aligned to a reference.</p>";	
						echo "</div>";
						echo "</div>";
					}
					//Query to get comment information:
						$grusql = "select * from Gru.minIONruns where runname = \"" . $_SESSION['active_run_name'] . "\";";
						$grusqlresults = $mindb2_connection->query($grusql);
						echo "<div class='panel panel-default'>";
						echo "<div class='panel-heading'>";
						echo "<h5>minoTour reporting:</h5>";
						echo "</div>";
						if ($grusqlresults->num_rows >= 1) {
							echo "<div class='panel-body'>";
							echo "<table class=\"table table-condensed\">";
							echo "<thead>";
							echo "<tr>";
							echo "<th>Date</th>";
							echo "<th>Flow Cell ID</th>";
							echo "<th>Flow Cell Owner</th>";
							echo "<th>Base Caller Algorithm</th>";
							echo "<th>Base Caller Version</th>";							
							echo "</tr>";
							echo "</thead>";
							echo "<tbody>";
							foreach ($grusqlresults as $row) {
								echo "<tr>";
								echo "<td>".$row['date']."</td>";
								echo "<td>".$row['flowcellid']."</td>";
								echo "<td>" . $row['FlowCellOwner']. "</td>";
								echo "<td>" . $row['basecalleralg']. "</td>";
								echo "<td>" . $row['version']. "</td>";
								echo "</tr>";
							}
							echo "</tbody>";
							echo "</table>";
							echo "</div>";
							echo "</div>";
							echo "<div class='panel panel-default'>";
							echo "<div class='panel-heading'>";
							echo "<h5>Run Upload Comment:</h5>";
							echo "</div>";
							echo "<div class='panel-body'>";
							echo "<p>" . $row['comment'] . "</p";
							echo "</div>";
							echo "</div>";
							echo "</div>";
						}
					}
				?>
					<div class="text-left">
					    <div class="well well-sm">
					        <h4>Run reporting and comments.</h4>
    						<div class="input-group col-lg-12">
    						<label for="message">Add new comment:</label>
    						<textarea class="form-control" rows="3" name="message" id="message" placeholder="Write your comment here..."></textarea>
					        </div>
					        <div class="input-group col-lg-4">
					        <label for="message">Please provide your name:</label>
					        <input type="text" name = "name" id = "name" class="form-control" placeholder="Please enter a name here..." />
					        </div>
					        <p><input type="hidden" id="user" name="user" value="<?php echo $_SESSION['user_name'];?>"/></p>
   					<p><input type="hidden" id="run" name="run" value="<?php echo $_SESSION['active_run_name'];?>"/></p>
						    <span class="input-group-btn" onclick="addComment()">     
						            <a id="formthing" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-comment"></span> Add Comment</a>
					        </span>
    					
    					
    <hr data-brackets-id="12673">
    <!--Error and success message wrapper-->
						<div id="errAll"></div>
	<!--New comments appear here.-->
	<ul data-brackets-id="12674" id="sortable" class="list-unstyled ui-sortable">
	<div id="commentpost"></div>
		<?php
				
					$grucomsql = "select * from Gru.comments where runname = \"" . $_SESSION['active_run_name'] . "\" order by date desc;";
					$grucomsqlresults = $mindb2_connection->query($grucomsql);
					if ($grucomsqlresults->num_rows >= 1) {
						foreach ($grucomsqlresults as $row) {
							echo "<strong class=\"pull-left primary-font\">" . $row['name'] . "</strong>";
							echo "<small class=\"pull-right text-muted\">";
							echo "<span class=\"glyphicon glyphicon-time\"></span>" . $row['date'] . "</small>";
							echo "</br>";
							echo "<li class=\"ui-state-default\">";
							echo $row['comment'];
							echo "</br>";
							echo "<hr>";
						}
					}
					?>
		
							
                
    </ul>
    </div>
</div>
			 	<div id="messages"></div>
				
                 </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <!-- /#page-wrapper -->

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
    <script src="js/plugins/morris/raphael-2.1.0.min.js"></script>
    <script src="js/plugins/morris/morris.js"></script>
	
	<!-- Highcharts Addition -->
	<script src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	
	<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#formthing").click(function(e){
		    cname = $( "#name" ).val();
		    cmessage = $("#message").val();
		    cuser = $("#user").val();
		    crun = $("#run").val();
		    var currentdate = new Date(); 
			var datetime = currentdate.getFullYear() + "/" + (currentdate.getMonth()+1)  + "/" + currentdate.getDate() + "/" +  " "  
            + currentdate.getHours() + ":"  
            + currentdate.getMinutes() + ":" 
            + currentdate.getSeconds();
		    ctime = datetime;
		    
			if( cname=="" || cmessage=="" ) {
				$("#errAll").html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>You have left a required field blank.</div>');
			}else {
				var data = { 
					name: cname, 
					user: cuser, 
					time: ctime, 
					message: cmessage,
					run: crun,
				};
				
				$.post( "includes/ajax.php", data, function( response ) {
  					//alert(response);
  					$('#name').val("");
  					$('#message').val("");
  					var test = response.toString();
  					$("#commentpost").prepend(test);
				});
			}
		});
		
 
			
	});
	</script>
	
    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>

    <!-- Page-Level Demo Scripts - Dashboard - Use for reference -->
    <script src="js/demo/dashboard-demo.js"></script>

     <script>
        $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
        var auto_refresh = setInterval(function ()
            {
            $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
            //eval(document.getElementById("infodiv").innerHTML);
            }, 10000); // refresh every 5000 milliseconds
    </script>

<?php include "includes/reporting.php";?>
</body>

</html>
