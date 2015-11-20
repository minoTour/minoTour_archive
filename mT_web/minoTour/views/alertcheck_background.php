<?php
error_reporting(0);

$lines = file('mT_param.conf');
foreach ($lines as $line_num => $line) {
	$line = str_replace("\n", '', $line);
    $fragments = explode("=", $line);
    if ($fragments[0] == "directory") {
		$directory = $fragments[1];
    }
}

require_once($directory . "includes/functions.php");
require_once($directory ."config/db.php");

if (strlen(consumerkey) >= 1 && strlen(consumersecret) >= 1 && strlen(accesstoken) >= 1 && strlen(accesssecret)){
	$url = $_SERVER['REQUEST_URI']; //returns the current URL
	$parts = explode('/',$url);
	array_pop($parts);
	$thang = implode('/',$parts);
	$dir = $_SERVER['SERVER_NAME'];
	$twiturl = "http://" . $dir . $thang . "/twitmino/";
}else {
	$twiturl = "http://www.nottingham.ac.uk/~plzloose/minoTourhome/";
}

$memcache = new Memcache;
$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);

$checkalertrunning = $memcache->get("alertcheckrunning");
if ($checkalertrunning > 0) {
	#echo "Not going to tweet now!\n";
	#echo $checkalertrunning . "\n";
}else{
	#echo "Trying to send a tweet\n";
    $messagearray = array("minoTour is alive","All's well with the minoTour","I wonder what day it is? Anyway, minoTour reporting for duty.","I'm a-ok right now","Baxter, is that you?","I love lamp","minoTour checking in","OK so I am a robot.","I fancy a little Jazz flute right now.","Phew - I am pretty busy right now.","Baxter?","Give me an M");
    $message = $messagearray[array_rand($messagearray)];
	$postData = "message=" . (urlencode($message));
	// Get cURL resource
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		//CURLOPT_URL => 'http://www.nottingham.ac.uk/~plzloose/minoTourhome/globaltweet.php?' .$postData ,
		CURLOPT_URL => $twiturl . 'globaltweet.php?' .$postData ,
		CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));
	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
    $memcache->set('alertcheckrunning', '1', 0, 3600);
}


