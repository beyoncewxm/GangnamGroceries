<?php
	session_start();
	include "db_connect.php";
	$conn = connect_to_db();

	//find the order id	
	$query = "SELECT * FROM gang.order WHERE userid=$1 AND status='cart'";
	$stmt = pg_prepare($conn, "findOrder", $query);
	if($stmt)
	{
		$result = pg_execute($conn, "findOrder", array($_SESSION['userid']));
		$row = pg_fetch_assoc($result);
	}
	else{
		echo "Statement preparation failed. <br>";
	}
	$orderid = $row['orderid'];
	
	
	$new_quantity =	$_POST['update']; //holds the new quantity
	$new_quantity = (int)$new_quantity;
	if(is_int($new_quantity)){
		$query = "UPDATE gang.lineitem SET quantity = $1 WHERE orderid = $2 AND inventoryid = $3";
		$stmt = pg_prepare($conn, "update_quantity",$query);
		if($stmt){
			$result = pg_execute($conn, "update_quantity", array($new_quantity, $orderid, $_POST['inventory']));
			if($result){
				header('Location: access_cart.php');	
			}
			else{
				echo "Update failed. <br>";
			}	
		}
		else{
			echo "Statement preparation failed. <br>";
		}
	}
	else{
		header('Location: access_cart.php');	
	}
?>
