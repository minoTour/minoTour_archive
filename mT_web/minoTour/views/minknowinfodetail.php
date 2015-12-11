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
                $resultarray = array();
                $detailedmessages = "SELECT * FROM messages where target = 'details' order by message_index desc;";
                #echo $currentbias . "<br>";
                $detailedmessagesres = $mindb_connection2->query($detailedmessages);
                if ($detailedmessagesres->num_rows > 0) {
                    foreach ($detailedmessagesres as $row) {
                        #echo $row['message'] . "\t" . $row['param1'] . "<br>";
                        $resultarray[$row['message']]=$row['param1'];
                    }
                }else {
                        #echo "Not Available" . "<br>";
                }
			echo "<div class='panel panel-info'>
  				<div class='panel-heading'>
    				<h3 class='panel-title'>minKNOW real time detailed data</h3>
				</div>
  				<div class='panel-body'>";
                    
                    $chunks = explode(" ", $resultarray['disk_usage']);
                    echo "You have " . round($chunks[0]/$chunks[2]*100) . "% (". $chunks[0] . " " .$chunks[3] . ") disk space remaining.";
                echo "</div>
				</div>
			</div>";




		}


}


?>
