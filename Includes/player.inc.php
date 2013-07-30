<?php
if (isset($_SESSION) == false)
			session_start();
	//echo $_SESSION["current_file"];
	readfile($_SESSION["current_file"]); 
?>