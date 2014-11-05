<?php
//I decided to get started on the other part that I'm going to be doing, a function to actually purchase something. Figured it would be pretty simple, the way our cart is implemented. -Dan
	include"db_connect.php";
	function purchase_cart($userid, $pid)
	{
		//connect to database
		$conn = db_connect();
		//start by finding the user's cart to be sure they actually have one
		$query = "SELECT * FROM gang.order WHERE userid=$1 AND status='cart'";
		$stmt = pg_prepare($conn, "findOrder", $query);
		if($stmt)
		{
			$result = pg_execute($conn, "findOrder", array($userid));
			$row = pg_fetch_assoc($result);
			//if they have a cart, get with the purchasing
			if($row)
			{
				//use the user's order and see if they are using different credit card data to purchase than is already stored with the order
				if($row['pid'] != $pid)
				{
					//if they're not using the already stored credit card information, fix it
					$query = "UPDATE gang.order SET pid=$1 WHERE userid=$2 AND status='cart'";
					$stmt = pg_prepare($conn, "updatePurchase", $query);
					if($stmt)
					{
						$result = pg_execute($conn, "updatePurchase", array($pid, $userid));
					}
				}
				//now that the appropriate payment information is definitely there, change the status and be done with it
				$query = "UPDATE gang.order SET status=$1 WHERE userid=$2 AND status=$3";
				$stmt = pg_prepare($conn, "purchaseCart", $query);
				if($stmt)
				{
					$result = pg_execute($conn, "purchaseCart", array("purchased", $userid, "cart"));
				}
				return(True);
			//otherwise, return false to show that the cart was not purchased
			} else
			{
				return(False);
			}
		}
	}
?>
