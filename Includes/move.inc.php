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
	require_once ("checkuri.inc.php");
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Remember a new date
	$uploadtime= date("D M j G:i:s T Y",time());
	$success = false;
	$redir ="";
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
			$displayname = getFileByHash($file);
			echo $displayname;	
			$success = true;
			if (fs_file_exists($displayname,$dir) == false)
				mysqli_query($connect,$sql) or die("Error: 015 ".mysqli_error($connect));	
			else 
				$success = false;
			mysqli_close($connect);
			$redir = $dir;
		
			//TODO: move check
		}
		else if ((isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["target"]) && isset($_POST["old_root"])))				
		{
			//TODO: More testing.
			if ($_GET["source"] != $_GET["target"]){
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
				if (fs_file_exists($target.getDisplayName($source,$source)."/",$target) == false)
					moveDir($source,$target,$old_root);
				else 
					$success = false;
				
			}
		}
	}
	if (isset($_POST["api_key"]))
	{
		echo "Command_Result:{$success}";
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