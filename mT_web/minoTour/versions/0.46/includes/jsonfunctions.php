<?php

### This file contains a number of functions which are used to generate json for the website and are also utilised by the perl wrapper script on the backend. It is currently experimental.



##Average Length Over Time - chart showing read lengths over time
function average_length_over_time($jobname,$currrun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){	
		$memcache->set("$checkrunning", "YES", 0, 0); 
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
		
			$resultarray;
	
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
			$jsonstring;
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
	   $memcache->set("$checkvar", $jsonstring, 0, 2);
	}
	// cache for 2 minute as we want yield to update semi-regularly...

   $memcache->delete("$checkrunning");
    return $jsonstring;
	
	
}

##Reads number length - Chart showing the number of reads for a given read length
function readnumberlength($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		
		
		
			$sql_template_lengths = "select seqpos, count(*) as count from last_align_basecalled_template where alignnum = 1 group by seqpos order by seqpos;";
		
			$resultarray2;
	
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
			$jsonstring;
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
		    $memcache->set("$checkvar", $jsonstring, 0, 2);
		}
	// cache for 2 minute as we want yield to update semi-regularly...
	
	$memcache->delete("$checkrunning");
    return $jsonstring;

}

##Reads length Qual - Read qualities over the length of aligned reads
function readlengthqual($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		
		
		
			$sql_template = "select floor(seqpos/50)*50 as bin_floor, AVG(seqbasequal) as avequal from last_align_basecalled_template group by 1 order by 1;";
		
			$resultarray;
	
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
			$jsonstring;
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
		$memcache->set("$checkvar", $jsonstring, 0, 2);
	}
	// cache for 2 minute as we want yield to update semi-regularly...
    
   	$memcache->delete("$checkrunning");
    return $jsonstring;

}


##Reads per pore - plots the production of reads on a pore by pore basis
function readsperpore($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select count(*) as count, channel from basecalled_template inner join config_general using (basename_id) group by channel order by channel;";

		
			$resultarray;
	
			$template=$mindb_connection->query($sql_template);
		
			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template'][$row['channel']]=$row['count'];
				}
			}
	
		
	
		
	
			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
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
		 $memcache->set("$checkvar", $jsonstring, 0, 2);
	}
		
	// cache for 2 minute as we want yield to update semi-regularly...
   
	         $memcache->delete("$checkrunning");
    return $jsonstring;
		
}


##These two functions are used for mapping channels to the minknow pore layout
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

