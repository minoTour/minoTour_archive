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
	
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	//echo cleanname($_SESSION['active_run_name']);;

////NOTE THAT THIS DOESN"T WORK YET!!! IT NEEDS TO BIG TIME....


	/if (!$db_connection->connect_errno) {
			$getruns = "SELECT runname FROM minIONruns inner join userrun using (runindex) inner join users using (user_id) where users.user_name = '" . $_SESSION['user_name'] ."' and activeflag = 1;";
			$getthemruns = $db_connection->query($getruns);
			if ($getthemruns->num_rows>=1){
				foreach ($getthemruns as $row){
					$databases[] = $row['runname'];
				}

				echo "<small>There are active runs.</small><br>";
				echo "<small>";
				$activealerts = 0;
				$completedalerts = 0;
				foreach ($databases as $dbname){
					$getalerts = "SELECT * FROM " . $dbname . ".alerts where complete = 0;";
					$getcompletealerts = "SELECT * FROM " . $dbname . ".alerts where complete = 1;";
					$getthemalerts = $db_connection->query($getalerts);
					$getthemcompletealerts = $db_connection->query($getcompletealerts);
					$activealerts = $activealerts + $getthemalerts->num_rows;
					$completedalerts = $completedalerts + $getthemcompletealerts->num_rows;
					
				}
				echo "$activealerts alerts outstanding.<br>";
				echo "$completedalerts alerts completed.<br>";
				echo "</small>";
			}
		
	}
	
} else {
	echo "ERROR";
}
?>