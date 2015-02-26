<?php

header('Content-Type: application/json');
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
		//echo "this bit is working!";
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	}
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		//Check if entry already exists in jsonstore table:
		$jsonjobname="avelen";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select ROUND(AVG(length(sequence))) as average_length, exp_script_purpose from basecalled_template inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_complement = "select ROUND(AVG(length(sequence))) as average_length, exp_script_purpose from basecalled_complement inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_2d = "select ROUND(AVG(length(sequence))) as average_length, exp_script_purpose from basecalled_2d inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		
		$resultarray;
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray[$row['exp_script_purpose']]['template']=$row['average_length'];
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$resultarray[$row['exp_script_purpose']]['complement']=$row['average_length'];
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$resultarray[$row['exp_script_purpose']]['2d']=$row['average_length'];
			}
		}
		
	
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring;
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				
				
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring ."{\n";
					$jsonstring = $jsonstring . "\"name\" : \"$key2\", \n";
					$jsonstring = $jsonstring . "\"data\": [";
					$jsonstring = $jsonstring . "$value2,";
					$jsonstring = $jsonstring . "]\n},\n";
				}
				
				//echo "},\n";
				
			}
			$jsonstring = $jsonstring . "]\n";
			
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
	
			
			
	$callback = $_GET['callback'];
	echo $callback.'('.$jsonstring.');';
	
		
	}
} else {
	echo "ERROR";
}
?>