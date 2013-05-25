<?php
	if (isset($_SESSION) == false)
			session_start();
	$success = false;
	if ($_SESSION["role"] != 3){
		if (isset($_GET["file"]) || isset($_POST["file"])){
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
			if (isset($_GET["dir"]))
				$dir = mysql_real_escape_string($_GET["dir"]);
			else
				$dir = mysql_real_escape_string($_POST["dir"]);
			if (isset($_GET["file"]))			
				$file = mysql_real_escape_string($_GET["file"]);	
			else
				$file = mysql_real_escape_string($_POST["file"]);
			copyFile($file,$dir);				
			mysql_close($connect);	
			$success = true;
		}	
		else if ((isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["target"]) && isset($_POST["old_root"])))
		{
		
			//Include database file
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
			if (isset($_GET["source"]))
				$source = mysql_real_escape_string($_GET["source"]); //directory to be moved
			else
				$source = mysql_real_escape_string($_POST["source"]); //directory to be moved
			if (isset($_GET["target"]))
				$target = mysql_real_escape_string($_GET["target"]);	//target
			else
				$target = mysql_real_escape_string($_POST["target"]);	//target
			if (isset($_GET["old_root"]))			
				$old_root = mysql_real_escape_string($_GET["old_root"]); // old root dir
			else
				$old_root = mysql_real_escape_string($_POST["old_root"]); // old root dir
			copyDir($source,$target,$old_root);	
			//mysql_close($connect);	
			$success = true;
		}
	}
	if (isset($_POST["api_key"]))
	{		
		echo "Command_Result:{$success}";
		exit;		
	}		
	header("Location: ./index.php?module=list&dir=/");
	exit;
	
?>