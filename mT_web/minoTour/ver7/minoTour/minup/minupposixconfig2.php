
<?php

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
}
$SQLPORT = DB_PORT;

$settingsstring;

$settingsstring = $settingsstring . "# --> This file is unique for your access to the minoTour installation you downloaded it from.\n";
$settingsstring = $settingsstring ."# --> Do not share this with other users. You can uncomment (remove the hash) from any of the lines below to enable the parameters to be loaded from the config file.\n";
$settingsstring = $settingsstring ."# --> For minotour-sharing-usernames you can set a comma separated list of names e.g. minotour-sharing-usernames=demo1,demo2,demo3\n";
$settingsstring = $settingsstring ."# --> All lines which are not commented out MUST have a value set.\n";
$settingsstring = $settingsstring ."[Defaults]\n";
$settingsstring = $settingsstring ."mysql-host=" . $SQLHOST . "\n";
$settingsstring = $settingsstring ."mysql-username=". $_GET['user_name'] . "\n";
$settingsstring = $settingsstring ."#mysql-password=\n";
$settingsstring = $settingsstring ."mysql-port=" . $SQLPORT . "\n";
$settingsstring = $settingsstring ."#align-ref-fasta=\n";
$settingsstring = $settingsstring ."#watch-dir=\n";
$settingsstring = $settingsstring ."aligning-threads=3\n";
$settingsstring = $settingsstring ."minotour-username=" . $_GET['user_name']."\n";
$settingsstring = $settingsstring ."#minotour-sharing-usernames=\n";
$settingsstring = $settingsstring ."flowcell-owner=" . $_GET['user_name'] . "\n";
$settingsstring = $settingsstring ."#run-number=\n";


$tmpfname = tempnam("./", "FOO");


$file = 'minUP.v0.6_posix.zip';


if (!copy($file, $tmpfname)) {
    echo "failed to copy $file...\n";
} else {
    echo "Copied arse to $tmpfname";
}

//$handle = fopen($tmpfname, "w");
//fwrite($handle, "writing to tempfile");
//fclose($handle);

$outname = $_GET['user_name'] . "_minUP.v0.6_posix.zip";

$zip = new ZipArchive;
$res = $zip->open($tmpfname, ZipArchive::CREATE);
if ($res === TRUE) {
    $zip->addFromString('minup_posix.config', $settingsstring);
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
            header("Content-Type: application/octet-stream");
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
