<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Remember a new date
	$uploadtime= date("D M j G:i:s T Y",time());
	$success = false;
	//Split between moving a file and moving a dir
	if ($_SESSION["role"] != 3){
		if ((isset($_GET["file"]) && isset($_GET["dir"])) || (isset($_POST["file"]) && isset($_POST["dir"]))){
			//Include database file
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
			//Remember values and process the (virtual) copy			
			if (isset($_GET["dir"]))
				$dir = mysqli_real_escape_string($connect,$_GET["dir"]);
			else
				$dir = mysqli_real_escape_string($connect,$_POST["dir"]);
			if (isset($_GET["file"]))		
				$file = mysqli_real_escape_string($connect,$_GET["file"]);	
			else
				$file = mysqli_real_escape_string($connect,$_POST["file"]);
			$sql = "UPDATE Files SET Directory='$dir',Directory_ID=".getDirectoryID($dir).",Uploaded='$uploadtime' WHERE Hash='$file'";			
			mysqli_query($connect,$sql) or die("Error: 015 ".mysqli_error($connect));		
			mysqli_close($connect);
			$success = true;
		}
		else if ((isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["target"]) && isset($_POST["old_root"])))				
		{
			//Include database file
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
			/*$source = mysqli_real_escape_string($connect,$_GET["source"]); //directory to be moved
			$target = mysqli_real_escape_string($connect,$_GET["target"]);	//target
			$old_root = mysqli_real_escape_string($connect,$_GET["old_root"]); // old root dir*/
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
			moveDir($source,$target,$old_root);
			$success = true;
		}
	}
	if (isset($_POST["api_key"]))
	{
		echo "Command_Result:{$success}";
		exit;	
	}	
	//Redirect the user if needed
	
	if (!isset($_GET["noredir"])){
			header("Location: ./index.php?module=list&dir=/");
			exit;
	}	
	
?>