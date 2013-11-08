<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * This file triggeres the Kernel.FileSystem.inc.php copy functions
	 */
	//Include uri check
	//require_once ("checkuri.inc.php");
	if (isset($_SESSION) == false)
			session_start();
	$success = false;
	//Only progress if the user account is not a guest account
	if ($_SESSION["role"] != 3 && isGuest() == false){
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
			
			//Get Display name of the file to check if the file is existing in the target directory.
			$fileDisplayName = getFileByHash($file);
			//Check if file exists
			if (Guard::copyOrMoveFileValidator($file,$dir) == 0 && isFileExisting($fileDisplayName,$dir) == false){
				$success = true;
				//Check if user has enough space left.
				if (isSpaceLeft($dir) == true)
					copyFile($file,$dir);		
				else
					$success = false;
			}
			else
				$success = false;
			//Close database connection
			mysqli_close($connect);			
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
			if ((isset($_GET["source"]) &&  $_GET["source"] != $_GET["target"]) || (isset($_POST["source"]) &&  $_POST["source"] != $_POST["target"])){
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
				//Only progress if the directory did not exists in the target directory
				if (Guard::copyOrMoveFolderValidator($source,$old_root,$target) == 0 &&  isFileExisting($target.getDisplayName($source,$source)."/",$target) == false)
				{
					$success = true;
					//Check if enought space is available
					if (isSpaceLeft($source))
					{
						copyDir($source,$target,$old_root);	
						$success = true;
					}
					else
						$success = false;
				}
				else
					$success = false;
									
			}
		}
	}
	if (isset($_POST["method"]))
	{		
		if ($success == false)
			echo "false";
		else
			echo "true";
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