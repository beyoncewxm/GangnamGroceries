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

	//check for user login
	if(!isset($_SESSION['userid'])){
		$_SESSION['no_cart'] = 1;
		header("Location: selected_item.php");
	}
	else {
		//handle qty, default to 1.
		if($_POST['qty'] == 0) {
			$qty = 1;
		}
		else {
			$qty = $_POST['qty'];
		}
	
		//handle missing variables
		if(!isset($_POST['item'])){
			echo "item not passed correctly.";
		}
		//else make function call
		else {
			addItem($_SESSION['userid'], $_POST['item'], $qty);
			$_SESSION['added'] = 1;
			header("Location: selected_item.php");
		}
	}
?>
