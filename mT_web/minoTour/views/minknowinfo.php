<?php

require_once("includes/functions.php");
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
    
	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$mindb_connection2 = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
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
			echo "minKNOW real time data<br><br>";
			$currentbias = "SELECT * FROM messages where message = 'biasvoltage' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentbiasis = $mindb_connection2->query($currentbias);
			if ($currentbiasis->num_rows > 0) {
				foreach ($currentbiasis as $row) {
					echo "Current Voltage offset is " . $row['param1'] . "mV<br>";
				}
			}else {
					echo "No information on Voltage Offset is available now. If you should see a value here, please try the magic button below.";
			}
			$currentscript = "SELECT * FROM messages where message = 'currentscript' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentscriptis = $mindb_connection2->query($currentscript);
			if ($currentscriptis->num_rows > 0) {
				foreach ($currentscriptis as $row) {
					echo "Current script is " . $row['param1'] . "<br>";
				}
			}else {
					echo "No information on the current running script is available.";
			}
			$currentcomp = "SELECT * FROM messages where message = 'machinename' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentcompis = $mindb_connection2->query($currentcomp);
			if ($currentcompis->num_rows > 0) {
				foreach ($currentcompis as $row) {
					echo "The computer name running minKNOW is " . $row['param1'] . "<br>";
				}
			}else {
					echo "No information on the computer name is available.";
			}
			$currentsample = "SELECT * FROM messages where message = 'sampleid' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentsampleis = $mindb_connection2->query($currentsample);
			if ($currentsampleis->num_rows > 0) {
				foreach ($currentsampleis as $row) {
					echo "The sample name is " . $row['param1'] . "<br>";
				}
			}else {
					echo "No information on the sample name is available.";
			}
			$currentyield = "SELECT * FROM messages where message = 'yield' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentyieldis = $mindb_connection2->query($currentyield);
			if ($currentyieldis->num_rows > 0) {
				foreach ($currentyieldis as $row) {
					echo "The current yield is " . $row['param1'] . "<br>";
				}
			}else {
					echo "No information on yield is available.";
			}

			
		}

		
}


?>