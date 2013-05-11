<?php
	//start a session if needed
	if (isset($_SESSION) == false)
		session_start();
	//Only proceed if the email= parameter is set
	if (isset($_GET["email"]))
	{
		//Include DataBase file
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";		
		//get the Email
		$email = mysql_real_escape_string($_GET["email"]);			
		$sql = "UPDATE Users SET Enabled=1 WHERE Email='$email'" ;
		//Update the data row
		mysql_query($sql) or die("DataBase Error: 002 ".mysql_error());;
		header("Location: ./index.php?message=1");
	}
?>