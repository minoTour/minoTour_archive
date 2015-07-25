<?php

### This file contains a number of functions which are used to generate json for the website and are also utilised by the perl wrapper script on the backend. It is currently experimental.

##### Template for new jobs...

function blanktemplate($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...
		}
		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}

###Calculates an approximation of the speed of sequencing by caluclating the number of bases sequenced in a 5 minute window per channel - this is a measure of speed through the pore.


function sequencingrate($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...
			$sqltemplate = "select (floor((basecalled_template.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor, sum(basecalled_template.duration) as time,  sum(length(sequence))/60/5/count(*) as effective_rate, count(*) as channels, sum(length(sequence))/sum(basecalled_template.duration) as rate from basecalled_template inner join tracking_id using (basename_id) group by 1 order by 1;";
			$sqlcomplement = "select (floor((basecalled_complement.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor, sum(basecalled_complement.duration) as time,  sum(length(sequence))/60/5/count(*) as effective_rate, count(*) as channels, sum(length(sequence))/sum(basecalled_complement.duration) as rate from basecalled_complement inner join tracking_id using (basename_id) group by 1 order by 1;";


			$resulttemplate = $mindb_connection->query($sqltemplate);
			$resultcomplement = $mindb_connection->query($sqlcomplement);

			$resultarray;

			if ($resulttemplate->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resulttemplate as $row) {
					#$cumucount++;
					$resultarray['template rate'][$row['bin_floor']]=$row['rate'];
					$resultarray['template effective rate'][$row['bin_floor']]=$row['effective_rate'];
				}
			}
			if ($resultcomplement->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resultcomplement as $row) {
					#$cumucount++;
					$resultarray['complement rate'][$row['bin_floor']]=$row['rate'];
					$resultarray['complement effective rate'][$row['bin_floor']]=$row['effective_rate'];
				}
			}



		$jsonstring;
		$jsonstring = $jsonstring . "[\n";

		foreach ($resultarray as $key => $value) {
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\": \"" . $key .  "\",\n";

				//if ($key == "template") {
					//$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "complement") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "2d") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}
				$jsonstring = $jsonstring . "\"data\":[";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[  $key2 , $value2 ],";
				}

			$jsonstring = $jsonstring . "]\n";
		$jsonstring = $jsonstring . "},\n";
			}
		$jsonstring = $jsonstring . "]\n";
	}
		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}

function lengthtimewindow($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...
			$sqltemplate = "select (floor((basecalled_template.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor,   sum(length(sequence))/count(*) as meanlength from basecalled_template inner join tracking_id using (basename_id) group by 1 order by 1;";
			$sqlcomplement = "select (floor((basecalled_complement.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor,   sum(length(sequence))/count(*) as meanlength from basecalled_complement inner join tracking_id using (basename_id) group by 1 order by 1;";
			$sql2d = "select (floor((basecalled_template.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor,   sum(length(basecalled_2d.sequence))/count(*) as meanlength from basecalled_template inner join basecalled_2d using (basename_id) inner join tracking_id using (basename_id) group by 1 order by 1;";


			$resulttemplate = $mindb_connection->query($sqltemplate);
			$resultcomplement = $mindb_connection->query($sqlcomplement);
			$result2d = $mindb_connection->query($sql2d);

			$resultarray;

			if ($resulttemplate->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resulttemplate as $row) {
					#$cumucount++;
					$resultarray['template length'][$row['bin_floor']]=$row['meanlength'];
					#$resultarray['template effective rate'][$row['bin_floor']]=$row['effective_rate'];
				}
			}
			if ($resultcomplement->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resultcomplement as $row) {
					#$cumucount++;
					$resultarray['complement length'][$row['bin_floor']]=$row['meanlength'];
					#$resultarray['complement effective rate'][$row['bin_floor']]=$row['effective_rate'];
				}
			}
			if ($result2d->num_rows >=1) {
				#$cumucount = 0;
				foreach ($result2d as $row) {
					#$cumucount++;
					$resultarray['2d length'][$row['bin_floor']]=$row['meanlength'];
					#$resultarray['complement effective rate'][$row['bin_floor']]=$row['effective_rate'];
				}
			}



		$jsonstring;
		$jsonstring = $jsonstring . "[\n";

		foreach ($resultarray as $key => $value) {
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\": \"" . $key .  "\",\n";

				//if ($key == "template") {
					//$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "complement") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "2d") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}
				$jsonstring = $jsonstring . "\"data\":[";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[  $key2 , $value2 ],";
				}

			$jsonstring = $jsonstring . "]\n";
		$jsonstring = $jsonstring . "},\n";
			}
		$jsonstring = $jsonstring . "]\n";
	}
		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}


##### ratiopassfail

function ratiopassfail($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...
			$sqltotalcount = "select (floor((basecalled_template.start_time)/60/10)*10*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join tracking_id using (basename_id) group by 1 order by 1;";
			$sqltemplate = "select (floor((basecalled_template.start_time)/60/10)*10*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join tracking_id using (basename_id) where file_path like '%pass%' group by 1 order by 1;";
			$sqlcomplement = "select (floor((basecalled_template.start_time)/60/10)*10*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_complement using (basename_id) inner join tracking_id using (basename_id) where file_path like '%pass%' group by 1 order by 1;";
			$sql2d = "select (floor((basecalled_template.start_time)/60/10)*10*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_2d using (basename_id) inner join tracking_id using (basename_id) where file_path like '%pass%' group by 1 order by 1;";
			$sqltemplate2 = "select (floor((basecalled_template.start_time)/60/10)*10*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join tracking_id using (basename_id) where file_path like '%fail%' group by 1 order by 1;";
			$sqlcomplement2 = "select (floor((basecalled_template.start_time)/60/10)*10*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_complement using (basename_id) inner join tracking_id using (basename_id) where file_path like '%fail%' group by 1 order by 1;";
			$sql2d2 = "select (floor((basecalled_template.start_time)/60/10)*10*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_2d using (basename_id) inner join tracking_id using (basename_id) where file_path like '%fail%' group by 1 order by 1;";

			$resulttotal = $mindb_connection->query($sqltotalcount);

			$resulttemplate = $mindb_connection->query($sqltemplate);
			$resultcomplement = $mindb_connection->query($sqlcomplement);
			$result2d = $mindb_connection->query($sql2d);

			$resulttemplate2 = $mindb_connection->query($sqltemplate2);
			$resultcomplement2 = $mindb_connection->query($sqlcomplement2);
			$result2d2 = $mindb_connection->query($sql2d2);

			$totalarray;

			if ($resulttotal->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resulttotal as $row) {
					#$cumucount++;
					$totalarray[$row['bin_floor']]=$row['count'];
				}
			}

			$resultarray;

			if ($resulttemplate->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resulttemplate as $row) {
					#$cumucount++;
					$resultarray['template pass'][$row['bin_floor']]=$row['count']/$totalarray[$row['bin_floor']]*100;
				}
			}
			if ($resultcomplement->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resultcomplement as $row) {
					#$cumucount++;
					$resultarray['complement pass'][$row['bin_floor']]=$row['count']/$totalarray[$row['bin_floor']]*100;
				}
			}
			if ($result2d->num_rows >=1) {
				#$cumucount = 0;
				foreach ($result2d as $row) {
					#$cumucount++;
					$resultarray['2d pass'][$row['bin_floor']]=$row['count']/$totalarray[$row['bin_floor']]*100;
				}
			}
			if ($resulttemplate2->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resulttemplate2 as $row) {
					#$cumucount++;
					$resultarray['template fail'][$row['bin_floor']]=$row['count']/$totalarray[$row['bin_floor']]*100;
				}
			}
			if ($resultcomplement2->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resultcomplement2 as $row) {
					#$cumucount++;
					$resultarray['complement fail'][$row['bin_floor']]=$row['count']/$totalarray[$row['bin_floor']]*100;
				}
			}
			if ($result2d2->num_rows >=1) {
				#$cumucount = 0;
				foreach ($result2d2 as $row) {
					#$cumucount++;
					$resultarray['2d fail'][$row['bin_floor']]=$row['count']/$totalarray[$row['bin_floor']]*100;
				}
			}

		}

		$jsonstring;
		$jsonstring = $jsonstring . "[\n";

		foreach ($resultarray as $key => $value) {
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\": \"" . $key .  "\",\n";

				//if ($key == "template") {
					//$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "complement") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "2d") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}
				$jsonstring = $jsonstring . "\"data\":[";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[  $key2 , $value2 ],";
				}

			$jsonstring = $jsonstring . "]\n";
		$jsonstring = $jsonstring . "},\n";
			}
		$jsonstring = $jsonstring . "]\n";

		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}


##### ratio2dtemplate

function ratio2dtemplate($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...
			$sqltemplate = "select (floor((basecalled_template.start_time)/60/15)*15*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join tracking_id using (basename_id)  group by 1 order by 1;";
			$sqlcomplement = "select (floor((basecalled_template.start_time)/60/15)*15*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_complement using (basename_id) inner join tracking_id using (basename_id)  group by 1 order by 1;";
			$sql2d = "select (floor((basecalled_template.start_time)/60/15)*15*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_2d using (basename_id) inner join tracking_id using (basename_id)  group by 1 order by 1;";

			$resulttemplate = $mindb_connection->query($sqltemplate);
			$resultcomplement = $mindb_connection->query($sqlcomplement);
			$result2d = $mindb_connection->query($sql2d);

			$resultarray;

			if ($resulttemplate->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resulttemplate as $row) {
					#$cumucount++;
					$resultarray['template'][$row['bin_floor']]=$row['count'];
				}
			}
			if ($resultcomplement->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resultcomplement as $row) {
					#$cumucount++;
					$resultarray['complement'][$row['bin_floor']]=$row['count'];
				}
			}
			if ($result2d->num_rows >=1) {
				#$cumucount = 0;
				foreach ($result2d as $row) {
					#$cumucount++;
					$resultarray['2d'][$row['bin_floor']]=$row['count'];
				}
			}

		}

		$jsonstring;
		$jsonstring = $jsonstring . "[\n";

		foreach ($resultarray as $key => $value) {
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\": \"" . $key .  "\",\n";

				//if ($key == "template") {
					//$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "complement") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "2d") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}
				$jsonstring = $jsonstring . "\"data\":[";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[  $key2 , $value2 ],";
				}

			$jsonstring = $jsonstring . "]\n";
		$jsonstring = $jsonstring . "},\n";
			}
		$jsonstring = $jsonstring . "]\n";

		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}


##### cumulative yield...

function cumulativeyield($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...
			$sqltemplate = "select (floor((basecalled_template.start_time)/60/15)*15*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join tracking_id using (basename_id)  group by 1 order by 1;";
			$sqlcomplement = "select (floor((basecalled_template.start_time)/60/15)*15*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_complement using (basename_id) inner join tracking_id using (basename_id)  group by 1 order by 1;";
			$sql2d = "select (floor((basecalled_template.start_time)/60/15)*15*60+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_2d using (basename_id) inner join tracking_id using (basename_id)  group by 1 order by 1;";

			#$sqltemplate = "SELECT (start_time+exp_start_time)*1000 as time FROM basecalled_template inner join tracking_id using (basename_id) order by start_time;";
			#$sqlcomplement = "SELECT (start_time+exp_start_time)*1000 as time FROM basecalled_complement inner join tracking_id using (basename_id) order by start_time;";
			#$sql2d = "SELECT (start_time+exp_start_time)*1000 as time FROM basecalled_2d inner join tracking_id using (basename_id) inner join basecalled_complement using (basename_id) order by start_time;";
			//echo $sqltemplate;
			$resulttemplate = $mindb_connection->query($sqltemplate);
			$resultcomplement = $mindb_connection->query($sqlcomplement);
			$result2d = $mindb_connection->query($sql2d);

			$resultarray;

			if ($resulttemplate->num_rows >=1) {
				$cumucount = 0;
				foreach ($resulttemplate as $row) {
					$cumucount=$cumucount+$row['count'];
					#echo "Count is ". $row['count'] . "\n";
					#echo "Cumu Count is ". $cumucount . "\n";
					#$resultarray['template'][$cumucount]=$row['bin_floor'];
					$resultarray['template'][$row['bin_floor']]=$cumucount;
				}
			}
			if ($resultcomplement->num_rows >=1) {
				$cumucount = 0;
				foreach ($resultcomplement as $row) {
					$cumucount=$cumucount+$row['count'];
					$resultarray['complement'][$row['bin_floor']]=$cumucount;
				}
			}
			if ($result2d->num_rows >=1) {
				$cumucount = 0;
				foreach ($result2d as $row) {
					$cumucount=$cumucount+$row['count'];
					$resultarray['2d'][$row['bin_floor']]=$cumucount;
				}
			}
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";

			foreach ($resultarray as $key => $value) {
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\": \"" . $key .  "\",\n";

				//if ($key == "template") {
					//$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "complement") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}else if ($key == "2d") {
				//	$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
				//}
				$jsonstring = $jsonstring . "\"data\":[";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[ $key2 , $value2 ],";
				}

				$jsonstring = $jsonstring . "]\n";
		$jsonstring = $jsonstring . "},\n";
			}

		}
		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}

##### Barcoding Coverage Over Time

##SELECT count(*) as count,FLOOR((start_time+duration)/1) as time,refid,refname,reffile,barcode_arrangement FROM last_align_maf_basecalled_2d inner join basecalled_template using (basename_id) inner join reference_seq_info using (refid) inner join barcode_assignment using (basename_id) where alignnum = 1 group by refid,barcode_arrangement,FLOOR((start_time+duration)/1) order by FLOOR((start_time+duration)/1);

