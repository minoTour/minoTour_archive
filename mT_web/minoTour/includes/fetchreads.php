<?php
//echo "test";

$db = $_GET["db"]; 


$jobtype = $_GET["job"];
$id = $db . "_" . $jobtype;

if (isset ($_GET["code"])){
	$filename = $db . "_" . $_GET["code"] . "_" . $_GET["job"] . "-" . $_GET["type"];	
}elseif (isset ($_GET["type"])) {
	if ($_GET["type"] == "histogram") {
		$filename = $db . "_" . $_GET["type"] . "_" . $_GET["length"] . "_" . ($_GET["length"] + 1000) . "_" . $_GET["job"] . ".fasta";
	}else {
		$filename = $id . "." . $_GET["type"];
	}
}else{
	$filename = $id . ".fasta";
}

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
		$minupver = $_SESSION['focus_minup'];
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
		$minupver = $_SESSION['active_minup'];
	}
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		$jobtype = strtolower($jobtype);
		
		if (isset ($_GET["code"])) {
			
			$querytable = "basecalled_" . $jobtype;
			$lasttable = "last_align_" . $querytable . "_5prime";
			
			if ($_GET["align"] == 1) {
				$sql = "select seqid,sequence, qual from $querytable inner join barcode_assignment using (basename_id) inner join $lasttable using (basename_id) where barcode_arrangement = \"". $_GET['code'] ."\";";
			}else {
				$sql = "select seqid,sequence, qual from $querytable inner join barcode_assignment using (basename_id) where barcode_arrangement = \"". $_GET['code'] ."\";";
				//echo $sql;
			}
			$queryresult=$mindb_connection->query($sql);
			if ($_GET['type'] == "fastq") {
				if ($queryresult->num_rows >= 1){
					foreach ($queryresult as $row) {
						echo "@" . $row['seqid'] . "\n";
						echo $row['sequence'] . "\n";
						echo "+\n";
						$qualscore= substr($row['qual'], 1, -1);
						$qualarray = str_split($qualscore);
						foreach ($qualarray as $value){
							if ($minupver < 0.5){ 
								echo chr(ord($value)-31);
							}else{
								echo chr(ord($value));
							}
						}
						print "\n";
					}
				}
			}else{
				if ($queryresult->num_rows >= 1){
					foreach ($queryresult as $row) {
						echo ">" . $row['seqid'] . "\n";
						echo $row['sequence'] . "\n";
					}
				}	
			}
			exit;	
		}
		if ($_GET["type"] =="histogram"){
			$querytable = "basecalled_" . $jobtype;
			$length_start = $_GET["length"];
			$length_end = intval($_GET["length"])+1000;
			$sql = "select seqid, sequence from $querytable where length(sequence) >= $length_start and length(sequence) <= $length_end;";
			//echo $sql;
			$queryresult=$mindb_connection->query($sql);
			if ($queryresult->num_rows >= 1){
				foreach ($queryresult as $row) {
					echo ">" . $row['seqid'] . "\n";
					echo $row['sequence'] . "\n";
				}
			}
			exit;
		}
		
		if ($_GET["type"] == "fastq"){
			$querytable = "basecalled_" . $jobtype;
			$lasttable = "last_align_" . $querytable . "_5prime";
			if ($_GET["align"] == 1) {
				$sql = "select seqid, sequence, qual,refname from $querytable inner join $lasttable using (basename_id) inner join reference_seq_info using (refid);";
				//echo $sql;
			}elseif ($_GET["unalign"] == 1) {
				$sql = "select seqid,sequence, qual from $querytable where basename_id not in (select basename_id from $lasttable);";
			}elseif (isset($_GET["readname"])){
				$sql = "select seqid,sequence, qual from $querytable where basename_id = '" . $_GET["readname"] . "';";
				//echo $sql;
			}else {
				$sql = "select seqid,sequence,qual from $querytable;";
			}		
			
			//echo $sql . "\n";
	
			$queryresult=$mindb_connection->query($sql);
			if ($queryresult->num_rows >= 1){
				foreach ($queryresult as $row) {
					echo "@" . $row['seqid'] . "\n";
					echo $row['sequence'] . "\n";
					echo "+\n";
					$qualscore= substr($row['qual'], 1, -1);
					$qualarray = str_split($qualscore);
					foreach ($qualarray as $value){
						if ($minupver < 0.5){ 
								echo chr(ord($value)-31);
							}else{
								echo chr(ord($value));
							}
					}
					print "\n";
				}
			}
			
		}else{
		
			$querytable = "basecalled_" . $jobtype;
			$lasttable = "last_align_" . $querytable . "_5prime";
			if ($_GET["align"] == 1) {
				$sql = "select seqid, sequence, qual,refname from $querytable inner join $lasttable using (basename_id) inner join reference_seq_info using (refid);";
			}elseif ($_GET["unalign"] == 1) {
				$sql = "select seqid,sequence, qual from $querytable where basename_id not in (select basename_id from $lasttable);";
			}elseif (isset($_GET["readname"])){
				$sql = "select seqid,sequence, qual from $querytable where basename_id = '" . $_GET["readname"] . "';";
				//echo $sql;
			}	else {
				$sql = "select seqid,sequence,qual from $querytable;";
			}	
			
			//echo $sql . "\n";
			
			$queryresult=$mindb_connection->query($sql);
			if ($queryresult->num_rows >= 1){
				foreach ($queryresult as $row) {
					echo ">" . $row['seqid'];
					if ($_GET["align"] == 1) {
						echo "\t" . $row['refname'];
						}
					echo "\n";
					echo $row['sequence'] . "\n";
				}
			}	
		}
	}
} else {
	echo "ERROR";
}
?>
