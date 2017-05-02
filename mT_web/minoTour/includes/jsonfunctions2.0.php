<?php

### This file contains a number of functions which are used to generate json for the website and are also utilised by the perl wrapper script on the backend. It is currently experimental.

##### Template for new jobs...

function newblanktemplate($jobname,$currun) {
    global $memcache;
    global $mindb_connection;
    $jsonstring = "";  //A simple variable to hold the value of the jsonstring to return
    //Flag to check to see if this job needs re-running or has expired
    $flagstate = $currun.$jobname."flag";
    //Flag to check if job is already running
    $runstate = $currun.$jobname."runstate";
    //Holder to store the actual data
    $storedvalue = $currun.$jobname."store";
    //Get flagstate and runstate from the memcache store.
    $checkflagstate = $memcache->get("$flagstate");
    $checkrunstate = $memcache->get("$runstate");
    //First check to see if it is safe to retrieve data from the store - it will be if checkflagstate is true or checkrunstate is true
    if ($checkflagstate == "True" || $checkrunstate == "True"){
        //echo "Retrieving current value";
        $jsonstring = $memcache->get("$storedvalue");
    } else {
        //echo "Value expired - recalculating";
        //This will run only if both flags are false and so will start to process the data. Therefore it must set the running flag to True
        $memcache->set("$runstate", "True",0,0);
        //Now we execute the code to retrieve whatever values we are after and store them in the $jsonstring value:
        $jsonstring = hello_world();
        if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
        //Now we store the value we are after in the memcache holder for the value with no time out (so it persists):
        $memcache->set("$storedvalue",$jsonstring,MEMCACHE_COMPRESSED,0);
        //Now we store the flag to report the data is updated
        $memcache->set("$flagstate","True",0,5); //Note this will expire after 5 seconds.
        //Finally we set the running flag to false
        $memcache->set("$runstate", "False",0,0);

    }
    return $jsonstring;
}

function assemblyscatjson($jobname,$currun) {
    //echo "TUMP";
    global $memcache;
    global $mindb_connection;
    $jsonstring = "";  //A simple variable to hold the value of the jsonstring to return
    //Flag to check to see if this job needs re-running or has expired
    $flagstate = $currun.$jobname."flag";
    //Flag to check if job is already running
    $runstate = $currun.$jobname."runstate";
    //Holder to store the actual data
    $storedvalue = $currun.$jobname."store";
    //Get flagstate and runstate from the memcache store.
    $checkflagstate = $memcache->get("$flagstate");
    $checkrunstate = $memcache->get("$runstate");
    //First check to see if it is safe to retrieve data from the store - it will be if checkflagstate is true or checkrunstate is true
    if ($checkflagstate == "True" || $checkrunstate == "True"){
        //echo "Retrieving current value";
        $jsonstring = $memcache->get("$storedvalue");
    } else {
        //echo "Value expired - recalculating";
        //This will run only if both flags are false and so will start to process the data. Therefore it must set the running flag to True
        $memcache->set("$runstate", "True",0,0);
        //Now we execute the code to retrieve whatever values we are after and store them in the $jsonstring value:

        $jsonstring = "";
        $sql = "select timeset,length from assembly_metrics inner join assembly_seq using (timeid);";
        $assemblies = $mindb_connection->query($sql);
        $emparray = array();
        while($row =mysqli_fetch_assoc($assemblies))
            {
                $emparray[] = $row;
            }

        $jsonstring = json_encode($emparray);

        if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
        //Now we store the value we are after in the memcache holder for the value with no time out (so it persists):
        $memcache->set("$storedvalue",$jsonstring,MEMCACHE_COMPRESSED,0);
        //Now we store the flag to report the data is updated
        $memcache->set("$flagstate","True",0,5); //Note this will expire after 5 seconds.
        //Finally we set the running flag to false
        $memcache->set("$runstate", "False",0,0);

    }
    return $jsonstring;
}


function assemblyjson($jobname,$currun) {
    //echo "TUMP";
    global $memcache;
    global $mindb_connection;
    $jsonstring = "";  //A simple variable to hold the value of the jsonstring to return
    //Flag to check to see if this job needs re-running or has expired
    $flagstate = $currun.$jobname."flag";
    //Flag to check if job is already running
    $runstate = $currun.$jobname."runstate";
    //Holder to store the actual data
    $storedvalue = $currun.$jobname."store";
    //Get flagstate and runstate from the memcache store.
    $checkflagstate = $memcache->get("$flagstate");
    $checkrunstate = $memcache->get("$runstate");
    //First check to see if it is safe to retrieve data from the store - it will be if checkflagstate is true or checkrunstate is true
    if ($checkflagstate == "True" || $checkrunstate == "True"){
        //echo "Retrieving current value";
        $jsonstring = $memcache->get("$storedvalue");
    } else {
        //echo "Value expired - recalculating";
        //This will run only if both flags are false and so will start to process the data. Therefore it must set the running flag to True
        $memcache->set("$runstate", "True",0,0);
        //Now we execute the code to retrieve whatever values we are after and store them in the $jsonstring value:

        $jsonstring = "";
        $sql = "select * from assembly_metrics;";
        $assemblies = $mindb_connection->query($sql);
        $emparray = array();
        while($row =mysqli_fetch_assoc($assemblies))
            {
                $emparray[] = $row;
            }

        $jsonstring = json_encode($emparray);

        if ($_GET["prev"] == 1){
			include 'savejson.php';
		}
        //Now we store the value we are after in the memcache holder for the value with no time out (so it persists):
        $memcache->set("$storedvalue",$jsonstring,MEMCACHE_COMPRESSED,0);
        //Now we store the flag to report the data is updated
        $memcache->set("$flagstate","True",0,5); //Note this will expire after 5 seconds.
        //Finally we set the running flag to false
        $memcache->set("$runstate", "False",0,0);

    }
    return $jsonstring;
}
##### Template for a function to run the desired code

function hello_world(){
    return "hello world ". date("h:i:sa");
}

##Average Length Over Time - chart showing read lengths over time
function average_length_over_time($jobname,$currun){
	$checkvar = $currun . $jobname;
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
        if (strlen($jsonstring) <= 1){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
			$checking=$mindb_connection->query($checkrow);
			if (is_object($checking) && $checking->num_rows ==1){
				//echo "We have already run this!";
				foreach ($checking as $row){
					$jsonstring = $row['json'];
				}
			} else {
			//$sql_template = "select 1minwin,exp_start_time, ROUND(AVG(seqlen)) as average_length from basecalled_template group by 2,1 order by 2,1;";
			//$sql_complement = "select 1minwin,exp_start_time, ROUND(AVG(seqlen)) as average_length from basecalled_complement group by 2,1 order by 2,1;";
            $sql_template = "select 1minwin, average_length from basecalled_template_1minwin_sum where average_length != 'null' order by 1;";
            $sql_complement = "select 1minwin, average_length from basecalled_complement_1minwin_sum where average_length != 'null' order by 1;";
			$resultarray=array();

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*1000;
					$resultarray['template'][$binfloor]=$row['average_length'];
				}
			}

			if ($complement->num_rows >=1) {
				foreach ($complement as $row) {
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*1000;
					$resultarray['complement'][$binfloor]=$row['average_length'];
				}
			}



			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring="";
			$jsonstring = $jsonstring . "[\n";
			foreach ($resultarray as $key => $value){
				$jsonstring = $jsonstring . "{\n";
				$jsonstring = $jsonstring . "\"name\" : \"$key\", \n";
				$jsonstring = $jsonstring . "\"data\": [";
                ksort($value);
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
	   $memcache->set("$checkvar", "$jsonstring",MEMCACHE_COMPRESSED,5);

	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
}
	// cache for 2 minute as we want yield to update semi-regularly...

   $memcache->delete("$checkrunning");
    return $jsonstring;


}

##Read Number - generates the numbers of reads for each read type:

