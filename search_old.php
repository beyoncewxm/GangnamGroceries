<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Search Page</title>
<link rel="stylesheet" href="SearchForm.css" type="text/css" media="all">
</head>

<body>
<div id="mainContainer" class="clearfix">

<!--header back to home page-->
<div id="header">
<a href="index.php">
<img src='images/banner.png' alt='gangnam // groceries' width ='890' height = '90'>
</a>
</div>

<!--search field on the top-->
<form method='GET' action='search.php'>	
<div id="search_top">
	<fieldset name='search'>	
		<label for='search' >search for items: </label>
		<?php echo "<input type='text' name='search' value='". $_GET['search']."'/>"?>
		<input type='submit' name='submit' value='search.' />
	</fieldset>
</div>

<div id="columnOne"><!--end #columnOne -->

	<!--search field on the left-->
	<div id="search_left">
	<h2> Precise Search:</h2>
	<h5> Sort By</h5>
		<select name="sort">
		<?php

		$selected[0]='';
		$selected[1]='';
		$selected[2]='';
		
		if($_GET['sort'] == 'best_match') $selected[0]='selected';
		else if($_GET['sort'] == 'Asc') $selected[1]='selected';
		else $selected[2]='selected';
		
		echo "<option value='Desc'".$selected[2]."> High price first</option>";
		echo "<option value='Asc'".$selected[1]."> Lowest price first</option>";
		echo "<option value='best_match'".$selected[0].">Best Match</option>";
		?>
		</select>
	<h5> Price range</h5>
	From: <input type='text' name='new_val'/></br>
	To: <input type='text' name='new_val2'/></br>
	<input type='submit' name='submit' value='search.' />
	</div>
</form>

</div>
<?php
require_once "db_connect.php";

$conn = connect_to_db();
if($conn == 0)
{
	echo "Unable to connect to the database.";
	exit;
}
// All words that user typed in are assumed to be seperated by space(s)
if(isset($_GET['submit']))
{
	$key_word = array();
	$search_term = $_GET['search'];
	$pieces = explode(" ",$search_term);
	$sort = $_GET['sort'];
	$count = count($pieces);
	$max = $_GET['new_val2'];
	$min = $_GET['new_val'];
	
	if($min =='')
		$min = 0;
	if($max =='')
		$max = 99999;

	//Store the none space input into array key_word
	for($i = 0, $j = 0; $i < $count; $i++)
	{
		if($pieces[$i] != '')
			{
				$key_word[$j] = $pieces[$i];
				$j+=1;
			}
	}
	
	/* test purposes
	for($i = 0; $i<$j; $i++)
		echo $key_word[$i].'</br>';	
	*/
		
	//link all the keywords into one string for furture search query
	for($i =0; $i<$j; $i++){
		if($i == 0)
		{
			$search_keywords_and = $key_word[$i];
			$search_keywords_or = $key_word[$i];
		}
		else 
		{
			$search_keywords_and = $search_keywords_and. '&' . $key_word[$i];
			$search_keywords_or = $search_keywords_or. '|' . $key_word[$i];
		}
	}	
}
else 
	$category_id =$_GET['category'];
	
?>
<div id="table">
<?php
if(isset($_GET['submit']))
{
	if($sort == 'Asc')
	$query = "SELECT inventoryid, price, ts_headline(name, to_tsquery($1)) AS name,
	ts_headline(description, to_tsquery($2)) AS description
	FROM (SELECT name, description, inventoryid, price FROM 
	gang.inventory WHERE text_searchable_index @@ to_tsquery($3)) AS foo 
	WHERE (price>$4 AND price<$5) ORDER BY price Asc";
		
	else if($sort == 'Desc')
	$query = "SELECT inventoryid, price, ts_headline(name, to_tsquery($1)) AS name,
	ts_headline(description, to_tsquery($2)) AS description
	FROM (SELECT name, description, inventoryid, price FROM 
	gang.inventory WHERE text_searchable_index @@ to_tsquery($3)) AS foo 
	WHERE (price>$4 AND price<$5) ORDER BY price Desc";

	else
	$query = "SELECT inventoryid, price, ts_headline(name, to_tsquery($1)) AS name,
	ts_headline(description, to_tsquery($2)) AS description
	FROM (SELECT name, description, inventoryid, price FROM 
	gang.inventory WHERE text_searchable_index @@ to_tsquery($3)) AS foo
	WHERE (price>$4 AND price<$5)";
}
else 
	$query ="SELECT inventoryid, price, name, description FROM gang.inventory WHERE category=$1";
	
$stmt = pg_prepare($conn,"search",$query);
if ($stmt) 
		{
			if(isset($_GET['submit']))
			$result = pg_execute($conn,"search", array($search_keywords_or,$search_keywords_or,$search_keywords_and,$min,$max));
			else
			$result = pg_execute($conn,"search", array($category_id));
			
			$address = "selected_item.php";
			echo "<table><tr><th>Name</th><th>Price</th><th>Description</th></tr>";		
			while($row = pg_fetch_assoc($result))
			{
				echo "<tr><td>"."<a href=".$address."?id=".$row['inventoryid'].">".$row['name']."</a></td><td>"
				.'$'.$row['price']."</td><td>"
				.$row['description']."</td></tr>";	
			}
			
		}
?>
</table>
</div><!--//end #table //-->
</form>
<div id="footer">
<p><a href="index.php" title="footer link">Home</a>&nbsp;|&nbsp;<a href="copyright.html"footer link">Copyright</a>&nbsp;|&nbsp;
<a href="privacy.html" title="footer link">Privacy Policy</a>&nbsp;|&nbsp;<a href="contact.html" title="footer link">Contact Us</a></p>
</div><!--// end #footer //-->
</div><!--// end #mainContainer //-->
</body>
</html>
