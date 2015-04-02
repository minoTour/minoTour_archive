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

//require_once("../includes/functions.php");
// include the configs / constants for the database connection
require_once($directory ."config/db.php");

//if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
    
    //As user is logged in, we can now look at the memcache to retrieve data from here and so reduce the load on the mySQL server
	// Connection creation
	$memcache = new Memcache;
	#$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT) or die ("Memcached Failure");
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
			CURLOPT_URL => 'http://www.nottingham.ac.uk/~plzloose/minoTourhome/globaltweet.php?' .$postData ,
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
						$getalerts = "SELECT name,threshold,alert_index,twitterhandle,type FROM " . $dbname . ".alerts where complete = 0;";
						$getthemalerts2 = $mindb_connection->query($getalerts);
						//echo "Num rows is " . $getthemalerts2->num_rows . "\n";
						if ($getthemalerts2->num_rows){
							foreach ($getthemalerts2 as $row){
								$jobstodo[] = array(
								    'job' => $row['name'],
									'threshold' => $row['threshold'],
									'jobid' => $row['alert_index'],
									'twitterhandle' => $row['twitterhandle'],
									'type' =>$row['type'],
									'database' => $dbname,
								  );
//								$jobstodo[]['job']=$row['name'];
//								$jobstodo[]['threshold']=$row['threshold'];
//								$jobstodo[]['jobid']=$row['alert_index'];
//								$jobstodo[]['database']=$dbname;
								//echo $row['name'] . '<br>';
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
								$message = "Coverage >=".$index['threshold']."X on template for ".$row['refname'];
								$postData = "twitteruser=" . $index['twitterhandle'] . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
								// Get cURL resource
								$curl = curl_init();
								// Set some options - we are passing in a useragent too here
								curl_setopt_array($curl, array(
									CURLOPT_RETURNTRANSFER => 1,
									CURLOPT_URL => 'http://www.nottingham.ac.uk/~plzloose/minoTourhome/tweet.php?' .$postData ,
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
				if ($index['job'] == "basenotification"){
					
					$sql_template = "SELECT sum(length(sequence)) as bases FROM basecalled_" . $index['type'] . ";";
					
					$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				
					$template=$mindb_connection->query($sql_template);
					
					if ($template->num_rows >= 1) {
//						echo $index['threshold'];
//						echo (($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']);
						foreach ($template as $row) {
							if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']) >=1 ){
									$checkrunning=$index['database'] . "basecoverage" . $index['type'];
									//echo $checkrunning;
									$basecoveragetemp = $memcache->get("$checkrunning");
									if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold'])>$basecoveragetemp){
										if (isset($index['twittername']) && ($index['threshold'] >= 500000)) {
											$message = "Sequenced ".($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases on template";
											$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
											$curl = curl_init();
											// Set some options - we are passing in a useragent too here
											curl_setopt_array($curl, array(
											    CURLOPT_RETURNTRANSFER => 1,
											    CURLOPT_URL => 'http://www.nottingham.ac.uk/~plzloose/minoTourhome/tweet.php?' .$postData ,
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