<?php
ini_set('max_execution_time', 300);
//header('Content-Type: application/json');
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
		$sqljsoncheck = "select * from jsonstore;";
		$sqljsonchecksecurity = $mindb_connection->query($sqljsoncheck);
		
		/// Execute all the json include scripts for checking if the data has been entered into the jsonstore database.
//Read Number
		$jsonjobname="readnumber";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		
		$sql_template = "select count(*) as readnum, exp_script_purpose from basecalled_template inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_complement = "select count(*) as readnum, exp_script_purpose from basecalled_complement inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_2d = "select count(*) as readnum, exp_script_purpose from basecalled_2d inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray[$row['exp_script_purpose']]['template']=$row['readnum'];
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$resultarray[$row['exp_script_purpose']]['complement']=$row['readnum'];
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$resultarray[$row['exp_script_purpose']]['2d']=$row['readnum'];
			}
		}
		$jsonstring="";
		$jsonstring = $jsonstring .   "[\n";
			foreach ($resultarray as $key => $value){
				
				
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring .   "{\n";
					$jsonstring = $jsonstring .   "\"name\" : \"$key2\", \n";
					$jsonstring = $jsonstring .  "\"data\": [";
					$jsonstring = $jsonstring .   "$value2,";
					$jsonstring = $jsonstring .  "]\n},\n";
				}
				
				//echo "},\n";
				
			}
	
		
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		
//Active Channels Over Time
		
		$jsonjobname="active_channels_over_time";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select floor(start_time/60)*60 as bin_floor, count(*) as chan_count from basecalled_template inner join config_general using (basename_id) group by 1 order by 1;";
		$sql_complement = "select floor(start_time/60)*60 as bin_floor, count(*) as chan_count from basecalled_complement inner join config_general using (basename_id) group by 1 order by 1;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['template'][$row['bin_floor']]=$row['chan_count'];
			}
		}
	
		if ($complement->num_rows >=1) {
			foreach ($complement as $row) {
				$resultarray['complement'][$row['bin_floor']]=$row['chan_count'];
			}
		}
		
	
		//var_dump($resultarray);
		//$jsonstring = $jsonstring . json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
	
// All Qualities

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
		if (isset($GET_["readname"])) {
			//Check if entry already exists in jsonstore table:
				$sql_template = "select qual,seqid from basecalled_template where basename = '" . $GET_['readname'] ."';";
				$sql_complement = "select qual,seqid from basecalled_complement where basename = '" . $GET_['readname'] ."';";
				$sql_2d = "select qual,seqid from basecalled_2d where basename = '" . $GET_['readname'] ."';";
			}else{
				$sql_template = "select qual,seqid from basecalled_template ORDER BY RAND() LIMIT 100;";
				$sql_complement = "select qual,seqid from basecalled_complement ORDER BY RAND() LIMIT 100;";
				$sql_2d = "select qual,seqid from basecalled_2d ORDER BY RAND() LIMIT 100;";
			}		
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$result = substr($row['qual'], 1, -1);
				$resultarray['template'][$row['seqid']]=$result;
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$result = substr($row['qual'], 1, -1);
				$resultarray['complement'][$row['seqid']]=$result;
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$result = substr($row['qual'], 1, -1);
				$resultarray['2d'][$row['seqid']]=$result;
			}
		}
		
		$qualityarray=array();
		
		foreach ($resultarray as $key => $value) {
			//echo $key."\n";
			foreach ($value as $key2 => $value2){
				//echo $value2."\n";
				$qualarray = str_split($value2);
				$counter = 1;
				foreach ($qualarray as $value3){
					//echo $key . "\t" . $counter . "\n";
					
					$qualityarray[$key][$counter]['value'] = $qualityarray[$key][$counter]['value']+(chr(ord($value3)-31));
					$qualityarray[$key][$counter]['number']++;
					$counter++;
				}
			}
		}
		
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($qualityarray as $key => $value){
				$jsonstring = $jsonstring .  "{\n";
				$jsonstring = $jsonstring .  "\"name\" : \"$key\",\n";
				$jsonstring = $jsonstring .  "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring .  "[" . $key2 . "," . ($qualityarray[$key][$key2]['value']/$qualityarray[$key][$key2]['number']) . "],\n" ;
				}

				$jsonstring = $jsonstring .  "]\n},\n";
				

				
			}
			$jsonstring = $jsonstring .   "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
