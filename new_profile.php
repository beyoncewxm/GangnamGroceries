
<?php 
	//FORCE DISPLAY OF ERRORS TO BROWSER
	ini_set("display_errors", 1);
	ERROR_REPORTING(E_ALL);
	//INCLUDE CONNECTION DETAILS FROM FILE
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
<!--
<h1>gangnam // groceries</h1>
<p></p>
-->
<a href="index.php">
<img src='images/banner.png' alt='gangnam // groceries' width ='890' height = '90'>
</a>
</div><!--// end #header //-->

<div id="navHorizontal">
<!--
<ul>
<li><a href="#" title="Page Link">Products</a></li>
<li><a href="#" title="Page Link">Inventory</a></li>
<li><a href="#" title="Page Link">Items</a></li>
<li><a href="#" title="Page Link">Parts</a></li>
<li><a href="#" title="Page Link">Cart</a></li>
</ul>
-->

	<fieldset name='search'>
	<form method='GET' action='search.php'>
		
		<label for='search' >search for items: </label>
		<input type='text' name='search' />	
		<input type='submit' name='submit', value='search.' />
	</form>
	</fieldset>
</div><!--// end #navHorizontal //-->

<div id="columnOne">
<h2>login.</h2>

<div id="navVertical">
<!--
<ul>
<li><a href="#" title="Page Link">Newest</a></li>
<li><a href="#" title="Page Link">Bestest</a></li>
<li><a href="#" title="Page Link">Cheapest</a></li>
<li><a href="#" title="Page Link">Boldest</a></li>
<li><a href="#" title="Page Link">Goldest</a></li>
</ul>
-->
	<!-- DISPLAY LOGIN FORM -->
	<fieldset name = "login">
	<form method='POST' action='index.php'>

		<label for='username' >username </label>
		<input type='text' name='username' /><br />
		<label for='password' >password </label>
		<input type='password' name='password' /><br />
		<input type='submit' name='submit' value='log in.' />
	</form>
	<p> <a href='register.html'>new user?</a></p>
	</fieldset>
	
</div><!--// end #navVertical //-->

<p>please choose your category. now.</p>

</div><!--// end #columnOne //-->


<div id="columnTwo">

<!-- BEGIN REGISTRATION FORM -->
<form id='register' action='register3.php' method='post'
    accept-charset='UTF-8'>
<fieldset >
<h2>register.</h2>
<br />
<!-- DISPLAY ERRORS IF FAILED REGISTRATION -->
<?php
	//IF USER TRIED TO SKIP TO NEXT REGISTRATION PAGE
	if(isset($_SESSION['reg_try'])){
		echo "<FONT COLOR=\"CC0000\">please enter registration details to proceed.<br /><br /></FONT>";
		unset($_SESSION['reg_try']);
	}
	//IF REGISTRATION PROCESS FAILS
	if(isset($_SESSION['reg_fail'])) {
		echo "<FONT COLOR=\"CC0000\">registration updating failed.<br /><br /></FONT>";
		unset($_SESSION['reg_fail']);
	}
	//IF TRIED TO REGISTER W BLANK USERNAME
	if(isset($_SESSION['blank_user'])){
		echo "<FONT COLOR=\"CC0000\">username cannot be blank.<br /><br /></FONT>";
		unset($_SESSION['blank_user']);
	}
	//IF USER TRIED REGISTER WITH NON-MATCHING PASSWORDS
	if(isset($_SESSION['pass_match'])){
		echo "<FONT COLOR=\"CC0000\">passwords do not match.<br /><br /></FONT>";
		unset($_SESSION['pass_match']);
	}
	//IF USER TRIED REGISTER WITH IN USE EMAIL ADDRESS
	if(isset($_SESSION['bad_email'])){
		echo "<FONT COLOR=\"CC0000\">this email address is already in use.<br /><br /></FONT>";
		unset($_SESSION['bad_email']);
	}
?>
<input type='hidden' name='submitted' id='submitted' value='1'/><br />
<table >
<!-- CCN -->
<tr>
<td><label for='CCN' >16 Digit Card Number. </label></td>
<td><input type='text' name='CCN' id='CCN' maxlength="16" /></td>
</tr>
<!--3 Number CV2-->
<tr>
<td><label for='CV2' >3 Digit CV2. </label></td>
<td><input type='CV2' name='CV2' id='CV2' maxlength="3" /></td>
</tr>
<!--Card name-->
<tr>
<td>Card Name</td>
<td>
<select name='cardname'><option value='Visa'>Visa</option>
			<option value='MasterCard'>MasterCard</option>
			<option value='Discover'>Discover</option>
</select>
</td>
</tr>
<!--Expiration Date-->
<tr>
<td>Exp. Date.</td>
<td>
<label for='month'>Month:</label>
<select name = "month"><option value='01'>1</option>
		    <option value='02'>2</option>
		    <option value='03'>3</option>
		    <option value='04'>4</option>
		    <option value='05'>5</option>
		    <option value='06'>6</option>
		    <option value='07'>7</option>
		    <option value='08'>8</option>
		    <option value='09'>9</option>
		    <option value='10'>10</option>
		    <option value='11'>11</option>
		    <option value='12'>12</option>
</select>
<label for='year'>Year:</label>
<select name="year"><option value='2012'>2012</option>
		    <option value='2013'>2013</option>
		    <option value='2014'>2014</option>
		    <option value='2015'>2015</option>
		    <option value='2016'>2016</option>
		    <option value='2017'>2017</option>
</select>
</td>
</tr>
</table>

<input type='submit' name='submit' value='submit' />
</fieldset>
</form>

</div><!--// end #columnTwo //-->


<div id="columnThree">

<h2>random.</h2>
<p>random items:</p>
<div class="img">
  <a target="_blank" href="itemID=012.php">
  <img src="images/5-meat.png" alt="itemID=012" width="110" height="90">
  </a>
  <div class="desc">t-bone steak.</div>
</div>
<br />
<div class="img">
  <a target="_blank" href="itemID=203.php">
  <img src="images/4-vegetable.png" alt="itemID=203" width="110" height="90">
  </a>
  <div class="desc">fingerling potatoes.</div>
</div>
</div><!--// end #columnThree //-->

<div id="footer">
<p><a href="index.php" title="footer link">Home</a>&nbsp;|&nbsp;<a href="copyright.html"footer link">Copyright</a>&nbsp;|&nbsp;
<a href="privacy.html" title="footer link">Privacy Policy</a>&nbsp;|&nbsp;<a href="contact.html" title="footer link">Contact Us</a></p>
</div><!--// end #footer //-->

</div><!--// end #mainContainer //-->

</body>
</html>
