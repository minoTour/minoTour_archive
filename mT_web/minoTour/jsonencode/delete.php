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
        $mindb_connectionGru = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
        $mindb_connectionGru = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	}
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';
    $deletefromGru = 'delete minIONruns, userrun from minIONruns inner join userrun using (runindex) where runname = "' . $_SESSION['focusrun'] . '";';
    $sqldeleteGru = $mindb_connectionGru->query($deletefromGru);
    $dropdatabase = 'drop database ' . $_SESSION['focusrun'] . ';';
    $dropfromGru = $mindb_connection->query($dropdatabase);
    echo "<div class='alert alert-warning alert-dismissible' role='alert'>
<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
<strong>Success!</strong> The database has been deleted for " . $_SESSION['focusrun'] . ". This page will redirect in 3 seconds.
</div>";

} else {
	echo "ERROR";
}
?>
