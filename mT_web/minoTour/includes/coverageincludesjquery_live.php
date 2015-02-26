<?php


	
	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
		//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		$maxlengththreshold = 10000;
		$modamount = 2500;
		$max;
		$min;
		$constrain_plot=0;
		
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
				
				echo "
				<script>

				$(document).ready(function() {
				    var options = {
				        chart: {
				            renderTo: 'coverage" . $row['refid'] . "',
							//type: 'scatter',
							type: 'line',
							zoomType: 'x'
				        },
				        title: {
				          text: 'Coverage Depth for ".$row['refname']."'
				        },
						xAxis: {
						            title: {
						                text: 'Ref Position'
						            }";
				if ($row['max_length'] >= $maxlengththreshold) {
					$max = $row['max_length']/2 + $modamount;
					$min = $row['max_length']/2 - $modamount;
					echo ",min: " . $min . ",\nmax: " . $max . ",\n";
					$constrain_plot = 1;
				}
							
				echo "		        
						        },
								yAxis: {
								            title: {
								                text: 'Depth'
								            }
								        },
								        min: 0,
									    plotOptions: {
									               scatter: {
									                   marker: {
									                       radius: 1
									                   }
									               }
									           },
										credits: {
										    enabled: false
										  },
										  scrollbar: {
      							  enabled: true
    						},
    						navigator: {
 	  						  enabled: true
    					    },
				        legend: {
				            layout: 'vertical',
				            align: 'right',
				            verticalAlign: 'middle',
				            borderWidth: 0
				        },
				        series: []
				    };

				    $.getJSON('jsonencode/coverage.php?prev=0&seqid=" . $row['refid'] . "&callback=?', function(data) {
						//alert('success');
				        options.series = data; // <- just assign the data to the series property.
    
 

				        //options.series = JSON2;
						var chart = new Highcharts.Chart(options);
						});
				});

					//]]>  

					</script>";


				
			}
		}
		
		foreach ($array as $key => $value){
			echo "			
			<script>

			$(document).ready(function() {
			    var options = {
			        chart: {
			            renderTo: '5primecoverage" . $key . "',
						//type: 'scatter',
						type: 'line',
						zoomType: 'x'
			        },
			        title: {
			          text: '5 prime Read Coverage for ".$value."'
			        },
					xAxis: {
					            title: {
					                text: 'Ref Position'
					            }";
				if ($constrain_plot == 1) {
					echo ",min: " . $min . ",\nmax: " . $max . ",\n";
				}
							
				echo "		   
					        },
							yAxis: {
							            title: {
							                text: '5\' End Coverage'
							            }
							        },
							        min: 0,
								    plotOptions: {
								               scatter: {
								                   marker: {
								                       radius: 3
								                   }
								               }
								           },
									credits: {
									    enabled: false
									  },
									  scrollbar: {
      							  enabled: true
    						},
    						navigator: {
 	  						  enabled: true
    					    },
			        legend: {
			            layout: 'vertical',
			            align: 'right',
			            verticalAlign: 'middle',
			            borderWidth: 0
			        },
			        series: []
			    };

			    $.getJSON('jsonencode/5primecoverage.php?prev=0&seqid=".$key."&callback=?', function(data) {
					//alert('success');
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
			            renderTo: '3primecoverage" . $key . "',
						//type: 'scatter',
						type: 'line',
						zoomType: 'x'
			        },
			        title: {
			          text: '3 prime read coverage for " . $value . "'
			        },
					xAxis: {
					            title: {
					                text: 'Ref Position'
					            }";
				if ($constrain_plot == 1) {
					echo ",min: " . $min . ",\nmax: " . $max . ",\n";
				}
							
				echo "		   
					        },
							yAxis: {
							            title: {
							                text: '3\' Read Coverage'
							            }
							        },
							        min: 0,
								    plotOptions: {
								               scatter: {
								                   marker: {
								                       radius: 3
								                   }
								               }
								           },
									credits: {
									    enabled: false
									  },
									  scrollbar: {
      							  enabled: true
    						},
    						navigator: {
 	  						  enabled: true
    					    },
			        legend: {
			            layout: 'vertical',
			            align: 'right',
			            verticalAlign: 'middle',
			            borderWidth: 0
			        },
			        series: []
			    };

			    $.getJSON('jsonencode/3primecoverage.php?prev=0&seqid=" . $key . "&callback=?', function(data) {
					//alert('success');
			        options.series = data; // <- just assign the data to the series property.



			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});
			});

				//]]>  

				</script>";
			
		}
	}

?>