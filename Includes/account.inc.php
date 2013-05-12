<div class = "contentWrapper">
<h1>User details</h1>
<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Include DataBase file
	include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
	//get the urrent user id an search for users with this id
	$id = mysql_real_escape_string($_SESSION["user_id"]);
	$result = mysql_query("Select * from Users  where ID = '$id' limit 1") or die("DataBase Error: 001 ".mysql_error());
	while ($row = mysql_fetch_object($result)) {
		echo "<b>Email:</b> ".$row->Email;	
		echo "<br><b>Username:</b> ".$row->User;
		if ($_SESSION["role"] != 3)
			echo "<br><p class = 'token'>API Token</p><input type ='text' cols='70' rows='2' value ='".$row->API_Key."'></input></p>";
		//If the config allows user deletion by the user himself, display a link
		if ($_SESSION["config"]["User_Allow_Delete"] == 1 )
			echo "<br><a href = 'index.php?module=goodbye'>Delete my account</a>";
	
	}
	//Close the connection if finished
	mysql_close($connect);
?>
</div>