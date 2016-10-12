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
                    <h1 class="page-header">Previous Data Summary - run: <?php echo cleanname($_SESSION['focusrun']); ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <ul class="nav nav-pills">
			  <li><a href="previous_summary.php">Read Summaries</a></li>
              <?php if ($_SESSION['focusbasesum'] > 0){?>
              <li class="active"><a href="previous_basecalling.php">Basecaller Summary</a></li>
              <?php }; ?>
              <li><a href="previous_histogram.php">Read Histograms</a></li>
			  <li><a href="previous_rates.php">Sequencing Rates</a></li>
			  <li><a href="previous_pores.php">Pore Activity</a></li>
  			  <li><a href="previous_quality.php">Read Quality</a></li>
  			   <?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>
  			  <li><a href="previous_coverage.php">Coverage Detail</a></li>
  			  <?php }; ?>
  			  <li><a href="previous_bases.php">Base Coverage</a></li>
  			  <!--<li><a href="current_development.php">W.I.M.M (Dev)</a></li>-->
			</ul>
						<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title"><!-- Button trigger modal -->
<button class="btn btn-info" data-toggle="modal" data-target="#modal2">
 <i class="fa fa-info-circle"></i> Basecalling Summary Information</h4>
</button>

<!-- Modal -->
<div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"> Sequencing Rate Information
      </div>
      <div class="modal-body">
        A number of plots will be available on this page summarising the basecalling analysis as reported by metrichor.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div></h3>
			  </div>
			  <div class="panel-body">
                    <div id="meanshifttime" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Mean Shifts for reads Over Time</div>
                    <div id="meanscaletime" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Mean Scales for reads Over Time</div>
					<div id="meanqualtime" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Mean Read Qualities Over Time</div>
			  		<div id="meanabasicheight" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating The Mean Abasic Height Over Time.</div>
                    <div id="meancurrentplots" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Mean Currents Over Time.</div>
					<div id="meanskips" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Mean Skips Over Time.</div>
					<div id="meanstays" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Mean Stays Over Time</div>

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
<script src="https://raw.githubusercontent.com/highcharts/export-csv/master/export-csv.js"></script>

    <script>

$(document).ready(function() {
    var options = {
                    chart: {
                        renderTo: 'meanshifttime',
                        zoomType: 'x',
                        type: 'spline'
                        //type: 'line'
                    },
                    plotOptions: {
                                spline: {
                                    animation: false,
                                    marker: {
                                        enabled: false,
                                    }

                                }
                            },
                    title: {
                      text: 'Mean Shifts in Template/Complement Model Correction Over Time'
                    },
                    xAxis: {
                    type: 'datetime',
                        dateTimeLabelFormats: { // don't display the dummy year
                            month: '%e. %b',
                            year: '%b',
                            },
                            title: {
                                text: 'Time/Date'
                            }
                        },
                            yAxis: {
                                        title: {
                                            text: 'Shifts'
                                        },
                                        //min: 0
                                    },
                                    credits: {
                                        enabled: false
                                      },
                    legend: {
                    title: {
                text: 'Read Type <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
                style: {
                    fontStyle: 'italic'
                }
            },
                        layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
                        borderWidth: 0
                    },
                    series: []
                };
    $.getJSON('jsonencode/meanparamtime.php?prev=1&timewin=1minwin&param=shiftT&param2=shiftC&callback=?', function(data) {
        //alert("success");
        options.series = data; // <- just assign the data to the series property.



        //options.series = JSON2;
        var chart = new Highcharts.Chart(options);
        });
});

    //]]>

    </script>

    <script>

