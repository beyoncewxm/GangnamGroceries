<?php 
	//FORCE DISPLAY OF ERRORS TO BROWSER
	ini_set("display_errors", 1);
	ERROR_REPORTING(E_ALL);
	//INCLUDE CONNECTION DETAILS FROM FILE
	include "db_connect.php";
	//INCLUDE FUNCTIONS
	include "function_page.php";
	//BEGIN SESSION
	session_start();
?>

<?php

	//IF NO POST DATA, KICK BACK TO REGISTRATION
	if(!isset($_POST['username'])) {
		$_SESSION['reg_try'] = 1;
		kickReg();
	}
	//IF NON-MATCHING PASSWORDS, KICK BACK TO REGISTRATION
	if($_POST['password'] != $_POST['confirm_password']){
		echo "Passwords shown as non-matching!<br />";
		$_SESSION['pass_match'] = 1;
		kickReg();
	}
	//IF POST DATA PRESENT, STORE VARIABLES LOCALLY
	if($_POST['submitted'] == 1) {
		//STORE POST VARIABLES
		$username = $_POST['username'];
		$prov_password = $_POST['password'];
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$addr = $_POST['addr'];
		$zip = $_POST['zip'];
		$phone = $_POST['phone'];
		$CCN = $_POST['CCN'];
		$expYear = $_POST['year'];
		$expMonth = $_POST['month'];
		$CV2 = $_POST['CV2']; 
		$cardName = $_POST['cardname'];
	}
	//QUERY FOR USERNAME TO DETERMINE IF AVAILABLE
	$notValidUser = checkUser($username);
	//IF USERNAME ALREADY EXISTS, KICK BACK TO REGISTRATION
	if($notValidUser) {
		$_SESSION['bad_email'] = 1;
		kickReg();  
	}
	//INSERT NEW USER INFO
	$insertInfo = insertInfo($fname, $lname, $addr, $zip, $username, $phone);
	//GET USER ID FROM NEW USER
	$selectionID = selectUser($username);
	//INSERT NEW AUTH
	$insertAuth = insertAuth($selectionID, $prov_password);
	//INSERT INTO NEW PAYMENT PROFILE
	$insertPayment = insertPayment($selectionID, $CCN, $expYear, $expMonth, $CV2, $cardName);		

	
	echo '<pre>' . print_r($_POST, true) . '</pre>';
	echo "$selectionID <br>";

	//IF SUCCESSFUL INSERT USER, FORWARD TO HOME
	if($insertPayment && $insertInfo && $insertAuth) {
		$_SESSION['userid'] = $selectionID;
		header( 'Location: http://babbage.cs.missouri.edu/~cs3380f12grp1/index.php' );
	}
	//IF UNSUCCESSFUL INSERT, REPORT UNKNOWN ERROR
	else {
		$_SESSION['reg_fail'] = 1;
		echo "failure <br>";
	//	kickReg();
	}
?>

