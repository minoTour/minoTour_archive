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
                    <h1 class="page-header">Previous Run Information</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div id="messages"></div>
					
					<?php checkuserruns(); ?>
					<br>	
					<?php // getallruns();?>
					
                    <div class="row">
   					 <div class="col-lg-12">
					 
               			 <table id="example" class="display table table-condensed table-hover " cellspacing="0" width="100%">
   							 <thead>
   								 <tr>
   									 <th>Date</th>
   									 <th>Flow Cell ID</th>
   									 <th>Comment</th>
   									 <th>Flow Cell Owner</th>
   									 <th>Run Name</th>
   									 <th>Run Order</th>
									 <th>Ref Sequence</th>
									 <th>Ref Length</th>
   								 </tr>
   							 </thead>
 
   							 <tfoot>	
   								 <tr>
   									 <th>Date</th>
   									 <th>Flow Cell ID</th>
   									 <th>Comment</th>
   									 <th>Flow Cell Owner</th>
   									 <th>Run Name</th>
   									 <th>Run Order</th>
									 <th>Ref Sequence</th>
									 <th>Ref Length</th>
   								 </tr>
   							 </tfoot>
   						 </table>
   					 </div>
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
	<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	
	<script>
	//jQuery(document).ready(function($) {
	//     $(".clickableRow").click(function() {
	//         window.document.location = $(this).attr("href");
	//      });
	//});
	</script>
	
    <!-- Page-Level Demo Scripts - Dashboard - Use for reference -->
		<script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>
		<script>
		$(document).ready(function() {
		    $('#example').dataTable( {
		        "processing": true,
		        "serverSide": true,
		        "sAjaxSource": "data_tables/data_table_prev_runs.php?prev=1"
		    } );
			$('#example tbody').on('click', 'tr', function () {
			        var name = $('td', this).eq(0).text();
			        //alert( name );
					$.post( "views/read_details.php?prev=1", { readname: name })
					  .done(function( data ) {
					    $("#read_details").html(data);
						$('html, body').animate({
						           'scrollTop':   $('#'+name).offset().top
						}, 1000);
						
					});
			    } );
			    
			    $(document).on('click', '#example tr', function(){
    					var tableData = $(this).children("td").map(function() {
       						 return $(this).text();
					    }).get();
						var url = "previous_runs.php?roi=" + $.trim(tableData[4]);
						window.location.href = url;
					});
		
		} );
	
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