function barcodwimm($jobname,$currun,$type) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql = "SELECT count(*) as count,FLOOR((start_time+duration)/1) as time,refid,refname,reffile,barcode_arrangement FROM last_align_maf_basecalled_" . $type . " inner join basecalled_template using (basename_id) inner join reference_seq_info using (refid) inner join barcode_assignment using (basename_id) where alignnum = 1 group by barcode_arrangement,FLOOR((start_time+duration)/1) order by FLOOR((start_time+duration)/1);";

			//echo $sql . "\n";

			//echo "\n$type\n";
			$result = $mindb_connection->query($sql);

			//array to store the results
			$resultarray;

			//refidarray - to store individual reference elements
			$refidarray;
			$refiddescarray;

			if ($result->num_rows >=1) {
				//echo "We're in the loop";
				$cumucount1;
				foreach ($result as $row) {
					### We're just going to track the barcodes over time - we're going to ignore the reference sequence at this point
					if (!in_array($row['barcode_arrangement'], $refidarray)){
					    $refidarray[] = $row['barcode_arrangement'];
					   // echo $row['barcode_arrangement']."\n";

					    $refiddescarray[$row['barcode_arrangement']]=$row['barcode_arrangement'];
					}
					//echo $type . "\n";
					//echo $row['time'] . "\n";
					//echo $row['barcode_arrangement'] . "\n";
					//$resultarray['$type'][$row['time']][$row['barcode_arrangement']]['barcodename']=$row['barcode_arrangement'];
					$resultarray[$type][$row['time']][$row['barcode_arrangement']]['barcodename']=$row['barcode_arrangement'];
					#$resultarray['$type'][$row['time']][$row['refid']]['reffile']=$row['reffile'];
					$resultarray[$type][$row['time']][$row['barcode_arrangement']]['count']=$row['count'];
					$cumucount1[$row['barcode_arrangement']] = $cumucount1[$row['barcode_arrangement']] + $row['count'];
					$resultarray[$type][$row['time']][$row['barcode_arrangement']]['cumucount']=$cumucount1[$row['barcode_arrangement']];
				}
			}

			//var_dump ($resultarray);

			$jsonstring;
			$jsonstring = $jsonstring . "[\n";

			foreach ($resultarray as $key => $value) {
				//echo $key . "\n";

				asort($refidarray);
				foreach ($refidarray as $index){
					//echo $index . "\n";

					$cumu = 0;
					$jsonstring = $jsonstring . "{\n";
					$jsonstring = $jsonstring . "\"name\": \"" . $refiddescarray[$index] .  "\",\n";

					if ($key == "template") {
						$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
					}else if ($key == "complement") {
						$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
					}else if ($key == "2d") {
						$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
					}
					$jsonstring = $jsonstring . "\"data\":[";
					foreach ($value as $key2 => $value2) {
						$cumu = $cumu + $value2[$index]['count'];
						$jsonstring = $jsonstring . "[ $key2 , $cumu ],";
					}

					$jsonstring = $jsonstring . "]\n";
					$jsonstring = $jsonstring . "},\n";


				}
							}
			$jsonstring = $jsonstring . "]\n";

			if ($_GET["prev"] == 1){
				#include 'savejson.php';
			}
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	$memcache->delete("$checkrunning");
    return $jsonstring;

}




##### Barcoding Coverage Summary

function barcodingcov($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...

			$jsonstring = $jsonstring . "[\n";

			//Barcode List
			$barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12"];

			//Fetch the coverage depth for each barcode:
			$barcov = "select ref_id, avg(count) as avecount from (SELECT ref_id, (A+T+G+C) as count, ref_pos FROM reference_coverage_barcode_2d) as refcounts group by ref_id;";
			$barcovquery = $mindb_connection->query($barcov);


			$refinfo = "SELECT refid,refname FROM reference_seq_info;";
			$refinfoquery  = $mindb_connection->query($refinfo);

			//Dump the ref info into a lookup table
			$reflookup=[];


			if ($refinfoquery->num_rows >= 1) {
				foreach ($refinfoquery as $row) {
					$reflookup[$row['refid']]=$row['refname'];
					#print $row['refid'] . " " . $row['refname'] . "\n";
				}
			}
			//Dump the barcode coverage info into a lookup table after splitting the barcode from the reference

			$barcodlookup=[];
			if ($barcovquery->num_rows >= 1) {
				foreach ($barcovquery as $row) {
					$referenceids = explode("_", $row['ref_id']);
					$barcodlookup[$reflookup[$referenceids[0]]][$referenceids[1]]=$row['avecount'];
					#print $reflookup[$referenceids[0]] . " " . $referenceids[1] . " " . $row['avecount'] . "\n";
				}
			}
			//var_dump ($barcodlookup);
			//Plot the results in a lovely bargraph with beautiful data presentation skills.
			foreach ($barcodlookup as $key=>$value){
				#print $key . "\n";
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\": \"". $key. "\",\n";
				$jsonstring = $jsonstring . "\"data\":\n";
   				$jsonstring = $jsonstring . "[\n";
				//foreach ($value as $key2=>$value2) {
					#print $key2 . " " . $value2 . "\n";
				//	$jsonstring = $jsonstring . "[\"" . $key2	. "\",".$value2 . "],\n";
				//}
				foreach ($barcodes as $barcode) {
					if ($value[$barcode] > 0){
						$jsonstring = $jsonstring . "[\"" . $barcode . "\",".$value[$barcode] . "],\n";
					}else{
						$jsonstring = $jsonstring . "[\"" . $barcode . "\",0],\n";
					}
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
			}


		}

		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			//include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}

##### Barcoding summary pie chart data

function barcodingpie($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...
			$jsonstring = $jsonstring . "[\n";
			$jsonstring = $jsonstring . "{\n";
			$barcodequery = "SELECT count(*) as count,barcode_arrangement FROM barcode_assignment group by barcode_arrangement order by barcode_arrangement;";
			$barcodesearch = $mindb_connection->query($barcodequery);

			if ($barcodesearch->num_rows >= 1) {
				$jsonstring = $jsonstring . "\"type\": \"pie\",\n";
				$jsonstring = $jsonstring . "\"name\": \"Barcode Share\",\n";
   				$jsonstring = $jsonstring . "\"data\":\n";
   				$jsonstring = $jsonstring . "[\n";
				foreach ($barcodesearch as $row){
					$jsonstring = $jsonstring . "[\"" . $row['barcode_arrangement']	. "\",".$row['count'] . "],\n";
				}

			}

			#$checkfails = "select count(*) as count from basecalled_2d where basename_id not in (select basename_id from barcode_assignment);";
			#$checkfailssearch = $mindb_connection->query($checkfails);

			#if ($checkfailssearch->num_rows >= 1) {
		#		foreach ($checkfailssearch as $row) {
		#			$jsonstring = $jsonstring . "[\"" . "UC" . "\",".$row['count'] . "],\n";
		#		}
			#}

			$jsonstring = $jsonstring . "]\n";
		}
		$jsonstring = $jsonstring . "}\n";
		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			//include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}


##########Code to generate kmer statistics about a run.

function kmerstats($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$jsonstring = $jsonstring . "[\n";

			###Get list of tables that we need to process

			$template_table = "show tables like \"caller_basecalled_template%\";";
			$template_tables = $mindb_connection->query($template_table);

			$templatemeans=[];

			if ($template_tables->num_rows >= 1) {
				foreach ($template_tables as $row=>$value) {
					#echo $row . "\n";
					foreach ($value as $monkey=>$tree) {
						#echo $tree . "\n";
						$template_query = "select mean, length,model_state from $tree;";
						$template_querysql = $mindb_connection->query($template_query);
						if ($template_querysql->num_rows >= 1) {
							foreach ($template_querysql as $row) {
								#echo $row['mean'] . "\n";
								$templatemeans[$row['model_state']]['mean'][]=$row['mean'];
								$templatemeans[$row['model_state']]['time'][]=$row['length'];

							}
						}

					}
				}
			}
			ksort ($templatemeans);
			echo "KMER\tMean_current\tMedian_current\tSTERR_current\tMin_current\tMax_current\tMean_time\tMedian_time\tSTERR_time\tMin_time\tMax_time\n";
			foreach ($templatemeans as $row => $value){
				echo $row  . "\t";
				echo mmmr($value['mean'],mean) . "\t";
				echo mmmr($value['mean'],median) . "\t";
				echo (mmmr($value['mean'],stddev)/sqrt(count($value['mean']))) . "\t";
				echo mmmr($value['mean'],min) . "\t";
				echo mmmr($value['mean'],max) . "\t";
				#echo "Mode " . mmmr($value['mean'],mode) . "\t";
				#echo "Range " . mmmr($value['mean'],range) . "\n";
				#echo "STDV " . mmmr($value['mean'],stddev) . "\n";

				#echo $value . "\n";
				echo mmmr($value['time'],mean) . "\t";
				echo mmmr($value['time'],median) . "\t";
				echo (mmmr($value['time'],stddev)/sqrt(count($value['mean']))) . "\t";
				echo mmmr($value['time'],min) . "\t";
				echo mmmr($value['time'],max) . "\t";
				echo "\n";
			}

			$jsonstring = $jsonstring . "]\n";
			$jsonstring = $jsonstring . "},\n";





		}


		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}


##########Code to generate basic statistics about a run.

function basicstats($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$jsonstring = $jsonstring . "[\n";
			### We will recover an array of sequence lengths for the template, complement and 2d sequences
			$sql_template_lengths = 'select length(sequence) as len from basecalled_template order by length(sequence);';
			$sql_complement_lengths = 'select length(sequence) as len from basecalled_complement order by length(sequence);';
			$sql_2d_lengths = 'select length(sequence) as len from basecalled_2d order by length(sequence);';

			$sql_template_lengths_result=$mindb_connection->query($sql_template_lengths);
			$sql_complement_lengths_result=$mindb_connection->query($sql_complement_lengths);
			$sql_2d_lengths_result=$mindb_connection->query($sql_2d_lengths);

			$template_lengths=[];
			$complement_lengths=[];
			$twod_lengths=[];



			if ($sql_template_lengths_result->num_rows >=1) {
				foreach ($sql_template_lengths_result as $row) {
					$template_lengths[] = $row['len'];
				}
			}
			if ($sql_complement_lengths_result->num_rows >=1) {
				foreach ($sql_complement_lengths_result as $row) {
					$complement_lengths[] = $row['len'];
				}
			}
			if ($sql_2d_lengths_result->num_rows >=1) {
				foreach ($sql_2d_lengths_result as $row) {
					$twod_lengths[] = $row['len'];
				}
			}
			$jsonstring = $jsonstring . "{\n";
			$jsonstring = $jsonstring . "\"name\" : \"template\",\n";
			$jsonstring = $jsonstring . "\"data\" : [\n";
			$jsonstring = $jsonstring . "\"median\" : " . mmmr($template_lengths,median) . ",\n";
			$jsonstring = $jsonstring . "\"percentile90\" : " . $template_lengths[round( 90/100 * count($complement_lengths))] . ",\n";


			#echo "Template median is : " . mmmr($template_lengths,median) . "\n";
			#echo "Template array length is: " . count($template_lengths). "\n";
			#echo "90 percentile is value: " . round( 90/100 * count($template_lengths)) . "\n";
			#echo "90 percentile length is: " . $template_lengths[round( 90/100 * count($template_lengths))] . "\n";
			#echo "Template length is: " .array_sum($template_lengths) . "\n";

			$cumulen = 0;
			foreach ($template_lengths as $row) {
				$cumulen = $cumulen + $row;
				if ($cumulen >= array_sum($template_lengths)) {
					#echo "N50 length is: " . $row . "\n";
					$jsonstring = $jsonstring . "\"N50\" : " . $row . ",\n";
					last;
				}
			}

			$jsonstring = $jsonstring . "]\n";
			$jsonstring = $jsonstring . "},\n";

			$jsonstring = $jsonstring . "{\n";
			$jsonstring = $jsonstring . "\"name\" : \"complement\",\n";
			$jsonstring = $jsonstring . "\"data\" : [\n";
			$jsonstring = $jsonstring . "\"median\" : " . mmmr($complement_lengths,median) . ",\n";
			$jsonstring = $jsonstring . "\"percentile90\" : " . $complement_lengths[round( 90/100 * count($complement_lengths))] . ",\n";


			#echo "Complement median is : " . mmmr($complement_lengths,median) . "\n";
			#echo "Complement array length is: " . count($complement_lengths). "\n";
			#echo "90 percentile is value: " . round( 90/100 * count($complement_lengths)) . "\n";
			#echo "90 percentile length is: " . $complement_lengths[round( 90/100 * count($complement_lengths))] . "\n";
			#echo "Complement length is: " .array_sum($complement_lengths) . "\n";
			$cumulen = 0;
			foreach ($complement_lengths as $row) {
				$cumulen = $cumulen + $row;
				if ($cumulen >= array_sum($complement_lengths)) {
					#echo "N50 length is: " . $row . "\n";
					$jsonstring = $jsonstring . "\"N50\" : " . $row . ",\n";
					last;
				}
			}

			$jsonstring = $jsonstring . "]\n";
			$jsonstring = $jsonstring . "},\n";

			$jsonstring = $jsonstring . "{\n";
			$jsonstring = $jsonstring . "\"name\" : \"2d\",\n";
			$jsonstring = $jsonstring . "\"data\" : [\n";
			$jsonstring = $jsonstring . "\"median\" : " . mmmr($twod_lengths,median) . ",\n";
			$jsonstring = $jsonstring . "\"percentile90\" : " . $twod_lengths[round( 90/100 * count($complement_lengths))] . ",\n";



			#echo "2d median is : " . mmmr($twod_lengths,median) . "\n";
			#echo "2d array length is: " . count($twod_lengths). "\n";
			#echo "90 percentile is value: " . round( 90/100 * count($twod_lengths)) . "\n";
			#echo "90 percentile length is: " . $twod_lengths[round( 90/100 * count($twod_lengths))] . "\n";
			#echo "2d length is: " .array_sum($twod_lengths) . "\n";
			$cumulen = 0;
			foreach ($twod_lengths as $row) {
				$cumulen = $cumulen + $row;
				if ($cumulen >= array_sum($twod_lengths)) {
					#echo "N50 length is: " . $row . "\n";
					$jsonstring = $jsonstring . "\"N50\" : " . $row . ",\n";
					last;
				}
			}

			$jsonstring = $jsonstring . "]\n";
			$jsonstring = $jsonstring . "},\n";





		}


		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}

