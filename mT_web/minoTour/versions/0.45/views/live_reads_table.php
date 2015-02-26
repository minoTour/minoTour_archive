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
                     <div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <strong>Warning!</strong> This page is under development. Some features may not work as expected. Please report bugs if you spot them.</div>

                 </div>
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
			        var name = oTable.fnGetData(this,2);
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

						if 	(ttrue == 'Y') {
					    $.getJSON('jsonencode/allqualities.php?prev=0&readname='+name+'&callback=?', function(data) {
						//$.getJSON('jsonencode/squiggles.php?prev=0&readname='+name+'&type=template&callback=?', function(data){

					        optionssteve.series = data; // <- just assign the data to the series property.
	        
		 
		
					        //options.series = JSON2;
							var chart = new Highcharts.Chart(optionssteve);
						});
						}else{
					    	$( '#allqualities' ).remove();
					    }
					    if 	(ttrue == 'Y') {
						$.getJSON('jsonencode/squiggles.php?prev=0&readname='+name+'&type=template&callback=?', function(data){

					        options.series = data; // <- just assign the data to the series property.
							var chart = new Highcharts.Chart(options);
						});		
						}else{
					    	$( '#templatesquiggles' ).remove();
					    }	
					    if (ctrue == 'Y') {
						$.getJSON('jsonencode/squiggles.php?prev=0&readname='+name+'&type=complement&callback=?', function(data){

					        optionscomplement.series = data; // <- just assign the data to the series property.
							var chart = new Highcharts.Chart(optionscomplement);
						});			   
								}else{
					    	$( '#complementsquiggles' ).remove();
					    }			   
							
						$('html, body').animate({
						           'scrollTop':   $('#'+name).offset().top
						}, 1000);
						
					});
			    } );
		
		} );
	
		 	</script>
<?php include "includes/reporting.php";?>
</body>
	<!-- Highcharts Addition -->
	<!--<script src='js/highcharts.js'></script>-->
	<script src='http://code.highcharts.com/stock/highstock.js'></script>
	<script type='text/javascript' src='js/themes/grid-light.js'></script>
	<script src='http://code.highcharts.com/4.0.3/modules/heatmap.js'></script>
	<script src='http://code.highcharts.com/modules/exporting.js'></script>";

</html>
