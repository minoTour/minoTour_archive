<?php
	
		
	//Check if JSON Table already exists:
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
		$query = "SHOW TABLES LIKE 'alerts';";
		$sql = $mindb_connection->query($query);
		$result = $sql->num_rows;
		//echo $query . "\n";
		//echo $result . "\n";
		
		if ($result >= 1){
			//echo "Table exists";
			//$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jsonjobname . "','".$jsonstring . "');";
			//$go = $mindb_connection->query($insertresult);
		}else{
			//echo "Table needs creating";
			$create_table =
			"CREATE TABLE `alerts` (
  `alert_index` INT NOT NULL AUTO_INCREMENT,
  `name` MEDIUMTEXT NOT NULL,
  `reference` MEDIUMTEXT,
  `username` MEDIUMTEXT,
  `twitterhandle` MEDIUMTEXT,
  `type` MEDIUMTEXT,
  `threshold` INT,
  `start` INT,
  `end` INT,
  `control` INT,
 `complete` INT,
 `createtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`alert_index`)
)
CHARACTER SET utf8;";
		//echo $create_table;
			$create_tbl = $mindb_connection->query($create_table);
			
			
			//echo "session variable is " . $_SESSION['jsonjobname'];
			//echo $_SESSION['jsonstring'];
			//$insertresult = "INSERT INTO jsonstore (name,json) VALUES ('". $jsonjobname . "','".$jsonstring . "');";
			//$go = $mindb_connection->query($insertresult);
				
		}
		
		
		

	
	
 
?>