##active_channels_over_time - determines how many channels are active in a given 60 second period
function active_channels_over_time($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 

$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
			$checking=$mindb_connection->query($checkrow);
			if ($checking->num_rows ==1){
				//echo "We have already run this!";
				foreach ($checking as $row){
					$jsonstring = $row['json'];
				}
			} else {
			$sql_template = "select floor(start_time/60)*60 as bin_floor, count(*) as chan_count from basecalled_template inner join config_general using (basename_id) group by 1 order by 1;";
			$sql_complement = "select floor(start_time/60)*60 as bin_floor, count(*) as chan_count from basecalled_complement inner join config_general using (basename_id) group by 1 order by 1;";
		
			$resultarray;
	
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
			$jsonstring;
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
		$memcache->set("$checkvar", $jsonstring, 0, 2);
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	
	         $memcache->delete("$checkrunning");
    return $jsonstring;

}



##average_time_over_time2 - determines how long reads are taking to be returned over time
function average_time_over_time2($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
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
			$jsonstring;
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
			$memcache->set("$checkvar", $jsonstring, 0, 2);
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	
	         $memcache->delete("$checkrunning");
    return $jsonstring;
}


##Reads_over_time2 - determines the rate of basecalling
function reads_over_time2($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select floor(start_time/60)*60 as bin_floor, count(*) as count from basecalled_template group by 1 order by 1 ;";
			$sql_complement = "select floor(start_time/60)*60 as bin_floor, count(*) as count from basecalled_complement group by 1 order by 1 ;";
		
			$resultarray;
		
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
			$jsonstring;
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
		$memcache->set("$checkvar", $jsonstring, 0, 2);	
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	
	         $memcache->delete("$checkrunning");
    return $jsonstring;
}


##Histogram of Read Depth
function histogrambases($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template_query = "SELECT (FLOOR(length(basecalled_template.sequence)/1000)*1000) as bucket, sum(length(basecalled_template.sequence)) as tempBASES from basecalled_template group by bucket;";			
			$sql_complement_query = "SELECT (FLOOR(length(basecalled_complement.sequence)/1000)*1000) as bucket, sum(length(basecalled_complement.sequence)) as compBASES from basecalled_complement group by bucket;";		
			$sql_2d_query = "SELECT (FLOOR(length(basecalled_2d.sequence)/1000)*1000) as bucket, sum(length(basecalled_2d.sequence)) as d2BASES from basecalled_2d group by bucket;";	
			$sql_template_execute=$mindb_connection->query($sql_template_query);
			$sql_complement_execute=$mindb_connection->query($sql_complement_query);
			$sql_2d_execute=$mindb_connection->query($sql_2d_query);			
			$category2 = array();
			$arraytemplate = array();
			$arraycomplement = array();
			$array2d = array();		
			if ($sql_template_execute->num_rows >= 1) {
				foreach ($sql_template_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$arraytemplate[$row['bucket']]=$row['tempBASES'];
				}	
			}
			if ($sql_complement_execute->num_rows >= 1) {
				foreach ($sql_complement_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$arraycomplement[$row['bucket']]=$row['compBASES'];
				}	
			}
			if ($sql_2d_execute->num_rows >= 1) {
				foreach ($sql_2d_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$array2d[$row['bucket']]=$row['d2BASES'];
				}	
			}
			asort($category2);
			//var_dump($category2);
				
			
			$category = array();
			$category['name'] = 'Size';
	
			$series1 = array();
			$series1['name'] = 'Template';
	
			$series2 = array();
			$series2['name'] = 'Complement';
		
			$series3 = array();
			$series3['name'] = '2d';
				
			foreach ($category2 as $bucket) {
				$category['data'][]=$bucket;
				if (array_key_exists($bucket, $arraytemplate)) {
					$series1['data'][]=$arraytemplate[$bucket];
				}else{
					$series1['data'][]=0;	
				}
				if (array_key_exists($bucket, $arraycomplement)) {
					$series2['data'][]=$arraycomplement[$bucket];
				}else{
					$series2['data'][]=0;	
				}
				if (array_key_exists($bucket, $array2d)) {
					$series3['data'][]=$array2d[$bucket];
				}else{
					$series3['data'][]=0;	
				}
					
			}
			
			//if ($sql_execute->num_rows >=1) {
			//	foreach ($sql_execute as $row){
			//		$category['data'][]= $row['bucket'];
			//	    $series1['data'][] = $row['tempCOUNT'];
			//	    $series2['data'][] = $row['compCOUNT'];
			//	    $series3['data'][] = $row['seq2dCOUNT'];   
			//	}
			//}
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
			$memcache->set("$checkvar", $jsonstring, 0, 2);	
	}	
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}



##Histogram of Read Lengths
function histogram($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template_query = "SELECT (FLOOR(length(basecalled_template.sequence)/1000)*1000) as bucket, COUNT(basecalled_template.sequence) as tempCOUNT from basecalled_template group by bucket;";
			$sql_complement_query = "SELECT (FLOOR(length(basecalled_complement.sequence)/1000)*1000) as bucket, COUNT(basecalled_complement.sequence) as compCOUNT from basecalled_complement group by bucket;";
			$sql_2d_query = "SELECT (FLOOR(length(basecalled_2d.sequence)/1000)*1000) as bucket, COUNT(basecalled_2d.sequence) as d2COUNT from basecalled_2d group by bucket;";
		
			$sql_template_execute=$mindb_connection->query($sql_template_query);
			$sql_complement_execute=$mindb_connection->query($sql_complement_query);
			$sql_2d_execute=$mindb_connection->query($sql_2d_query);
			
			$category2 = array();
			$arraytemplate = array();
			$arraycomplement = array();
			$array2d = array();
				
			if ($sql_template_execute->num_rows >= 1) {
				foreach ($sql_template_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$arraytemplate[$row['bucket']]=$row['tempCOUNT'];
				}	
			}
			if ($sql_complement_execute->num_rows >= 1) {
				foreach ($sql_complement_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$arraycomplement[$row['bucket']]=$row['compCOUNT'];
				}	
			}
			if ($sql_2d_execute->num_rows >= 1) {
				foreach ($sql_2d_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];	
					}
					$array2d[$row['bucket']]=$row['d2COUNT'];
				}	
			}
			asort($category2);
			//var_dump($arraytemplate);
					
					
			$category = array();
			$category['name'] = 'Size';
	
			$series1 = array();
			$series1['name'] = 'Template';
		
			$series2 = array();
			$series2['name'] = 'Complement';
	
			$series3 = array();
			$series3['name'] = '2d';
				
			foreach ($category2 as $bucket) {
				$category['data'][]=$bucket;
				if (array_key_exists($bucket, $arraytemplate)) {
					$series1['data'][]=$arraytemplate[$bucket];
				}else{
					$series1['data'][]=0;	
				}
				if (array_key_exists($bucket, $arraycomplement)) {
					$series2['data'][]=$arraycomplement[$bucket];
				}else{
					$series2['data'][]=0;	
				}
				if (array_key_exists($bucket, $array2d)) {
					$series3['data'][]=$array2d[$bucket];
				}else{
					$series3['data'][]=0;	
				}
			}
		
			//if ($sql_execute->num_rows >=1) {
			//	foreach ($sql_execute as $row){
			//		$category['data'][]= $row['bucket'];
			//	    $series1['data'][] = $row['tempCOUNT'];
			//	    $series2['data'][] = $row['compCOUNT'];
			//	    $series3['data'][] = $row['seq2dCOUNT'];   
			//	}
			//}

			$result = array();
			array_push($result,$category);
			array_push($result,$series1);
			array_push($result,$series2);
			array_push($result,$series3);
	
			$jsonstring = json_encode($result, JSON_NUMERIC_CHECK);
			//$jsonstring = json_encode($result);
			//$jsonstring = '[{"name":"Month","data":["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]},{"name":"Wordpress","data":[4,5,6,2,5,7,2,1,6,7,3,4]},{"name":"CodeIgniter","data":[5,2,3,6,7,1,2,6,6,4,6,3]},{"name":"Highcharts","data":[7,8,9,6,7,10,9,7,6,9,8,4]}] ';
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		   $memcache->set("$checkvar", $jsonstring, 0, 2);
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;

}





