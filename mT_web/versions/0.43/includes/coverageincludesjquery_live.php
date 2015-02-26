<?php


	
	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
		//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		
		//$sql_template = "select refpos, count(*) as count from last_align_basecalled_template where refpos != \'null\' and (cigarclass = 7 or cigarclass = 8) group by refpos;";
		
		$sql_template = "SELECT refid,refname FROM last_align_basecalled_template inner join reference_seq_info using (refid) group by refid;";
		
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
							type: 'scatter',
							zoomType: 'xy'
				        },
				        title: {
				          text: 'Coverage Depth for ".$row['refname']."'
				        },
						xAxis: {
						            title: {
						                text: 'Ref Position'
						            }
						        },
								yAxis: {
								            title: {
								                text: 'Depth'
								            }
								        },
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
						type: 'scatter',
						zoomType: 'xy'
			        },
			        title: {
			          text: '5 prime Read Coverage for ".$value."'
			        },
					xAxis: {
					            title: {
					                text: 'Ref Position'
					            }
					        },
							yAxis: {
							            title: {
							                text: '5\' End Coverage'
							            }
							        },
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
						type: 'scatter',
						zoomType: 'xy'
			        },
			        title: {
			          text: '3 prime read coverage for " . $value . "'
			        },
					xAxis: {
					            title: {
					                text: 'Ref Position'
					            }
					        },
							yAxis: {
							            title: {
							                text: '3\' Read Coverage'
							            }
							        },
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