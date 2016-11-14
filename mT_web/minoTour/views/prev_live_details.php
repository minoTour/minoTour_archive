<?php
ini_set('max_execution_time', 300);
header('Content-Type: application/json');
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
	//	$prevval = 1;
	//	$database = $_SESSION['focusrun'];
	//	$telemetry = $_SESSION['focustelem'];
    //}else{
	//	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	//	$prevval = 0;
	//	$database = $_SESSION['active_run_name'];
	//	$telemetry = $_SESSION['currenttelem'];
	//}
	//echo cleanname($_SESSION['active_run_name']);;
	//echo '<br>';
    $mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	if (!$mindb_connection->connect_errno) {
        $sql = "select json_string from json_store where runkey=".$_POST['liverunname'].";";
        $sql_result = $mindb_connection->query($sql);
        $resultsarray;
        //echo $sql;
		if ($sql_result->num_rows >=1){
			foreach ($sql_result as $row){
				while ($property = mysqli_fetch_field($sql_result)) {
					//echo "<p>" . $property->name . " : " . $row[$property->name] . "</p>";
					$resultsarray[$property->name]=$row[$property->name];
				}
			}
		}
        $temp=str_replace("'", '"', $resultsarray["json_string"]);
        $temp =  str_replace('u"', '"', $temp);
        $json_array=json_decode($temp);
        //var_dump($temp);
        //var_dump($json_array);
        //var_dump(string($resultsarray["json_string"]));
        echo $resultsarray["json_string"];
        #var_dump($resultsarray);








	}
} else {
	echo "ERROR";
}
?>
