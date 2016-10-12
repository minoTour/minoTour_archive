<?php
//ini_set('max_execution_time', 300);
//echo "hello";
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

//echo "hello";
// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    session_start();
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
//    echo "hello fruity";
	$new = $_POST['new'];

	$update = "UPDATE users SET twitnote = '" . $new . "' where user_name = '". $_SESSION['user_name'] . "';";
	$updatequery=$mindb_connection->query($update);
    $_SESSION['twitnote'] = (int)$new;
//	}
} else {
	echo "ERROR";
}
?>
