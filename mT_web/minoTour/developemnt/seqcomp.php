<?php
error_reporting(0);
// include the configs / constants for the database connection
require_once("../config/db.php");

// load the login class
require_once("../classes/Login.php");

// load the functions
require_once("../includes/functions.php");

$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

##create an array to hold the analysis data

//$mafarray=array();
$mafarray2=array();

echo "We're in!";

##Create string to hold text:

$textholder;
	if (!$db_connection->connect_errno) {
		echo "Connected!<br>";
		echo date('l jS \of F Y h:i:s A') . "<br>";
		
		$sql = "select refid,r_align_string,r_start,q_align_string from minion_PLSP57501_20140909_JA_defA_4434.last_align_maf_basecalled_template where refid = 1;";
		$runquery = $mindb_connection->query($sql);
		
		echo "Sql query complete<br>";
		echo date('l jS \of F Y h:i:s A') . "<br>";
			foreach ($runquery as $row){
				##Need to get the position in the reference.
				$counter = ($row['r_start']);
				$position = 1;
				//$chunksize = 10000;
				
				//echo "Reference start is " . $row['r_start'] . ".<br>";
				//echo "Ref id is " . $row['refid'] . ".<br>";
				
				$refarray = str_split($row['r_align_string']);
				$quearray = str_split($row['q_align_string']);
				//$mafarray=array();
				//for ($x=0; $x<$stringlength; $x++) {
				foreach (array_keys($refarray) as $key){
					if ($refarray[$key] === "-") {
						//next;
					}else{
						$counter++;
						$position = 0;
					}
					$position++;
					//echo $row['refid'],$counter,$position,$quearray[$key];
					//$key2=$row['refid'].":".$counter.":".$position;
					//$testarray[$key2]=$quearray[$key];
					//$rtestarray[$key2]=$refarray[$key];
					$mafarray[$row['refid']][$counter][$position]['refpos']=$refarray[$key];
					$mafarray[$row['refid']][$counter][$position][$quearray[$key]]++;
				}
								/*
				foreach ($refarray as $key => $value) {
					echo "$key $value <br>";
					echo "Ref position is " . $row['r_start'] . " modified is " . ($row['r_start'] + ($key * $chunksize)) . ".<br>";
					compare_two_strings($value,$quearray[$key],($row['r_start'] + ($key * $chunksize)),$row['refid']);
				}*/
				
				//Counter for number of processes
				/*$i = 1;

				//Loop through the image sizes but this time fork a process for each size.
				foreach($refarray as $key => $value)
					{
						
					//echo "We really in the loop $value<br>";
				    //Fork a process
				    $pid = pcntl_fork();
					
				    //if we're in a child thread then grab an image size and process it.
				    if (!$pid) {
				        echo 'starting child ', $i, PHP_EOL;
				        echo "Value is ". $value . "\n";
				        echo "Query is " . $quearray[$key] . "\n";
				        echo "Start is " . ($row['r_start'] + ($key * $chunksize)) . "\n";
				        echo "Ref id is " . $row['refid'] . "\n";
				       	compare_two_strings($value,$quearray[$key],($row['r_start'] + ($key * $chunksize)),$row['refid']);
			
				        //Die otherwise the process will continue to loop and each process will create all the thumbnails
				        die();
				    }
			
				    $i++;
				}
	
				//Wait for all the subprocesses to complete to avoid zombie processes
				foreach($refarray as $key => $value)
				{
				    pcntl_wait($status);
				} */
				/*
				$stringlength = (strlen($row['r_align_string']));
				for ($x=0; $x<$stringlength; $x++) {
					if ($row['r_align_string'][$x] === "-") {
						//next;
					}else{
						$counter++;
						$position = 0;
					}
					$position++;
					$mafarray[$row['refid']][$counter][$position]['refpos']=$row['r_align_string'][$x];
					$mafarray[$row['refid']][$counter][$position][$row['q_align_string'][$x]]++;
				}*/
	
				
				
		}

		
	}
	
	//var_dump ($mafarray);

	
	function compare_two_strings($refstring,$querystring,$refstart,$refid) {
		global $mafarray2;
		$position = 1;
		for ($x=0; $x<=(strlen($refstring)-1); $x++) {
			if ($refstring[$x] !== "-") {
				$refstart++;
				$position = 0;
			}
				//echo "hello";
			$position++;
			$mafarray2[$refid][$refstart][$position]['refpos']=$refstring[$x];
			$mafarray2[$refid][$refstart][$position][$querystring[$x]]++;
		}
	}
	
