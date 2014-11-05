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
	if(!isset($_POST['CCN'])) {
		$_SESSION['pay_try'] = 1;
		header( "payment.php" );
	}
	
	//IF USER NOT LOGGED IN
	if(!isset($_SESSION['userid'])) {
		$_SESSION['log_try'] = 1;
		kickHome();
	}
	
	if($_POST['submitted'] == 1) {
		$CCN = $_POST['CCN'];
		$CV2 = $_POST['CV2'];
		$exp = $_POST['exp'];
		$cname = $_POST['name'];
	
		pg_prepare($conn, "ins_pay", "INSERT INTO gang.payment_profile (userid, ccn, expiration, cv2, cardname) VALUES($1, $2, $3, $4, $5)");
		$result = pg_execute($conn, "ins_pay", array($_SESSION['userid'], $CCN, $exp, $CV2, $cname));
		
		if($result) {
			$_SESSION['pay_good'] = 1;
			header( "payment.php" );
		}
		else {
			$_SESSION['pay_bad'] = 1;
			header( "payment.php");
		}
	}
	
?>
	
	
	