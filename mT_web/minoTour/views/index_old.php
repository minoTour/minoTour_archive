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
                    <h3 class="page-header">Overview</h3>
					
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<?php checkuserruns(); ?>
			
			<div class = "row">
				<div class="col-lg-12">
					<h4 class="page-header">News</h4>
					
					<div id="newstarget">To get news messages your installation of minoTour must be on a server which can connect to the outside world.<br></div>
				</div>
			</div>
			<div class = "row">
				<div class="col-lg-12">
					<h4 class="page-header">Important Notice:</h4>
					
					<div id="notice">minoTour is a platform in development. It is realeased as an 0.x version and may contain <strong>errors</strong> and <strong>bugs</strong>.<br><br>A rudimentary bug reporting system is included within this application. Please make full use of it.<br><br>In order for bugs and feature requests to be reported we are collecting information from you. The version checks above request information from our central website as to the current version of the tools to notify you if we have made a new release available.<br><br>Alongside this error reporting, we intend to collect usage statistics from you. Every time a new dataset is uploaded into minoTour we will log the username on our own servers along with the date and time. This will allow us to collect usage statistics to support the development of these tools.<br><br>We will <strong>not</strong> store any other information about your run, your run data or any of your personal information. </div>
				</div>
			</div>
			</div>
			
		     
			

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
        
		eval(document.getElementById("infodiv").innerHTML);
        var auto_refresh = setInterval(function ()
            {
            $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
            eval(document.getElementById("infodiv").innerHTML);
            }, 10000); // refresh every 5000 milliseconds
    </script>
	
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
