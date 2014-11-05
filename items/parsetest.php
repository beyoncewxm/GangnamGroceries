<?php
	//get URL of current page
	$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	if ($_SERVER["SERVER_PORT"] != "80")
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} 
	else 
	{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}

	//parse URL to get each piece
	$urlArray = parse_url($pageURL);
	//store path only
	$urlPath = $urlArray['path'];
	echo $urlPath;
	//substring of path to get current directory
	$itemID = substr($urlPath, 16, 5);
	echo "<br />" . $itemID;
	
	if($itemID == 'items') {
		echo "<br />currently in items path";
	}
	else {
		echo "<br />not in items path";
	}

?>