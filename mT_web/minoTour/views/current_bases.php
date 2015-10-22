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
                    <h1 class="page-header">Current Data Summary - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<ul class="nav nav-pills">
			  <li><a href="current_summary.php">Read Summaries</a></li>
              <?php if ($_SESSION['currentbasesum'] > 0){?>
              <li><a href="current_basecalling.php">Basecaller Summary</a></li>
              <?php }; ?>
			  <li><a href="current_histogram.php">Read Histograms</a></li>
			  <li><a href="current_rates.php">Sequencing Rates</a></li>
			  <li><a href="current_pores.php">Pore Activity</a></li>
  			  <li><a href="current_quality.php">Read Quality</a></li>
  			   <?php if ($_SESSION['activereference'] != "NOREFERENCE") {?>
  			  <li><a href="current_coverage.php">Coverage Detail</a></li>
  			  <?php }; ?>
  			  <li class="active"><a href="current_bases.php">Base Coverage</a></li>
  			  <!--<li><a href="current_development.php">W.I.M.M (Dev)</a></li>-->
			</ul>
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title"><!-- Button trigger modal -->
<button class="btn btn-info" data-toggle="modal" data-target="#modal3">
 <i class="fa fa-info-circle"></i> Coverage
</button></h3>
<!-- Modal -->
<div class="modal fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"> New Views
      </div>
      <div class="modal-body">
        <h3>Base Coverage</h3>
		These plots show a view of base coverage across the entire reference. <br>To generate these plots, we take the MAF/SAM alignment data from Last/BWA and use it to call bases where they align to the reference. We call either the base which is aligned at that position, a single deletion if no base is placed at that point, or an insertion if extra bases are present. The insertion count is incremented by the number of inserted bases at that position. This is something which will be addressed in more detail in the future.
		<br>

		  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div></h3>
			  </div>
			  <div class="panel-body">
                  <?php if ($_SESSION['currentBASE'] > 0) {?>
					<?php if ($_SESSION['activereference'] != "NOREFERENCE") {?>





						<?php if (!empty($_POST)): ?>
			  	<?php
			  	$type = $_POST["type"];
			  	$coverage = $_POST["coverage"];
			  	$center = $_POST["center"];
			  	$start = $center - ($coverage/2);
			  	$end = $center + ($coverage/2);
			  	$reference = $_POST["reference"];
			  	$leftshift = $_POST["freddy"];
			  	$center = $center-$leftshift;
			  	$_POST["shift"]=0;

			  			?>
   <?php else: ?>
				<?php
				$type = '2d';
				$coverage = 100;
				$center = 500;
			  	$start = $center - ($coverage/2);
			  	$end = $center + ($coverage/2);
			  	$reference =1;
				 ?>
				<?php endif; ?>

						<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post" name="form" class="form-inline">
						<div class="form-group">
						<label for "reference">Reference:</label>
						 <select class="form-control" name="reference" id="reference" onchange="this.form.submit()">
						<?php foreach ($_SESSION['activerefnames'] as $key => $value) {?>
							<option <?php if ($reference == $key){echo "selected=\"selected\"";};?> value="<?php echo $key;?>"><?php echo $value;?></option>
							<?php
					}
					?>
						</select>


    <label for="type">Read Type:</label>
   <select class="form-control" name="type" id="type" onchange="this.form.submit()">
        <option <?php if ($type == 'template'){echo "selected=\"selected\"";};?> value="template">Template</option>
        <option <?php if ($type == 'complement'){echo "selected=\"selected\"";};?> value="complement">Complement</option>
        <option <?php if ($type == '2d'){echo "selected=\"selected\"";};?> value="2d">2d</option>
     </select>
     <label for="coverage">Window Size:</label>
     <select class="form-control" name="coverage" id="coverage" onchange="this.form.submit()">
     	<option <?php if ($coverage == 100){echo "selected=\"selected\"";};?> value="100">100bp</option>
     	<option <?php if ($coverage == 500){echo "selected=\"selected\"";};?> value="500">500bp</option>
     	<option <?php if ($coverage == 1000){echo "selected=\"selected\"";};?> value="1000">1kb</option>
     	<option <?php if ($coverage == 2000){echo "selected=\"selected\"";};?> value="2000">2kb</option>
     	<option <?php if ($coverage == 5000){echo "selected=\"selected\"";};?> value="5000">5kb</option>
     </select>
     </div>
     <br>
     <br>
     <button id="button1" type="button" class="btn btn-default" aria-label="Left Align">
  <span class="fa fa-fast-backward" aria-hidden="true"></span>
</button>
<button id="button2" type="button" class="btn btn-default" aria-label="Left Align">
  <span class="fa fa-backward" aria-hidden="true"></span>
</button>
<button id="button3" type="button" class="btn btn-default" aria-label="Left Align">
  <span class="fa fa-step-backward" aria-hidden="true"></span>
</button>
     Genome Position: <input type="text" name="center" id="center" value="<?php echo $center;?>" onchange="this.form.submit()">
<button id="button4" type="button" class="btn btn-default" aria-label="Left Align">
  <span class="fa fa-step-forward" aria-hidden="true"></span>
</button>
<button id="button5" type="button" class="btn btn-default" aria-label="Left Align">
  <span class="fa fa-forward" aria-hidden="true"></span>
</button>
<button id="button6" type="button" class="btn btn-default" aria-label="Left Align">
  <span class="fa fa-fast-forward" aria-hidden="true"></span>
</button>

    </form>
						<div id="basesnpcoverage" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x" ></i> Calculating 'Base Coverage Plots' for <?php echo $value;?></div>


				<?php }else { ?>
												<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence and so no SNPs can be called.</small></p></div>
				<?php }; ?>
                <?php } else { echo "Bases cannot be shown from the raw data alone. You must upload basecalled data to see these features.";};?>


			  </div>
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
    <script src="http://highslide-software.github.io/export-csv/export-csv.js"></script>
	<script>

	</script>



						<script>
			$(document).ready(function() {
				var start = <?php echo $start;?>;
				var end = <?php echo $end;?>;
				var reference = '<?php echo $reference;?>';
				var type = '<?php echo $type;?>';
			    var options = {
			        chart: {
			            renderTo: 'basesnpcoverage',
						zoomType: 'x',
			            type: 'column',
			        },
			        title: {
			          text: 'Base Coverage <?php echo $_SESSION['activerefnames'][$reference];?>'
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
			        	column: {
	                stacking: 'normal',
			        	},
	            area: {
	                stacking: 'normal',
	                lineColor: '#666666',
	                lineWidth: 1,
	                marker: {
	                	enabled: false,
	                    lineWidth: 1,
	                    lineColor: '#666666'
	                }
	            }
	        },
					xAxis: {
					            title: {
					                text: 'Base Number from reference'
					            }
					        },
							yAxis: [{
					                labels: {
	            				        align: 'right',
	            	    			    x: -3
	            	   				},
	            	    			title: {
	            	        			text: '<?php echo $type; ?> Coverage'
					                },
					                height: '100%',
					                lineWidth: 1
					            },],
									credits: {
									    enabled: false
									  },
			        legend: {
	            title: {
	                text: 'Base<br/><span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
	                style: {
	                    fontStyle: 'italic'
	                }
	            },
	            layout: 'vertical',
	            align: 'right',
	            verticalAlign: 'top',
	            x: -10,
	            y: 100
	        },
			        series: []
			    };

			    $.getJSON('jsonencode/basesnpcoverage.php?prev=0&start=<?php echo $start;?>&end=<?php echo $end; ?>&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});

					$( "#button1" ).click(function() {
					start = start - 1000;
					end = end - 1000;
					$("#center").val($("#center").val()-1000);
					//alert ("HOORAY!");
					$.getJSON('jsonencode/basesnpcoverage.php?prev=0&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});

				});
					$( "#button2" ).click(function() {
					start = start - 500;
					end = end - 500;
					$("#center").val($("#center").val()-500);
					//alert ("HOORAY!");
					$.getJSON('jsonencode/basesnpcoverage.php?prev=0&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});

				});
					$( "#button3" ).click(function() {
					start = start - 100;
					end = end - 100;
					$("#center").val($("#center").val()-100);
					//alert ("HOORAY!");
					$.getJSON('jsonencode/basesnpcoverage.php?prev=0&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});

				});
				$( "#button6" ).click(function() {
					start = start + 1000;
					end = end + 1000;
					$("#center").val(parseFloat($("#center").val())+1000);
					//alert ("HOORAY!");
					$.getJSON('jsonencode/basesnpcoverage.php?prev=0&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});

				});
					$( "#button5" ).click(function() {
					start = start + 500;
					end = end + 500;
					$("#center").val(parseFloat($("#center").val())+500);
					//alert ("HOORAY!");
					$.getJSON('jsonencode/basesnpcoverage.php?prev=0&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});

				});
					$( "#button4" ).click(function() {
					start = start + 100;
					end = end + 100;
					$("#center").val(parseFloat($("#center").val())+100);
					//alert ("HOORAY!");
					$.getJSON('jsonencode/basesnpcoverage.php?prev=0&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});

				});
			});

				//]]>


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
