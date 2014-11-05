<?php


	//FORCE DISPLAY OF ERRORS TO BROWSER
	//ini_set("display_errors", 1);
	//ERROR_REPORTING(E_ALL);
	//INCLUDE CONNECTION/MISC FUNCTION PAGES
	include "function_page.php";
	include "db_connect.php";
	include "cart.php";
	//BEGIN SESSION
	session_start();

	addItem($_SESSION['userid'], $_POST['item'], $qty);
	echo "should be succesful add.";
	
?>