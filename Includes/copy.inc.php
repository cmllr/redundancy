<?php
	if (isset($_SESSION) == false)
			session_start();
	$success = false;
	if ($_SESSION["role"] != 3){
		if (isset($_GET["file"]) || isset($_POST["file"])){
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
			if (isset($_GET["dir"]))
				$dir = mysqli_real_escape_string($connect,$_GET["dir"]);
			else
				$dir = mysqli_real_escape_string($connect,$_POST["dir"]);
			if (isset($_GET["file"]))			
				$file = mysqli_real_escape_string($connect,$_GET["file"]);	
			else
				$file = mysqli_real_escape_string($connect,$_POST["file"]);
			
			if (fs_file_exists($file,$dir) == false){
				copyFile($file,$dir);		
			}
			echo 	"existing:".fs_file_exists($file,$dir);		
			mysqli_close($connect);	
			$success = true;
		}	
		else if ((isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["target"]) && isset($_POST["old_root"])))
		{
			//TODO: More testing.
			if ($_GET["source"] != $_GET["target"]){
				//Include database file
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
				if (isset($_GET["source"]))
					$source = mysqli_real_escape_string($connect,$_GET["source"]); //directory to be moved
				else
					$source = mysqli_real_escape_string($connect,$_POST["source"]); //directory to be moved
				if (isset($_GET["target"]))
					$target = mysqli_real_escape_string($connect,$_GET["target"]);	//target
				else
					$target = mysqli_real_escape_string($connect,$_POST["target"]);	//target
				if (isset($_GET["old_root"]))			
					$old_root = mysqli_real_escape_string($connect,$_GET["old_root"]); // old root dir
				else
					$old_root = mysqli_real_escape_string($connect,$_POST["old_root"]); // old root dir
				copyDir($source,$target,$old_root);	
				//mysql_close($connect);	
				$success = true;
			}
		}
	}
	if (isset($_POST["api_key"]))
	{		
		echo "Command_Result:{$success}";
		exit;		
	}
	else{	
		if ($GLOBALS["config"]["Program_Debug"] != 1){
			header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]);
			exit;
		}	
	}
?>