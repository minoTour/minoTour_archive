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
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<!--
Import the header.
-->
<?php
include 'includes/head-new.php';
?>
  <!--
  BODY TAG OPTIONS:
  =================
  Apply one or more of the following classes to get the
  desired effect
  |---------------------------------------------------------|
  | SKINS         | skin-blue                               |
  |               | skin-black                              |
  |               | skin-purple                             |
  |               | skin-yellow                             |
  |               | skin-red                                |
  |               | skin-green                              |
  |---------------------------------------------------------|
  |LAYOUT OPTIONS | fixed                                   |
  |               | layout-boxed                            |
  |               | layout-top-nav                          |
  |               | sidebar-collapse                        |
  |               | sidebar-mini                            |
  |---------------------------------------------------------|
  -->
  <body class="hold-transition skin-blue sidebar-mini fixed">
    <div class="wrapper">

        <!--Import the header-->
        <?php
        include 'navbar-header-new.php';
        ?>

        <!--Import the left hand navigation-->
        <?php
        include 'navbar-top-links-new.php';
        #include 'test.php';
        ?>


      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>
            minUP
            <small> - uploading data to minoTour.</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-cloud-upload"></i> minUP Scripts</a></li>
            <li class="active">Here</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            <div class="box">
            <div class="box-header">
              <h3 class="box-title">Getting Hold Of minUP</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <?php  if (checkminup($_SESSION['user_name']) > 0){
    				echo "You are authorised to upload data to this minoTour installation. All versions of the minup script are available to download from here. Please see the notes below.<br>";
                    echo "<h4><u>minUP for Windows 0.67</u></h4>";
                    echo "<p>The latest version of minUP for Windows can be downloaded below. Please note that you must download either a windows 7 or a windows 10 version now. We are trying to rectify this asap. This compressed folder includes a compiled version of the last and BWA aligners which in turn require CYGWIN to be installed on your system (available from <a href='http://www.cygwin.com/' target='_blank'>www.cygwin.com</a>).<br><br> This version also allows you to use BWA and additinal alignment options will be introduced in the near future.<br><br> A new feature for minUP version 0.6 is a GUI - a graphical user interface - to simplify data upload.<br><br> minUP 0.67 also enables remote control facilities for your minION although this is somewhat experimental! <br><br>";

                    echo "<a href='https://github.com/minoTour/winminUP/archive/master.zip' target='_blank'><i class='fa fa-file-code-o'></i> minUP for Windows 10</a><br><br>";
                    echo "<a href='https://github.com/minoTour/winminUPwin7/archive/master.zip' target='_blank'><i class='fa fa-file-code-o'></i> minUP for Windows 7/8</a><br><br>";

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
    </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>



         <script>


             $.getJSON('http://www.nottingham.ac.uk/~plzloose/minoTourhome/message.php?callback=?', function(result) {

                       $.each(result, function(key,value){
                          //checking version info.
                          if (key == 'version'){
                              if (value == '<?php echo $_SESSION['minotourversion'];?>'){
                                  $('#newstarget').html("You are running the most recent version of minoTour - version "+value+".<br>");
                              }else if (value < '<?php echo $_SESSION['minotourversion'];?>'){
                                  $('#newstarget').html("You appear to be in the fortunate position of running a future version of the minoTour web application "+value+". If you have modified the code yourself - great. If not then there might be an issue somewhere!.<br>");
                              }else if (value > '<?php echo $_SESSION['minotourversion'];?>'){
                                  $('#newstarget').html("You are running an outdated version of the minoTour web application. The most recent version of minoTour is version "+value+".<br>"+"Instructions for upgrading will be posted below.<br>");
                              }


                          }else if (key.substring(0, 7) == 'message') {
                              $('#newstarget').append(value + "<br>");
                            }
                       });
                     });

         </script>

  </body>
</html>
<?php
} else {

	    // the user is not logged in. you can do whatever you want here.
	    // for demonstration purposes, we simply show the "you are not logged in" view.
	    include("views/not_logged_in.php");
	}

	?>
