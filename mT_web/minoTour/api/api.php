<?php
session_start();
header('Content-Type: application/json');
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("../config/db.php");

// load the login class
require_once("../classes/Login.php");

// load the functions
require_once("../includes/functions.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    //We are logged in so we can make a connection to the memcache store
    // Connection creation
	$memcache = new Memcache;
	#$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT) or die ("Memcached Failure");
	$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);



    function checkmemstore($dbname,$testtype,$ref,$memcache,$array){
        // A simple funtion to test if a given requested value is present in the memcache store or needs recalculating.
        // Takes in the database name and the name of the data type to check
        //Psuedocode
        //Check if the testype exists in memcache.
        //If it does exist, return 0 (nothing to do). If it doesn't exist or has timed out, return (1) - job to do.
        //On the assumption that we now will be calculating this value, we set a flag to say its being calculated.
        //This function will also return the data if it is calculated and able to do so.
        #echo "Checking running \n";
        //Timeout test:
        $timeouttestname = md5($dbname.$testtype.$ref."timeout");
        $checkrun = md5($dbname.$testtype.$ref."checkrun");
        $datastore = md5($dbname.$testtype.$ref."store");

        $checkingrunning = $memcache->get($checkrun);
        $timeouttest = $memcache->get($timeouttestname);


        $returnval = 0;

        #echo "Checkingrunning " .  $checkingrunning . "\n";
        #echo "Timeouttest " .  $timeouttest . "\n";
        if(($timeouttest === "No" || $timeouttest === FALSE) && ($checkingrunning === "No" || $checkingrunning === FALSE) ){
            $returnval = 1;
            $memcache->set($checkrun, "True",0,0);

        }else{
            //echo $returnval . "\n";
            $array = $memcache->get($datastore);
        }
        return array($returnval,$array);
    }

    function setmemstore($dbname,$testtype,$ref,$memcache,$array){
        // A simple function to store a value in memcache for long term storage.
        // dbname is database name
        // testtype is the query that is running
        // reference is a link to define the reference name
        // array is the value or values to store in the memcache data store
        // Will set three values:
        // First being the dataset which will have a long time out value approx 60 seconds
        // The second is the timer which will instruct the code to recalculate the value - typically every 5 seconds
        $recalculationtime = 5;
        $timeouttestname = md5($dbname.$testtype.$ref."timeout");
        $checkrun = md5($dbname.$testtype.$ref."checkrun");
        $datastore = md5($dbname.$testtype.$ref."store");
        #echo $datastore;

        $memcache->set($datastore,$array,MEMCACHE_COMPRESSED,0);
        $_SESSION[$datastore]=$array;

        //Now we store the flag to report the data is updated
        $memcache->set($timeouttestname,"True",0,$recalculationtime); //Note this will expire after 5 seconds.
        $memcache->set($checkrun,"True",0,$recalculationtime);



    }



    // create a login object. when this object is created, it will do all login/logout stuff automatically
    // so this single line handles the entire login process. in consequence, you can simply ...
    $login = new Login();

    // ... ask if we are logged in here:
    if ($login->isUserLoggedIn() == true) {
        $mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
        $currun = $_SESSION['active_run_name'];
        #echo "Welcome To Matt's API\n";
        #echo "Going off to fetch read counts in 1minwin.\n";
        #echo "Working on " . $currun . "\n";

        //Array to hold all the data
        $resultarray=array();

        //Collect some summary information that is important to store:

        //This is a holder specific to a reference if we are using it. By default set as null.

        //Collecting the starttime values from the database
        $jobname = "starttimes";
        $ref = "";
        $resultstore=array();
        list($runtest,$resultstore) = checkmemstore($_SESSION['active_run_name'],$jobname,$ref,$memcache,$resultstore);
        #echo $runtest . "\n";
        #echo $resultstore . "\n";
        if ($runtest==1){
            //Get exp_start_time
            $start_time_queries = "select count(*) as count, device_id, exp_script_purpose,exp_start_time,run_id,version_name from tracking_id group by run_id order by exp_start_time;";

            //$start_time_queries = "select exp_start_time from tracking_id group by exp_start_time;";
            $starttime=$mindb_connection->query($start_time_queries);
            if ($starttime->num_rows>=1){
                foreach ($starttime as $row){
                    //$resultarray["summaryinfo"]["exp_start_time"][]=$row['exp_start_time'];
                    $resultstore["BC"][$row['exp_start_time']]["count"]=$row['count'];
                    $resultstore["BC"][$row['exp_start_time']]["device_id"]=$row['device_id'];
                    $resultstore["BC"][$row['exp_start_time']]["exp_script_purpose"]=$row['exp_script_purpose'];
                    $resultstore["BC"][$row['exp_start_time']]["run_id"]=$row['run_id'];
                    $resultstore["BC"][$row['exp_start_time']]["version_name"]=$row['version_name'];
                    $resultstore["BC"][$row['exp_start_time']]["exp_start_time"]=$row['exp_start_time'];
                }
            }
            $pre_start_time_queries = "select count(*) as count,device_id, exp_script_purpose,exp_start_time,run_id,version_name from pre_tracking_id group by run_id order by exp_start_time;";
            $starttime=$mindb_connection->query($pre_start_time_queries);
            if ($starttime->num_rows>=1){
                foreach ($starttime as $row){
                    //$resultarray["summaryinfo"]["exp_start_time"][]=$row['exp_start_time'];
                    $resultstore["R"][$row['exp_start_time']]["count"]=$row['count'];
                    $resultstore["R"][$row['exp_start_time']]["device_id"]=$row['device_id'];
                    $resultstore["R"][$row['exp_start_time']]["exp_script_purpose"]=$row['exp_script_purpose'];
                    $resultstore["R"][$row['exp_start_time']]["run_id"]=$row['run_id'];
                    $resultstore["R"][$row['exp_start_time']]["version_name"]=$row['version_name'];
                    $resultstore["R"][$row['exp_start_time']]["exp_start_time"]=$row['exp_start_time'];
                }
            }
            setmemstore($_SESSION['active_run_name'],$jobname,$ref,$memcache,$resultstore);
        }
        //var_dump($resultstore);


        //Collecting read number stats from the database.

        //Collecting the starttime values from the database
        $jobname = "readnumberstats";
        $ref = "";
        $resultstore=array();
        list($runtest,$resultstore) = checkmemstore($_SESSION['active_run_name'],$jobname,$ref,$memcache,$resultstore);
        #echo $runtest . "\n";
        #echo $resultstore . "\n";
        $limiter = "";
        if ($runtest==1){
            //Get exp_start_time
            $sql_config_general = "select 1minwin,count(*) as readnum from config_general group by 1 order by 1 desc ".$limiter.";";
            //MIGHT NEED A PRE BASECALLED ONE HERE?
            $configgeneral=$mindb_connection->query($sql_config_general);
            if ($configgeneral->num_rows >= 1){
                foreach ($configgeneral as $row){
                    $resultstore["uploaded"]['count'][$row['1minwin']]=$row['readnum'];
                }
            }

            $sql_template = "select 1minwin,count(*) as readnum from basecalled_template inner join tracking_id using (basename_id) group by 1 order by 1 desc".$limiter.";";
            $sql_complement = "select 1minwin,count(*) as readnum from basecalled_complement inner join tracking_id using (basename_id) group by 1 order by 1 desc".$limiter.";";
            $sql_2d = "select 1minwin,count(*) as readnum from basecalled_2d inner join tracking_id using (basename_id) group by 1 order by 1 desc".$limiter.";";
            $pre_template = "SELECT count(*) as readnum FROM pre_tracking_id group by 1 order by 1 desc".$limiter.";";
            $pre_complement = "SELECT count(*) as readnum FROM pre_tracking_id where hairpin_found = 1 group by 1 order by 1 desc".$limiter.";";


            $template=$mindb_connection->query($sql_template);
            $complement=$mindb_connection->query($sql_complement);
            $read2d=$mindb_connection->query($sql_2d);
            $pretemplate=$mindb_connection->query($pre_template);
            $precomplement=$mindb_connection->query($pre_complement);

            if ($template->num_rows >= 1){
                foreach ($template as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['template'][$row['1minwin']]=$row['readnum'];
                    $resultstore['template']['count'][$row['1minwin']]=$row['readnum'];
                }
            }
            if ($complement->num_rows >= 1){
                foreach ($complement as $row) {
                    //$resultstore["readcounts"][$row['exp_script_purpose']]['complement'][$row['1minwin']]=$row['readnum'];
                    $resultstore['complement']['count'][$row['1minwin']]=$row['readnum'];
                }
            }
            if ($read2d->num_rows >= 1){
                foreach ($read2d as $row) {
                    //$resultstore["readcounts"][$row['exp_script_purpose']]['2d'][$row['1minwin']]=$row['readnum'];
                    $resultstore['2d']['count'][$row['1minwin']]=$row['readnum'];
                }
            }
            if ($pretemplate->num_rows >= 1){
                foreach ($pretemplate as $row) {
                    //$resultstore["readcounts"][$row['exp_script_purpose']]['Raw Template']=$row['readnum'];
                    $resultstore['Raw Template']['count'][$row['1minwin']]=$row['readnum'];
                }
            }
            if ($precomplement->num_rows >= 1){
                foreach ($precomplement as $row) {
                    //$resultstore["readcounts"][$row['exp_script_purpose']]['Raw Complement']=$row['readnum'];
                    $resultstore['Raw Complement']['count'][$row['1minwin']]=$row['readnum'];
                }
            }

            $sql_template = "SELECT 1minwin,sum(seqlen) as bases, max(seqlen) as maxlen,min(seqlen) as minlen FROM basecalled_template group by 1 order by 1 desc".$limiter.";";
            $sql_complement = "SELECT 1minwin,sum(seqlen) as bases,max(seqlen) as maxlen,min(seqlen) as minlen FROM basecalled_complement group by 1 order by 1 desc".$limiter.";";
            $sql_2d = "SELECT 1minwin,sum(seqlen) as bases,max(seqlen) as maxlen,min(seqlen) as minlen FROM basecalled_2d group by 1 order by 1 desc".$limiter.";";
            $pre_template="SELECT 1minwin,sum(case when pre_config_general.hairpin_found=1 then hairpin_event_index else total_events end) as events,max(case when pre_config_general.hairpin_found=1 then hairpin_event_index else total_events end) as maxevents,min(case when pre_config_general.hairpin_found=1 then hairpin_event_index else total_events end) as minevents FROM pre_config_general  group by 1 order by 1 desc".$limiter.";";
            $pre_complement = "SELECT 1minwin,sum(total_events-hairpin_event_index) as events, max(total_events-hairpin_event_index) as maxevents, min(total_events-hairpin_event_index) as minevents FROM pre_config_general where pre_config_general.hairpin_found = 1 group by 1 order by 1 desc".$limiter.";";


            $template=$mindb_connection->query($sql_template);
            $complement=$mindb_connection->query($sql_complement);
            $read2d=$mindb_connection->query($sql_2d);
            $pretemplate=$mindb_connection->query($pre_template);
            $precomplement=$mindb_connection->query($pre_complement);

            if ($template->num_rows >= 1){
                foreach ($template as $row) {
                    //$resultarray["lengthsum"]['template'][$row['1minwin']]=$row['bases'];
                    $resultstore['template']['lengthsum'][$row['1minwin']]=$row['bases'];
                    $resultstore['template']['max'][$row['1minwin']]=$row['maxlen'];
                    $resultstore['template']['min'][$row['1minwin']]=$row['minlen'];
                    //$resultstore['template']['std'][$row['1minwin']]=$row['stdlen'];
                }
            }
            if ($complement->num_rows >= 1){
                foreach ($complement as $row) {
                    //$resultarray["lengthsum"]['complement'][$row['1minwin']]=$row['bases'];
                    $resultstore['complement']['lengthsum'][$row['1minwin']]=$row['bases'];
                    $resultstore['complement']['max'][$row['1minwin']]=$row['maxlen'];
                    $resultstore['complement']['min'][$row['1minwin']]=$row['minlen'];
                    //$resultstore['complement']['std'][$row['1minwin']]=$row['stdlen'];
                }
            }
            if ($read2d->num_rows >= 1){
                foreach ($read2d as $row) {
                    //$resultarray["lengthsum"]['2d'][$row['1minwin']]=$row['bases'];
                    $resultstore['2d']['lengthsum'][$row['1minwin']]=$row['bases'];
                    $resultstore['2d']['max'][$row['1minwin']]=$row['maxlen'];
                    $resultstore['2d']['min'][$row['1minwin']]=$row['minlen'];
                    //$resultstore['2d']['std'][$row['1minwin']]=$row['stdlen'];
                }
            }

            if ($pretemplate->num_rows >= 1){
                foreach ($pretemplate as $row) {
                    //$resultarray["lengthsum"]['Raw Template']=$row['events'];
                    $resultstore['Raw Template']['lengthsum'][$row['1minwin']]=$row['events'];
                    $resultstore['Raw Template']['max'][$row['1minwin']]=$row['maxevents'];
                    $resultstore['Raw Template']['min'][$row['1minwin']]=$row['minevents'];
                    //$resultstore['Raw Template']['std'][$row['1minwin']]=$row['stdevents'];
                }
            }
            if ($precomplement->num_rows >= 1){
                foreach ($precomplement as $row) {
                    //$resultarray["lengthsum"]['Raw Complement']=$row['events'];
                    $resultstore['Raw Complement']['lengthsum'][$row['1minwin']]=$row['events'];
                    $resultstore['Raw Complement']['max'][$row['1minwin']]=$row['maxevents'];
                    $resultstore['Raw Complement']['min'][$row['1minwin']]=$row['minevents'];
                    //$resultstore['Raw Complement']['std'][$row['1minwin']]=$row['stdevents'];
                }
            }
            //Adding aligned counts
            $sql_template = "select 1minwin,count(*) as readnum from basecalled_template where align = 1 group by 1 order by 1 desc".$limiter.";";
            $sql_complement = "select 1minwin,count(*) as readnum from basecalled_complement where align = 1 group by 1 order by 1 desc".$limiter.";";
            $sql_2d = "select 1minwin,count(*) as readnum from basecalled_2d where align = 1 group by 1 order by 1 desc".$limiter.";";
            $pre_template = "SELECT count(*) as readnum FROM pre_tracking_id where align = 1 group by 1 order by 1 desc".$limiter.";";
            $pre_complement = "SELECT count(*) as readnum FROM pre_tracking_id where hairpin_found = 1 and align = 1 group by 1 order by 1 desc".$limiter.";";


            $template=$mindb_connection->query($sql_template);
            $complement=$mindb_connection->query($sql_complement);
            $read2d=$mindb_connection->query($sql_2d);
            $pretemplate=$mindb_connection->query($pre_template);
            $precomplement=$mindb_connection->query($pre_complement);

            if ($template->num_rows >= 1){
                foreach ($template as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['template'][$row['1minwin']]=$row['readnum'];
                    $resultstore['template']['countalign'][$row['1minwin']]=$row['readnum'];
                }
            }
            if ($complement->num_rows >= 1){
                foreach ($complement as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['complement'][$row['1minwin']]=$row['readnum'];
                    $resultstore['complement']['countalign'][$row['1minwin']]=$row['readnum'];
                }
            }
            if ($read2d->num_rows >= 1){
                foreach ($read2d as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['2d'][$row['1minwin']]=$row['readnum'];
                    $resultstore['2d']['countalign'][$row['1minwin']]=$row['readnum'];
                }
            }
            if ($pretemplate->num_rows >= 1){
                foreach ($pretemplate as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['Raw Template']=$row['readnum'];
                    $resultstore['Raw Template']['countalign'][$row['1minwin']]=$row['readnum'];
                }
            }
            if ($precomplement->num_rows >= 1){
                foreach ($precomplement as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['Raw Complement']=$row['readnum'];
                    $resultstore['Raw Complement']['countalign'][$row['1minwin']]=$row['readnum'];
                }
            }
            //Getting pass counts
            $sql_template = "select 1minwin,count(*) as readnum from basecalled_template where pass = 1 group by 1 order by 1 desc".$limiter.";";
            $sql_complement = "select 1minwin,count(*) as readnum from basecalled_complement where pass = 1 group by 1 order by 1 desc".$limiter.";";
            $sql_2d = "select 1minwin,count(*) as readnum from basecalled_2d where pass = 1 group by 1 order by 1 desc".$limiter.";";
            $template=$mindb_connection->query($sql_template);
            $complement=$mindb_connection->query($sql_complement);
            $read2d=$mindb_connection->query($sql_2d);

            if ($template->num_rows >= 1){
                foreach ($template as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['template'][$row['1minwin']]=$row['readnum'];
                    $resultstore['template']['countpass'][$row['1minwin']]=$row['readnum'];
                    $resultstore['template']['countfail'][$row['1minwin']]=$resultstore['template']['count'][$row['1minwin']]-$row['readnum'];
                    $resultstore['template']["proppass"][$row['1minwin']]=$row['readnum']/$resultstore['template']['count'][$row['1minwin']]*100;
                    $resultstore['template']["propfail"][$row['1minwin']]=$resultstore['template']['countfail'][$row['1minwin']]/$resultstore['template']['count'][$row['1minwin']]*100;

                }
            }
            if ($complement->num_rows >= 1){
                foreach ($complement as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['complement'][$row['1minwin']]=$row['readnum'];
                    $resultstore['complement']['countpass'][$row['1minwin']]=$row['readnum'];
                    $resultstore['complement']['countfail'][$row['1minwin']]=$resultstore['complement']['count'][$row['1minwin']]-$row['readnum'];
                    $resultstore['complement']["proppass"][$row['1minwin']]=$row['readnum']/$resultstore['complement']['count'][$row['1minwin']]*100;
                    $resultstore['complement']["propfail"][$row['1minwin']]=$resultstore['complement']['countfail'][$row['1minwin']]/$resultstore['complement']['count'][$row['1minwin']]*100;
                }
            }
            if ($read2d->num_rows >= 1){
                foreach ($read2d as $row) {
                    //$resultarray["readcounts"][$row['exp_script_purpose']]['2d'][$row['1minwin']]=$row['readnum'];
                    $resultstore['2d']['countpass'][$row['1minwin']]=$row['readnum'];
                    $resultstore['2d']['countfail'][$row['1minwin']]=$resultstore['2d']['count'][$row['1minwin']]-$row['readnum'];
                    $resultstore['2d']["proppass"][$row['1minwin']]=$row['readnum']/$resultstore['2d']['count'][$row['1minwin']]*100;
                    $resultstore['2d']["propfail"][$row['1minwin']]=$resultstore['2d']['countfail'][$row['1minwin']]/$resultstore['2d']['count'][$row['1minwin']]*100;
                }
            }

            setmemstore($_SESSION['active_run_name'],$jobname,$ref,$memcache,$resultstore);
        }

        //Now we are going to convert previously grabbed values to sums and store them.

        //DO NOT SEPERATE THESE


        //Collecting the starttime values from the database
        $jobname = "summarystats";
        $ref = "";
        $resultarray = $resultstore;
        $resultstore=array();
        list($runtest,$resultstore) = checkmemstore($_SESSION['active_run_name'],$jobname,$ref,$memcache,$resultstore);
        #echo $runtest . "\n";
        #echo $resultstore . "\n";
        $limiter = "";
        if ($runtest==1){
            //var_dump($resultarray);
            foreach ($resultarray as $key=>$value){
                $count = array_sum($resultarray[$key]["count"]);
                $align = array_sum($resultarray[$key]["countalign"]);
                $yield = array_sum($resultarray[$key]["lengthsum"]);
                if (isset($_SESSION['activereflength'])){
                    $resultstore["est_cov"][$key]=array_sum($resultarray[$key]["lengthsum"])/intval($_SESSION['activereflength']);
                }else{
                    $resultstore["est_cov"][$key]=0;
                    //intval($_SESSION['activereflen']);
                }
                $pass = array_sum($resultarray[$key]["countpass"]);
                $fail = array_sum($resultarray[$key]["countfail"]);
                $max = max($resultarray[$key]["max"]);
                $min = min($resultarray[$key]["min"]);
                $resultstore["totalcount"][$key]=$count;
                $resultstore["totalyield"][$key]=$yield;
                $resultstore["totalalign"][$key]=$align;
                $resultstore["totalpass"][$key]=$pass;
                $resultstore["totalfail"][$key]=$fail;
                $resultstore["avglen"][$key]=$yield/$count;
                $resultstore["maxlen"][$key]=$max;
                $resultstore["minlen"][$key]=$min;
            }

            $sql_template = "SELECT std(seqlen) as std FROM basecalled_template;";
            $sql_complement = "SELECT std(seqlen) as std FROM basecalled_complement;";
            $sql_2d = "SELECT std(seqlen) as std FROM basecalled_2d;";
            $pre_template = "SELECT std(case when pre_config_general.hairpin_found=1 then hairpin_event_index else total_events end) as std FROM pre_config_general;";
            $pre_complement = "SELECT std(total_events-hairpin_event_index) as std FROM pre_config_general where pre_config_general.hairpin_found = 1;";
            $template=$mindb_connection->query($sql_template);
            $complement=$mindb_connection->query($sql_complement);
            $read2d=$mindb_connection->query($sql_2d);
            $pretemp=$mindb_connection->query($pre_template);
            $precomp=$mindb_connection->query($pre_complement);
            $val = mysqli_fetch_array($template);
            $resultstore["std"]["template"]=$val[0];
            $val = mysqli_fetch_array($complement);
            $resultstore["std"]["complement"]=$val[0];
            $val = mysqli_fetch_array($read2d);
            $resultstore["std"]["2d"]=$val[0];
            $val = mysqli_fetch_array($pretemp);
            $resultstore["std"]["Raw Template"]=$val[0];
            $val = mysqli_fetch_array($precomp);
            $resultstore["std"]["Raw Complement"]=$val[0];

            #The number of reads which have been processed:
    		$sql_template = "select count(*) as processed from read_tracking_template;";
    		$sql_complement = "select count(*) as processed from read_tracking_complement;";
    		$sql_2d = "select count(*) as processed from read_tracking_2d;";

    		$template=$mindb_connection->query($sql_template);
    		$complement=$mindb_connection->query($sql_complement);
    		$read2d=$mindb_connection->query($sql_2d);
            $val = mysqli_fetch_array($template);
            $resultstore["processed"]["template"]=$val[0];
            $val = mysqli_fetch_array($complement);
            $resultstore["processed"]["complement"]=$val[0];
            $val = mysqli_fetch_array($read2d);
            $resultstore["processed"]["2d"]=$val[0];


            //Get median, 1st and 3rd quartile values for box plots

            $sqltempquartiles = "select (select floor(count(*)/4) from basecalled_template) as first_q, (select floor(count(*)/2) from basecalled_template) as mid_pos, (select floor(count(*)/4*3) from basecalled_template) as third_q from basecalled_template order by seqlen limit 1;";
			$sqlcompquartiles = "select (select floor(count(*)/4) from basecalled_complement) as first_q, (select floor(count(*)/2) from basecalled_complement) as mid_pos, (select floor(count(*)/4*3) from basecalled_complement) as third_q from basecalled_complement order by seqlen limit 1;";
			$sql2dquartiles = "select (select floor(count(*)/4) from basecalled_2d) as first_q, (select floor(count(*)/2) from basecalled_2d) as mid_pos, (select floor(count(*)/4*3) from basecalled_2d) as third_q from basecalled_2d order by seqlen limit 1;";
			$pretempquartiles = "select (select floor(count(*)/4) from pre_config_general) as first_q, (select floor(count(*)/2) from pre_config_general) as mid_pos, (select floor(count(*)/4*3) from pre_config_general) as third_q from pre_config_general order by hairpin_event_index limit 1;";
			$precompquartiles = "select (select floor(count(*)/4) from pre_config_general where hairpin_found = 1) as first_q, (select floor(count(*)/2) from pre_config_general where hairpin_found = 1) as mid_pos, (select floor(count(*)/4*3) from pre_config_general where hairpin_found = 1) as third_q from pre_config_general where hairpin_found = 1 order by (total_events-hairpin_event_index) limit 1;";
            $resulttempquartiles = $mindb_connection->query($sqltempquartiles);
			$resultcompquartiles = $mindb_connection->query($sqlcompquartiles);
			$result2dquartiles = $mindb_connection->query($sql2dquartiles);
			$resultpretempquartiles = $mindb_connection->query($pretempquartiles);
			$resultprecompquartiles = $mindb_connection->query($precompquartiles);
            $variablearray=array();
            $val = mysqli_fetch_array($resulttempquartiles);
            $variablearray["template"]["first_q"]=$val[0];
            $variablearray["template"]["mid_pos"]=$val[1];
            $variablearray["template"]["third_q"]=$val[2];
            $val = mysqli_fetch_array($resultcompquartiles);
            $variablearray["complement"]["first_q"]=$val[0];
            $variablearray["complement"]["mid_pos"]=$val[1];
            $variablearray["complement"]["third_q"]=$val[2];
            $val = mysqli_fetch_array($result2dquartiles);
            $variablearray["2d"]["first_q"]=$val[0];
            $variablearray["2d"]["mid_pos"]=$val[1];
            $variablearray["2d"]["third_q"]=$val[2];
            $val = mysqli_fetch_array($resultpretempquartiles);
            $variablearray["Raw Template"]["first_q"]=$val[0];
            $variablearray["Raw Template"]["mid_pos"]=$val[1];
            $variablearray["Raw Template"]["third_q"]=$val[2];
            $val = mysqli_fetch_array($resultprecompquartiles);
            $variablearray["Raw Complement"]["first_q"]=$val[0];
            $variablearray["Raw Complement"]["mid_pos"]=$val[1];
            $variablearray["Raw Complement"]["third_q"]=$val[2];

            $sqltemp = "select (select seqlen from basecalled_template order by seqlen limit " .$variablearray['template']['first_q'] . ",1) as firstq, (select seqlen from basecalled_template order by seqlen limit ".$variablearray['template']['mid_pos'].",1) as median, (select seqlen from basecalled_template order by seqlen limit ".$variablearray['template']['third_q'].",1) as lastq from basecalled_template;";
			$sqlcomp = "select (select seqlen from basecalled_complement order by seqlen limit " .$variablearray['complement']['first_q'] . ",1) as firstq, (select seqlen from basecalled_complement order by seqlen limit ".$variablearray['complement']['mid_pos'].",1) as median, (select seqlen from basecalled_complement order by seqlen limit ".$variablearray['complement']['third_q'].",1) as lastq from basecalled_complement;";
			$sql2d = "select (select seqlen from basecalled_2d order by seqlen limit " .$variablearray['2d']['first_q'] . ",1) as firstq, (select seqlen from basecalled_2d order by seqlen limit ".$variablearray['2d']['mid_pos'].",1) as median, (select seqlen from basecalled_2d order by seqlen limit ".$variablearray['2d']['third_q'].",1) as lastq from basecalled_2d;";
			$pretemp = "select (select hairpin_event_index from pre_config_general order by hairpin_event_index limit " .$variablearray['Raw Template']['first_q'] . ",1) as firstq, (select hairpin_event_index from pre_config_general order by hairpin_event_index limit ".$variablearray['Raw Template']['mid_pos'].",1) as median, (select hairpin_event_index from pre_config_general order by hairpin_event_index limit ".$variablearray['Raw Template']['third_q'].",1) as lastq from pre_config_general;";
			$precomp = "select (select (total_events-hairpin_event_index) from pre_config_general where hairpin_found = 1 order by (total_events-hairpin_event_index) limit " .$variablearray['Raw Complement']['first_q'] . ",1) as firstq, (select (total_events-hairpin_event_index) from pre_config_general where hairpin_found = 1 order by (total_events-hairpin_event_index) limit ".$variablearray['Raw Complement']['mid_pos'].",1) as median, (select (total_events-hairpin_event_index) from pre_config_general where hairpin_found = 1 order by (total_events-hairpin_event_index) limit ".$variablearray['Raw Complement']['third_q'].",1) as lastq from pre_config_general where hairpin_found = 1;";
            #echo "<br>";
            #echo $sqltemp . "<br>";
            $resultsqltemp = $mindb_connection->query($sqltemp);
			$resultsqlcomp = $mindb_connection->query($sqlcomp);
			$resultsql2d = $mindb_connection->query($sql2d);
			$resultpretemp = $mindb_connection->query($pretemp);
			$resultprecomp = $mindb_connection->query($precomp);

            $val = mysqli_fetch_array($resultsqltemp);
            $resultstore["first_q"]["template"]=$val[0];
            $resultstore["median"]["template"]=$val[1];
            $resultstore["third_q"]["template"]=$val[2];
            $val = mysqli_fetch_array($resultsqlcomp);
            $resultstore["first_q"]["complement"]=$val[0];
            $resultstore["median"]["complement"]=$val[1];
            $resultstore["third_q"]["complement"]=$val[2];
            $val = mysqli_fetch_array($resultsql2d);
            $resultstore["first_q"]["2d"]=$val[0];
            $resultstore["median"]["2d"]=$val[1];
            $resultstore["third_q"]["2d"]=$val[2];
            $val = mysqli_fetch_array($resultpretemp);
            $resultstore["first_q"]["Raw Template"]=$val[0];
            $resultstore["median"]["Raw Template"]=$val[1];
            $resultstore["third_q"]["Raw Template"]=$val[2];
            $val = mysqli_fetch_array($resultprecomp);
            $resultstore["first_q"]["Raw Complement"]=$val[0];
            $resultstore["median"]["Raw Complement"]=$val[1];
            $resultstore["third_q"]["Raw Complement"]=$val[2];



            setmemstore($_SESSION['active_run_name'],$jobname,$ref,$memcache,$resultstore);
        }

    //var_dump($resultstore);

    }else{
    	echo "ERROR";
    }
}else{

    	echo "ERROR";
}
?>
