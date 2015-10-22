<?php //Generate text file on the fly

header('Content-type: text/plain');
header('Content-Disposition: attachment; filename="settings.json"');



// include the configs / constants for the database connection
require_once("../config/db.php");

// load the login class
require_once("../classes/Login.php");

// load the functions
require_once("../includes/functions.php");

if (DB_HOST == "localhost" || DB_HOST == "127.0.0.1") {
	$SQLHOST = $_SERVER['SERVER_ADDR'];

}else {
	$SQLHOST = DB_HOST;
#   }
$SQLPORT = DB_PORT;

print "{\r\n";
print "    \"Aligner_to_Use\": \"none\",\r\n";
print "    \"Alignment_Options\": \"FALSE\",\r\n";
print "    \"Comment\": \"\",\r\n";
print "    \"Custom_Name\": \"None\",\r\n";
print "    \"Drop_Database?\": false,\r\n";
print "    \"Fasta_Batch_Align?\": false,\r\n";
print "    \"Fasta_Reference_Sequence\": \"None\",\r\n";
print "    \"Flow_cell_Owner\": \"". $_GET['user_name'] . "\",\r\n";
print "    \"IP_Address\": \"FALSE\",\r\n";
print "    \"Include_Telemetry?\": false,\r\n";
print "    \"Number_of_Processors\": -1,\r\n";
print "    \"Number_of_Threads\": \"3\",\r\n";
print "    \"Run_Number\": \"0\",\r\n";
print "    \"Security_Pin\": \"FALSE\",\r\n";
print "    \"Verbose_Output?\": false,\r\n";
print "    \"Watch_Directory\": \"None\",\r\n";
print "    \"minoTour_Sharing_Usernames\": \"FALSE\",\r\n";
print "    \"minoTour_Username\": \"". $_GET['user_name'] . "\",\r\n";
print "    \"mySQL_Host\": \"" . $SQLHOST . "\",\r\n";
print "    \"mySQL_Password\": \"None\",\r\n";
print "    \"mySQL_Port\": " . $SQLPORT . ",\r\n";
print "    \"mySQL_Username\": \"". $_GET['user_name'] . "\"\r\n";
print "}\r\n";


?>
