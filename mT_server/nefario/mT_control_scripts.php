<?php
### A simple PHP parsing file to call the functions contained within jsonfunctions.php. 
### Parameters for this file are set in the file mT_param.conf which should be in the same folder as this file. 

$lines = file('mT_param.conf');
foreach ($lines as $line_num => $line) {
	$line = str_replace("\n", '', $line);
    $fragments = explode("=", $line);
    if ($fragments[0] == "directory") {
		$directory = $fragments[1];	
    }
}

require_once($directory . "includes/jsonfunctions.php");

// include the configs / constants for the database connection 
require_once($directory . "config/db.php");

error_reporting(0);

parse_str(implode('&', array_slice($argv, 1)), $_GET);


//As user is logged in, we can now look at the memcache to retrieve data from here and so reduce the load on the mySQL server
// Connection creation
$memcache = new Memcache;
$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);

$reflength=(string)$_GET['reflength'];

$jsonjobname=(string)$_GET['jobname'];
$currun = (string)$_GET['dbname'];

$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$currun);

$jsonstring=$jsonjobname($jsonjobname,$currun);
?>