<?php
	//Begin Session
	session_start();
	//Destroy Session
	session_destroy();
	//Forward back to home page
	header( 'Location: index.php' );
?>