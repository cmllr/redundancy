<?php
	if (isset($_SESSION) == false)
			session_start();
	$success = false;
	//Only progress if the user account is not a guest account
	if ($_SESSION["role"] != 3){
		/*
			Case 1: User wants to copy a single file.
			Params: $_GET["file"] or $_POSt ["file"] -> source file			
			Params: $_GET["dir"] or $_POST["dir"] -> target directory
		*/
		if (isset($_GET["file"]) || isset($_POST["file"])){
			//Include the database file
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			//remember the two needed parameters (source file and target)
			if (isset($_GET["dir"]))
				$dir = mysqli_real_escape_string($connect,$_GET["dir"]);
			else
				$dir = mysqli_real_escape_string($connect,$_POST["dir"]);
			if (isset($_GET["file"]))			
				$file = mysqli_real_escape_string($connect,$_GET["file"]);	
			else
				$file = mysqli_real_escape_string($connect,$_POST["file"]);
			
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Re-Release of 1.9.8
			//Get Display name of the file to check if the file is existing in the target directory.
			$fileDisplayName = getFileByHash($file);
			//Check if file exists
			if (fs_file_exists($fileDisplayName,$dir) == false){
				$success = true;
				//Check if user has enough space left.
				if (fs_enough_space($dir) == true)
					fs_copyFile($file,$dir);		
<<<<<<< HEAD
=======
			$fileDisplayName = getFileByHash($file);
			echo $file;
			echo $dir;
			echo $dir;
			if (fs_file_exists($fileDisplayName,$dir) == false){
				$success = true;
				if (fs_enough_space($dir) == true)
					copyFile($file,$dir);		
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
=======
>>>>>>> Re-Release of 1.9.8
				else
					$success = false;
			}
			else
				$success = false;
<<<<<<< HEAD
<<<<<<< HEAD
			//Close database connection
			mysqli_close($connect);			
=======
				
			mysqli_close($connect);	
		
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
=======
			//Close database connection
			mysqli_close($connect);			
>>>>>>> Re-Release of 1.9.8
		}	
		/*
			Case 1: User wants to copy a folder file.
			Params: $_GET["source"] or $_POSt ["source"] -> source folder
			Params: $_GET["old_root"] or $_POST["old_root"] -> directory of the directory
			Params: $_GET["target"] or $_POST["target"] -> target directory
		*/
		else if ((isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["target"]) && isset($_POST["old_root"])))
		{
			//TODO: More testing.
			if ($_GET["source"] != $_GET["target"]){
				//Include database file
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
				//Get the needed values, source, target and the old root
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Re-Release of 1.9.8
				//Only progress if the directory did not exists in the target directory
				if (fs_file_exists($target.getDisplayName($source,$source)."/",$target) == false)
				{
					$success = true;
					//Check if enought space is available
					if (fs_enough_space($source))
					{
						fs_copyDir($source,$target,$old_root);	
<<<<<<< HEAD
=======
				//TODO: überschreibehilfe
				echo $target.getDisplayName($source,$source)."/";
				if (fs_file_exists($target.getDisplayName($source,$source)."/",$target) == false)
				{
					$success = true;
					if (fs_enough_space($source))
					{
						copyDir($source,$target,$old_root);	
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
=======
>>>>>>> Re-Release of 1.9.8
						$success = true;
					}
					else
						$success = false;
				}
				else
					$success = false;
<<<<<<< HEAD
<<<<<<< HEAD
									
=======
					
				//mysql_close($connect);	
				
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
=======
									
>>>>>>> Re-Release of 1.9.8
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