$(document).ready(function() {
    var options = {
                    chart: {
                        renderTo: 'meanscaletime',
                        zoomType: 'x',
                        type: 'spline'
                        //type: 'line'
                    },
                    plotOptions: {
                                spline: {
                                    animation: false,
                                    marker: {
                                        enabled: false,
                                    }

                                }
                            },
                    title: {
                      text: 'Mean Scales in Template and Complement Model Correction Over Time'
                    },
                    xAxis: {
                    type: 'datetime',
                        dateTimeLabelFormats: { // don't display the dummy year
                            month: '%e. %b',
                            year: '%b',
                            },
                            title: {
                                text: 'Time/Date'
                            }
                        },
                            yAxis: {
                                        title: {
                                            text: 'Scale'
                                        },
                                        //min: 0
                                    },
                                    credits: {
                                        enabled: false
                                      },
                    legend: {
                    title: {
                text: 'Read Type <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
                style: {
                    fontStyle: 'italic'
                }
            },
                        layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
                        borderWidth: 0
                    },
                    series: []
                };
    $.getJSON('jsonencode/meanparamtime.php?prev=1&timewin=1minwin&param=scaleT&param2=scaleC&callback=?', function(data) {
        //alert("success");
        options.series = data; // <- just assign the data to the series property.



        //options.series = JSON2;
        var chart = new Highcharts.Chart(options);
        });
});

    //]]>

    </script>


	<script>
		$(document).ready(function() {
		    var options = {
                chart: {
		            renderTo: 'meanqualtime',
					zoomType: 'x',
		            type: 'spline',
		        },
		        title: {
		          text: 'Mean Quality Over Time'
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
		        	spline: {
					                animation: false,
									marker: {
							            enabled: false
							        }

				},



        },
				xAxis: {
					type: 'datetime',
			            dateTimeLabelFormats: { // don't display the dummy year
               				month: '%e. %b',
           				    year: '%b'
				            },
				            title: {
				                text: 'Time/Date'
				            }
				        },
						yAxis: [{
				                labels: {
            				        align: 'right',
            	    			    x: -3
            	   				},

            	    			title: {
            	        			text: 'Median Quality Score'
				                },
				                height: '100%',
				                lineWidth: 1,
				                min: 0
				            }],
								credits: {
								    enabled: false
								  },
		        legend: {
		        	title: {
                text: 'Read Type <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
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

		    $.getJSON('jsonencode/meanqualtime.php?prev=1&callback=?', function(data) {
				//alert("success");
		        options.series = data; // <- just assign the data to the series property.



		        //options.series = JSON2;
				var chart = new Highcharts.Chart(options);
				});
		});

			//]]>

			</script>

            <script>
            		$(document).ready(function() {
            		    var options = {
            		        chart: {
            		            renderTo: 'meanabasicheight',
            					zoomType: 'x',
            		            type: 'spline',
            		        },
            		        title: {
            		          text: 'Mean Abasic/Hairpin Heights'
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
            		        	spline: {
            					                animation: false,
            									marker: {
            							            enabled: false
            							        }

            				},



                    },
            				xAxis: {
            					type: 'datetime',
            			            dateTimeLabelFormats: { // don't display the dummy year
                           				month: '%e. %b',
                       				    year: '%b'
            				            },
            				            title: {
            				                text: 'Time/Date'
            				            }
            				        },
            						yAxis:
            							{

            				                labels: {
                        				        align: 'right',
                        	    			    x: -3
                        	   				},

                        	    			title: {
                        	        			text: 'Current Shift'
            				                },
            				                height: '100%',
            				                lineWidth: 1,
            				                //min: 0
            				            },
            								credits: {
            								    enabled: false
            								  },
            		        legend: {
            		        	title: {
                            text: 'Read Type <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
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

               			$.getJSON('jsonencode/meanparamtime.php?prev=1&timewin=1minwin&param=abasic_peak&param2=hairpin_peak&callback=?', function(data) {

                        options.series = data; // <- just assign the data to the series property.

                        //options.series = JSON2;
                        var chart = new Highcharts.Chart(options);

            });


            			});

            				//]]>

            </script>

			<script>

			$(document).ready(function() {
			    var options = {
			        chart: {
			            renderTo: 'meancurrentplots',
						//zoomType: 'x'
			            type: 'spline'
			        },
					plotOptions: {
					            spline: {
					                animation: false,
                                    marker:{
                                        enabled: false,
                                    }

					            }
					        },
			        title: {
			          text: 'Median Current Level for Template and Complement Reads Over Time'
			        },
					xAxis: {
						type: 'datetime',
			            dateTimeLabelFormats: { // don't display the dummy year
               				month: '%e. %b',
           				    year: '%b'
				            },
					            title: {
					                text: 'Time/Date'
					            }
					        },
							yAxis: {
							            title: {
							                text: 'Current'
							            },
							            min: 0
							        },
									credits: {
									    enabled: false
									  },
			        legend: {
			            layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
			            borderWidth: 0
			        },
			        series: []
			    };

			    $.getJSON('jsonencode/meanparamtime.php?prev=1&timewin=1minwin&param=median_level_temp&param2=median_level_comp&callback=?', function(data) {
				//alert("success");
		        options.series = data; // <- just assign the data to the series property.



		        //options.series = JSON2;
				var chart = new Highcharts.Chart(options);
				});
		});



				//]]>

</script>




<script>

			$(document).ready(function() {
			    var options = {
			        chart: {
			            renderTo: 'meanskips',
						//zoomType: 'x'
			            type: 'spline'
			        },
					plotOptions: {
					            spline: {
					                animation: false,
                                    marker: {
                                        enabled: false,
                                    }

					            }
					        },
			        title: {
			          text: 'Mean Skips in Template and Complement Reads'
			        },
					xAxis: {
						type: 'datetime',
			            dateTimeLabelFormats: { // don't display the dummy year
               				month: '%e. %b',
           				    year: '%b'
				            },
					            title: {
					                text: 'Time/Date'
					            }
					        },
							yAxis: {
							            title: {
							                text: 'Skips'
							            },
							            min: 0
							        },
									credits: {
									    enabled: false
									  },
			        legend: {
			            layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
			            borderWidth: 0
			        },
			        series: []
			    };
			    $.getJSON('jsonencode/meanparamtime.php?prev=1&timewin=1minwin&param=num_skipsT&param2=num_skipsC&callback=?', function(data) {
				//alert("success");
		        options.series = data; // <- just assign the data to the series property.



		        //options.series = JSON2;
				var chart = new Highcharts.Chart(options);
				});
		});

				//]]>

</script>





	<script>

$(document).ready(function() {
    var options = {
			        chart: {
			            renderTo: 'meanstays',
						zoomType: 'x',
                        type: 'spline'
			            //type: 'line'
			        },
                    plotOptions: {
					            spline: {
					                animation: false,
                                    marker: {
                                        enabled: false,
                                    }

					            }
					        },
			        title: {
			          text: 'Mean Stays in Template and Complement Reads'
			        },
					xAxis: {
					type: 'datetime',
			            dateTimeLabelFormats: { // don't display the dummy year
               				month: '%e. %b',
           				    year: '%b',
				            },
				            title: {
				                text: 'Time/Date'
				            }
				        },
							yAxis: {
							            title: {
							                text: 'Stays'
							            },
							            min: 0
							        },
									credits: {
									    enabled: false
									  },
			        legend: {
		        	title: {
                text: 'Read Type <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
                style: {
                    fontStyle: 'italic'
                }
            },
			            layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
			            borderWidth: 0
			        },
			        series: []
			    };
    $.getJSON('jsonencode/meanparamtime.php?prev=1&timewin=1minwin&param=num_staysT&param2=num_staysC&callback=?', function(data) {
		//alert("success");
        options.series = data; // <- just assign the data to the series property.



        //options.series = JSON2;
		var chart = new Highcharts.Chart(options);
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
