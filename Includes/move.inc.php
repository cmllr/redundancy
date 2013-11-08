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
	 * The movement of files or folders is managed over this file
	 */
	 //Include uri check
	//require_once ("checkuri.inc.php");
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Remember a new date
	$uploadtime= date("Y-m-d H:i:s",time());
	$success = false;
	$redir ="";
	//Split between moving a file and moving a dir
	if ($_SESSION["role"] != 3 && isGuest() == false){
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
			if (Guard::copyOrMoveFileValidator($file,$dir) == 0){
				$timestamp = time();
				$modified= date("Y-m-d H:i:s",$timestamp);	
				$sql = "UPDATE Files SET Directory='$dir',lastWrite='$modified',Directory_ID=".getDirectoryID($dir)." WHERE Hash='$file'";
				$displayname = getFileByHash($file);
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo $displayname;	
				}
				$success = true;
				if (isFileExisting($displayname,$dir) == false)
					mysqli_query($connect,$sql) or die("Error: 015 ".mysqli_error($connect));	
				else 
					$success = false;
				mysqli_close($connect);
				$redir = $dir;
			}
			else			
			{
				$success = false;
			}
		}
		else if ((isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["target"]) && isset($_POST["old_root"])))				
		{
			//TODO: More testing.
			if ((isset($_GET["source"]) && ($_GET["source"] != $_GET["target"])) || (isset($_POST["source"]) && ($_POST["source"] != $_POST["target"]))){ 
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
				$redir = $target;
				$success = true;
				if (Guard::copyOrMoveFolderValidator($source,$old_root,$target) == 0 && isFileExisting($target.getDisplayName($source,$source)."/",$target) == false)
					moveDir($source,$target,$old_root);
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
	//Redirect the user if needed
	
	if ($GLOBALS["config"]["Program_Debug"] != 1){
		if ($success != true)
				header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]."&message=movefail&img=exclamation");
			else
				header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]."&message=movesuccess&img=accept");
			exit;
	}	
	
?>