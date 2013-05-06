<?php include "./Includes/gpl.inc.php";?>
<!doctype html>
<html>
<head>
<link rel = "stylesheet" href="./Style.css" type = "text/css"/>
<link rel="shortcut icon" href="./images/favicon.ico">
<title>
<?php
	//Display all the errors
	error_reporting(E_ALL);
	//Include the main program file
	include "./Includes/Program.inc.php";	
	//start a session if needed
	if (isset($_SESSION) == false)
		session_start();
	$_SESSION["Program_Dir"] = $_GLOBALS["Program_Path"];#
	//Display the Program name and calculate the user space
	echo $_GLOBALS["Program_Name_ALT"]; 
	if (isset($_SESSION["user_name"])){
		setUsedSpace($_SESSION['user_name']);		
	}
?>
</title>
<script type="text/javascript" language="JavaScript"
src="Core.js">
</script>
</head>
<body> 
<p id = "title">
<?php
	echo $_GLOBALS["Program_Name"];
?>
</p>
<div id = "body">
<?php
	//Create a new session, if no one exists
	if (isset($_SESSION) == false)
		session_start();
	if (isset($_SESSION["user_logged_in"]))
	{		
		//Include the status bar and menu and the wanted file
		include "./Includes/statusbar.inc.php";
		include $_SESSION["Program_Dir"]."Includes/Menu.inc.php";		
		if ($_SESSION["user_logged_in"] == true && isset($_GET["module"])){
			$path = $_SESSION["Program_Dir"]."Includes/".$_GET["module"].".inc.php";			
			if (file_exists($path))
				include $path;
		}
	}
	else
		include "./Includes/Login.inc.php";
	
?>
</div>
</body>
</html>
