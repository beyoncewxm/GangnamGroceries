<?php
//This is a function I started working on to add something to the cart. It isn't finished yet. -Dan
include"db_connect.php";
	function add_to_cart($userid, $itemid, $quantity, $price)
	{
		//connect to the database
		$conn = connect_to_db();
		//use a query to check if the user currently has a cart
		$query = "SELECT * FROM gang.order WHERE userid=$1 AND status=$2";
		$stmt = pg_prepare($conn, "findOrder", $query);
		if($stmt)
		{
			$result = pg_execute($conn, "findOrder", array($userid, "cart"));
			//if there is a cart, continue to add something to it
			$row = pg_fetch_assoc($result);
			if($row)
			{
				//create a new query, this time to add a line item to the cart
				$query = "INSERT INTO gang.lineitem(orderid, inventoryid, quantity, amount) VALUES ($1, $2, $3, $4)";
				$stmt = pg_prepare($conn, "addItem", $query);
				if($stmt)
				{
					$total = $quantity * $price;
					$result = pg_execute($conn, "addItem", array($row['orderid'], $itemid, $quantity, $total));
					//increase the total price of the order
					$query = "UPDATE gang.order SET no_tax_total=$1, total=$2 WHERE orderid=$3";
					$stmt = pg_prepare($conn, "changeOrder", $query);
					if($stmt)
					{
						//this needs to be changed once we get the google API stuff working
						$result = pg_execute($conn, "changeOrder", array($row['no_tax_total'] + $total, $row['total'] + $total, $row['orderid']));
					}
				}
			//otherwise, make a new cart and then add something to it
			} else
			{
				//create a query to add a new order to the order table
				$query = "INSERT INTO gang.order(pid, userid, no_tax_total, charge, total, status, est_delivery_time) VALUES ($1, $2, $3, $4, $5, $6, $7)";
				$stmt = pg_prepare($conn, "createCart", $query);
				if($stmt)
				{
					$total = $quantity * $price;
					//this needs to be changed once we get the purchase info implemented, and the google API stuff, until then i'm just hardcoding values in
					$result = pg_execute($conn, "createCart", array(1, $userid, $total, 5.00, $total + 5.00, "cart", time()));
					//get the order id for the one just created
					$result = pg_execute($conn, "findOrder", array($userid, "cart"));
					$row = pg_fetch_assoc($result);
					//now add the item into the cart
					$query = "INSERT INTO gang.lineitem(orderid, inventoryid, quantity, amount) VALUES ($1, $2, $3, $4)";
					$stmt = pg_prepare($conn, "addItem", $query);
					if($stmt)
					{
						$result = pg_execute($conn, "addItem", array($row['orderid'], $itemid, $quantity, $total));
					}
				}
			}
		}
	}
	//function to delete an item from the cart
	function delete_from_cart($userid, $itemid)
	{
		//connect to database
		$conn = connect_to_db();
		//start by doing a querty to be sure the user even has a cart
		$query = "SELECT * FROM gang.order WHERE userid=$1 AND status=$2";
		$stmt = pg_prepare($conn, "findCart", $query);
		if($stmt)
		{
			$result = pg_execute($conn, "findCart", array($userid, "cart"));
			$row = pg_fetch_assoc($result);
			//if the user has a cart, continue
			if($row)
			{
				//execute a query to be sure the item is within the cart
				$query = "SELECT * FROM gang.lineitem WHERE orderid=$1 AND itemid=$2";
				$stmt = pg_prepare($conn, "findItem", $query);
				if($stmt)
				{
					$result = pg_execute($conn, "findItem", array($row['orderid'], $itemid));
					$check = pg_fetch_assoc($result);
					//if the item is within the cart, delete it
					if($check)
					{
						//deletion query
						$query = "DELETE FROM gang.lineitem WHERE orderid=$1 AND itemid=$2";
						$stmt = pg_prepare($conn, "deleteItem", $query);
						if($stmt)
						{
							$result = pg_execute($conn, "deleteItem", array($row['orderid'], $itemid));
						}
					}
				}
			}
		}
	}
	//function that simply returns the cart
	function get_cart($userid)
	{
		//make sure a cart exists
		$conn = connect_to_db();
		$query = "SELECT * FROM gang.order WHERE userid=$1 AND status=$2";
		$stmt = pg_prepare($conn, "findCart", $query);
		if($stmt)
		{
			$result = pg_execute($conn, "findCart", array($userid, "cart"));
			$row = pg_fetch_assoc($result);
			if($row)
			{
				//return every item in the cart
				$query = "SELECT * FROM gang.lineitem WHERE userid=$1 AND orderid=$2";
				$stmt = pg_prepare($conn, "getCart", $query);
				if($stmt)
				{
					$result = pg_execute($conn, "getCart", array($userid, $row['orderid']));
					return $result;
				}
			} else
			{
				$row = NULL;
			}
		}
		return NULL;
	}
	//function to get the order info of the cart
	function get_cart_info($userid)
	{
                //simple query to get order info
                $conn = connect_to_db();
                $query = "SELECT * FROM gang.order WHERE userid=$1 AND status=$2";
                $stmt = pg_prepare($conn, "findCart", $query);
                if($stmt)
                {
                        $result = pg_execute($conn, "findCart", array($userid, "cart"));
                        $row = pg_fetch_assoc($result);
			return $row;
		}
		return NULL;
	}
	//give it the row from the cart, and it'll spit back out the item information
	function get_items($row)
	{
		$conn = connect_to_db();
		if($row)
		{
			//find the item information based on the item id from the row
			$query = "SELECT * FROM gang.inventory WHERE inventoryid=$1";
			$stmt = pg_prepare($conn, "findItem", $query);
			if($stmt)
			{
				$result = pg_execute($conn, "findItem", array($row['inventoryid']));
				$item = pg_fetch_assoc($result);
				return $item;
			}
		}
		return NULL;
	}
?>
