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
                     <div class="alert alert-danger" role="alert">This page is under development. Many features will <strong>not work</strong> at this time.</div>
                 </div>
                 <!-- /.col-lg-12 -->
                 <div class="row">
					 <div class="col-lg-12">
					 
            			 <table id="example" class="display table table-condensed table-hover " cellspacing="0" width="100%">
							 <thead>
								 <tr>
									 <th>Basename</th>
									 <th>Template</th>
									 <th>T Aligns</th>
									 <th>T Length</th>
									 <th>Complement</th>
									 <th>C Aligns</th>
									 <th>C Length</th>
									 <th>2d</th>
									 <th>2d Aligns</th>
									 <th>2d Length</th>
								 </tr>
							 </thead>
 
							 <tfoot>	
								 <tr>
									 <th>Basename</th>
									 <th>Template</th>
									 <th>T Aligns</th>
									 <th>T Length</th>
									 <th>Complement</th>
									 <th>C Aligns</th>
									 <th>C Length</th>
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
				"sDom": '<"top"ilp>rt<"bottom"><"clear">',
		        "processing": true,
		        "serverSide": true,
		        "sAjaxSource": "data_tables/data_table2.php?prev=1"
		    } );
			$('#example tbody').on('click', 'tr', function () {
			        var name = $('td', this).eq(0).text();
			        //alert( name );
					$.post( "views/read_details.php?prev=1", { readname: name })
					  .done(function( data ) {
						  //alert('badger');
					    $("#read_details").html(data);
					    var options = {
					        chart: {
					            renderTo: 'allqualities',
								zoomType: 'x'
					            //type: 'line'
					        },
					        title: {
					          text: 'Read Qualities'
					        },
							xAxis: {
							            title: {
							                text: 'Basepairs'
							            }
							        },
									yAxis: {
									            title: {
									                text: 'Quality Score'
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
	
					    $.getJSON('jsonencode/allqualities.php?prev=1&readname='+name+'&callback=?', function(data) {
						
					        options.series = data; // <- just assign the data to the series property.
	        
		 
		
					        //options.series = JSON2;
							var chart = new Highcharts.Chart(options);
							});
					

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
	<script src='js/highcharts.js'></script>
	<script type='text/javascript' src='js/themes/grid-light.js'></script>
	<script src='http://code.highcharts.com/4.0.3/modules/heatmap.js'></script>
	<script src='http://code.highcharts.com/modules/exporting.js'></script>";

</html>
