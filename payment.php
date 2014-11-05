<?php
	//FORCE DISPLAY OF ERRORS TO BROWSER
	//ini_set("display_errors", 1);
	//ERROR_REPORTING(E_ALL);
	//INCLUDE CONNECTION/MISC FUNCTION PAGES
	include "function_page.php";
	include "db_connect.php";
	//BEGIN SESSION
	session_start();
?>

<!DOCTYPE html PUBLIC "http://babbage.cs.missouri.edu/~cs3380f12grp1/">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="description" content="Description of our online store page." />
<meta name="keywords" content="keywords, will, go here, when determined" />
<meta name="author" content="Team" />
<link rel="stylesheet" type="text/css" href="reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="style.css" media="screen,print" />
<title>gangnam // groceries</title> 
</head>
<body>

<div id="mainContainer" class="clearfix">

<div id="header">
<a href="index.php">
<img src='images/banner.png' alt='gangnam // groceries' width ='890' height = '90'>
</a>
</div><!--// end #header //-->

<div id="navHorizontal">
	<fieldset name='search'>
	<form method='GET' action='search.php'>
		
		<label for='search' >search for items: </label>
		<input type='text' name='search' />	
		<input type='submit' name='submit', value='search.' />
	</form>
	</fieldset>
</div><!--// end #navHorizontal //-->

<div id="columnOne">

<?php
	
	//Begin IF user logged in statements
	if(isset($_SESSION['userid'])) {
?>

<h2>cart.</h2>
<div id="navVertical">
	<fieldset name = "cart">
	<ul>these are cart items.
<?php
	//Check if user has current active cart
	$cartCheck = hasCart($_SESSION['userid']);
	//IF user does not have current cart
	if($cartCheck == null) {
?>
	<li>cart is empty.  :[</li>
	</ul>
<?php
	//End IF user does not have current cart
	}
	//IF user has current cart
	if($cartCheck) {
		getCartItems($cartCheck['orderid']);	
?>
	</ul>
<?php
	//End IF user has current cart
	}
?>
	<a href="access_cart.php">view cart or check out.</a>
	<a href="logout.php">logout.</a>
	</fieldset>
</div><!--// end #navVertical //-->

<?php
	//End IF user logged in statements
	}
	
	//Begin IF user not logged in statements
	if(!isset($_SESSION['userid'])) {
		header( "index.php");
?>

<!--// display login form //-->
<h2>login.</h2>

<?php 
	//Perform checks for error handling
	//IF user tries to skip to login page
	if(isset($_SESSION['log_try'])) {
		echo "<FONT COLOR=\"CC0000\">enter login info.<br /><br /></FONT>";
		unset($_SESSION['log_try']);
	}
	//IF username not found
	if(isset($_SESSION['bad_user'])) {
		echo "<FONT COLOR=\"CC0000\">username not found.<br /><br /></FONT>";
		unset($_SESSION['bad_user']);
	}
	//IF bad password
	if(isset($_SESSION['bad_pass'])) {
		echo "<FONT COLOR=\"CC0000\">password incorrect.<br /><br /></FONT>";
		unset($_SESSION['bad_pass']);
	}
	if(isset($_SESSION['username'])) {
		echo "<FONT COLOR=\"CC0000\">" . $_SESSION['username'] . " is username.<br /><br /></FONT>";
	}
	if(isset($_SESSION['username'])) {
		echo "<FONT COLOR=\"CC0000\">" . $_SESSION['userid'] . " is user id.<br /><br /></FONT>";
	}
?>	

<div id="navVertical">
	<fieldset name = "login">
	<form method='POST' action='login.php'>
		<label for='username' >username </label>
		<input type='text' name='username' /><br />
		<label for='password' >password </label>
		<input type='password' name='password' /><br />
		<input type='submit' name='submit' value='log in.' />
	</form>
	<p> <a href='register2.php'>new user?</a></p>
	</fieldset>
</div><!--// end #navVertical //-->

<?php
	//End IF user not logged in statements
	}
?>

<p>please choose your category. now.</p>

</div><!--// end #columnOne //-->


<div id="columnTwo">

<?php

	if(isset($_SESSION['pay_try'])) {
		echo "<FONT COLOR=\"CC0000\">please fill out all information and try again.<br /><br /></FONT>";
		unset($_SESSION['pay_try']);
	}
	if(isset($_SESSION['pay_good'])) {
		echo "<FONT COLOR=\"CC0000\">payment method added succesfully, please add another payment method or click banner to return to home page.<br /><br /></FONT>";
		unset($_SESSION['pay_good']);
	}
	if(isset($_SESSION['pay_bad'])) {
		echo "<FONT COLOR=\"CC0000\">unable to add payment method, please check information and try again.<br /><br /></FONT>";
		unset($_SESSION['pay_bad']);
	}
?>

<!-- BEGIN REGISTRATION FORM -->
<form id='payment' action='payment2.php' method='post'
    accept-charset='UTF-8'>
<fieldset >
<h2>payment profile.</h2>
<br />
<!-- DISPLAY ERRORS IF FAILED REGISTRATION -->
<?php
	//CHECK FOR USER LOGGED IN
?>
<input type='hidden' name='submitted' id='submitted' value='1'/><br />
<table >
<!-- USERNAME -->
<tr>
<td><label for='CCN' >card number. </label></td>
<td><input type='text' name='CCN' id='CCN' maxlength="16" /></td>
</tr>
<!-- PASSWORD -->
<tr>
<td><label for='exp' >expiration date. </label></td>
<td><input type='text' name='exp' id='exp' maxlength="10" /></td>
</tr>
<!-- FNAME -->
<tr>
<td><label for='CV2' >CV2 code. </label></td>
<td><input type='text' name='CV2' id='CV2' maxlength="4" /></td>
</tr>
<!-- FNAME -->
<tr>
<td><label for='name' >save this card as... </label></td>
<td><input type='text' name='name' id='name' maxlength="30" /></td>
</tr>
</table>

<input type='submit' name='submit' value='submit' />
</fieldset>
</form>

</div><!--// end #columnTwo //-->


<div id="columnThree">

<?php 
	//IF user logged in
	if(isset($_SESSION['userid'])) {
?>

<?php
	$checkRecent = checkRecent($_SESSION['userid']);
	if(($checkRecent['recent_1'] != null) && ($checkRecent['recent_2'] != null)) {
?>
		<h2>recent.</h2>
		<p>recently viewed items:</p>
<?php
		itemQuery($checkRecent['recent_1']);
		itemQuery($checkRecent['recent_2']);
	}
	else {
?>
		<h2>random.</h2>
		<p>random items:</p>
<?php
		printRandItem();
		printRandItem();
	}
?>

<?php
	//End IF user logged in
	}
	
	//IF user not logged in
	if(!isset($_SESSION['userid'])) {
?>

<h2>random.</h2>
<p>random items:</p>
<ul>
<li>user is not logged in.</li>
</ul>
<?php
	
	//print to page, edit FOR loop to adjust number of printed items
	printRandItem();
	printRandItem();
?>

<?php
	//End IF user not logged in
	}
?>

</div><!--// end #columnThree //-->

<div id="footer">
<p><a href="index.php" title="footer link">Home</a>&nbsp;|&nbsp;<a href="copyright.html"footer link">Copyright</a>&nbsp;|&nbsp;
<a href="privacy.html" title="footer link">Privacy Policy</a>&nbsp;|&nbsp;<a href="contact.html" title="footer link">Contact Us</a></p>
</div><!--// end #footer //-->

</div><!--// end #mainContainer //-->

</body>
</html>