##Average Depth of Coverage - primitive depth calculator
function depthcoverage($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
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
		
		
			$covarray;
		
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
			$jsonstring;
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
			$memcache->set("$checkvar", $jsonstring, 0, 2);
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}



##Percentage of Reference with Read - primitive coverage calculator based on total coverage of all sequences being aligned too.
function percentcoverage($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
			$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
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
		
		
			$covarray;
		
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
			
			$jsonstring;
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
			$memcache->set("$checkvar", $jsonstring, 0, 2);
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
	
}


##Read Number - generates the numbers of reads for each read type:

function readnumber($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
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
		//echo $sql_template;
		$resultarray;
		
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
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring;
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
			$memcache->set("$checkvar", $jsonstring, 0, 2);
	}	
	// cache for 10 seconds as we want yield to update regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}

##MaxLen - generates max length of read data for plots:

function maxlen($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
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
		
			$resultarray;
	
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
			$jsonstring;
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
		 $memcache->set("$checkvar", $jsonstring, 0, 2);
	}

// cache for 10 seconds as we want yield to update regularly...
   
   	         $memcache->delete("$checkrunning");
    return $jsonstring;
}		


##AveLen - generates average length data for plot:

function avelen($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
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
		
			$resultarray;
	
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
			
	
			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
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
		    $memcache->set("$checkvar", $jsonstring, 0, 2);
	}
	
	// cache for 10 seconds as we want yield to update regularly...

		
	         $memcache->delete("$checkrunning");
    return $jsonstring;
	

}

##Volume - generates the Yield summary plots:

function bases($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
		$memcache->set("$checkrunning", "YES", 0, 0); 
    	$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		
		
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
		
			$sql_template = "SELECT sum(length(sequence)) as bases FROM basecalled_template;";
			$sql_complement = "SELECT sum(length(sequence)) as bases FROM basecalled_complement;";
			$sql_2d = "SELECT sum(length(sequence)) as bases FROM basecalled_2d;";
		
			$resultarray;
	
			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);
			$read2d=$mindb_connection->query($sql_2d);
				
			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template']=$row['bases'];
				}
			}
			if ($complement->num_rows >= 1){
				foreach ($complement as $row) {
					$resultarray['complement']=$row['bases'];
				}
			}
			if ($read2d->num_rows >= 1){
				foreach ($read2d as $row) {
					$resultarray['2d']=$row['bases'];
				}
			}
			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring .   "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring .   "{\n";
				$jsonstring = $jsonstring .   "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring .  "\"data\": [";
				$jsonstring = $jsonstring .   "$value,";
				$jsonstring = $jsonstring .  "]\n},\n";
			}
			
			
			$jsonstring = $jsonstring .  "]\n";
	
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		$memcache->set("$checkvar", $jsonstring, 0, 2);
	}
		

		
		
	// cache for 10 seconds as we want yield to update regularly...
    
		
	         $memcache->delete("$checkrunning");
    return $jsonstring;
	
	

}

?>