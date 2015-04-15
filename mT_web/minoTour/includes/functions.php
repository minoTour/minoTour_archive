<?php

//Setting general system wide parameters for various features
$_SESSION['minotourversion']=0.48;
$_SESSION['pagerefresh']=5000;


//function for calculating mean mode and median
function mmmr($array, $output = 'mean'){ 
    if(!is_array($array)){ 
        return FALSE; 
    }else{ 
        switch($output){ 
            case 'mean': 
                $count = count($array); 
                $sum = array_sum($array); 
                $total = $sum / $count; 
            break; 
            case 'median': 
                rsort($array); 
                $middle = round(count($array) / 2); 
                $total = $array[$middle-1]; 
            break; 
            case 'mode': 
                $v = array_count_values($array); 
                arsort($v); 
                foreach($v as $k => $v){$total = $k; break;} 
            break; 
            case 'range': 
                sort($array); 
                $sml = $array[0]; 
                rsort($array); 
                $lrg = $array[0]; 
                $total = $lrg - $sml; 
            break;
            case 'max': 
                rsort($array); 
                $total = $array[0]; 
            break;
            case 'min': 
                sort($array); 
                $total = $array[0]; 
            break; 
            case 'stddev':
            $fMean = array_sum($array) / count($array);
   			 $fVariance = 0.0;
			    foreach ($array as $i)
				    {
			        $fVariance += pow($i - $fMean, 2);
    				}
    				$fVariance /= ( $bSample ? count($array) - 1 : count($array) );
    				$total = (float) sqrt($fVariance);
        } 
        return $total; 
    } 
} 


//functions for viewing alignments

function displayalignment($ref,$query,$r_start,$q_start,$align_strand){
	$vislen = 60;
	
	$refarray = str_split($ref,$vislen);
	$querarray = str_split($query,$vislen);
	
	echo "<pre>";
	for ($x = 0; $x < count($refarray); $x++) {
		echo "Q:" .return10char($q_start) . " ";
   		echo $querarray[$x];
   		$q_start = $q_start + $vislen - substr_count($quearray[$x], '-');
		echo " " . $q_start . "<br>";
		$q_start++;
		echo "R:" . return10char($r_start) . " ";
		echo $refarray[$x];
		if ($align_strand == "F") {
			$r_start = $r_start + $vislen - substr_count($refarray[$x], '-');
			echo " " . $r_start . "<br><br>";
			$r_start++;
		}else if ($align_strand == "R") {
			$r_start = $r_start - $vislen + substr_count($refarray[$x], '-');
			echo " " . $r_start . "<br><br>";
			$r_start--;
		}
	} 
	echo "</pre>";


}

function return10char($value) {
	$value2 = str_pad($value, 10, " ",STR_PAD_LEFT);
	return $value2;
}

function alignsim($ref,$query) {
	$rarray = str_split($ref);
	$qarray = str_split($query);
	$identities=0;
	$rcount = 0;
	$qcount = 0;
	for ($x = 0; $x < count($rarray); $x++) {
		if ($rarray[$x] == $qarray[$x] && $rarray[$x] != "-"){
			$identities++;
		}
		if ($rarray[$x] != "-") {
			$rcount++;
		}
		if ($qarray[$x] != "-") {
			$qcount++;
		}
	}
	return array ($identities,$rcount,$qcount);
}


//generate an array containing all possible 5mers

function getkmers(){
	$kmerarray = array();
	$letterarray = array("A","T","G","C");
	foreach ($letterarray as $letter1){
		foreach ($letterarray as $letter2) {
			foreach ($letterarray as $letter3) {
				foreach ($letterarray as $letter4) {
					foreach ($letterarray as $letter5) {
						$kmer = $letter1.$letter2.$letter3.$letter4.$letter5;
						$kmerarray[$kmer]['con'] = 0;
						$kmerarray[$kmer]['ml'] = 0;
					}	
				}
			}
		}
	}
	return array ($kmerarray);	
}