########### Plots considering 5mers
#### Generate a plot of all 5mers
function kmercomp($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//Get the frequency of kmers in the reference
			$ref_kmers="SELECT kmer,freq FROM ref_sequence_kmer order by kmer;";
			$ref_kmers_result=$mindb_connection->query($ref_kmers);
			$refkmers=array();
			foreach ($ref_kmers_result as $key->$value){
				$refkmers[$key]=$value;
			}
			//Get the frequency of kmers in a random selectio of 500 template reads.
			$kmersample = "SELECT sequence FROM basecalled_template where rand()<=0.01 limit 500;";
			$kmersampleresults = $mindb_connection->query($kmersample);
			$templatekmercount=array();
			$kmertemplatecount=0;
			if ($kmersampleresults->num_rows>=1) {
				foreach ($kmersampleresults as $row){
					//echo $row[sequence] . "<br>";
					//Loop through each sequence and get the kmers
					for ($i = 0; $i <= (strlen($row[sequence])-5) ; $i++) {
						$substr =  substr($row[sequence], $i, 5);
						$kmertemplatecount++;
						if (isset($templatekmercount[$substr])) {
							 $templatekmercount[$substr]++;
						} else {
							$templatekmercount[$substr] = 1;
						}
					}
				}
			}
			foreach ($templatelmetcount as $key->$vaule) {
				echo "$key	$value<br>";
			}
		}
		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}

function kmercoveragereads($jobname,$currun,$refid) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something intersting here...
			###This will generate total counts for events from the two tables
			## Note we are going to have to reverse complement the complement strand



			$sql_template_total="select count(*) from caller_basecalled_template;";
			$sql_complement_total="select count(*) from caller_basecalled_complement;";

			###This will select individual event counts based on model state

			$sql_template_ind="SELECT count(*),model_state FROM caller_basecalled_template group by model_state order by model_state;";
			$sql_complement_ind="SELECT count(*),model_state FROM caller_basecalled_complement group by model_state order by model_state;";

			###This will select individual event counts based on the most probable 5mer ignoring the consensus sequence

			$sql_template_ind_prob  ="SELECT count(*),mp_state FROM caller_basecalled_template group by mp_state order by mp_state;";
			$sql_complement_ind_prob  ="SELECT count(*),mp_state FROM caller_basecalled_complement group by mp_state order by mp_state;";

			###This will select model positions which disagree with the most probable 5mer ignoring the consensus sequence
			$sql_template_disagree ="SELECT count(*),model_state FROM caller_basecalled_template where model_state != mp_state group by model_state order by model_state;";
			$sql_complement_disagree ="SELECT count(*),model_state FROM caller_basecalled_complement where model_state != mp_state group by model_state order by model_state;";

			###Would be nice to pick 5mers which disagree with the reference at a given position. This needs to be post processed for every sequence though.

			$sql_template_total_result=$mindb_connection->query($sql_template_total);
			$sql_complement_total_result=$mindb_connection->query($sql_complement_total);
			$sql_template_ind_result=$mindb_connection->query($sql_template_ind);
			$sql_complement_ind_result=$mindb_connection->query($sql_complement_ind);
			$sql_template_ind_prob_result=$mindb_connection->query($sql_template_ind_prob);
			$sql_complement_ind_prob_result=$mindb_connection->query($sql_complement_ind_prob);

			### These two queries are very slow
			//$sql_template_disagree_result=$mindb_connection->query($sql_template_disagree);
			//$sql_complement_disagree_result=$mindb_connection->query($sql_complement_disagree);

			var_dump ($sql_template_total_result);

		}
		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;

}

##### Simple code to reverse complement a 5mer
function Complement($seq){
        // change the sequence to upper case
        $seq = strtoupper ($seq);
        // the system used to get the complementary sequence is simple but fas
        $seq=str_replace("A", "t", $seq);
        $seq=str_replace("T", "a", $seq);
        $seq=str_replace("G", "c", $seq);
        $seq=str_replace("C", "g", $seq);
        $seq=str_replace("Y", "r", $seq);
        $seq=str_replace("R", "y", $seq);
        $seq=str_replace("W", "w", $seq);
        $seq=str_replace("S", "s", $seq);
        $seq=str_replace("K", "m", $seq);
        $seq=str_replace("M", "k", $seq);
        $seq=str_replace("D", "h", $seq);
        $seq=str_replace("V", "b", $seq);
        $seq=str_replace("H", "d", $seq);
        $seq=str_replace("B", "v", $seq);
        // change the sequence to upper case again for output
        $seq = strtoupper ($seq);
		$seq = strrev ( $seq );
        return $seq;
}


#### Plot to generate snp coverage over a specific region taking in a fixed point which represents the event of interest also including the read type under investigation

function basesnpcoveragepos($jobname,$currun,$refid,$position,$type,$barcodecheck) {
	##We're not going to store this in memcahce as it isn't a dynamic plot.
	##Construct our query...
	global $mindb_connection;
	if ($barcodecheck >= 1 && $type == "2d") {
		$sql = "select *, @var_max_val:= GREATEST(A,T,G,C) AS max_value,
       CASE @var_max_val WHEN A THEN 'A'
                         WHEN T THEN 'T'
                         WHEN C THEN 'C'
                         WHEN G THEN 'G'
       END AS max_value_column_name from reference_coverage_barcode_" . $type ." where ref_id = '".$refid."' and ref_pos >= ".$position."-100 and ref_pos <= ".$position."+100;";
      // echo $sql;
	}else{
		$sql = "select *, @var_max_val:= GREATEST(A,T,G,C) AS max_value,
       CASE @var_max_val WHEN A THEN 'A'
                         WHEN T THEN 'T'
                         WHEN C THEN 'C'
                         WHEN G THEN 'G'
       END AS max_value_column_name from reference_coverage_" . $type ." where ref_id = ".$refid." and ref_pos >= ".$position."-100 and ref_pos <= ".$position."+100;";
	}
       //echo $sql;
       $result=$mindb_connection->query($sql);

       $resultarray;
       $refidarray;
       $refidescarray;
       $basearray;
       if ($result->num_rows >=1) {
				foreach ($result as $row) {
					$resultarray[$type][$row['ref_id']]['A'][$row['ref_pos']]=$row['A'];
					$resultarray[$type][$row['ref_id']]['T'][$row['ref_pos']]=$row['T'];
					$resultarray[$type][$row['ref_id']]['G'][$row['ref_pos']]=$row['G'];
					$resultarray[$type][$row['ref_id']]['C'][$row['ref_pos']]=$row['C'];
					$resultarray[$type][$row['ref_id']]['D'][$row['ref_pos']]=$row['D'];
					$resultarray[$type][$row['ref_id']]['I'][$row['ref_pos']]=$row['I'];
					//$resultarray[$type][$row['ref_id']]['ref'][$row['ref_pos']]=$row['ref_seq'];

					$bases = array("A", "T", "G", "C");
					foreach ($bases as $value) {
						if ($row['ref_seq'] == $value && $row['ref_seq'] != $row['max_value_column_name']) {
							$basearray[$value][$row['ref_pos']]=1;
						}elseif ($row['ref_seq'] == $value) {
							$basearray[$value][$row['ref_pos']]=0.5;
						}else{
							$basearray[$value][$row['ref_pos']]=0;
						}
					}
				}
			}
			$jsonstring;
		$jsonstring = $jsonstring . "[\n";
		foreach ($basearray as $key=> $value) {
			$jsonstring = $jsonstring . "{\n";
			$jsonstring = $jsonstring . "\"name\":\"ref " . $key . "\",\n";
			if ($key == "A"){
				$jsonstring = $jsonstring . "\"color\": 'blue',\n";
			}
			if ($key == "T"){
				$jsonstring = $jsonstring .  "\"color\": 'yellow',\n";
			}
			if ($key == "G"){
				$jsonstring = $jsonstring .  "\"color\": 'green',\n";
			}
			if ($key == "C"){
				$jsonstring = $jsonstring .  "\"color\": 'red',\n";
			}
			$jsonstring = $jsonstring . "\"yAxis\": 1,\n";
			$jsonstring = $jsonstring . "\"pointPadding\": 0.1,\n";
			$jsonstring = $jsonstring . "\"groupPadding\": 0.1,\n";
			$jsonstring = $jsonstring . "\"borderWidth\": 0.1,\n";
			$jsonstring = $jsonstring . "\"shadow\": false,\n";
			$jsonstring = $jsonstring . "\"data\":[";
			foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[ $key2 , $value2 ],";
			}
			$jsonstring = $jsonstring . "]\n";
			$jsonstring = $jsonstring . "},\n";
		}
		foreach ($resultarray as $key => $value) {
			foreach ($value as $key2 => $value2){
				foreach ($value2 as $key3 => $value3){
					$jsonstring = $jsonstring . "{\n";
						$jsonstring = $jsonstring . "\"name\":\"" . $key3 .  "\",\n";
						#				echo $key . "\n";
						#			echo $value . "\n";
						if ($key3 == "A"){
							$jsonstring = $jsonstring . "\"color\": 'blue',\n";
						}
						if ($key3 == "T"){
							$jsonstring = $jsonstring .  "\"color\": 'yellow',\n";
						}
						if ($key3 == "G"){
							$jsonstring = $jsonstring .  "\"color\": 'green',\n";
						}
						if ($key3 == "C"){
							$jsonstring = $jsonstring .  "\"color\": 'red',\n";
						}
						if ($key3 == "I"){
							$jsonstring = $jsonstring .  "\"color\": 'grey',\n";
						}
						if ($key3 == "D"){
							$jsonstring = $jsonstring .  "\"color\": 'black',\n";
						}
						if ($key == "template") {
							$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
						}else if ($key == "complement") {
							$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
						}else if ($key == "2d") {
							$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
						}
						$jsonstring = $jsonstring . "\"pointPadding\": 0.1,\n";
						$jsonstring = $jsonstring . "\"groupPadding\": 0.1,\n";
						$jsonstring = $jsonstring . "\"borderWidth\": 0.1,\n";
						$jsonstring = $jsonstring . "\"shadow\": false,\n";
						$jsonstring = $jsonstring . "\"data\":[";

#						echo $key2 . "\n";

#						echo $key3 . "\n";
						foreach ($value3 as $key4 => $value4){
#							echo $value4 . "\n";
							$jsonstring = $jsonstring . "[ $key4 , $value4 ],";
						}
						$jsonstring = $jsonstring . "]\n";
						$jsonstring = $jsonstring . "},\n";
				}
			}
		}
		$jsonstring = $jsonstring . "]\n";
		return $jsonstring;
}


#### This aim of this plot is to provide a dynamic view of bases aligned and called over the reference

function basesnpcoverage($jobname,$currun,$refid,$start,$end,$type) {
	$checkvar = $currun . $jobname . $refid;
	$checkrunning = $currun . $jobname .$refid . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$checkingrunning = $memcache->get("$checkrunning");
	if ($checkingrunning === "No" || $checkingrunning === FALSE) {
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows == 1) {
			foreach ($checking as $row) {
				$jsonstring=$row['json'];
			}
		}else{

			$sql_query = "select *, @var_max_val:= GREATEST(A,T,G,C) AS max_value,
       CASE @var_max_val WHEN A THEN 'A'
                         WHEN T THEN 'T'
                         WHEN C THEN 'C'
                         WHEN G THEN 'G'
       END AS max_value_column_name from reference_coverage_".$type." where ref_id = " . $refid . " and ref_pos >= " . $start ." and ref_pos <= ".$end.";";

       		//echo $sql_query ;
       		$result = $mindb_connection->query($sql_query);
			//array to store the results
			$resultarray;
			//refidarray - to store individual reference elements
			$refidarray;
			$refiddescarray;

			if ($result->num_rows >=1) {
				foreach ($result as $row) {
					$resultarray[$type][$row['ref_id']]['A'][$row['ref_pos']]=$row['A'];
					$resultarray[$type][$row['ref_id']]['T'][$row['ref_pos']]=$row['T'];
					$resultarray[$type][$row['ref_id']]['G'][$row['ref_pos']]=$row['G'];
					$resultarray[$type][$row['ref_id']]['C'][$row['ref_pos']]=$row['C'];
					$resultarray[$type][$row['ref_id']]['D'][$row['ref_pos']]=$row['D'];
					$resultarray[$type][$row['ref_id']]['I'][$row['ref_pos']]=$row['I'];
				}
			}

		}
		$jsonstring;
		$jsonstring = $jsonstring . "[\n";
		foreach ($resultarray as $key => $value) {
			foreach ($value as $key2 => $value2){
				foreach ($value2 as $key3 => $value3){
					$jsonstring = $jsonstring . "{\n";
						$jsonstring = $jsonstring . "\"name\":\"" . $key3 .  "\",\n";
						#				echo $key . "\n";
						#			echo $value . "\n";
						if ($key3 == "A"){
							$jsonstring = $jsonstring . "\"color\": 'blue',\n";
						}
						if ($key3 == "T"){
							$jsonstring = $jsonstring .  "\"color\": 'yellow',\n";
						}
						if ($key3 == "G"){
							$jsonstring = $jsonstring .  "\"color\": 'green',\n";
						}
						if ($key3 == "C"){
							$jsonstring = $jsonstring .  "\"color\": 'red',\n";
						}
						if ($key3 == "I"){
							$jsonstring = $jsonstring .  "\"color\": 'grey',\n";
						}
						if ($key3 == "D"){
							$jsonstring = $jsonstring .  "\"color\": 'black',\n";
						}
						if ($key == "template") {
							$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
						}else if ($key == "complement") {
							$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
						}else if ($key == "2d") {
							$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
						}
						$jsonstring = $jsonstring . "\"pointPadding\": 0.1,\n";
						$jsonstring = $jsonstring . "\"groupPadding\": 0.1,\n";
						$jsonstring = $jsonstring . "\"borderWidth\": 0.1,\n";
						$jsonstring = $jsonstring . "\"shadow\": false,\n";
						$jsonstring = $jsonstring . "\"data\":[";

#						echo $key2 . "\n";

#						echo $key3 . "\n";
						foreach ($value3 as $key4 => $value4){
#							echo $value4 . "\n";
							$jsonstring = $jsonstring . "[ $key4 , $value4 ],";
						}
						$jsonstring = $jsonstring . "]\n";
						$jsonstring = $jsonstring . "},\n";
				}
			}
		}
		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			#include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;


}



