
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

$settingsstring;
$settingsstring = $settingsstring . "{\r\n";
$settingsstring = $settingsstring . "    \"Aligner_to_Use\": \"none\",\r\n";
$settingsstring = $settingsstring . "    \"Alignment_Options\": \"FALSE\",\r\n";
$settingsstring = $settingsstring . "    \"Comment\": \"\",\r\n";
$settingsstring = $settingsstring . "    \"Custom_Name\": \"None\",\r\n";
$settingsstring = $settingsstring . "    \"Drop_Database?\": false,\r\n";
$settingsstring = $settingsstring . "    \"Fasta_Batch_Align?\": false,\r\n";
$settingsstring = $settingsstring . "    \"Fasta_Reference_Sequence\": \"None\",\r\n";
$settingsstring = $settingsstring . "    \"Flow_cell_Owner\": \"". $_GET['user_name'] . "\",\r\n";
$settingsstring = $settingsstring . "    \"IP_Address\": \"FALSE\",\r\n";
$settingsstring = $settingsstring . "    \"Include_Telemetry?\": false,\r\n";
$settingsstring = $settingsstring . "    \"Number_of_Processors\": -1,\r\n";
$settingsstring = $settingsstring . "    \"Number_of_Threads\": \"3\",\r\n";
$settingsstring = $settingsstring . "    \"Run_Number\": \"0\",\r\n";
$settingsstring = $settingsstring . "    \"Security_Pin\": \"FALSE\",\r\n";
$settingsstring = $settingsstring . "    \"Verbose_Output?\": false,\r\n";
$settingsstring = $settingsstring . "    \"Watch_Directory\": \"None\",\r\n";
$settingsstring = $settingsstring . "    \"minoTour_Sharing_Usernames\": \"FALSE\",\r\n";
$settingsstring = $settingsstring . "    \"minoTour_Username\": \"". $_GET['user_name'] . "\",\r\n";
$settingsstring = $settingsstring . "    \"mySQL_Host\": \"" . $SQLHOST . "\",\r\n";
$settingsstring = $settingsstring . "    \"mySQL_Password\": \"None\",\r\n";
$settingsstring = $settingsstring . "    \"mySQL_Port\": " . $SQLPORT . ",\r\n";
$settingsstring = $settingsstring . "    \"mySQL_Username\": \"". $_GET['user_name'] . "\"\r\n";
$settingsstring = $settingsstring . "}\r\n";


$tmpfname = tempnam("./", "FOO");


$file = 'minUP_0.60W.zip';


if (!copy($file, $tmpfname)) {
    //echo "failed to copy $file...\n";
} else {
    //echo "Copied arse to $tmpfname";
}

//$handle = fopen($tmpfname, "w");
//fwrite($handle, "writing to tempfile");
//fclose($handle);

$outname = $_GET['user_name'] . "_minUP_v0.6.zip";

$zip = new ZipArchive;
$res = $zip->open($tmpfname, ZipArchive::CREATE);
if ($res === TRUE) {
    $zip->addFromString('minUP_0.60W/settings.json', $settingsstring);
    $zip->close();

    $file=$tmpfname;
    if (headers_sent()) {
        echo 'HTTP header already sent';
    } else {
        if (!is_file($file)) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            echo 'File not found';
        } else if (!is_readable($file)) {
            header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
            echo 'File not readable';
        } else {
            header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length: ".filesize($file)."\"");
            header("Content-Disposition: attachment; filename=\"".$outname."\"");
            ob_clean();
            readfile($file);
        }
    }
}else {
    //echo 'failed';
}





unlink($tmpfname);
?>
