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

<p>

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
                echo "<h4><u>minUP for Windows 0.6</u></h4>";
                echo "<p>The latest version of minUP for Windows can be downloaded below. This compressed folder includes a compiled version of the last and BWA aligners which in turn require CYGWIN to be installed on your system (available from <a href='http://www.cygwin.com/' target='_blank'>www.cygwin.com</a>).<br><br> This version also allows you to use BWA and additinal alignment options will be introduced in the near future.<br><br> A new feature for minUP version 0.6 is a GUI - a graphical user interface - to simplify data upload.<br><br> minUP 0.6 also enables remote control facilities for your minION. This is EXPERIMENTAL now and more information will be provided soon.<br>Finally, the configuration file is now packaged with this version automagically.<br><br>";
                echo "<a href='https://github.com/minoTour/winminUP/archive/master.zip' target='_blank'><i class='fa fa-file-code-o'></i> minUP for Windows</a><br><br>";

                echo "This version of minUP utilises a configuration file to bypass entering some of the standard parameters on the command line or in the GUI. You can download custom versions of these files below. Copy both files into the minUP folder.<br><br>";

                echo "<h4><u>Windows minUP Configuration Files</u></h4>";
				echo "This file is provided for backwards compatability only. Click the icon below to download your personalised Windows minup configuration file. It should be saved in the folder containing the windows minup executable and you should ensure it is named minup_windows.config - some browsers may append .txt to the end of the file.<br><br>";
                echo "<a href='minup/GUIconfig.php?user_name=" . $_SESSION['user_name'] . "' target='_blank'><i class='fa fa-file-code-o'></i> minUP Gui Config</a><br><br>";
				echo "<a href='minup/minupwindowsconfig.php?user_name=" . $_SESSION['user_name'] . "' target='_blank'><i class='fa fa-file-code-o'></i> minUP Windows Config</a><br><br>";
				echo "<p>You are free to edit this file and the parameters within it can be overridden by the parameter settings on the command line. Do not distribute this file to others as it is specific to you. Within the file is a hashed out line containing your password for uploading data to the database.</p><br><br>";
				echo "<h4><u>minUP for Linux</u></h4>";
				echo "<p>The latest version of minUP for linux can be downloaded below. This is a python script and requires several dependencies. This includes the option to run BWA or LAST. It also provides remote control facilities for your minION. This is EXPERIMENTAL now and more information will be provided soon.</p>";
				echo "<a href='https://github.com/minoTour/linminUP/archive/master.zip' target='_blank'><i class='fa fa-file-code-o'></i> minUP for Linux</a><br><br>";
				echo "<h4><u>Linux minUP Configuration File</u></h4>";
				echo "<p>Click the icon below to download your personalised minup configuration file. It should be saved in the same folder as your minup script and you should ensure it is called minup_posix.config - some browsers may append .txt to the end of the file.</p><br><br>";
				echo "<a href='minup/minupposixconfig2.php?user_name=" . $_SESSION['user_name'] . "' target='_blank'><i class='fa fa-file-code-o'></i> minUP Linux Config</a><br><br>";
				echo "<p>You are free to edit this file and the parameters within it can be overridden by the parameter settings on the command line. Do not distribute this file to others as it is specific to you.</p><br><strong>The password to upload data will have been sent to you seperately - contact the system administrator if it is lost.</strong><br>";
				echo "<h4><u>Demo Data Set</u></h4>";
				echo "<p>To test your installation we have created a small sample of data from the released Loman Lab dataset (<a href='http://dx.doi.org/10.5524/100102' target='_blank'>http://dx.doi.org/10.5524/100102'</a>). This sample set consists of just the first 100 or so reads from the run and some newer features of minoTour are not supported by the older dataset. Decompress the demo_data_set.zip folder to a location on your machine. The folder structure is important here. metrichor returns files to a folder called ‘downloads’ and minup looks for this folder in any location you point it at - so keep the folder structure. We also include a copy of the reference genome in this dataset.</p><br><br>";
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
</p>

</html>
<?php
} else {

	    // the user is not logged in. you can do whatever you want here.
	    // for demonstration purposes, we simply show the "you are not logged in" view.
	    include("views/not_logged_in.php");
	}

	?>
