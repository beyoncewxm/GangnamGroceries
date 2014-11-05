<?php
//php script to delete an item
session_start();
include"db_connect.php";

echo '<pre>' . print_r($_POST, true) . '</pre>';


$conn = connect_to_db();
$itemid = $_POST['delete'];
//get the order id
$query = "SELECT * FROM gang.order WHERE userid=$1";
$stmt = pg_prepare($conn, "getOrder", $query);
if($stmt)
{
	$result = pg_execute($conn, "getOrder", array($_SESSION['userid']));
	$row = pg_fetch_assoc($result);
	$orderid = $row['orderid'];
	//delete the item from the cart that has the three appropriate id's
	$query = "DELETE FROM gang.lineitem WHERE orderid=$1 AND inventoryid=$2";
	$stmt = pg_prepare($conn, "deleteItem", $query);
	if($stmt)
	{
		$result = pg_execute($conn, "deleteItem", array($orderid, $_POST['delete']));
		/* 
		   Functional, yet obsolete code,
		   Is obsolete nonetheless.
			
				-Walt Whitman

		//update the total price of the order
		$subtotal = $row['no_tax_total'];
		$total = $row['total'];
		$query = "SELECT price FROM gang.inventory WHERE inventoryid=$1";
		$stmt = pg_prepare($conn, "getItemInfo", $query);
		if($stmt)
		{
			$result = pg_execute($conn, "getItemInfo", array($_POST['delete']));
			$row = pg_fetch_assoc($result);
			$price = $row['price'];
			$query = "UPDATE gang.order SET no_tax_total=$1, total=$2";
			$stmt = pg_prepare($conn, "changeTotal", $query);
			if($stmt)
			{
				$result = pg_execute($conn, "changeTotal", array($subtotal-$price, $total-$price));
			}
		}*/
	}
}
header('Location: access_cart.php');
?>
