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
                     <h1 class="page-header">Reads Summary - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></h1>
                                                       <!-- /.col-lg-12 -->
                 <div class="row">
					 <div class="col-lg-12">
					 
            			 <table id="example" class="display table table-condensed table-hover " cellspacing="0" width="100%">
							 <thead>
								 <tr>
								 		<th>Channel</th>
								 		<th>Read</th>
									 <th>Basename</th>
									 <th>Template</th>
									 <th>T Aligns</th>
									 <th>T Length</th>
									 <th>T Start</th>
									 <th>T Duration</th>
									 <th>Complement</th>
									 <th>C Aligns</th>
									 <th>C Length</th>
									 <th>C Start</th>
									 <th>C Duration</th>
									 <th>2d</th>
									 <th>2d Aligns</th>
									 <th>2d Length</th>
								 </tr>
							 </thead>
 
							 <tfoot>	
								 <tr>
								 		<th>Channel</th>
								 		<th>Read</th>
									 <th>Basename</th>
									 <th>Template</th>
									 <th>T Aligns</th>
									 <th>T Length</th>
									 <th>T Start</th>
									 <th>T Duration</th>
									 <th>Complement</th>
									 <th>C Aligns</th>
									 <th>C Length</th>
									 <th>C Start</th>
									 <th>C Duration</th>
									 <th>2d</th>
									 <th>2d Aligns</th>
									 <th>2d Length</th>
								 </tr>
							 </tfoot>

						 </table>
					 </div>
				 </div>
             </div>
			 
			
			 <div class="row">
				 <div class="col-lg-12">
				 <?php if ($_SESSION['currenttelem'] >= 1) {?> 
				 <h4>Viewer options</h4>
				 <input type='checkbox' id='toggle-two' data-onstyle='primary' data-offstyle='info' data-size='mini'>If ticked you will see data in base order.
			<script>
				  $(function() {
				    $('#toggle-two').bootstrapToggle({
				      on: 'Event View',
				      off: 'Time View'
				    });
				  })
				  
				</script>
				<?php } ?>
					 <div id = "read_details">Click on a read from the table above to view specific details.</div>
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
	

	
	<!-- Highcharts Addition -->
	
	
	
	
    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>

    <!-- Page-Level Demo Scripts - Dashboard - Use for reference -->
		<script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>


		
		
		<script>
		$(document).ready(function() {
		    $('#example').dataTable( {
		    	"columnDefs": [
				    { "visible": false, "targets": 2 }
				  ],
				"sDom": '<"top"ilp>rt<"bottom"><"clear">',
		        "processing": true,
		        "serverSide": true,
		        "sAjaxSource": "data_tables/data_table2.php?prev=0"
		    } );
		    oTable = $('#example').dataTable( );
			$('#example tbody').on('click', 'tr', function () {
			        var nameindex = $('td', this).eq(0).text();
			        //alert (nameindex);
			        var name = oTable.fnGetData(this,2);
			        //alert (name);
					var channel = oTable.fnGetData(this,0).slice(-1);
					//alert (channel);
			        //alert(name);
			        var templength = $('td', this).eq(4).text();
			        var complength = $('td', this).eq(9).text();
			        var length2d = $('td', this).eq(14).text();
			        
			        if (templength >= 1000 || complength >= 1000 || length2d >= 1000) {
			        	var midpoint = templength/2;
			        	var xmin = midpoint-250;
			        	var xmax = midpoint+250;
			        }else{
			        	var xmin;
			        	var xmax;
			        }
			        
			        var tstart = parseFloat($('td',this).eq(5).text());
			        var tdur = parseFloat($('td',this).eq(6).text());
			        
			        if (tdur >= 15) {
			        	//alert (tdur);
						var mod = 5;
			        	var tmin = tstart+(tdur/2)-mod;
			        	var tmax = tstart+(tdur/2)+mod;
			        	//alert (tmax);	
			        }else {
			        	var tmin;
			        	var tmax;	
			        }
			        
			        
			        var compstart = parseFloat($('td',this).eq(10).text());
			        var compdur = parseFloat($('td',this).eq(11).text());
			        //alert (compdur);
			        if (compdur >= 15) {
			        	var mod = 5;
			        	var cmin = compstart+(compdur/2)-mod;
			        	var cmax = compstart+(compdur/2)+mod;	
			        }else {
			        	var cmin;
			        	var cmax;	
			        }
			        
			        var ttrue = $('td',this).eq(2).text();
			        var ctrue = $('td',this).eq(7).text();
   			        var d2true = $('td',this).eq(12).text();
			        

					$.post( "views/live_read_details.php?prev=0", { readname: name })
					  .done(function( data ) {
						  //alert('badger');
					    $("#read_details").html(data);
					    var optionssteve = {
					    	chart: {
					            renderTo: 'allqualities',
								zoomType: 'x',
								//type: 'scatter',
					            type: 'line'
					        },
					        title: {
					          text: 'Read Qualities'
					        },
							xAxis: {
								title: {
									text: 'Basepairs'
							    },
							    min: xmin,
							    max: xmax,
					        },
							yAxis: {
					            title: {
					                text: 'Quality Score'
					            }
					        },
					        scrollbar: {
      							  enabled: false
    						},
    						navigator: {
 	  						  enabled: true
    					    },
					        plotOptions: {
					    	    scatter: {
			 	                   marker: {
				                       radius: 2
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
					    
					    var options = {
					    	chart: {
					            renderTo: 'templatesquiggles',
								zoomType: 'x',
								//type: 'scatter',
					            type: 'line'
					        },
					        title: {
					          text: 'Template Squiggle Plot'
					        },
							xAxis: {
								title: {
									text: 'Time (s)'
							    },
							    min: tmin,
							    max: tmax,
					        },
							yAxis: {
					            title: {
					                text: 'Current'
					            }
					        },
					        navigator: {
 	  						  enabled: true
    					    },
					        scrollbar: {
      							  enabled: false
    						},
					        plotOptions: {
					    	    scatter: {
			 	                   marker: {
				                       radius: 1
				                   }	
				     	         },
				     	         series: {
                						lineWidth: 1
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
					    
					    var optionscomplement = {
					    	chart: {
					            renderTo: 'complementsquiggles',
								zoomType: 'x',
								//type: 'scatter',
					            type: 'line'
					        },
					        title: {
					          text: 'Complement Squiggle Plot'
					        },
							xAxis: {
								title: {
									text: 'Time (s)'
							    },
							    min: cmin,
							    max: cmax,
					        },
							yAxis: {
					            title: {
					                text: 'Current'
					            }
					        },
					        scrollbar: {
      							  enabled: false
    						},
    						navigator: {
 	  						  enabled: true
    					    },
					        plotOptions: {
					    	    scatter: {
			 	                   marker: {
				                       radius: 1
				                   }	
				     	         },
				     	         series: {
                						lineWidth: 1
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
					    var groupingUnits = [[
                				'millisecond',                         // unit name
					                [1, 2, 5, 10, 20, 25, 50, 100, 200, 500]                             // allowed multiples
					            ], [
                				'second',
                					[1, 2, 5, 10, 15, 30]
					            ]];
					            
					    var newdata;
					    if ($('#toggle-two').prop('checked') == true) {
					    	newdata = {
					    	 rangeSelector: {
				                selected: 1
				            },
					    	chart: {
					           	renderTo: 'templatefancy',
								zoomType: 'x',
								//type: 'scatter',
					           	type: 'line'
					        },
					        title: {
					          text: 'Template Combined Squiggle Quality Probability and Called Base Plot',
					          
					        },
					        xAxis: {
								title: {
									text: 'Bases'
							    },
							    min: xmin,
							    max: xmax,
					        },
					         yAxis: [{
				                labels: {
            				        align: 'right',
            	    			    x: -3
            	   				},
            	    			title: {
            	        			text: 'Squiggle'
				                },
				                height: '28%',
				                lineWidth: 1
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Quality'
				                },
				                top: '30%',
				                height: '28%',
				                offset: 0,
				                lineWidth: 1
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Base Probabilities'
				                },
				                top: '60%',
				                height: '28%',
				                offset: 0,
				                lineWidth: 1,
				                min:0
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Bases'
				                },
				                max: 1,
				                min: 0,
				                top: '90%',
				                height: '10%',
				                offset: 0,
				                lineWidth: 1
				            }],
					        scrollbar: {
      							  enabled: false
    						},
    						navigator: {
 	  						  enabled: true
    					    },
					        plotOptions: {
					    	    scatter: {
			 	                   marker: {
				                       enabled: false
				                   }	
				     	         },
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
					    	
					    	
					    }else{
					    	newdata = {
					    	 rangeSelector: {
				                selected: 1
				            },
					    	chart: {
					           	renderTo: 'templatefancy',
								zoomType: 'x',
								//type: 'scatter',
					           	type: 'line'
					        },
					        title: {
					          text: 'Template Combined Squiggle Quality Probability and Called Base Plot',
					          
					        },
					        xAxis: {
								title: {
									text: 'Time (s)'
							    },
							    min: tmin,
							    max: tmax,
					        },
					         yAxis: [{
				                labels: {
            				        align: 'right',
            	    			    x: -3
            	   				},
            	    			title: {
            	        			text: 'Squiggle'
				                },
				                height: '28%',
				                lineWidth: 1
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Quality'
				                },
				                top: '30%',
				                height: '28%',
				                offset: 0,
				                lineWidth: 1
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Base Probabilities'
				                },
				                top: '60%',
				                height: '28%',
				                offset: 0,
				                lineWidth: 1,
				                min:0
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Bases'
				                },
				                max: 1,
				                min: 0,
				                top: '90%',
				                height: '10%',
				                offset: 0,
				                lineWidth: 1
				            }],
					        scrollbar: {
      							  enabled: false
    						},
    						navigator: {
 	  						  enabled: true
    					    },
					        plotOptions: {
					    	    scatter: {
			 	                   marker: {
				                       enabled: false
				                   }	
				     	         },
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
					    }
					    var newdata2;
					    if ($('#toggle-two').prop('checked') == true) {
					    	newdata2 = {
					    	 rangeSelector: {
				                selected: 1
				            },
					    	chart: {
					           	renderTo: 'complementfancy',
								zoomType: 'x',
								//type: 'scatter',
					           	type: 'line'
					        },
					        title: {
					          text: 'Complement Combi - Squiggle/Quality/Probability/Called Bases',
					          
					        },
					        xAxis: {
								title: {
									text: 'Bases'
							    },
							    min: xmin,
							    max: xmax,
					        },
					         yAxis: [{
				                labels: {
            				        align: 'right',
            	    			    x: -3
            	   				},
            	    			title: {
            	        			text: 'Squiggle'
				                },
				                height: '28%',
				                lineWidth: 1
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Quality'
				                },
				                top: '30%',
				                height: '28%',
				                offset: 0,
				                lineWidth: 1
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Base Probabilities'
				                },
				                top: '60%',
				                height: '28%',
				                offset: 0,
				                lineWidth: 1,
				                min:0
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Bases'
				                },
				                max: 1,
				                min: 0,
				                top: '90%',
				                height: '10%',
				                offset: 0,
				                lineWidth: 1
				            }],
					        scrollbar: {
      							  enabled: false
    						},
    						navigator: {
 	  						  enabled: true
    					    },
					        plotOptions: {
					    	    scatter: {
			 	                   marker: {
				                       enabled: false
				                   }	
				     	         },
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
					    }else{
					    	newdata2 = {
					    	 rangeSelector: {
				                selected: 1
				            },
					    	chart: {
					           	renderTo: 'complementfancy',
								zoomType: 'x',
								//type: 'scatter',
					           	type: 'line'
					        },
					        title: {
					          text: 'Complement Combi - Squiggle/Quality/Probability/Called Bases',
					          
					        },
					        xAxis: {
								title: {
									text: 'Time (s)'
							    },
							    min: cmin,
							    max: cmax,
					        },
					         yAxis: [{
				                labels: {
            				        align: 'right',
            	    			    x: -3
            	   				},
            	    			title: {
            	        			text: 'Squiggle'
				                },
				                height: '28%',
				                lineWidth: 1
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Quality'
				                },
				                top: '30%',
				                height: '28%',
				                offset: 0,
				                lineWidth: 1
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Base Probabilities'
				                },
				                top: '60%',
				                height: '28%',
				                offset: 0,
				                lineWidth: 1,
				                min:0
				            }, {
            				    labels: {
                    				align: 'right',
         					           x: -3
                					},
				                title: {
				                    text: 'Bases'
				                },
				                max: 1,
				                min: 0,
				                top: '90%',
				                height: '10%',
				                offset: 0,
				                lineWidth: 1
				            }],
					        scrollbar: {
      							  enabled: false
    						},
    						navigator: {
 	  						  enabled: true
    					    },
					        plotOptions: {
					    	    scatter: {
			 	                   marker: {
				                       enabled: false
				                   }	
				     	         },
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
					    }
					    

						//if 	(ttrue == 'Y') {
					    //$.getJSON('jsonencode/allqualities.php?prev=0&readname='+name+'&callback=?', function(data) {
					        //optionssteve.series = data; // <- just assign the data to the series property.
						//	var chart = new Highcharts.Chart(optionssteve);
						//});
						//}else{
					    //	$( '#allqualities' ).remove();
					    //}
					    if 	(ttrue == 'Y') {
					    	if ($('#toggle-two').prop('checked') == true) {
								$.getJSON('jsonencode/squiggles3.php?channel='+channel+'&prev=0&readname='+name+'&type=template&callback=?', function(data){
									newdata.series = data;
									var chart = new Highcharts.Chart(newdata);
								});
					    	}else{
					    		$.getJSON('jsonencode/squiggles2.php?channel='+channel+'&prev=0&readname='+name+'&type=template&callback=?', function(data){
									newdata.series = data;
									var chart = new Highcharts.Chart(newdata);
								});
					    	}		
						}else{
					    	$( '#templatefancy' ).remove();
					    }	
					    if (ctrue == 'Y') {
							if ($('#toggle-two').prop('checked') == true) {
								$.getJSON('jsonencode/squiggles3.php?channel='+channel+'&prev=0&readname='+name+'&type=complement&callback=?', function(data){
									newdata2.series = data;
									var chart = new Highcharts.Chart(newdata2);
								});
					    	}else{
					    		$.getJSON('jsonencode/squiggles2.php?channel='+channel+'&prev=0&readname='+name+'&type=complement&callback=?', function(data){
									newdata2.series = data;
									var chart = new Highcharts.Chart(newdata2);
								});
					    	}					   
						}else{
					    	$( '#complementfancy' ).remove();
					    }
					    	
						$('html, body').animate({
						           'scrollTop':   $('#'+name).offset().top
						}, 1000);
						
					});
			    } );
			    $(function() {
					 $('#toggle-two').change(function() {
					 	var checker = $(this).prop('checked');
					 	if (checker == true) {
							//$.getJSON('jsonencode/squiggles3.php?prev=0&readname='+name+'&type=template&callback=?', function(data){
									
							//		var chart = $('#templatefancy').highcharts();
									//chart.destroy();
							//		chart.series = data;
									//var chart = new Highcharts.Chart(newdata);
							//		chart.redraw();
							//});
							//$.getJSON('jsonencode/squiggles3.php?prev=0&readname='+name+'&type=complement&callback=?', function(data){
							//		newdata2.series = data;
							//		var chart = new Highcharts.Chart(newdata2);
							//});

					 	}
					 	if (checker == false) {
					 		//$.getJSON('jsonencode/squiggles2.php?prev=0&readname='+name+'&type=template&callback=?', function(data){
							//		newdata.series = data;
							//		var chart = new Highcharts.Chart(newdata);
							//});
							//$.getJSON('jsonencode/squiggles3.php?prev=0&readname='+name+'&type=complement&callback=?', function(data){
							//		newdata2.series = data;
							//		var chart = new Highcharts.Chart(newdata2);
							//});
					 	}
				    })
				  });
		
		} );
	
		 	</script>
		 	<script>
		 	
		 	</script>
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
	<!-- Highcharts Addition -->
	<!--<script src='js/highcharts.js'></script>-->
	<script src='http://code.highcharts.com/stock/2.0.4/highstock.js'></script>
	<script type='text/javascript' src='js/themes/grid-light.js'></script>
	<script src='http://code.highcharts.com/modules/heatmap.js'></script>
	<script src='http://code.highcharts.com/modules/exporting.js'></script>";
	

</html>
