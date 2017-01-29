<script>
		$(document).ready(function() {
			var options = {
				chart: {
					type: 'boxplot',
					renderTo: 'boxplotlength'
				},
				plotOptions: {
					boxplot: {
						animation: false,
						//colorByPoint: true
					}
				},
				title: {
				  text: 'Boxplot of Read Lengths'
				},
				legend: {
					enabled: false
				},

				xAxis: {
					categories: ['Template', 'Complement', '2D', 'Raw Template', 'Raw Complement'],
					title: {
						text: 'Read Type'
					}
				},
				yAxis: {
					//type: 'logarithmic',
					title: {
						text: 'Read Length'
					},
					type: 'logarithmic',
					min :1,

				},




				series: []
			};
			function loadchirpbpl() {

					if($('#readsummarycheck').prop('checked')) {
   										   $.getJSON('jsonencode/boxplotlength.php?prev=0&callback=?', function(data) {
										//	   options.xAxis.categories = json[0]['data'];
       				   					//		options.series[0] = json[1];
                                        options.series = data; // <- just assign the data to the series property.
										//alert (data);
                                                setTimeout(loadchirpbpl,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirpbpl,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


				        loadchirpbpl();

			});

				//]]>

</script>


<script>
		$(document).ready(function() {
			var options = {
				chart: {
					renderTo: 'mappabletime',
					zoomType: 'x',
					type: 'spline',
				},
				title: {
				  text: 'Proportion of reads mapping over time'
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
									text: 'Proportion Reads'
								},
								height: '100%',
								lineWidth: 1,
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
					//verticalAlign: 'middle',
					borderWidth: 0
				},
				series: []
			};
			function loadchirpmtw() {

					if($('#readsummarycheck').prop('checked')) {
   										   $.getJSON('jsonencode/mappabletime.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirpmtw,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirpmtw,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


				        loadchirpmtw();

			});

				//]]>

</script>



<script>
		$(document).ready(function() {
			var options = {
				chart: {
					renderTo: 'lengthtimewindow',
					zoomType: 'x',
					type: 'spline',
				},
				title: {
				  text: 'Average Read Lengths Over Time'
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
									text: 'Average Read Length'
								},
								height: '100%',
								lineWidth: 1,
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
					//verticalAlign: 'middle',
					borderWidth: 0
				},
				series: []
			};
			function loadchirpltw() {

					if($('#readsummarycheck').prop('checked')) {
   										   $.getJSON('jsonencode/lengthtimewindow.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirpltw,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirpltw,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


				        loadchirpltw();

			});

				//]]>

</script>



<script>
		$(document).ready(function() {
		    var options = {
		        chart: {
		            renderTo: 'cumulativeyield',
					zoomType: 'x',
		            type: 'spline',
		        },
		        title: {
		          text: 'Cumulative Reads'
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
            	        			text: 'Cumulative Reads'
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
			function loadchirpcy() {

					if($('#sequencingratecheck').prop('checked')) {
   										   $.getJSON('jsonencode/cumulativeyield.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirpcy,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirpcy,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


				        loadchirpcy();

			});

				//]]>

</script>


<script>
		$(document).ready(function() {
		    var options = {
		        chart: {
		            renderTo: 'sequencingrate',
					zoomType: 'x',
		            type: 'spline',
		        },
		        title: {
		          text: 'Sequencing Rate'
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
            	        			text: 'Bases/Second'
				                },
				                height: '100%',
				                lineWidth: 1,
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
		            //verticalAlign: 'middle',
		            borderWidth: 0
		        },
		        series: []
		    };
			function loadchirpsr() {

					if($('#sequencingratecheck').prop('checked')) {
   										   $.getJSON('jsonencode/sequencingrate.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirpsr,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirpsr,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


				        loadchirpsr();

			});

				//]]>

</script>

<script>

			$(document).ready(function() {
			    var options = {
			        chart: {
			            renderTo: 'ratiopassfail',
						//zoomType: 'x'
			            type: 'spline'
			        },
					plotOptions: {
					            spline: {
					                animation: false,
									marker: {
							            enabled: false
							        }

					            }
					        },
			        title: {
			          text: '2d, Complement and Template Pass/Fail Proportions'
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
							                text: '% of total template reads'
							            },
							            min: 0,
                                        max: 100
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
				function loadchirprpf() {

					if($('#sequencingratecheck').prop('checked')) {
   										   $.getJSON('jsonencode/ratiopassfail.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirprpf,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirprpf,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


				        loadchirprpf();

			});

				//]]>

</script>

<script>

			$(document).ready(function() {
			    var options = {
			        chart: {
			            renderTo: 'ratio2dtemplate',
						//zoomType: 'x'
			            type: 'spline'
			        },
					plotOptions: {
					            spline: {
					                animation: false,
									marker: {
							            enabled: false
							        }

					            }
					        },
			        title: {
			          text: '2d, Complement and Template reads in 15 minute windows'
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
							                text: 'Reads'
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
				function loadchirpr2t() {

					if($('#sequencingratecheck').prop('checked')) {
   										   $.getJSON('jsonencode/ratio2dtemplate.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirpr2t,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirpr2t,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


				        loadchirpr2t();

			});

				//]]>

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
						            	categories: []
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
                                        layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
                                        borderWidth: 0
                                    },
                                    series: []
                                };
                                function loadchirpbarcodcov() {
									if($('#barcodingcheck').prop('checked')) {
   										 $.getJSON('jsonencode/barcodingcov.php?prev=0&callback=?', function(data) {
                                                //alert("success");
                                                options.xAxis.categories = data[0]['data'];
                                                options.series = data.slice(1,);
                                        //options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirpbarcodcov,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirpbarcodcov,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
                                        loadchirpbarcodcov();

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
			    	function loadchirpbarcode() {

if($('#barcodingcheck').prop('checked')) {
   										 $.getJSON('jsonencode/barcodingpie.php?prev=0&callback=?', function(data) {
       options.series = data; // <- just assign the data to the series property.



	                setTimeout(loadchirpbarcode,<?php echo $_SESSION['pagerefresh'];?>);


	                var chart = new Highcharts.Chart(options);
	                });} else {
   setTimeout(loadchirpbarcode,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
	        loadchirpbarcode();

});

			   			</script>



<!-- Pore Mux Data -->

<script>
			$(document).ready(function() {
			    var options = {
			        chart: {
						renderTo: 'readmuxproduction',
			            type: 'heatmap',
			            marginTop: 30,
			            marginBottom: 30
			        },


			        title: {
			            text: 'Reads Per Pore/Mux'
			        },

			        xAxis: {
			        	categories: [],
			            title: 'Columns',
						labels: {
						    enabled: false
						  },

			        },

			        yAxis: {
			        	categories: [],
			            title: 'Rows',
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
			    	function loadchirp23() {

if($('#poreactivitycheck').prop('checked')) {
   										 $.getJSON('jsonencode/readsperporemux.php?prev=0&callback=?', function(data) {
       options.series = data; // <- just assign the data to the series property.



	                setTimeout(loadchirp23,<?php echo $_SESSION['pagerefresh'];?>);


	                var chart = new Highcharts.Chart(options);
	                });} else {
   setTimeout(loadchirp23,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
	        loadchirp23();

});

			   			</script>



<script>

                            $(document).ready(function() {
                            	//alert ($(window).width());
                            	if ($(window).width() >=720) {
                                var options = {
                                    chart: {
                                        renderTo: 'processing',
                                        type: 'bar',
                                        //type: 'line'
                                    },
                                    plotOptions: {
                                    	bar: {
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

                                      text: 'Read Upload And Processing'

                                    },
                                    xAxis: {
                                                title: {
                                                    text: 'Strand'
                                                },
                                                categories: [
									                'template',
									                'complement',
									                '2d',
									                ]

                                            },
                                            yAxis: {
                                                        title: {
                                                            text: 'Number of Reads'
                                                        }
                                                    },
                                                    credits: {
                                                        enabled: false
                                                      },
                                    legend: {
                                        layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
                                        borderWidth: 0,
                                        reversed: true
                                    },
                                    series: []
                                };
                            	}else{
                            	var options = {
                                    chart: {
                                        renderTo: 'processing',
                                        type: 'bar',
                                        //type: 'line'
                                    },
                                    plotOptions: {
                                    	bar: {
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

                                      text: ''

                                    },
                                    xAxis: {
                                                title: {
                                                    text: 'Strand'
                                                },
                                                categories: [
									                't',
									                'c',
									                '2d',
									                ]

                                            },
                                            yAxis: {
                                                        title: {
                                                            text: 'Number of Reads'
                                                        }
                                                    },
                                                    credits: {
                                                        enabled: false
                                                      },
                                    legend: {
                                        layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
                                        borderWidth: 0,
                                        reversed: true
                                    },
                                    series: []
                                };

                            	}
                                function loadchirp77() {
									if($('#readsummarycheck').prop('checked')) {
   										 $.getJSON('jsonencode/readnumber2.php?prev=0&callback=?', function(data) {
                                                //alert("success");

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp77,<?php echo $_SESSION['pagerefresh'] ;?>);


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp7,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
                                        loadchirp77();

                            });



                                //]]>

                                </script>


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
                                      text: 'Read Count'
                                    },
                                    xAxis: {
                                                title: {
                                                    text: 'Strand'
                                                },
                                                labels: {
						            	enabled:false,
						            },
                                                categories: [
									                'Template',
									                'Complement',
									                '2d',
									                ]

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
						            },
						            labels: {
						            	enabled:false,
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
										            },
										            labels: {
						            	enabled:false,
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
										            },
										            labels: {
						            	enabled:false,
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


                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp9,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }
									        loadchirp9();

								});


									//]]>

									</script>


								<?php if ($_SESSION['activereference'] != "NOREFERENCE") {?>

                                        <?php if (count($_SESSION['activerefnames']) >= 3){?>
                                            <script>
                                                $(document).ready(function() {
                                                    var options = {
                                                        chart: {
                                                            renderTo: 'percentcoverageglob',
                                                            type: 'column',
                                                        },
                                                        plotOptions: {
                                                            column: {
                                                                animation: false,
                                                            }
                                                        },
                                                        title: {
                                                          text: 'Percentage of Reference Sequenced'
                                                        },
                                                        xAxis: {
                                                                    title: {
                                                                        text: 'Reference'
                                                                    },
                                                                    labels: {
                                                                        rotation: -45,
                                                            enabled:true,
                                                            },
                                                            categories: []
                                                                },
                                                                yAxis: {
                                                                            title: {
                                                                                text: '% Coverage'
                                                                            }
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
                                                    function loadchirppercentcoverageglob() {
                                                        $.getJSON('jsonencode/percentcoverageglob.php?prev=0&callback=?', function(data) {
                                                            options.xAxis.categories = data[0]['data'];
                                                            options.series = data.slice(1,); // <- just assign the data to the series property.
                                                            setTimeout(loadchirppercentcoverageglob,<?php //echo $_SESSION['pagerefresh'] ;?>);
                                                            var chart = new Highcharts.Chart(options);
                                                        })
                                                    };

                                                    loadchirppercentcoverageglob();

                                                });
                                            </script>

                                            <script>

                                            $(document).ready(function() {
                                                //alert("camel");
                                                var options = {
                                                    chart: {
                                                        renderTo: 'depthcoverageglob',
                                                        type: 'column'
                                                        //type: 'line'
                                                    },
                                                    plotOptions: {
                                                        column: {
                                                            animation: false,
                                                        }
                                                            },
                                                    title: {
                                                      text: 'Average Depth of Sequenced Positions'
                                                    },
                                                    xAxis: {
                                                                title: {
                                                                    text: 'Reference'
                                                                },
                                                                labels: {
                                                                    rotation: -45,
                                                        enabled:true,
                                                        },
                                                            },
                                                            yAxis: {
                                                                        title: {
                                                                            text: 'Depth'
                                                                            //text: ''
                                                                        },
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
                                                function loadchirpdepthglob() {
                if($('#readsummarycheck').prop('checked')) {
                                         $.getJSON('jsonencode/depthcoverageglob.php?prev=0&refid=<?php echo $key;?>&callback=?', function(data) {
                                             options.xAxis.categories = data[0]['data'];
                                             options.series = data.slice(1,);
                                                setTimeout(loadchirpdepthglob,<?php echo $_SESSION['pagerefresh'] ;?>);
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirpdepthglob,<?php echo $_SESSION['pagerefresh'] ;?>);
}

                                        }


                                                        loadchirpdepthglob();

                                            });

                                                //]]>

                                                </script>

                                            <?php
                                        }else{?>
									<?php foreach ($_SESSION['activerefnames'] as $key => $value) {
										//echo $key . " " . $value . "<br>";?>


										<script>
																				$(document).ready(function() {
																				    var options = {
																				        chart: {
																				            renderTo: 'percentcoverage<?php echo $key;?>',
																							type: 'bar'
																				            //type: 'line'
																				        },
																						plotOptions: {
																						            bar: {
																						                animation: false

																						            }
																						        },
																				        title: {
																				          text: 'Percentage of Reference (<?php echo $value;?>) with Read'
																				        },
																						xAxis: {
																						            title: {
																						                text: 'Strand'
																						            }
																						        },
																								yAxis: {
																								            title: {
																								                //text: 'Percentage'
																								                text: ''
																								            },
																											max: 100
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
																					function loadchirp10<?php echo $key;?>() {
										                								if($('#readsummarycheck').prop('checked')) {
										   										 $.getJSON('jsonencode/percentcoverage.php?prev=0&refid=<?php echo $key;?>&callback=?', function(data) {
										                                                //alert("success");

										                                        options.series = data; // <- just assign the data to the series property.

										                                                setTimeout(loadchirp10<?php echo $key;?>,<?php echo $_SESSION['pagerefresh'] ;?>);


										                                                var chart = new Highcharts.Chart(options);
										                                                });} else {
										   setTimeout(loadchirp10<?php echo $key;?>,<?php echo $_SESSION['pagerefresh'] ;?>);
										}

										                                        }


																					        loadchirp10<?php echo $key;?>();

																				});


																					//]]>

																					</script>
																					<script>

																					$(document).ready(function() {
																					    var options = {
																					        chart: {
																					            renderTo: 'depthcoverage<?php echo $key;?>',
																								type: 'bar'
																					            //type: 'line'
																					        },
																							plotOptions: {
																							            bar: {
																							                animation: false

																							            }
																							        },
																					        title: {
																					          text: 'Average Depth of Sequenced Positions (<?php echo $value;?>)'
																					        },
																							xAxis: {
																							            title: {
																							                text: 'Strand'
																							            }
																							        },
																									yAxis: {
																									            title: {
																									                //text: 'Depth'
																									                text: ''
																									            },
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
																						function loadchirp11<?php echo $key;?>() {
										                if($('#readsummarycheck').prop('checked')) {
										   										 $.getJSON('jsonencode/depthcoverage.php?prev=0&refid=<?php echo $key;?>&callback=?', function(data) {

										                                                //alert("success");

										                                        options.series = data; // <- just assign the data to the series property.

										                                                setTimeout(loadchirp11<?php echo $key;?>,<?php echo $_SESSION['pagerefresh'] ;?>);


										                                                var chart = new Highcharts.Chart(options);
										                                                });} else {
										   setTimeout(loadchirp11<?php echo $key;?>,<?php echo $_SESSION['pagerefresh'] ;?>);
										}

										                                        }


																						        loadchirp11<?php echo $key;?>();

																					});

																						//]]>

																						</script>




										<?php
                                        }
										}
										?>
										<?php }
										?>

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
            layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
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
							                text: 'Reads/Minute'
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
				function loadchirp() {
					if($('#sequencingratecheck').prop('checked')) {
   										   $.getJSON('jsonencode/reads_over_time2.php?prev=0&callback=?', function(data) {

                                        options.series = data; // <- just assign the data to the series property.

                                                setTimeout(loadchirp,<?php echo $_SESSION['pagerefresh'] ;?>);


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
						            },

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
                                                setTimeout(loadchirp2,<?php echo $_SESSION['pagerefresh'];?>);
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
                                                    setTimeout(loadchirp2,<?php echo $_SESSION['pagerefresh'] ;?>);
                                                }
                                        }
                                    loadchirp2();
				});
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
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp3,<?php echo $_SESSION['pagerefresh'] ;?>);
}
                                        }
				   loadchirp3();
				});
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
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp4,<?php echo $_SESSION['pagerefresh']; ?>);
                                        }
loadchirp4();
					});
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
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp6,<?php echo $_SESSION['pagerefresh'] ;?>);
}
                                        }
							        loadchirp6();
						});
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
							            layout: 'horizontal',
                                        align: 'center',
                                        verticalAlign: 'bottom',
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
                                                var chart = new Highcharts.Chart(options);
                                                });} else {
   setTimeout(loadchirp12,<?php echo $_SESSION['pagerefresh'] ;?>);
}
                                        }
								        loadchirp12();
							});
								</script>
