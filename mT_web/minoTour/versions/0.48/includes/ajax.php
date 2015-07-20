<?php 
// load the functions
require_once("functions.php");
// include the configs / constants for the database connection
require_once("../config/db.php");

$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

$sql = "INSERT INTO comments (runname, user_name,date,comment,name) VALUES (	'" . $_POST['run'] . "','" . $_POST['user'] . "','" . $_POST['time']. "','" . $_POST['message'] . "','" . $_POST['name'] . "')";

if ($mindb_connection->query($sql) === TRUE) {
					
					$grucomsql = "select * from Gru.comments where runname = \"" . $_POST['run'] . "\" order by date desc limit 1;";
					$grucomsqlresults = $mindb_connection->query($grucomsql);
					if ($grucomsqlresults->num_rows >= 1) {
						foreach ($grucomsqlresults as $row) {
							echo "<strong class=\"pull-left primary-font\">" . $row['name'] . "</strong>";
							echo "<small class=\"pull-right text-muted\">";
							echo "<span class=\"glyphicon glyphicon-time\"></span>" . $row['date'] . "</small>";
							echo "</br>";
							echo "<li class=\"ui-state-default\">";
							echo $row['comment'];
							echo "</br>";
							echo "<hr>";
						}
					}
				

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
?>
