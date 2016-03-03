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
                    <h2 class="page-header">Set Alerts  - run: <?php echo cleanname($_SESSION['active_run_name']); ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div id="messages"></div>
            <div class="row">
                <div class="col-lg-12">
			One of the benefits of a real time sequencing platform is interacting with the sequencer during the run. This page enables this.<br> You can browse around the website once these parameters are set and notifications will appear on any page. If you leave the website, notifications are only available to you via Twitter. You must set a twitter handle to receive messages at if you wish to receive real time notifications.
            <br>

            
                <!-- /.col-lg-12 -->
		<div class="row">
            <div class="col-lg-12">
            <h4>Coverage Depth</h4>
    			<div class="input-group">
      				<input id="foldchange" type="text" class="form-control">
      			  	<span class="input-group-btn">
        				<button class="btn btn-default" id="gen_coverage" type="button">Set</button>
      			  	</span>
				</input>
				</div><!-- /input-group -->

				<label class="radio-inline">
  					<input type="radio" name="coveragenoticeradio" id="inlineRadio1" value="All" checked="checked"> All
				</label>
				<label class="radio-inline">
					<input type="radio" name="coveragenoticeradio" id="inlineRadio2" value="Template"> Template
				</label>
				<label class="radio-inline">
  					<input type="radio" name="coveragenoticeradio" id="inlineRadio3" value="Complement"> Complement
				</label>
				<label class="radio-inline">
  					<input type="radio" name="coveragenoticeradio" id="inlineRadio4" value="2D"> 2D
				</label>
    			  		  	</div><!-- /.col-lg-6 -->
		</div>
		
		<div class="row">
			<div class="col-lg-12">
            As an alternative example: set an alert for every X bases sequenced (again with reference to the template). This alert is non-persistent - it disappears.
			<br>
            <h4>Base Notification (strongly suggest minimum setting of 100000)</h4>
    			<div class="input-group">
      				<input id="basenotification" type="text" class="form-control">
      			  	<span class="input-group-btn">
        				<button class="btn btn-default" id="base_notification" type="button">Set</button>
      			  	</span>
    			</div><!-- /input-group -->
    			<label class="radio-inline">
  					<input type="radio" name="basenoticeradio" id="inlineRadio1" value="All" checked="checked"> All
				</label>
				<label class="radio-inline">
					<input type="radio" name="basenoticeradio" id="inlineRadio2" value="Template"> Template
				</label>
				<label class="radio-inline">
  					<input type="radio" name="basenoticeradio" id="inlineRadio3" value="Complement"> Complement
				</label>
				<label class="radio-inline">
  					<input type="radio" name="basenoticeradio" id="inlineRadio4" value="2D"> 2D
				</label>
  		  	</div><!-- /.col-lg-6 -->

		</div>
		
		<?php if ($_SESSION['currentbarcode'] >= 1) {?>
		We are looking at barcodes here...
		<?php } ?>

				<br><Strong>Tweeting Features</strong><br>
					<?php if (isset($_SESSION['twittername'])) {
						echo "You have set a twitter handle to receive messages at - it is " . $_SESSION['twittername'] . ".<br>";
					}else { ?>
						You can have your alerts tweeted to you if you so wish. If you specify a username below, all the alerts you set above will be sent to your twitter handle from the minoTour twitter account (@minoTour_01 on a standard minoTour install. This may be changed depending on your site admins preferences). The only exception to this is if you set a base notification for a value of less than 500000 base pairs.<br>
			            <br><h4>Set Twitter Handle</h4>
			    			<div class="input-group">
								<input id="twitterhandle" type="text" class="form-control">
								<span class="input-group-btn">
			        				<button class="btn btn-default" id="twitter_handle" type="button">Set</button>
			      			  	</span>
			    			</div>
						<?php } ?>
					
					<!-- /input-group -->
		  		  	</div>
                </div>
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
	
	
	
    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>
    <script>

            $(function(){
            $('#gen_coverage').on('click', function(e){
                e.preventDefault(); // preventing default click action
                var idClicked = e.target.id;
                var idVal = $("#foldchange").val();
                //alert('were getting there ' + idClicked + ' is ' + idVal);
        		//alert ($("input:radio[name='coveragenoticeradio']:checked").val());
        		var type = $("input:radio[name='coveragenoticeradio']:checked").val(); 
                 var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type='+type+'&task=gencoverage&threshold='+idVal;
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
    <script>

            $(function(){
            $('#base_notification').on('click', function(e){
                e.preventDefault(); // preventing default click action
                var idClicked = e.target.id;
                var idVal = $("#basenotification").val()
                //alert('were getting there ' + idClicked + ' is ' + idVal);
        		var type = $("input:radio[name='basenoticeradio']:checked").val(); 
                 var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type='+type+'&task=basenotification&threshold='+idVal;
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
    <script>
	$(function(){
		$('#twitter_handle').on('click', function(e){
			e.preventDefault();
			var idClicked=e.target.id;
			var idVal = $('#twitterhandle').val();
			var monkey = 'jsonencode/tweetset.php?username='+idVal;
			//alert (monkey);
            $.ajax({
                url: monkey,
               // alert ('url'),                    
                success: function(data){
                    //alert ('success');
                    //$('#resetmodal').modal('hide')
                    //alert(data);
                    $("#messages").html(data);
                }, error: function(){
                    alert('ajax failed');
                },
            })
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

<?php include "includes/reporting.php";?>
</body>

</html>
