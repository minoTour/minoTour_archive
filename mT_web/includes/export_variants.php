<?php






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
	//echo cleanname($_SESSION['focusrun']);
	if($_GET["prev"] == 1){
		$database =$_SESSION['focusrun'];
	}else{
		$database =$_SESSION['active_run_name'];
	}
	// Database connection information
	$gaSql['user']     = DB_USER;
	$gaSql['password'] = DB_PASS;
	$gaSql['db']       = $database;
	$gaSql['server']   = DB_HOST;
	$gaSql['port']     = DB_PORT; // 3306 is the default MySQL port
	
	$type = $_GET["type"];
	$coverage = $_GET["coverage"];
	$job = $_GET["job"];
	
	
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=$database.$type.$coverage.$job.var");


	$db = new mysqli($gaSql['server'], $gaSql['user'], $gaSql['password'], $gaSql['db'], $gaSql['port']);
	if (mysqli_connect_error()) {
		die( 'Error connecting to MySQL server (' . mysqli_connect_errno() .') '. mysqli_connect_error() );
	}
 
	
	switch ($job) {
    	case "consensus":
    		$sql = "SELECT ref_id, refname, ref_seq, @var_max_val:= GREATEST(A, T, G, C), CASE @var_max_val WHEN A THEN 'A' WHEN T THEN 'T' WHEN C THEN 'C' WHEN G THEN 'G' END, ref_pos, A, T, G, C, (A + T + G + C), (((A + T + G + C) - GREATEST(A, T, G, C)) / (A + T + G + C)), (GREATEST(A, T, G, C) / (A + T + G + C))
FROM reference_coverage_" . $type . " inner join reference_seq_info WHERE (A + T + G + C) >= " . $coverage . " and ref_id = refid and ref_seq != case greatest(A, T, G, C) WHEN A THEN 'A'
    WHEN T THEN 'T'
    WHEN C THEN 'C'
    WHEN G THEN 'G'
    END ORDER BY `ref_id` asc;";
    $headings = "RefID,rename,refSEQ,conSEQcount,conSEQ,refPOS,A,T,G,C,Total,mismatched,common\n";
    	    break;
    	case "variants":
    	$sql = "SELECT ref_id, refname, ref_seq, @var_max_val:= GREATEST(A, T, G, C), CASE @var_max_val WHEN A THEN 'A' WHEN T THEN 'T' WHEN C THEN 'C' WHEN G THEN 'G' END, ref_pos, A, T, G, C, (A + T + G + C), (((A + T + G + C) - GREATEST(A, T, G, C)) / (A + T + G + C)), (GREATEST(A, T, G, C) / (A + T + G + C))
FROM reference_coverage_" . $type . " inner join reference_seq_info WHERE (A + T + G + C) >= " . $coverage . " and ref_id = refid and (((A + T + G + C) - GREATEST(A, T, G, C)) / (A + T + G + C)) >= (select (AVG(((A + T + G + C) - GREATEST(A, T, G, C)) / (A + T + G + C)) + 2 * ( STD(((A + T + G + C) - GREATEST(A, T, G, C)) / (A + T + G + C)))) as threshold from reference_coverage_2d) ORDER BY `ref_id` asc;";
		$headings = "RefID,rename,refSEQ,conSEQcount,conSEQ,refPOS,A,T,G,C,Total,mismatched,common\n";
    	    break;
    	case "deletions":
    		$sql = "SELECT ref_id, refname, ref_seq, @var_max_val:= GREATEST(A, T, G, C, D), CASE @var_max_val WHEN A THEN 'A' WHEN T THEN 'T' WHEN C THEN 'C' WHEN G THEN 'G' WHEN D THEN 'D' END, ref_pos, A, T, G, C, D, (A + T + G + C + D), (((A + T + G + C + D) - GREATEST(A, T, G, C, D)) / (A + T + G + C + D)), (GREATEST(A, T, G, C, D) / (A + T + G + C + D))
FROM reference_coverage_" . $type . " inner join reference_seq_info WHERE (A + T + G + C + D) >= " . $coverage . " and ref_id = refid and case greatest(A, T, G, C, D) WHEN A THEN 'A'
    WHEN T THEN 'T'
    WHEN C THEN 'C'
    WHEN G THEN 'G'
    WHEN D THEN 'D'
    END = 'D' ORDER BY `ref_id` asc;";
		$headings = "RefID,rename,refSEQ,conSEQcount,conSEQ,refPOS,A,T,G,C,D,Total,mismatched,common\n";
    	    break;
    	case "insertions":
    	$sql = "SELECT SQL_CALC_FOUND_ROWS ref_id, refname, ref_seq, @var_max_val:= GREATEST(A,T,G,C,I), CASE @var_max_val WHEN A THEN 'A' WHEN T THEN 'T' WHEN C THEN 'C' WHEN G THEN 'G' WHEN I THEN 'I' END, ref_pos, A, T, G, C, I, (A+T+G+C+I), (((A+T+G+C+I) - GREATEST(A,T,G,C,I))/(A+T+G+C+I)), (GREATEST(A,T,G,C,I)/(A+T+G+C+I))
	FROM reference_coverage_" . $type . " inner join reference_seq_info WHERE (A+T+G+C+I) >= " . $coverage . " and ref_id = refid and case greatest(A,T,G,C,I) WHEN A THEN 'A'
                         WHEN T THEN 'T'
                         WHEN C THEN 'C'
                         WHEN G THEN 'G'
						 WHEN I THEN 'I'
       END = 'I' ORDER BY `ref_id` asc;";
       $headings = "RefID,rename,refSEQ,conSEQcount,conSEQ,refPOS,A,T,G,C,I,Total,mismatched,common\n";
    	    break;

	}
	$rResult = $db->query( $sql ) or die($db->error);
	echo $headings;
	if ($rResult->num_rows >= 1){
		foreach ($rResult as $row) {
			//$array[] = $row;
			echo implode(",", $row). "\n";
		}
	}	
	
	
	

	}else {
	echo "ERROR";
}

	?>