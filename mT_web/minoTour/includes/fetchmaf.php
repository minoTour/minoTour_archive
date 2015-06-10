<?php

$db = $_GET["db"]; 


$jobtype = $_GET["job"];
$id = $db . "_" . $jobtype;

$filename = $id . ".maf";

header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=$filename");
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
		$jobtype = strtolower($jobtype);
		$querytable = "basecalled_" . $jobtype;
		$lasttable = "last_align_maf_" . $querytable;
		
		//$sql = "SELECT * FROM $lasttable inner join config_general using (basename_id) inner join reference_seq_info using (refid) order by r_start limit 2;";
		$sql = "SELECT * FROM last_align_maf_basecalled_$jobtype 
inner join reference_seq_info using (refid) inner join basecalled_$jobtype using (basename_id) order by r_start;";
		
		//echo $sql . "\n";
		
		$queryresult=$mindb_connection->query($sql);
		if ($queryresult->num_rows >= 1) {
			foreach ($queryresult as $row) {
				$name1len = strlen($row{refname});
				$name2len = strlen($row{seqid});
				$diff = $name1len - $name2len;
				$name1comp = 1;
				$name2comp = 1;
				if ($diff > 0) {
					$name2comp+=$diff;	
				}elseif ($diff < 0) {
					$name1comp+=(0-$diff);	
				}
				
				$start1len = strlen($row{r_start});
				$start2len = strlen($row{q_start});
				$diff = $start1len - $start2len;
				$start1comp = 0;
				$start2comp = 0;
				if ($diff > 0) {
					$start2comp+=$diff;	
				}elseif ($diff < 0) {
					$start1comp+=(0-$diff);	
				}
				
				$alignlen1len = strlen($row{r_align_len});
				$alignlen2len = strlen($row{q_align_len});
				$diff = $alignlen1len - $alignlen2len;
				$alignlen1comp = 0;
				$alignlen2comp = 0;
				if ($diff > 0) {
					$alignlen2comp+=$diff;	
				}elseif ($diff < 0) {
					$alignlen1comp+=(0-$diff);	
				}
				
				$search  = array('F', 'R');
				$replace = array('+', '-');
				
				$len1len = strlen($row{reflen});
				$len2len = strlen(strlen($row{sequence}));
				$diff = $len1len - $len2len;
				$len1comp = 0;
				$len2comp = 0;
				if ($diff > 0) {
					$len2comp+=$diff;	
				}elseif ($diff < 0) {
					$len1comp+=(0-$diff);	
				}
					
				echo "a score=" . $row{score} . "\n";
				echo "s " . $row{refname} . str_repeat(" ", $name1comp) . str_repeat(" ", $start1comp) . $row{r_start} . " " . str_repeat(" ", $alignlen1comp).  $row{r_align_len} . " " . str_replace($search, $replace, $row{alignstrand}) . " " . str_repeat(" ", $len1comp). $row{reflen} . " " . $row{r_align_string} . "\n";
				echo "s " . $row{seqid} . str_repeat(" ", $name2comp) . str_repeat(" ", $start2comp) . $row{q_start} . " " . str_repeat(" ", $alignlen2comp). $row{q_align_len} . " " . str_replace($search, $replace, $row{alignstrand}) . " " . str_repeat(" ", $len2comp). strlen($row{sequence}) . " " . $row{q_align_string} . "\n";
				echo "\n";	
			}	
		}else{
			echo "Blank results";	
		}
	}
} else {
	echo "ERROR";
}
?>
