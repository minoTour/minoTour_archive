<?php


$db = $_GET["db"]; 
$jobtype = $_GET["job"];
$id = $db . "_" . $jobtype;

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
	if($_GET["prev"] == 1){
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	}
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		
		//$sql_template = "select refpos, count(*) as count from last_align_basecalled_template where refpos != \'null\' and (cigarclass = 7 or cigarclass = 8) group by refpos;";
		
		$sql_template = "SELECT refid FROM last_align_basecalled_template group by refid;";
		
		$template=$mindb_connection->query($sql_template);
		$array;
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$array[] = $row->refpos;
				echo "<div id='coverage'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Calculating Coverage Plots for " . $row->refpos . "</div>";
			}
		}
		
		foreach ($array as $value){
			echo "<div id='5primecoverage'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Calculating 5' Mapped Coverage " . $value . "</div>
			<div id='3primecoverage'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Calcularing 3' Mapped Coverage  " . $value . "</div>";
			
		}
		
} else {
	echo "ERROR";
}
?>