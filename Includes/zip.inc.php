<?php
if (isset($_SESSION) == false);
		session_start();
	if (isset($_GET["dir"]) )
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$dir = mysqli_real_escape_string($connect,$_GET["dir"]);
		startZipCreation($dir);
	}
?>