//given an array of kmer counts, calculate the expected abundancies of individual kmers in the data
//psuedo - obtain the proportion of ATGC in the sequenced collection - then calculate the probabiity of seeing each kmer on that basis

function getallusers(){
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$checkuser = "SELECT user_name FROM Gru.users;";
			$checkuser_result = $db_connection->query($checkuser);
			if ($checkuser_result->num_rows>=1) {
				foreach ($checkuser_result as $row){
					$users[] = $row['user_name'];
				
				}
				return $users;
			}
			
		}
	}
}		
	

function checkminup($username) {
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$checkuser = "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = '" . $username . "') as status;";
			$checkuser_result = $db_connection->query($checkuser);
			if ($checkuser_result->num_rows==1) {
				$result_row = $checkuser_result->fetch_object();
				return $result_row->status;
			}
			
		}
	}		
	
}

function cleanname($nametoclean){
	$pieces = explode("_", $nametoclean);
	//array_shift($pieces);
	$nametoreturn = implode(" ", $pieces);
	//echo "test " . $nametoreturn;
	return($nametoreturn);
}

function restorename($nametoclean){
	$pieces = explode(" ", $nametoclean);
	array_shift($pieces);
	$nametoreturn = implode("_", $pieces);
	//echo "test " . $nametoreturn;
	return($nametoreturn);
}
function checkalerts(){
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		//$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		//Check for all active databases and find those which have alerts set
		echo "<div class='alert alert-success' role='alert'>";
		echo "Messages:<br>";
		//if (!$db_connection->connect_errno) {
		//	$getruns = "SELECT runname FROM minIONruns inner join userrun using (runindex) inner join users using (user_id) where users.user_name = '" . $_SESSION['user_name'] ."' and activeflag = 1;";
		//	$getthemruns = $db_connection->query($getruns);
		//	if ($getthemruns->num_rows>=1){
				
				
				echo "<div id='infodiv'></div>";
		//	}else{
		//		echo "<small>Active run alerts can be monitored here.</small>";

		//	}
$basename = end(preg_split('/\//',$_SERVER['PHP_SELF']));
			$filesnamestocheck = array("switch_run.php","current_summary.php","live_reads_table.php","live_data.php","export.php","set_alerts.php","current_export.php","current_rates.php","current_pores.php","current_quality.php");
			//echo $basename;
			if ( in_array ($basename, $filesnamestocheck) ) {
                //echo "This is the region of the page that will check for the existence of the alert table and create it if it does not exist";
                include 'views/alert_table_addition.php';
				//echo "I should be setting up the table...";
            }
            echo "</div>";
		}
	}


function getusers(){
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$getusers = "select user_id,user_name,user_email from users;";
			$getthemusers = $db_connection->query($getusers);
			$exist_user;
			$checkusers = "SELECT user_id,users.user_name, user_email FROM users inner join userrun using (user_id) inner join minIONruns using (runindex) where runname = '". $_GET['roi'] . "';";
			$getthem = $db_connection->query($checkusers);
			$rowcounter=$getthemusers->num_rows;
			
			if ($getthem->num_rows>=1) {
				foreach ($getthem as $row){
					$exist_user[] = $row['user_name'];
				
				}
			}
			if ($getthemusers->num_rows>=1){
				echo "<div id='checkboxes' class='col-xs-4'>";
				foreach ($getthemusers as $row) {
					echo "<div class='checkbox'>";
					echo  "<label>";
					if (in_array($row['user_name'], $exist_user)) {
						echo "<input type='checkbox' name='" .$row['user_name']. "' value='" . $row['user_id'] . "' checked>";
				    	echo $row['user_name'];
						echo "</label>";
					}else{
						echo "<input type='checkbox' name='" .$row['user_name']. "' value='" . $row['user_id'] . "'>";
				    	echo $row['user_name'];
						echo "</label>";
					}
				echo "</div>";
				}
				//echo "</select>";
				echo "</div>";
			}
		}
	}
}

