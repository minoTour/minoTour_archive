<?php

//header('Content-Type: application/json');
// checking for minimum PHP version
/*if (version_compare(PHP_VERSION, '5.3.7', '<')) {
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
if($_GET["prev"] == 1){
	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
}else{
	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
}

	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		
		$sql_reads = "select count(*) as reads from basecalled_template;";
		$sql_time = "select start_time from basecalled_template  order by start_time desc limit 1;";
		
		$resultarray;
	
		$reads=$mindb_connection->query($sql_reads);
		$time=$mindb_connection->query($sql_time);
		
        if ($reads->num_rows == 1 && $time->num_rows ==1) {

            // get result row (as an object)
            $reads_result = $reads->fetch_object();
			$num_reads = $reads_result->reads;
			
			$time_result = $time->fetch_object();
			$totaltime = $time_result->start_time;
			echo $num_reads/($totaltime/60);
		} else {
			echo "0";
		}

		
	
		
	
		
	}
	//} else {
//	echo "ERROR";
//}
?>