function readnumber($jobname,$currun){
    $summarystats = retrievefromsession($currun,"summarystats","");
    if( !empty( $summarystats ) ){
        if (array_key_exists("totalcount",$summarystats)){
            $datarray=array();
            $readtypearray=array('Raw Template','Raw Complement', 'template','complement','2d');
            foreach($readtypearray as $type){
                if (isset ($summarystats["totalcount"][$type])){
                    $datarray[$type][]=$summarystats["totalcount"][$type];
                }
            }
            $jsonstring = "";
            $jsonstring = $jsonstring . "[\n";
            foreach($readtypearray as $type){
                if (isset ($datarray[$type])){
                    $jsonstring = $jsonstring . "{\n";
                    $jsonstring = $jsonstring . '"name":"'.$type. '"' . ",\n";
                    $jsonstring = $jsonstring . '"data":[' . implode(",",$datarray[$type]) . "]\n";
                    $jsonstring = $jsonstring . "},\n";
                }
            }
            $jsonstring = $jsonstring . "]\n";
        }
    }
    return $jsonstring;
}

##Maximum Length:

function maxlen($jobname,$currun){
    $summarystats = retrievefromsession($currun,"summarystats","");
    if( !empty( $summarystats ) ){
        if (array_key_exists("totalcount",$summarystats)){
            $datarray=array();
            $readtypearray=array('Raw Template','Raw Complement', 'template','complement','2d');
            foreach($readtypearray as $type){
                if (isset ($summarystats["maxlen"][$type])){
                    $datarray[$type][]=$summarystats["maxlen"][$type];
                }
            }
            $jsonstring = "";
            $jsonstring = $jsonstring . "[\n";
            foreach($readtypearray as $type){
                if (isset ($datarray[$type])){
                    $jsonstring = $jsonstring . "{\n";
                    $jsonstring = $jsonstring . '"name":"'.$type. '"' . ",\n";
                    $jsonstring = $jsonstring . '"data":[' . implode(",",$datarray[$type]) . "]\n";
                    $jsonstring = $jsonstring . "},\n";
                }
            }
            $jsonstring = $jsonstring . "]\n";
        }
    }
    return $jsonstring;
}

##Average Length:

function avelen($jobname,$currun){
    $summarystats = retrievefromsession($currun,"summarystats","");
    if( !empty( $summarystats ) ){
        if (array_key_exists("totalcount",$summarystats)){
            $datarray=array();
            $readtypearray=array('Raw Template','Raw Complement', 'template','complement','2d');
            foreach($readtypearray as $type){
                if (isset ($summarystats["avglen"][$type])){
                    $datarray[$type][]=$summarystats["avglen"][$type];
                }
            }
            $jsonstring = "";
            $jsonstring = $jsonstring . "[\n";
            foreach($readtypearray as $type){
                if (isset ($datarray[$type])){
                    $jsonstring = $jsonstring . "{\n";
                    $jsonstring = $jsonstring . '"name":"'.$type. '"' . ",\n";
                    $jsonstring = $jsonstring . '"data":[' . implode(",",$datarray[$type]) . "]\n";
                    $jsonstring = $jsonstring . "},\n";
                }
            }
            $jsonstring = $jsonstring . "]\n";
        }
    }
    return $jsonstring;
}

##Yield:

function bases($jobname,$currun){
    $summarystats = retrievefromsession($currun,"summarystats","");
    if( !empty( $summarystats ) ){
        if (array_key_exists("totalcount",$summarystats)){
            $datarray=array();
            $readtypearray=array('Raw Template','Raw Complement', 'template','complement','2d');
            foreach($readtypearray as $type){
                if (isset ($summarystats["totalyield"][$type])){
                    $datarray[$type][]=$summarystats["totalyield"][$type];
                }
            }
            $jsonstring = "";
            $jsonstring = $jsonstring . "[\n";
            foreach($readtypearray as $type){
                if (isset ($datarray[$type])){
                    $jsonstring = $jsonstring . "{\n";
                    $jsonstring = $jsonstring . '"name":"'.$type. '"' . ",\n";
                    $jsonstring = $jsonstring . '"data":[' . implode(",",$datarray[$type]) . "]\n";
                    $jsonstring = $jsonstring . "},\n";
                }
            }
            $jsonstring = $jsonstring . "]\n";
        }
    }
    return $jsonstring;
}



//Calculating front page summary information for read upload.
function readnumberupload($jobname,$currun){
    $summarystats = retrievefromsession($currun,"summarystats","");
    if( !empty( $summarystats ) ){
        if (array_key_exists("totalcount",$summarystats)){
            $datarray=array();
            $count=0;
            $jsonstring="";
            $readtypearray=array('template','complement','2d');
            foreach($readtypearray as $type){
                $count++;
                if (isset($summarystats["totalcount"][$type])){
                    $datarray["Called"][]=$summarystats["totalcount"][$type];
                }
                if (isset($summarystats["totalalign"][$type])){
                    $datarray["Aligned"][]=$summarystats["totalalign"][$type];
                }
                if (isset($summarystats["processed"][$type])){
                    $datarray["Processed"][]=$summarystats["processed"][$type];
                }
            }
            for ($x = 0; $x < $count; $x++){
                $datarray["Uploaded"][]=$summarystats["totalcount"]["uploaded"];
            }
            $jsonstring = "";
            $jsonstring = $jsonstring . "[\n";
            $resultarray=array('Uploaded','Called','Aligned','Processed');
            foreach($resultarray as $type){
                if (isset($datarray[$type])){
                    $jsonstring = $jsonstring . "{\n";
                        $jsonstring = $jsonstring . '"name":"'.$type. '"' . ",\n";
                        $jsonstring = $jsonstring . '"data":[' . implode(",",$datarray[$type]) . "]\n";
                        $jsonstring = $jsonstring . "},\n";
                    }
            }
            $jsonstring = $jsonstring . "]\n";
        }
    }
    return $jsonstring;
}

###Attempt to calculate a boxplot of read lengths

function boxplotlength($jobname,$currun) {
    $summarystats = retrievefromsession($currun,"summarystats","");
    if( !empty( $summarystats ) ){
        if (array_key_exists("minlen",$summarystats)){
            $datarray=array();
            $jsonstring="";
            $readtypearray=array('Raw Template','Raw Complment','template','complement','2d');
            foreach($readtypearray as $type){
                #$jsonstring = $jsonstring . $type;
                if (isset ($summarystats["minlen"][$type])){
                #if (isset ($summarystats["minlen"])){
                #    $jsonstring = $jsonstring . "hello";
                    $datarray[$type][]=$summarystats["minlen"][$type];
                    $datarray[$type][]=$summarystats["first_q"][$type];
                    $datarray[$type][]=$summarystats["median"][$type];
                    $datarray[$type][]=$summarystats["third_q"][$type];
                    $datarray[$type][]=$summarystats["maxlen"][$type];
                }

            }
            ##$jsonstring = $jsonstring . var_dump($summarystats);
            $jsonstring = $jsonstring . "[\n";
            $jsonstring = $jsonstring . "{\n";
                $jsonstring = $jsonstring . '"name": "Observations",' . "\n";
                $jsonstring = $jsonstring . '"data":[' .  "\n";
                foreach($readtypearray as $type){
                    if (isset ($datarray[$type])){
                        $jsonstring = $jsonstring . '[' . implode(",",$datarray[$type]) . "],\n";
                    }
                }
                $jsonstring = $jsonstring . "]\n";
                $jsonstring = $jsonstring . "},\n";
                $jsonstring = $jsonstring . "]\n";

        }
    }
    return $jsonstring;
}

function compare_startime($a, $b)
  {
    return strnatcmp($a[0], $b[0]);
  }

  // sort alphabetically by name
  //usort($data, 'compare_lastname');



  ###Calculates an approximation of the speed of sequencing by caluclating the number of bases sequenced in a 5 minute window per channel - this is a measure of speed through the pore.