function checksessionvars(){
	
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$user_name=$_SESSION['user_name'];
			
			$checkadmin = "select admin from users where user_name = '".$user_name."';";
			$checkrights = $db_connection->query($checkadmin);
			if ($checkrights->num_rows ==1) {
				$result_row = $checkrights->fetch_object();
				if ($result_row->admin == 1) {
					$_SESSION['adminuser']=1;
				}else{
					$_SESSION['adminuser']=0;
				}
			}
			
			$sql = "select users.user_name,runname, activeflag,reference,reflength from users inner join userrun using (user_id) inner join minIONruns where userrun.runindex=minIONruns.runindex and users.user_name = '" . $user_name . "';";
			$runs_available = $db_connection->query($sql);
			if ($runs_available->num_rows >=1) {
				
				$runarray;
				
				foreach ($runs_available as $row) {
					$runarray[$row['activeflag']][$row['runname']]=$row['user_name'];
				}
				if (isset ($_GET['actru'])){
					$_SESSION['chosenactiverun']=$_GET['actru'];
				}
				//echo "You have " . $runs_available->num_rows . " minION runs available to view.<br>\n";
				if (array_key_exists('1', $runarray)) {
					//var_dump($runarray);
					
				//	echo "You have 1 currently active run.<br>\n";
					if (isset ($_SESSION['chosenactiverun']) && array_key_exists($_SESSION['chosenactiverun'],$runarray[1])){
						$_SESSION['active_run_name'] = $_SESSION['chosenactiverun'];
					}else {
						$_SESSION['active_run_name']=key($runarray[1]);
					}
					$sql2 = "select reference,reflength,minup_version from minIONruns where runname='" . $_SESSION['active_run_name'] . "';";
					
					$activerundetails = $db_connection->query($sql2);
					if ($activerundetails->num_rows >= 1){
						//echo "Hello World";
						$result_row2 = $activerundetails->fetch_object();
						$_SESSION['activereference']=$result_row2->reference;
						$_SESSION['activereflength']=$result_row2->reflength;
						$_SESSION['active_minup']=$result_row2->minup_version;
					}
					
					$sql3  = "SELECT refid,refname FROM " . $_SESSION['active_run_name'] . ".reference_seq_info;";
					$refnamedetails = $db_connection->query($sql3);
					foreach ($refnamedetails as $row){
						$refnames[$row['refid']] = $row['refname'];
					}
					$_SESSION['activerefnames'] = $refnames;
					
					//Check for the existence of an XML table in the active run database:
					$db_connection2 = new mysqli(DB_HOST, DB_USER, DB_PASS, $_SESSION['active_run_name']);
					$xmlcheck = "select * from XML;";
					$xmlresult = $db_connection2->query($xmlcheck);
					if ($xmlresult->num_rows >= 1) {
						$_SESSION['currentXML'] = $xmlresult->num_rows;
					}else{
						$_SESSION['currentXML'] = $xmlresult->num_rows;
					}
					
					//Check for the existence of a barcoding table in the active run database:
					$barcodecheck = "select * from barcode_assignment;";
					$barcoderesult = $db_connection2->query($barcodecheck);
					if ($barcoderesult->num_rows >= 1) {
						$_SESSION['currentbarcode'] = $barcoderesult->num_rows;
					}else{
						$_SESSION['currentbarcode'] = $barcoderesult->num_rows;
					}
					//echo "The run is called " . key($runarray[1]) .".<br>\n";
					
				}else{
					//echo "You have no currently active runs.<br>\n";
					unset($_SESSION['active_run_name']);
				}
				if (isset($_SESSION['focusrun'])){
					$sql3 = "select reference,reflength,minup_version from minIONruns where runname='" . $_SESSION['focusrun'] . "';";
					$focusrundetails=$db_connection->query($sql3);
					if ($focusrundetails->num_rows == 1){
						$result_row3 = $focusrundetails->fetch_object();
						$_SESSION['focusreference']=$result_row3->reference;
						$_SESSION['focusreflength']=$result_row3->reflength;
						$_SESSION['focus_minup']=$result_row3->minup_version;
						//echo "Hello World" . $result_row3->reference;
						$sql4  = "SELECT refid,refname FROM " . $_SESSION['focusrun'] . ".reference_seq_info;";
						$refnamedetails = $db_connection->query($sql4);
						foreach ($refnamedetails as $row){
							$focusrefnames[$row['refid']] = $row['refname'];
						}
						$_SESSION['focusrefnames'] = $focusrefnames;
					}
					//Check for the existence of an XML table in the focus run database:
					$db_connection2 = new mysqli(DB_HOST, DB_USER, DB_PASS, $_SESSION['focusrun']);
					$xmlcheck = "select * from XML;";
					$xmlresult = $db_connection2->query($xmlcheck);
					if ($xmlresult->num_rows >= 1) {
						$_SESSION['focusXML'] = $xmlresult->num_rows;
					}else{
						$_SESSION['focusXML'] = $xmlresult->num_rows;
					}
					
					//Check for the existence of a barcoding table in the active run database:
					$barcodecheck = "select * from barcode_assignment;";
					$barcoderesult = $db_connection2->query($barcodecheck);
					if ($barcoderesult->num_rows >= 1) {
						$_SESSION['focusbarcode'] = $barcoderesult->num_rows;
					}else{
						$_SESSION['focusbarcode'] = $barcoderesult->num_rows;
					}
				}
				
			}else{
				//echo "You have no minION runs available to view.<br>\n";
			}
		}
		return true;
	}
	
	return false;
}

