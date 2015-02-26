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
		
		$sql_template = "select seqid,MIN(seqpos) as seqpos,refpos from last_align_template where cigarclass = 7 and alignstrand=\"forward\"  group by seqid;";
		$sql_template_rev = "select seqid,MIN(seqpos) as seqpos,refpos from last_align_template where cigarclass = 7 and alignstrand=\"reverse\"  group by seqid;";
		$resultarray;
	
		$template=$mindb_connection->query($sql_template);
		$template_rev=$mindb_connection->query($sql_template_rev);
		
		if ($template->num_rows >= 1){
			$count=1;
			foreach ($template as $row) {
				$resultarray['template_forwards'][$count][$row['refpos']]=$row['seqpos'];
				$count++;
			}
		}
		if ($template_rev->num_rows >= 1){
			$count=1;
			foreach ($template_rev as $row) {
				$resultarray['template_reverse'][$count][$row['refpos']]=$row['seqpos'];
				$count++;
			}
		}
		
		//var_dump($template);
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		echo "[\n";
			foreach ($resultarray as $type => $typeval){
				echo "{\n";
				echo "\"name\" : \"$type\", \n";
				echo "\"data\": [";
				foreach ($typeval as $index) {
					foreach ($index as $key => $value) {
						echo "[$key,$value],\n";
					}
				}
				echo "]\n";
				echo "},\n";
				
			}
			echo "]\n";
		
	}
} else {
	echo "ERROR";
}
?>