//$refstring = "GCGCT-CGTGTAACG-C-TTTCATCGCACCTATCGCCATATCGTC-GTTACTGGCAACTAACGCGCTAAATTTAGCCCCACGTTCGAGCAACATTTCTACCCCTTCGGCCCCGCTGGCAGGCGTCCATTTACCGTTAGCGATAAGTTTTTCATTGAGCGCAATACCA-";
//$querystring = "GCGCTACGGGCTTCGGCATTTCATGACAGCTATC---ATATCGTCTGTT-CTGGCAAC-AACGCACCG--TGT-GAC--AGGTCGGAGC--C-TTTC-ATG---TCAACCCC--TGGCGGG-G--CATT-AC-GTCCG-GAT-----TTTCATTACGAGTATCCCCGC";
//$refstart = "2838478";
//$querystart = "90";





//var_dump($mafarray2);
echo "Compute completed.<br>";
echo date('l jS \of F Y h:i:s A') . "<br>";
asort($mafarray);
echo  "Array is sorted. <br>";
echo  date('l jS \of F Y h:i:s A') . "<br>";

echo "Looping through array.<br>";
$textholdingarray=array();

 foreach($mafarray as $key => $value) {
 	ksort ($value);
 	//echo ".<br>";
 	foreach($value as $key2 => $value2) {
 		//echo "-";
 		
 		//foreach ($value2 as $key3 => $value2) {
	 	//	echo $key3;
 		//}
 		foreach ($value2 as $key3 => $value3) {
 			 $textholdingarray[] = $key;
  			 $textholdingarray[] = $key2;
  			 $textholdingarray[] = $key3;
// 			$textholder = $textholder . ($key."\t".$key2 . "\t" . $key3. "\t" );
 			//$textholder = $textholder . $value3['result'] . "\t";
 			if (array_key_exists('refpos',$value3)){
 			 $textholdingarray[] = $value3['refpos']; 				
// 				$textholder = $textholder . $value3['refpos'] . "\t";
 			} else {
 				$textholdingarray[] = "n/a";
// 				$textholder = $textholder . "n/a\t";
 			}
 			if (array_key_exists('A', $value3)){
 				$textholdingarray[] = $value3['A'];
// 				$textholder = $textholder . $value3['A']. "\t";	
 			} else {
 				$textholdingarray[] = "0";
// 				$textholder = $textholder . "0\t";
 			}
 			if (array_key_exists('T', $value3)){
 				$textholdingarray[] = $value3['T'];
// 				$textholder = $textholder . $value3['T']. "\t";	
 			} else {
 				$textholdingarray[] = "0";
// 				$textholder = $textholder . "0\t";
 			}
 			if (array_key_exists('G', $value3)){
 				$textholdingarray[] = $value3['G'];
// 				$textholder = $textholder . $value3['G']. "\t";	
 			} else {
 				$textholdingarray[] = "0";
// 				$textholder = $textholder . "0\t";
 			}
 			if (array_key_exists('C', $value3)){
 				$textholdingarray[] = $value3['C'];
// 				$textholder = $textholder . $value3['C']. "\t";	
 			} else {
 				$textholdingarray[] = "0";
 //				$textholder = $textholder . "0\t";
 			}
 			//if (array_key_exists('ins', $value3)) {
 			//	$textholdingarray[] = $value3['-'];
// 				$textholder = $textholder . $value3['ins'] . "\t";
 			//} else {
 			//	$textholdingarray[] = "0";
//				$textholder = $textholder . "0\t";
 			//}
 			if (array_key_exists('-', $value3)) {
 				$textholdingarray[] = $value3['-'];
// 				$textholder = $textholder . $value3['del'] . "\t";
 			} else {
 				$textholdingarray[] = "0";
// 				$textholder = $textholder . "0\t";
 			}
 			$textholdingarray[] = "<br>";
// 	 		$textholder = $textholder .  "<br>";
 		}
 	}
 	
}
echo $textholder;

echo  "Processing finished. <br>";
echo  date('l jS \of F Y h:i:s A') , "<br>";

$tab_separated = implode("\t", $textholdingarray);
//echo $tab_separated;

echo "Processing really finished .<br>";
echo  date('l jS \of F Y h:i:s A') , "<br>";


?>

