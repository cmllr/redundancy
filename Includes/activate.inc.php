<?php
	//Only proceed if the email= parameter is set
	if (isset($_GET["email"]))
	{
		//Include DataBase file
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		//get the Email
		$email = mysqli_real_escape_string($connect,$_GET["email"]);			
		$sql = "UPDATE Users SET Enabled=1 WHERE Email='$email'" ;
		//Update the data row
		mysqli_query($sql) or die("DataBase Error: 002 ".mysqli_error($connect));
		header("Location: ./index.php?message=user_enabled");
	}
?>