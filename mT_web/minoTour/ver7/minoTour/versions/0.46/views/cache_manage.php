<?php
// load the functions
require_once("includes/functions.php");

//As user is logged in, we can now look at the memcache to retrieve data from here and so reduce the load on the mySQL server
	// Connection creation
	$memcache = new Memcache;
	#$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT) or die ("Memcached Failure");
	$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);

?>
<!DOCTYPE html>
<html>

<?php include "includes/head.php";?>
<meta http-equiv="refresh" content="10" > 

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
                    <h1 class="page-header">Cache Management:</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
				<h4>memcache</h4>
				
				<br>
			 	
				<br>
				
				minoTour can take advantage of memcache to speed up performace for all users. This page is used to test if memcache is running and - if so - provide you with some performance related stats.<br><br>
				<?php 
				$memcache->set("php_mem_cache_test", "test", 0, 10);
				$cachecheck = $memcache->get("php_mem_cache_test");
				if ($cachecheck === false) {
					echo "<strong>This minotour installation is not configured to use memcahce. Setting up memcache on your installation will significantly improve performance - please consult with the user manual to set this up.<br></strong>";
				}else{
					echo "<strong>This minotour installation is using memcache.<br></strong>";
				}
				
				$cachecheck = $memcache->get("perl_mem_cache_connection");
				if($cachecheck === false){
					echo "<strong>Your minotour installation is not using background perl scripts which accelerate web performance - please consult with the user manual to set these up.<br></strong>";
				}else {
					echo "<strong>Congratulations, you have memcache up and running and the perl script is communicating well with your web backend!</strong><br>";
					$active_runs = $memcache->get("perl_proc_active");
					if ($active_runs === false) {
						echo "You have no active runs being processed at this time.<br>";
					}else {
						echo "You have $active_runs active runs at this time.<br>";
						for ($x=1; $x<=$active_runs; $x++) {
							$run_to_retrieve = "perl_active_" . $x;
							$runname = $memcache->get($run_to_retrieve);
							echo "The number is $x: $runname <br>";
							$jsonreads = $runname . "bases";
							$json_test = $memcache->get($jsonreads);
							echo $jsonreads . "<br>";
							echo "$json_test<br>";							
						}
						
					}
				}
				?>
				
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
  
	
	<!-- Highcharts Addition -->
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	
	

	
	
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