//Ave Len

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
		
		$resultarray=array();
	
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
		$jsonstring="";
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

// Average Length Over Time
	$jsonjobname="average_length_over_time";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select floor(start_time/60)*60 as bin_floor, ROUND(AVG(length(sequence))) as average_length from basecalled_template group by 1 order by 1;";
		$sql_complement = "select floor(start_time/60)*60 as bin_floor, ROUND(AVG(length(sequence))) as average_length from basecalled_complement group by 1 order by 1;;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['template'][$row['bin_floor']]=$row['average_length'];
			}
		}
	
		if ($complement->num_rows >=1) {
			foreach ($complement as $row) {
				$resultarray['complement'][$row['bin_floor']]=$row['average_length'];
			}
		}
	
		
	
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		
//Average Time Over Time

	$jsonjobname="average_time_over_time";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select floor(start_time/60)*60 as bin_floor, ROUND(AVG(duration)) as average_time from basecalled_template group by 1 order by 1;";
		$sql_complement = "select floor(start_time/60)*60 as bin_floor, ROUND(AVG(duration)) as average_time from basecalled_complement group by 1 order by 1;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['template'][$row['bin_floor']]=$row['average_time'];
			}
		}
	
		if ($complement->num_rows >=1) {
			foreach ($complement as $row) {
				$resultarray['complement'][$row['bin_floor']]=$row['average_time'];
			}
		}
	
		
	
		//var_dump($resultarray);
		//$jsonstring = $jsonstring . json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "var JSON3 = [\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "name : '$key', \n";
				$jsonstring = $jsonstring . "data: [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}

//Average Time Over Time2

	$jsonjobname="average_time_over_time2";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select floor(start_time/60)*60 as bin_floor, ROUND(AVG(duration)) as average_time from basecalled_template group by 1 order by 1;";
		$sql_complement = "select floor(start_time/60)*60 as bin_floor, ROUND(AVG(duration)) as average_time from basecalled_complement group by 1 order by 1;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		
		//echo $sql_template;
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				//$resultarray=array(
				//	"template"=>array(
				//		$row['bin_floor']=>$row['average_time']
				//	)
				//);
				$resultarray['template'][$row['bin_floor']]=$row['average_time'];
				//echo $row['average_time'] . "t\n";
				//var_dump ($resultarray);
			}
		}
	
		if ($complement->num_rows >=1) {
			foreach ($complement as $row) {
				//$resultarray=array(
				//	"complement"=>array(
				//		$row['bin_floor']=>$row['average_time']
				//	)
		//	);
				$resultarray['complement'][$row['bin_floor']]=$row['average_time'];
				//echo $row['average_time'] . "c\n";
			}
		}
		
		//var_dump ($template);
		//var_dump($resultarray);
		//$jsonstring = $jsonstring . json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		
		
// Coverage
if ($_SESSION['focusreference'] != "NOREFERENCE") {
	$refid_Query = "select refid from last_align_basecalled_template group by refid;";
	$refidarray=array();
	$refidnames = $mindb_connection->query($refid_Query);
	if ($refidnames->num_rows >= 1) {
		foreach ($refidnames as $row){
			$refidarray[]=$row['refid'];	
		}	
	}
	
	foreach ($refidarray as $refname) {
	
		$jsonjobname="coverage_" . $refname;
		
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		
		
			$sql_template = "SELECT refpos, count(*) as count FROM last_align_basecalled_template where (cigarclass=7 or cigarclass=8) and refid = '" . $refname . "' and covcount = 1 group by refpos order by refpos;";
			$sql_complement = "SELECT refpos, count(*) as count FROM last_align_basecalled_complement where (cigarclass=7 or cigarclass=8) and refid = '" . $refname . "' and covcount = 1  group by refpos order by refpos;";
			$sql_2d = "SELECT refpos, count(*) as count FROM last_align_basecalled_2d where (cigarclass=7 or cigarclass=8) and refid = '" . $refname . "' and covcount = 1 group by refpos order by refpos;";
		
		
			$covarray=array();
			
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
			$countarray=array();
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
			$jsonstring="";
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
	}
				};

