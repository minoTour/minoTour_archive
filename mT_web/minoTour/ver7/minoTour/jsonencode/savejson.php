<?php


	//Check if JSON Table already exists:
		$query = "SHOW TABLES LIKE 'jsonstore';";
		$sql = $mindb_connection->query($query);
		$result = $sql->num_rows;
		//echo $query . "\n";
		//echo $result . "\n";

		if ($result >= 1){
			//echo "Table exists";
			$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jobname . "','".$jsonstring . "') ON DUPLICATE KEY UPDATE name=VALUES(name), json=VALUES(JSON);";
            //echo $insertresult . "\n";
            $go = $mindb_connection->query($insertresult);
		}else{
			//echo "Table needs creating";
			$create_table =
			"CREATE TABLE `jsonstore` (
  `name` VARCHAR(255) NOT NULL,
  `json` LONGTEXT NOT NULL,
  PRIMARY KEY (`name`)
)
CHARACTER SET utf8;";
		//echo $create_table;
			$create_tbl = $mindb_connection->query($create_table);
            //echo $create_table;
            //echo "We ought to have created the table";
			//echo "session variable is " . $_SESSION['jsonjobname'];
			//echo $_SESSION['jsonstring'];
			$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jobname . "','".$jsonstring . "') ON DUPLICATE KEY UPDATE name=VALUES(name), json=VALUES(JSON);";
			$go = $mindb_connection->query($insertresult);

		}

        //echo "Save JSON CALLED";





?>
