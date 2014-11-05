<?php
	session_start();	

	//echo '<pre>' . print_r($_POST, true) . '</pre>';

	//echo '<pre>' . print_r($_SESSION, true) . '</pre>';


	$charge = $_POST['chargeOutput'];
	$total = $_POST['totalOutput'];
	
	/*Run three updates. One to set the final total. One to set the charge
	One to set the status to 'final'*/
	include "db_connect.php";
	$conn = connect_to_db();
	$query = "UPDATE gang.order SET status='final', total=$1, charge=$2 WHERE userid = $3 AND status='cart'";
	$stmt = pg_prepare($conn, "update_order",$query);
	if($stmt){
		$success = pg_execute($conn, "update_order", array($total, $charge, $_SESSION['userid']));
	}
	else{
		echo "Statement preparation failed. <br>";
	}

	if($success){
		$_SESSION['order_placed']=1;
		header("Location: index.php");
		//echo "Your order has been placed. <br>";
	/*	echo "Not really, because that would mess up our testing. <br>
		      Once we're ready to roll, I'll uncomment the pg_execute <br>
		      and the status will be updated to 'final' and all of the totals <br>
		      will be finalized.";
	*/	
	}
?>
