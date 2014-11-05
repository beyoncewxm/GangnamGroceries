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
<div id = "selected_item">
<form method='GET' action='itemtest.php'>
<?php
require_once "function_page.php";

$conn = connect_to_db();
if($conn == 0)
{
        echo "Unable to connect to the database.";
        exit;
}
 
	$query = "SELECT * FROM gang.inventory WHERE inventoryid = $1";
	$stmt = pg_prepare($conn, "selected_item", $query);
	if ($stmt)
	{
		$id = empty($_GET['id']) ? $_SESSION['inventoryid']: $_GET['id'];
		$_SESSION['inventoryid']= $id;
		//echo $_SESSION['invertoryid'];
		$result = pg_execute($conn, "selected_item", array($id));
        	$row = pg_fetch_assoc($result);
        	echo $row['name']."</br>";
        	//echo "inventoryid:". $row['inventoryid']."</br>";
			//echo "warehouseid:".$row['warehouseid']."</br>";
        	echo "price: $".$row['price']."</br>";
        	//echo $row['stock']." in stock"."</br>";
        	echo "Description: ".$row['description']."</br>";
     		echo "Category: ".$row['category']."</br>";
		if($row['category']==1){
?>		
		<img src="images/5-meat.png" alt="Grain Category" width="110" height="90">
<?php
		}//end if
		else if($row['category']==2){
?>
		<img src="images/6-beverage.png" alt="Dairy Category" width="110" height="90">
<?php
		}//end if
		else if($row['category']==3){
?>
		<img src="images/3-fruit.png" alt="Fruit Category" width="110" height="90">
<?php
		}//end if
		else if($row['category']==4){
?>
		<img src="images/4-vegetable.png" alt="Vegetable Category" width="110" height="90">
<?php
		}//end if
		else if($row['category']==5){
?>
		<img src="images/1-grain.png" alt="Meat Category" width="110" height="90">
<?php
		}//end if
		else if($row['category']==6){
?>
		<img src="images/2-dairy.png" alt="Beverage Category" width="110" height="90">
<?php
		}//end else
	}//end if($stmt)
?>
</div>
<div>
	<input type='submit' name='AddtoCart' value='Add to Cart' />
</div>

<?php
	$query2 = "SELECT * FROM gang.comment WHERE inventoryid = $1";
        $stmt2 = pg_prepare($conn, "show_comment", $query2);
        if ($stmt2)
        {
                $result2 = pg_execute($conn, "show_comment", array($id));
		while ($row2 = pg_fetch_assoc($result2))
                {
                	echo "userid: ".$row2['userid']."</br>";
                	echo "comment: ".$row2['text']."</br>";
		}
	}
?>

<div id='content-wrapper'>
 <br />
        <form method='GET' action='selected_item.php'>
                <textarea name='commentfield' rows='15' cols='50'>Enter comments here.</textarea><br />
                <input type='submit' name='comment' value='comment' />
        </form>
</div>

<?php
       // echo $_SESSION['inventoryid'];

        //echo "inventoryid= ".$_SESSION['inventoryid']."</br>";
        $userid= empty($_SESSION['userid'])? 19: $_SESSION['userid'];
        //echo "userid = ".$userid."</br>";
        if(empty($_SESSION['userid']))

                echo "You haven't logged in, your comments will be anomynous.</br>";
        if(empty($_SESSION['inventoryid'])){
                echo "You haven't selected any item to comment on.</br>";

        }
        else{
                echo "Thank you for your time, your comments are highly valued!";

                $query_comment = "insert into gang.comment(userid, inventoryid, text) values($1, $2, $3)";
                //print_r($_SESSION);
                if (isset($_GET['comment'])) {
                        $user_comment = $_GET['commentfield'];
                        $stmt0= pg_prepare($conn, "insert_comment", $query_comment);
                        echo "it works 1</br>";
                        if($stmt0){
                                echo "userid= ".$userid."</br>";
                                echo "inventory_id= ".$_SESSION['inventoryid']."</br>";
                                echo "user_comment= ".$user_comment."</br>";

                                $result = pg_execute($conn, "insert_comment",array($userid, $_SESSION['inventoryid'], $user_comment));
 				echo "it works.<br/>";
                                header("Location: selected_item.php");
                        }//end if($stmt0)
                        else{
                                echo "pg_prepare failed!";
                        }//end else
                }//end if(isset($_GET['comment']))
        }//end else
?>

</div><!--// end #columnTwo //-->


<div id="columnThree">

<?php 
	//IF user logged in
	if(isset($_SESSION['userid'])) {
?>

<h2>random.</h2>
<p>random items:</p>
<ul>
<li>user is logged in.</li>
</ul>
<?php
	//print to page, edit FOR loop to adjust number of printed items
	printRandItem();
	printRandItem();
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
