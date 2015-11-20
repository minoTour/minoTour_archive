
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

    $webnotify = 1;

	if (!$mindb_connection->connect_errno) {

			if (strlen(consumerkey) >= 1 && strlen(consumersecret) >= 1 && strlen(accesstoken) >= 1 && strlen(accesssecret)){
				#echo "Yay!";
				$url = $_SERVER['REQUEST_URI']; //returns the current URL
				$parts = explode('/',$url);
				array_pop($parts);
				$thang = implode('/',$parts);
				$dir = $_SERVER['SERVER_NAME'];
				$twiturl = "http://" . $dir . $thang . "/twitmino/";

			}else {
				$twiturl = "http://www.nottingham.ac.uk/~plzloose/minoTourhome/";
			}

            $databases = array();
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
					if (is_object($getthemalerts)){
						$activealerts = $activealerts + $getthemalerts->num_rows;
					}else{
						$activealerts = $activealerts;
					}
					if (is_object($getthemcompletealerts)){
						$completedalerts = $completedalerts + $getthemcompletealerts->num_rows;
					}else{
						$completedalerts = $completedalerts;
					}

				}
				echo "<small>$activealerts alerts running.</small><br>";
				echo "<small>$completedalerts alerts completed.</small><br>";
			}


			//NEED TO RESET THE POINTER FOR GETTHEMALERTS!
			if (count($databases)>=1){
				foreach ($databases as $dbname){
					//echo $dbname . "<br>";
					if (isset ($databases)){
						$getalerts = "SELECT name,reference,username,threshold,alert_index,twitterhandle,type,start,end,control FROM " . $dbname . ".alerts where complete = 0;";
						$getthemalerts2 = $mindb_connection->query($getalerts);
						//echo "Num rows is " . $getthemalerts2->num_rows . "\n";
						if (is_object($getthemalerts2) && $getthemalerts2->num_rows >= 1){
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
			if (isset ($jobstodo)){
			foreach ($jobstodo as $index) {
				if ($index['job'] == "gencoverage"){
					$sql_template = "SELECT avg(A+T+G+C) as coverage, refname FROM " . $index['database'] . ".reference_coverage_" . $index['type'] . " inner join " . $index['database'] . ".reference_seq_info where ref_id = refid group by ref_id;";
					$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
					$template=$mindb_connection->query($sql_template);
					if ($template->num_rows >= 1){
						foreach ($template as $row) {
							if ($row['coverage']>=$index['threshold']) {
								if ($webnotify == 1){
									echo"<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
		    								title: 'Coverage Alert!',
								    		text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand of ".$row['refname'].".',
		    								type: 'success',
								    		hide: false
											});";
											echo "</script>";
								}
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
							if ($webnotify == 1){
								echo"<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
		    								title: 'Barcode Coverage Alert!',
								    		text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand for barcode  ".$index['reference'].".',
		    								type: 'success',
								    		hide: false
											});";
								echo "</script>";
							}
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
									CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
									CURLOPT_USERAGENT => 'Codular Sample cURL Request'
								));
								// Send the request & save response to $resp
								$resp = curl_exec($curl);
								// Close request to clear up some resources
								curl_close($curl);
							}
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
									if ($webnotify == 1){
										echo"<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
		    								title: 'Global Barcode Coverage Alert!',
								    		text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand for barcode  ".$row['barcodeid'].".',
		    								type: 'success',
								    		hide: false
										});";
										echo "</script>";
									}
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
						}

					}else{
						if ($index['control'] > 0) {
							$command = "insert into interaction (instruction,target,complete) VALUES ('stop','all','0');";
					    	$sqlcommand = $mindb_connection->query($command);
						}
						if ($webnotify == 1){
							if ($index['control'] == 0){
								echo"<script type=\"text/javascript\" id=\"runscript\">
								new PNotify({
									title: 'Global Barcode Coverage Alert!',
					    			text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand for all barcodes.',
									type: 'success',
					    			hide: false
								});";
								echo "</script>";
								//echo "Not stopping";
							}else{
								echo"<script type=\"text/javascript\" id=\"runscript\">
								new PNotify({
									title: 'Global Barcode Coverage Alert!',
					    			text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand for all barcodes and your run has been stopped.',
									type: 'success',
					    			hide: false
								});";
								echo "</script>";
							}
						}
						if (isset($_SESSION['twittername'])) {
							//echo "alert ('tryingtotweet');";
							if ($index['control'] == 0){
								//echo "Not stopping";
								$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for all barcodes.";
							}else{
								//echo "Stopping";
								$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for all barcodes and your run has been stopped.";
							}
							//	$message = "Coverage >=".$index['threshold']."X on " . $index['type'] . " for all barcodes.";
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
						$resetalert="update alerts set complete = 1 where alert_index = " . $index['jobid'] . ";";
						//echo $resetalert;
						$resetalerts=$mindb_connection->query($resetalert);
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

                                    if ($webnotify == 1){
                                        echo"<script type=\"text/javascript\" id=\"runscript\">
                                            new PNotify({
                                                title: 'Coverage Alert!',
                                                text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand of ".$row['refname']." and your run has been stopped.',
                                                type: 'error',
                                                hide: false
                                                });";
                                                echo "</script>";
                                            }

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
                                    if ($webnotify == 1){
                                        echo"<script type=\"text/javascript\" id=\"runscript\">
                                            new PNotify({
                                                title: 'Coverage Alert!',
                                                text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the " . $index['type'] . " strand of ".$row['refname'].".',
                                                type: 'success',
                                                hide: false
                                                });";
                                                echo "</script>";
                                            }

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
								$checkrunning=$index['database'] . "basecoverage" . $index['type'];
								//echo $checkrunning;
								$basecoveragetemp = $memcache->get("$checkrunning");
								$checkname = "basecoverage" . $index['type'];
								if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold'])>$_SESSION[$checkname]){
									if ($webnotify == 1){
										echo "<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
											title: 'Sequencing Alert!',
											text: 'You\'ve sequenced ". ($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases for run ".cleanname($index['database'])." on the ". $index['type'] . " strand.',
											type: 'info',
										});";
										echo "</script>";
									}
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
											CURLOPT_URL => $twiturl . 'tweet.php?' .$postData ,
											CURLOPT_USERAGENT => 'Codular Sample cURL Request'
										));
										// Send the request & save response to $resp
										$resp = curl_exec($curl);
										// Close request to clear up some resources
										curl_close($curl);
									}
									$_SESSION[$checkname]=(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']);
									$memcache->set("$checkrunning", (($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']));
								}
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
				if ($webnotify == 1){
					echo"<script type=\"text/javascript\" id=\"runscript\">
					new PNotify({
	    				title: 'New Run Started',
						text: '".cleanname($value)." has been started.',
	    				type: 'info',
						hide: false
					});
					</script>";
				}
				if (isset($_SESSION['twittername'])) {
					$message = "run started.";
					$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($value))) ."&message=" . (urlencode($message));
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
				if ($webnotify == 1){
					echo"<script type=\"text/javascript\" id=\"runscript\">
						new PNotify({
    						title: 'Run Finished.',
					    	text: '" .cleanname($value2) . " has been completed.',
    						type: 'info',
					    	hide: false
						});

						</script>";
				}
				if (isset($_SESSION['twittername'])) {
					$message = "run finished.";
					$postData = "twitteruser=" . ($_SESSION['twittername']) . "&run=". (urlencode(cleanname($value2))) ."&message=" . (urlencode($message));
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