function getallruns()
{
	//echo "working...";
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$user_name=$_SESSION['user_name'];
			$sql = "select users.user_name,runname, activeflag,date,flowcellid,comment,FlowCellOwner,RunNumber,reference,reflength from users inner join userrun using (user_id) inner join minIONruns where userrun.runindex=minIONruns.runindex and minIONruns.activeflag=0 and users.user_name = '" . $user_name . "';";
			
			//echo "$sql";
			$runs_available = $db_connection->query($sql);
			if ($runs_available->num_rows >=1) {
				echo " <table class='table table-condensed table-hover'>
					<thead>
				<tr>
						<th>User Name</th>
						<th>Date</th>
						<th>Flow Cell ID</th>
						<th>Comment</th>
						<th>FlowCellOwner</th>
						<th>Run Name</th>
						<th>Run Order</th>
						<th>Ref Sequence</th>
						<th>Ref Length</th>
						</tr>
					</thead>
					<tbody>";

				foreach ($runs_available as $row){
					if ($_GET["roi"] == $row['runname']) {
						echo "<tr class='clickableRow active' href='previous_runs.php?roi=" .  $row['runname'] . "'>";
					}else{
						echo "<tr class='clickableRow' href='previous_runs.php?roi=" .  $row['runname'] . "'>";
					}
					echo "<td>" . $row['user_name'] . "</td>";
					echo "<td>" . $row['date'] . "</td>";
					echo "<td>" . $row['flowcellid'] . "</td>";
					echo "<td>" . $row['comment'] . "</td>";
					echo "<td>" . $row['FlowCellOwner'] . "</td>";
					echo "<td>" . $row['runname'] . "</td>";
					echo "<td>" . $row['RunNumber'] . "</td>";
					echo "<td>" . $row['reference'] . "</td>";
					echo "<td>" . $row['reflength'] . "</td>";
					echo "</tr>";
					
				}
				echo "</tbody>";
				echo "</table>";
			}
		}
	}
			
}
function checkuserruns()
{
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$user_name=$_SESSION['user_name'];
			$sql = "select users.user_name,runname, activeflag from users inner join userrun using (user_id) inner join minIONruns where userrun.runindex=minIONruns.runindex and users.user_name = '" . $user_name . "';";
			
			$runs_available = $db_connection->query($sql);
			if ($runs_available->num_rows >=1) {
				
				$runarray;
				
				foreach ($runs_available as $row) {
					$runarray[$row['activeflag']][$row['runname']]=$row['user_name'];
				}
				
				echo "You have " . $runs_available->num_rows . " minION runs available to view.<br>\n";
				if (array_key_exists('1', $runarray)) {
					echo "You have active runs available.<br>\n";
				//foreach ($runarray as $runinfo) {
				//	if ()
				//}

					$_SESSION['active_run_name']=key($runarray[1]);
					echo "The currently selected active run is " . cleanname(key($runarray[1])) .".<br>\n";	
				}else{
					echo "You have no currently active runs.<br>\n";
					unset($_SESSION['active_run_name']);
				}
				
			}else{
				echo "You have no minION runs available to view.<br>\n";
			}
		}
		return true;
	}
	
	return false;
}
function checknumactive() {
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$user_name=$_SESSION['user_name'];
			$sql = "select users.user_name,runname, activeflag from users inner join userrun using (user_id) inner join minIONruns where userrun.runindex=minIONruns.runindex and activeflag=1 and users.user_name = '" . $user_name . "';";
			
			$runs_available = $db_connection->query($sql);
			if ($runs_available->num_rows >=1) {
				$numactiveruns = $runs_available->num_rows;	
			}else {
				$numactiveruns = 0;
			}
			return $numactiveruns;
		}
	}
}
function checkactiverun() {
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$user_name=$_SESSION['user_name'];
			$sql = "select users.user_name,runname, activeflag from users inner join userrun using (user_id) inner join minIONruns where userrun.runindex=minIONruns.runindex and users.user_name = '" . $user_name . "';";
			
			$runs_available = $db_connection->query($sql);
			if ($runs_available->num_rows >=1) {
				
				$runarray;
				
				foreach ($runs_available as $row) {
					$runarray[$row['activeflag']][$row['runname']]=$row['user_name'];
				}
				
				if (array_key_exists('1', $runarray)) {
					return true;
				}else{
					return false;
				}
				
			}
		}
	}
}

