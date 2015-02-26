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
		
		$jsonjobname="coverage_" . $_GET['seqid'];
		
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		
		//$sql_template = "select refpos, count(*) as count from last_align_basecalled_template where refpos != \'null\' and (cigarclass = 7 or cigarclass = 8) group by refpos;";
		
		#$sql_template = "SELECT refpos, count(*) as count FROM last_align_basecalled_template where (cigarclass=7 or cigarclass=8) and refid = '" . $_GET['seqid'] . "' and covcount = 1 and refpos >= 0 and refpos <= 20000 group by refpos order by refpos;";
		$sql_template = "SELECT refpos, count(*) as count FROM last_align_basecalled_template where (cigarclass=7 or cigarclass=8) and refid = '" . $_GET['seqid'] . "' and covcount = 1 group by refpos order by refpos;";
		#$sql_complement = "SELECT refpos, count(*) as count FROM last_align_basecalled_complement where (cigarclass=7 or cigarclass=8) and refid = '" . $_GET['seqid'] . "' and covcount = 1 and refpos >= 0 and refpos <= 20000 group by refpos order by refpos;";
		$sql_complement = "SELECT refpos, count(*) as count FROM last_align_basecalled_complement where (cigarclass=7 or cigarclass=8) and refid = '" . $_GET['seqid'] . "' and covcount = 1  group by refpos order by refpos;";
		#$sql_2d = "SELECT refpos, count(*) as count FROM last_align_basecalled_2d where (cigarclass=7 or cigarclass=8) and refid = '" . $_GET['seqid'] . "' and covcount = 1 and refpos >= 0 and refpos <= 20000 group by refpos order by refpos;";
		$sql_2d = "SELECT refpos, count(*) as count FROM last_align_basecalled_2d where (cigarclass=7 or cigarclass=8) and refid = '" . $_GET['seqid'] . "' and covcount = 1 group by refpos order by refpos;";
		
		
		$covarray;
		
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$covarray['template'][$row['refpos']]=$row['count'];

			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$covarray['complement'][$row['refpos']]=$row['count'];

			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$covarray['2d'][$row['refpos']]=$row['count'];

			}
		}
		
		//echo $sql_template . "\n";
		//Count the number of entries...
		$countarray;
		foreach ($covarray as $type => $typeval){
			$mycount=0;
			foreach ($typeval as $key => $value) {
				$mycount++;
			}
			$valtosplit = strval(floor($mycount/1000));
			//echo "we will take every $valtosplit read\n";
		
			if ($valtosplit < 1) {
				$valtosplit = 1;
			}
			$countarray[$type]=$valtosplit;
		}
		
		
		//echo "there are $mycount entries...\n";
		
//		echo "$valtosplit";
		//var_dump($countarray);
		//echo json_encode($resultarray);
		$jsonstring;
		$jsonstring = $jsonstring . "[\n";
		foreach ($covarray as $type => $typeval){
			$jsonstring = $jsonstring . "{\n";
			$jsonstring = $jsonstring .  "\"name\" : \"" . $type . "\", \n";
			$jsonstring = $jsonstring .  "\"data\": [";
			//var numvals = count($typeval);
			//echo "This is " . numvals . "\n";
			ksort($typeval);
			$i = 0;
			$j = 0;
			$k = 0;
			foreach ($typeval as $key => $value) {
				$i = $i+$key;
				$j = $j+$value;
				$k++;
				if ($k==$countarray[$type]){
					$i = $i/$k;
					$j = $j/$k;
					$jsonstring = $jsonstring .  "[$i,$j],\n";
					$i =0;
					$j =0;
					$k=0;
				}
				//echo "[$key,$value],\n";
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