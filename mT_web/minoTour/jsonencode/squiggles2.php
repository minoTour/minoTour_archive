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
	//$jsonstring = $jsonstring . $_SESSION['active_run_name'];
	
	//$jsonstring = $jsonstring . '<br>';
	
	if (!$mindb_connection->connect_errno) {
		
		//Check if entry already exists in jsonstore table:
		//$jsonjobname="average_time_over_time";
		
		//$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		//$checking=$mindb_connection->query($checkrow);
		//if ($checking->num_rows ==1){
			//echo "We have already run this!";
		//	foreach ($checking as $row){
		//		$jsonstring = $row['json'];
		//	}
		//} else {
		
		if (isset($_GET["readname"])) {
			$sqlsequence = "SELECT sequence,qual from basecalled_" . $_GET['type'] . " inner join tracking_id using (basename_id) where basename = '" .$_GET['readname'] . "';";
			$sqlsquiggle = "SELECT mean,start,stdv,length,model_state,model_level,move,p_model_state,mp_state,p_mp_state,p_A,p_C,p_G,p_T FROM caller_basecalled_" . $_GET['type'] ." inner join tracking_id using (basename_id) where basename = '" . $_GET['readname'] ."';";
			//echo $sqlsquiggle . "\n";
			//echo $sqlsequence . "\n";
			$resultarray;
			$squiggleresult = $mindb_connection->query($sqlsquiggle);
			if ($squiggleresult->num_rows>= 1){
				foreach ($squiggleresult as $row) {
					$resultarray[$_GET['type']][$row['start']]['mean']=$row['mean'];
					$resultarray[$_GET['type']][$row['start']]['stdv']=$row['stdv'];
					$resultarray[$_GET['type']][$row['start']]['length']=$row['length'];
					$resultarray[$_GET['type']][$row['start']]['model_state']=$row['model_state'];
					$resultarray[$_GET['type']][$row['start']]['model_level']=$row['model_level'];
					$resultarray[$_GET['type']][$row['start']]['move']=$row['move'];
					$resultarray[$_GET['type']][$row['start']]['p_model_state']=$row['p_model_state'];
					$resultarray[$_GET['type']][$row['start']]['mp_state']=$row['mp_state'];
					$resultarray[$_GET['type']][$row['start']]['p_mp_state']=$row['p_mp_state'];
					$resultarray[$_GET['type']][$row['start']]['p_A']=$row['p_A'];
					$resultarray[$_GET['type']][$row['start']]['p_C']=$row['p_C'];
					$resultarray[$_GET['type']][$row['start']]['p_G']=$row['p_G'];
					$resultarray[$_GET['type']][$row['start']]['p_T']=$row['p_T'];
				}
			}
			$sequencearray;
			$letterarray;
			$qualarray;
			$sequenceresult = $mindb_connection->query($sqlsequence);
			if ($sequenceresult->num_rows == 1) {
				$sequence_row = $sequenceresult->fetch_object();	
				//var_dump ($sequence_row);
				//echo $sequence_row->sequence . "\n";
				//echo $sequence_row->qual . "\n";
				$string = substr($sequence_row->qual, 1, -1);
				$letterarray = str_split($sequence_row->sequence);
				$qualarray = str_split($string);
			}
			
			//make sure we know where we are in the sequence and the quality position
			settype($position_indicator, "int");
			$position_indicator = 0;
			
			//note that the first event is always going to be a 0 but after that a 0 means that we haven't moved...
			$firstcheck = 0;
			
			//BUT - dam - we need to read the previous 5 bases to get the new sequences...
			$previous5;
			$previousstep;
			$previousstart;
			$previouscount;
			
			
			//Create an array to store the base position values, quality scores and so on...
			$basearray;
			
			//create an array to store flags indicated inferred positions
			$flagarray;
			
			//create an array to store quality scores based on position
			$qualityarray;
			$callback = $_GET['callback'];
			echo $callback.'([';
			foreach ($resultarray as $type => $value) {
				//So the first entry we need to take the first three positions for. After that we just take the middle position UNLESS the step is 0 (the base hasn't changed) or 2 in which case we need to take the 2 and third positions.
				$lastletarray;
				$lastadjust;
				$laststart;
				
				foreach ($value as $start => $value2) {
					$letarray = str_split($value2['model_state']);
					$adjustment = ($value2['length']/4);
					if ($firstcheck == 0) {
						//we're going to take the first three positions here and we are going to add a fictional time point for the first two.
						//echo $value2['model_state'] . "\n";						
						for ($i = 0; $i <= 2; $i++) {
							$newstart = $start - ((2-$i)*$adjustment);
							//echo $newstart . "\t";
							//echo $letarray[$i] . "\n";
							if ($i < 2) {
								$flagarray[] = $start;
							}
							if ($letarray[$i] == "A") {
								$basearray[($newstart*10000)]["A"]=1;
							}else{
								$basearray[($newstart*10000)]["A"]=0;
							}
							if ($letarray[$i] == "T") {
								$basearray[($newstart*10000)]["T"]=1;
							}else{
								$basearray[($newstart*10000)]["T"]=0;
							}
							if ($letarray[$i] == "G") {
								$basearray[($newstart*10000)]["G"]=1;
							}else{
								$basearray[($newstart*10000)]["G"]=0;
							}
							if ($letarray[$i] == "C") {
								$basearray[($newstart*10000)]["C"]=1;
							}else{
								$basearray[($newstart*10000)]["C"]=0;
							}
							if ($i < 2) {
								$flagarray[($newstart*10000)]='i';
							}
							//$qualityarray[$newstart]=(ord($qualarray[$position_indicator])-31);
							$qualityarray[($newstart*10000)]=(ord($qualarray[$position_indicator])-31);
							//echo $adjustment . "\t" . $newstart . "\t" . $position_indicator . "\tstarting" ."\n";
							$position_indicator++;
						}
						$firstcheck = 1;
					}	
					if ($firstcheck != 0) {
						$move = $value2['move'];
						if ($move == 0) {
							//echo "nothing to see here\n";
							next;
						}else if ($move == 1) {
							//echo $start . "\t";
							//echo $letarray[2] . "\n";
							if ($letarray[2] == "A") {
								$basearray[($start*10000)]["A"]=1;
							}else{
								$basearray[($start*10000)]["A"]=0;
							}
							if ($letarray[2] == "T") {
								$basearray[($start*10000)]["T"]=1;
							}else{
								$basearray[($start*10000)]["T"]=0;
							}
							if ($letarray[2] == "G") {
								$basearray[($start*10000)]["G"]=1;
							}else{
								$basearray[($start*10000)]["G"]=0;
							}
							if ($letarray[2] == "C") {
								$basearray[($start*10000)]["C"]=1;
							}else{
								$basearray[($start*10000)]["C"]=0;
							}
							//$qualityarray[$start]=(ord($qualarray[$position_indicator])-31);
							$qualityarray[($start*10000)]=(ord($qualarray[$position_indicator])-31);
							//echo $adjustment . "\t" . $start . "\t" . $position_indicator . "\tmove=1" ."\n";
							$position_indicator++;
						}else if ($move == 2) {
							for ($i = 1; $i <= 2; $i++) {
								if ($i < 2) {
									$flagarray[] = $start;
								}
								//echo "we're in the doublet...\n";
								$newstart = $start - ((2-$i)*$adjustment);
								//echo $newstart . "\t";
								//echo $letarray[$i] . "\n";
								if ($letarray[$i] == "A") {
									$basearray[($newstart*10000)]["A"]=1;
								}else{
									$basearray[($newstart*10000)]["A"]=0;
								}
								if ($letarray[$i] == "T") {
									$basearray[($newstart*10000)]["T"]=1;
								}else{
									$basearray[($newstart*10000)]["T"]=0;
								}
								if ($letarray[$i] == "G") {
									$basearray[($newstart*10000)]["G"]=1;
								}else{
									$basearray[($newstart*10000)]["G"]=0;
								}
								if ($letarray[$i] == "C") {
									$basearray[($newstart*10000)]["C"]=1;
								}else{
									$basearray[($newstart*10000)]["C"]=0;
								}
								if ($i < 2) {
									$flagarray[($newstart*10000)]='i';
								}
							
								//$qualityarray[$newstart]=(ord($qualarray[$position_indicator])-31);
								$qualityarray[($newstart*10000)]=(ord($qualarray[$position_indicator])-31);
								//echo $adjustment . "\t" . $newstart . "\t" . $position_indicator ."\tcorrecting" . "\n";
								$position_indicator++;
							}
						}
					}
					$lastletarray = str_split($value2['model_state']);
					$lastadjust = $value2['length'];
					$laststart = $start;
				}
				
				for ($i = 3; $i <= 4; $i++) {
					settype($newstart, "string");
					$newstart = $laststart + (($i-2)*$lastadjust);
					//echo $newstart . "\t";
					//echo $lastletarray[$i] . "\n";
					if ($letarray[$i] == "A") {
						$basearray[($newstart*10000)]["A"]=1;
					}else{
						$basearray[($newstart*10000)]["A"]=0;
					}
					if ($letarray[$i] == "T") {
						$basearray[($newstart*10000)]["T"]=1;
					}else{
						$basearray[($newstart*10000)]["T"]=0;
					}
					if ($letarray[$i] == "G") {
						$basearray[($newstart*10000)]["G"]=1;
					}else{
						$basearray[($newstart*10000)]["G"]=0;
					}
					if ($letarray[$i] == "C") {
						$basearray[($newstart*10000)]["C"]=1;
					}else{
						$basearray[($newstart*10000)]["C"]=0;
					}
					//$qualityarray[$newstart]=(ord($qualarray[$position_indicator])-31);
					$qualityarray[($newstart*10000)]=(ord($qualarray[$position_indicator])-31);
					//echo $adjustment . "\t" . $newstart . "\t" . $position_indicator . "\tending" . "\n";
					$position_indicator++;
				}


				//Outputting all the data for the plots.
				
				echo "{\"type\": \"line\",\n";
				echo "\"name\":\"Squiggle\",\n";
				echo "\"color\": 'black',\n";
				echo "\"yAxis\": 0,\n";
				echo "\"step\": \"left\",\n";
				echo "\"data\":[";				
				$position_indicator=0;
				$firstcheck=0;
				//echo "Squiggle Data\n";
				foreach ($value as $start => $value2) {
					echo "[";
					echo $start . ",";
					echo $value2['mean'];
					echo "],\n";
				}
				echo "]},\n";
				
				
				echo "{\"type\": \"line\",\n";
				echo "\"name\":\"Quality\",\n";
				echo "\"color\": 'darkgrey',\n";
				echo "\"yAxis\": 1,\n";
				echo "\"step\": \"left\",\n";
				echo "\"data\":[";	
				foreach ($qualityarray as $sausage => $monkey) {
//					foreach ($value2 as $value2=> $monkey) {
						echo "[" . ($sausage/10000) ."," . $qualityarray[$sausage]. "],\n";	
//					}
				}			
				
				
				echo "]},\n";
				
				echo "{\"type\": \"line\",\n";
				echo "\"name\":\"p_A\",\n";
				echo "\"color\": 'blue',\n";
				echo "\"yAxis\": 2,\n";
				echo "\"step\": \"left\",\n";
				echo "\"data\":[";
				foreach ($value as $start => $value2) {
					echo "[" . $start . ",";
					echo $value2['p_A'] . "],\n";
				}
				//echo "\"dataGrouping\": {
                //    \"units\": \"groupingUnits\"
                //}";
				echo "]},\n";
				echo "{\"type\": \"line\",\n";
				echo "\"name\":\"p_T\",\n";
				echo "\"color\": 'yellow',\n";
				echo "\"yAxis\": 2,\n";
				echo "\"step\": \"center\",\n";
				echo "\"data\":[";
				foreach ($value as $start => $value2) {
					echo "[" . $start . ",";
					echo $value2['p_T'] . "],\n";
				}
				echo "]},\n";
				echo "{\"type\": \"line\",\n";
				echo "\"name\":\"p_G\",\n";
				echo "\"color\": 'green',\n";
				echo "\"yAxis\": 2,\n";
				echo "\"step\": \"left\",\n";
				echo "\"data\":[";
				foreach ($value as $start => $value2) {
					echo "[" . $start . ",";
					echo $value2['p_G'] . "],\n";
				}
				echo "]},\n";
				echo "{\"type\": \"line\",\n";
				echo "\"name\":\"p_C\",\n";
				echo "\"color\": 'red',\n";
				echo "\"yAxis\": 2,\n";
				echo "\"step\": \"left\",\n";
				echo "\"data\":[";
				foreach ($value as $start => $value2) {
					echo "[" . $start . ",";
					echo $value2['p_C'] . "],\n";
				}
				echo "]},\n";
				}
				echo "{\"type\": \"column\",\n";
				echo "\"name\":\"A\",\n";
				echo "\"color\": 'blue',\n";
				echo "\"yAxis\": 3,\n";
				echo "\"stacking\": \"normal\",\n";
				echo "\"data\":[";
				foreach ($basearray as $sausage => $monkey) {
//					foreach ($value2 as $value2=> $monkey) {
						echo "[" . ($sausage/10000) ."," . $basearray[$sausage]['A']. "],\n";	
//					}
				}
				echo "]},\n";
				echo "{\"type\": \"column\",\n";
				echo "\"name\":\"T\",\n";
				echo "\"color\": 'yellow',\n";
				echo "\"yAxis\": 3,\n";
				echo "\"stacking\": \"normal\",\n";
				echo "\"data\":[";
				foreach ($basearray as $sausage => $monkey) {
//					foreach ($value2 as $value2=> $monkey) {
						echo "[" . ($sausage/10000) ."," . $basearray[$sausage]['T']. "],\n";	
//					}
				}
				echo "]},\n";
				echo "{\"type\": \"column\",\n";
				echo "\"name\":\"G\",\n";
				echo "\"color\": 'green',\n";
				echo "\"yAxis\": 3,\n";
				echo "\"stacking\": \"normal\",\n";
				echo "\"data\":[";
				foreach ($basearray as $sausage => $monkey) {
//					foreach ($value2 as $value2=> $monkey) {
						echo "[" . ($sausage/10000) ."," . $basearray[$sausage]['G']. "],\n";	
//					}
				}
				echo "]},\n";
				echo "{\"type\": \"column\",\n";
				echo "\"name\":\"C\",\n";
				echo "\"color\": 'red',\n";
				echo "\"yAxis\": 3,\n";
				echo "\"stacking\": \"normal\",\n";
				echo "\"data\":[";
				foreach ($basearray as $sausage => $monkey) {
//					foreach ($value2 as $value2=> $monkey) {
						echo "[" . ($sausage/10000) ."," . $basearray[$sausage]['C']. "],\n";	
//					}
				}
				echo "]},\n";
			echo "]);";
			
			
			
	
		}
		
		
			
	}
}
	//} else {
//	$jsonstring = $jsonstring . "ERROR";
//}
?>
