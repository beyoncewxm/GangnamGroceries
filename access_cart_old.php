<?php
    //FORCE DISPLAY OF ERRORS TO BROWSER
    //ini_set("display_errors", 1);
    //ERROR_REPORTING(E_ALL);
    //INCLUDE CONNECTION/MISC FUNCTION PAGES
    include "cart.php";
    include "function_page.php";
    include "db_connect.php";
	//BEGIN SESSION
    session_start();
	//boot back to the homepage if they aren't logged in
	if(!isset($_SESSION['userid']))
	{
		header('Location: index.php');
	}
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
<style>
td, th
{
padding:2px;
}
</style>
<title>gangnam // cart display</title>

<!-- My api key and stuff-->  
<script type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxkuS1U5mnIya8r23S4_wm5pUNHQNuI3c&sensor=false">
</script>

<!--This is all of the javascript. Isn't it neat?-->
<script type="text/javascript">

	/*var location1;
	var location2;
	
	var address1;
	var address2;

	var latlng;
	var geocoder;
	var map;
	
	var distance;
	var time;
	var time_minutes;*/
	
	// finds the coordinates for the two locations and calls the showMap() function
	function initialize()
	{
		geocoder = new google.maps.Geocoder(); // creating a new geocode object
		
		// getting the two address values
		address1 = document.getElementById("address1").value;
		address2 = document.getElementById("address2").value;
		
		// finding out the coordinates
		if (geocoder) 
		{
			geocoder.geocode( { 'address': address1}, function(results, status) 
			{
				if (status == google.maps.GeocoderStatus.OK) 
				{
					//location of first address (latitude + longitude)
					location1 = results[0].geometry.location;
				} else 
				{
					alert("Geocode was not successful for the following reason: " + status);
				}
			});
			geocoder.geocode( { 'address': address2}, function(results, status) 
			{
				if (status == google.maps.GeocoderStatus.OK) 
				{
					//location of second address (latitude + longitude)
					location2 = results[0].geometry.location;
					// calling the showMap() function to create and show the map 
					showMap();
				} else 
				{
					alert("Geocode was not successful for the following reason: " + status);
				}
			});
		}
	}
		
	// creates and shows the map
	function showMap()
	{
		// center of the map (compute the mean value between the two locations)
		latlng = new google.maps.LatLng((location1.lat()+location2.lat())/2,(location1.lng()+location2.lng())/2);
		
		// set map options
		// set zoom level
		// set center
		// map type
		var mapOptions = 
		{
			zoom: 1,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.HYBRID
		};
		
		// create a new map object
		// set the div id where it will be shown
		// set the map options
		map = new google.maps.Map(document.getElementById("map"), mapOptions);
		
		// show route between the points
		directionsService = new google.maps.DirectionsService();
		directionsDisplay = new google.maps.DirectionsRenderer(
		{
			suppressMarkers: true,
			suppressInfoWindows: true
		});
		directionsDisplay.setMap(map);
		var request = {
			origin:location1, 
			destination:location2,
			travelMode: google.maps.DirectionsTravelMode.DRIVING
		};
		directionsService.route(request, function(response, status) 
		{
			if (status == google.maps.DirectionsStatus.OK) 
			{
				directionsDisplay.setDirections(response);
				
				distance = response.routes[0].legs[0].distance.value;  //in meters
				time = response.routes[0].legs[0].duration.value; //in seconds
				time += 600; //add ten minutes for preparing the order
				time_minutes = Math.round(time/60); //convert to minutes and round to a whole number
				charge = distance/1609.34; //convert to miles
				charge = Math.round(charge*100)/100; //this rounds to 2 d. places
				charge_string = charge.toFixed(2); //this always displays 2 d. places, but is a string
				
				//display the minutes
				document.getElementById("time").innerHTML = "<br />Your order can arrive in "
				+ time_minutes +" minutes.";
				
				//display the the calcuated charge
				document.getElementById("chargeData").innerHTML = "$"+charge_string;
			
				//get the total from the table so you can add the charge and print it
				total = document.getElementById("orderTotal").innerHTML;
				total = parseFloat(total.replace(/\$/g, '')); //get rid of dollar signs
				total += charge; //add the charge
				total = total.toFixed(2); //always show two decimal places
				document.getElementById("finalTotal").innerHTML = "$"+total; //print it

				//write the charge and total to hidden inputs so that we 
				//can POST them after the button submit
				document.getElementById("chargeOutput").value = charge;
				document.getElementById("totalOutput").value = total;
			}
		});
		
		// create the markers for the two locations		
		var marker1 = new google.maps.Marker({
			map: map, 
			position: location1,
			title: "First location"
		});
		var marker2 = new google.maps.Marker({
			map: map, 
			position: location2,
			title: "Second location"
		});
		
		// create the text to be shown in the infowindows
		var text1 = '<div id="content">'+
				'<h3 id="firstHeading">Gangnam Groceries</h3>'+
				'<div id="bodyContent">'+
				'<p>W3033 Lafferre Hall<br></p>'+
				'</div>'+
				'</div>';
				
		var text2 = '<div id="content">'+
			'<h3 id="firstHeading">Delivery Destination</h3>'+
			'<div id="bodyContent">'+
			'<p>'+address2+'</p>'+
			'</div>'+
			'</div>';
		
		// create info boxes for the two markers
		var infowindow1 = new google.maps.InfoWindow({
			content: text1
		});
		var infowindow2 = new google.maps.InfoWindow({
			content: text2
		});

		// add action events so the info windows will be shown when the marker is clicked
		google.maps.event.addListener(marker1, 'click', function() {
			infowindow1.open(map,marker1);
		});
		google.maps.event.addListener(marker2, 'click', function() {
			infowindow2.open(map,marker2);
		});
	}
