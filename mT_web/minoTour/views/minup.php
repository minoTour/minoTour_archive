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
        						<?php include 'includes/run_check.php';?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Obtaining minUP to upload data.</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<?php  if (checkminup($_SESSION['user_name']) > 0){ 
				echo "You are authorised to upload data to this minoTour installation. All versions of the minup script are available to download from here. Please see the notes below.<br>";
				echo "<h4><u>minUP for Windows</u></h4>";
				echo "<body>The latest version of minUP for Windows can be downloaded below. This compressed folder includes a compiled version of the last aligner which in turn requires CYGWIN to be installed on your system (available from <a href='http://www.cygwin.com/' target='_blank'>www.cygwin.com</a>).<br><br> You should pay close attention to the enclosed readme file to ensure that you correctly configure the path to run minUP on Windows.</body><br><br>";
				echo "<body>This version of minUP can utilise a configuration file to bypass entering some of the standard parameters on the command line. You can download a copy of this configuration specific for your account below. This file only works with the Windows version of minUP.</body><br><br>";
				echo "<a href='minup/minup.v0.38W.zip' target='_blank'><i class='fa fa-file-code-o'></i> minUP for Windows</a><br><br>";
				echo "<h4><u>Windows minUP Configuration File</u></h4>";
				echo "<body>Click the icon below to download your personalised Windows minup configuration file. It should be saved in the folder containing the windows minup executable and you should ensure it is named minup_windows.config - some browsers may append .txt to the end of the file.</body><br><br>";
				echo "<a href='minup/minupwindowsconfig.php?user_name=" . $_SESSION['user_name'] . "' target='_blank'><i class='fa fa-file-code-o'></i> minUP Windows Config</a><br><br>";
				echo "<body>You are free to edit this file and the parameters within it can be overridden by the parameter settings on the command line. Do not distribute this file to others as it is specific to you. Within the file is a hashed out line containing your password for uploading data to the database.</body><br><br>";
				echo "<h4><u>minUP for Linux</u></h4>";
				echo "<body>The latest version of minUP for linux can be downloaded below. This is a python script and requires several dependencies - see the enclosed readme file for details.</body><br><br>";
				echo "<a href='minup/minup.v0.38.zip' target='_blank'><i class='fa fa-file-code-o'></i> minUP for Linux</a><br><br>";
				echo "<h4><u>Linux minUP Configuration File</u></h4>";
				echo "<body>Click the icon below to download your personalised minup configuration file. It should be saved in the same folder as your minup script and you should ensure it is called minup_posix.config - some browsers may append .txt to the end of the file.</body><br><br>";
				echo "<a href='minup/minupposixconfig.php?user_name=" . $_SESSION['user_name'] . "' target='_blank'><i class='fa fa-file-code-o'></i> minUP Linux Config</a><br><br>";
				echo "<body>You are free to edit this file and the parameters within it can be overridden by the parameter settings on the command line. Do not distribute this file to others as it is specific to you.</body><br><strong>The password to upload data will have been sent to you seperately - contact the system administrator if it is lost.</strong><br>";
				echo "<h4><u>Demo Data Set</u></h4>";
				echo "<body>To test your installation we have created a small sample of data from the recently released Loman Lab dataset (<a href='http://dx.doi.org/10.5524/100102' target='_blank'>http://dx.doi.org/10.5524/100102'</a>). This sample set consists of just the first 100 or so reads from the run. Decompress the demo_data_set.zip folder to a location on your machine. The folder structure is important here. metrichor returns files to a folder called ‘downloads’ and minup looks for this folder in any location you point it at - so keep the folder structure. We also include a copy of the reference genome in this dataset.</body><br><br>";
				echo "<a href='minup/demo_data_set.zip' target='_blank'><i class='fa fa-file-code-o'></i> Demo Data Set</a><br><br>";
			}else {
				echo "We're sorry, but your user account is not configured to allow data upload at this time. If you think you should be able to do so, please contact the system administrator for this installation.<br>";
			}; ?>	
	
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
