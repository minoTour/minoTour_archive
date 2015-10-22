<?php //Generate text file on the fly

header('Content-type: text/plain');
header('Content-Disposition: attachment; filename="minup_posix.config"');

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
$SQLPORT = DB_PORT;


print "# --> This file is unique for your access to the minoTour installation you downloaded it from.\n";
print "# --> Do not share this with other users. You can uncomment (remove the hash) from any of the lines below to enable the parameters to be loaded from the config file.\n";
print "# --> For minotour-sharing-usernames you can set a comma separated list of names e.g. minotour-sharing-usernames=demo1,demo2,demo3\n";
print "# --> All lines which are not commented out MUST have a value set.\n";
print "[Defaults]\n";
print "mysql-host=" . $SQLHOST . "\n";
print "mysql-username=". $_GET['user_name'] . "\n";
print "#mysql-password=\n";
print "mysql-port=" . $SQLPORT . "\n";
print "#align-ref-fasta=\n";
print "#watch-dir=\n";
print "aligning-threads=3\n";
print "minotour-username=" . $_GET['user_name']."\n";
print "#minotour-sharing-usernames=\n";
print "flowcell-owner=" . $_GET['user_name'] . "\n";
print "#run-number=\n";

?>