function basesnpcoverage_OLD($jobname,$currun,$refid,$start,$end,$type) {
	$checkvar = $currun . $jobname . $refid;
	$checkrunning = $currun . $jobname .$refid . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$checkingrunning = $memcache->get("$checkrunning");
	if ($checkingrunning === "No" || $checkingrunning === FALSE) {
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows == 1) {
			foreach ($checking as $row) {
				$jsonstring=$row['json'];
			}
		}else{

			$sql_template = "select *, @var_max_val:= GREATEST(A,T,G,C) AS max_value,
       CASE @var_max_val WHEN A THEN 'A'
                         WHEN T THEN 'T'
                         WHEN C THEN 'C'
                         WHEN G THEN 'G'
       END AS max_value_column_name from reference_coverage_template where ref_id = " . $refid . " and ref_pos >= " . $start ." and ref_pos <= ".$end.";";
       		$sql_complement = "select *, @var_max_val:= GREATEST(A,T,G,C) AS max_value,
       CASE @var_max_val WHEN A THEN 'A'
                         WHEN T THEN 'T'
                         WHEN C THEN 'C'
                         WHEN G THEN 'G'
       END AS max_value_column_name from reference_coverage_complement where ref_id = " . $refid . " and ref_pos >= " . $start ." and ref_pos <= ".$end.";";
       		$sql_2d = "select *, @var_max_val:= GREATEST(A,T,G,C) AS max_value,
       CASE @var_max_val WHEN A THEN 'A'
                         WHEN T THEN 'T'
                         WHEN C THEN 'C'
                         WHEN G THEN 'G'
       END AS max_value_column_name from reference_coverage_2d where ref_id = " . $refid . " and ref_pos >= " . $start ." and ref_pos <= ".$end.";";
       		$resulttemplate = $mindb_connection->query($sql_template);
			$resultcomplement = $mindb_connection->query($sql_complement);
			$result2d = $mindb_connection->query($sql_2d);
			//array to store the results
			$resultarray;
			//refidarray - to store individual reference elements
			$refidarray;
			$refiddescarray;

			if ($resulttemplate->num_rows >=1) {
				foreach ($resulttemplate as $row) {
					$resultarray['template'][$row['ref_id']]['A'][$row['ref_pos']]=$row['A'];
					$resultarray['template'][$row['ref_id']]['T'][$row['ref_pos']]=$row['T'];
					$resultarray['template'][$row['ref_id']]['G'][$row['ref_pos']]=$row['G'];
					$resultarray['template'][$row['ref_id']]['C'][$row['ref_pos']]=$row['C'];
					$resultarray['template'][$row['ref_id']]['D'][$row['ref_pos']]=$row['D'];
					$resultarray['template'][$row['ref_id']]['I'][$row['ref_pos']]=$row['I'];
				}
			}
			if ($resultcomplement->num_rows >=1) {
				foreach ($resultcomplement as $row) {
					$resultarray['complement'][$row['ref_id']]['A'][$row['ref_pos']]=$row['A'];
					$resultarray['complement'][$row['ref_id']]['T'][$row['ref_pos']]=$row['T'];
					$resultarray['complement'][$row['ref_id']]['G'][$row['ref_pos']]=$row['G'];
					$resultarray['complement'][$row['ref_id']]['C'][$row['ref_pos']]=$row['C'];
					$resultarray['complement'][$row['ref_id']]['D'][$row['ref_pos']]=$row['D'];
					$resultarray['complement'][$row['ref_id']]['I'][$row['ref_pos']]=$row['I'];
				}
			}
			if ($result2d->num_rows >=1) {
				foreach ($result2d as $row) {
					$resultarray['2d'][$row['ref_id']]['A'][$row['ref_pos']]=$row['A'];
					$resultarray['2d'][$row['ref_id']]['T'][$row['ref_pos']]=$row['T'];
					$resultarray['2d'][$row['ref_id']]['G'][$row['ref_pos']]=$row['G'];
					$resultarray['2d'][$row['ref_id']]['C'][$row['ref_pos']]=$row['C'];
					$resultarray['2d'][$row['ref_id']]['D'][$row['ref_pos']]=$row['D'];
					$resultarray['2d'][$row['ref_id']]['I'][$row['ref_pos']]=$row['I'];
				}
			}
		}
		$jsonstring;
		$jsonstring = $jsonstring . "[\n";
		foreach ($resultarray as $key => $value) {
			foreach ($value as $key2 => $value2){
				foreach ($value2 as $key3 => $value3){
					$jsonstring = $jsonstring . "{\n";
						$jsonstring = $jsonstring . "\"name\":\"" . $key3 .  "\",\n";
						#				echo $key . "\n";
						#			echo $value . "\n";
						if ($key3 == "A"){
							$jsonstring = $jsonstring . "\"color\": 'blue',\n";
						}
						if ($key3 == "T"){
							$jsonstring = $jsonstring .  "\"color\": 'yellow',\n";
						}
						if ($key3 == "G"){
							$jsonstring = $jsonstring .  "\"color\": 'green',\n";
						}
						if ($key3 == "C"){
							$jsonstring = $jsonstring .  "\"color\": 'red',\n";
						}
						if ($key3 == "I"){
							$jsonstring = $jsonstring .  "\"color\": 'grey',\n";
						}
						if ($key3 == "D"){
							$jsonstring = $jsonstring .  "\"color\": 'black',\n";
						}
						if ($key == "template") {
							$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
						}else if ($key == "complement") {
							$jsonstring = $jsonstring . "\"yAxis\": 1,\n";
						}else if ($key == "2d") {
							$jsonstring = $jsonstring . "\"yAxis\": 2,\n";
						}
						$jsonstring = $jsonstring . "\"pointPadding\": 0.1,\n";
						$jsonstring = $jsonstring . "\"groupPadding\": 0.1,\n";
						$jsonstring = $jsonstring . "\"borderWidth\": 0.1,\n";
						$jsonstring = $jsonstring . "\"shadow\": false,\n";
						$jsonstring = $jsonstring . "\"data\":[";

#						echo $key2 . "\n";

#						echo $key3 . "\n";
						foreach ($value3 as $key4 => $value4){
#							echo $value4 . "\n";
							$jsonstring = $jsonstring . "[ $key4 , $value4 ],";
						}
						$jsonstring = $jsonstring . "]\n";
						$jsonstring = $jsonstring . "},\n";
				}
			}
		}
		$jsonstring = $jsonstring . "]\n";
		if ($_GET["prev"] == 1){
			#include 'savejson.php';
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...
	$memcache->delete("$checkrunning");
   	return $jsonstring;


}
#### Time to recreate the old what's in my pot - here called whatsinmyminion - this version pulls in all reads (template complement and 2d) which doesn't seem to work very well - This is the current live version of the WIMM plot

function whatsinmyminion2($jobname,$currun,$type) {
	$checkvar = $currun . $jobname . $type;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . $type . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql = "SELECT count(*) as count,FLOOR((start_time+duration)/1) as time,refid,refname,reffile FROM last_align_maf_basecalled_" . $type . " inner join basecalled_template using (basename_id) inner join reference_seq_info using (refid) where alignnum = 1 group by refid,FLOOR((start_time+duration)/1) order by FLOOR((start_time+duration)/1);";

			//echo $sql . "\n";
			$result = $mindb_connection->query($sql);

			//array to store the results
			$resultarray;

			//refidarray - to store individual reference elements
			$refidarray;
			$refiddescarray;


			if ($result->num_rows >=1) {
				$cumucount1;
				foreach ($result as $row) {
					if (!in_array($row['refid'], $refidarray)){
					    $refidarray[] = $row['refid'];
					    $refiddescarray[$row['refid']]=$row['refname'];
					}
					$resultarray['$type'][$row['time']][$row['refid']]['refname']=$row['refname'];
					$resultarray['$type'][$row['time']][$row['refid']]['reffile']=$row['reffile'];
					$resultarray['$type'][$row['time']][$row['refid']]['count']=$row['count'];
					$cumucount1[$row['refid']] = $cumucount1[$row['refid']] + $row['count'];
					$resultarray['$type'][$row['time']][$row['refid']]['cumucount']=$cumucount1[$row['refid']];
				}
			}

			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value) {
				//echo $key . "\n";


				foreach ($refidarray as $index){
					$cumu = 0;
					$jsonstring = $jsonstring . "{\n";
					$jsonstring = $jsonstring . "\"name\": \"" . $refiddescarray[$index] .  "\",\n";
					if ($key == "template") {
						$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
					}else if ($key == "complement") {
						$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
					}else if ($key == "2d") {
						$jsonstring = $jsonstring . "\"yAxis\": 0,\n";
					}
					$jsonstring = $jsonstring . "\"data\":[";
					foreach ($value as $key2 => $value2) {
						$cumu = $cumu + $value2[$index]['count'];
						$jsonstring = $jsonstring . "[ $key2 , $cumu ],";
					}
					$jsonstring = $jsonstring . "]\n";
					$jsonstring = $jsonstring . "},\n";
				}
			}
			$jsonstring = $jsonstring . "]\n";

			if ($_GET["prev"] == 1){
				#include 'savejson.php';
			}
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	$memcache->delete("$checkrunning");
    return $jsonstring;

}


#### Time to recreate the old what's in my pot - here called whatsinmyminion - this version pulls in all reads (template complement and 2d) which doesn't seem to work very well

function whatsinmyminion($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "SELECT count(*) as count,FLOOR((start_time+duration)/100) as time,refid,refname,reffile FROM last_align_maf_basecalled_template inner join basecalled_template using (basename_id) inner join reference_seq_info using (refid) where alignnum = 1 group by refid,FLOOR((start_time+duration)/100) order by FLOOR((start_time+duration)/100);";
			$sql_complement = "SELECT count(*) as count,FLOOR((start_time+duration)/100) as time,refid,refname,reffile FROM last_align_maf_basecalled_complement inner join basecalled_complement using (basename_id) inner join reference_seq_info using (refid) where alignnum = 1 group by refid,FLOOR((start_time+duration)/100) order by FLOOR((start_time+duration)/100);";
			$sql_2d = "SELECT count(*) as count,FLOOR((start_time+duration)/100) as time,refid,refname,reffile FROM last_align_maf_basecalled_2d inner join basecalled_complement using (basename_id) inner join reference_seq_info using (refid) where alignnum = 1 group by refid,FLOOR((start_time+duration)/100) order by FLOOR((start_time+duration)/100);";
			$resulttemplate = $mindb_connection->query($sql_template);
			$resultcomplement = $mindb_connection->query($sql_complement);
			$result2d = $mindb_connection->query($sql_2d);

			//array to store the results
			$resultarray;

			//refidarray - to store individual reference elements
			$refidarray;
			$refiddescarray;


			if ($resulttemplate->num_rows >=1) {
				$cumucount1;
				foreach ($resulttemplate as $row) {
					if (!in_array($row['refid'], $refidarray)){
					    $refidarray[] = $row['refid'];
					    $refiddescarray[$row['refid']]=$row['refname'];
					}
					$resultarray['template'][$row['time']][$row['refid']]['refname']=$row['refname'];
					$resultarray['template'][$row['time']][$row['refid']]['reffile']=$row['reffile'];
					$resultarray['template'][$row['time']][$row['refid']]['count']=$row['count'];
					$cumucount1[$row['refid']] = $cumucount1[$row['refid']] + $row['count'];
					$resultarray['template'][$row['time']][$row['refid']]['cumucount']=$cumucount1[$row['refid']];
				}
			}
			if ($resultcomplement->num_rows >=1) {
				$cumucount2;
				foreach ($resultcomplement as $row) {
					if (!in_array($row['refid'], $refidarray)){
					    $refidarray[] = $row['refid'];
					    $refiddescarray[$row['refid']]=$row['refname'];
					}
					$resultarray['complement'][$row['time']][$row['refid']]['refname']=$row['refname'];
					$resultarray['complement'][$row['time']][$row['refid']]['reffile']=$row['reffile'];
					$resultarray['complement'][$row['time']][$row['refid']]['count']=$row['count'];
					$cumucount2[$row['refid']] = $cumucount2[$row['refid']] + $row['count'];
					$resultarray['complement'][$row['time']][$row['refid']]['cumucount']=$cumucount2[$row['refid']];
				}
			}
			if ($result2d->num_rows >=1) {
				$cumucount3;
				foreach ($result2d as $row) {
					if (!in_array($row['refid'], $refidarray)){
					    $refidarray[] = $row['refid'];
   					    $refiddescarray[$row['refid']]=$row['refname'];
					}
					$resultarray['2d'][$row['time']][$row['refid']]['refname']=$row['refname'];
					$resultarray['2d'][$row['time']][$row['refid']]['reffile']=$row['reffile'];
					$resultarray['2d'][$row['time']][$row['refid']]['count']=$row['count'];
					$cumucount3[$row['refid']] = $cumucount3[$row['refid']] + $row['count'];
					$resultarray['2d'][$row['time']][$row['refid']]['cumucount']=$cumucount3[$row['refid']];
				}
			}
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value) {
				//echo $key . "\n";


				foreach ($refidarray as $index){
					$cumu = 0;
					$jsonstring = $jsonstring . "{\n";
					$jsonstring = $jsonstring . "\"name\": \"" . $refiddescarray[$index] . " " . $key .  "\",\n";
					$jsonstring = $jsonstring . "\"data\":[";
					foreach ($value as $key2 => $value2) {
						$cumu = $cumu + $value2[$index]['count'];
						$jsonstring = $jsonstring . "[ $key2 , $cumu ],";
					}
					$jsonstring = $jsonstring . "]\n";
					$jsonstring = $jsonstring . "},\n";
				}
			}
			$jsonstring = $jsonstring . "]\n";

			if ($_GET["prev"] == 1){
				#include 'savejson.php';
			}
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	$memcache->delete("$checkrunning");
    return $jsonstring;

}

