<?php
	//FORCE DISPLAY OF ERRORS TO BROWSER
	ini_set("display_errors", 1);
	ERROR_REPORTING(E_ALL);
	//INCLUDE CONNECTION DETAILS FROM FILE
	include "db_connect.php";
	include "function_page.php";
	//BEGIN SESSION
	session_start();
	
	//IF NO POST DATA, KICK BACK TO REGISTRATION
	if(!isset($_POST['username'])) {
		$_SESSION['log_try'] = 1;
		kickHome();
	}
	//STORE POST VARIABLES LOCALLY
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	//Check for username in database
	$userArray = checkUser($username);
	//If supplied username not found in database, kick back with error
	if(!$userArray) {
		$_SESSION['bad_user'] = 1;
		kickHome();
	}
	
	//call login function to try login
	$log_success = loginUser($username, $password);
	//if passwords match, save session variables
	if($log_success == TRUE) {
		$_SESSSION['userLogged'] = $userArray['userid'];
		$_SESSION['username'] = $userArray['email'];
		$_SESSION['userid'] = $userArray['userid'];
		kickHome();
	}
	//if passwords don't match
	else {
		$_SESSION['bad_pass'] = 1;
		kickHome();
	}
	/*
	//Check for username in database
	include "function_page.php";
	$userArray = checkUser($username);
	//If supplied username not found in database, kick back with error
	if(!$userArray) {
		$_SESSION['bad_user'] = 1;
		kickHome();
	}
	
	//Get credentials for supplied username
	$authArray = selectAuth($userArray['userid']);
	
	$passwordTest = testPass($userArray['userid'], $password);
	
	//IF matching passwords, store session vars to login user
	if($passwordTest == 1) {
		$_SESSSION['logged'] = 1;
		$_SESSION['username'] = $username;
		$_SESSION['userid'] = $userArray['userid'];
		kickHome();
		
	}
	//IF supplied password does not match database password
	else {
		$_SESSION['bad_pass'] = 1;
		kickHome();
	}
		 
		 
	function login($username, $password){
		include 'db_connect.php';
		$conn = connect_to_db();
		
		//run a query that returns the hashed password and salt for a particular username
		$query = "SELECT password_hash, salt FROM authentication WHERE username = $1";
		
		$stmt = pg_prepare($conn, "login_query", $query);
		if($stmt){	
			$result = pg_execute($conn, "login_query", array($username));
		}
		else{
			echo "Statement preparation failed.";
			exit;
		}
		$row = pg_fetch_assoc($result);
		$salt2 = $row['salt'];
		//for some reason, my salts are coming back with a whitespace
		//on the end, so we need to trim it
		$salt = trim($salt2);
		
		$userhash = sha1( $salt . $password . $salt );

		//compare the password created from the user input to the password
		//retrieved from the db
		if($userhash == $row['password_hash']){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}	 
	*/
?>
