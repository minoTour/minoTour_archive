<?php



	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
		//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {


		$table_check = "SHOW TABLES LIKE 'last_align_basecalled_template'";
		$table_exists = $mindb_connection->query($table_check);
		$sql_template;
		if ($table_exists->num_rows >= 1){

			$sql_template = "SELECT refid,refname, max(refpos) as max_length FROM last_align_basecalled_template inner join reference_seq_info using (refid) group by refid;";
		}else{
			$sql_template = "SELECT ref_id as refid,refname, max(ref_pos) as max_length FROM reference_coverage_template inner join reference_seq_info where ref_id = refid group by refid;";
		}

		$template=$mindb_connection->query($sql_template);

		$array;
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$array[$row['refid']] = $row['refname'] ;
				if ($row['max_length'] > $maxlengththreshold) {
					$start = 0;
					$end = $maxlengththreshold;
				}else{
					$start = -1;
					$end = -1;
				}
				echo "
				<script>

				$(document).ready(function() {
					var options" . $row['refid'] . " = {
						chart: {
							renderTo: 'coverage" . $row['refid'] . "',
							//type: 'scatter',
							type: 'line',
							zoomType: 'x'
						},
						title: {
						  text: 'Coverage Depth for ".$row['refname']."'
						},
						tooltip: {
					formatter: function () {
						var s = 'Coverage Depth at Position: <b>' + this.x + '</b>';

						$.each(this.points, function () {
							s += '<br/>' + this.series.name + ': ' +
								this.y.toPrecision(5) ;
						});

						return s;
					},
					shared: true
				},
				xAxis: {
					title: {
						text: 'Position'
					},
					labels: {
						formatter: function () {
							return this.value + ' bp';
				},
				";

		echo "
			}
				},
				yAxis: {
					title: {
						text: 'Depth',
					},
					labels: {
						align: 'left',
						x: 2,
						y: 5
					},
					min: 0
				},


						scrollbar: {
							enabled: true,
						},
						navigator: {
					xAxis: {
						labels: {
						formatter: function () {
							return this.value + ' bp';
						}
					}
					}
				},
						plotOptions: {
							scatter: {
								marker: {
									radius: 1,
								}
							}
						},
						rangeSelector: {
							selected: 4,
							inputEnabled: false,
							buttonTheme: {
								visibility: 'hidden'
							},
							labelStyle: {
								visibility: 'hidden'
							}
						},
						credits: {
							enabled: false,
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle',
							borderWidth: 0,
						},
						series: []
					};
					$('#range".$row['refid']."').ionRangeSlider({
						hide_min_max: true,
						keyboard: false,
						min: 0,
						max: ".$row['max_length'].",
						from: ".($maxlengththreshold/2).",
						type: 'single',
						step: 500,
						grid: true,
						onFinish: function(data){
							$.getJSON('jsonencode/coverage.php?prev=0&start='+(Number(data.from)-".$modamount.")+'&end='+(Number(data.from)+".$modamount.")+'&seqid=" . $row['refid'] . "&callback=?
', function(data) {
								//alert('testing success');
								options1.series = data; // <- just assign the data to the series property.
								var chart" . $row['refid'] . " = new Highcharts.StockChart(options" . $row['refid'] . ");
								});
						}

					});
					$.getJSON('jsonencode/coverage.php?prev=0&start=".$start."&end=".$end."&seqid=" . $row['refid'] . "&callback=?', function(data) {
						//alert('success');
						options" . $row['refid'] . ".series = data; // <- just assign the data to the series property.



						//options.series = JSON2;
						var chart" . $row['refid'] . " = new Highcharts.StockChart(options" . $row['refid'] . ");
						});
				});

					//]]>








					</script>";
