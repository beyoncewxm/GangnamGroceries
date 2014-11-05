<?php  //INCLUDED FILES

//GET CONNECTION DETAILS
include "db_connect.php";
?>

<?PHP //KICK USER TO VARIOUS PAGES

//Forward user back to registration page
function kickReg(){ //cjhzp9
	header( 'Location: http://babbage.cs.missouri.edu/~cs3380f12grp1/register2.php' );
}
//Forward user back to home page
function kickHome() { //cjhzp9
	header( 'Location: http://babbage.cs.missouri.edu/~cs3380f12grp1/' );
}
?>

<?PHP //PERFORM QUERIES FOR USER/AUTH INFO

//Get associative array for user
function checkUser($username) { //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//perform query
	pg_prepare($conn, "get_user", "SELECT * FROM gang.user_info WHERE email = $1");
	$result = pg_execute($conn, "get_user", array($username));

	RETURN pg_fetch_assoc($result);
}
function checkRecent($userid) { //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//perform query
	pg_prepare($conn, "get_recent", "SELECT * FROM gang.user_info WHERE userid = $1");
	$result = pg_execute($conn, "get_recent", array($userid));

	RETURN pg_fetch_assoc($result);
}
//insert new user info
function insertInfo($fname, $lname, $addr, $zip, $username, $phone){ //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//perform query
	pg_prepare($conn, "reg_info", "INSERT INTO gang.user_info (userid, fname, lname, address, zip, email, phone) VALUES(nextval('gang.user_info_userid_seq'), $1, $2, $3, $4, $5, $6)");

	RETURN pg_execute($conn, "reg_info", array($fname, $lname, $addr, $zip, $username, $phone));
}
//get userid from username
function selectUser($username){ //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//perform query
	pg_prepare($conn, "get_userid", "SELECT * FROM gang.user_info WHERE email = $1");
	$selectid_result = pg_execute($conn, "get_userid", array($username));
	$selection = pg_fetch_assoc($selectid_result);
	
	RETURN $selection['userid'];
}
//get user auth info from userid
function selectAuth($userid){ //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//perform query
	pg_prepare($conn, "get_auth", "SELECT * FROM gang.authentiation WHERE userid = $1");
	$selectid_result = pg_execute($conn, "get_auth", array($userid));
	
	RETURN pg_fetch_assoc($selectid_result);
}
//insert new user auth info
function insertAuth($userid, $prov_password) { //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//get salt
	$salt = getSalt(10);
	//get hashed password
	$hashedPass = hashPass($prov_password, $salt);
	
	//perform insert
	pg_prepare($conn, "reg_auth", "INSERT INTO gang.authentication (userid, password_hash, salt) VALUES($1, $2, $3)");

	RETURN pg_execute($conn, "reg_auth", array($userid, $hashedPass, $salt));
}
//INSERT NEW PAYMENT PROFILE
function insertPayment($userid, $CCN, $year, $month, $CV2, $name) {
	//INCLUDE CONNECTION DETAILS FROM FILE
	$conn = connect_to_db();

	//CONCATENATE YEAR AND MONTH FOR INSERTION
	$exp = $year . "/" . $month;

	//PERFORM THE QUERY
	$query = "INSERT INTO gang.payment_profile (userid, CCN, expiration, CV2, cardname)
		  VALUES ($1, $2, $3, $4, $5)";
	$stmt = pg_prepare($conn, "insert_payment", $query);
	$result = pg_execute($conn, "insert_payment", array($userid, $CCN, $exp, $CV2, $name));
	RETURN $result;
}
//FUNCTION TO SELECT ITEM, RETURN ASSOCIATIVE ARRAY
function selectItem($itemID) {
	//connect to db
	$conn = connect_to_db();
	
	//perform query for rand item
	$query = "SELECT * FROM gang.inventory WHERE inventoryid = $1";
	$stmt = pg_prepare($conn, "get_item", $query);
	$result = pg_execute($conn, "get_item", array($itemID));
	RETURN pg_fetch_assoc($result);
}
?>

<?PHP //PASSWORD MANAGEMENT

