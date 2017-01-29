<?php //Generate text file on the fly
 
header('Content-type: text/plain');
header('Content-Disposition: attachment; filename="minup_windows.config"');

?>

<?php

// include the configs / constants for the database connection
require_once("../config/db.php");

// load the login class
require_once("../classes/Login.php");

// load the functions
require_once("../includes/functions.php");

if (DB_HOST == "localhost" || DB_HOST == "127.0.0.1") {
	$SQLHOST = gethostname();
}else {
	$SQLHOST = DB_HOST;
}



print "# --> This file is unique for your access to the minoTour installation you downloaded it from.\r\n";
print "# --> Do not share this with other users. You can uncomment (remove the hash) from any of the lines below to enable the parameters to be loaded from the config file.\r\n";
print "# --> For minotour-sharing-usernames you can set a comma separated list of names e.g. minotour-sharing-usernames=demo1,demo2,demo3\r\n";
print "# --> All lines which are not commented out MUST have a value set.\r\n";
print "[Defaults]\r\n";
print "mysql-host=" . $SQLHOST . "\r\n";
print "mysql-username=". $_GET['user_name'] . "\r\n";
print "#mysql-password=\r\n";
print "mysql-port=3306\r\n";
print "#align-ref-fasta=\r\n";
print "#watch-dir=\r\n";
//print "aligning-threads=3\r\n";
print "minotour-username=" . $_GET['user_name']."\r\n";
print "#minotour-sharing-usernames=\r\n";
print "flowcell-owner=" . $_GET['user_name'] . "\r\n";
print "#run-number=\r\n";

?>