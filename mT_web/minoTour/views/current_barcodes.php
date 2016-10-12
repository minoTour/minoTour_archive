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
                    <h1 class="page-header">Barcoding Summary - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
<div class="panel panel-default">
						  <div class="panel-heading">
						    <h3 class="panel-title"><!-- Button trigger modal -->
			<button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#modalbarcodecov">
			 <i class="fa fa-info-circle"></i> Barcoding Summary
			</button>

			<!-- Modal -->
			<div class="modal fade" id="modalbarcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			        <h4 class="modal-title" id="myModalLabel">Barcoding Summary</h4>
			      </div>
			      <div class="modal-body">
			        This panel provides information on the number of reads assigned to each barcode using the Oxford Nanopore barcoding protocol.<br><br>
			        The standard ONT barcoding analysis only searches for barcodes in PASS reads - i.e those reads generating full 2D sequence. Reads which cannot be classified are moved to the fail bin. We therefore show as unclassified (UC) those reads which generated 2D sequence but could not be barcoded by the ONT pipeline in the charts below.<br><br>
					Note that further barcoding analysis options are availble under the specific barcoding tab in the left hand menu.<br>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>
			      </div>
			    </div>
			  </div>
			</div>
						  </div>
						  <div id="barcoding">
						  <div class="panel-body">
									<div class="row">
									<div class="col-md-5" id="barcod" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Barcoding</div>
									<div class="col-md-7" id="barcodcov" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Barcode Coverage</div>


								</div>

						  </div>
						</div>

					</div>

                    <div class="panel panel-default">
                    						  <div class="panel-heading">
                    						    <h3 class="panel-title"><!-- Button trigger modal -->
                    			<button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#modalbarcodecov">
                    			 <i class="fa fa-info-circle"></i> Barcoding Coverage Plots
                    			</button>

                    			<!-- Modal -->
                    			<div class="modal fade" id="modalbarcodecov" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    			  <div class="modal-dialog">
                    			    <div class="modal-content">
                    			      <div class="modal-header">
                    			        <button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    			        <h4 class="modal-title" id="myModalLabel">Barcoding Coverage Plots</h4>
                    			      </div>
                    			      <div class="modal-body">
                    			        These plots provide coverage details on a per barcode basis. They will not work well if you have more than one reference sequence and are currently experimental.<br>
                    			      </div>
                    			      <div class="modal-footer">
                    			        <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>
                    			      </div>
                    			    </div>
                    			  </div>
                    			</div>
                    						  </div>
                    						  <div id="barcodingcov">
                    						  <div class="panel-body">
                    									<div class="row">
                    									<div class="col-md-12" id="barcodcovdet" style="height:1000px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Barcoding Coverage</div>



                    								</div>

                    						  </div>
                    						</div>

                    					</div>


<!--			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title"><!-- Button trigger modal -->
<!--<button class="btn btn-info" data-toggle="modal" data-target="#modal3">
 <i class="fa fa-info-circle"></i> Barcodes Over Time</h4>
</button>

