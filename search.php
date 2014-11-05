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

<form method='GET' action='search.php'>	
<div id="navHorizontal">
	<fieldset name='search'>
		<label for='search' >search for items: </label>
		<?php echo "<input type='text' name='search' value='". $_GET['search']."'/>"?>	
		<input type='submit' name='submit', value='search.' />
	</fieldset>
</div><!--// end #navHorizontal //-->

<div id="columnOne">

	<h2> Precise Search:</h2>
	<h5> Sort By</h5>
		<select name="sort">
		<?php

		$selected[0]='';
		$selected[1]='';
		$selected[2]='';
		
		if($_GET['sort'] == 'best_match') $selected[0]='selected';
		else if($_GET['sort'] == 'Asc') $selected[1]='selected';
		else $selected[2]='selected';
		
		echo "<option value='Desc'".$selected[2]."> High price first</option>";
		echo "<option value='Asc'".$selected[1]."> Lowest price first</option>";
		echo "<option value='best_match'".$selected[0].">Best Match</option>";
		?>
		</select>
	<h5> Price range</h5>
	From: <input type='text' name='new_val'/></br>
	To: <input type='text' name='new_val2'/></br>
	<input type='submit' name='submit' value='search.' />
	
</form>


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
require_once "db_connect.php";

$conn = connect_to_db();
if($conn == 0)
{
	echo "Unable to connect to the database.";
	exit;
}
// All words that user typed in are assumed to be seperated by space(s)
if(isset($_GET['submit']))
{
	$key_word = array();
	$search_term = $_GET['search'];
	$pieces = explode(" ",$search_term);
	$sort = $_GET['sort'];
	$count = count($pieces);
	$max = $_GET['new_val2'];
	$min = $_GET['new_val'];
	
	if($min =='')
		$min = 0;
	if($max =='')
		$max = 99999;

	//Store the none space input into array key_word
	for($i = 0, $j = 0; $i < $count; $i++)
	{
		if($pieces[$i] != '')
			{
				$key_word[$j] = $pieces[$i];
				$j+=1;
			}
	}
		
	//link all the keywords into one string for furture search query
	for($i =0; $i<$j; $i++){
		if($i == 0)
		{
			$search_keywords_and = $key_word[$i];
			$search_keywords_or = $key_word[$i];
		}
		else 
		{
			$search_keywords_and = $search_keywords_and. '&' . $key_word[$i];
			$search_keywords_or = $search_keywords_or. '|' . $key_word[$i];
		}
	}	
}
else 
	$category_id =$_GET['category'];
	
?>
<div id="table">
<?php
if(isset($_GET['submit']))
{
	if($sort == 'Asc')
	$query = "SELECT inventoryid, price, ts_headline(name, to_tsquery($1)) AS name,
	ts_headline(description, to_tsquery($2)) AS description
	FROM (SELECT name, description, inventoryid, price FROM 
	gang.inventory WHERE text_searchable_index @@ to_tsquery($3)) AS foo 
	WHERE (price>$4 AND price<$5) ORDER BY price Asc";
		
	else if($sort == 'Desc')
	$query = "SELECT inventoryid, price, ts_headline(name, to_tsquery($1)) AS name,
	ts_headline(description, to_tsquery($2)) AS description
	FROM (SELECT name, description, inventoryid, price FROM 
	gang.inventory WHERE text_searchable_index @@ to_tsquery($3)) AS foo 
	WHERE (price>$4 AND price<$5) ORDER BY price Desc";

	else
	$query = "SELECT inventoryid, price, ts_headline(name, to_tsquery($1)) AS name,
	ts_headline(description, to_tsquery($2)) AS description
	FROM (SELECT name, description, inventoryid, price FROM 
	gang.inventory WHERE text_searchable_index @@ to_tsquery($3)) AS foo
	WHERE (price>$4 AND price<$5)";
}
else 
	$query ="SELECT inventoryid, price, name, description FROM gang.inventory WHERE category=$1";
	
$stmt = pg_prepare($conn,"search",$query);
if ($stmt) 
		{
			if(isset($_GET['submit']))
			$result = pg_execute($conn,"search", array($search_keywords_or,$search_keywords_or,$search_keywords_and,$min,$max));
			else
			$result = pg_execute($conn,"search", array($category_id));
			
			if(pg_num_rows($result) == 0)
				echo "None active listings";
			else
			{
				$address = "selected_item.php";
				echo "<table><tr><th>Name</th><th>Price</th><th>Description</th></tr>";		
				while($row = pg_fetch_assoc($result))
				{
					echo "<tr><td>"."<a href=".$address."?id=".$row['inventoryid'].">".$row['name']."</a></td><td>"
					.'$'.$row['price']."</td><td>"
					.$row['description']."</td></tr>";	
				}
			}
			
		}
?>
</table>
</div><!--//end #table //-->
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