function sequencingrate($jobname,$currun) {
  	$checkvar = $currun . $jobname;
  	//echo $type . "\n";
  	$checkrunning = $currun . $jobname . "status";
  	global $memcache;
  	global $mindb_connection;
  	global $reflength;
  	$jsonstring = $memcache->get("$checkvar");
      //echo "String length is " . strlen($jsonstring);
  	$checkingrunning = $memcache->get("$checkrunning");
  	if($checkingrunning === "No" || $checkingrunning === FALSE){
          if (strlen($jsonstring) <= 1){
  		    //$memcache->set("$checkrunning", "YES", 0, 0);
  		    $checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
      		$checking=$mindb_connection->query($checkrow);
      		if (is_object($checking) && $checking->num_rows ==1){
      			//echo "We have already run this!";
      			foreach ($checking as $row){
      				$jsonstring = $row['json'];
      			}
      		} else {

      			//do something interesting here...
      			//Query to get pore occupancy over 15 minutes -> select (floor((basecalled_template.start_time)/60/15)*60*15+exp_start_time)*1000 as bin_floor, count(distinct config_general.channel) as chandist, sum(basecalled_template.duration+basecalled_complement.duration)/count(distinct config_general.channel)/9 as occupancy from basecalled_template inner join tracking_id using (basename_id) inner join config_general using (basename_id) inner join basecalled_complement using (basename_id) group by 1 order by 1
      			#$sqltemplate = "select (basecalled_template.5minwin*5*60+basecalled_template.exp_start_time)*1000 as bin_floor,sum(basecalled_template.duration) as time,sum(ifnull(basecalled_template.seqlen,0)+ifnull(basecalled_complement.seqlen,0))/60/5/count(distinct config_general.channel) as effective_rate, count(distinct config_general.channel) as chandist, sum(basecalled_template.seqlen)/sum(basecalled_template.duration) as rate from basecalled_template inner join config_general using (basename_id) left join basecalled_complement using (basename_id) group by 1 order by 1;";
                $sqltemplate = "select 1minwin*60*1000 as bin_floor,IFNULL(basecalled_template_1minwin_sum.cumuduration,0)+IFNULL(basecalled_complement_1minwin_sum.cumuduration,0) as time,(IFNULL(basecalled_template_1minwin_sum.cumuduration,0)+IFNULL(basecalled_complement_1minwin_sum.cumuduration,0))/60/basecalled_template_1minwin_sum.distchan as effective_rate,basecalled_template_1minwin_sum.distchan as chandist, basecalled_template_1minwin_sum.cumulength/basecalled_template_1minwin_sum.cumuduration as rate from basecalled_template_1minwin_sum left join basecalled_complement_1minwin_sum using (1minwin) order by 1;";
                $sqlcomplement = "select 1minwin*60*1000 as bin_floor,basecalled_template_1minwin_sum.cumuduration+basecalled_complement_1minwin_sum.cumuduration as time,(basecalled_template_1minwin_sum.cumulength+basecalled_complement_1minwin_sum.cumulength)/60/basecalled_complement_1minwin_sum.distchan as effective_rate,basecalled_complement_1minwin_sum.distchan as chandist, basecalled_complement_1minwin_sum.cumulength/basecalled_complement_1minwin_sum.cumuduration as rate from basecalled_template_1minwin_sum inner join basecalled_complement_1minwin_sum using (1minwin) where basecalled_complement_1minwin_sum.bases != 'Null' order by 1;";
                #$sqlcomplement = "select (basecalled_complement.5minwin*5*60+basecalled_complement.exp_start_time)*1000 as bin_floor, sum(basecalled_complement.duration) as time,  sum(ifnull(basecalled_template.seqlen,0)+ifnull(basecalled_complement.seqlen,0))/60/5/count(distinct config_general.channel) as effective_rate, count(distinct config_general.channel) as channels, sum(basecalled_complement.seqlen)/sum(basecalled_complement.duration) as rate from basecalled_template inner join config_general using (basename_id)  inner join basecalled_complement using (basename_id) group by 1 order by 1;";
      			$prebasecalledevents="select (floor((pre_config_general.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor, sum(pre_config_general.total_events) as total_events,  sum(pre_config_general.total_events)/60/5/count(*) as effective_rate, sum(pre_config_general.total_events)/sum(pre_config_general.total_events/pre_config_general.sample_rate)/100 as rate from pre_config_general inner join pre_tracking_id using (basename_id) group by 1 order by 1;";

      			$resulttemplate = $mindb_connection->query($sqltemplate);
      			$resultcomplement = $mindb_connection->query($sqlcomplement);
      			$resultprebasecalledevents = $mindb_connection->query($prebasecalledevents);

      			$resultarray=array();

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
                if (isset($resultprebasecalledevents->num_rows)){
      			    if ( $resultprebasecalledevents->num_rows >= 1){
      				foreach($resultprebasecalledevents as $row){
      					$resultarray['Raw Event Rate'][$row['bin_floor']]=$row['rate'];
      					$resultarray['Raw Effective Rate'][$row['bin_floor']]=$row['effective_rate'];
      				}
                }
      			}

      		    $jsonstring="";
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
                          if ($value2 > 0){
      					    $jsonstring = $jsonstring . "[  $key2 , $value2 ],";
                          }
      				}

      			    $jsonstring = $jsonstring . "]\n";
      		        $jsonstring = $jsonstring . "},\n";
      			}
      	        $jsonstring = $jsonstring . "]\n";
  	        }
      		if ($_GET["prev"] == 1){
      			include 'savejson.php';
      		}
      		$memcache->set("$checkvar", "$jsonstring",MEMCACHE_COMPRESSED,5);

  	    }else{
              //echo "We're pulling back an old value\n";
  		    $jsonstring = $memcache->get("$checkvar");
          }
  	}
  	// cache for 2 minute as we want yield to update semi-regularly...
  	$memcache->delete("$checkrunning");
     	return $jsonstring;

  }


  //THIS IS BROKEN IN THIS NEW CODE IT DOESN"T ACTUALLY WORK
    function sequencingrate_new($jobname,$currun) {
        global $memcache;
        global $mindb_connection;
        $jsonstring = "";  //A simple variable to hold the value of the jsonstring to return
        //Flag to check to see if this job needs re-running or has expired
        $flagstate = $currun.$jobname."flag";
        //Flag to check if job is already running
        $runstate = $currun.$jobname."runstate";
        //Holder to store the actual data
        $storedvalue = $currun.$jobname."store";
        //Get flagstate and runstate from the memcache store.
        $checkflagstate = $memcache->get("$flagstate");
        $checkrunstate = $memcache->get("$runstate");
        //First check to see if it is safe to retrieve data from the store - it will be if checkflagstate is true or checkrunstate is true
        if ($checkflagstate == "True" || $checkrunstate == "True"){
            //echo "Retrieving current value";
            $jsonstring = $memcache->get("$storedvalue");
        } else {

            //We want to check if we are a previous run - in which case the data ought to be stored in the database
            if ($_GET["prev"] == 1){
                //echo "We are running a previous database so values should be recorded.";
            	$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
    		    $checking=$mindb_connection->query($checkrow);
    		    if (is_object($checking) && $checking->num_rows ==1){
    			//             echo "We have already run this!";
    			    foreach ($checking as $row){
    				    $jsonstring = $row['json'];
    			    }
    		    }
            }
            if (strlen($jsonstring) < 1){
                //echo "Value expired - recalculating";
                //This will run only if both flags are false and so will start to process the data. Therefore it must set the running flag to True
                $memcache->set("$runstate", "True",0,0);
                //Get previous json entry to merge
                $jsonstringprev = $memcache->get("$storedvalue");
                //We want to test the length of this to see if there is a stored value or not/
                //echo strlen($jsonstringprev) . "\n";
                //A holder for the limit query:
                $limiter = "";
                //If the returned value is non existent then we need to analyse all the data
                if (strlen($jsonstringprev) == 0){
                    $limiter = "";
                }else{
                    $limiter = " limit 5";
                }
                $jsonstringarray = json_decode($jsonstringprev);
                //echo "previous\n";
                //var_dump($jsonstringarray);
                //Now we execute the code to retrieve whatever values we are after and store them in the $jsonstring value:
                //do something interesting here...
                //Query to get pore occupancy over 15 minutes -> select (floor((basecalled_template.start_time)/60/15)*60*15+exp_start_time)*1000 as bin_floor, count(distinct config_general.channel) as chandist, sum(basecalled_template.duration+basecalled_complement.duration)/count(distinct config_general.channel)/9 as occupancy from basecalled_template inner join tracking_id using (basename_id) inner join config_general using (basename_id) inner join basecalled_complement using (basename_id) group by 1 order by 1
      			$sqltemplate = "select (basecalled_template.5minwin*5*60+basecalled_template.exp_start_time)*1000 as bin_floor,sum(basecalled_template.duration) as time,sum(ifnull(basecalled_template.seqlen,0)+ifnull(basecalled_complement.seqlen,0))/60/5/count(distinct config_general.channel) as effective_rate, count(distinct config_general.channel) as chandist, sum(basecalled_template.seqlen)/sum(basecalled_template.duration) as rate from basecalled_template inner join config_general using (basename_id) left join basecalled_complement using (basename_id) group by 1 order by 1 desc ".$limiter .";";
      			$sqlcomplement = "select (basecalled_complement.5minwin*5*60+basecalled_complement.exp_start_time)*1000 as bin_floor, sum(basecalled_complement.duration) as time,  sum(ifnull(basecalled_template.seqlen,0)+ifnull(basecalled_complement.seqlen,0))/60/5/count(distinct config_general.channel) as effective_rate, count(distinct config_general.channel) as channels, sum(basecalled_complement.seqlen)/sum(basecalled_complement.duration) as rate from basecalled_template inner join config_general using (basename_id)  inner join basecalled_complement using (basename_id) group by 1 order by 1 desc ".$limiter .";";
      			$prebasecalledevents="select (floor((pre_config_general.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor, sum(pre_config_general.total_events) as total_events,  sum(pre_config_general.total_events)/60/5/count(*) as effective_rate, sum(pre_config_general.total_events)/sum(pre_config_general.total_events/pre_config_general.sample_rate)/100 as rate from pre_config_general inner join pre_tracking_id using (basename_id) group by 1 order by 1 desc ".$limiter .";";

      			$resulttemplate = $mindb_connection->query($sqltemplate);
      			$resultcomplement = $mindb_connection->query($sqlcomplement);
      			$resultprebasecalledevents = $mindb_connection->query($prebasecalledevents);

      			$resultarray=array();
                $resultarray2=array();

      			if ($resulttemplate->num_rows >=1) {
      				#$cumucount = 0;
      				foreach ($resulttemplate as $row) {
      					#$cumucount++;
      					$resultarray['template rate'][$row['bin_floor']]=$row['rate'];
      					$resultarray['template effective rate'][$row['bin_floor']]=$row['effective_rate'];
                        $temparray = array();
                        $resultarray2[0]["name"]='template rate';
                        array_push($temparray,$row['bin_floor']);
                        array_push($temparray, $row['rate']);
                        $resultarray2[0]["data"][]=$temparray;
                        $temparray = array();
                        $resultarray2[1]["name"]='template effective rate';
                        array_push($temparray,$row['bin_floor']);
                        array_push($temparray, $row['effective_rate']);
                        $resultarray2[1]["data"][]=$temparray;

      				}
      			}
      			if ($resultcomplement->num_rows >=1) {
      				#$cumucount = 0;
      				foreach ($resultcomplement as $row) {
      					#$cumucount++;
      					$resultarray['complement rate'][$row['bin_floor']]=$row['rate'];
      					$resultarray['complement effective rate'][$row['bin_floor']]=$row['effective_rate'];
                        $temparray = array();
                        $resultarray2[2]["name"]='complement rate';
                        array_push($temparray,$row['bin_floor']);
                        array_push($temparray, $row['rate']);
                        $resultarray2[2]["data"][]=$temparray;
                        $temparray = array();
                        $resultarray2[3]["name"]='complement effective rate';
                        array_push($temparray,$row['bin_floor']);
                        array_push($temparray, $row['effective_rate']);
                        $resultarray2[3]["data"][]=$temparray;
      				}
      			}

      			if ($resultprebasecalledevents->num_rows >= 1){
      				foreach($resultprebasecalledevents as $row){
      					$resultarray['Raw Event Rate'][$row['bin_floor']]=$row['rate'];
      					$resultarray['Raw Effective Rate'][$row['bin_floor']]=$row['effective_rate'];
                        $temparray = array();
                        $resultarray2[4]["name"]='Raw Event Rate';
                        array_push($temparray,$row['bin_floor']);
                        array_push($temparray, $row['rate']);
                        $resultarray2[4]["data"][]=$temparray;
                        $temparray = array();
                        $resultarray2[5]["name"]='Raw Effective Rate';
                        array_push($temparray,$row['bin_floor']);
                        array_push($temparray, $row['effective_rate']);
                        $resultarray2[5]["data"][]=$temparray;
      				}
      			}

              //var_dump($resultarray);
                //echo "Going through result array.\n";
                foreach ($resultarray2 as $index=>$value){
                    //echo $index . "\n";
                    foreach ($resultarray2[$index]["data"] as &$key2){
                    //    echo $key2[0] . "\t" . $key2[1] . "\t";
                    //    echo "Lookup this value in the original array\n";
                        $match = 0;
                        //echo gettype($jsonstringarray[$index]->name);
                        foreach ($jsonstringarray[$index]->data as &$key3){
                            if ($key3[0]==$key2[0]){
                    //            echo "We got a match";
                                $match = 1;
                                #$jsonstringarray[0]->data[$key3][0]=$key2[0];
                                $key3[1]=$key2[1];
                //                    echo "reset value " . $key3[1];
                            }
            //                echo "\t original value " . $key2[1] . "\n";
                        }
                        if ($match == 0) {
                            //echo "wanna push \n";
                            $jsonstringarray[$index]->name= $resultarray2[$index]["name"];
                            $jsonstringarray[$index]->data[]= array($key2[0],$key2[1]);
                            usort($jsonstringarray[$index]->data, 'compare_startime');

                            //echo gettype(array($key2[0],$key2[1]));
                        }else{
                        //echo "no push\n";
                    }
                }
            }


                //var_dump($jsonstringarray);
                //var_dump(json_encode(array_merge($jsonstringarray,$resultarray2)));

                #$jsonstring = json_encode($resultarray2);
                $jsonstring = json_encode($jsonstringarray);
                #if ($_GET["prev"] == 1){
        		include 'savejson.php';
        		#}
            }
            //Now we store the value we are after in the memcache holder for the value with no time out (so it persists):
            $memcache->set("$storedvalue",$jsonstring,0,10);
            //Now we store the flag to report the data is updated
            $memcache->set("$flagstate","True",0,1); //Note this will expire after 1 seconds.
            //Finally we set the running flag to false

            $memcache->set("$runstate", "False",0,0);

        }
        return $jsonstring;
    }

