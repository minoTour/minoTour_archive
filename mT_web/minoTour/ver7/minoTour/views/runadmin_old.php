<?php
// load the functions
require_once("includes/functions.php");

?>
<!DOCTYPE html>
<html>

<?php include "includes/head.php";?>

<body>
    <?php $memcache = new Memcache;
    #$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT) or die ("Memcached Failure");
    $cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);
    ?>
    <div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">

            <!-- /.navbar-header -->
			<?php include 'navbar-header.php' ?>
            <!-- /.navbar-top-links -->
			<?php include 'navbar-top-links.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Run Administration: <?php echo cleanname($_SESSION['active_run_name']); ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
				<h4>Basic Run Control</h4>
                <br>
			 	<div id="messages"></div>
				<br>
                <p> In the future a number of basic tasks will be able to be completed here.</p>

                <p> Currently you can use this page to switch a run from active to inactive. You shouldn't normally need to do this as minUP will automatically switch a run to inactive when it is complete. This catches the odd use case when this process fails for some reason  <i class="fa fa-smile-o"></i>.</p>
                <button id='resetcache' class='btn btn-warning' data-toggle='modal' data-target='#resetcachemodal'>
				    <i class='fa fa-exclamation-triangle'></i> Reset Cache
				</button>
                <!-- Button trigger modal -->
				<button id='archivebutton' class='btn btn-warning' data-toggle='modal' data-target='#deletemodal'>
				    <i class='fa fa-exclamation-triangle'></i> Inactivate Run
				</button>
                <br><br>
                <?php echo '<pre>';
                //echo "Hello";
                $list = array();
    $allSlabs = $memcache->getExtendedStats('slabs');
    $items = $memcache->getExtendedStats('items');
    foreach($allSlabs as $server => $slabs) {
        foreach($slabs AS $slabId => $slabMeta) {
            $cdump = $memcache->getExtendedStats('cachedump',(int)$slabId);
            foreach($cdump AS $keys => $arrVal) {
                if (!is_array($arrVal)) continue;
                foreach($arrVal AS $k => $v) {
                    if (strpos($k,$_SESSION['active_run_name']) !== false) {
                        echo $k .'<br>';
                    }
                }
            }
        }
    }
   echo '</pre>';



   ?>
   <!-- Modal -->
   <div class='modal fade' id='resetcachemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
       <div class='modal-dialog'>
           <div class='modal-content'>
               <div class='modal-header'>
                   <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                   <h4 class='modal-title' id='myModalLabel'>Reset Cache</h4>
               </div>
                   <div class='modal-body'>
                       <div id='resetcache'>
                           <p>This action will reset the data cache. If you have started reuploading a run and the data looks wrong - try hitting this button!</p>
                       </div>
                       <div id='resetcacheworking'>
                           <p class='text-center'>We're working to reset the cache. Please be patient and don't navigate away from this page.</p>
                           <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
                       </div>
                       <div class='modal-footer'>
                           <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                           <button id='resetcacheopt' type='button' class='btn btn-warning'>Reset Cache</button>
                       </div>
                   </div><!-- /.modal-content -->
               </div><!-- /.modal-dialog -->
           </div><!-- /.modal -->
       </div>
   </div>
				<!-- Modal -->
				<div class='modal fade' id='deletemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
				    <div class='modal-dialog'>
					    <div class='modal-content'>
					        <div class='modal-header'>
							    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
							    <h4 class='modal-title' id='myModalLabel'>Inactivate Run</h4>
							</div>
								<div class='modal-body'>
								    <div id='archiveinfo'>
								        <p>This action will switch the current live run to an inactive run. It will then appear under the previosu runs collection.</p>
										<p><strong>If data is currently being uploaded to minoTour for this run, the run will not revert back to being an actie run.</strong></p>
										<p>To try and finish this run, first check minUP isn't running or inactivate it by typing ctrl-c. If this does not switch the run from active to inactive then use the inactivate run button below.</p>
								    </div>
								    <div id='archiveworking'>
								        <p class='text-center'>We're working to archive your database. Please be patient and don't navigate away from this page.</p>
								        <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
								    </div>
								    <div class='modal-footer'>
								        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
								        <button id='inactivateopt' type='button' class='btn btn-warning'>Inactivate</button>
								    </div>
							    </div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /.modal -->


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
	    	$('#resetcacheworking').hide();
	        $('#resetcacheopt').on('click', function(e){
	        	$('#resetcache').hide();
       		    $('#resetcacheworking').show();
       		    $('#resetcacheopt').addClass('disabled');
	            e.preventDefault(); // preventing default click action
	            $.ajax({
	                url: 'jsonencode/resetcache.php',
	                success: function(data){
						//alert ('success');
	                    $('#resetcachemodal').modal('hide')
						//alert(data);
						$("#messages").html(data);
						//$('#optobutton').addClass('disabled');
						//$('#archivebutton').addClass('disabled');
                        var delay = 3000; //Your delay in milliseconds
                        URL="index.php";
                        setTimeout(function(){ window.location = URL; }, delay);
	                }, error: function(){
	                    alert('ajax failed');
	                },
	            })
				//alert ("button clicked");
	        })
	    })
        $(function(){
	    	$('#archiveworking').hide();
	        $('#inactivateopt').on('click', function(e){
	        	$('#archiveinfo').hide();
       		    $('#archiveworking').show();
       		    $('#inactivateopt').addClass('disabled');
	            e.preventDefault(); // preventing default click action
	            $.ajax({
	                url: 'jsonencode/inactivate.php',
	                success: function(data){
						//alert ('success');
	                    $('#deletemodal').modal('hide')
						//alert(data);
						$("#messages").html(data);
						//$('#optobutton').addClass('disabled');
						$('#archivebutton').addClass('disabled');
                        var delay = 3000; //Your delay in milliseconds
                        URL="index.php";
                        setTimeout(function(){ window.location = URL; }, delay);
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
