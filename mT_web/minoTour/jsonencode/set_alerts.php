<?php
ini_set('max_execution_time', 300);
//header('Content-Type: application/json');
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
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
    //BY DEFINITION WORKING WITH ACTIVE DATABASE HERE
	
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';
	
	##Array to store read types
	
	$typearray = array();

	if (!$mindb_connection->connect_errno) {
		if (isset ($_GET['task'])){
			$name = "'".$_GET['task']."'";
		}else{
			$name = "'FAILED'";
		}
		if (isset ($_GET['reference'])) {
			$reference = "'".$_GET['reference']."'";	
		}else{
			$reference = "null";
		}
		if (isset ($_GET['threshold'])) {
			$threshold = $_GET['threshold'];	
		}else{
			$threshold = "null";
		}
		if (isset ($_GET['rununtil'])){
			$control = $_GET['rununtil'];
		}else{
			$control = 0;
		}
		if (isset ($_GET['start'])) {
			$start = $_GET['start'];	
		}else{
			$start = "null";
		}
		if (isset ($_GET['twitterhandle']) && strlen ($_GET['twitterhandle']) > 0) {
			$twitterhandle = "'".$_GET['twitterhandle']."'";	
		}else{
			$twitterhandle = "null";	
		}
		if (isset ($_GET['type'])) {
			$typeref = $_GET['type'];
			switch ($_GET['type']) {
			    case "All":
			    $typearray = array("'template'","'complement'","'2d'");
		        break;
			    case "Template":	
			    $typearray = array("'template'");
        		break;
    			case "Complement":
			    $typearray = array("'complement'");
        		break;
    			case "2D":
			    $typearray = array("'2d'");
        		break;
    		default:
			    $typearray = array();
			}
		}else{
			$typearray = array();	
		}
		if (isset ($_GET['end'])) {
			$end = $_GET['end'];	
		}else{
			$end= "null";
		}
		
		foreach ($typearray as &$type) {
			//echo $name . "<br>";
			//We need to check if specific alerts which will stop the sequencer are already set and - if they are reset them
			if ($name === "'barcodecoverage'") {
				//echo "HELLO - WE NEED TO FIX SOMETHING HERE";
				$sqldelete = "delete from alerts where name = 'barcodecoverage' and reference = " . $reference . ";";
				$sqldeleteexecute = $mindb_connection->query($sqldelete);
				$sqlinsert = "insert into alerts (name,reference,twitterhandle,type,threshold,start,end,control,complete) values (" . $name .	"," . $reference .	"," . $twitterhandle .	"," . $type .	"," . $threshold .	"," . $start .	 "," . $end . "," . $control .	",0);";
			//echo $sqlinsert;
				$sqlinsertexecute = $mindb_connection->query($sqlinsert);
			}elseif ($name === "'genbarcodecoverage'") {
				$sqldelete = "delete from alerts where name = 'genbarcodecoverage';";
				$sqldeleteexecute = $mindb_connection->query($sqldelete);
				$sqlinsert = "insert into alerts (name,reference,twitterhandle,type,threshold,start,end,control,complete) values (" . $name .	"," . $reference .	"," . $twitterhandle .	"," . $type .	"," . $threshold .	"," . $start .	 "," . $end . "," . $control .	",0);";	
				$sqlinsertexecute = $mindb_connection->query($sqlinsert);
			}elseif ($name === "'genbarcodecoveragedelete'"){
				$sqldelete = "delete from alerts where name = 'genbarcodecoverage';";
				$sqldeleteexecute = $mindb_connection->query($sqldelete);
				//echo $sqldelete . "<br>";
			}elseif ($name === "'barcodecoveragedelete'"){
				$sqldelete = "delete from alerts where name = 'barcodecoverage';";
				$sqldeleteexecute = $mindb_connection->query($sqldelete);
				//echo $sqldelete . "<br>";
			}elseif ($name === "'referencecoveragedelete'"){
				//echo "YELLOW";
				$alert_index = substr($reference, 10,-1);
				$sqldelete = "delete from alerts where alert_index = " . $alert_index . ";";
				//echo $sqldelete . "<br>";
				$sqldeleteexecute = $mindb_connection->query($sqldelete);
				//echo $sqldelete . "<br>";
			}else{
				$sqlinsert = "insert into alerts (name,reference,twitterhandle,type,threshold,start,end,control,complete) values (" . $name .	"," . $reference .	"," . $twitterhandle .	"," . $type .	"," . $threshold .	"," . $start .	 "," . $end . "," . $control .	",0);";
			//echo $sqlinsert;
				$sqlinsertexecute = $mindb_connection->query($sqlinsert);
			}
		}
		echo "<div class='alert alert-success alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>  <strong>Success!</strong> An alert has been set for the following run: " .cleanname($_SESSION['active_run_name']) . "<br> The alert is for $name on $typeref strands. </div>";
	}else{
		echo "<div class='alert alert-danger alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>  <strong>Failed!</strong> An alert has not been set. You should not normally see this message so a really unexpected error has occured.</div>";
	}
		
	
	
} else {
	echo "ERROR";
}
?>