//Depth Coverage
if ($_SESSION['focusreference'] != "NOREFERENCE") {
	$reflength = $_SESSION['focusreflength'];
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
		
		
		$covarray=array();
		
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
		
		$jsonstring="";
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
};	

//HistogramBases

	$jsonjobname="histogrambases";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			
			$sql_query = "SELECT ROUND(length(basecalled_template.sequence), -2.5) as bucket,  sum(length(basecalled_template.sequence)) as tempBASES, sum(length(basecalled_complement.sequence)) as compBASES,   sum(length(basecalled_2d.sequence)) as seq2dBASES from basecalled_template left join basecalled_complement using (basename_id) left join basecalled_2d using (basename_id) group by bucket;";

			$sql_execute=$mindb_connection->query($sql_query);		
		
		$category = array();
		$category['name'] = 'Size';

		$series1 = array();
		$series1['name'] = 'Template';

		$series2 = array();
		$series2['name'] = 'Complement';

		$series3 = array();
		$series3['name'] = '2d';
		
		if ($sql_execute->num_rows >=1) {
			foreach ($sql_execute as $row){
				$category['data'][]= $row['bucket'];
			    $series1['data'][] = $row['tempBASES'];
			    $series2['data'][] = $row['compBASES'];
			    $series3['data'][] = $row['seq2dBASES'];   
			}
		}

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
		
		
//histograms

		$jsonjobname="histogram";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			
		$sql_query = "SELECT ROUND(length(basecalled_template.sequence), -2.5) as bucket, COUNT(basecalled_template.sequence) as tempCOUNT,  COUNT(basecalled_complement.sequence) as compCOUNT,   COUNT(basecalled_2d.sequence) as seq2dCOUNT from basecalled_template left join basecalled_complement using (basename_id) left join basecalled_2d using (basename_id) group by bucket;";

		$sql_execute=$mindb_connection->query($sql_query);		
		
		$category = array();
		$category['name'] = 'Size';

		$series1 = array();
		$series1['name'] = 'Template';

		$series2 = array();
		$series2['name'] = 'Complement';

		$series3 = array();
		$series3['name'] = '2d';
		
		if ($sql_execute->num_rows >=1) {
			foreach ($sql_execute as $row){
				$category['data'][]= $row['bucket'];
			    $series1['data'][] = $row['tempCOUNT'];
			    $series2['data'][] = $row['compCOUNT'];
			    $series3['data'][] = $row['seq2dCOUNT'];   
			}
		}

		$result = array();
		array_push($result,$category);
		array_push($result,$series1);
		array_push($result,$series2);
		array_push($result,$series3);

		$jsonstring = json_encode($result, JSON_NUMERIC_CHECK);
		if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
//maxlen

$jsonjobname="maxlen";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select MAX(length(sequence)) as maxlen, exp_script_purpose from basecalled_template inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_complement = "select MAX(length(sequence)) as maxlen, exp_script_purpose from basecalled_complement inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_2d = "select MAX(length(sequence)) as maxlen, exp_script_purpose from basecalled_2d inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray[$row['exp_script_purpose']]['template']=$row['maxlen'];
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$resultarray[$row['exp_script_purpose']]['complement']=$row['maxlen'];
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$resultarray[$row['exp_script_purpose']]['2d']=$row['maxlen'];
			}
		}
		
	
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				
				
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "{\n";
					$jsonstring = $jsonstring . "\"name\" : \"$key2\", \n";
					$jsonstring = $jsonstring . "\"data\": [";
					$jsonstring = $jsonstring . "$value2,";
					$jsonstring = $jsonstring . "]\n},\n";
				}
				
				//$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
	
	