//THIS IS BROKEN IN THIS NEW CODE IT DOESN"T ACTUALLY WORK
  function cumulativeyield($jobname,$currun) {
      global $memcache;
      global $mindb_connection;
      $jsonstring = "";  //A simple variable to hold the value of the jsonstring to return
      //Flag to check to see if this job needs re-running or has expired
      $flagstate = $currun.$jobname."flag";
      ////echo "$flagstate";
      //echo "\n";
      //Flag to check if job is already running
      $runstate = $currun.$jobname."runstate";
      //echo "$runstate";
      //echo "\n";
      //Holder to store the actual data
      $storedvalue = $currun.$jobname."store";
      //Get flagstate and runstate from the memcache store.
      $checkflagstate = $memcache->get("$flagstate");
      //echo $checkflagstate;
      //echo "\n";
      $checkrunstate = $memcache->get("$runstate");
      //echo $checkrunstate;
      //echo "\n";
      //First check to see if it is safe to retrieve data from the store - it will be if checkflagstate is true or checkrunstate is true
      if ($checkflagstate == "True" || $checkrunstate == "True"){
          //echo "Retrieving current value";
          $jsonstring = $memcache->get("$storedvalue");
      } else {
          //echo "SLART";
          //We want to check if we are a previous run - in which case the data ought to be stored in the database
          if ($_GET["prev"] == 1){
              //echo "We are running a previous database so values should be recorded.";
          	$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
  		    $checking=$mindb_connection->query($checkrow);
  		    if (is_object($checking) && $checking->num_rows ==1){
  			//             echo "We have already run this!";
  			    foreach ($checking as $row){
  				    $jsonstring = $row['json'];
  			    }
  		    }
          }
          if (strlen($jsonstring) < 1){
              //echo "Value expired - recalculating";
              //This will run only if both flags are false and so will start to process the data. Therefore it must set the running flag to True
              $memcache->set("$runstate", "True",0,0);
              //Get previous json entry to merge
              $jsonstringprev = $memcache->get("$storedvalue");
              //We want to test the length of this to see if there is a stored value or not/
              //echo strlen($jsonstringprev) . "\n";
              //A holder for the limit query:
              $limiter = "";
              //If the returned value is non existent then we need to analyse all the data
              if (strlen($jsonstringprev) == 0){
                  $limiter = "";
              }else{
                  $limiter = " limit 5";
              }
              $jsonstringarray = json_decode($jsonstringprev);
              //echo "previous\n";
              //var_dump($jsonstringarray);
              //Now we execute the code to retrieve whatever values we are after and store them in the $jsonstring value:
              //do something interesting here...
              #$sqltemplate = "select (basecalled_template.5minwin*60*5+exp_start_time)*1000 as bin_floor,   sum(seqlen)/count(*) as meanlength from basecalled_template group by 2,1 order by 2,1;";
              $sqltemplate = "select 1minwin,readcount,passcount from basecalled_template_1minwin_sum order by 1;";
              $sqlcomplement = "select 1minwin,readcount,passcount from basecalled_complement_1minwin_sum where readcount !='NULL'  order by 1 ;";
              #$sqltemplate = "select 5minwin,exp_start_time, count(*) as count from basecalled_template group by 2,1 order by 2,1  ".$limiter .";";
              #$sqltemplatepass = "select 5minwin,exp_start_time, count(*) as count from basecalled_template where pass = 1  group by 2,1 order by 2,1  ".$limiter .";";
  			//$sqlcomplement = "select 5minwin,exp_start_time, count(*) as count from basecalled_complement group by 2,1 order by 2,1  ".$limiter .";";
  			//$sqlcomplementpass = "select 5minwin,exp_start_time, count(*) as count from basecalled_complement where pass = 1  group by 2,1 order by 2,1  ".$limiter .";";
            $sql2d = "select 1minwin,readcount,passcount from basecalled_2d_1minwin_sum where readcount !='NULL' order by 1;";
            //$sql2d = "select 5minwin,exp_start_time, count(*) as count from basecalled_2d group by 2,1 order by 2,1  ".$limiter .";";
  			//$sql2dpass = "select 5minwin,exp_start_time, count(*) as count from basecalled_2d where pass = 1  group by 2,1 order by 2,1  ".$limiter .";";
  			$pretemplate = "select (floor((pre_config_general.start_time)/60/5)*5*60+exp_start_time)*1000 as bin_floor, count(*) as count from pre_config_general inner join pre_tracking_id using (basename_id)  group by 1 order by 1  ".$limiter .";";
  			$precomplement = "select (floor((pre_config_general.start_time)/60/5)*5*60+exp_start_time)*1000 as bin_floor, count(*) as count from pre_config_general inner join pre_tracking_id using (basename_id) where pre_config_general.hairpin_found = 1 group by 1 order by 1  ".$limiter .";";
                //echo $sqltemplate;
  			#$sqltemplate = "SELECT (start_time+exp_start_time)*1000 as time FROM basecalled_template inner join tracking_id using (basename_id) order by start_time;";
  			#$sqlcomplement = "SELECT (start_time+exp_start_time)*1000 as time FROM basecalled_complement inner join tracking_id using (basename_id) order by start_time;";
  			#$sql2d = "SELECT (start_time+exp_start_time)*1000 as time FROM basecalled_2d inner join tracking_id using (basename_id) inner join basecalled_complement using (basename_id) order by start_time;";
  			//echo $sqltemplate;
  			$resulttemplate = $mindb_connection->query($sqltemplate);
  			$resultcomplement = $mindb_connection->query($sqlcomplement);
  			$result2d = $mindb_connection->query($sql2d);
  			//$resulttemplatepass = $mindb_connection->query($sqltemplatepass);
  			//$resultcomplementpass = $mindb_connection->query($sqlcomplementpass);
  			//$result2dpass = $mindb_connection->query($sql2dpass);
  			$resultpretemp = $mindb_connection->query($pretemplate);
  			$resultprecomp = $mindb_connection->query($precomplement);

  			$resultarray=array();
            $resultarray2=array();
            $INDEXPOSITION = 0;

  			if ($resulttemplate->num_rows >=1) {
  				$cumucount = 0;

  				foreach ($resulttemplate as $row) {
                    $temparray=array();
  					$cumucount=$cumucount+$row['readcount'];
  				    #echo "Count is ". $row['count'] . "\n";
  					#echo "Cumu Count is ". $cumucount . "\n";
                    #echo "Time is " . $row['5minwin'] . "\n";
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    #echo "Binfloor is " . $binfloor . "\n";
                    #echo $binfloor . "\t" . $cumucount . "\n";
  					#$resultarray['template'][$cumucount]=$row['bin_floor'];

  					$resultarray['template'][$binfloor]=$cumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='template';
                    array_push($temparray,$binfloor);
                    array_push($temparray, $cumucount);
                    //echo "Temparray is " . var_dump($temparray) . "\n";
                    $resultarray2[$INDEXPOSITION]["data"][]=$temparray;
                }
                $INDEXPOSITION++;

                $passcumucount = 0;
                foreach ($resulttemplate as $row){
                    $passtemparray=array();
  					$passcumucount=$passcumucount+$row['passcount'];
                    //echo $passcumucount;
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    $resultarray['template pass'][$binfloor]=$passcumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='template pass';
                    array_push($passtemparray,$binfloor);
                    array_push($passtemparray, $passcumucount);
                    $resultarray2[$INDEXPOSITION]["data"][]=$passtemparray;

  				}
                $INDEXPOSITION++;
            }

            if ($resultcomplement->num_rows >=1) {
  				$cumucount = 0;

  				foreach ($resultcomplement as $row) {
                    $temparray=array();
  					$cumucount=$cumucount+$row['readcount'];
  				    #echo "Count is ". $row['count'] . "\n";
  					#echo "Cumu Count is ". $cumucount . "\n";
                    #echo "Time is " . $row['5minwin'] . "\n";
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    #echo "Binfloor is " . $binfloor . "\n";
                    #echo $binfloor . "\t" . $cumucount . "\n";
  					#$resultarray['complement'][$cumucount]=$row['bin_floor'];

  					$resultarray['complement'][$binfloor]=$cumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='complement';
                    array_push($temparray,$binfloor);
                    array_push($temparray, $cumucount);
                    //echo "Temparray is " . var_dump($temparray) . "\n";
                    $resultarray2[$INDEXPOSITION]["data"][]=$temparray;
                }
                $INDEXPOSITION++;

                $passcumucount = 0;
                foreach ($resultcomplement as $row){
                    $passtemparray=array();
  					$passcumucount=$passcumucount+$row['passcount'];
                    //echo $passcumucount;
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    $resultarray['complement pass'][$binfloor]=$passcumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='complement pass';
                    array_push($passtemparray,$binfloor);
                    array_push($passtemparray, $passcumucount);
                    $resultarray2[$INDEXPOSITION]["data"][]=$passtemparray;

  				}
                $INDEXPOSITION++;
            }
            if ($result2d->num_rows >=1) {
  				$cumucount = 0;

  				foreach ($result2d as $row) {
                    $temparray=array();
  					$cumucount=$cumucount+$row['readcount'];
  				    #echo "Count is ". $row['count'] . "\n";
  					#echo "Cumu Count is ". $cumucount . "\n";
                    #echo "Time is " . $row['5minwin'] . "\n";
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    #echo "Binfloor is " . $binfloor . "\n";
                    #echo $binfloor . "\t" . $cumucount . "\n";
  					#$resultarray['complement'][$cumucount]=$row['bin_floor'];

  					$resultarray['2d'][$binfloor]=$cumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='2d';
                    array_push($temparray,$binfloor);
                    array_push($temparray, $cumucount);
                    //echo "Temparray is " . var_dump($temparray) . "\n";
                    $resultarray2[$INDEXPOSITION]["data"][]=$temparray;
                }
                $INDEXPOSITION++;

                $passcumucount = 0;
                foreach ($result2d as $row){
                    $passtemparray=array();
  					$passcumucount=$passcumucount+$row['passcount'];
                    //echo $passcumucount;
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    $resultarray['2d pass'][$binfloor]=$passcumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='2d pass';
                    array_push($passtemparray,$binfloor);
                    array_push($passtemparray, $passcumucount);
                    $resultarray2[$INDEXPOSITION]["data"][]=$passtemparray;

  				}
                $INDEXPOSITION++;
            }
  			/*if ($result2d->num_rows >=1) {
  				$cumucount = 0;
  				foreach ($result2d as $row) {
                    $temparray=array();
  					$cumucount=$cumucount+$row['count'];
                      $binfloor = ($row['5minwin']*5*60+$row['exp_start_time'])*1000;
  					$resultarray['2d'][$binfloor]=$cumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='2d';
                    array_push($temparray,$binfloor);
                    array_push($temparray, $cumucount);
                    $resultarray2[$INDEXPOSITION]["data"][]=$temparray;
  				}
                $INDEXPOSITION++;
  			}


  			if ($result2dpass->num_rows >=1) {
  				$cumucount = 0;
  				foreach ($result2dpass as $row) {
                    $temparray=array();
  					$cumucount=$cumucount+$row['count'];
                      $binfloor = ($row['5minwin']*5*60+$row['exp_start_time'])*1000;
  					$resultarray['2d pass'][$binfloor]=$cumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='2d pass';
                    array_push($temparray,$binfloor);
                    array_push($temparray, $cumucount);
                    $resultarray2[$INDEXPOSITION]["data"][]=$temparray;
  				}
                $INDEXPOSITION++;
  			}
            */
  			if (isset($resultpretemp->num_rows) && $resultpretemp->num_rows >=1) {
  				$cumucount = 0;
  				foreach ($resultpretemp as $row) {
                    $temparray=array();
  					$cumucount=$cumucount+$row['count'];
  					$resultarray['Raw Template'][$row['bin_floor']]=$cumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='Raw Template';
                    array_push($temparray,$binfloor);
                    array_push($temparray, $cumucount);
                    $resultarray2[$INDEXPOSITION]["data"][]=$temparray;
  				}
                $INDEXPOSITION++;
  			}
  			if (isset($resultprecomp->num_rows) && $resultprecomp->num_rows >=1) {
  				$cumucount = 0;
  				foreach ($resultprecomp as $row) {
                    $temparray=array();
  					$cumucount=$cumucount+$row['count'];
  					$resultarray['Raw Complement'][$row['bin_floor']]=$cumucount;
                    $resultarray2[$INDEXPOSITION]["name"]='Raw Complement';
                    array_push($temparray,$binfloor);
                    array_push($temparray,$cumucount);
                    $resultarray2[$INDEXPOSITION]["data"][]=$temparray;
  				}
                $INDEXPOSITION++;
  			}
                //var_dump($resultarray2);
              //echo "Going through result array.\n";
              foreach ($resultarray2 as $index=>$value){
                 //echo $index . "\n";
                  foreach ($resultarray2[$index]["data"] as &$key2){
                    //  echo $key2[0] . "\t" . $key2[1] . "\n";
                  //    echo "Lookup this value in the original array\n";
                      $match = 0;
                      //echo gettype($jsonstringarray[$index]->name);
                      if (isset($jsonstringarray[$index])){
                      foreach ($jsonstringarray[$index]->data as &$key3){
                          if ($key3[0]==$key2[0]){
                  //            echo "We got a match";
                              $match = 1;
                              #$jsonstringarray[0]->data[$key3][0]=$key2[0];
                              $key3[1]=$key2[1];
              //                    echo "reset value " . $key3[1];
                          }
          //                echo "\t original value " . $key2[1] . "\n";
                        }
                    }
                      if ($match == 0) {
                          //echo "wanna push \n";
                          $jsonstringarray[$index]->name=$resultarray2[$index]["name"];
                          $jsonstringarray[$index]->data[]= array($key2[0],$key2[1]);
                          //usort($jsonstringarray[$index]->data, 'compare_startime');
                          //ksort($jsonstringarray[$index]->data, 'compare_startime');
                          //echo gettype(array($key2[0],$key2[1]));
                      }else{
                      //echo "no push\n";
                  }
              }
          }


              //var_dump($jsonstringarray);
              //var_dump(json_encode(array_merge($jsonstringarray,$resultarray2)));

              #$jsonstring = json_encode($resultarray2);
              $jsonstring = json_encode($jsonstringarray);
              #if ($_GET["prev"] == 1){
      		include 'savejson.php';
      		#}
          }
          //Now we store the value we are after in the memcache holder for the value with no time out (so it persists):
          $memcache->set("$storedvalue",$jsonstring,0,10);
          //Now we store the flag to report the data is updated
          $memcache->set("$flagstate","True",0,1); //Note this will expire after 1 seconds.
          //Finally we set the running flag to false
          //echo $checkrunstate;
          $memcache->set("$runstate", "False",0,0);

      }
      return $jsonstring;
  }

