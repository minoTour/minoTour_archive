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
			  <li class="active"><a href="current_histogram.php">Read Histograms</a></li>
			  <li><a href="current_rates.php">Sequencing Rates</a></li>
			  <li><a href="current_pores.php">Pore Activity</a></li>
  			  <li><a href="current_quality.php">Read Quality</a></li>
  			  <li><a href="current_coverage.php">Coverage Detail</a></li>
			</ul>
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title"><!-- Button trigger modal -->
<button class="btn btn-info" data-toggle="modal" data-target="#modal1">
 <i class="fa fa-info-circle"></i> Reads Histograms
</button>

<!-- Modal -->
<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Read Histograms</h4>
      </div>
      <div class="modal-body">
        This panel provides a histogram of actual read lengths for template, complement and 2d reads. It can be extremely informative to remove individual read types from the plot by clicking on the legend!<br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div></h3>
			  </div>
			  <div class="panel-body">
				<div class="row">
					<div class="col-md-12" id="container" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Histograms - read numbers</div>
				</div>
				<div class="row">
					<div class="col-md-12" id="container2" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Histograms - base counts</div>
				</div>
			  </div>
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
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	
	<script>
				$(document).ready(function(){
					$('#runsummary').load('includes/runsummary.php');
					setInterval(function(){
    			 	$('#runsummary').load('includes/runsummary.php');
    				}, 1000);
				}); 
				</script>
			
					        <script type="text/javascript">
					        $(document).ready(function() {
					            var options = {
					                chart: {
					                    renderTo: 'container',
					                    type: 'column'
					                },
									plotOptions: {
									            column: {
									                animation: false
								          
									            }
									        },
					                title: {
					                    text: 'Histogram of Read Lengths'
					                },
									credits: {
									    enabled: false
									  },
					                subtitle: {
					                    text: '',
					                    x: -20
					                },
					                xAxis: {
					                    categories: [],
										labels: {
										                rotation: -45,
													},
					                },
					                yAxis: {
					                    title: {
					                        text: 'Number of Reads'
					                    },
					                   
					                },
					                tooltip: {
					                    formatter: function() {
					                            return '<b>'+ this.series.name +'</b><br/>'+
					                            this.x +': '+ this.y;
					                    }
					                },
					                legend: {
					                    layout: 'vertical',
					                    align: 'right',
					                    verticalAlign: 'middle',
					                    borderWidth: 0
					                },
					                series: [],
									groupPadding: 0,
					            };
								    $.getJSON("jsonencode/histograms.php?prev=0&callback=?", function(json) {
					                options.xAxis.categories = json[0]['data'];
					                options.series[0] = json[1];
					                options.series[1] = json[2];
					                options.series[2] = json[3];

								        //options.series = JSON2;
								                var chart = new Highcharts.Chart(options);
								                });
								 
							});
					            
					              
					        </script>
							
					        <script type="text/javascript">
					        $(document).ready(function() {
					            var options = {
					                chart: {
					                    renderTo: 'container2',
					                    type: 'column'
					                },
									plotOptions: {
									            column: {
									                animation: false
								          
									            }
									        },
					                title: {
					                    text: 'Bases sequenced by Read Length'
					                },
									credits: {
									    enabled: false
									  },
					                subtitle: {
					                    text: '',
					                    x: -20
					                },
					                xAxis: {
					                    categories: [],
										labels: {
										                rotation: -45,
													},
					                },
					                yAxis: {
					                    title: {
					                        text: 'Number of Bases'
					                    },
					                   
					                },
					                tooltip: {
					                    formatter: function() {
					                            return '<b>'+ this.series.name +'</b><br/>'+
					                            this.x +': '+ this.y;
					                    }
					                },
					                legend: {
					                    layout: 'vertical',
					                    align: 'right',
					                    verticalAlign: 'middle',
					                    borderWidth: 0
					                },
					                series: [],
									groupPadding: 0,
					            };
								    $.getJSON("jsonencode/histogrambases.php?prev=0&callback=?", function(json) {
					                options.xAxis.categories = json[0]['data'];
					                options.series[0] = json[1];
					                options.series[1] = json[2];
					                options.series[2] = json[3];

								        //options.series = JSON2;
								                var chart = new Highcharts.Chart(options);
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
