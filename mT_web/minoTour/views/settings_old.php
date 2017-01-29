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
						
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Settings - User: <?php echo $_SESSION['user_name']; ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
				<h4>Change Password</h4>
				
				<br>
			 	<div id="messages"></div>
				<br>
				<div class="row">
				<form id="passwordreset" class="col-md-4" role="form">
				  <div class="form-group">
				    <p class="help-block">To change your password enter your old password and new password twice below.</p>
				    <label for="exampleInputEmail1">Old Password</label>
				    <input type="password" class="form-control" id="old_password" >
				  </div>
				  <div class="form-group">
				    <label for="InputPassword1">New Password</label>
				    <input type="password" class="form-control" id="InputPassword1" >
				  </div>
				  <div class="form-group">
				    <label for="InputPassword2">Confirm Password</label>
				    <input type="password" class="form-control" id="InputPassword2" >
				  </div>
				  <button id="formgo" type="submit" class="btn btn-default">Submit</button>
				</form>
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
	    $(function(){
	        $('#formgo').on('click', function(e){
	            e.preventDefault(); // preventing default click action
				if ($( "#InputPassword1" ).val().length < 8){
					$("#messages").append( "<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><strong>Warning!</strong> New Password must be at least 8 characters long.</div>" );
				}else{
					if ($( "#InputPassword1" ).val() == $( "#InputPassword2" ).val()) {
						$.post( "jsonencode/psswd_change.php", { current: $("#old_password").val(), new: $( "#InputPassword1" ).val()  })
						  .done(function( data ) {
						    $("#messages").html(data);
						});
					}else {
						$("#messages").append( "<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><strong>Warning!</strong> New Passwords Do Not Match.</div>" );
					}
				}
				document.getElementById("passwordreset").reset();
	            
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