$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if (!$mindb_connection->connect_errno) {
	$getruns = "SELECT runname FROM minIONruns where activeflag = 1;";
	$getthemruns = $mindb_connection->query($getruns);
	foreach ($getthemruns as $row){
		$databases[] = $row['runname'];
		//echo $row['runname'] . "<br>";
	}
	$activealerts = 0;
	$completedalerts = 0;
	if (isset ($databases)){
		foreach ($databases as $dbname){
			$getalerts = "SELECT * FROM " . $dbname . ".alerts where complete = 0;";
			$getcompletealerts = "SELECT * FROM " . $dbname . ".alerts where complete = 1;";
			$getthemalerts = $mindb_connection->query($getalerts);
			$getthemcompletealerts = $mindb_connection->query($getcompletealerts);
			$activealerts = $activealerts + $getthemalerts->num_rows;
			$completedalerts = $completedalerts + $getthemcompletealerts->num_rows;
		}
		//echo "<small>$activealerts alerts running.</small><br>";
		//echo "<small>$completedalerts alerts completed.</small><br>";
	}


	//NEED TO RESET THE POINTER FOR GETTHEMALERTS!
	if (count($databases)>=1){
		foreach ($databases as $dbname){
			//echo $dbname . "<br>";
			if (isset ($databases)){
				$getalerts = "SELECT name,reference,username,threshold,alert_index,twitterhandle,type,start,end,control FROM " . $dbname . ".alerts where complete = 0;";
				$getthemalerts2 = $mindb_connection->query($getalerts);
				//echo "Num rows is " . $getthemalerts2->num_rows . "\n";
				if ($getthemalerts2->num_rows){
					foreach ($getthemalerts2 as $row){
						$jobstodo[] = array(
						    'job' => $row['name'],
						    'reference' => $row['reference'],
						    'username' => $row['username'],
						    'start' => $row['start'],
						    'end' => $row['end'],
						    'control' => $row['control'],
							'threshold' => $row['threshold'],
							'jobid' => $row['alert_index'],
							'twitterhandle' => $row['twitterhandle'],
							'type' =>$row['type'],
							'database' => $dbname,
						  );
					}
				}
			}
		}
	}
	//echo '<pre>';
	//print_r($jobstodo);
	//echo '</pre>';
	if (isset ($jobstodo)){
		foreach ($jobstodo as $index) {
			//echo $index['database'] . "\t" . $index['twitterhandle'] . "\t" . $index['type'] . "\t" . $index['job'] . "\t" . $index['threshold'] . "\t" . "\n";
			if ($index['job'] == "gencoverage"){
				#$sql_template = "select avg(count) as coverage, refname from (SELECT count(*) as count,refname FROM " . $index['database'] . ".last_align_basecalled_template inner join " . $index['database'] . ".reference_seq_info using (refid) where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x;";
				$sql_template = "SELECT avg(A+T+G+C) as coverage, refname FROM " . $index['database'] . ".reference_coverage_" . $index['type'] . " inner join " . $index['database'] . ".reference_seq_info where ref_id = refid group by ref_id;";
				//echo $sql_template . "\n";
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				$template=$mindb_connection->query($sql_template);
				//$complement=$mindb_connection->query($sql_complement);
				//$read2d=$mindb_connection->query($sql_2d);
				if ($template->num_rows >= 1){
					foreach ($template as $row) {
						if ($row['coverage']>=$index['threshold'] && strlen($index['twitterhandle']) > 0) {
							$message = "Coverage >=".$index['threshold']."X on ".$index['type'] ." for ".$row['refname'];
							$postData = "twitteruser=" . $index['twitterhandle'] . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
							// Get cURL resource
							$curl = curl_init();
							// Set some options - we are passing in a useragent too here
							curl_setopt_array($curl, array(
								CURLOPT_RETURNTRANSFER => 1,
								CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
								CURLOPT_USERAGENT => 'Codular Sample cURL Request'
							));
							// Send the request & save response to $resp
							$resp = curl_exec($curl);
							// Close request to clear up some resources
							curl_close($curl);
							$resetalert="update " . $index['database'] .".alerts set complete = 1 where alert_index = " . $index['jobid'] . ";";
							$resetalerts=$mindb_connection->query($resetalert);
						}
					}
				}
			}
            if ($index['job'] == "referencecoverage"){
                $sql_template;
                if ($index['start']==0 && $index['end']==0) {
                    $sql_template = "SELECT avg(A+T+G+C) as coverage, refname FROM " . $index['database'] . ".reference_coverage_" . $index['type'] . " inner join " . $index['database'] . ".reference_seq_info where ref_id = refid group by ref_id;";
                }else{
                    $sql_template = "SELECT avg(A+T+G+C) as coverage, refname FROM " . $index['database'] . ".reference_coverage_" . $index['type'] . " inner join " . $index['database'] . ".reference_seq_info where ref_id = refid  and ref_pos >= ".$index['start']." and ref_pos <= ".$index['end']." group by ref_id;";
                }
                $mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
                $template=$mindb_connection->query($sql_template);
                if ($template->num_rows >= 1){
                    foreach ($template as $row) {
                        if ($row['coverage']>=$index['threshold']) {
                            if ($index['control'] > 0) {
                                $command = "insert into interaction (instruction,target,complete) VALUES ('stop','all','0');";
                                $sqlcommand = $mindb_connection->query($command);


                                if (isset($_SESSION['twittername'])) {
                                            //echo "alert ('tryingtotweet');";
                                    $message = "RUN STOPPED: Coverage >=".$index['threshold']."X on " . $index['type'] . " for ".$row['refname'];
                                    $postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
                                //echo "alert ('".$postData."');";
                                // Get cURL resource
                                    $curl = curl_init();
                                // Set some options - we are passing in a useragent too here
                                    curl_setopt_array($curl, array(
                                    CURLOPT_RETURNTRANSFER => 1,
                                    CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
                                    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
                                    ));
                                    // Send the request & save response to $resp
                                    $resp = curl_exec($curl);
                                    // Close request to clear up some resources
                                    curl_close($curl);
                                }
                            }else{


                                if (isset($_SESSION['twittername'])) {
                                            //echo "alert ('tryingtotweet');";
                                    $message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for ".$row['refname'];
                                    $postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
                                //echo "alert ('".$postData."');";
                                // Get cURL resource
                                    $curl = curl_init();
                                // Set some options - we are passing in a useragent too here
                                    curl_setopt_array($curl, array(
                                    CURLOPT_RETURNTRANSFER => 1,
                                    CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
                                    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
                                    ));
                                    // Send the request & save response to $resp
                                    $resp = curl_exec($curl);
                                    // Close request to clear up some resources
                                    curl_close($curl);
                                }
                            }
                            $resetalert="update " . $index['database'] .".alerts set complete = 1 where alert_index = " . $index['jobid'] . ";";
                            //echo $resetalert;
                            $resetalerts=$mindb_connection->query($resetalert);
                        }
                    }
                }
            }

			if ($index['job'] == "barcodecoverage"){
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				//Fetch the coverage depth for the barcode of interest
				$barcov = "select ref_id, avg(count) as avecount from (SELECT ref_id, (A+T+G+C) as count, ref_pos FROM " . $index['database'] . ".reference_coverage_barcode_2d) as refcounts where ref_id like \"%" . $index['reference'] . "\" group by ref_id;";
				//echo $barcov . "\n";
				$barcovquery = $mindb_connection->query($barcov);
				$barcodlookup=[];
				if ($barcovquery->num_rows >= 1) {
					foreach ($barcovquery as $row) {
						$referenceids = explode("_", $row['ref_id']);
						$barcodlookup[$reflookup[$referenceids[0]]][$referenceids[1]]=$row['avecount'];
						#print $reflookup[$referenceids[0]] . " " . $referenceids[1] . " " . $row['avecount'] . "\n";
					}
				}
				//echo "Here?";
				foreach ($barcodlookup as $key=>$value) {
					//echo $value[$index['reference']] . "\n";
					if ($value[$index['reference']] >= $index['threshold']){
						//Here we update the row in the table to set the barcode to complete
						$updatesql = "update " . $index['database'] . ".barcode_control set complete = 1 where barcodeid = \"".$index['reference'] ."\";";
						//echo $updatesql . "\n";
						$updatesqlexecute=$mindb_connection->query($updatesql);
						//echo "alert ('tryingtotweet');";
						$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for barcode ".$index['reference'];
						$postData = "twitteruser=" . $index['twitterhandle'] . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
						//echo "alert ('".$postData."');";
						// Get cURL resource
						$curl = curl_init();
						// Set some options - we are passing in a useragent too here
						curl_setopt_array($curl, array(
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
							CURLOPT_USERAGENT => 'Codular Sample cURL Request'
						));
						// Send the request & save response to $resp
						$resp = curl_exec($curl);
						// Close request to clear up some resources
						curl_close($curl);
						$resetalert="update " . $index['database'] . ".alerts set complete = 1 where alert_index = " . $index['jobid'] . ";";
						//echo $resetalert;
						$resetalerts=$mindb_connection->query($resetalert);
					}else{
					//here we might be able to set the global job as complete.
					}
				}
			}



			if ($index['job'] == "genbarcodecoverage") {
				//This task is to identify which barcodes have been sequenced to a given level of coverage and then reject those barcodes from further sequencing. It will therefore calculate mean coverage and then set a list of barcodes to reject.
				//FIrst get the barcodes that need to be checked
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				$barcodecheck = "SELECT barcodeid from barcode_control where complete=0;";
				$barcodes = $mindb_connection->query($barcodecheck);
				//Fetch the coverage depth for each barcode:
				$barcov = "select ref_id, avg(count) as avecount from (SELECT ref_id, (A+T+G+C) as count, ref_pos FROM " . $index['database'] . ".reference_coverage_barcode_2d) as refcounts group by ref_id;";
				$barcovquery = $mindb_connection->query($barcov);
				$barcodlookup=[];
				if ($barcovquery->num_rows >= 1) {
					foreach ($barcovquery as $row) {
						$referenceids = explode("_", $row['ref_id']);
						$barcodlookup[$reflookup[$referenceids[0]]][$referenceids[1]]=$row['avecount'];
						#print $reflookup[$referenceids[0]] . " " . $referenceids[1] . " " . $row['avecount'] . "\n";
					}
				}
				if ($barcodes->num_rows >= 1) {
					foreach ($barcodlookup as $key=>$value){
						foreach ($barcodes as $row){
							//echo $value[$row['barcodeid']] . "!";
							if ($value[$row['barcodeid']] >= $index['threshold']){
								//Here we update the row in the table to set the barcode to complete

								$updatesql = "update " . $index['database'] . ".barcode_control set complete = 1 where barcodeid = \"".$row['barcodeid'] ."\";";
								//echo $updatesql . "\n";
								$updatesqlexecute=$mindb_connection->query($updatesql);
								//echo "alert ('tryingtotweet');";
								$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for barcode ".$row['barcodeid'];
								$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
								//echo "alert ('".$postData."');";
								// Get cURL resource
								$curl = curl_init();
								// Set some options - we are passing in a useragent too here
								curl_setopt_array($curl, array(
									CURLOPT_RETURNTRANSFER => 1,
									CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
									CURLOPT_USERAGENT => 'Codular Sample cURL Request'
								));
								// Send the request & save response to $resp
								$resp = curl_exec($curl);
								// Close request to clear up some resources
								curl_close($curl);
							}
						}
					}

				}else{
					//echo "alert ('tryingtotweet');";
					if ($index['control'] > 0) {
							$command = "insert into " . $index['database'] . ".interaction (instruction,target,complete) VALUES ('stop','all','0');";
					    	$sqlcommand = $mindb_connection->query($command);
					}
					if ($index['control'] == 0){
						$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for all barcodes.";
					}else{
						$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for all barcodes and your run has been stopped.";
					}
					$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
					//echo "alert ('".$postData."');";
					// Get cURL resource
					$curl = curl_init();
					// Set some options - we are passing in a useragent too here
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
						CURLOPT_USERAGENT => 'Codular Sample cURL Request'
					));
					// Send the request & save response to $resp
					$resp = curl_exec($curl);
					// Close request to clear up some resources
					curl_close($curl);
					$resetalert="update " . $index['database'] . ".alerts set complete = 1 where alert_index = " . $index['jobid'] . ";";
					//echo $resetalert;
					$resetalerts=$mindb_connection->query($resetalert);
				}
			}





			if ($index['job'] == "basenotification"){
				$sql_template = "SELECT sum(length(sequence)) as bases FROM basecalled_" . $index['type'] . ";";
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				$template=$mindb_connection->query($sql_template);
				if ($template->num_rows >= 1) {
					foreach ($template as $row) {
						if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']) >=1 ){
							$checkrunning=$index['database'] . "basecoverage" . $index['type'];
							//echo $checkrunning;
							$basecoveragetemp = $memcache->get("$checkrunning");
							if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold'])>$basecoveragetemp){
								if (isset($index['twittername']) && ($index['threshold'] >= 500000)) {
									$message = "Sequenced ".($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases on " . $index['type'];
									$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
									$curl = curl_init();
									// Set some options - we are passing in a useragent too here
									curl_setopt_array($curl, array(
									    CURLOPT_RETURNTRANSFER => 1,
									    CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
									    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
									));
									// Send the request & save response to $resp
									$resp = curl_exec($curl);
									// Close request to clear up some resources
									curl_close($curl);
								}
								$memcache->set("$checkrunning", (($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']));
							}
						}
					}
				}
			}
		}
	}
}


?>
