<?php

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("config/db.php");

// load the login class
require_once("classes/Login.php");

// load the functions
require_once("includes/functions.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");
	?>


<!DOCTYPE html>
<html>
<?php include "includes/head.php";?>

<body>

    <div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            
			<?php 
			include 'navbar-header.php';
			?>
            <!-- /.navbar-top-links -->
			<?php include 'navbar-top-links.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
						
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Enable User Upload</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <body><strong>The buttons on this page are non functional.</strong><br>We are looking at ways of securely implementing this system for admin users. Currently this page serves to tell you which users are currently authorsied to upload data and those which can only view data. To modify user permissions you can use the scripts provided with the original installation documents for minoTour.<p><br>
			<body>These pages allow you to provide upload access for individual users to add data to the mySQL account associated with this installation of minoTour. <strong>Any admin user can enable others to upload data so exercise caution!</strong><br><br>
			<p>The following users are currently authorised to upload data to the database:</p>	
			<?php foreach (getallusers() as $user) {
				if (checkminup($user) > 0) {
					echo "<i>$user </i><button type='button' id='loading-example-btn-".$user."' data-loading-text='Revoking Access...' data-complete-text='Access Revoked' class='btn btn-alert btn-xs'>Revoke Access</button><br>";
				}
			} ?>
			<p><br>These users are not authorised to upload data to the database:</p>
			<?php foreach (getallusers() as $user) {
				if (checkminup($user) == 0) {
					echo "<i>$user </i><button type='button' id='loading-example-btn-".$user."' data-loading-text='Granting Access...' data-complete-text='Access Granted' class='btn btn-warning btn-xs'>Grant Access</button><br><br>";
				}
			} ?>
			
	
	    </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.min.js"></script>-->
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
	
	<script>
	<?php foreach (getallusers() as $user) {?>
		$("#loading-example-btn-<?php echo $user;?>").
		click(function () {
    	//alert (this);
    	//var btn = $(this);
        //btn.button('loading');
        //setTimeout(function () {
        //	btn.button('complete');
        //}, 1000);
	  });
		
	<?php } ?>

	</script>
    <!-- Page-Level Plugin Scripts - Dashboard -->
			    <script type="text/javascript" src="js/pnotify.custom.min.js"></script>
			    <script type="text/javascript">
				PNotify.prototype.options.styling = "fontawesome";
				</script>
    <script src="js/plugins/morris/raphael-2.1.0.min.js"></script>
    <script src="js/plugins/morris/morris.js"></script>

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
<?php 
} else {
	
	    // the user is not logged in. you can do whatever you want here.
	    // for demonstration purposes, we simply show the "you are not logged in" view.
	    include("views/not_logged_in.php");
	}
	
	?>
