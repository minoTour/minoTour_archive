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
  			  <li><a href="previous_bases.php">Base Coverage</a></li>
  			  <li class="active"><a href="previous_development.php">W.I.M.M (Dev)</a></li>
			</ul>
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title"><!-- Button trigger modal -->
<button class="btn btn-info" data-toggle="modal" data-target="#modal3">
 <i class="fa fa-info-circle"></i> New Views</h4>
</button>

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
		We are constantly working on new ways of interrogating the data from minION sequencers. However, more so than any other area of the site these may produce incorrect or erroneous data. In some cases we are exploring with ways of presenting the data or alternative methods for analysing data in real time. Once a view has been more thoroughly analysed the (Dev) marking will be removed.<br>
		<h3>What's in my minION</h3>
		Rather shamelessy borrowed from ONT's 'What's in my Pot' we present the development of our equivalent feature 'What's in your minION' - we were working on this prior to nanopore's announcement of a similar feature! Essentially this plots the number of sequences alignable to different sequences within your reference sequence file over the time course of a run.
		<br>Currently this view suffers from lag with respect to data upload to the rest of the site (see the Live Data tab to keep track of how processing is going). It should also only be considered indicative of the relative proportions of reads matching to one or more components in the reference sequence file provided for comparison. It does not provide a global analysis of data - specifically it will not tell you about contaminant sequences unless you have included those sequences in your reference file for uploading. Sequences which do not align cannot be distinguished from contaminant sequences at this time.<br>
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
					<?php $arr = array("template", "complement", "2d");?>
					<?php foreach ($arr as $key => $value) {
						
						//echo $key . " " . $value . "<br>";?>
						<div id="wimm<?php echo $key;?>" style="width:100%; height:300px;"><i class="fa fa-cog fa-spin fa-3x" ></i> Calculating <?php echo $value;?> WIMM</div>
						<?php
					}
					?>
				
				<?php }else { ?>
												<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence - we cannot determine a WIMM plot for it.</small></p></div>
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
	
	
					<?php $arr = array("template", "complement", "2d");?>
					<?php foreach ($arr as $key => $value) {
						//echo $key . " " . $value . "<br>";?>
	
			<script>
		$(document).ready(function() {
		    var options = {
		        chart: {
		            renderTo: 'wimm<?php echo $key;?>',
					zoomType: 'x',
		            type: 'area',
		        },
		        title: {
		          text: '<?php echo $value;?> WIMM PLOT'
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
				                text: 'Time (S)'
				            }
				        },
						yAxis: [{
				                labels: {
            				        align: 'right',
            	    			    x: -3
            	   				},
            	    			title: {
            	        			text: '<?php echo $value;?>'
				                },
				                height: '100%',
				                lineWidth: 1
				            }],
								credits: {
								    enabled: false
								  },
		        legend: {
		        	title: {
                text: 'Sequence Type<br/><span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
                style: {
                    fontStyle: 'italic'
                }
            },

		            layout: 'vertical',
		            align: 'center',
		            //verticalAlign: 'middle',
		            borderWidth: 0
		        },
		        series: []
		    };
	
		    $.getJSON('jsonencode/wimm.php?prev=1&type=<?php echo $value; ?>&callback=?', function(data) {
				//alert("success");
		        options.series = data; // <- just assign the data to the series property.
	        
		 
		
		        //options.series = JSON2;
				var chart = new Highcharts.Chart(options);
				});
		});

			//]]>  

			</script>
		<?php } ?>
		
				
			
 
			
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
