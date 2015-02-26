<?php
ini_set('max_execution_time', 300);
//header('Content-Type: application/json');
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("../config/db.php");

// load the login class
require_once("../classes/Login.php");

// load the functions
require_once("../includes/functions.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
    //BY DEFINITION WORKING WITH ACTIVE DATABASE HERE
	
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		if (isset ($_GET['task'])){
			$name = "'".$_GET['task']."'";
		}else{
			$name = "'FAILED'";
		}
		if (isset ($_GET['reference'])) {
			$reference = "'".$_GET['reference']."'";	
		}else{
			$reference = "null";
		}
		if (isset ($_GET['threshold'])) {
			$threshold = $_GET['threshold'];	
		}else{
			$threshold = "null";
		}
		if (isset ($_GET['start'])) {
			$start = $_GET['start'];	
		}else{
			$start = "null";
		}
		if (isset ($_GET['end'])) {
			$end = $_GET['end'];	
		}else{
			$end= "null";
		}
		$sqlinsert = "insert into alerts (name,reference,threshold,start,end,complete) values (" . $name .	"," . $reference .	"," . $threshold .	"," . $start .	"," . $end .	",0);";
		//echo $sqlinsert;
		$sqlinsertexecture = $mindb_connection->query($sqlinsert);
		echo "<div class='alert alert-success alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>  <strong>Success!</strong> An alert has been set for the following run: " .cleanname($_SESSION['active_run_name']) . "<br> The alert is for $name </div>";
	}else{
		echo "<div class='alert alert-danger alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>  <strong>Failed!</strong> An alert has not been set. You should not normally see this message so a really unexpected error has occured.</div>";
	}
		
	
	
} else {
	echo "ERROR";
}
?>