<!-- Modal -->
<!--<div class="modal fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"> New Views
      </div>
      <div class="modal-body">
        This plot shows the accumulation of different barcode sequences over time. This is the total number of barcodes sequenced over time which have aligned to a reference sequence.
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
					<?php if ($_SESSION['activereference'] != "NOREFERENCE") {?>
					<?php $arr = array("template", "complement", "2d");?>
					<?php foreach ($arr as $key => $value) {
						//echo $key . " " . $value . "<br>";?>
						<div id="barcodwimm<?php echo $key;?>" style="width:100%; height:300px;"><i class="fa fa-cog fa-spin fa-3x" ></i> Calculating <?php echo $value;?> WIMM</div>
						<?php
					}
					?>

				<?php }else { ?>
												<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence - we cannot determine a WIMM plot for it.</small></p></div>
				<?php }; ?>



                </div>
                </div>-->


			<br>
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
<script src="https://raw.githubusercontent.com/highcharts/export-csv/master/export-csv.js"></script>

	<script>
				$(document).ready(function(){
					$('#runsummary').load('includes/runsummary.php');
					setInterval(function(){
    			 	$('#runsummary').load('includes/runsummary.php');
    				}, 1000);
				});
	</script>


	<?php $arr = array("template", "complement", "2d");?>
					<?php foreach ($arr as $key => $value) {
						//echo $key . " " . $value . "<br>";?>







			<script>
		$(document).ready(function() {
		    var options = {
		        chart: {
		            renderTo: 'barcodwimm<?php echo $key;?>',
					zoomType: 'x',
		            type: 'area',
		        },
		        title: {
		          text: '<?php echo $value;?> Barcode Plot'
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
            	    			    //x: -3
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
                text: 'Barcode<br/><span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
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

		    $.getJSON('jsonencode/barcodwimm.php?prev=0&type=<?php echo $value; ?>&callback=?', function(data) {
				//alert("success");
		        options.series = data; // <- just assign the data to the series property.



		        //options.series = JSON2;
				var chart = new Highcharts.Chart(options);
				});
		});

			//]]>

			</script>
		<?php } ?>

    <!-- Detailed Barcode Coverage Plots -->

<script>
    $(document).ready(function() {
        chartsetup = {
            chart: {
    		   	renderTo: 'barcodcovdet',
    			//zoomType: 'x',
    			type: 'scatter',
    		   	//type: 'line'
    		},
    		title: {
    		    text: 'Coverage Depth By Barcode',
	        },
	        xAxis: {
				title: {
					text: 'Position (bp)'
			    },
		        //min: tmin,
    			//max: tmax,
    		},
    		yAxis: [
                {
    		        labels: {
                        //align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 1'
    			    },
    			    height: '7%',
    			    lineWidth: 1
    		    },
                {
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 2'
    			    },
                    top: '8%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 3'
    			    },
                    top: '16%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 4'
    			    },
                    top: '24%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 5'
    			    },
                    top: '32%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',

    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 6',

    			    },
                    top: '40%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 7'
    			    },
                    top: '48%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 8'
    			    },
                    top: '56%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 9'
    			    },
                    top: '64%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 10'
    			    },
                    top: '72%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 11'
    			    },
                    top: '80%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },{
    		        labels: {
                        align: 'right',
    	                //x: -3
            	    },
            	    title: {
            	           text: 'Barcode 12'
    			    },
                    top: '88%',
                    offset: 0,
    			    height: '7%',
    			    lineWidth: 1
    		    },
        	],
    		scrollbar: {
          	    enabled: false
        	},
        	navigator: {
     	  	    enabled: true
        	},
    		plotOptions: {

    		line: {
    			marker: {
    		 		enabled: false
    		 	}
    		},
    		},
    		credits: {
    		    enabled: false
    		},
    		legend: {
    		    layout: 'vertical',
    			align: 'right',
    			verticalAlign: 'middle',
    			borderWidth: 0
    		},
    		series: []
    		};
            $.getJSON('jsonencode/coverage_barcodes.php?prev=0&start=-1&end=-1&seqid=1&callback=?', function(data){
				//alert(data);
                chartsetup.series = data;
			    var chart = new Highcharts.Chart(chartsetup);
			});
            //chartsetup.series = data;
			//var chart = new Highcharts.Chart(chartsetup);
    	});

</script>

	<!-- Barcode Coverage Information -->



<script>

                            $(document).ready(function() {
                                var options = {
                                    chart: {
                                        renderTo: 'barcodcov',
                                        type: 'column',
                                        //type: 'line'
                                    },
                                    plotOptions: {
                                    	column: {
                                        	animation: false,
										    //colorByPoint: true
                                        }
                                    },
                                    //colors: [
								      //  '#4A6D8E',
								       // '#7cb5ec',
								       // '#A3CBF2',
								       // '#CBE1F7',
								    //],
                                    title: {
                                      text: 'Coverage Depth'
                                    },
                                    xAxis: {
                                                title: {
                                                    text: 'Barcodes'
                                                },
                                                labels: {
						            	enabled:true,
						            	},
						            	categories: [

									                ]


                                            },
                                            yAxis: {
                                                        title: {
                                                            text: 'Barcode Coverage Depth'
                                                        }
                                                    },
                                                    credits: {
                                                        enabled: false
                                                      },
                                    legend: {
                                        layout: 'vertical',
                                        align: 'center',
                                        verticalAlign: 'bottom',
                                        borderWidth: 0
                                    },
                                    series: []
                                };
                                $.getJSON('jsonencode/barcodingcov.php?prev=0&callback=?', function(data) {
					                //alert("success");

					        options.series = data; // <- just assign the data to the series property.

					        //options.series = JSON2;
					                var chart = new Highcharts.Chart(options);
					                });

				});




                                //]]>

                                </script>



<!-- Barcode Information -->

<script>
$(document).ready(function() {
			    var options = {
			        chart: {
						renderTo: 'barcod',
			            type: 'pie',
			            marginTop: 30,
			            marginBottom: 30
			        },


			        title: {
			            text: 'Barcoding Proportions'
			        },

					credits: {
					    enabled: false
					  },
	            plotOptions: {
            pie: {
            	animation: false,
                allowPointSelect: false,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
				    series: []

			    };
			    $.getJSON('jsonencode/barcodingpie.php?prev=0&callback=?', function(data) {
					                //alert("success");

					        options.series = data; // <- just assign the data to the series property.

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
