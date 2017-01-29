<?php
// load the functions
require_once("includes/functions.php");
require_once("includes/jsonfunctions.php");

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
			
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">K-mer Summary: <?php echo cleanname($_SESSION['focusrun']); ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
				<h4>Basic K-mer Information (template : complement)</h4>
				<br>
				
				
				<?php 
				//SELECT model_state,mp_state,move FROM caller_basecalled_template where move > 0 and rand()<=0.005
				date_default_timezone_set('UTC');
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
				if (!$mindb_connection->connect_errno) {
								//Get the frequency of kmers in the reference
			$ref_kmers="SELECT kmer,freq FROM ref_sequence_kmer order by kmer;";
			$ref_kmers_result=$mindb_connection->query($ref_kmers);
			$refkmers=array();
			foreach ($ref_kmers_result as $key->$value){
				$refkmers[$key]=$value;	
			}
			//Get the frequency of kmers in a random selectio of 500 template reads.
			$kmersample = "SELECT sequence FROM basecalled_template where rand()<=0.01 limit 500;";
			$kmersampleresults = $mindb_connection->query($kmersample);
			$templatekmercount=array();
			$kmertemplatecount=0;
			if ($kmersampleresults->num_rows>=1) {
				foreach ($kmersampleresults as $row){
					//echo $row[sequence] . "<br>";
					//Loop through each sequence and get the kmers
					for ($i = 0; $i <= (strlen($row[sequence])-5) ; $i++) {
						$substr =  substr($row[sequence], $i, 5); 
						$kmertemplatecount++;
						if (isset($templatekmercount[$substr])) {
							 $templatekmercount[$substr]++;
						} else {
							$templatekmercount[$substr] = 1; 
						}
					}
				}	
			}
			foreach ($templatelmetcount as $key->$vaule) {
				echo "$key	$value<br>";
			}

					
					
					
				
				}
				
				
				?>
				
				
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