</script>
</head>
<body onload="initialize()">

	<!--BEGINNING OF SCOTT'S CODE FOR THE MAP. NOTHING IS OUTPUT-->
	<?php
		//session_start();
		
		//include "db_connect.php";
		$conn = connect_to_db();
		$query = "SELECT address, zip FROM gang.user_info WHERE userid = $1";
		$stmt = pg_prepare($conn, "get_address",$query);
		if($stmt){
			$result = pg_execute($conn, "get_address", array($_SESSION['userid']));
		
		}
		else{
			echo "Statement preparation failed. <br>";
		}

		$row = pg_fetch_assoc($result);
		$user_address = $row['address'];
		$user_zip = $row['zip'];

		/*add1 is the warehouse. It will be hardcoded.
		add2 is the user's address. It will be filled by a query
		that will return the user's address and user's zip*/
		//$user_address = "400 South 9th St.";
		//$user_zip = "65201";

				
		$add1 = "Lafferre Hall, University of Missouri, Columbia, MO 65201";
		$add2 = $user_address . " " . $user_zip;				
	?>
	<!--These hidden inputs let the javascript access the addresses-->
	<input type="hidden" name="address1" id="address1" value= '<?php echo $add1; ?>' size="50"/>
	<input type="hidden" name="address2" id="address2" value= '<?php echo $add2; ?>' size="50"/>
	
	<!--END OF SCOTT'S CODE FOR THE MAP-->

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
                <input type='submit' name='submit' value='search.' />
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
</div>
<div id="columnTwo">

<?php
/*First things first: we need to determine if the cart is empty*/


	$query = "SELECT orderid FROM gang.order AS o
		WHERE o.status = 'cart'
		AND o.userid = $1";

	$conn = connect_to_db();
	$stmt = pg_prepare($conn, "display2", $query);
	if($stmt)
	{
		$result = pg_execute($conn, "display2", array($_SESSION['userid']));
		$row = pg_fetch_assoc($result);
		$total = $row['total'];	
		$num_rows = pg_num_rows($result);
	}

//if($num_rows == 0 || $total == 0){ 
if($num_rows == 0) {
	echo "Your cart is empty. <br>
	You should really consider spending more money. <br>";
}
else{//this block is going to take up all of column 2


echo "<h2>Here is your cart:</h2><br>";

	//Dan's code works nicely. Here's my attempt. 
	
	/*This php displays the lineItems for the order with 'cart'
	status of the user logged in*/

	/*This super awesome query inner joins inventory with lineItem
	just to get the name of the item in the lineItem, and then inner
	joins that table with the order, matching orders to all of their
	orderids. A where clause then takes only those lineItems that match
	our user and have the status 'cart'*/

	$conn = connect_to_db();	
	$query = "SELECT price, name, quantity, amount, i.inventoryid FROM gang.inventory AS i
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
			<th>Subtotal</th>
			<th>Delete?</th></tr>";
		while($row = pg_fetch_assoc($result))
		{
			echo "<tr><td><a href='selected_item.php?id=".$row['inventoryid']."'>".$row['name']."</a></td><td>
				  <form method='POST' action = 'update_item.php'> 
					<input type='text' name='update' value=" . $row['quantity']." style='WIDTH: 30px'>"."
					@ $". $row['price'] . " each.
					<input type='hidden' name='inventory' value='". $row['inventoryid'] ."'>
					<input type='submit' name='submit' value='Update'>
			          </form></td><td>$"
				  .$row['amount']."</td><td>
				  <form method='POST' action='delete_item.php'>
					<input type = 'hidden' name = 'delete' value = '" . $row['inventoryid'] . "' />  
					<input type='submit' name='deleteSub' value= 'Delete'/>
				  </form></td></tr>";
		}
		
		//Now that the items have been printed, find the total
		$query = "SELECT * FROM gang.order WHERE userid=$1 AND status='cart'";
		$stmt = pg_prepare($conn, "findOrder", $query);
		if($stmt){
			$result = pg_execute($conn, "findOrder", array($_SESSION['userid']));
			$order = pg_fetch_assoc($result);
		}
		else{
			echo "Statement 2 preparation failed. <br>";
		}
		
		//print the total. It is named so javascript can grab it to compute the sum
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		      <tr><td>&nbsp;</td>
	              <td>Item Total: </td>
	              <td id='orderTotal'>$".$order['total']."</td></tr>";

		//print the charge. The td id='chargeData' is filled from javascript
		echo " <tr><td></td><td>Delivery Charge:</td>
		       <td id='chargeData'></td></tr>";
	
		//print the sum total of the total and charge
		echo "<tr><td></td><td>Final Total:</td>
			  <td id='finalTotal'></td></tr>";
		
	}
	else{  //if the first query fails to prepare
		echo "Statement 1 preparation failed. <br>";
	}
	echo "</table>";
	
echo "
<!--This is the div that holds the time estimate-->
<div id='time'></div>

<!--This div holds the Place Order button-->
<form id='info' action='order.php' method='POST' accept-charset='UTF-8'>
	<input type='hidden' name='totalOutput' id='totalOutput' size='50'/>
	<input type='hidden' name='chargeOutput' id='chargeOutput' size='50'/>
	<input type='submit' name='submit' value='Place Order' />
</form>

<br>
<!--This div holds the map-->
<div id='map' style='width:100%; height:250px';></div>";

}   //end of num_rows if block

echo "</div>"; //end of column two div
?>
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
</div>


</div>
</body>
</html>