###select sum(ifnull(length(basecalled_template.sequence),0)+ifnull(length(basecalled_complement.sequence),0)) as bases, channel, start_mux as mux from basecalled_template inner join config_general using (basename_id) left join basecalled_complement using (basename_id) group by channel,mux;

##Bases per pore - plots the number of bases sequenced on a pore by pore bases corrected for the nanopore map - this calculates the sum of template and complement bases sequenced through the pore based on the basecalled sequence.

function basesperporemux($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select sum(ifnull(length(basecalled_template.sequence),0)+ifnull(length(basecalled_complement.sequence),0)) as bases, channel, start_mux as mux from basecalled_template inner join config_general using (basename_id) left join basecalled_complement using (basename_id) group by channel,mux;";


			$resultarray;

			$template=$mindb_connection->query($sql_template);

			###Get the reverse map to decode from channel and mux to position.
			$reverse_map = minion_map()[1];

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					//$resultarray['template'][$row['channel']][$row['mux']]=$row['count'];
					$tempitem = $row['channel'] . "," . $row['mux'];
					//echo $tempitem . "\t";
					$tempitem2 = $reverse_map["$tempitem"];
					//echo $tempitem2 . "\n";
					$temparray = explode( ',', $tempitem2 );
					//echo $temparray[0];
					$resultarray['template'][$temparray[1]][$temparray[0]]=$row['bases'];
				}
			}



			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"borderWidth\": 1,\n";
				$jsonstring = $jsonstring . "\"data\": [";
				for ($i = 75 ; $i >=1; $i--){
					if (array_key_exists($i, $resultarray[$key])){
						for ($j = 75; $j >= 1; $j--) {
							if (array_key_exists($j, $resultarray[$key][$i])) {
								$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . "," . $resultarray[$key][$i][$j] . "],\n";
							}else{
								//$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . ",0],\n";
							}
						}
					}else{
						for ($j = 75; $j >= 1; $j--) {
							//$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . ",0],\n";
						}

					} //closing if statement line 51
				} // closing the for loop at line 50


			$jsonstring = $jsonstring . "],\n\"dataLabels\": {
            \"enabled\": false,
            \"color\":\"black\",
            \"style\": {
            \"textShadow\": \"none\",
            \"fontSize\": \"7\"
            }
           	}	";
			$jsonstring = $jsonstring . "},\n";

		}
		$jsonstring = $jsonstring .  "]\n";
		if ($_GET["prev"] == 1){
			//include 'savejson.php';
		}
	}
	$memcache->set("$checkvar", $jsonstring);
}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	$memcache->delete("$checkrunning");
    return $jsonstring;

}

##Plot the ratio pass reads per pore against the number of reads produced by a Pore
##Plot the ratio of pass to fail reads per pore.
function passfailcountperporemux($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_allpass = "SELECT count(*) as count,channel,start_mux FROM tracking_id inner join config_general using (basename_id) where file_path like \"%pass%\" group by channel,start_mux;";
			$sql_fail = "SELECT count(*) as count,channel,start_mux FROM tracking_id inner join config_general using (basename_id) where file_path like \"\%fail\%\" group by channel,start_mux;";
			$sql_all = "SELECT count(*) as count,channel,start_mux FROM tracking_id inner join config_general using (basename_id) group by channel,start_mux;";


			$allpass=$mindb_connection->query($sql_allpass);
			$fail=$mindb_connection->query($sql_fail);
			$all=$mindb_connection->query($sql_all);

			###Get the reverse map to decode from channel and mux to position.
			$reverse_map = minion_map()[1];

			if ($all->num_rows >= 1){
				//echo "we're in all.\n";
				foreach ($all as $row) {
					$tempitem = $row['channel'] . "," . $row['start_mux'];
					$tempitem = (string)$tempitem;
					//echo "tempitem " . $tempitem . "\n";
					$resultarray["$tempitem"]['all']=$row['count'];
				}
			}
			if ($allpass->num_rows >= 1){
				//echo "we're in pass";
				foreach ($allpass as $row) {
					$tempitem = $row['channel'] . "," . $row['start_mux'];
					$tempitem = (string)$tempitem;
					//echo $tempitem . "tempitem \n";
					$resultarray["$tempitem"]['pass']=$row['count'];
				}
			}else{
				echo "non result badger";
			}
			if ($fail->num_rows >= 1){
				foreach ($fail as $row) {
					$tempitem = $row['channel'] . "," . $row['start_mux'];

					$tempitem = (string)$tempitem;
					$resultarray["$tempitem"]['fail']=$row['count'];
				}
			}



			//var_dump($resultarray);
			//var_dump($resultarrayproc);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			$jsonstring = $jsonstring . "{\n";
			$jsonstring = $jsonstring . "\"name\" : \"counttopass\", \n";
			$jsonstring = $jsonstring . "\"data\": [";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "[" . $resultarray[$key]['all'] . "," . $resultarray[$key]['pass']/$resultarray[$key]['all']*100 . "],\n";
			} // closing the for loop at line 50


			$jsonstring = $jsonstring . "]";
			$jsonstring = $jsonstring . "},\n";

		//}
		$jsonstring = $jsonstring .  "]\n";
		if ($_GET["prev"] == 1){
			//include 'savejson.php';
		}
	}
	$memcache->set("$checkvar", $jsonstring);
}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	$memcache->delete("$checkrunning");
    return $jsonstring;

}



##Plot the ratio of pass to fail reads per pore.
function passfailperporemux($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_allpass = "SELECT count(*) as count,channel,start_mux FROM tracking_id inner join config_general using (basename_id) where file_path like \"%pass%\" group by channel,start_mux;";
			$sql_fail = "SELECT count(*) as count,channel,start_mux FROM tracking_id inner join config_general using (basename_id) where file_path like \"\%fail\%\" group by channel,start_mux;";
			$sql_all = "SELECT count(*) as count,channel,start_mux FROM tracking_id inner join config_general using (basename_id) group by channel,start_mux;";

			$resultarray;

			$allpass=$mindb_connection->query($sql_allpass);
			$fail=$mindb_connection->query($sql_fail);
			$all=$mindb_connection->query($sql_all);

			###Get the reverse map to decode from channel and mux to position.
			$reverse_map = minion_map()[1];

			if ($all->num_rows >= 1){
				//echo "we're in all.\n";
				foreach ($all as $row) {
					$tempitem = $row['channel'] . "," . $row['start_mux'];
					$tempitem = (string)$tempitem;
					//echo "tempitem " . $tempitem . "\n";
					$resultarray["$tempitem"]['all']=$row['count'];
				}
			}
			if ($allpass->num_rows >= 1){
				//echo "we're in pass";
				foreach ($allpass as $row) {
					$tempitem = $row['channel'] . "," . $row['start_mux'];
					$tempitem = (string)$tempitem;
					//echo $tempitem . "tempitem \n";
					$resultarray["$tempitem"]['pass']=$row['count'];
				}
			}else{
				echo "non result badger";
			}
			if ($fail->num_rows >= 1){
				foreach ($fail as $row) {
					$tempitem = $row['channel'] . "," . $row['start_mux'];

					$tempitem = (string)$tempitem;
					$resultarray["$tempitem"]['fail']=$row['count'];
				}
			}

			$resultarrayproc;
			foreach ($resultarray as $chanmux => $value ){
				//echo $chanmux['pass'] . "\n";
				$tempitem2 = $reverse_map["$chanmux"];
				//echo $tempitem2 . "\n";
				$temparray = explode( ',', $tempitem2 );
				//echo $chanmux . "\t" . $value['pass'] . "\t" . $chanmux['pass'] . "\t". $chanmux['fail'] . "\t" . ($chanmux['pass']/$chanmux['all'])*100 . "\n";
				$resultarrayproc['percentpass'][$temparray[1]][$temparray[0]]=($value['pass']/$value['all'])*100;
			}

			//var_dump($resultarray);
			//var_dump($resultarrayproc);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarrayproc as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"borderWidth\": 1,\n";
				$jsonstring = $jsonstring . "\"data\": [";
				for ($i = 75 ; $i >=1; $i--){
					if (array_key_exists($i, $resultarrayproc[$key])){
						for ($j = 75; $j >= 1; $j--) {
							if (array_key_exists($j, $resultarrayproc[$key][$i])) {
								$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . "," . $resultarrayproc[$key][$i][$j] . "],\n";
							}else{
								//$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . ",0],\n";
							}
						}
					}else{
						for ($j = 75; $j >= 1; $j--) {
							//$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . ",0],\n";
						}

					} //closing if statement line 51
				} // closing the for loop at line 50


			$jsonstring = $jsonstring . "],\n\"dataLabels\": {
            \"enabled\": false,
            \"color\":\"black\",
            \"style\": {
            \"textShadow\": \"none\",
            \"fontSize\": \"7\"
            }
           	}	";
			$jsonstring = $jsonstring . "},\n";

		}
		$jsonstring = $jsonstring .  "]\n";
		if ($_GET["prev"] == 1){
			//include 'savejson.php';
		}
	}
	$memcache->set("$checkvar", $jsonstring);
}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	$memcache->delete("$checkrunning");
    return $jsonstring;

}


##Reads per pore - plots the production of reads on a pore by pore basis corrected for the nanopore map
function readsperporemux($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select count(*) as count, channel, start_mux as mux from basecalled_template inner join config_general using (basename_id) group by channel,mux;";


			$resultarray;

			$template=$mindb_connection->query($sql_template);

			###Get the reverse map to decode from channel and mux to position.
			$reverse_map = minion_map()[1];

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					//$resultarray['template'][$row['channel']][$row['mux']]=$row['count'];
					$tempitem = $row['channel'] . "," . $row['mux'];
					//echo $tempitem . "\t";
					$tempitem2 = $reverse_map["$tempitem"];
					//echo $tempitem2 . "\n";
					$temparray = explode( ',', $tempitem2 );
					//echo $temparray[0];
					$resultarray['template'][$temparray[1]][$temparray[0]]=$row['count'];
				}
			}



			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"borderWidth\": 1,\n";
				$jsonstring = $jsonstring . "\"data\": [";
				for ($i = 75 ; $i >=1; $i--){
					if (array_key_exists($i, $resultarray[$key])){
						for ($j = 75; $j >= 1; $j--) {
							if (array_key_exists($j, $resultarray[$key][$i])) {
								$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . "," . $resultarray[$key][$i][$j] . "],\n";
							}else{
								//$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . ",0],\n";
							}
						}
					}else{
						for ($j = 75; $j >= 1; $j--) {
							//$jsonstring = $jsonstring . "[" . ($i-1) . "," . ($j-1) . ",0],\n";
						}

					} //closing if statement line 51
				} // closing the for loop at line 50


			$jsonstring = $jsonstring . "],\n\"dataLabels\": {
            \"enabled\": false,
            \"color\":\"black\",
            \"style\": {
            \"textShadow\": \"none\",
            \"fontSize\": \"7\"
            }
           	}	";
			$jsonstring = $jsonstring . "},\n";

		}
		$jsonstring = $jsonstring .  "]\n";
		if ($_GET["prev"] == 1){
			//include 'savejson.php';
		}
	}
	$memcache->set("$checkvar", $jsonstring);
}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	$memcache->delete("$checkrunning");
    return $jsonstring;

}



#### Function to calculate the correct layout of muxes and pores...
#### Note that this return two arrays - the forward and reverse maps