echo "
			<script>
				$(document).ready(function() {
					var optionsprecoverage" . $row['refid'] . " = {
						chart: {
							renderTo: 'precoverage" . $row['refid'] . "',
							zoomType: 'x',
							//type: 'scatter',
							type: 'line',
						},
						title: {
							text: 'Raw Coverage Depth for ".$row['refname']."',
						},
						tooltip: {
		            formatter: function () {
		                var s = 'Coverage Depth at Position: <b>' + this.x + '</b>';

		                $.each(this.points, function () {
		                    s += '<br/>' + this.series.name + ': ' +
		                        this.y.toPrecision(5) ;
		                });

		                return s;
		            },
		            shared: true
		        },
				xAxis: {
                    title: {
                        text: 'Position'
                    },
                    labels: {
                        formatter: function () {
                            return this.value + ' bp';
                },
				";
				if ($row['max_length'] >= $maxlengththreshold) {
					$max = round($row['max_length']/2) + $modamount;
					$min = round($row['max_length']/2) - $modamount;
					echo "
				min: " . $min . ",
				max: " . $max . ",";
					$constrain_plot = 1;
				}
		echo "
            }
                },
                yAxis: {
                    title: {
                        text: 'Depth',
                    },
                    labels: {
                        align: 'left',
                        x: 2,
                        y: 5
                    },
                    min: 0
                },


						scrollbar: {
							enabled: true,
						},
						navigator: {
		            xAxis: {
		                labels: {
		                formatter: function () {
		                    return this.value + ' bp';
		                }
		            }
		            }
		        },
						plotOptions: {
							scatter: {
								marker: {
									radius: 1,
								}
							}
						},
						rangeSelector: {
		                    selected: 4,
		                    inputEnabled: false,
		                    buttonTheme: {
		                        visibility: 'hidden'
		                    },
		                    labelStyle: {
		                        visibility: 'hidden'
		                    }
		                },
						credits: {
							enabled: false,
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle',
							borderWidth: 0,
						},
						series: []
					};
					//alert ('max is ".$max."');
				    $.getJSON('jsonencode/precoverage.php?prev=0&seqid=" . $row['refid'] . "&callback=?', function(data) {
						//alert('success');
				        optionsprecoverage" . $row['refid'] . ".series = data; // <- just assign the data to the series property.



				        //options.series = JSON2;
						var chart = new Highcharts.StockChart(optionsprecoverage" . $row['refid'] . ");
					});
				});
			</script>";




			}
		}

		foreach ($array as $key => $value){
			echo "
			<script>

			$(document).ready(function() {
			    var options5primecoverage" . $key . " = {
			        chart: {
			            renderTo: '5primecoverage" . $key . "',
						//type: 'scatter',
						type: 'line',
						zoomType: 'x'
			        },
			        title: {
			          text: '5 prime Read Coverage for ".$value."'
			        },
					tooltip: {
				formatter: function () {
					var s = 'Coverage Depth at Position: <b>' + this.x + '</b>';

					$.each(this.points, function () {
						s += '<br/>' + this.series.name + ': ' +
							this.y.toPrecision(5) ;
					});

					return s;
				},
				shared: true
			},
			xAxis: {
				title: {
					text: 'Position'
				},
				labels: {
					formatter: function () {
						return this.value + ' bp';
			},
			";
			if ($row['max_length'] >= $maxlengththreshold) {
				$max = round($row['max_length']/2) + $modamount;
				$min = round($row['max_length']/2) - $modamount;
				echo "
			min: " . $min . ",
			max: " . $max . ",";
				$constrain_plot = 1;
			}
	echo "
		}
			},
			yAxis: {
				title: {
					text: 'Depth',
				},
				labels: {
					align: 'left',
					x: 2,
					y: 5
				},
				min: 0
			},


					scrollbar: {
						enabled: true,
					},
					navigator: {
				xAxis: {
					labels: {
					formatter: function () {
						return this.value + ' bp';
					}
				}
				}
			},
					plotOptions: {
						scatter: {
							marker: {
								radius: 1,
							}
						}
					},
					rangeSelector: {
						selected: 4,
						inputEnabled: false,
						buttonTheme: {
							visibility: 'hidden'
						},
						labelStyle: {
							visibility: 'hidden'
						}
					},
					credits: {
						enabled: false,
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle',
						borderWidth: 0,
					},
					series: []
				};

			    $.getJSON('jsonencode/5primecoverage.php?prev=0&seqid=".$key."&callback=?', function(data) {
					//alert('success');
			        options5primecoverage" . $key . ".series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.StockChart(options5primecoverage" . $key . ");
					});
			});

				//]]>

				</script>
			<script>

			$(document).ready(function() {
			    var options3primecoverage" . $key . " = {
			        chart: {
			            renderTo: '3primecoverage" . $key . "',
						//type: 'scatter',
						type: 'line',
						zoomType: 'x'
			        },
			        title: {
			          text: '3 prime read coverage for " . $value . "'
			        },
					tooltip: {
				formatter: function () {
					var s = 'Coverage Depth at Position: <b>' + this.x + '</b>';

					$.each(this.points, function () {
							s += '<br/>' + this.series.name + ': ' +
							this.y.toPrecision(5) ;
					});

					return s;
				},
				shared: true
			},
			xAxis: {
				title: {
					text: 'Position'
				},
				labels: {
					formatter: function () {
						return this.value + ' bp';
			},
			";
			if ($row['max_length'] >= $maxlengththreshold) {
				$max = round($row['max_length']/2) + $modamount;
				$min = round($row['max_length']/2) - $modamount;
				echo "
			min: " . $min . ",
			max: " . $max . ",";
				$constrain_plot = 1;
			}
	echo "
		}
			},
			yAxis: {
				title: {
					text: 'Depth',
				},
				labels: {
					align: 'left',
					x: 2,
					y: 5
				},
				min: 0
			},


					scrollbar: {
						enabled: true,
					},
					navigator: {
				xAxis: {
					labels: {
					formatter: function () {
						return this.value + ' bp';
					}
				}
				}
			},
					plotOptions: {
						scatter: {
							marker: {
								radius: 1,
							}
						}
					},
					rangeSelector: {
						selected: 4,
						inputEnabled: false,
						buttonTheme: {
							visibility: 'hidden'
						},
						labelStyle: {
							visibility: 'hidden'
						}
					},
					credits: {
						enabled: false,
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle',
						borderWidth: 0,
					},
					series: []
				};

			    $.getJSON('jsonencode/3primecoverage.php?prev=0&seqid=" . $key . "&callback=?', function(data) {
					//alert('success');
			        options3primecoverage" . $key . ".series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.StockChart(options3primecoverage" . $key . ");
					});
			});

				//]]>

				</script>";

		}
	}

?>
