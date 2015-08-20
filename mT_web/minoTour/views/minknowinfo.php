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
			echo "<div class='panel panel-info'>
  				<div class='panel-heading'>
    				<h3 class='panel-title'>minKNOW real time data</h3>
				</div>
  				<div class='panel-body'>
  				<div class='table-responsive'>
  				<table class='table table-condensed' >
 					 <tr>
    <th>Category</td>
    <th>Info</td>
  </tr>
  <tr>
    <td>minKNOW computer name</td>
    <td>";
    $currentcomp = "SELECT * FROM messages where message = 'machinename' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentcompis = $mindb_connection2->query($currentcomp);
			if ($currentcompis->num_rows > 0) {
				foreach ($currentcompis as $row) {
					echo $row['param1'];
				}
			}else {
					echo "Not Available";
			}
    echo "</td>
  </tr>
  <tr>
    <td>minKNOW Status</td>
    <td>";
    $currentstatus = "SELECT * FROM messages where message = 'Status' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentstatusis = $mindb_connection2->query($currentstatus);
			if ($currentstatusis->num_rows > 0) {
				foreach ($currentstatusis as $row) {
					echo $row['param1'];
				}
			}else {
					echo "Not Available";
			}

    echo "</td>
  </tr>
  <tr>
    <td>Current Script</td>
    <td>";
    $currentscript = "SELECT * FROM messages where message = 'currentscript' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentscriptis = $mindb_connection2->query($currentscript);
			if ($currentscriptis->num_rows > 0) {
				foreach ($currentscriptis as $row) {
					$bits = explode("/", $row['param1']);
					echo end($bits);
				}
			}else {
					echo "Not Available";
			}

    echo "</td>
  </tr>
  <tr>
    <td>Sample Name</td>
    <td>";
    $currentsample = "SELECT * FROM messages where message = 'sampleid' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentsampleis = $mindb_connection2->query($currentsample);
			if ($currentsampleis->num_rows > 0) {
				foreach ($currentsampleis as $row) {
					echo $row['param1'];
				}
			}else {
					echo "Not Available";
			}
    echo "</td>
  </tr>
  <tr>
    <td>Run Name</td>
    <td>";
    $currentdataset = "SELECT * FROM messages where message = 'Dataset' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentdatasetis = $mindb_connection2->query($currentdataset);
			if ($currentdatasetis->num_rows > 0) {
				foreach ($currentdatasetis as $row) {
					echo cleanname($row['param1']);
				}
			}else {
					echo "Not Available";
			}
    echo "</td>
  </tr>
  <tr>
    <td>Voltage Offset</td>
    <td>";
    $currentbias = "SELECT * FROM messages where message = 'biasvoltage' order by message_index desc limit 1;";
			$currentbiasis = $mindb_connection2->query($currentbias);
			if ($currentbiasis->num_rows > 0) {
				foreach ($currentbiasis as $row) {
					echo $row['param1'] . " mV";
				}
			}else {
					echo "Not Available";
			}

    echo "</td>
  </tr>
  <tr>
    <td>Yield</td>
    <td>";
    $currentyield = "SELECT * FROM messages where message = 'yield' order by message_index desc limit 1;";
			#echo $currentbias . "<br>";
			$currentyieldis = $mindb_connection2->query($currentyield);
			if ($currentyieldis->num_rows > 0) {
				foreach ($currentyieldis as $row) {
					echo $row['param1'];
				}
			}else {
					echo "Not Available.";
			}

    echo "</td>
  </tr>
				</table>

                <em>Available scripts are:</em><br>
                ";
                $availablescripts = "SELECT * FROM messages where message = 'runscript' order by message_index;";
            			#echo $currentbias . "<br>";
            			$availablescriptsare = $mindb_connection2->query($availablescripts);
            			if ($availablescriptsare->num_rows > 0) {
            				foreach ($availablescriptsare as $row) {
            					echo "-->" . $row['param1'] . "<br>";
            				}
            			}else {
            					echo "Not Available.";
            			}

                echo "

				</div>
				</div>
			</div>";




		}


}


?>
