<!--Reads and Coverage Summary readnum yield avglen maxlen percentcoverage depthcoverage-->

<script>

                            $(document).ready(function() {
                                var options = {
                                    chart: {
                                        renderTo: 'readnum',
                                        type: 'column',
                                        //type: 'line'
                                    },
                                    plotOptions: {
                                                column: {
                                                    animation: false

                                                }
                                            },
                                    title: {
                                      text: 'Reads Called'
                                    },
                                    xAxis: {
                                                title: {
                                                    text: 'Strand'
                                                }
                                            },
                                            yAxis: {
                                                        title: {
                                                            text: 'Number of Reads Called'
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
                                function loadchirp7() {
									if($('#readsummarycheck').prop('checked')) {
   										 $.getJSON('jsonencode/readnumber.php?prev=0&callback=?', function(data) {
                                                //alert("success");

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp7,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp7,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
                                        loadchirp7();

                            });



                                //]]>

                                </script>
                                <script>

				$(document).ready(function() {
				    var options = {
				        chart: {
				            renderTo: 'yield',
							type: 'column',
				            //type: 'line'
				        },
						plotOptions: {
						            column: {
						                animation: false

						            }
						        },
				        title: {
				          text: 'Yield'
				        },
						xAxis: {
						            title: {
						                text: 'Strand'
						            }
						        },
								yAxis: {
								            title: {
								                text: 'Yield'
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
					function loadchirp13() {
                		if($('#readsummarycheck').prop('checked')) {
   										 $.getJSON('jsonencode/volume.php?prev=0&callback=?', function(data) {
                                                //alert("success");

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp13,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp13,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
					        loadchirp13();

				});



					//]]>

					</script>
					<script>

								$(document).ready(function() {
								    var options = {
								        chart: {
								            renderTo: 'avglen',
											type: 'column'
								            //type: 'line'
								        },
										plotOptions: {
										            column: {
										                animation: false

										            }
										        },
								        title: {
								          text: 'Average Read Length'
								        },
										xAxis: {
										            title: {
										                text: 'Strand'
										            }
										        },
												yAxis: {
												            title: {
												                text: 'Average Read Length'
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
									function loadchirp8() {
                						if($('#readsummarycheck').prop('checked')) {
   										 $.getJSON('jsonencode/avelen.php?prev=0&callback=?', function(data) {
                                                //alert("success");

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp8,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp8,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
                                        loadchirp8();

								});


									</script>
<script>

								$(document).ready(function() {
								    var options = {
								        chart: {
								            renderTo: 'maxlen',
											type: 'column',
								            //type: 'line'
								        },
										plotOptions: {
										            column: {
										                animation: false

										            }
										        },
								        title: {
								          text: 'Maximum Read Length'
								        },
										xAxis: {
										            title: {
										                text: 'Strand'
										            }
										        },
												yAxis: {
												            title: {
												                text: 'Maximum Read Length'
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
									function loadchirp9() {
                						if($('#readsummarycheck').prop('checked')) {
   										 $.getJSON('jsonencode/maxlen.php?prev=0&callback=?', function(data) {
                                                //alert("success");

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp9,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp9,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
									        loadchirp9();

								});


									//]]>

									</script>
<script>

										$(document).ready(function() {
										    var options = {
										        chart: {
										            renderTo: 'percentcoverage',
													type: 'bar'
										            //type: 'line'
										        },
												plotOptions: {
												            bar: {
												                animation: false

												            }
												        },
										        title: {
										          text: 'Percentage of Reference with Read'
										        },
												xAxis: {
												            title: {
												                text: 'Strand'
												            }
												        },
														yAxis: {
														            title: {
														                text: 'Percentage'
														            },
																	max: 100
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
											function loadchirp10() {
                								if($('#readsummarycheck').prop('checked')) {
   										 $.getJSON('jsonencode/percentcoverage.php?prev=0&callback=?', function(data) {
                                                //alert("success");

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp10,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp10,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


											        loadchirp10();

										});


											//]]>

											</script>
											<script>

											$(document).ready(function() {
											    var options = {
											        chart: {
											            renderTo: 'depthcoverage',
														type: 'bar'
											            //type: 'line'
											        },
													plotOptions: {
													            bar: {
													                animation: false

													            }
													        },
											        title: {
											          text: 'Average Depth of Sequenced Positions'
											        },
													xAxis: {
													            title: {
													                text: 'Strand'
													            }
													        },
															yAxis: {
															            title: {
															                text: 'Depth'
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
												function loadchirp11() {
                if($('#readsummarycheck').prop('checked')) {
   										 $.getJSON('jsonencode/depthcoverage.php?prev=0&callback=?', function(data) {

                                                //alert("success");

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp11,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp11,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


												        loadchirp11();

											});

												//]]>

												</script>
<!-- Histogram Collection -->
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
	function loadchirp21() {
		 if($('#histogramcheck').prop('checked')) {
   										$.getJSON("jsonencode/histograms.php?prev=0&callback=?", function(json) {
        options.xAxis.categories = json[0]['data'];
        options.series[0] = json[1];
        options.series[1] = json[2];
        options.series[2] = json[3];

	                setTimeout(loadchirp21,<?php echo $_SESSION['pagerefresh'];?>);

	        //options.series = JSON2;
	                var chart = new Highcharts.Chart(options);
	                });
} else {
   setTimeout(loadchirp21,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


	        loadchirp21();

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
	function loadchirp22() {
if($('#histogramcheck').prop('checked')) {
   										 $.getJSON("jsonencode/histogrambases.php?prev=0&callback=?", function(json) {
        options.xAxis.categories = json[0]['data'];
        options.series[0] = json[1];
        options.series[1] = json[2];
        options.series[2] = json[3];

	                setTimeout(loadchirp22,<?php echo $_SESSION['pagerefresh'];?>);

	        //options.series = JSON2;
	                var chart = new Highcharts.Chart(options);
	                });} else {
   setTimeout(loadchirp22,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
	        loadchirp22();

});


</script>

<!-- sequencing rate collection readrate averagelength averagetime -->



<script>

			$(document).ready(function() {
			    var options = {
			        chart: {
			            renderTo: 'readrate',
						zoomType: 'x'
			            //type: 'line'
			        },
					plotOptions: {
					            line: {
					                animation: false

					            }
					        },
			        title: {
			          text: 'Rate Of BaseCalling'
			        },
					xAxis: {
					            title: {
					                text: 'Time (S)'
					            }
					        },
							yAxis: {
							            title: {
							                text: 'Reads/Minute'
							            }
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
				function loadchirp() {
					if($('#sequencingratecheck').prop('checked')) {
   										   $.getJSON('jsonencode/reads_over_time2.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }

				   
				        loadchirp();

			});

				//]]>

				</script>

				<script>

				$(document).ready(function() {
				    var options = {
				        chart: {
				            renderTo: 'averagelength',
							zoomType: 'x',
				            //type: 'line'
				        },
						plotOptions: {
						            line: {
						                animation: false

						            }
						        },
				        title: {
				          text: 'Average Read Length Over Time'
				        },
						xAxis: {
						            title: {
						                text: 'Time (S)'
						            }
						        },
								yAxis: {
								            title: {
								                text: 'Average Read Length'
								            }
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
					function loadchirp2() {
						if($('#sequencingratecheck').prop('checked')) {
   										  $.getJSON('jsonencode/average_length_over_time.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp2,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp2,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
					    
					        loadchirp2();

				});


					//]]>

					</script>

					<script>

				$(document).ready(function() {
				    var options = {
				        chart: {
				            renderTo: 'averagetime',
							zoomType: 'x'
				            //type: 'line'
				        },
						plotOptions: {
						            line: {
						                animation: false

						            }
						        },
				        title: {
				          text: 'Average Time to process Reads Over Time'
				        },
						xAxis: {
						            title: {
						                text: 'Time (S)'
						            }
						        },
								yAxis: {
								            title: {
								                text: 'Average Time To Process Read (s)'
								            }
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
					function loadchirp3() {
						if($('#sequencingratecheck').prop('checked')) {
   										 $.getJSON('jsonencode/average_time_over_time2.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp3,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp3,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }

					   
				   loadchirp3();

				});

					//]]>

					</script>

<!-- Pore Activity Check activechannels poreactivity -->


						<script>
					$(document).ready(function() {
					    var options = {
					        chart: {
					            renderTo: 'activechannels',
								zoomType: 'x'
					            //type: 'line'
					        },
							plotOptions: {
							            line: {
							                animation: false

							            }
							        },
					        title: {
					          text: 'Active Channels Over Time'
					        },
							xAxis: {
							            title: {
							                text: 'Time (S)'
							            }
							        },
									yAxis: {
									            title: {
									                text: 'Number of Active Channels'
									            }
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
						function loadchirp4() {
							if($('#poreactivitycheck').prop('checked')) {
   										  $.getJSON('jsonencode/active_channels_over_time.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp4,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp4,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }

						    						        loadchirp4();

					});


						//]]>

						</script>
			

						<script>
						$(document).ready(function() {
						    var options = {
						        chart: {
									renderTo: 'poreproduction',
						            type: 'heatmap',
						            marginTop: 40,
						            marginBottom: 40
						        },
								plotOptions: {
								            column: {
								                animation: false

								            }
								        },

						        title: {
						            text: 'Reads Per Pore'
						        },

						        xAxis: {
						            title: null,
									labels: {
									    enabled: false
									  },

						        },

						        yAxis: {
						            title: null,
									labels: {
									    enabled: false
									  },

						        },
								credits: {
								    enabled: false
								  },
						        colorAxis: {
						            min: 0,
						            minColor: '#FFFFFF',
						            maxColor: Highcharts.getOptions().colors[0]
						        },

						        legend: {
						            align: 'right',
						            layout: 'vertical',
						            margin: 0,
						            verticalAlign: 'middle',
						            y: 25,
						            symbolHeight: 320
						        },

						        series: []

						    };
							function loadchirp5() {
								if($('#poreactivitycheck').prop('checked')) {
   										 $.getJSON('jsonencode/readsperpore.php?prev=0&callback=?', function(data) {
                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp5,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp5,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
							   
							        loadchirp5();

						});

						</script>
						
<!-- Quality info check avgquallength numberoverlength -->
						
							<script>
						$(document).ready(function() {
						    var options = {
						        chart: {
						            renderTo: 'avgquallength',
									zoomType: 'x'
						            //type: 'line'
						        },
								plotOptions: {
								            line: {
								                animation: false

								            }
								        },
						        title: {
						          text: 'Read Quality Over Length of Aligned Reads'
						        },
								xAxis: {
								            title: {
								                text: 'Basepairs'
								            }
								        },
										yAxis: {
										            title: {
										                text: 'Average Quality Score'
										            }
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
							function loadchirp6() {
								if($('#qualityinfocheck').prop('checked')) {
   										 $.getJSON('jsonencode/readlengthqual.php?prev=0&callback=?', function(data) {                                                //

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp6,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp6,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }

							        loadchirp6();

						});


							//]]>

							</script>
								<script>
							$(document).ready(function() {
							    var options = {
							        chart: {
							            renderTo: 'numberoverlength',
										zoomType: 'x'
							            //type: 'line'
							        },
									plotOptions: {
									            line: {
									                animation: false

									            }
									        },
							        title: {
							          text: 'Read Number Over Length'
							        },
									xAxis: {
									            title: {
									                text: 'Basepairs'
									            }
									        },
											yAxis: {
											            title: {
											                text: 'Number of Reads of this Length'
											            }
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
								function loadchirp12() {
									if($('#qualityinfocheck').prop('checked')) {
   										 $.getJSON('jsonencode/readnumberlength.php?prev=0&callback=?', function(data) {
                                                //alert("success");

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp12,<?php echo $_SESSION['pagerefresh'] ;?>);

                                        //options.series = JSON2;
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp12,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
								    
								        loadchirp12();

							});


								//]]>

								</script>




