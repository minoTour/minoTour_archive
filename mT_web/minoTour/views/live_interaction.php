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
                    <h1 class="page-header">Live Control - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></h1>
                </div>
                

                <?php
	
		
	//Check if INTERACTION Table already exists:
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
		$query = "SHOW TABLES LIKE 'interaction';";
		$sql = $mindb_connection->query($query);
		$result = $sql->num_rows;
		//echo $query . "\n";
		//echo $result . "\n";
		
		if ($result >= 1){
			//echo "Table exists";
			//$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jsonjobname . "','".$jsonstring . "');";
			//$go = $mindb_connection->query($insertresult);
		}else{
			//echo "Table needs creating";
			$create_table =
			"CREATE TABLE `interaction` (
  `job_index` INT NOT NULL AUTO_INCREMENT,
  `instruction` MEDIUMTEXT NOT NULL,
  `target` MEDIUMTEXT NOT NULL,
  `param1` MEDIUMTEXT,
  `param2` MEDIUMTEXT,
 `complete` INT NOT NULL,
  PRIMARY KEY (`job_index`)
)
CHARACTER SET utf8;";
			$create_tbl = $mindb_connection->query($create_table);
			
			
				
		}
		if ($_SESSION['currentbarcode'] >= 1){
			//Check if BARCODE_CONTROL table already exists
			$query = "SHOW TABLES LIKE 'barcode_control';";
			$sql = $mindb_connection->query($query);
			$result = $sql->num_rows;
			//echo $query . "\n";
			//echo $result . "\n";
			
			if ($result >= 1){
				//echo "Table exists";
				//$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jsonjobname . "','".$jsonstring . "');";
				//$go = $mindb_connection->query($insertresult);
			}else{
				//echo "Table needs creating";
				$create_table =
				"CREATE TABLE `barcode_control` (
  `job_index` INT NOT NULL AUTO_INCREMENT,
  `barcodeid` MEDIUMTEXT NOT NULL,
 `complete` INT NOT NULL,
  PRIMARY KEY (`job_index`)
)
CHARACTER SET utf8;";
				$create_tbl = $mindb_connection->query($create_table);
				$barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12"];
				foreach ($barcodes as $barcode) {
					$sqlinsert = "insert into barcode_control (barcodeid,complete) values (\"" . $barcode .	"\",0);";
					//echo $sqlinsert;
					$sqlinsertexecute = $mindb_connection->query($sqlinsert);
			}

						
			
				
		}
		}

		
		
		

	
	
 
?>

                <!-- /.col-lg-12 -->
            </div>
            <br>
			 	<div id="messages"></div>
				<br>
            <div class="panel panel-danger">
  <div class="panel-heading">
    <h3 class="panel-title">Polite Warning!</h3>
  </div>
  <div class="panel-body">
    This page is in development to exploit both Run Until and ultimately Read Until features in the minION platform. We must emphasize that these features provide two way interaction between minoTour and your minION device. Thus you can stop or start runs remotely and interact with the minION device in other ways. This is an alpha service relying on the API that is in development by Oxford Nanopore. We, the developers of minoTour, <strong>take no responsibility for any loss of sequencing data</strong> as a consequence of your use of these features. <strong>They are used at your own risk</strong>.
  </div>
