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
    //As user is logged in, we can now look at the memcache to retrieve data from here and so reduce the load on the mySQL server
	// Connection creation
	$memcache = new Memcache;
	#$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT) or die ("Memcached Failure");
	$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);
	
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
	if($_GET["prev"] == 1){
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
		$currun = $_SESSION['focusrun'];
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
		$currun = $_SESSION['active_run_name'];
	}
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		
		$jsonjobname="3primecoverage_" . $_GET['seqid'] ;
		$checkvar = $currrun . $jsonjobname;
		$jsonstring = $memcache->get("$checkvar");
		if($jsonstring === false){	
			$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
			$checking=$mindb_connection->query($checkrow);
			if ($checking->num_rows ==1){
				//echo "We have already run this!";
				foreach ($checking as $row){
					$jsonstring = $row['json'];
				}
			} else {
			
			//$sql_template = "select refpos, count(*) as count from last_align_basecalled_template where refpos != \'null\' and (cigarclass = 7 or cigarclass = 8) group by refpos;";
		
			#$sql_template = "SELECT alignstrand,count(*) as count,refpos FROM last_align_basecalled_template_3prime where refid = '" . $_GET['seqid'] . "' and refpos >= 0 and refpos <= 20000 group by refpos;";
			$sql_template = "SELECT alignstrand,count(*) as count,refpos FROM last_align_basecalled_template_3prime where refid = '" . $_GET['seqid'] . "' group by refpos;";
			//echo $sql_template;
		
			$minposarray;
			
			$template=$mindb_connection->query($sql_template);
			
			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$minposarray[$row['alignstrand']][$row['refpos']]=$row['count'];
	
				}
			}
			
			
			
			//var_dump($minposarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($minposarray as $type => $typeval){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"" . $type . "\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				//var numvals = count($typeval);
				//echo "This is " . numvals . "\n";
				ksort($typeval);
				foreach ($typeval as $key => $value) {
					$jsonstring = $jsonstring . "[$key,$value],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring . "]\n";
			//var_dump($minposarray);
			//echo json_encode($resultarray);
			
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}

	// cache for 2 minute as we want yield to update semi-regularly...
	    $memcache->set("$checkvar", $jsonstring, 0, 120);
	}else {
		//echo "Using memcached!";
	}	
		
	$callback = $_GET['callback'];
	echo $callback.'('.$jsonstring.');';
		
	}
} else {
	echo "ERROR";
}
?>