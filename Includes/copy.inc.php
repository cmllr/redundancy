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
			
			$fileDisplayName = getFileByHash($file);
			echo $file;
			echo $dir;
			echo $dir;
			if (fs_file_exists($fileDisplayName,$dir) == false){
				$success = true;
				if (fs_enough_space($dir) == true)
					copyFile($file,$dir);		
				else
					$success = false;
			}
			else
				$success = false;
				
			mysqli_close($connect);	
		
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
				//TODO: überschreibehilfe
				echo $target.getDisplayName($source,$source)."/";
				if (fs_file_exists($target.getDisplayName($source,$source)."/",$target) == false)
				{
					$success = true;
					if (fs_enough_space($source))
					{
						copyDir($source,$target,$old_root);	
						$success = true;
					}
					else
						$success = false;
				}
				else
					$success = false;
					
				//mysql_close($connect);	
				
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
			if ($success != true)
				header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]."&message=copyfail&img=exclamation");
			else
				header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]."&message=copysuccess&img=accept");
			exit;
		}	
	}
?>