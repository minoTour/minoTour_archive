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
	if($_GET["prev"] == 1){
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	}
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	$jobtype = $_GET['job'];
	
	switch ($jobtype) {
    case "stopminion":
    	$command = "insert into interaction (instruction,target,complete) VALUES ('stop','all','0');";
    	$sqlcommand = $mindb_connection->query($command);
        echo "<div class='alert alert-warning alert-dismissible' role='alert'>
	  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
	  <strong>Success!</strong> A stop instruction has been sent to the minION device.
	</div>";
        break;
    case "startminion":
	    $command = "insert into interaction (instruction,target,complete) VALUES ('start','all','0');";
    	$sqlcommand = $mindb_connection->query($command);
       echo "<div class='alert alert-warning alert-dismissible' role='alert'>
	  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
	  <strong>Success!</strong> A start instruction has been sent to the minION device.
	</div>";
        break;
    case "testminion":
    	$command = "insert into interaction (instruction,target,complete) VALUES ('test','all','0');";
    	$sqlcommand = $mindb_connection->query($command);
       echo "<div class='alert alert-warning alert-dismissible' role='alert'>
	  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
	  <strong>Success!</strong> A test message has been sent to the minION device.
	</div>";
	break;
	case "biasvoltageget":
    	$command = "insert into interaction (instruction,target,complete) VALUES ('biasvoltageget','all','0');";
    	$sqlcommand = $mindb_connection->query($command);
       echo "<div class='alert alert-warning alert-dismissible' role='alert'>
	  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
	  <strong>Success!</strong> Trying to get bias voltage.
	</div>";
	break;
	case "biasvoltageinc":
    	$command = "insert into interaction (instruction,target,complete) VALUES ('biasvoltageinc','all','0');";
    	$sqlcommand = $mindb_connection->query($command);
       echo "<div class='alert alert-warning alert-dismissible' role='alert'>
	  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
	  <strong>Success!</strong> Trying to increment bias voltage.
	</div>";
	break;
	case "biasvoltagedec":
    $command = "insert into interaction (instruction,target,complete) VALUES ('biasvoltagedec','all','0');";
    $sqlcommand = $mindb_connection->query($command);
      echo "<div class='alert alert-warning alert-dismissible' role='alert'>
	 <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
	 <strong>Success!</strong> Trying to decrease bias voltage.
	</div>";
	break;
    default:
    	 echo "<div class='alert alert-danger alert-dismissible' role='alert'>
	  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
	  <strong>ERROR!</strong> No job has been specified and some kind of error has occurred - sorry about this!
	</div>";
}
	
			
		
		
		
		
		
}

function getx($value){
	$value = $value-1;
	$xval=31-(($value - ($value % 4))/4 % 32);
	return($xval);
}


function gety($value){
	$value = $value-1;
	$ad36 = $value % 4;
	$ab37 = ($value - $ad36)/4;
	$ad37 = ($ab37 % 32);
	$ab38 = (($ab37-$ad37)/32);
	$ad38 = ($ab38 % 4);
	$ag38 = ($ad36+(4*$ad38));
	$yval=(15 - $ag38);
	return($yval);
}
?>