</div>
            
			
        
        As proof of principle we shall begin with a simple remote stop and start switch.
        
        <br><br>
				<!-- Indicates a dangerous or potentially negative action -->
				<!-- Button trigger modal -->
				<button id='stopminion' class='btn btn-danger' data-toggle='modal' data-target='#stopminionmodal'>
				  <i class='fa fa-stop'></i> Stop minION
				</button>
 
				<!-- Modal -->
				<div class='modal fade' id='stopminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
					<div class='modal-dialog'>
						<div class='modal-content'>
							<div class='modal-header'>
								<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
								<h4 class='modal-title' id='myModalLabel'>Stop your minION</h4>
							</div>
							<div class='modal-body'>
								<div id='stopminioninfo'>
									<p>This will attempt to stop your minION sequencer remotely. It should be possible to restart sequencing remotely but software crashes on the minION controlling device may cause problems. You should only stop your minION device remotely if you are certain you wish to do so and <strong> at your own risk</strong>.</p>
									
									<p>If you are sure you wish to do this, click 'Stop minION' below. Otherwise close this window.</p>
								</div>
								<div id='stopworking'>
									<p class='text-center'>We're working to stop your minION device. Please be patient and don't navigate away from this page.</p>
								    <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
								</div>
								<div class='modal-footer'>
									<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
								    <button id='stopnow' type='button' class='btn btn-danger'>Stop minION</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
        		</div>
        		
        <br><br>
				<!-- Indicates a dangerous or potentially negative action -->
				<!-- Button trigger modal -->
				<button id='startminion' class='btn btn-success' data-toggle='modal' data-target='#startminionmodal'>
				  <i class='fa fa-play'></i> Start minION
				</button>
 
				<!-- Modal -->
				<div class='modal fade' id='startminionmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
					<div class='modal-dialog'>
						<div class='modal-content'>
							<div class='modal-header'>
								<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
								<h4 class='modal-title' id='myModalLabel'>Start your minION</h4>
							</div>
							<div class='modal-body'>
								<div id='startminioninfo'>
									<p>This will attempt to restart your minION sequencer remotely.</p>
									
									<p>If you are sure you wish to do this, click 'Start minION' below. Otherwise close this window.</p>
								</div>
								<div id='startworking'>
									<p class='text-center'>We're working to restart your minION device. Please be patient and don't navigate away from this page.</p>
								    <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
								</div>
								<div class='modal-footer'>
									<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
								    <button id='startnow' type='button' class='btn btn-success'>Start minION</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
        		</div><br>
			<br>
		<?php if ($_SESSION['currentbarcode'] >= 1) {?>
<div class="panel panel-default">
						  <div class="panel-heading">
						    <h3 class="panel-title"><!-- Button trigger modal -->
			<button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#modalbarcode">
			 <i class="fa fa-info-circle"></i> Barcode Interaction
			</button>
			

			<!-- Modal -->
			<div class="modal fade" id="modalbarcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			        <h4 class="modal-title" id="myModalLabel">Barcoding Summary</h4>
			      </div>
			      <div class="modal-body">
			        Here you can attempt to control barcode coverage on the minION device with a 'proof of principle' demonstration of read unti. <strong>This system is not yet using true read until.</strong><br><br>Users can either set global thresholds for all barcodes or specify coverage on a barcode by barcode basis.
			        
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>
			      </div>
			    </div>
			  </div>
			</div>
						  </div>
						  <div id="barcoding">
						  <div class="panel-body">
									<div class="row">
									<div class = "col-lg-12">
									<strong>Set Global barcode coverage threshold.</strong><br>
									</div>
									</div>
									<div class="col-lg-12">
            Enter Desired Coverage Depth
    			<div class="input-group">
      				<input id="genbarcodecov" type="text" class="form-control">
      			  	<span class="input-group-btn">
        				<button class="btn btn-default" id="genbarcode_coverage" type="button">Set</button>
      			  	</span>
				</input>
				</div>
				</div>
				
				<div class="row">
									<div class = "col-lg-12">
									<br>
									<strong>Set coverage threshold/barcode.</strong><br>
									<br>
									</div>
									</div>
				<?php $barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12"];?>			
				<?php foreach ($barcodes as $barcode) {?>
				<div class="col-lg-12">
				<form class="form-inline">
            	<div class="form-group">
			    <label for="barcode_coverage_<?php echo $barcode; ?>"><?php echo $barcode; ?></label>
    			<input type="text" class="form-control" id="barcodecov<?php echo $barcode; ?>">
    			<span class="form-group-btn">
        				<button class="btn btn-default" id="barcode_coverage_<?php echo $barcode; ?>_button" type="button">Set</button>
      			  	</span>
      			
				</div>
            </div>
            </form>
            
				<?php } ?>
				
				

								</div>	
								
						  </div>
						</div>
						
					</div>
					<?php }; ?>
        </div>
        
        
        <!-- /#page-wrapper -->
		<!-- Checking for table existence -->
		
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
	<script src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	
			     
									
    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>


	<script>
	    $(function(){
	    	$('#stopworking').hide();
	        $('#stopnow').on('click', function(e){
	        	$('#stopminioninfo').hide();
       		    $('#stopworking').show();
       		    $('#stopnow').addClass('disabled');
	            e.preventDefault(); // preventing default click action
	            $.ajax({
	                url: 'jsonencode/interaction.php?prev=0&job=stopminion',
	                success: function(data){
						//alert ('success');
	                    $('#stopminionmodal').modal('hide')
						//alert(data);
						$("#messages").html(data);
						$('#stopminion').addClass('disabled');
						$('#startminion').removeClass('disabled');
	                }, error: function(){
	                    alert('ajax failed');
	                },
	            })
				//alert ("button clicked");
	        })
	    })
		$(function(){
	    	$('#startworking').hide();
	        $('#startnow').on('click', function(e){
	        	$('#startminioninfo').hide();
       		    $('#startworking').show();
       		    $('#startnow').addClass('disabled');
	            e.preventDefault(); // preventing default click action
	            $.ajax({
	                url: 'jsonencode/interaction.php?prev=0&job=startminion',
	                success: function(data){
						//alert ('success');
	                    $('#startminionmodal').modal('hide')
						//alert(data);
						$("#messages").html(data);
						$('#startminion').addClass('disabled');
						$('#stopminion').removeClass('disabled');
	                }, error: function(){
	                    alert('ajax failed');
	                },
	            })
				//alert ("button clicked");
	        })
	    })	   
	</script>

     <script>
        $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
        var auto_refresh = setInterval(function ()
            {
            $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
            //eval(document.getElementById("infodiv").innerHTML);
            }, 10000); // refresh every 5000 milliseconds
    </script>
    
    <?php $barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12"];?>			
				<?php foreach ($barcodes as $barcode) {?>
				<script>

            $(function(){
            $('#barcode_coverage_<?php echo $barcode; ?>_button').on('click', function(e){
                e.preventDefault(); // preventing default click action
                var idClicked = e.target.id;
                var idVal = $("#barcodecov<?php echo $barcode; ?>").val();
                //alert('were getting there ' + idClicked + ' is ' + idVal);
        		//alert ($("input:radio[name='coveragenoticeradio']:checked").val());
        		//var type = $("input:radio[name='coveragenoticeradio']:checked").val(); 
                 var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&reference=<?php echo $barcode; ?>&task=barcodecoverage&threshold='+idVal;
                //alert (monkey);
                $.ajax({
                    url: monkey,
                   // alert ('url'),                    
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
    </script>
				
				
				<?php };?>
    
    <script>

            $(function(){
            $('#genbarcode_coverage').on('click', function(e){
                e.preventDefault(); // preventing default click action
                var idClicked = e.target.id;
                var idVal = $("#genbarcodecov").val();
                //alert('were getting there ' + idClicked + ' is ' + idVal);
        		//alert ($("input:radio[name='coveragenoticeradio']:checked").val());
        		//var type = $("input:radio[name='coveragenoticeradio']:checked").val(); 
                 var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type=2D&task=genbarcodecoverage&threshold='+idVal;
                //alert (monkey);
                $.ajax({
                    url: monkey,
                   // alert ('url'),                    
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
    </script>

    
    
<?php include "includes/reporting.php";?>
</body>

</html>