function lengthtimewindow($jobname,$currun) {
    global $memcache;
    global $mindb_connection;
    $jsonstring = "";  //A simple variable to hold the value of the jsonstring to return
    //Flag to check to see if this job needs re-running or has expired
    $flagstate = $currun.$jobname."flag";
    //Flag to check if job is already running
    $runstate = $currun.$jobname."runstate";
    //Holder to store the actual data
    $storedvalue = $currun.$jobname."store";
    //Get flagstate and runstate from the memcache store.
    $checkflagstate = $memcache->get("$flagstate");
    $checkrunstate = $memcache->get("$runstate");
    //First check to see if it is safe to retrieve data from the store - it will be if checkflagstate is true or checkrunstate is true
    if ($checkflagstate == "True" || $checkrunstate == "True"){
        //echo "Retrieving current value";
        $jsonstring = $memcache->get("$storedvalue");
    } else {

        //We want to check if we are a previous run - in which case the data ought to be stored in the database
        if ($_GET["prev"] == 1){
            //echo "We are running a previous database so values should be recorded.";
        	$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		    $checking=$mindb_connection->query($checkrow);
		    if (is_object($checking) && $checking->num_rows ==1){
			//             echo "We have already run this!";
			    foreach ($checking as $row){
				    $jsonstring = $row['json'];
			    }
		    }
        }
        if (strlen($jsonstring) < 1){
            //echo "Value expired - recalculating";
            //This will run only if both flags are false and so will start to process the data. Therefore it must set the running flag to True
            $memcache->set("$runstate", "True",0,0);
            //Get previous json entry to merge
            $jsonstringprev = $memcache->get("$storedvalue");
            //We want to test the length of this to see if there is a stored value or not/
            //echo strlen($jsonstringprev) . "\n";
            //A holder for the limit query:
            $limiter = "";
            //If the returned value is non existent then we need to analyse all the data
            if (strlen($jsonstringprev) == 0){
                $limiter = "";
            }else{
                $limiter = " limit 5";
            }
            $jsonstringarray = json_decode($jsonstringprev);
            //echo "previous\n";
            //var_dump($jsonstringarray);
            //Now we execute the code to retrieve whatever values we are after and store them in the $jsonstring value:
            //do something interesting here...
            #$sqltemplate = "select (basecalled_template.5minwin*60*5+exp_start_time)*1000 as bin_floor,   sum(seqlen)/count(*) as meanlength from basecalled_template group by 2,1 order by 2,1;";
            $sqltemplate = "select 1minwin,exp_start_time,sum(seqlen)/count(*) as meanlength from basecalled_template group by 2,1 order by 2,1 desc ".$limiter .";";
            $sqlcomplement = "select 1minwin,exp_start_time,sum(seqlen)/count(*) as meanlength from basecalled_complement group by 2,1 order by 2,1 desc ".$limiter .";";
            $sql2d = "select 1minwin,exp_start_time,sum(basecalled_2d.seqlen)/count(*) as meanlength from basecalled_2d  group by 2,1 order by 2,1 desc ".$limiter .";";
            $pretemplate = "select (floor((pre_config_general.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor,   sum(hairpin_event_index)/count(*) asx meanlength from pre_config_general inner join pre_tracking_id using (basename_id) group by 2,1 order by 2,1 desc ".$limiter .";";
            $precomplement = "select (floor((pre_config_general.start_time)/60/5)*60*5+exp_start_time)*1000 as bin_floor,   sum(total_events-hairpin_event_index)/count(*) as meanlength from pre_config_general inner join pre_tracking_id using (basename_id) group by 2,1 order by 2,1 desc ".$limiter .";";
            $resulttemplate = $mindb_connection->query($sqltemplate);
            $resultcomplement = $mindb_connection->query($sqlcomplement);
            $result2d = $mindb_connection->query($sql2d);
            $resultpretemp = $mindb_connection->query($pretemplate);
            $resultprecomp = $mindb_connection->query($precomplement);

            $resultarray=array();
            $resultarray2=array();

            if ($resulttemplate->num_rows >=1) {
                #$cumucount = 0;
                foreach ($resulttemplate as $row) {
                    $temparray=array();
                    $binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $resultarray['template length'][$binfloor]=$row['meanlength'];
                    $resultarray2[0]["name"]='template length';
                    array_push($temparray,$binfloor);
                    array_push($temparray,floatval($row['meanlength']));
                    $resultarray2[0]["data"][]=$temparray;
                    //array_push($resultarray2[0]["data"], [$binfloor,$row['meanlength']]);
                    #$resultarray['template effective rate'][$row['bin_floor']]=$row['effective_rate'];
                }
            }
            if ($resultcomplement->num_rows >=1) {
                #$cumucount = 0;
                foreach ($resultcomplement as $row) {
                    #$cumucount++;
                    $temparray=array();
                    $binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $resultarray['complement length'][$binfloor]=$row['meanlength'];
                    $resultarray2[1]["name"]='complement length';
                    array_push($temparray,$binfloor);
                    array_push($temparray,floatval($row['meanlength']));
                    $resultarray2[1]["data"][]=$temparray;
                    #$resultarray['complement effective rate'][$row['bin_floor']]=$row['effective_rate'];
                }
            }
            if ($result2d->num_rows >=1) {
                #$cumucount = 0;
                foreach ($result2d as $row) {
                    #$cumucount++;
                    $temparray=array();
                    $binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $resultarray['2d length'][$binfloor]=$row['meanlength'];
                    $resultarray2[2]["name"]='2d length';
                    array_push($temparray,$binfloor);
                    array_push($temparray,floatval($row['meanlength']));
                    $resultarray2[2]["data"][]=$temparray;
                    #$resultarray['complement effective rate'][$row['bin_floor']]=$row['effective_rate'];
                }
            }
            if (isset($resultpretemp->num_rows)){
            if ($resultpretemp->num_rows >=1) {
                #$cumucount = 0;
                foreach ($resultpretemp as $row) {
                    #$cumucount++;
                    $temparray=array();
                    $resultarray['Raw Template length'][$row['bin_floor']]=$row['meanlength'];
                    $resultarray2[3]["name"]='Raw Template length';
                    array_push($temparray,$binfloor);
                    array_push($temparray,floatval($row['meanlength']));
                    $resultarray2[3]["data"][]=$temparray;
                    #$resultarray['complement effective rate'][$row['bin_floor']]=$row['effective_rate'];
                }
            }
            }
            if (isset($resultprecomp->num_rows)){
            if ($resultprecomp->num_rows >=1) {
                #$cumucount = 0;
                foreach ($resultprecomp as $row) {
                    #$cumucount++;
                    $temparray=array();
                    $resultarray['Raw Complement length'][$row['bin_floor']]=$row['meanlength'];
                    $resultarray2[4]["name"]='Raw Complement length';
                    array_push($temparray,$binfloor);
                    array_push($temparray,floatval($row['meanlength']));
                    $resultarray2[4]["data"][]=$temparray;
                    #$resultarray['complement effective rate'][$row['bin_floor']]=$row['effective_rate'];
                }
            }
        }
            //var_dump(json_encode($resultarray2));
            //echo "Go Through Previous Array\n";

            /*
            foreach ($jsonstringarray as $index=>$value){
                echo gettype($index) . "\n";
                echo $index . "\n";
                echo gettype($value) . "\n";
                foreach ($value->data as &$key4){
                    echo $key4[0] . "\t". $key4[1] . "\n";
                    echo "Lookup this value in the original array\n";
                }
            }*/

            //echo "Going through result array.\n";
            foreach ($resultarray2 as $index=>$value){
                //echo $index . "\n";
                foreach ($resultarray2[$index]["data"] as &$key2){
                //    echo $key2[0] . "\t" . $key2[1] . "\t";
                //    echo "Lookup this value in the original array\n";
                    $match = 0;
                    //echo gettype($jsonstringarray[$index]->name);
                    foreach ($jsonstringarray[$index]->data as &$key3){
                        if ($key3[0]==$key2[0]){
                //            echo "We got a match";
                            $match = 1;
                            #$jsonstringarray[0]->data[$key3][0]=$key2[0];
                            $key3[1]=$key2[1];
            //                    echo "reset value " . $key3[1];
                        }
        //                echo "\t original value " . $key2[1] . "\n";
                    }
                    if ($match == 0) {
                        //echo "wanna push \n";
                        $jsonstringarray[$index]->name= $resultarray2[$index]["name"];
                        $jsonstringarray[$index]->data[]= array($key2[0],$key2[1]);
                        usort($jsonstringarray[$index]->data, 'compare_startime');

                        //echo gettype(array($key2[0],$key2[1]));
                    }else{
                    //echo "no push\n";
                }
            }
        }


            //var_dump($jsonstringarray);
            //var_dump(json_encode(array_merge($jsonstringarray,$resultarray2)));

            #$jsonstring = json_encode($resultarray2);
            $jsonstring = json_encode($jsonstringarray);
            #if ($_GET["prev"] == 1){
    		include 'savejson.php';
    		#}
        }
        //Now we store the value we are after in the memcache holder for the value with no time out (so it persists):
        $memcache->set("$storedvalue",$jsonstring,0,10);
        //Now we store the flag to report the data is updated
        $memcache->set("$flagstate","True",0,1); //Note this will expire after 1 seconds.
        //Finally we set the running flag to false
        $memcache->set("$runstate", "False",0,0);

    }
    return $jsonstring;
}

