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
			echo "
  				<div class='panel-heading'>
    				<h3 class='panel-title'>minKNOW real time detailed data</h3>
				</div>
  				<div class='panel-body'>";
                    echo "<strong> This view is experimental and may not be in sync with native minKNOW. </strong><br><br>";

                    $chunks = explode(" ", $resultarray['disk_usage']);
                    if (strlen($chunks[0]) > 0) {
                        echo "You have " . round($chunks[0]/$chunks[2]*100) . "% (". $chunks[0] . " " .$chunks[3] . ") disk space remaining.<br>";
                    }
                    echo "Experiment Time: ".$resultarray['cached_exp_time_string_for_display_in_client']."<br>";
                    echo "Channels Sequencing/Total Channels: " . $resultarray['channels_with_read_event_count']."/".$resultarray['channel_count']."<br>";
                    echo "ASIC Temperature: ".$resultarray['minion_asic_temperature']."&#176;C<br>";
                    echo "minION heatsink Temperature: ".round($resultarray['minion_heatsink_temperature'],2)."&#176;C<br>";
                    $ll = json_decode($resultarray['channel_states_conf']);
                    $ll = cvf_convert_object_to_array($ll);
                    //print_r($ll);
                    $statearray = array();
                    for ($x = 0; $x <= 10; $x++){
                        $statearray[$x]=0;
                    }
                    for ($x = 1; $x <= 512; $x++) {
                        $statearray[$resultarray[$x]]+=1;
                        //echo $resultarray[$x] . "<br>";
                    }
                    /*foreach ($ll as $monkey=> $cow){
                        ksort($cow);
                        echo $monkey . "\n";
                        foreach ($cow as $sheep=>$ant){
                            echo $sheep . "\n";
                            foreach ($ant as $mouse=>$butt){
                                if (is_array($butt)) {
                                    foreach ($butt as $beaver=>$camel){
                                        if (is_array($camel)){
                                            foreach ($camel as $donkey=>$rabbit){
                                                echo $mouse . ":" . $beaver . "-" . $donkey . "\t" . $rabbit . "\n";
                                            }
                                        }else{
                                            echo $mouse . ":" . $beaver . "\t" . $camel . "\n";
                                        }
                                    }
                                }else{
                                    echo $mouse . "\t" . $butt . "\n";
                                }

                            }
                        }
                    };*/
                    foreach ($ll as $monkey=>$cow){
                        echo "<br>" . "<strong>Channel States</strong>" . "<br><br>";
                        ksort($cow);
                        echo "<div class='row center-block'>";
                        foreach ($cow as $sheep=>$ant){
                            echo "<div class='col-md-4'><div id='rectangle' style='width:20px; height:20px; background-color:#". $ant['style']['colour'] ."'></div><small>" . $ant['group'] . " (" . $ant['name'] . "\t" .$ant['style']['label'] . "): " . $statearray[$sheep] .  "</small></div>";
                        }
                        echo "</div>";
                    }
                echo "

                </div>
                </div>
			";




		}


}


?>
