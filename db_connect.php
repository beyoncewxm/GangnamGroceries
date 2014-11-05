<?php
	//function that connects to the database
	
	function connect_to_db(){
		$server = "dbhost-pgsql.cs.missouri.edu";
		$username = "cs3380f12grp1";
		$password = "ix2gRndS";
		$dbname = "cs3380f12grp1";
		$conn = pg_connect("host=$server
		user=$username password=$password
		dbname=$dbname");	
		
		if(!$conn){
			echo "Unable to connect to database";
			return 0;
		}
		else{
			return $conn;
		}
	}
?>