function checkallruns() {
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
		$db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (!$db_connection->connect_errno) {
			$user_name=$_SESSION['user_name'];
			$sql = "select users.user_name,runname, activeflag from users inner join userrun using (user_id) inner join minIONruns where minIONruns.activeflag != 1 and userrun.runindex=minIONruns.runindex and users.user_name = '" . $user_name . "';";
			
			$runs_available = $db_connection->query($sql);
			if ($runs_available->num_rows >=1) {
				return true;
			}else{
				return false;
			}
		}
	}
}

function runsummary() {

	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1 and isset($_SESSION['active_run_name'])) {
		//echo "run summary is running - .";
		$mindb_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, $_SESSION['active_run_name']);
		if (!$mindb_connection->connect_errno) {
			$sql = "select device_id, exp_script_purpose,exp_start_time,run_id,version_name from tracking_id group by run_id order by exp_start_time;";
			
			
			$runsummary=$mindb_connection->query($sql);
			if ($runsummary->num_rows >= 1) {
				echo " <div class='table-responsive'>
				<table class='table table-condensed'>
					<thead>
				<tr>
						<th>Device ID</th>
						<th>Experiment Purpose</th>
						<th>Reads Generated</th>
						<th>Start Time</th>
						<th>Start Date</th>
						<th>Run ID</th>
						<th>Version Name</th>
						</tr>
					</thead>
					<tbody>";
					foreach ($runsummary as $row) {
						$purpose = $row['exp_script_purpose'];
						$sql2 = "select count(*) as count from config_general inner join tracking_id using (basename_id) where exp_script_purpose='$purpose';";
						$counts = $mindb_connection->query($sql2);
						if ($counts->num_rows == 1){
							$result_row = $counts->fetch_object();
							$count = $result_row->count;
						}else{
							$count = "0";
						}
						echo "<tr>";
						echo "<td>" . $row['device_id'] . "</td>";
						echo "<td>" . $row['exp_script_purpose'] . "</td>";
						echo "<td>" . $count . "</td>";
						echo "<td>" . gmdate('H:i:s', $row['exp_start_time']) . "</td>";
						echo "<td>" . gmdate('d-m-y', $row['exp_start_time']) . "</td>";
						echo "<td style='word-wrap: break-word;'>" . $row['run_id'] . "</td>";
						echo "<td>" . $row['version_name'] . "</td>";
						echo "</tr>";
					}
						
				echo"</tbody>
					</table>
					</div>
					";
				}
			}
			echo "<h3>Read Data</h3>";
			echo "<h4>Basecalled Template</h4>";
			$sql3 = "select count(*) as readnum,exp_script_purpose,ROUND(AVG(length(sequence))) as average_length,STDDEV(length(sequence)) as standard_dev,MAX(length(sequence)) as maxlen,MIN(length(sequence)) as minlen from basecalled_template inner join tracking_id using (basename_id) group by exp_script_purpose;";
			$template=$mindb_connection->query($sql3);
			if ($template->num_rows >= 1) {
				echo " <div class='table-responsive'>
				<table class='table table-condensed'>
					<thead>
				<tr>
						<th>Experiment Purpose</th>
						<th>Sequenced Reads</th>
						<th>Average Length</th>
						<th>Standard Deviation</th>
						<th>Max Length</th>
						<th>Min Length</th>
						</tr>
					</thead>
					<tbody>";
				foreach ($template as $row) {
					echo "<tr>";
					echo "<td>" . $row['exp_script_purpose'] . "</td>";
					echo "<td>" . $row['readnum'] . "</td>";
					echo "<td>" . $row['average_length'] . "</td>";
					echo "<td>" . round($row['standard_dev'],2) . "</td>";
					echo "<td>" . $row['maxlen']. "</td>";
					echo "<td>" . $row['minlen']. "</td>";
					echo "</tr>";
				}
						
				echo"</tbody>
					</table>
					</div>
					";
				
			}
			echo "<h4>Basecalled Complement</h4>";
			$sql4 = "select count(*) as readnum,exp_script_purpose,ROUND(AVG(length(sequence))) as average_length,STDDEV(length(sequence)) as standard_dev,MAX(length(sequence)) as maxlen,MIN(length(sequence)) as minlen from basecalled_complement inner join tracking_id using (basename_id) group by exp_script_purpose;";
			$complement=$mindb_connection->query($sql4);
			if ($complement->num_rows >= 1) {
				echo " <div class='table-responsive'>
				<table class='table table-condensed'>
					<thead>
				<tr>
						<th>Experiment Purpose</th>
						<th>Sequenced Reads</th>
						<th>Average Length</th>
						<th>Standard Deviation</th>
						<th>Max Length</th>
						<th>Min Length</th>
						</tr>
					</thead>
					<tbody>";
				foreach ($complement as $row) {
					echo "<tr>";
					echo "<td>" . $row['exp_script_purpose'] . "</td>";
					echo "<td>" . $row['readnum'] . "</td>";
					echo "<td>" . $row['average_length'] . "</td>";
					echo "<td>" . round($row['standard_dev'],2) . "</td>";
					echo "<td>" . $row['maxlen']. "</td>";
					echo "<td>" . $row['minlen']. "</td>";
					echo "</tr>";
				}
						
				echo"</tbody>
					</table>
					</div>
					";
				
			}
			echo "<h4>Basecalled 2d</h4>";
			$sql5 = "select count(*) as readnum,exp_script_purpose,ROUND(AVG(length(sequence))) as average_length,STDDEV(length(sequence)) as standard_dev,MAX(length(sequence)) as maxlen,MIN(length(sequence)) as minlen from basecalled_2d inner join tracking_id using (basename_id) group by exp_script_purpose;";
			$twod=$mindb_connection->query($sql5);
			if ($twod->num_rows >= 1) {
				echo " <div class='table-responsive'>
				<table class='table table-condensed'>
					<thead>
				<tr>
						<th>Experiment Purpose</th>
						<th>Sequenced Reads</th>
						<th>Average Length</th>
						<th>Standard Deviation</th>
						<th>Max Length</th>
						<th>Min Length</th>
						</tr>
					</thead>
					<tbody>";
				foreach ($twod as $row) {
					echo "<tr>";
					echo "<td>" . $row['exp_script_purpose'] . "</td>";
					echo "<td>" . $row['readnum'] . "</td>";
					echo "<td>" . $row['average_length'] . "</td>";
					echo "<td>" . round($row['standard_dev'],2) . "</td>";
					echo "<td>" . $row['maxlen']. "</td>";
					echo "<td>" . $row['minlen']. "</td>";
					echo "</tr>";
				}
						
				echo"</tbody>
					</table>
					</div>
					";
				
			}
			
		}
}
function prevrunsummary() {
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1 and isset($_SESSION['focusrun'])) {
		$mindb_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, $_SESSION['focusrun']);
		if (!$mindb_connection->connect_errno) {
			$sql = "select device_id, exp_script_purpose,exp_start_time,run_id,version_name from tracking_id group by run_id order by exp_start_time;";
			
			
			$runsummary=$mindb_connection->query($sql);
			if ($runsummary->num_rows >= 1) {
				echo " <table class='table table-condensed'>
					<thead>
				<tr>
						<th>Device ID</th>
						<th>Experiment Purpose</th>
						<th>Reads Generated</th>
						<th>Start Time</th>
						<th>Start Date</th>
						<th>Run ID</th>
						<th>Version Name</th>
						</tr>
					</thead>
					<tbody>";
					foreach ($runsummary as $row) {
						$purpose = $row['exp_script_purpose'];
						$sql2 = "select count(*) as count from config_general inner join tracking_id using (basename) where exp_script_purpose='$purpose';";
						$counts = $mindb_connection->query($sql2);
						if ($counts->num_rows == 1){
							$result_row = $counts->fetch_object();
							$count = $result_row->count;
						}else{
							$count = "0";
						}
						echo "<tr>";
						echo "<td>" . $row['device_id'] . "</td>";
						echo "<td>" . $row['exp_script_purpose'] . "</td>";
						echo "<td>" . $count . "</td>";
						echo "<td>" . gmdate('H:i:s', $row['exp_start_time']) . "</td>";
						echo "<td>" . gmdate('d-m-y', $row['exp_start_time']) . "</td>";
						echo "<td>" . $row['run_id'] . "</td>";
						echo "<td>" . $row['version_name'] . "</td>";
						echo "</tr>";
					}
						
				echo"</tbody>
					</table>
					";
				}
			}
			echo "<h3>Read Data</h3>";
			echo "<h4>Basecalled Template</h4>";
			$sql3 = "select count(*) as readnum,exp_script_purpose,ROUND(AVG(length(sequence))) as average_length,STDDEV(length(sequence)) as standard_dev,MAX(length(sequence)) as maxlen,MIN(length(sequence)) as minlen from basecalled_template inner join tracking_id using (basename_id) group by exp_script_purpose;";
			$template=$mindb_connection->query($sql3);
			if ($template->num_rows >= 1) {
				echo " <table class='table table-condensed'>
					<thead>
				<tr>
						<th>Experiment Purpose</th>
						<th>Sequenced Reads</th>
						<th>Average Length</th>
						<th>Standard Deviation</th>
						<th>Max Length</th>
						<th>Min Length</th>
						</tr>
					</thead>
					<tbody>";
				foreach ($template as $row) {
					echo "<tr>";
					echo "<td>" . $row['exp_script_purpose'] . "</td>";
					echo "<td>" . $row['readnum'] . "</td>";
					echo "<td>" . $row['average_length'] . "</td>";
					echo "<td>" . round($row['standard_dev'],2). "</td>";
					echo "<td>" . $row['maxlen']. "</td>";
					echo "<td>" . $row['minlen']. "</td>";
					echo "</tr>";
				}
						
				echo"</tbody>
					</table>
					";
				
			}
			echo "<h4>Basecalled Complement</h4>";
			$sql4 = "select count(*) as readnum,exp_script_purpose,ROUND(AVG(length(sequence))) as average_length,STDDEV(length(sequence)) as standard_dev,MAX(length(sequence)) as maxlen,MIN(length(sequence)) as minlen from basecalled_complement inner join tracking_id using (basename_id) group by exp_script_purpose;";
			$complement=$mindb_connection->query($sql4);
			if ($complement->num_rows >= 1) {
				echo " <table class='table table-condensed'>
					<thead>
				<tr>
						<th>Experiment Purpose</th>
						<th>Sequenced Reads</th>
						<th>Average Length</th>
						<th>Standard Deviation</th>
						<th>Max Length</th>
						<th>Min Length</th>
						</tr>
					</thead>
					<tbody>";
				foreach ($complement as $row) {
					echo "<tr>";
					echo "<td>" . $row['exp_script_purpose'] . "</td>";
					echo "<td>" . $row['readnum'] . "</td>";
					echo "<td>" . $row['average_length'] . "</td>";
					echo "<td>" . round($row['standard_dev'],2) . "</td>";
					echo "<td>" . $row['maxlen']. "</td>";
					echo "<td>" . $row['minlen']. "</td>";
					echo "</tr>";
				}
						
				echo"</tbody>
					</table>
					";
				
			}
			echo "<h4>Basecalled 2d</h4>";
			$sql5 = "select count(*) as readnum,exp_script_purpose,ROUND(AVG(length(sequence))) as average_length,STDDEV(length(sequence)) as standard_dev,MAX(length(sequence)) as maxlen,MIN(length(sequence)) as minlen from basecalled_2d inner join tracking_id using (basename_id) group by exp_script_purpose;";
			$twod=$mindb_connection->query($sql5);
			if ($twod->num_rows >= 1) {
				echo " <table class='table table-condensed'>
					<thead>
				<tr>
						<th>Experiment Purpose</th>
						<th>Sequenced Reads</th>
						<th>Average Length</th>
						<th>Standard Deviation</th>
						<th>Max Length</th>
						<th>Min Length</th>
						</tr>
					</thead>
					<tbody>";
				foreach ($twod as $row) {
					echo "<tr>";
					echo "<td>" . $row['exp_script_purpose'] . "</td>";
					echo "<td>" . $row['readnum'] . "</td>";
					echo "<td>" . $row['average_length'] . "</td>";
					echo "<td>" . round($row['standard_dev'],2) . "</td>";
					echo "<td>" . $row['maxlen']. "</td>";
					echo "<td>" . $row['minlen']. "</td>";
					echo "</tr>";
				}
						
				echo"</tbody>
					</table>
					";
				
			}
			
		}
}

function activechannels(){
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1 and isset($_SESSION['active_run_name'])) {
		$mindb_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, $_SESSION['active_run_name']);
		$sql = "select count(*) as counts from ( select count(*) as count from config_general inner join tracking_id using (basename) where exp_script_purpose !='dry_chip' group by channel) as counts;";
		$activechans=$mindb_connection->query($sql);
		if ($activechans->num_rows == 1){
			$result_row = $activechans->fetch_object();
			$count = $result_row->counts;
			return($count);
		}else{
			$count = "0";
			return($count);
		}
	}
}

function averagechannels(){
	if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1 and isset($_SESSION['active_run_name'])) {
		$mindb_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, $_SESSION['active_run_name']);
		$sql = "select AVG(count) as average from ( select count(*) as count from config_general inner join tracking_id using (basename) where exp_script_purpose !='dry_chip' group by channel) as counts;";
		$avechans=$mindb_connection->query($sql);
		if ($avechans->num_rows == 1){
			$result_row = $avechans->fetch_object();
			$average = $result_row->average;
			return($average);
		}else{
			$count = "0";
			return($average);
		}
	}
	
}

//mySQL useful checks and balances

?>
