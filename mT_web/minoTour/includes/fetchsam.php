<?php

$db = $_GET["db"]; 


$jobtype = $_GET["job"];
$id = $db . "_" . $jobtype;

$filename = $id . ".sam";

header("Content-type: text/plain; charset=utf-8");
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
		
		$sql2= "select refname,reflen from reference_seq_info;";
		
		$headerresult=$mindb_connection->query($sql2);
		if ($headerresult->num_rows >= 1) {
			foreach ($headerresult as $row) {
				echo "@SQ\t";
				echo "SN:" . $row{refname} . "\t";
				echo "LN:" . $row{reflen} . "\n";	
			}	
			echo "@PG\tID:bwa\n";
		}
		
		//$sql = "SELECT * FROM $lasttable inner join config_general using (basename_id) inner join reference_seq_info using (refid) order by r_start limit 2;";
		$sql = "SELECT * FROM align_sam_basecalled_$jobtype inner join basecalled_$jobtype using (basename_id) order by rname, pos;";
		
		//echo $sql . "\n";
		
		$queryresult=$mindb_connection->query($sql);
		if ($queryresult->num_rows >= 1) {
			foreach ($queryresult as $row) {
				echo $row{qname} . "\t";
				echo $row{flag} . "\t";
				echo $row{rname} ."\t";
				echo $row{pos} . "\t";
				echo $row{mapq} . "\t";
				echo $row{cigar} . "\t";
				echo $row{rnext} . "\t";
				echo $row{pnext} . "\t";
				echo $row{tlen} . "\t";
				echo $row{seq} . "\t";
				#$qualscore= substr($row['qual'], 1, -1);
				#$qualarray = str_split($qualscore);
				#foreach ($qualarray as $value){
				#	echo chr(ord($value)-31);
				#}
				echo substr($row{qual},1,-1);
				echo "\t";
				echo $row{n_m} . "\t";
				echo $row{m_d} . "\t";
				echo $row{a_s} . "\t";
				echo $row{x_s} . "\n";
			}	
		}else{
			echo "Blank results";	
		}
	}
} else {
	echo "ERROR";
}
?>
