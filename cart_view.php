<!--
Credit for the javascript code goes largely to Irina Borozan. See:
www.1stwebdesigner.com/tutorials/distance-finder-google-maps-api/
-->

<html>
<head>
<title>Review Cart</title>

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
		map = new google.maps.Map(document.getElementById("columnOne"), mapOptions);
		
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
				
				//display the minutes
				document.getElementById("results").innerHTML = "<br />Your order can arrive in "
				+ time_minutes +" minutes.";
				
				//write the distance to an input so that we can POST it after they confirm
				document.getElementById("distance").value = distance;
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
				'<p>W3033 Lafferre Hall<br>'+
				'University of Missouri, Columbia</p>'+
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

	<!--These hidden inputs let the javascript access the addresses-->
	<input type="hidden" name="address1" id="address1" value= '<?php echo $add1; ?>' size="50"/>
	<input type="hidden" name="address2" id="address2" value= '<?php echo $add2; ?>' size="50"/>
	<?php
		session_start();
		
		include "db_connect.php";
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


<?php
/*This php displays the lineItems for the order with 'cart'
status of the user logged in*/

/*This super awesome query inner joins inventory with lineItem
just to get the name of the item in the lineItem, and then inner
joins that table with the order, matching orders to all of their
orderids. A where clause then takes only those lineItems that match
our user and have the status 'cart'*/

$query = "SELECT name, quantity, amount FROM gang.inventory AS i
	INNER JOIN gang.lineItem AS l
	ON (i.inventoryid = l.inventoryid)
	INNER JOIN gang.order AS o
	ON (o.orderid = l.orderid)
	WHERE o.userid = $1
	AND o.status = 'cart'";

$conn = connect_to_db();
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
}
else{
	echo "Statement preparation failed. <br>";
}
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

?>

<!--This is the div that holds the time estimate-->
<div style="text-align:left;width:100%; height:8%" id="results"></div>

<!--Place order button-->
<form id='info' action='order.php' method='POST' accept-charset='UTF-8'>
	<input type="hidden" name="distance" id="distance" size="50"/>
	<input type='submit' name='submit' value='Place Order' />
</form>

<!--This is the div that holds the map-->
<div style="text-align:left;width:40%; height:40%" id="columnOne"></div>


</body>
</html>