##### ratiopassfail

function ratiopassfail($jobname,$currun) {
	$checkvar = $currun . $jobname;
	//echo $type . "\n";
	$checkrunning = $currun . $jobname . "status";
	global $memcache;
	global $mindb_connection;
	global $reflength;
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
		//$memcache->set("$checkrunning", "YES", 0, 0);
        if (strlen($jsonstring) <= 1){
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if (is_object($checking) && $checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			//do something interesting here...
            //echo "badger";
			#$sqltotalcount = "select ((basecalled_template.10minwin*10*60)+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join tracking_id using (basename_id) group by 2,1 order by 2,1;";
            //$sqltotalcount = "select 10minwin,exp_start_time, count(*) as count from basecalled_template group by 2,1 order by 2,1;";
            $sqltotalcount = "select 1minwin, readcount as count from basecalled_template_1minwin_sum where bases !='Null' and readcount >=0  order by 1;";
            #$sqltemplate = "select ((basecalled_template.10minwin*10*60)+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join tracking_id using (basename_id) where pass = 1 group by 2,1 order by 2,1;";

            //$sqltemplate = "select 10minwin,exp_start_time, count(*) as count from basecalled_template where pass = 1 group by 2,1 order by 2,1;";
            $sqltemplate = "select 1minwin, passcount as count from basecalled_template_1minwin_sum order by 1;";

            $sqlcomplement = "select 1minwin, passcount as count from basecalled_complement_1minwin_sum where bases!='Null' order by 1;";
			#$sql2d = "select ((basecalled_template.10minwin*10*60)+exp_start_time)*1000 as bin_floor, count(*) as count from basecalled_template inner join basecalled_2d using (basename_id) inner join tracking_id using (basename_id) where pass = 1 group by 2,1 order by 2,1;";
			#$sql2d="select 10minwin,exp_start_time, count(*) as count from basecalled_2d where pass = 1 group by 2,1 order by 2,1;";
            $sql2d = "select 1minwin, passcount as count from basecalled_2d_1minwin_sum where bases!='Null' order by 1;";
            //$sqltemplate2 = "select 10minwin,exp_start_time, count(*) as count from basecalled_template where pass = 0 group by 2,1 order by 2,1;";
            $sqltemplate2 = "select 1minwin, (readcount-passcount) as count from basecalled_template_1minwin_sum where bases !='Null' and readcount >= 0 order by 1;";
            $sqlcomplement2 = "select 1minwin, (readcount-passcount) as count from basecalled_complement_1minwin_sum where bases!='Null' and readcount >=0 order by 1;";
			#$sql2d2 = "select 10minwin,exp_start_time, count(*) as count from basecalled_2d where pass = 0 group by 2,1 order by 2,1;";
            $sql2d2 = "select 1minwin, (readcount-passcount) as count from basecalled_2d_1minwin_sum where bases!='Null' order by 1;";
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
                    #$binfloor = ($row['1minwin']*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
                    //echo $row['count'];
					$totalarray[$binfloor]=$row['count'];

				}
			}

			$resultarray=array();

			if ($resulttemplate->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resulttemplate as $row) {
					#$cumucount++;
                    #$binfloor = ($row['1minwin']*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
                    if (!is_nan($row['count']/$totalarray[$binfloor]*100)){
                        $resultarray['template pass'][$binfloor]=$row['count']/$totalarray[$binfloor]*100;
                    }

                    //echo $row['count']/$totalarray[$binfloor]*100 . "<br>";
                    //echo $row['count'];
				}
			}
			if ($resultcomplement->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resultcomplement as $row) {
					#$cumucount++;
                    #$binfloor = ($row['1minwin']*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
                    if (!is_nan($row['count']/$totalarray[$binfloor]*100)){
					    $resultarray['complement pass'][$binfloor]=$row['count']/$totalarray[$binfloor]*100;
                    }
				}
			}
			if ($result2d->num_rows >=1) {
				#$cumucount = 0;
				foreach ($result2d as $row) {
					#$cumucount++;
                    #$binfloor = ($row['1minwin']*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
                    if (!is_nan($row['count']/$totalarray[$binfloor]*100)){
					    $resultarray['2d pass'][$binfloor]=$row['count']/$totalarray[$binfloor]*100;
                    }
				}
			}
			if ($resulttemplate2->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resulttemplate2 as $row) {
					#$cumucount++;
                    #$binfloor = ($row['1minwin']*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
                    if (!is_nan($row['count']/$totalarray[$binfloor]*100)){
					    $resultarray['template fail'][$binfloor]=$row['count']/$totalarray[$binfloor]*100;
                    }

				}
			}
			if ($resultcomplement2->num_rows >=1) {
				#$cumucount = 0;
				foreach ($resultcomplement2 as $row) {
					#$cumucount++;
                    #$binfloor = ($row['1minwin']*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
                    if (!is_nan($row['count']/$totalarray[$binfloor]*100)){
					    $resultarray['complement fail'][$binfloor]=$row['count']/$totalarray[$binfloor]*100;
                    }
				}
			}
			if ($result2d2->num_rows >=1) {
				#$cumucount = 0;
				foreach ($result2d2 as $row) {
					#$cumucount++;
                    #$binfloor = ($row['1minwin']*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
                    if (!is_nan($row['count']/$totalarray[$binfloor]*100)){
					    $resultarray['2d fail'][$binfloor]=$row['count']/$totalarray[$binfloor]*100;
                    }
				}
			}

		}
		//$resultarray=array();
		$jsonstring="";
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
			//include 'savejson.php';
		}
		$memcache->set("$checkvar", "$jsonstring",MEMCACHE_COMPRESSED,5);

	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
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
	$jsonstring = $memcache->get("$checkvar");
	$checkingrunning = $memcache->get("$checkrunning");
	if($checkingrunning === "No" || $checkingrunning === FALSE){
        if (strlen($jsonstring) <= 1){
		$memcache->set("$checkrunning", "YES", 0, 0);
		$checkrow = "select name,json from jsonstore where name = '" . $jobname . "' ;";
		$checking=$mindb_connection->query($checkrow);
		if (is_object($checking) && $checking->num_rows ==1){
			//echo "We have already run this!";
			foreach ($checking as $row){
				$jsonstring = $row['json'];
			}
		} else {
			$sql_template = "select 1minwin, readcount as count from basecalled_template_1minwin_sum where readcount != 'Null' order by 1 ;";

			$sql_complement = "select 1minwin, readcount as count from basecalled_complement_1minwin_sum where readcount != 'Null'  order by 1 ;";

			$resultarray=array();

			$template=$mindb_connection->query($sql_template);
			$complement=$mindb_connection->query($sql_complement);

			if ($template->num_rows >= 1){
				foreach ($template as $row) {
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
					$resultarray['template'][$binfloor]=$row['count'];

				}
			}

			if ($complement->num_rows >=1) {
				foreach ($complement as $row) {
                    #$binfloor = ($row['1minwin']*1*60+$row['exp_start_time'])*1000;
                    $binfloor = $row['1minwin']*60*1000;
                    settype($binfloor,"string");
					$resultarray['complement'][$binfloor]=$row['count'];
				}
			}
			//var_dump($resultarray);
			//echo json_encode($resultarray);
			$jsonstring="";
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
		$memcache->set("$checkvar", "$jsonstring",MEMCACHE_COMPRESSED,5);

	}else{
		$jsonstring = $memcache->get("$checkvar");
	}
}
	// cache for 2 minute as we want yield to update semi-regularly...

	         $memcache->delete("$checkrunning");
    return $jsonstring;
}

?>