function minion_map() {
	$NODE_MAP = array(3, 4, 1, 2, 6, 5, 8, 7);
	$MUX_MAP = array(3,4,1,2,2,1,4,3);

	$PORES = range(1, 2048);

	$map = array();
	$rev_map = array();

	foreach ($PORES as &$pore){
		//echo $pore. "\n";
		$channel = (($pore - 1)%128)+1;
		$col = $NODE_MAP[($channel - 1) % 8];
		$block = ceil((1 + ($channel - 1) % 32) / 8);
		$col += 8 * ($block - 1);
		$row = 5 - ceil($channel / 32);
		$pos = ceil($pore / 256);
		$side = 1 + floor(($pore - 1) / 256 + 0.5) - $pos;
		if ($side) {
 			$col = 33 - $col;
 		}
 		$col += ($side * 32);
		$row += ($pos - 1) * 4;
		$channel = ceil($pore / 4);
		$row = 33 - $row;
 		$col = 65 - $col;
 		$mux = $MUX_MAP[($col - 1) % 8];
 		if ($col > 32) {
			$col = $col + 4;
		}
		$col += 7;
		$row = 1 + ($row + 3) % 32;
		$row = 33 - $row;

		$map["$row,$col"] = "$channel,$mux";
		$rev_map["$channel,$mux"] = "$row,$col";
 	}
 	return array($map, $rev_map);

}


##Average Length Over Time - chart showing read lengths over time
function average_length_over_time($jobname,$currrun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
			$checking=$mindb_connection->query($checkrow);
			if ($checking->num_rows ==1){
				//echo "We have already run this!";
				foreach ($checking as $row){
					$jsonstring = $row['json'];
				}
			} else {
			$sql_template = "select (floor(start_time/60)*60 + exp_start_time) *1000 as bin_floor, ROUND(AVG(length(sequence))) as average_length from basecalled_template inner join tracking_id using (basename_id) group by 1 order by 1;";
			$sql_complement = "select (floor(start_time/60)*60 + exp_start_time) *1000 as bin_floor, ROUND(AVG(length(sequence))) as average_length from basecalled_complement inner join tracking_id using (basename_id) group by 1 order by 1;";

			$resultarray;

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template'][$row['bin_floor']]=$row['average_length'];
				}
			}

			if ($complement->num_rows >=1) {
				foreach ($complement as $row) {
					$resultarray['complement'][$row['bin_floor']]=$row['average_length'];
				}
			}



			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";

			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
	   $memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

   $memcache->delete("$checkrunning");
    return $jsonstring;


}

##Reads number length - Chart showing the number of reads for a given read length
function readnumberlength($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {



			//$sql_template_lengths = "select seqpos, count(*) as count from last_align_basecalled_template_5prime where alignnum = 1 group by seqpos order by seqpos;";
			$resultarray2;

			$sql_template_lengths = "select length(sequence) as lenseq from last_align_basecalled_template_5prime inner join basecalled_template using (basename_id) group by basename_id order by length(sequence);";

			$template_lengths=$mindb_connection->query($sql_template_lengths);
			$template_numbers = $template_lengths->num_rows;
			if ($template_lengths->num_rows >= 1){
				foreach ($template_lengths as $row) {
					$resultarray2['template(aligned)'][$row['lenseq']]=$template_numbers;
					$template_numbers = $template_numbers-1;
				}
			}


			//$sql_complement_lengths = "select seqpos, count(*) as count from last_align_basecalled_complement_5prime where alignnum = 1 group by seqpos order by seqpos;";
			$sql_complement_lengths = "select length(sequence) as lenseq from last_align_basecalled_complement_5prime inner join basecalled_complement using (basename_id) group by basename_id order by length(sequence);";

			$complement_lengths=$mindb_connection->query($sql_complement_lengths);
			$complement_numbers = $complement_lengths->num_rows;

			if ($complement_lengths->num_rows >= 1){
				foreach ($complement_lengths as $row) {
					$resultarray2['complement(aligned)'][$row['lenseq']]=$complement_numbers;
					$complement_numbers = $complement_numbers-1;
				}
			}
			//$sql_2d_lengths = "select seqpos, count(*) as count from last_align_basecalled_2d_5prime where alignnum = 1 group by seqpos order by seqpos;";
			$sql_2d_lengths = "select length(sequence) as lenseq from last_align_basecalled_2d_5prime inner join basecalled_2d using (basename_id) group by basename_id order by length(sequence);";

			$read2d_lengths=$mindb_connection->query($sql_2d_lengths);
			$read2d_numbers = $read2d_lengths->num_rows;
				if ($read2d_lengths->num_rows >= 1){
				foreach ($read2d_lengths as $row) {
					$resultarray2['2d(aligned)'][$row['lenseq']]=$read2d_numbers;
					$read2d_numbers = $read2d_numbers-1;
				}
			}

			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
				foreach ($resultarray2 as $key => $value){
				$jsonstring = $jsonstring ."{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"type\" : \"line\",\n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring ."},\n";

			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		    $memcache->set("$checkvar", $jsonstring);
		}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	$memcache->delete("$checkrunning");
    return $jsonstring;

}

##Reads length Qual - Read qualities over the length of aligned reads
function readlengthqual($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jsonjobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {



			$sql_template = "select floor(seqpos/50)*50 as bin_floor, AVG(seqbasequal) as avequal from last_align_basecalled_template_5prime group by 1 order by 1;";

			$resultarray;

			$template=$mindb_connection->query($sql_template);
			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template'][$row['bin_floor']]=$row['avequal'];
				}
			}


			$sql_complement = "select floor(seqpos/50)*50 as bin_floor, AVG(seqbasequal) as avequal from last_align_basecalled_complement_5prime group by 1 order by 1;";

			$complement=$mindb_connection->query($sql_complement);
			if ($complement->num_rows >= 1){
				foreach ($complement as $row) {
					$resultarray['complement'][$row['bin_floor']]=$row['avequal'];
				}
			}
			$sql_2d = "select floor(seqpos/50)*50 as bin_floor, AVG(seqbasequal) as avequal from last_align_basecalled_2d_5prime group by 1 order by 1;";

			$read2d=$mindb_connection->query($sql_2d);

			if ($read2d->num_rows >= 1){
				foreach ($read2d as $row) {
					$resultarray['2d'][$row['bin_floor']]=$row['avequal'];
				}
			}

			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring ."{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"type\" : \"line\",\n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring ."},\n";

			}

			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

   	$memcache->delete("$checkrunning");
    return $jsonstring;

}


##Reads per pore - plots the production of reads on a pore by pore basis
function readsperpore($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select count(*) as count, channel from basecalled_template inner join config_general using (basename_id) group by channel order by channel;";


			$resultarray;

			$template=$mindb_connection->query($sql_template);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template'][$row['channel']]=$row['count'];
				}
			}





			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"borderWidth\": 1,\n";
				$jsonstring = $jsonstring . "\"data\": [";
				for ($i = 1 ; $i <=512; $i++){
					if (array_key_exists($i, $resultarray[$key])){
						$jsonstring = $jsonstring . "[" . getx($i) . "," . gety($i) . "," . $resultarray[$key][$i] . "],\n";
					}else{
						$jsonstring = $jsonstring . "[" . getx($i) . "," . gety($i) . ",0],\n";
					}
				}

				$jsonstring = $jsonstring . "],\n\"dataLabels\": {
                \"enabled\": true,
                \"color\":\"black\",
                \"style\": {
                    \"textShadow\": \"none\"
                }
            }	";
				$jsonstring = $jsonstring . "},\n";

			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		 $memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;

}

##Bases per pore - plots the production of bases on a pore by pore basis
function basesperpore($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select sum(ifnull(length(basecalled_template.sequence),0)+ifnull(length(basecalled_complement.sequence),0)) as bases, channel from basecalled_template inner join config_general using (basename_id) left join basecalled_complement using (basename_id) group by channel order by channel;";


			$resultarray;

			$template=$mindb_connection->query($sql_template);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template'][$row['channel']]=round($row['bases']/1000);
				}
			}





			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"borderWidth\": 1,\n";
				$jsonstring = $jsonstring . "\"data\": [";
				for ($i = 1 ; $i <=512; $i++){
					if (array_key_exists($i, $resultarray[$key])){
						$jsonstring = $jsonstring . "[" . getx($i) . "," . gety($i) . "," . $resultarray[$key][$i] . "],\n";
					}else{
						$jsonstring = $jsonstring . "[" . getx($i) . "," . gety($i) . ",0],\n";
					}
				}

				$jsonstring = $jsonstring . "],\n\"dataLabels\": {
                \"enabled\": true,
                \"color\":\"black\",
                \"style\": {
                    \"textShadow\": \"none\"
                }
            }	";
				$jsonstring = $jsonstring . "},\n";

			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		 $memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;

}

##These two functions are used for mapping channels to the minknow pore layout
function getx($value){
			$value = $value-1;
			$xval=31-(($value - ($value % 4))/4 % 32);
			return($xval);
		}


		function gety($value){
			$value = $value-1;
			$ad36 = $value % 4;
			$ab37 = ($value - $ad36)/4;
			$ad37 = ($ab37 % 32);
			$ab38 = (($ab37-$ad37)/32);
			$ad38 = ($ab38 % 4);
			$ag38 = ($ad36+(4*$ad38));
			$yval=(15 - $ag38);
			return($yval);
		}

##active_channels_over_time - determines how many channels are active in a given 60 second period
function active_channels_over_time($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);

$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
			$checking=$mindb_connection->query($checkrow);
			if ($checking->num_rows ==1){
				//echo "We have already run this!";
				foreach ($checking as $row){
					$jsonstring = $row['json'];
				}
			} else {
			$sql_template = "select (floor(start_time/60)*60 + exp_start_time) *1000 as bin_floor, count(*) as chan_count from basecalled_template inner join config_general using (basename_id) inner join tracking_id using (basename_id) group by 1 order by 1;";
			$sql_complement = "select (floor(start_time/60)*60  + exp_start_time ) * 1000 as bin_floor, count(*) as chan_count from basecalled_complement inner join config_general using (basename_id) inner join tracking_id using (basename_id) group by 1 order by 1;";

			$resultarray;

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template'][$row['bin_floor']]=$row['chan_count'];
				}
			}

			if ($complement->num_rows >=1) {
				foreach ($complement as $row) {
					$resultarray['complement'][$row['bin_floor']]=$row['chan_count'];
				}
			}


			//var_dump($resultarray);
			//$jsonstring = $jsonstring . json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";

			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;

}



##average_time_over_time2 - determines how long reads are taking to be returned over time
function average_time_over_time2($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select (floor(start_time/60)*60 + exp_start_time) * 1000 as bin_floor, ROUND(AVG(duration)) as average_time from basecalled_template inner join tracking_id using (basename_id) group by 1 order by 1;";
			$sql_complement = "select (floor(start_time/60)*60 + exp_start_time) * 1000 as bin_floor, ROUND(AVG(duration)) as average_time from basecalled_complement inner join tracking_id using (basename_id) group by 1 order by 1;";

			$resultarray=array();

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);

			//echo $sql_template;

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					//$resultarray=array(
					//	"template"=>array(
					//		$row['bin_floor']=>$row['average_time']
					//	)
					//);
					$resultarray['template'][$row['bin_floor']]=$row['average_time'];
					//echo $row['average_time'] . "t\n";
					//var_dump ($resultarray);
				}
			}

			if ($complement->num_rows >=1) {
				foreach ($complement as $row) {
					//$resultarray=array(
					//	"complement"=>array(
					//		$row['bin_floor']=>$row['average_time']
					//	)
			//	);
					$resultarray['complement'][$row['bin_floor']]=$row['average_time'];
					//echo $row['average_time'] . "c\n";
				}
			}

			//var_dump ($template);
			//var_dump($resultarray);
			//$jsonstring = $jsonstring . json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";

			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
			$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}


##Reads_over_time2 - determines the rate of basecalling
function reads_over_time2($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select (floor((basecalled_template.start_time)/(60*5))*(60*5) + exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join tracking_id using (basename_id) group by 1 order by 1 ;";

			$sql_complement = "select (floor((basecalled_complement.start_time)/(60*5))*(60*5) + exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_complement inner join tracking_id using (basename_id) group by 1 order by 1 ;";

			$resultarray;

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template'][$row['bin_floor']]=$row['count'];
				}
			}

			if ($complement->num_rows >=1) {
				foreach ($complement as $row) {
					$resultarray['complement'][$row['bin_floor']]=$row['count'];
				}
			}
			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "[$key2,$value2],\n";
				}
				$jsonstring = $jsonstring . "]\n";
				$jsonstring = $jsonstring . "},\n";
			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}


