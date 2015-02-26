<?php

require_once("includes/functions.php");
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
    
	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

	if (!$mindb_connection->connect_errno) {
		
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
						$getalerts = "SELECT name,threshold,alert_index FROM " . $dbname . ".alerts where complete = 0;";
						$getthemalerts2 = $mindb_connection->query($getalerts);
						//var_dump ($getthemalerts2);
						if ($getthemalerts2->num_rows>=1){
							foreach ($getthemalerts2 as $row){
								$jobstodo[] = array(
								    'job' => $row['name'],
									'threshold' => $row['threshold'],
									'jobid' => $row['alert_index'],
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
				
				//echo $index['job'] . "<br>";
				
				if ($index['job'] == "gencoverage"){
					$sql_template = "select avg(count) as coverage, refname from (SELECT count(*) as count,refname FROM " . $index['database'] . ".last_align_basecalled_template inner join " . $index['database'] . ".reference_seq_info using (refid) where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x;";
					//$sql_complement = "select avg(count) as coverage, refname from (SELECT count(*) as count,refname FROM " . $index['database'] , ".last_align_basecalled_complement inner join " . $index['database'] , ".reference_seq_info using (refid) where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x;";
					//$sql_2d = "select avg(count) as coverage, refname from (SELECT count(*) as count,refname FROM " . $index['database'] , ".last_align_basecalled_2d inner join " . $index['database'] , ".reference_seq_info using (refid) where (cigarclass=7 or cigarclass=8) group by refid,refpos) as x;";
					$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				
					$template=$mindb_connection->query($sql_template);
					//$complement=$mindb_connection->query($sql_complement);
					//$read2d=$mindb_connection->query($sql_2d);
					if ($template->num_rows >= 1){
						foreach ($template as $row) {
							if ($row['coverage']>=$index['threshold']) {
								echo"<script type=\"text/javascript\" id=\"runscript\">
										new PNotify({
		    								title: 'Coverage Alert!',
								    		text: 'Coverage exceeding ".$index['threshold']."X has been achieved for run ".cleanname($index['database'])." on the template strand.',
		    								type: 'success',
								    		hide: false
											});";
											if (isset($_SESSION['twittername'])) {
												//echo "alert ('tryingtotweet');";
												$message = "Coverage >=".$index['threshold']."X on template";
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
				if ($index['job'] == "basenotification"){
					$sql_template = "SELECT sum(length(sequence)) as bases FROM basecalled_template;";
					$sql_complement = "SELECT sum(length(sequence)) as bases FROM basecalled_complement;";
					$sql_2d = "SELECT sum(length(sequence)) as bases FROM basecalled_2d;";
					$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$index['database']);
				
					$template=$mindb_connection->query($sql_template);
					$complement=$mindb_connection->query($sql_complement);
					$read2d=$mindb_connection->query($sql_2d);
					if ($template->num_rows >= 1) {
						foreach ($template as $row) {
							if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']) >=1 ){
								//echo (($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']). "<br>";
									//echo $_SESSION['basecoveragetemp'];
								//if (isset $_SESSION['basecoverage']){
									if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold'])>$_SESSION['basecoveragetemp']){
										echo "<script type=\"text/javascript\" id=\"runscript\">
											new PNotify({
												title: 'Sequencing Alert!',
												text: 'You\'ve sequenced ". ($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases for run ".cleanname($index['database'])." on the template strand.',
												type: 'info',
												
												});";
											if (isset($_SESSION['twittername']) && ($index['threshold'] >= 500000)) {
												//echo "alert ('tryingtotweet');";
												$message = "Sequenced ".($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases on template";
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
											$_SESSION['basecoveragetemp']=(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']);
									}
									//}else{
									
									
								//}
								$_SESSION['basecoveragetemp']=((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']));
							}
							
						}
					}
					if ($complement->num_rows >= 1) {
						foreach ($complement as $row) {
							if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']) >=1 ){
								//if (isset $_SESSION['basecoverage']){
									if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold'])>$_SESSION['basecoveragecomp']){
										echo "<script type=\"text/javascript\" id=\"runscript\">
											new PNotify({
												title: 'Sequencing Alert!',
												text: 'You\'ve sequenced ". ($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases for run ".cleanname($index['database'])." on the complement strand.',
												type: 'info',
												
												});";
											if (isset($_SESSION['twittername']) && ($index['threshold'] >= 500000)) {
												//echo "alert ('tryingtotweet');";
												$message = "Sequenced ".($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases on  complement";
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
													$_SESSION['basecoveragecomp']=(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']);
									}
									//}else{
									
									
								//}
								$_SESSION['basecoveragecomp']=(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']);
							}
							
						}
					}
					if ($read2d->num_rows >= 1) {
						foreach ($read2d as $row) {
							if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']) >=1 ){
								//if (isset $_SESSION['basecoverage']){
									if ((($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold'])>$_SESSION['basecoverage2d']){
										echo "<script type=\"text/javascript\" id=\"runscript\">
											new PNotify({
												title: 'Sequencing Alert!',
												text: 'You\'ve sequenced ". ($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases for run ".cleanname($index['database'])." in 2d.',
												type: 'info',
												
												});";
											if (isset($_SESSION['twittername']) && ($index['threshold'] >= 500000)) {
												//echo "alert ('tryingtotweet');";
												$message = "Sequenced ".($index['threshold']*(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']))." bases on  2d";
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
													$_SESSION['basecoverage2d']=(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']);
									}
									//}else{
									
									
								//}
								$_SESSION['basecoverage2d']=(($row['bases']-($row['bases'] % $index['threshold']))/$index['threshold']);
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