//percent coverage
if ($_SESSION['focusreference'] != "NOREFERENCE") {
$jsonjobname="percentcoverage";
			
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
		
		
			$covarray=array();
		
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
		
			$jsonstring="";
			$jsonstring = $jsonstring . "[\n";
			foreach ($covarray as $type => $typeval){
					$jsonstring = $jsonstring .  "{\n";
					$jsonstring = $jsonstring . "\"name\" : \"" . $type . "\", \n";
					$jsonstring = $jsonstring . "\"data\": [";
			//var numvals = count($typeval);
			//echo "This is " . numvals . "\n";

					foreach ($typeval as $key => $value) {
						if ($key == "percov") {
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
};

//readlengthqual
if ($_SESSION['focusreference'] != "NOREFERENCE") {
$jsonjobname="readlengthqual";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		
		
		
		$sql_template = "select floor(seqpos/50)*50 as bin_floor, AVG(seqbasequal) as avequal from last_align_basecalled_template group by 1 order by 1;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['template'][$row['bin_floor']]=$row['avequal'];
			}
		}
			
		
		$sql_complement = "select floor(seqpos/50)*50 as bin_floor, AVG(seqbasequal) as avequal from last_align_basecalled_complement group by 1 order by 1;";
		
		$complement=$mindb_connection->query($sql_complement);
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$resultarray['complement'][$row['bin_floor']]=$row['avequal'];
			}
		}
		$sql_2d = "select floor(seqpos/50)*50 as bin_floor, AVG(seqbasequal) as avequal from last_align_basecalled_2d group by 1 order by 1;";
		
		$read2d=$mindb_connection->query($sql_2d);
		
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$resultarray['2d'][$row['bin_floor']]=$row['avequal'];
			}
		}
		
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring ."{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"type\" : \"line\",\n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring ."},\n";
				
			}
			
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
};

//readnumberlength
$jsonjobname="readnumberlength";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		
		
		
		$sql_template_lengths = "select seqpos, count(*) as count from last_align_basecalled_template where alignnum = 1 group by seqpos order by seqpos;";
		
		$resultarray2=array();
	
		$template_lengths=$mindb_connection->query($sql_template_lengths);
			if ($template_lengths->num_rows >= 1){
			foreach ($template_lengths as $row) {
				$resultarray2['template'][$row['seqpos']]=$row['count'];
			}
		}
		
		
		$sql_complement_lengths = "select seqpos, count(*) as count from last_align_basecalled_complement where alignnum = 1 group by seqpos order by seqpos;";
		
		$complement_lengths=$mindb_connection->query($sql_complement_lengths);
			if ($complement_lengths->num_rows >= 1){
			foreach ($complement_lengths as $row) {
				$resultarray2['complement'][$row['seqpos']]=$row['count'];
			}
		}
		$sql_2d_lengths = "select seqpos, count(*) as count from last_align_basecalled_2d where alignnum = 1 group by seqpos order by seqpos;";
		
		$read2d_lengths=$mindb_connection->query($sql_2d_lengths);
			if ($read2d_lengths->num_rows >= 1){
			foreach ($read2d_lengths as $row) {
				$resultarray2['2d'][$row['seqpos']]=$row['count'];
			}
		}
	
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
				foreach ($resultarray2 as $key => $value){
				$jsonstring = $jsonstring ."{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"type\" : \"line\",\n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring ."},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		
//reads_over_time

$jsonjobname="reads_over_time";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select floor(start_time/60)*60 as bin_floor, count(*) as count from basecalled_template group by 1 order by 1 ;";
		$sql_complement = "select floor(start_time/60)*60 as bin_floor, count(*) as count from basecalled_complement group by 1 order by 1 ;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['template'][$row['bin_floor']]=$row['count'];
			}
		}
	
		if ($complement->num_rows >=1) {
			foreach ($complement as $row) {
				$resultarray['complement'][$row['bin_floor']]=$row['count'];
			}
		}
	
		
	
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "var JSON = [\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "name : '$key', \n";
				$jsonstring = $jsonstring . "data: [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}

//reads_over_time2