##Histogram of Read Depth
function histogrambases($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template_query = "SELECT (FLOOR(length(basecalled_template.sequence)/1000)*1000) as bucket, sum(length(basecalled_template.sequence)) as tempBASES from basecalled_template group by bucket;";
			$sql_complement_query = "SELECT (FLOOR(length(basecalled_complement.sequence)/1000)*1000) as bucket, sum(length(basecalled_complement.sequence)) as compBASES from basecalled_complement group by bucket;";
			$sql_2d_query = "SELECT (FLOOR(length(basecalled_2d.sequence)/1000)*1000) as bucket, sum(length(basecalled_2d.sequence)) as d2BASES from basecalled_2d group by bucket;";
			$sql_template_execute=$mindb_connection->query($sql_template_query);
			$sql_complement_execute=$mindb_connection->query($sql_complement_query);
			$sql_2d_execute=$mindb_connection->query($sql_2d_query);
			$category2 = array();
			$arraytemplate = array();
			$arraycomplement = array();
			$array2d = array();
			if ($sql_template_execute->num_rows >= 1) {
				foreach ($sql_template_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];
					}
					$arraytemplate[$row['bucket']]=$row['tempBASES'];
				}
			}
			if ($sql_complement_execute->num_rows >= 1) {
				foreach ($sql_complement_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];
					}
					$arraycomplement[$row['bucket']]=$row['compBASES'];
				}
			}
			if ($sql_2d_execute->num_rows >= 1) {
				foreach ($sql_2d_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];
					}
					$array2d[$row['bucket']]=$row['d2BASES'];
				}
			}
			asort($category2);
			//var_dump($category2);


			$category = array();
			$category['name'] = 'Size';

			$series1 = array();
			$series1['name'] = 'Template';

			$series2 = array();
			$series2['name'] = 'Complement';

			$series3 = array();
			$series3['name'] = '2d';

			foreach ($category2 as $bucket) {
				$category['data'][]=$bucket;
				if (array_key_exists($bucket, $arraytemplate)) {
					$series1['data'][]=$arraytemplate[$bucket];
				}else{
					$series1['data'][]=0;
				}
				if (array_key_exists($bucket, $arraycomplement)) {
					$series2['data'][]=$arraycomplement[$bucket];
				}else{
					$series2['data'][]=0;
				}
				if (array_key_exists($bucket, $array2d)) {
					$series3['data'][]=$array2d[$bucket];
				}else{
					$series3['data'][]=0;
				}

			}

			//if ($sql_execute->num_rows >=1) {
			//	foreach ($sql_execute as $row){
			//		$category['data'][]= $row['bucket'];
			//	    $series1['data'][] = $row['tempCOUNT'];
			//	    $series2['data'][] = $row['compCOUNT'];
			//	    $series3['data'][] = $row['seq2dCOUNT'];
			//	}
			//}
			$result = array();
			array_push($result,$category);
			array_push($result,$series1);
			array_push($result,$series2);
			array_push($result,$series3);

			$jsonstring = json_encode($result, JSON_NUMERIC_CHECK);
			//$jsonstring = json_encode($result);

			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
			$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}



##Histogram of Read Lengths
function histogram($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template_query = "SELECT (FLOOR(length(basecalled_template.sequence)/1000)*1000) as bucket, COUNT(basecalled_template.sequence) as tempCOUNT from basecalled_template group by bucket;";
			$sql_complement_query = "SELECT (FLOOR(length(basecalled_complement.sequence)/1000)*1000) as bucket, COUNT(basecalled_complement.sequence) as compCOUNT from basecalled_complement group by bucket;";
			$sql_2d_query = "SELECT (FLOOR(length(basecalled_2d.sequence)/1000)*1000) as bucket, COUNT(basecalled_2d.sequence) as d2COUNT from basecalled_2d group by bucket;";

			$sql_template_execute=$mindb_connection->query($sql_template_query);
			$sql_complement_execute=$mindb_connection->query($sql_complement_query);
			$sql_2d_execute=$mindb_connection->query($sql_2d_query);

			$category2 = array();
			$arraytemplate = array();
			$arraycomplement = array();
			$array2d = array();

			if ($sql_template_execute->num_rows >= 1) {
				foreach ($sql_template_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];
					}
					$arraytemplate[$row['bucket']]=$row['tempCOUNT'];
				}
			}
			if ($sql_complement_execute->num_rows >= 1) {
				foreach ($sql_complement_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];
					}
					$arraycomplement[$row['bucket']]=$row['compCOUNT'];
				}
			}
			if ($sql_2d_execute->num_rows >= 1) {
				foreach ($sql_2d_execute as $row) {
					if (!in_array($row['bucket'], $category2)) {
						$category2[]=$row['bucket'];
					}
					$array2d[$row['bucket']]=$row['d2COUNT'];
				}
			}
			asort($category2);
			//var_dump($arraytemplate);


			$category = array();
			$category['name'] = 'Size';

			$series1 = array();
			$series1['name'] = 'Template';

			$series2 = array();
			$series2['name'] = 'Complement';

			$series3 = array();
			$series3['name'] = '2d';

			foreach ($category2 as $bucket) {
				$category['data'][]=$bucket;
				if (array_key_exists($bucket, $arraytemplate)) {
					$series1['data'][]=$arraytemplate[$bucket];
				}else{
					$series1['data'][]=0;
				}
				if (array_key_exists($bucket, $arraycomplement)) {
					$series2['data'][]=$arraycomplement[$bucket];
				}else{
					$series2['data'][]=0;
				}
				if (array_key_exists($bucket, $array2d)) {
					$series3['data'][]=$array2d[$bucket];
				}else{
					$series3['data'][]=0;
				}
			}

			//if ($sql_execute->num_rows >=1) {
			//	foreach ($sql_execute as $row){
			//		$category['data'][]= $row['bucket'];
			//	    $series1['data'][] = $row['tempCOUNT'];
			//	    $series2['data'][] = $row['compCOUNT'];
			//	    $series3['data'][] = $row['seq2dCOUNT'];
			//	}
			//}

			$result = array();
			array_push($result,$category);
			array_push($result,$series1);
			array_push($result,$series2);
			array_push($result,$series3);

			$jsonstring = json_encode($result, JSON_NUMERIC_CHECK);
			//$jsonstring = json_encode($result);
			//$jsonstring = '[{"name":"Month","data":["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]},{"name":"Wordpress","data":[4,5,6,2,5,7,2,1,6,7,3,4]},{"name":"CodeIgniter","data":[5,2,3,6,7,1,2,6,6,4,6,3]},{"name":"Highcharts","data":[7,8,9,6,7,10,9,7,6,9,8,4]}] ';
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		   $memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;

}





##Average Depth of Coverage - primitive depth calculator
function depthcoverage($jobname,$currun,$refid) {
	$jobname = $jobname . $refid;
	$checkvar = $currun . $jobname . $refid;
	$checkrunning = $currun . $jobname .$refid . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
			$checking=$mindb_connection->query($checkrow);
			if ($checking->num_rows ==1){
				//echo "We have already run this!";
				foreach ($checking as $row){
					$jsonstring = $row['json'];
				}
			} else {
			//$sql_template = "select refpos, count(*) as count from last_align_basecalled_template where refpos != \'null\' and (cigarclass = 7 or cigarclass = 8) group by refpos;";
			$table_check = "SHOW TABLES LIKE 'last_align_basecalled_template'";
			$table_exists = $mindb_connection->query($table_check);


			$jsonstring;

			if ($table_exists->num_rows >= 1){
				$sql_template = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_template where (cigarclass=7 or cigarclass=8) and refid = " . $refid . " group by refid,refpos) as x";
				$sql_complement = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_complement where (cigarclass=7 or cigarclass=8) and refid = " . $refid . " group by refid,refpos) as x";
				$sql_2d = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_2d where (cigarclass=7 or cigarclass=8) and refid = " . $refid . " group by refid,refpos) as x";


				$covarray;

				$template=$mindb_connection->query($sql_template);
				$complement=$mindb_connection->query($sql_complement);
				$read2d=$mindb_connection->query($sql_2d);
				if ($template->num_rows >= 1){
					foreach ($template as $row) {
						$perccov=($row['bases']/$reflength)*100;
						$covarray['template']['percov']=$perccov;
						$covarray['template']['avecover']=$row['coverage'];

					}
				}
				if ($complement->num_rows >= 1){
					foreach ($complement as $row) {
						$perccov=($row['bases']/$reflength)*100;
						$covarray['complement']['percov']=$perccov;
						$covarray['complement']['avecover']=$row['coverage'];
					}
				}
				if ($read2d->num_rows >= 1){
					foreach ($read2d as $row) {
						$perccov=($row['bases']/$reflength)*100;
						$covarray['2d']['percov']=$perccov;
						$covarray['2d']['avecover']=$row['coverage'];

					}
				}

				$jsonstring = $jsonstring . "[\n";
				foreach ($covarray as $type => $typeval){
					$jsonstring = $jsonstring .  "{\n";
					$jsonstring = $jsonstring .  "\"name\" : \"" . $type . "\", \n";
					$jsonstring = $jsonstring .  "\"data\": [";
					//var numvals = count($typeval);
					//echo "This is " . numvals . "\n";

					foreach ($typeval as $key => $value) {
						if ($key == "avecover") {
							$jsonstring = $jsonstring .  "$value";
						}
					}

					$jsonstring = $jsonstring .  "]\n";
					$jsonstring = $jsonstring .  "},\n";

				}
				$jsonstring = $jsonstring .  "]\n";
			}else {
				$sql_template = "SELECT avg(A+T+G+C) as depth,ref_id,ref_pos FROM reference_coverage_template where ref_id = " . $refid . " group by ref_id;";
				$sql_complement = "SELECT avg(A+T+G+C) as depth,ref_id,ref_pos FROM reference_coverage_complement where ref_id = " . $refid . " group by ref_id;";
				$sql_2d = "SELECT avg(A+T+G+C) as depth,ref_id,ref_pos FROM reference_coverage_2d where ref_id = " . $refid . " group by ref_id;";

				$template=$mindb_connection->query($sql_template);
				$complement=$mindb_connection->query($sql_complement);
				$read2d=$mindb_connection->query($sql_2d);

				$covarray;

				if ($template->num_rows >= 1){
					foreach ($template as $row) {
						$perccov=$row['depth'];
						$covarray['template'][$row['ref_id']]['percov']=$perccov;
					}
				}
				if ($complement->num_rows >= 1){
					foreach ($complement as $row) {
						$perccov=$row['depth'];
						$covarray['complement'][$row['ref_id']]['percov']=$perccov;
					}
				}
				if ($read2d->num_rows >= 1){
					foreach ($read2d as $row) {
						$perccov=$row['depth'];
						$covarray['2d'][$row['ref_id']]['percov']=$perccov;
					}
				}
				$jsonstring = $jsonstring . "[\n";
				foreach ($covarray as $type => $typeval){
					foreach ($typeval as $refid => $value){
						$jsonstring = $jsonstring .  "{\n";
						$jsonstring = $jsonstring .  "\"name\" : \"" . $type .  "\", \n";
						$jsonstring = $jsonstring .  "\"data\": [";
						//var numvals = count($typeval);
						//echo "This is " . numvals . "\n";

						foreach ($value as $key => $value2) {
							if ($key == "percov") {
								$jsonstring = $jsonstring .  "$value2";
							}
						}

						$jsonstring = $jsonstring .  "]\n";
						$jsonstring = $jsonstring .  "},\n";
					}

				}
				$jsonstring = $jsonstring .  "]\n";

				if ($_GET["prev"] == 1){
					include 'savejson.php';
				}
			}



		}
			$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}



##Percentage of Reference with Read - primitive coverage calculator based on total coverage of all sequences being aligned too.
function percentcoverage($jobname,$currun,$refid) {
	$jobname = $jobname . $refid;
	$checkvar = $currun . $jobname . $refid;
	$checkrunning = $currun . $jobname . $refid . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//echo $refid . "\n";
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
			$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
			$checking=$mindb_connection->query($checkrow);
			if ($checking->num_rows ==1){
				//echo "We have already run this!";
				foreach ($checking as $row){
					$jsonstring = $row['json'];
				}
			} else {
				$table_check = "SHOW TABLES LIKE 'last_align_basecalled_template'";
				$table_exists = $mindb_connection->query($table_check);
				$jsonstring;
				if ($table_exists->num_rows >= 1){
					$sql_template = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_template where (cigarclass=7 or cigarclass=8) and refid = " . $refid . " group by refid,refpos) as x";
					$sql_complement = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_complement where (cigarclass=7 or cigarclass=8) and refid = " . $refid . " group by refid,refpos) as x";
					$sql_2d = "select count(*) as bases, AVG(count) as coverage from (SELECT count(*) as count,refid,refpos FROM last_align_basecalled_2d where (cigarclass=7 or cigarclass=8) and refid = " . $refid . " group by refid,refpos) as x";
					$covarray;
					$template=$mindb_connection->query($sql_template);
					$complement=$mindb_connection->query($sql_complement);
					$read2d=$mindb_connection->query($sql_2d);
					if ($template->num_rows >= 1){
						foreach ($template as $row) {
							$perccov=($row['bases']/$reflength)*100;
							$covarray['template']['percov']=$perccov;
							$covarray['template']['avecover']=$row['coverage'];
						}
					}
					if ($complement->num_rows >= 1){
						foreach ($complement as $row) {
							$perccov=($row['bases']/$reflength)*100;
							$covarray['complement']['percov']=$perccov;
							$covarray['complement']['avecover']=$row['coverage'];
						}
					}
					if ($read2d->num_rows >= 1){
						foreach ($read2d as $row) {
							$perccov=($row['bases']/$reflength)*100;
							$covarray['2d']['percov']=$perccov;
							$covarray['2d']['avecover']=$row['coverage'];
						}
					}
					$jsonstring = $jsonstring . "[\n";
					foreach ($covarray as $type => $typeval){
						$jsonstring = $jsonstring .  "{\n";
						$jsonstring = $jsonstring . "\"name\" : \"" . $type . "\", \n";
						$jsonstring = $jsonstring . "\"data\": [";
						//var numvals = count($typeval);
						//echo "This is " . numvals . "\n";

						foreach ($typeval as $key => $value) {
							if ($key == "percov") {
								$jsonstring = $jsonstring .  "$value";
							}
						}

						$jsonstring = $jsonstring .  "]\n";
						$jsonstring = $jsonstring .  "},\n";

					}
					$jsonstring = $jsonstring .  "]\n";
				}else{
					//echo "Yep - we're in here!";
					$sql_template = "SELECT (sum(A+T+G+C)/reflen) as coverage,ref_id,refname FROM reference_coverage_template inner join reference_seq_info where refid=ref_id and refid = " . $refid . " group by ref_id;";
					$sql_complement = "SELECT (sum(A+T+G+C)/reflen) as coverage,ref_id,refname FROM reference_coverage_complement inner join reference_seq_info where refid=ref_id and refid = " . $refid . "  group by ref_id;";
					$sql_2d = "SELECT (sum(A+T+G+C)/reflen) as coverage,ref_id,refname FROM reference_coverage_2d inner join reference_seq_info where refid=ref_id and refid = " . $refid . " group by ref_id;";
					$template=$mindb_connection->query($sql_template);
					$complement=$mindb_connection->query($sql_complement);
					$read2d=$mindb_connection->query($sql_2d);
					//echo "\n" . $sql_template . "\n";
					$covarray;

					if ($template->num_rows >= 1){
						foreach ($template as $row) {
							$perccov=$row['coverage']*100;
							$covarray['template'][$row['refname']]['percov']=$perccov;
						}
					}
					if ($complement->num_rows >= 1){
						foreach ($complement as $row) {
							$perccov=$row['coverage']*100;
							$covarray['complement'][$row['refname']]['percov']=$perccov;
						}
					}
					if ($read2d->num_rows >= 1){
						foreach ($read2d as $row) {
							$perccov=$row['coverage']*100;
							$covarray['2d'][$row['refname']]['percov']=$perccov;
						}
					}
					$jsonstring = $jsonstring . "[\n";
					foreach ($covarray as $type => $typeval){
						foreach ($typeval as $refid => $value){
							$jsonstring = $jsonstring .  "{\n";
							$jsonstring = $jsonstring .  "\"name\" : \"" . $type . "\", \n";
							$jsonstring = $jsonstring .  "\"data\": [";
							//var numvals = count($typeval);
							//echo "This is " . numvals . "\n";

							foreach ($value as $key => $value2) {
								if ($key == "percov") {
									$jsonstring = $jsonstring .  "$value2";
								}
							}

							$jsonstring = $jsonstring .  "]\n";
							$jsonstring = $jsonstring .  "},\n";
						}

					}
					$jsonstring = $jsonstring .  "]\n";
					if ($_GET["prev"] == 1){
						include 'savejson.php';
					}

				}

			}
				$memcache->set("$checkvar", $jsonstring);
		}else{
			$jsonstring = $memcache->get("$checkvar");
		}
	// cache for 2 minute as we want yield to update semi-regularly...

	   $memcache->delete("$checkrunning");
    return $jsonstring;

}

