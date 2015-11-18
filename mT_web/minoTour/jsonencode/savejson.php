<?php


	//Check if JSON Table already exists:
		$query = "SHOW TABLES LIKE 'jsonstore';";
		$sql = $mindb_connection->query($query);
		$result = $sql->num_rows;
		//echo $query . "\n";
		//echo $result . "\n";

		if ($result >= 1){
			//echo "Table exists";
			$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jobname . "','".$jsonstring . "');";
			$go = $mindb_connection->query($insertresult);
		}else{
			//echo "Table needs creating";
			$create_table =
			"CREATE TABLE `jsonstore` (
  `index` INT NOT NULL AUTO_INCREMENT,
  `name` MEDIUMTEXT NOT NULL,
  `json` LONGTEXT NOT NULL,
  PRIMARY KEY (`index`)
)
CHARACTER SET utf8;";
		//echo $create_table;
			$create_tbl = $mindb_connection->query($create_table);

			//echo "session variable is " . $_SESSION['jsonjobname'];
			//echo $_SESSION['jsonstring'];
			$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jobname . "','".$jsonstring . "');";
			$go = $mindb_connection->query($insertresult);

		}







?>
