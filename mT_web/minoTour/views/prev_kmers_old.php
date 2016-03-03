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
			<?php include 'includes/run_check.php';?>
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
					//Query to generate basic run report:
					$kmersample = "SELECT model_state,mp_state,move FROM caller_basecalled_template_1 where move > 0 and rand()<=0.01;";
					$complementkmersample = "SELECT model_state,mp_state,move FROM caller_basecalled_complement_1 where move > 0 and rand()<=0.01;";
					$kmersampleresults = $mindb_connection->query($kmersample);
					$complementkmersampleresults = $mindb_connection->query($complementkmersample);
					
					$kmerarray = getkmers()[0];
					//$counter = 0;
					//foreach ($kmerarray as $k => $v) {
					//	$counter++;
 					//   echo "$counter Current value of $k: $v.<br>"; 
					//}
					$basearray = array();
					if ($kmersampleresults->num_rows>=1) {
						foreach ($kmersampleresults as $row){
							//echo $row['model_state'] . " " . $row['mp_state'] . "<br>";
							$kmerarray[$row['model_state']]['con'] += 1;
							$kmerarray[$row['mp_state']]['ml'] += 1;
							//add basecounts to basearray
							$bases = str_split($row['model_state']);	
							foreach ($bases as $base) {
								$basearray[$base]['con']+=1;
							}
							$bases = str_split($row['mp_state']);	
							foreach ($bases as $base) {
								$basearray[$base]['ml']+=1;	
							}
						}
					}
					if ($complementkmersampleresults->num_rows>=1) {
						foreach ($complementkmersampleresults as $row){
							//echo $row['model_state'] . " " . $row['mp_state'] . "<br>";
							//$compmodel_state = Complement($row['model_state']);
							//$compmp_state = Complement($row['mp_state']);
							$compmodel_state = $row['model_state'];
							$compmp_state = $row['mp_state'];
							
							$kmerarray[$compmodel_state]['ccon'] += 1;
							$kmerarray[$compmp_state]['cml'] += 1;
							//add basecounts to basearray
							$bases = str_split($compmodel_state);	
							foreach ($bases as $base) {
								$basearray[$base]['ccon']+=1;
							}
							$bases = str_split($compmp_state);	
							foreach ($bases as $base) {
								$basearray[$base]['cml']+=1;	
							}
						}
					}
					
					//Calculate the proportion of ATGC
					foreach ($basearray as $base => $type) {
						$basearray[$base]['conf'] = $type['con']/(5*$kmersampleresults->num_rows);
						$basearray[$base]['mlf'] = $type['ml']/(5*$kmersampleresults->num_rows);
						$basearray[$base]['cconf'] = $type['ccon']/(5*$complementkmersampleresults->num_rows);
						$basearray[$base]['cmlf'] = $type['cml']/(5*$complementkmersampleresults->num_rows);
					}
					
					
					
					echo "<table class='table table-condensed'>";
					echo "<thead>";
					echo "<tr>";
					echo "<th>#</th>";
					echo "<th>K-mer</th>";
					echo "<th>Consensus Observed</th>";
					echo "<th>Consensus Frequency</th>";
					echo "<th>Consensus Expected</th>";
					echo "<th>Con Obs/Exp</th>";
					echo "<th>Most Likely Observed</th>";
					echo "<th>Most Likely Frequency</th>";
					echo "<th>Most Likely Expected</th>";
					echo "<th>ML Obs/Exp</th>";
					echo "</tr>";
					echo "</thead>";
					echo  "<tbody>";
					
					$counter = 0;
					foreach ($kmerarray as $k => $v) {
						$counter++;
						echo "<tr>";
						echo "<th scope='row'>" . $counter . "</th>";
						echo "<td>" . $k . "</td>";//observed kmer
						echo "<td>" . $v['con'] . ":" . $v['ccon'] . "</td>";//observed kmer count consensus
						//Calculate the chance of seeing a given kmer based on the base frequencies observed in the data set
						$kmersplit=str_split($k);
						$kmerprob = $basearray[$kmersplit[0]]['conf']*$basearray[$kmersplit[1]]['conf']*$basearray[$kmersplit[2]]['conf']*$basearray[$kmersplit[3]]['conf']*$basearray[$kmersplit[4]]['conf'];
						$kmernum = $kmerprob * $kmersampleresults->num_rows;
						$kmerarray[$k]['conf']=($v['con']/$kmersampleresults->num_rows);
						
						$ckmerprob = $basearray[$kmersplit[0]]['cconf']*$basearray[$kmersplit[1]]['cconf']*$basearray[$kmersplit[2]]['cconf']*$basearray[$kmersplit[3]]['cconf']*$basearray[$kmersplit[4]]['cconf'];
						$ckmernum = $ckmerprob * $complementkmersampleresults->num_rows;
						$ckmerarray[$k]['cconf']=($v['ccon']/$complementkmersampleresults->num_rows);

						
						echo "<td>" . round(($v['con']/$kmersampleresults->num_rows),6,PHP_ROUND_HALF_UP) . ":" . round(($v['ccon']/$complementkmersampleresults->num_rows),6,PHP_ROUND_HALF_UP) . "</td>";//observed kmer frequency consensus
						echo "<td>" . round( $kmernum, 0, PHP_ROUND_HALF_UP) . ":" . round ( $ckmernum,0,PHP_ROUND_HALF_UP) . "</td>"; //expected number of kmers
						echo "<td>" . round(($v['con'] / round( $kmernum, 0, PHP_ROUND_HALF_UP)),3,PHP_ROUND_HALF_UP) . ":" . round(($v['ccon'] / round( $ckmernum, 0, PHP_ROUND_HALF_UP)),3,PHP_ROUND_HALF_UP) . "</td>"; //observed div expected
						echo "<td>" . $v['ml'] . ":" . $v['cml'] . "</td>";//observed kmer count most probable
						//Calculate the chance of seeing a given kmer based on the base frequencies observed in the data set
						//$kmersplit=str_split($k);//don't need to repeat
						
						$kmerprob = $basearray[$kmersplit[0]]['mlf']*$basearray[$kmersplit[1]]['mlf']*$basearray[$kmersplit[2]]['mlf']*$basearray[$kmersplit[3]]['mlf']*$basearray[$kmersplit[4]]['mlf'];
						$kmernum = $kmerprob * $kmersampleresults->num_rows;
						$kmerarray[$k]['mlf']=($v['ml']/$kmersampleresults->num_rows);
						
						$ckmerprob = $basearray[$kmersplit[0]]['cmlf']*$basearray[$kmersplit[1]]['cmlf']*$basearray[$kmersplit[2]]['cmlf']*$basearray[$kmersplit[3]]['cmlf']*$basearray[$kmersplit[4]]['cmlf'];
						$ckmernum = $ckmerprob * $complementkmersampleresults->num_rows;
						$ckmerarray[$k]['cmlf']=($v['cml']/$complementkmersampleresults->num_rows);
						
						
						echo "<td>" . round(($v['ml']/$kmersampleresults->num_rows),6,PHP_ROUND_HALF_UP) .  ":" . round(($v['cml']/$complementkmersampleresults->num_rows),6,PHP_ROUND_HALF_UP)  . "</td>";//observed kmer frequency most probable
						echo "<td>" . round( $kmernum, 0, PHP_ROUND_HALF_UP) . ":" . round( $ckmernum, 0, PHP_ROUND_HALF_UP) . "</td>"; //expected number of kmers
						echo "<td>" . round(($v['ml'] / round( $kmernum, 0, PHP_ROUND_HALF_UP)),3,PHP_ROUND_HALF_UP) . ":" . round(($v['cml'] / round( $ckmernum, 0, PHP_ROUND_HALF_UP)),3,PHP_ROUND_HALF_UP) . "</td>"; //observed div expected
						echo "</tr>";
						
						
					
					}
					
					echo "</tbody>";
					echo "</table>";

					
					
					//Calculate the proportion of ATGC
					foreach ($basearray as $base => $type) {
						$basearray[$base]['conf'] = $type['con']/(5*$kmersampleresults->num_rows);
						$basearray[$base]['mlf'] = $type['ml']/(5*$kmersampleresults->num_rows);
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
