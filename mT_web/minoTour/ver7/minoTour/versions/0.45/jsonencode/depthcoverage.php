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
		$reflength = $_SESSION['focusreflength'];
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
		$reflength = $_SESSION['activereflength'];
	}
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		//Check if entry already exists in jsonstore table:
		$jsonjobname="depthcoverage";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		//$sql_template = "select refpos, count(*) as count from last_align_basecalled_template where refpos != \'null\' and (cigarclass = 7 or cigarclass = 8) group by refpos;";
		
		$sql_template = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_template where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x";
		$sql_complement = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_complement where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x";
		$sql_2d = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_2d where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x";
		
		
		$covarray;
		
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$perccov=($row['bases']/$reflength)*100;
				$covarray['template']['percov']=$perccov;
				$covarray['template']['avecover']=$row['coverage'];

			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$perccov=($row['bases']/$reflength)*100;
				$covarray['complement']['percov']=$perccov;
				$covarray['complement']['avecover']=$row['coverage'];
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$perccov=($row['bases']/$reflength)*100;
				$covarray['2d']['percov']=$perccov;
				$covarray['2d']['avecover']=$row['coverage'];

			}
		}
		
		$jsonstring;
		$jsonstring = $jsonstring . "[\n";
		foreach ($covarray as $type => $typeval){
			$jsonstring = $jsonstring .  "{\n";
			$jsonstring = $jsonstring .  "\"name\" : \"" . $type . "\", \n";
			$jsonstring = $jsonstring .  "\"data\": [";
			//var numvals = count($typeval);
			//echo "This is " . numvals . "\n";

			foreach ($typeval as $key => $value) {
				if ($key == "avecover") {
					$jsonstring = $jsonstring .  "$value";
				}
			}

			$jsonstring = $jsonstring .  "]\n";
			$jsonstring = $jsonstring .  "},\n";
			
		}
		$jsonstring = $jsonstring .  "]\n";
		
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