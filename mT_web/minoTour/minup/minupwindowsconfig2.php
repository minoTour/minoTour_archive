
<?php

$zip = new ZipArchive;
if ($zip->open('minUPW_test.zip') === TRUE) {
    $zip->addFile('./test.txt', 'newname.txt');
    $zip->close();
    echo 'ok';
} else {
    echo 'failed';
}



$dir    = './minUP_build_windows/';
$files1 = scandir($dir);
$files2 = scandir($dir, 1);

//print_r($files1);
print_r($files2);

//foreach ($files2 as $key => $value) {
//    echo "Key: $key; Value: $value<br />\n";
//}

$zip = new ZipArchive();
$filename = "./test112.zip";

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$zip->addFromString($files2);
$zip->addFromString("testfilephp2.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
$zip->addFile($thisdir . "/too.php","/testfromfile.php");
echo "numfiles: " . $zip->numFiles . "\n";
echo "status:" . $zip->status . "\n";
$zip->close();
echo "Do we get here?";
/*$zip = new ZipArchive;
if ($zip->open('test.zip') === TRUE) {
    $zip->addFromString('dir/test.txt', 'file content goes here');
    $zip->close();
    echo 'ok';
} else {
    echo 'failed';
}*/
?>



<!--
header('Content-type: text/plain');
header('Content-Disposition: attachment; filename="minup_windows.config"');

?>
-->
<?php

// include the configs / constants for the database connection
////require_once("../config/db.php");

// load the login class
////require_once("../classes/Login.php");

// load the functions
////require_once("../includes/functions.php");

////if (DB_HOST == "localhost" || DB_HOST == "127.0.0.1") {
////	$SQLHOST = gethostname();
////}else {
////	$SQLHOST = DB_HOST;
////}



////print "# --> This file is unique for your access to the minoTour installation you downloaded it from.\r\n";
////print "# --> Do not share this with other users. You can uncomment (remove the hash) from any of the lines below to enable the parameters to be loaded from the config file.\r\n";
////print "# --> For minotour-sharing-usernames you can set a comma separated list of names e.g. minotour-sharing-usernames=demo1,demo2,demo3\r\n";
////print "# --> All lines which are not commented out MUST have a value set.\r\n";
////print "[Defaults]\r\n";
////print "mysql-host=" . $SQLHOST . "\r\n";
////print "mysql-username=". $_GET['user_name'] . "\r\n";
////print "#mysql-password=\r\n";
////print "mysql-port=3306\r\n";
////print "#align-ref-fasta=\r\n";
////print "#watch-dir=\r\n";
//print "aligning-threads=3\r\n";
////print "minotour-username=" . $_GET['user_name']."\r\n";
////print "#minotour-sharing-usernames=\r\n";
////print "flowcell-owner=" . $_GET['user_name'] . "\r\n";
////print "#run-number=\r\n";

?>
