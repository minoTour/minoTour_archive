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
            
            <!-- /.navbar-header -->
			<?php include 'navbar-header.php' ?>
            <!-- /.navbar-top-links -->
			<?php include 'navbar-top-links.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
			<?php include 'includes/run_check.php';?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Manage Data - run: <?php echo cleanname($_SESSION['focusrun']); ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
				<h4>Database Management</h4>
				
				<br>
			 	<div id="messages"></div>
				<br>
				For speed purposes, the data analyses for each run are stored to a database table after the run is complete. This maximises speed for the website. These stored files are generated on the first viewing of a comlpleted run. These tables can be reset here.<br>
				It is also possible to delete all non essential data from the database to save space. Essentially this archives a run such that it can no longer be reprocessed without re-uploading the data to the website again. You should only archive data from the database if you understand what you are doing.<br>
				
				<br><br>
				<?php 
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
				if (!$mindb_connection->connect_errno) {
					$sqlcheck = "select * from jsonstore where name = 'do_not_delete' and json = '1';";
					$sqlchecksecurity = $mindb_connection->query($sqlcheck);
					if ($sqlchecksecurity->num_rows == 1){
						echo "<!-- Button trigger modal -->
				<button class='btn btn-warning disabled' data-toggle='modal' data-target='#resetmodal'>
				  <i class='fa fa-exclamation-triangle'></i> Reset Database Optimisations
				</button> This database has been archived, so cannot be reset. <br><br>
				<button id='archivebutton' class='btn btn-danger disabled' data-toggle='modal' data-target='#deletemodal'>
				  <i class='fa fa-exclamation-triangle'></i> Archive Database
				</button> This database has already been archived.
				";
					}else{
						//Check if entry already exists in jsonstore table:
						echo "<!-- Button trigger modal -->
				<button id = 'optobutton' class='btn btn-warning' data-toggle='modal' data-target='#resetmodal'>
				  <i class='fa fa-exclamation-triangle'></i> Reset Database Optimisations
				</button>
 
				<!-- Modal -->
				<div class='modal fade' id='resetmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
				  <div class='modal-dialog'>
				    <div class='modal-content'>
				      <div class='modal-header'>
				        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				        <h4 class='modal-title' id='myModalLabel'>Reset Database Optimisations</h4>
				      </div>
				      <div class='modal-body'>
				        <p>This action will reset the optimised values for this run in the database. This data can be regenerated on the fly as required.</p>
						<p>If you are sure you wish to do this, click reset below. Otherwise close this window.</p>
				      </div>
				      <div class='modal-footer'>
				        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
				        <button id='resetopt' type='button' class='btn btn-warning'>Reset</button>
				      </div>
				    </div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<br><br>
				<!-- Indicates a dangerous or potentially negative action -->
				<!-- Button trigger modal -->
								<button id='archivebutton' class='btn btn-danger' data-toggle='modal' data-target='#deletemodal'>
								  <i class='fa fa-exclamation-triangle'></i> Archive Database
								</button>
 
								<!-- Modal -->
								<div class='modal fade' id='deletemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog'>
								    <div class='modal-content'>
								      <div class='modal-header'>
								        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
								        <h4 class='modal-title' id='myModalLabel'>Archive Database</h4>
								      </div>
								      <div class='modal-body'>
								       <div id='archiveinfo'>
								        <p>This action will archive this database. It will remove all data used to calculate rates of sequencing and coverage data. After carrying out this process you will only be able to view previously optimised data on the website. You will currently still be able to download sequences.</p>
										<p><strong>The only way to undo this operation is to reupload data to the database.</strong></p>
										<p>If you are sure you wish to do this, click archive below. Otherwise close this window.</p>
								      </div>
								      <div id='archiveworking'>
								      <p class='text-center'>We're working to archive your database. Please be patient and don't navigate away from this page.</p>
								      <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
								      </div>
								      <div class='modal-footer'>
								        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
								        <button id='archiveopt' type='button' class='btn btn-warning'>Archive</button>
								      </div>
								    </div><!-- /.modal-content -->
								  </div><!-- /.modal-dialog -->
								</div><!-- /.modal -->
				
				";
					}		
				}
			
				?>
				
				
				
				
				
			
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
	<script src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	
	
	<script>
	    
	    $(function(){
	        $('#resetopt').on('click', function(e){
	        		            e.preventDefault(); // preventing default click action
	            $.ajax({
	                url: 'jsonencode/clearjson.php?prev=1',
	                success: function(data){
						//alert ('success');
	                    $('#resetmodal').modal('hide')
						//alert(data);
						$("#messages").html(data);
	                }, error: function(){
	                    alert('ajax failed');
	                },
	            })
				//alert ("button clicked");
	        })
	    })
	    $(function(){
	    	$('#archiveworking').hide();
	        $('#archiveopt').on('click', function(e){
	        	$('#archiveinfo').hide();
       		    $('#archiveworking').show();
       		    $('#archiveopt').addClass('disabled');
	            e.preventDefault(); // preventing default click action
	            $.ajax({
	                url: 'jsonencode/archive.php?prev=1',
	                success: function(data){
						//alert ('success');
	                    $('#deletemodal').modal('hide')
						//alert(data);
						$("#messages").html(data);
						$('#optobutton').addClass('disabled');
						$('#archivebutton').addClass('disabled');
	                }, error: function(){
	                    alert('ajax failed');
	                },
	            })
				//alert ("button clicked");
	        })
	    })
	   
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
