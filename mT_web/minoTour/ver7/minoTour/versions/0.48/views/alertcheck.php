
<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
require_once("includes/functions.php");
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
    
	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	// Connection creation
	$memcache = new Memcache;
	#$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT) or die ("Memcached Failure");
	$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);
    
    $checkalertrunning = $memcache->get("alertcheckrunning");

	if (!$db_connection->connect_errno) {
		
			//echo "<script src=\"js/jquery-1.10.2.js\"></script>
			//    <script src=\"js/bootstrap.min.js\"></script>
			  //  <script src=\"js/plugins/metisMenu/jquery.metisMenu.js\"></script>";
			//echo"<script type=\"text/javascript\" src=\"../js/pnotify.custom.min.js\"></script>
			 //   <script type=\"text/javascript\">
			//	PNotify.prototype.options.styling = \"fontawesome\";
			//	</script>";
			
			$getruns = "SELECT runname FROM minIONruns inner join userrun using (runindex) inner join users using (user_id) where users.user_name = '" . $_SESSION['user_name'] ."' and activeflag = 1;";
			$getthemruns = $mindb_connection->query($getruns);
			foreach ($getthemruns as $row){
				$databases[] = $row['runname'];
			}
			
			//$_SESSION['activerunarray']=$databases;

			if ($getthemruns->num_rows == 1){
				echo "<small>1 active run.</small><br>";
			}else{
				echo "<small>" . $getthemruns->num_rows . " active runs.</small><br>";
			}
			//if (isset($_SESSION['active_run_name'])) {
			 //	echo "<small><i class='fa fa-fire'></i>" . $_SESSION['active_run_name'] . "</small><br>"; 
			//}
			//NEED TO CHECK IF BOTH VALUES ARE ARRAYS - IF THEY AREN'T THEN THAT TELLS US SOMETHING.
			if (isset($databases)  && isset($_SESSION['activerunarray'])){
				$runsadded = array_diff($databases, $_SESSION['activerunarray']);
			}else if (isset($databases)){
				$runsadded = $databases;
			}
			if (isset($databases)  && isset($_SESSION['activerunarray'])){
				$runsfinished = array_diff($_SESSION['activerunarray'],$databases);
			}else if (isset ($_SESSION['activerunarray'])) {
				$runsfinished = $_SESSION['activerunarray'];	
			}
			$_SESSION['activerunarray'] = $databases;
			
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
				echo "<small>$activealerts alerts running.</small><br>";
				echo "<small>$completedalerts alerts completed.</small><br>";
			}
			

			//NEED TO RESET THE POINTER FOR GETTHEMALERTS!
			if (count($databases)>=1){
				foreach ($databases as $dbname){
					//echo $dbname . "<br>";
					if (isset ($databases)){
						$getalerts = "SELECT name,reference,threshold,alert_index,twitterhandle,type FROM " . $dbname . ".alerts where complete = 0;";
						$getthemalerts2 = $mindb_connection->query($getalerts);
						//var_dump ($getthemalerts2);
						if ($getthemalerts2->num_rows>=1){
							foreach ($getthemalerts2 as $row){
								$jobstodo[] = array(
								    'job' => $row['name'],
									'threshold' => $row['threshold'],
									'jobid' => $row['alert_index'],
									'twitterhandle' => $row['twitterhandle'],
									'type' =>$row['type'],
									'database' => $dbname,
									'reference' => $row['reference'],
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
				
				//echo $index['job'] . "<br>";
				
				if ($index['job'] == "gencoverage"){
					#$sql_template = "select avg(count) as coverage, refname from (SELECT count(*) as count,refname FROM " . $index['database'] . ".last_align_basecalled_template inner join " . $index['database'] . ".reference_seq_info using (refid) where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x;";
					$sql_template = "SELECT avg(A+T+G+C) as coverage, refname FROM " . $index['database'] . ".reference_coverage_" . $index['type'] . " inner join " . $index['database'] . ".reference_seq_info where ref_id = refid group by ref_id;";
					//$sql_template = "SELECT avg(A+T+G+C) as coverage, refname FROM " . $index['database'] . ".reference_coverage_template inner join " . $index['database'] . ".reference_seq_info where ref_id = refid group by ref_id;";
					//$sql_complement = "SELECT avg(A+T+G+C) as coverage, refname FROM " . $index['database'] . ".reference_coverage_complement inner join " . $index['database'] . ".reference_seq_info where ref_id = refid group by ref_id;";
					//$sql_2d = "SELECT avg(A+T+G+C) as coverage, refname FROM " . $index['database'] . ".reference_coverage_2d inner join " . $index['database'] . ".reference_seq_info where ref_id = refid group by ref_id;";
					//$sql_complement = "select avg(count) as coverage, refname from (SELECT count(*) as count,refname FROM " . $index['database'] , ".last_align_basecalled_complement inner join " . $index['database'] , ".reference_seq_info using (refid) where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x;";
					//$sql_2d = "select avg(count) as coverage, refname from (SELECT count(*) as count,refname FROM " . $index['database'] , ".last_align_basecalled_2d inner join " . $index['database'] , ".reference_seq_info using (refid) where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x;";
					$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				
					$template=$mindb_connection->query($sql_template);
					//$complement=$mindb_connection->query($sql_complement);
					//$twod=$mindb_connection->query($sql_2d);
					//$complement=$mindb_connection->query($sql_complement);
					//$read2d=$mindb_connection->query($sql_2d);
					if ($template->num_rows >= 1){
						foreach ($template as $row) {
							if ($row['coverage']>=$index['threshold']) {
								echo"<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
		    								title: 'Coverage Alert!',
								    		text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand of ".$row['refname'].".',
		    								type: 'success',
								    		hide: false
											});";
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
												    CURLOPT_URL => 'http://www.nottingham.ac.uk/~plzloose/minoTourhome/tweet.php?' .$postData ,
												    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
												));
												// Send the request & save response to $resp
												$resp = curl_exec($curl);
												// Close request to clear up some resources
												curl_close($curl);
											}
											echo "</script>";
											$resetalert="update " . $index['database'] .".alerts set complete = 1 where alert_index = " . $index['jobid'] . ";";
											//echo $resetalert;
											$resetalerts=$mindb_connection->query($resetalert);
											
										}
						}
					}
					
				
				
				}
				if ($index['job'] == "barcodecoverage"){
					$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
					//$barcodecheck = "SELECT barcodeid from barcode_control where complete=0;";
					//$barcodes = $mindb_connection->query($barcodecheck);
					
					//Fetch the coverage depth for the barcode of interest
					$barcov = "select ref_id, avg(count) as avecount from (SELECT ref_id, (A+T+G+C) as count, ref_pos FROM reference_coverage_barcode_2d) as refcounts where ref_id like \"%" . $index['reference'] . "\" group by ref_id;";
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
									
							$updatesql = "update barcode_control set complete = 1 where barcodeid = \"".$index['reference'] ."\";"; 
							//echo $updatesql . "\n";
							$updatesqlexecute=$mindb_connection->query($updatesql);
							echo"<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
		    								title: 'Barcode Coverage Alert!',
								    		text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand for barcode  ".$index['reference'].".',
		    								type: 'success',
								    		hide: false
											});";
											if (isset($_SESSION['twittername'])) {
												//echo "alert ('tryingtotweet');";
												$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for barcode ".$index['reference'];
												$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
												//echo "alert ('".$postData."');";
												


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
											}
											echo "</script>";
							$resetalert="update alerts set complete = 1 where alert_index = " . $index['jobid'] . ";";
							//echo $resetalert;
							$resetalerts=$mindb_connection->query($resetalert);
						}else{
						//here we might be able to set the global job as complete.	
						}
					}
				}
				
				if ($index['job'] == "genbarcodecoverage") {
					//This task is to identify which barcodes have been sequenced to a given level of coverage and then reject those barcodes from furthre sequencing. It will therefore calculate mean coverage and then set a list of barcodes to reject.	
					//FIrst get the barcodes that need to be checked
					$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
					$barcodecheck = "SELECT barcodeid from barcode_control where complete=0;";
					$barcodes = $mindb_connection->query($barcodecheck);
					
					//Fetch the coverage depth for each barcode:
					$barcov = "select ref_id, avg(count) as avecount from (SELECT ref_id, (A+T+G+C) as count, ref_pos FROM reference_coverage_barcode_2d) as refcounts group by ref_id;";
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
									
									$updatesql = "update barcode_control set complete = 1 where barcodeid = \"".$row['barcodeid'] ."\";"; 
									//echo $updatesql . "\n";
									$updatesqlexecute=$mindb_connection->query($updatesql); 
									echo"<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
		    								title: 'Global Barcode Coverage Alert!',
								    		text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand for barcode  ".$row['barcodeid'].".',
		    								type: 'success',
								    		hide: false
											});";
											if (isset($_SESSION['twittername'])) {
												//echo "alert ('tryingtotweet');";
												$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for barcode ".$row['barcodeid'];
												$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
												//echo "alert ('".$postData."');";
												


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
											}
											echo "</script>";
									
								}
							}
						}							
					
					}else{
						echo"<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
		    								title: 'Global Barcode Coverage Alert!',
								    		text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand for all barcodes.',
		    								type: 'success',
								    		hide: false
											});";
											if (isset($_SESSION['twittername'])) {
												//echo "alert ('tryingtotweet');";
												$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for all barcodes.";
												$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
												//echo "alert ('".$postData."');";
												


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
											}
											echo "</script>";
							$resetalert="update alerts set complete = 1 where alert_index = " . $index['jobid'] . ";";
							//echo $resetalert;
							$resetalerts=$mindb_connection->query($resetalert);	
					}
					
				}
				
				if ($index['job'] == "basenotification"){
					
					$sql_template = "SELECT sum(length(sequence)) as bases FROM basecalled_" . $index['type'] . ";";
					
					//$sql_template = "SELECT sum(length(sequence)) as bases FROM basecalled_template;";
					//$sql_complement = "SELECT sum(length(sequence)) as bases FROM basecalled_complement;";
					//$sql_2d = "SELECT sum(length(sequence)) as bases FROM basecalled_2d;";
					$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				
					$template=$mindb_connection->query($sql_template);
					//$complement=$mindb_connection->query($sql_complement);
					//$read2d=$mindb_connection->query($sql_2d);
					if ($template->num_rows >= 1) {
						foreach ($template as $row) {
							if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']) >=1 ){
								//echo (($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']). "<br>";
									//echo $_SESSION['basecoveragetemp'];
								//if (isset $_SESSION['basecoverage']){
									$checkrunning=$index['database'] . "basecoverage" . $index['type'];
									//echo $checkrunning;
									$basecoveragetemp = $memcache->get("$checkrunning");
									$checkname = "basecoverage" . $index['type'];
									if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold'])>$_SESSION[$checkname]){
										echo "<script type=\"text/javascript\" id=\"runscript\">
											new PNotify({
												title: 'Sequencing Alert!',
												text: 'You\'ve sequenced ". ($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases for run ".cleanname($index['database'])." on the ". $index['type'] . " strand.',
												type: 'info',
												
												});";
											if (isset($_SESSION['twittername']) && ($index['threshold'] >= 500000)) {
												//echo "alert ('tryingtotweet');";
												$message = "Sequenced ".($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases on ". $index['type'];
												$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($index['database']))) ."&message=" . (urlencode($message));
												//echo "alert ('".$postData."');";
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
											}
											echo "</script>";
											$_SESSION[$checkname]=(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']);
											$memcache->set("$checkrunning", (($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']));
									}
									//}else{
									
									
								//}
								$_SESSION[$checkname]=((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']));
								$memcache->set("$checkrunning", (($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']));
							}
							
						}
					}
					
				}				
			}
		}
			//var_dump ($jobstodo);
			if (isset ($runsadded)){
				//print "Runs added:";
				foreach ($runsadded as $value){
					echo"<script type=\"text/javascript\" id=\"runscript\">
							new PNotify({
    							title: 'New Run Started',
						    	text: '".cleanname($value)." has been started.',
    							type: 'info',
						    	hide: false
							});
					
						</script>";
						if (isset($_SESSION['twittername'])) {
							$message = "run started.";
							$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($value))) ."&message=" . (urlencode($message));
							//echo "alert ('".$postData."');";
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
						}
						$postData = "userid=" . (urlencode($_SESSION['user_name'])) . "&runname=". (urlencode(cleanname($value)));
						//echo $postData;
						$curl = curl_init();
						// Set some options - we are passing in a useragent too here
						curl_setopt_array($curl, array(
						    CURLOPT_RETURNTRANSFER => 1,
						    CURLOPT_URL => 'http://www.nottingham.ac.uk/~plzloose/minoTourhome/runstart.php?' . $postData,
						    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
						));
						// Send the request & save response to $resp
						$resp = curl_exec($curl);
						// Close request to clear up some resources
						curl_close($curl);
					//print $value . "<br>";
				}
			}
			if (isset ($runsfinished)){
				//print "Runs deleted:";
				foreach ($runsfinished as $value2){
				 	//print $value2 . "<br>";
					echo"<script type=\"text/javascript\" id=\"runscript\">
							new PNotify({
    							title: 'Run Finished.',
						    	text: '" .cleanname($value2) . " has been completed.',
    							type: 'info',
						    	hide: false
							});
					
						</script>";
						if (isset($_SESSION['twittername'])) {
							$message = "run finished.";
							$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($value2))) ."&message=" . (urlencode($message));
							//echo "alert ('".$postData."');";
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
						
						}
						// Get cURL resource
						$curl = curl_init();
						$postData = "userid=" . (urlencode($_SESSION['user_name'])) . "&runname=". (urlencode(cleanname($value2)));
						//echo $postData;
						// Set some options - we are passing in a useragent too here
						curl_setopt_array($curl, array(
						    CURLOPT_RETURNTRANSFER => 1,
						    CURLOPT_URL => 'http://www.nottingham.ac.uk/~plzloose/minoTourhome/runfinished.php?' .$postData ,
						    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
						));
						// Send the request & save response to $resp
						$resp = curl_exec($curl);
						// Close request to clear up some resources
						curl_close($curl);
				}
			
			}
			
		}

		
}


?>