##Read Number Upload - generates the numbers of reads uploaded, basecalled, aligned and processed for template, complement and 2d

function readnumberupload($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if ($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {

			#Check the number of cached reads - should be a memcached variable if all is going well.
		$checkcached = $currun."cached";
		//echo $checkcached . "\n";
		$memcache->get('$checkcached');
		//echo $memcache->get("$checkcached");
		if ( $memcache->get("$checkcached") > 0) {
			$resultarray['Cached']['template']=$memcache->get("$checkcached");
			$resultarray['Cached']['complement']=$memcache->get("$checkcached");
			$resultarray['Cached']['2d']=$memcache->get("$checkcached");
		}


		#Check the number of raw reads uploaded.

		$raw_count = "select count(*) as raw_count from pre_tracking_id;";
		$raw_count_query = $mindb_connection->query($raw_count);
		if ($raw_count_query->num_rows >= 1) {
			foreach ($raw_count_query as $row) {
				$resultarray['Raw']['PRETemp']=$row['raw_count'];
			}
		}
		$raw_count = "select count(*) as raw_count from pre_tracking_id where hairpin_found = 1;";
		$raw_count_query = $mindb_connection->query($raw_count);
		if ($raw_count_query->num_rows >= 1) {
			foreach ($raw_count_query as $row) {
				$resultarray['Raw']['PREcomp']=$row['raw_count'];
			}
		}


		#Check the number of raw reads aligned.
		$raw_align_count = "SELECT count(*) as raw_align_count FROM pre_align_template;";
		$raw_align_count_query = $mindb_connection->query($raw_align_count);
		if ($raw_align_count_query->num_rows >= 1) {
			foreach ($raw_align_count_query as $row) {
				$resultarray['Raw Align']['PRETemp']=$row['raw_align_count'];
			}
		}
		$raw_align_count = "SELECT count(*) as raw_align_count FROM pre_align_complement;";
		$raw_align_count_query = $mindb_connection->query($raw_align_count);
		if ($raw_align_count_query->num_rows >= 1) {
			foreach ($raw_align_count_query as $row) {
				$resultarray['Raw Align']['PREComp']=$row['raw_align_count'];
			}
		}
		$raw_align_count = "SELECT count(*) as raw_align_count FROM pre_align_2d;";
		$raw_align_count_query = $mindb_connection->query($raw_align_count);
		if ($raw_align_count_query->num_rows >= 1) {
			foreach ($raw_align_count_query as $row) {
				$resultarray['Raw Align']['PRE2d']=$row['raw_align_count'];
			}
		}

			#The number of reads uploaded will always be the current count from tracking_id regardless of wether it is template complement or 2s
		$current_count = "select count(*) as curr_count from tracking_id;";
		$resultarray;
		$current_count_query = $mindb_connection->query($current_count);
		if ($current_count_query->num_rows >=1) {
			foreach ($current_count_query as $row) {
				$resultarray['Uploaded']['template']=$row['curr_count'];
				$resultarray['Uploaded']['complement']=$row['curr_count'];
				$resultarray['Uploaded']['2d']=$row['curr_count'];
			}
		}

		#The number of reads yielding suequence will be:

		$sql_template = "select count(*) as readnum, exp_script_purpose from basecalled_template inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_complement = "select count(*) as readnum, exp_script_purpose from basecalled_complement inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_2d = "select count(*) as readnum, exp_script_purpose from basecalled_2d inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		//echo $sql_template;


		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);

		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['Called']['template']=$row['readnum'];
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$resultarray['Called']['complement']=$row['readnum'];
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$resultarray['Called']['2d']=$row['readnum'];
			}
		}

		#The number of reads which have aligned to the reference:
		$sql_template = "select count(*) as aligned from (select count(*) from last_align_basecalled_template_5prime group by basename_id) as t;";

		$sql_complement = "select count(*) as aligned from (select count(*) from last_align_basecalled_complement_5prime group by basename_id) as t;";
		$sql_2d = "select count(*) as aligned from (select count(*) from last_align_basecalled_2d_5prime group by basename_id) as t;";

		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);

		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['Aligned']['template']=$row['aligned'];
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$resultarray['Aligned']['complement']=$row['aligned'];
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$resultarray['Aligned']['2d']=$row['aligned'];
			}
		}

		#The number of reads which have been processed:
		$sql_template = "select count(*) as processed from read_tracking_template;";
		$sql_complement = "select count(*) as processed from read_tracking_complement;";
		$sql_2d = "select count(*) as processed from read_tracking_2d;";

		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);

		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray['Processed']['template']=$row['processed'];
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$resultarray['Processed']['complement']=$row['processed'];
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$resultarray['Processed']['2d']=$row['processed'];
			}
		}
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring;
		$jsonstring = $jsonstring .   "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring .   "{\n";
					$jsonstring = $jsonstring .   "\"name\" : \"$key\", \n";
					$jsonstring = $jsonstring .  "\"data\": [";

				foreach ($value as $key2 => $value2) {
										$jsonstring = $jsonstring .   "$value2,";

				}
				$jsonstring = $jsonstring .  "]\n},\n";

				//echo "},\n";

			}

			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
			$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 10 seconds as we want yield to update regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}


##Read Number - generates the numbers of reads for each read type:

function readnumber($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if ($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {

		$sql_template = "select count(*) as readnum, exp_script_purpose from basecalled_template inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_complement = "select count(*) as readnum, exp_script_purpose from basecalled_complement inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		$sql_2d = "select count(*) as readnum, exp_script_purpose from basecalled_2d inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
		//echo $sql_template;
		$resultarray;

		$template=$mindb_connection->query($sql_template);
		$complement=$mindb_connection->query($sql_complement);
		$read2d=$mindb_connection->query($sql_2d);

		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$resultarray[$row['exp_script_purpose']]['template']=$row['readnum'];
			}
		}
		if ($complement->num_rows >= 1){
			foreach ($complement as $row) {
				$resultarray[$row['exp_script_purpose']]['complement']=$row['readnum'];
			}
		}
		if ($read2d->num_rows >= 1){
			foreach ($read2d as $row) {
				$resultarray[$row['exp_script_purpose']]['2d']=$row['readnum'];
			}
		}
		//var_dump($resultarray);
		//echo json_encode($resultarray);
		$jsonstring;
		$jsonstring = $jsonstring .   "[\n";
			foreach ($resultarray as $key => $value){
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring .   "{\n";
					$jsonstring = $jsonstring .   "\"name\" : \"$key2\", \n";
					$jsonstring = $jsonstring .  "\"data\": [";
					$jsonstring = $jsonstring .   "$value2,";
					$jsonstring = $jsonstring .  "]\n},\n";
				}

				//echo "},\n";

			}

			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
			$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
	// cache for 10 seconds as we want yield to update regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}

##MaxLen - generates max length of read data for plots:

function maxlen($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select MAX(length(sequence)) as maxlen, exp_script_purpose from basecalled_template inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
			$sql_complement = "select MAX(length(sequence)) as maxlen, exp_script_purpose from basecalled_complement inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
			$sql_2d = "select MAX(length(sequence)) as maxlen, exp_script_purpose from basecalled_2d inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";

			$resultarray;

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);
			$read2d=$mindb_connection->query($sql_2d);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray[$row['exp_script_purpose']]['template']=$row['maxlen'];
				}
			}
			if ($complement->num_rows >= 1){
				foreach ($complement as $row) {
					$resultarray[$row['exp_script_purpose']]['complement']=$row['maxlen'];
				}
			}
			if ($read2d->num_rows >= 1){
				foreach ($read2d as $row) {
					$resultarray[$row['exp_script_purpose']]['2d']=$row['maxlen'];
				}
			}


			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring . "{\n";
					$jsonstring = $jsonstring . "\"name\" : \"$key2\", \n";
					$jsonstring = $jsonstring . "\"data\": [";
					$jsonstring = $jsonstring . "$value2,";
					$jsonstring = $jsonstring . "]\n},\n";
				}

				//$jsonstring = $jsonstring . "},\n";

			}
			$jsonstring = $jsonstring .  "]\n";
			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		 $memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}

// cache for 10 seconds as we want yield to update regularly...

   	         $memcache->delete("$checkrunning");
    return $jsonstring;
}


##AveLen - generates average length data for plot:

function avelen($jobname,$currun) {
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select ROUND(AVG(length(sequence))) as average_length, exp_script_purpose from basecalled_template inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
			$sql_complement = "select ROUND(AVG(length(sequence))) as average_length, exp_script_purpose from basecalled_complement inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";
			$sql_2d = "select ROUND(AVG(length(sequence))) as average_length, exp_script_purpose from basecalled_2d inner join tracking_id using (basename_id) where exp_script_purpose != \"dry_chip\" group by exp_script_purpose;";

			$resultarray;

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);
			$read2d=$mindb_connection->query($sql_2d);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray[$row['exp_script_purpose']]['template']=$row['average_length'];
				}
			}
			if ($complement->num_rows >= 1){
				foreach ($complement as $row) {
					$resultarray[$row['exp_script_purpose']]['complement']=$row['average_length'];
				}
			}
			if ($read2d->num_rows >= 1){
				foreach ($read2d as $row) {
					$resultarray[$row['exp_script_purpose']]['2d']=$row['average_length'];
				}
			}


			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				foreach ($value as $key2 => $value2) {
					$jsonstring = $jsonstring ."{\n";
					$jsonstring = $jsonstring . "\"name\" : \"$key2\", \n";
					$jsonstring = $jsonstring . "\"data\": [";
					$jsonstring = $jsonstring . "$value2,";
					$jsonstring = $jsonstring . "]\n},\n";
				}

			//echo "},\n";

			}
			$jsonstring = $jsonstring . "]\n";

			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		    $memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}

	// cache for 10 seconds as we want yield to update regularly...


	         $memcache->delete("$checkrunning");
    return $jsonstring;


}

##Volume - generates the Yield summary plots:

function bases($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	//$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	//if($jsonstring === false && ($checkingrunning === "No" || $checkingrunning === FALSE)){
	if ($checkingrunning === "No" || $checkingrunning === FALSE) {
		$memcache->set("$checkrunning", "YES", 0, 0);
    	$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);


		if ($checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {

			$sql_template = "SELECT sum(length(sequence)) as bases FROM basecalled_template;";
			$sql_complement = "SELECT sum(length(sequence)) as bases FROM basecalled_complement;";
			$sql_2d = "SELECT sum(length(sequence)) as bases FROM basecalled_2d;";

			$resultarray;

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);
			$read2d=$mindb_connection->query($sql_2d);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
					$resultarray['template']=$row['bases'];
				}
			}
			if ($complement->num_rows >= 1){
				foreach ($complement as $row) {
					$resultarray['complement']=$row['bases'];
				}
			}
			if ($read2d->num_rows >= 1){
				foreach ($read2d as $row) {
					$resultarray['2d']=$row['bases'];
				}
			}
			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring;
			$jsonstring = $jsonstring .   "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring .   "{\n";
				$jsonstring = $jsonstring .   "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring .  "\"data\": [";
				$jsonstring = $jsonstring .   "$value,";
				$jsonstring = $jsonstring .  "]\n},\n";
			}


			$jsonstring = $jsonstring .  "]\n";

			if ($_GET["prev"] == 1){
				include 'savejson.php';
			}
		}
		$memcache->set("$checkvar", $jsonstring);
	}else{
		$jsonstring = $memcache->get("$checkvar");
	}



	// cache for 10 seconds as we want yield to update regularly...


	         $memcache->delete("$checkrunning");
    return $jsonstring;



}

?>
