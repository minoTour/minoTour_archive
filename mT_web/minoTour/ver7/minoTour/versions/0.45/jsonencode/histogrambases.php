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
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	}

	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		//Check if entry already exists in jsonstore table:
		$jsonjobname="histogrambases";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			
				
		
			$sql_template_query = "SELECT (FLOOR(length(basecalled_template.sequence)/1000)*1000) as bucket, sum(length(basecalled_template.sequence)) as tempBASES from basecalled_template group by bucket;";
			
			$sql_complement_query = "SELECT (FLOOR(length(basecalled_complement.sequence)/1000)*1000) as bucket, sum(length(basecalled_complement.sequence)) as compBASES from basecalled_complement group by bucket;";
			
			$sql_2d_query = "SELECT (FLOOR(length(basecalled_2d.sequence)/1000)*1000) as bucket, sum(length(basecalled_2d.sequence)) as d2BASES from basecalled_2d group by bucket;";
		
			$sql_template_execute=$mindb_connection->query($sql_template_query);
			$sql_complement_execute=$mindb_connection->query($sql_complement_query);
			$sql_2d_execute=$mindb_connection->query($sql_2d_query);
			
			$category2 = array();
			$arraytemplate = array();
			$arraycomplement = array();
			$array2d = array();
			
			if ($sql_template_execute->num_rows >= 1) {
				foreach ($sql_template_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$arraytemplate[$row['bucket']]=$row['tempBASES'];
				}	
			}
			if ($sql_complement_execute->num_rows >= 1) {
				foreach ($sql_complement_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$arraycomplement[$row['bucket']]=$row['compBASES'];
				}	
			}
			if ($sql_2d_execute->num_rows >= 1) {
				foreach ($sql_2d_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$array2d[$row['bucket']]=$row['d2BASES'];
				}	
			}
			asort($category2);
			//var_dump($category2);
			
			
		$category = array();
		$category['name'] = 'Size';

		$series1 = array();
		$series1['name'] = 'Template';

		$series2 = array();
		$series2['name'] = 'Complement';

		$series3 = array();
		$series3['name'] = '2d';
		
			foreach ($category2 as $bucket) {
				$category['data'][]=$bucket;
				if (array_key_exists($bucket, $arraytemplate)) {
					$series1['data'][]=$arraytemplate[$bucket];
				}else{
					$series1['data'][]=0;	
				}
				if (array_key_exists($bucket, $arraycomplement)) {
					$series2['data'][]=$arraycomplement[$bucket];
				}else{
					$series2['data'][]=0;	
				}
				if (array_key_exists($bucket, $array2d)) {
					$series3['data'][]=$array2d[$bucket];
				}else{
					$series3['data'][]=0;	
				}
				
			}
		
		//if ($sql_execute->num_rows >=1) {
		//	foreach ($sql_execute as $row){
		//		$category['data'][]= $row['bucket'];
		//	    $series1['data'][] = $row['tempCOUNT'];
		//	    $series2['data'][] = $row['compCOUNT'];
		//	    $series3['data'][] = $row['seq2dCOUNT'];   
		//	}
		//}
		$result = array();
		array_push($result,$category);
		array_push($result,$series1);
		array_push($result,$series2);
		array_push($result,$series3);

		$jsonstring = json_encode($result, JSON_NUMERIC_CHECK);
		//$jsonstring = json_encode($result);
		
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
	
			
			
	$callback = $_GET['callback'];
	echo $callback.'('.$jsonstring.');';
	//echo $jsonstring;
		
	}
} else {
	echo "ERROR";
}
1
?>