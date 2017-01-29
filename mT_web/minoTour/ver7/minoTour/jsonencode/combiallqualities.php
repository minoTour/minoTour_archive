<?php
ini_set('max_execution_time', 300);
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
		
		$jsonjobname="allqualities";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		//Check if we want to get a single read or all reads.
		if (isset($_GET["readname"])) {
			//Check if entry already exists in jsonstore table:
				$sql_template = "select sequence,qual,seqid from basecalled_template inner join tracking_id using (basename_id) where basename = '" . $_GET['readname'] ."';";
				$sql_complement = "select sequence,qual,seqid from basecalled_complement inner join tracking_id using (basename_id) where basename = '" . $_GET['readname'] ."';";
				$sql_2d = "select sequence,qual,seqid from basecalled_2d inner join tracking_id using (basename_id) where basename = '" . $_GET['readname'] ."';";
			}else{
				$sql_template = "select sequence,qual,seqid from basecalled_template ORDER BY RAND() LIMIT 100;";
				$sql_complement = "select sequence,qual,seqid from basecalled_complement ORDER BY RAND() LIMIT 100;";
				$sql_2d = "select sequence,qual,seqid from basecalled_2d ORDER BY RAND() LIMIT 100;";
			}		
		
		$resultarray;
		$sequencearray;
		
		//echo $sql_template;
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$templength = strlen($row['qual']);
				$result = substr($row['qual'], 1, -1);
				$resultarray['template'][$row['seqid']]=$result;
				$sequence = substr($row['sequence'],1,-1);
				$sequencearray['template'][$row['seqid']]=$sequence;
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$complength = strlen($row['qual']);
				$result = substr($row['qual'], 1, -1);
				$resultarray['complement'][$row['seqid']]=$result;
				$sequence = substr($row['sequence'],1,-1);
				$sequencearray['complement'][$row['seqid']]=$sequence;
			}
		}
		if ($read2d->num_rows >= 1){
			$counter =0;
			foreach ($read2d as $row) {
				$length2d = strlen($row['qual']);
				$result = substr($row['qual'], 1, -1);
				$resultarray['2d'][$row['seqid']]=$result;
				$sequence = substr($row['sequence'],1,-1);
				$sequencearray['2d'][$row['seqid']][$counter]=$sequence;
				$counter++;
			}
		}
		
		$qualityarray;
		
		foreach ($resultarray as $key => $value) {
			//echo $key."\n";
			foreach ($value as $key2 => $value2){
				//echo $value2."\n";
				$qualarray = str_split($value2);
				$counter = 1;
				foreach ($qualarray as $value3){
					//echo $key . "\t" . $counter . "\n";
					
					$qualityarray[$key][$counter]['value'] = $qualityarray[$key][$counter]['value']+(ord($value3)-31);
					$qualityarray[$key][$counter]['number']++;
					$counter++;
				}
			}
		}
		

		$sequencenumbersarray;
		foreach ($sequencearray as $key=> $value) {
			foreach ($value as $key2=> $value2) {
				$seqarray = str_split($value2);
				$counter = 1;
				foreach ($seqarray as $value3) {
					//echo $value3 . "\n";
					if ($value3 == "A") {
						$sequencenumbersarray[$key]["A"][$counter]=-1;	
					}else{
						$sequencenumbersarray[$key]["A"][$counter]=0;		
					}
					if ($value3 == "T") {
						$sequencenumbersarray[$key]["T"][$counter]=-1;	
					}else{
						$sequencenumbersarray[$key]["T"][$counter]=0;		
					}
					if ($value3 == "G") {
						$sequencenumbersarray[$key]["G"][$counter]=-1;	
					}else{
						$sequencenumbersarray[$key]["G"][$counter]=0;		
					}
					if ($value3 == "C") {
						$sequencenumbersarray[$key]["C"][$counter]=-1;	
					}else{
						$sequencenumbersarray[$key]["C"][$counter]=0;		
					}
					$counter++;
				}				
			}
		}
		

		//echo $templength;
		//if ($templength >= 500 || $complength >= 500 || $length2d >= 500) {
		//	$lenlimit = 1;
		//}
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring;
		$jsonstring = $jsonstring . "[\n";
			foreach ($sequencenumbersarray as $key => $value) {
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring .  "{\n";
					$jsonstring = $jsonstring .  "\"name\" : \"$key2\",\n";
					$jsonstring = $jsonstring . "\"type\": \"column\",\n";
					//$jsonstring = $jsonstring . "\"yAxis\": 1,\n";
					$jsonstring = $jsonstring . "\"pointPadding\": 0,\n";
					$jsonstring = $jsonstring . "\"groupPadding\": 0,\n";
					$jsonstring = $jsonstring . "\"borderWidth\": 0,\n";
					$jsonstring = $jsonstring . "\"shadow\": \"false\",\n";
					
					
					//if (isset($_GET["readname"])) {
					//	$jsonstring = $jsonstring . "\"step\": \"center\",\n";
						//if ($lenlimit == 1) {
						//	$jsonstring = $jsonstring . "\"min\": \"1\",\n";
						//	$jsonstring = $jsonstring . "\"max\": \"500\",\n";
						//}
					//}
					$jsonstring = $jsonstring .  "\"data\": [";
					foreach ($value2 as $key3 => $value3) {
						$jsonstring = $jsonstring . "[" . $key3 . "," . $value3 . "],\n";	
					}
					$jsonstring = $jsonstring .  "]\n},\n";
				}
			}

			foreach ($qualityarray as $key => $value){
				$jsonstring = $jsonstring .  "{\n";
				$jsonstring = $jsonstring .  "\"name\" : \"$key\",\n";
				$jsonstring = $jsonstring . "\"type\": \"line\",\n";
				//$jsonstring = $jsonstring . "\"color\": \"#000000\",\n";
				if (isset($_GET["readname"])) {
					//$jsonstring = $jsonstring . "\"step\": \"center\",\n";
					//if ($lenlimit == 1) {
					//	$jsonstring = $jsonstring . "\"min\": \"1\",\n";
					//	$jsonstring = $jsonstring . "\"max\": \"500\",\n";
					//}
				}
				$jsonstring = $jsonstring .  "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring .  "[" . $key2 . "," . ($qualityarray[$key][$key2]['value']/$qualityarray[$key][$key2]['number']) . "],\n" ;
				}

				$jsonstring = $jsonstring .  "]\n},\n";
				

				
			}
			
			
			
						
			
			$jsonstring = $jsonstring .   "]\n";
			if ($_GET["prev"] == 1){
				//include 'savejson.php';
			}
		}
	
			
			
	$callback = $_GET['callback'];
	echo $callback.'('.$jsonstring.');';
	
	}
} else {
	echo "ERROR";
}
?>