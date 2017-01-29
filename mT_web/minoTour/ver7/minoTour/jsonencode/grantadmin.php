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
    //if($_GET["prev"] == 1){
	//	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
	//}else{
	//	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	//}
    $mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
        if ($_SESSION['adminuser'] == 1){
            $sql = "select * from users where user_name = '".$_GET["user"]."';";
            //echo $sql;
            $users = $mindb_connection->query($sql);
            //var_dump($users);
            if ($users->num_rows==1){
                //foreach ($users as $user){
                    //echo $user['user_name'];
                    //echo $_GET['pass'];
                //}
                $sql1="update USERS set admin = 1 where user_name =  '".$_GET["user"]."';";
                //echo $sql1 . "<br>";
                $mindb_connection->query($sql1);


            };
            echo "<div class='alert alert-success alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
  <strong>Success!</strong> You are authorized to modify user accounts.
</div>";
}else{
    echo "<div class='alert alert-danger alert-dismissible' role='alert'>
<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
<strong>Action Not Authorized</strong> You are not authorized to make these changes.
</div>";
}
    }
} else {
	echo "ERROR";
}
?>
