<div id = "login">
<h1>User details</h1>
<?php
	if (isset($_SESSION) == false)
			session_start();
	include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
	$id = mysql_real_escape_string($_SESSION["user_id"]);
	$result = mysql_query("Select * from Users  where ID = '$id'") or die("Error: ".mysql_error());
	while ($row = mysql_fetch_object($result)) {
		echo "<b>Email:</b> ".$row->Email;	
		echo "<br><b>Username:</b> ".$row->User;
	}
	mysql_close($connect);
?>
</div>
