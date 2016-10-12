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
                    <h1 class="page-header">XML Reports: <?php echo cleanname($_SESSION['active_run_name']); ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
				<h4>ENA metaData</h4>
				<br>
				
				
				<?php
				date_default_timezone_set('UTC');
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
				if (!$mindb_connection->connect_errno) {
					$masterxml = "SELECT * FROM XML;";
					$masterxmlquery = $mindb_connection->query($masterxml);
					
					foreach ($masterxmlquery as $row) { 
						//echo $row['type'];
						$xmlresult = (string)$row['xml'];
						$xml = new SimpleXMLElement($xmlresult);
						//var_dump($xml);
						//echo $xmlresult;
						//echo $xmlresult;
						switch ($row['type']) {
						    case "study":
						    	echo "<div class='panel panel-default'>";
								echo "<div class='panel-heading'>";
								echo "<h5>" . ucwords($row['type']) . ": " . substr($row['filename'],0,9) .  "</h5>";
								echo "</div>";
								echo "<div class='panel-body'>";
								foreach ($xml->STUDY as $record) {
									echo "Submitting Center: ";
									echo $record['center_name'] . "<br>";
									#echo $record->IDENTIFIERS->PRIMARY_ID;
									echo "Study Title: <i>";
									echo $record->DESCRIPTOR->STUDY_TITLE . "</i><br>";	
									echo "Study Type: ";
									echo $record->DESCRIPTOR->STUDY_TYPE['existing_study_type'] . "<br>";
									echo "Study Abstract: ";
									echo $record->DESCRIPTOR->STUDY_ABSTRACT . "<br>";
								}
						        echo "</div>";
						        echo "</div>";
						        break;
						    case "experiment":
						        echo "<div class='panel panel-default'>";
								echo "<div class='panel-heading'>";
								echo "<h5>" . ucwords($row['type']) . ": " . substr($row['filename'],0,9) .  "</h5>";
								echo "</div>";
								echo "<div class='panel-body'>";
								foreach ($xml->EXPERIMENT as $record) {
									echo "Library Name: ";
									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_NAME . "<br>";
									#echo $record->IDENTIFIERS->PRIMARY_ID;
									echo "Library Strategy: ";
									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_STRATEGY . "<br>";
									echo "Library Source: ";
									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_SOURCE . "<br>";
									echo "Library Selection: ";
									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_SELECTION . "<br>";
									echo "Library Construction Protocol: ";
									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_CONSTRUCTION_PROTOCOL . "<br>";
									echo "<br>";
									echo "Experiment Attributes:<br>";
									foreach ($record->EXPERIMENT_ATTRIBUTES->EXPERIMENT_ATTRIBUTE as $attribute) {
										echo $attribute->TAG;
										echo ": ";
										echo $attribute->VALUE . "<br>";
									}
								}
						        echo "</div>";
						        echo "</div>";
						        break;
						    case "run":
						        echo "<div class='panel panel-default'>";
								echo "<div class='panel-heading'>";
								echo "<h5>" . ucwords($row['type']) . ": " . substr($row['filename'],0,9) .  "</h5>";
								echo "</div>";
								echo "<div class='panel-body'>";
								foreach ($xml->RUN as $record) {
									//var_dump($record);
									echo "Run Attributes:<br>";
									foreach ($record->RUN_ATTRIBUTES->RUN_ATTRIBUTE as $attribute) {
										echo $attribute->TAG;
										echo ": ";
										echo $attribute->VALUE . "<br>";
									}
								}
						        echo "</div>";
						        echo "</div>";
						        break;
						    case "sample":
						        echo "<div class='panel panel-default'>";
								echo "<div class='panel-heading'>";
								echo "<h5>" . ucwords($row['type']) . ": " . substr($row['filename'],0,9) .  "</h5>";
								echo "</div>";
								echo "<div class='panel-body'>";
								foreach ($xml->SAMPLE as $record) {
									echo "Taxon ID: ";
									echo $record->SAMPLE_NAME->TAXON_ID . "<br>";
									echo "Scientific Name: <i>";
									echo $record->SAMPLE_NAME->SCIENTIFIC_NAME . "</i><br>";
									
								}
						        echo "</div>";
						        echo "</div>";
						        break;

						    default:
						        echo "I'm sorry, but minoTour doesn't recognise this XML file type!";
						}	
					}
					
					
					
					

					
				
					}
				?>
					
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
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/modules/heatmap.js"></script>
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