//get salt, send desired size
function getSalt($size) { //cjhzp9
	//generate salt
	$characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$salt = "";
	for($i=0; $i<$size; $i++) {
		$salt .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
	}
	
	RETURN $salt;
}
//Salt and hash a provided password for storage or retrieval
function hashPass($password, $salt) { //cjhzp9
	
	$password = sha1($salt.$password.$salt);
	
	RETURN $password;
}
//test provided password against database credentials
function testPass($userid, $password) { //cjhzp9
	//connect
	$conn = connect_to_db();
		
	//perform query
	pg_prepare($conn, "get_pass", "SELECT * FROM gang.authentication WHERE userid = $1");
	$pass_result = pg_execute($conn, "get_pass", array($userid));
	$pass_check = pg_fetch_assoc($pass_result);

	//fetch salt
	$salt = trim($pass_check['salt']);
	//fetch hashed pass
	$user_pass = $pass_check['password_hash'];
	//hash and salt provided password
	$test_pass = hashPass($password, $salt);
		
	//if match
	if($test_pass == $user_pass)
	{
		return 1;
	}
	//if no match
	else
	{
		return 0;
	}
}
//perform user login
function loginUser($username, $password){ 
	//connect
	$conn = connect_to_db();
	
	//perform query
	$query1 = "SELECT userid FROM gang.user_info WHERE email = $1";
	$stmt1 = pg_prepare($conn, "id_query", $query1);
	if($stmt1) {
		$result1 = pg_execute($conn, "id_query", array($username));
		$row1 = pg_fetch_assoc($result1);
	}
	else {
		echo "Failed to prepare statement for userid query.";
		exit;
	}
	
	//run a query that returns the hashed password and salt for a particular username
	$query = "SELECT password_hash, salt FROM gang.authentication WHERE userid = $1";
	$stmt = pg_prepare($conn, "login_query", $query);
	if($stmt){	
		$result = pg_execute($conn, "login_query", array($row1['userid']));
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
?>

<?PHP //FUNCTIONS FOR ACCESSING CART

//check if user has current order in 'cart' status
function hasCart($userid) { //cjhzp9
	//connect
	$conn = connect_to_db();
	//use a query to check if the user currently has a cart
	$query = "SELECT * FROM gang.order WHERE userid=$1 AND status='cart'";
	$stmt = pg_prepare($conn, "findOrder", $query);
	$result = pg_execute($conn, "findOrder", array($userid));
	//if user has cart, return 
	if($result) {
		RETURN pg_fetch_assoc($result);
	}
	//no cart, return null
	else {
		RETURN null;
	}
}
//retrieve current cart items
function getCartItems($orderid) { //cjhzp9
	//connect to database
	$conn = connect_to_db();
	//query for line items on order
	$query = "SELECT * FROM gang.lineItem WHERE orderid = $1";
	$stmt = pg_prepare($conn, "get_lines", $query);
	$result = pg_execute($conn, "get_lines", array($orderid));
	
	//for each row, call printCartItem function with itemID
	while($row = pg_fetch_assoc($result)) {
		$itemID = $row['inventoryid'];
		printCartItem($itemID);
	}

}
//add and item to current cart
function addItem($user, $item, $qty) { //cjhzp9
	//connect to db
	$conn = connect_to_db();	
	
	//check for current cart
	$cartCheck = hasCart($user);
	
	//if no current cart, add one
	if($cartCheck == null) {
		//get new cart
		$order = addNewCart($user);
		echo "order: ".$order."<br />";
	}
	else {
		$order = $cartCheck['orderid'];
		echo "order: ".$order."<br />";
	}
	
	//check if item already exists in order
	$exists = existsInOrder($order, $item);
	
	//item not in current cart, add it
	if($exists == 0) {
		addToCart($order, $item, $qty);
	}
	//item present in current cart, update qty
	else if($exists > 0) {
		addQty($order, $item, $qty);
	}
}
//Add a lineItem to given order
function addToCart($order, $item, $qty){ //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//perform query
	$query = "INSERT INTO gang.lineItem (orderid, inventoryid, quantity) VALUES($1, $2, $3)";
	$stmt = pg_prepare($conn, "ins_line", $query);
	$result = pg_execute($conn, "ins_line", array($order, $item, $qty));
}
function existsInOrder($order, $item){ //cjhzp9
	//connect
	$conn = connect_to_db();

	//perform query
	$query = "SELECT * FROM gang.lineItem WHERE orderid = $1 AND inventoryid = $2";
	$stmt = pg_prepare($conn, "line3", $query);
	$result = pg_execute($conn, "line3", array($order, $item));

	RETURN pg_num_rows($result);
}
//create a new order in 'cart' status
function addNewCart($user){ //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//get user's payment id
	$pid = getpid($user);
	if(!pid) {
		die();
	}
	
	//perform insert of new cart
	$query = "INSERT INTO gang.order(pid, userid, no_tax_total, charge, total, status) VALUES ($1, $2, $3, $4, $5, $6) RETURNING orderid";
	$stmt = pg_prepare($conn, "createCart", $query);
	$result = pg_execute($conn, "createCart", array($pid, $user, 0, 0, 0, 'cart'));
	$row = pg_fetch_row($result);
	
	RETURN $row[0];
}
//get payment id number for current user
function getpid($user){ //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//perform query
	$query = "SELECT * FROM gang.payment_profile WHERE userid = $1";
	$stmt = pg_prepare($conn, "get_pid", $query);
	$result = pg_execute($conn, "get_pid", array($user));
	$row = pg_fetch_assoc($result);
	
	RETURN $row['pid'];
}
//update quantity of current item in cart
function addQty($order, $item, $qty){ //cjhzp9
	//connect to db
	$conn = connect_to_db();
	
	//get current line item
	$query = "SELECT * FROM gang.lineItem WHERE orderid = $1 AND inventoryid = $2";
	$stmt = pg_prepare($conn, "get_qty", $query);
	$result = pg_execute($conn, "get_qty", array($order, $item));
	$row = pg_fetch_assoc($result);
	
	//add current qty to new qty
	$qty += $row['quantity'];
	
	//update line item with new qty
	$query2 = "UPDATE gang.lineItem SET quantity = $1 WHERE orderid = $2 AND inventoryid = $3";
	$stmt2 = pg_prepare($conn, "qty3", $query2);
	$result2 = pg_execute($conn, "qty3", array($qty, $order, $item));
}
//retrieve each cart item and print
function printCartItem($itemID) { //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//query for the item
	$query = "SELECT * FROM gang.inventory WHERE inventoryid = $1";
	$stmt = pg_prepare($conn, "get_item", $query);
	$result = pg_execute($conn, "get_item", array($itemID));
	
	//store to associative array
	$row = pg_fetch_assoc($result);
	
	//print item to cart display on page
	echo "<li>" . $row['name'] . " " . $row['price'] . "</li>";

}
?>

<?php //RANDOM ITEMS

function printRandItem() { //cjhzp9
	//connect
	$conn = connect_to_db();

	//get lower bound of inventory ID #'s
	$lowQuery = "SELECT min(inventoryid) FROM gang.inventory";
	$lowResult = pg_query($conn, $lowQuery);
	$low = pg_fetch_row($lowResult);
	//get upper bound of inventory ID #'s
	$highQuery = "SELECT max(inventoryid) FROM gang.inventory";
	$highResult = pg_query($conn, $highQuery);
	$high = pg_fetch_row($highResult);
	
	//get rand item number b/w bounds
	$itemID = rand($low[0], $high[0]);

	//call querying function
	itemQuery($itemID);
}
//query database for itemID
function itemQuery($itemID) { //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//perform query for rand item
	$query = "SELECT * FROM gang.inventory WHERE inventoryid = $1";
	$stmt = pg_prepare($conn, "myRandQuery", $query);
	$result = pg_execute($conn, "myRandQuery", array($itemID));
	$row = pg_fetch_assoc($result);
	
	//get category picture url
	$catPic = getCatPic($row['category']);
	
	printToPage($catPic, $row);
}
//get appropriate picture url for the category
function getCatPic($category) { //cjhzp9
	
	//get path for appropriate category picture
	switch($category) {
		case 1:
			$picture = "5-meat.png";
			break;
		case 2:
			$picture = "6-beverage.png";
			break;
		case 3:
			$picture = "3-fruit.png";
			break;
		case 4:
			$picture = "4-vegetable.png";
			break;
		case 5:
			$picture = "1-grain.png";
			break;
		case 6:
			$picture = "2-dairy.png";
			break;
		default:
			break;
	}
	
	RETURN $picture;
}

function printToPage($picture, $row) { //cjhzp9
	//print to item to page
	echo "
		<div class=\"img\">
		<a href=\"selected_item.php?id=" . $row['inventoryid'] . "\">
		<img src=\"images/". $picture . "\" alt=\"itemID=" . $itemID . "\" width=\"110\" height=\"90\">
		</a>
		<div class=\"desc\">" . $row['name'] . ".</div>
		</div>
		<br />";
	
}
?>

<?php
function storeRecent($userid, $itemid) { //cjhzp9
	//connect
	$conn = connect_to_db();
	
	//Retrieve recent_1
	pg_prepare($conn, "get_old", "SELECT recent_1 FROM gang.user_info WHERE userid = $1");
	$result = pg_execute($conn, "get_old", array($userid));
	$row = pg_fetch_row($result);
	
	//if current item is not the same as recent 1
	if($itemid != $row[0]) {
		//Move recent_1 to recent_2
		pg_prepare($conn, "move_old", "UPDATE gang.user_info SET recent_2 = $1 WHERE userid = $2");
		$stmt = pg_execute($conn, "move_old", array($row[0], $userid));
		//Add currently viewed item to recent_1
		pg_prepare($conn, "add_new", "UPDATE gang.user_info SET recent_1 = $1 WHERE userid = $2");
		pg_execute($conn, "add_new", array($itemid, $userid));
		
		RETURN;
	}
	else {
		RETURN;
	}
}
?>

<?php
//get URL of current page
function getURL() { //cjhzp9
	$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	if ($_SERVER["SERVER_PORT"] != "80")
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} 
	else 
	{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	RETURN $pageURL;
}

?>


<?PHP //DISPLAYS THE CART OF A USER

function getCartItemsTable(){
	/*This php displays the lineItems for the order with 'cart'
	status of the user logged in*/

	/*This super awesome query inner joins inventory with lineItem
	just to get the name of the item in the lineItem, and then inner
	joins that table with the order, matching orders to all of their
	orderids. A where clause then takes only those lineItems that match
	our user and have the status 'cart'*/

	$conn = connect_to_db();	
	$query = "SELECT name, quantity, amount FROM gang.inventory AS i
		INNER JOIN gang.lineItem AS l
		ON (i.inventoryid = l.inventoryid)
		INNER JOIN gang.order AS o
		ON (o.orderid = l.orderid)
		WHERE o.userid = $1
		AND o.status = 'cart'";

	$stmt = pg_prepare($conn,"display",$query);
	if ($stmt)
	{
		$result = pg_execute($conn,"display", array($_SESSION['userid']));
		echo "<table border='1'>
			<tr><th>Item Name</th>
			<th>Quantity</th>
				<th>Subtotal</th></tr>";
		while($row = pg_fetch_assoc($result))
		{
			echo "<tr><td>".$row['name']."</td><td>"
				  .$row['quantity']."</td><td>"
				  .$row['amount']."</td></tr>";
		}
		echo "<tr><td id='charge'>hi</td></tr>";
	}
	else{
		echo "Statement preparation failed. <br>";
	}
	echo "<tr><td id='charge'>hi</td></tr>";
	echo "</table>";


	/*alternative method using Cory's functions. Works, but
	outputs a list, not a table*/

	/*
	include "function_page.php";
	$query = "SELECT orderid FROM gang.order AS o
		WHERE o.status = 'cart'
		AND o.userid = $1";

	$conn = connect_to_db();
	$stmt = pg_prepare($conn, "display2", $query);
	if($stmt)
	{
		$result = pg_execute($conn, "display2", array($_SESSION['userid']));

		  echo "<table>
			<tr><th>Item Name</th>
			<th>Quantity</th>
				<th>Subtotal</th></tr>";
		while($row = pg_fetch_assoc($result))
		{
			getCartItems($row['orderid']);
		}
		echo "</table>";
	}
	else{
		echo "Statement preparation failed. <br>";
	}
	*/

}
?>
