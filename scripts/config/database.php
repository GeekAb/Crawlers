<?php

	global $_MYSQL_HOST;
	global $_MYSQL_USERNAME;
	global $_MYSQL_PASSWD;
	global $_MYSQL_DB;

	function connect() {
		global $_MYSQL_HOST;
		global $_MYSQL_USERNAME;
		global $_MYSQL_PASSWD;
		global $_MYSQL_DB;
		$dbh =  mysql_pconnect (MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWD) or die('cannot'); 
		if (!$dbh)
		{
			die ('Could not connect: ' . mysql_error());
		}
		$dbSelected = mysql_select_db(MYSQL_DB,$dbh);
		if (!$dbSelected)
		{
			die ('Could not choose database: ' . mysql_error());
		}
	}
	
	function iconnect() {
		global $_MYSQL_HOST;
		global $_MYSQL_USERNAME;
		global $_MYSQL_PASSWD;
		global $_MYSQL_DB;
		
		// Create connection
		$con=mysqli_connect('p:localhost',MYSQL_USERNAME,MYSQL_PASSWD,MYSQL_DB);

		// Check connection
		if (mysqli_connect_errno($con))
		{
			echo "Failed to connect to MySQL: ".mysqli_connect_error();
			error_log("Failed to connect to MySQL: ".mysqli_connect_error(),0);
		}
		
		return $con;
	}

	function pdoconnect($value='')
	{
		global $_MYSQL_HOST;
		global $_MYSQL_USERNAME;
		global $_MYSQL_PASSWD;
		global $_MYSQL_DB;

		try {
			$pdo = new PDO("mysql:host=localhost;dbname=".MYSQL_DB, MYSQL_USERNAME, MYSQL_PASSWD);	
		} 
		catch(Exception $e) {
			echo "Failed to connect to MySQL";
			error_log("Failed to connect to MySQL: ",0);

			exit;
		}
		return $pdo;
	}

	function pdoSet($fields, &$values, $source = array()) {
		$set = '';
		$values = array();

		foreach ($fields as $field) {
			if (isset($source[$field])) {
				$set.="`".str_replace("`","``",$field)."`". "=:$field, ";
				$values[$field] = $source[$field];
			}
		}
		return substr($set, 0, -2); 
	}

	function write($db, $table, $data) {

	    $sql = "";
	    $columns = array_keys($data);

	    foreach ($columns as $column) {
			if ($sql) {
	        	$sql .= ", ";
	      	}
	      	$sql .= $column . " = :" . $column;
	    }
	    
	    $sql = "INSERT INTO " .  $table . " SET " . $sql . " ON DUPLICATE KEY UPDATE " . $sql;

	    try {
	    	$stmt = $db->prepare($sql);
	    	
	      	 $c = $stmt->execute($data);

	    } catch(PDOException $e) {
	      	
	      	echo "Error: " . $e->getMessage() . "\n";
	      	fwrite($this->error_log, "Error: " . $e->getMessage() . "\r\n");
	      	echo "SQL: "   . $sql . "\n";
	      	fwrite($this->error_log, "Data:\r\n");
	      	foreach ($data as $key => $value) {
	        	fwrite($this->error_log, "\t$key: $value\r\n");
	      	}
	    }
  	}

  	function update($table, $db, $cond, $data) {

		$sql = "";

		$columns = array_keys($data);

		foreach ($data as $key => $value) {
			if ($sql != "") {
				$sql .= ", ";
			}

			$sql .= $key."=".$db->quote($value);
		}

		$sql = "UPDATE " . $table . " SET " . $sql . " where " . $cond;
		try {

			$stmt = $db->prepare($sql);
			$stmt->execute($data);
		} catch(PDOException $e) {

			echo "Error: " . $e->getMessage() . "\n";
			fwrite($this->error_log, "Error: " . $e->getMessage() . "\r\n");
			fwrite($this->error_log, "SQL: "   . $sql . "\r\n");
			fwrite($this->error_log, "Data:\r\n");

			foreach ($data as $key => $value) {
				fwrite($this->error_log, "\t$key: $value\r\n");
			}
		}
	}

?>