$jsonjobname="reads_over_time2";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select floor(start_time/60)*60 as bin_floor, count(*) as count from basecalled_template group by 1 order by 1 ;";
		$sql_complement = "select floor(start_time/60)*60 as bin_floor, count(*) as count from basecalled_complement group by 1 order by 1 ;";
		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['template'][$row['bin_floor']]=$row['count'];
			}
		}
	
		if ($complement->num_rows >=1) {
			foreach ($complement as $row) {
				$resultarray['complement'][$row['bin_floor']]=$row['count'];
			}
		}
	
		
	
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
	
//readsperpore

$jsonjobname="readsperpore";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select count(*) as count, channel from basecalled_template inner join config_general using (basename_id) group by channel order by channel;";

		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['template'][$row['channel']]=$row['count'];
			}
		}
	
		
	
		
	
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"borderWidth\": 1,\n";
				$jsonstring = $jsonstring . "\"data\": [";
				for ($i = 1 ; $i <=512; $i++){
					if (array_key_exists($i, $resultarray[$key])){
						$jsonstring = $jsonstring . "[" . getx($i) . "," . gety($i) . "," . $resultarray[$key][$i] . "],\n";
					}else{
						$jsonstring = $jsonstring . "[" . getx($i) . "," . gety($i) . ",0],\n";
					}
				}
				
				$jsonstring = $jsonstring . "],\n\"dataLabels\": {
                \"enabled\": true,
                \"color\":\"black\",
                \"style\": {
                    \"textShadow\": \"none\"
                }
            }	";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
//tracesperpore

$jsonjobname="tracessperpore";
			
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		$sql_template = "select count(*) as count,channel from config_general group by channel order by channel;";

		
		$resultarray=array();
	
		$template=$mindb_connection->query($sql_template);
		
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['template'][$row['channel']]=$row['count'];
			}
		}
	
		
	
		
	
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring="";
		$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"borderWidth\": 1,\n";
				$jsonstring = $jsonstring . "\"data\": [";
				for ($i = 1 ; $i <=512; $i++){
					if (array_key_exists($i, $resultarray[$key])){
						$jsonstring = $jsonstring . "[" . getx($i) . "," . gety($i) . "," . $resultarray[$key][$i] . "],\n";
					}else{
						$jsonstring = $jsonstring . "[" . getx($i) . "," . gety($i) . ",0],\n";
					}
				}
				
				$jsonstring = $jsonstring . "],\n\"dataLabels\": {
                \"enabled\": true,
                \"color\":\"black\",
                \"style\": {
                    \"textShadow\": \"none\"
                }
            }	";
				$jsonstring = $jsonstring . "},\n";
				
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
			
		
		$sqlcheck = "insert into jsonstore (name,json) VALUES ('do_not_delete','1');";
			//echo $sqlcheck;
		$sqlchecksecurity = $mindb_connection->query($sqlcheck);
			
		$drop1 = "drop table last_align_basecalled_template;";
		$drop2 = "drop table last_align_basecalled_complement;";
		$drop3 = "drop table last_align_basecalled_2d;";
		$sqldrop1 = $mindb_connection->query($drop1);
		$sqldrop2 = $mindb_connection->query($drop2);
		$sqldrop3 = $mindb_connection->query($drop3);
				echo "<div class='alert alert-warning alert-dismissible' role='alert'>
	  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
	  <strong>Success!</strong> The database has been archived for " . $_SESSION['focusrun'] . ".
	</div>";
		
		
		
		
		
	}		
} else {
	echo "ERROR";
}

function getx($value){
	$value = $value-1;
	$xval=31-(($value - ($value % 4))/4 % 32);
	return($xval);
}


function gety($value){
	$value = $value-1;
	$ad36 = $value % 4;
	$ab37 = ($value - $ad36)/4;
	$ad37 = ($ab37 % 32);
	$ab38 = (($ab37-$ad37)/32);
	$ad38 = ($ab38 % 4);
	$ag38 = ($ad36+(4*$ad38));
	$yval=(15 - $ag38);
	return($yval);
}
?>