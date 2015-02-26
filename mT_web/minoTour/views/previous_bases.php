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
                    <h1 class="page-header">Previous Data Summary - run: <?php echo cleanname($_SESSION['focusrun']);; ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<ul class="nav nav-pills">
			  <li><a href="previous_summary.php">Read Summaries</a></li>
			  <li><a href="previous_histogram.php">Read Histograms</a></li>
			  <li><a href="previous_rates.php">Sequencing Rates</a></li>
			  <li><a href="previous_pores.php">Pore Activity</a></li>
  			  <li><a href="previous_quality.php">Read Quality</a></li>
  			   <?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>
  			  <li><a href="previous_coverage.php">Coverage Detail</a></li>
  			  <?php }; ?>
  			  <li class="active"><a href="previous_bases.php">Base Coverage (Dev)</a></li>
  			  <li><a href="previous_development.php">W.I.M.M (Dev)</a></li>
			</ul>
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title"><!-- Button trigger modal -->
<button class="btn btn-info" data-toggle="modal" data-target="#modal3">
 <i class="fa fa-info-circle"></i> New Views
</button></h3><h4>Please note this page can be extremely slow to load.</h4>

<!-- Modal -->
<div class="modal fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"> New Views
      </div>
      <div class="modal-body">
        Work in Progress<br>
		This is a work in progress - more so than any other area of the site these may produce incorrect or erroneous data. In some cases we are exploring with ways of presenting data or alternative methods for analysing data in real time. Once a view has been more thoroughly analysed the (Dev) marking will be removed.  <br>
		<h3>Base Coverage</h3>
		These plots show a view of base coverage across the entire reference. For large reference sequences this view will break the site currently.<br>To generate these plots, we take the MAF alignment data from Last and use it to call bases where they align to the reference. We call either the base which is aligned at that position, a single deletion if no base is placed at that point, or an insertion if extra bases are present. The insertion count is incremented by the number of inserted bases at that position. This is something which will be addressed in more detail in the future.
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
					
					<?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>
				
					<?php foreach ($_SESSION['focusrefnames'] as $key => $value) {
						//echo $key . " " . $value . "<br>";?>
						<div id="basesnpcoverage<?php echo $key;?>" style="width:100%; height:900px;"><i class="fa fa-cog fa-spin fa-3x" ></i> Calculating 'Base SNP Coverage Plots' for <?php echo $value;?></div>
						<?php
					}
					?>
				
				<?php }else { ?>
												<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence and so no SNPs can be called.</small></p></div>
				<?php }; ?>
					
					
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
	
	
	
	
					
		<?php foreach ($_SESSION['focusrefnames'] as $key => $value) {
			//echo $key . " " . $value . "<br>";?>
				<script>
			$(document).ready(function() {
			    var options = {
			        chart: {
			            renderTo: 'basesnpcoverage<?php echo $key;?>',
						zoomType: 'x',
			            type: 'column',
			        },
			        title: {
			          text: 'Base SNP Coverage <?php echo $value;?>'
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
	            	        			text: 'Template'
					                },
					                height: '33%',
					                lineWidth: 1
					            }, {
	            				    labels: {
	                    				align: 'right',
	         					           x: -3
	                					},
					                title: {
					                    text: 'Complement'
					                },
					                top: '33%',
					                height: '33%',
					                offset: 0,
					                lineWidth: 1
					            }, {
	            				    labels: {
	                    				align: 'right',
	         					           x: -3
	                					},
					                title: {
					                    text: '2D'
					                },
					                top: '66%',
					                height: '33%',
					                offset: 0,
					                lineWidth: 1,
					                min:0
					            }],
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
	
			    $.getJSON('jsonencode/basesnpcoverage.php?prev=1&refid=<?php echo $key;?>&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.
	        
		 
		
			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});
			});

				//]]>  

				</script>
			<?php
		}
		?>
		
			
 
			
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
