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
	 * this file calls api functions
	 * If new functions should be added, an exception must be added in this file, too.
	 */
	header("Content-Type: text/html; charset=utf-8");
	error_reporting(E_ALL);	
	$config = parse_ini_file("../../Redundancy.conf");
	$GLOBALS["Program_Dir"] = $GLOBALS["config"]["Program_Path"];
	include "../Program.inc.php";
	setExceptionHandler();
	if (isset($_SESSION) == false)
				session_start();	
	$_SESSION["authentificated"] = false;
	$token;
	if ($config["Api_Enable"] != 1)
	{
		echo "false";
		exit;
	}
	else
	{
		if(isset($_POST["method"]))
		{
<<<<<<< HEAD
			$authentificated = acknowledge($_POST["ACK"]);
			if (isset($_POST["ACKONLY"]))
				echo $authentificated;	
			$token = $_POST["ACK"];
		}			
		foreach ($_POST as $keyValue => $value) 
		{		
			if ($value == "getFiles")
			{
				$dir = $_POST["dir"];
				echo getFiles($dir,$token);
			}
			if ($value == "getProperty")
			{
				$id = $_POST["id"];
				echo getProperty($id,$token);
			}
			if ($value == "getContent")
			{
				$id = $_POST["id"];
				getContent($id,$token);
			}
			if ($value == "getName")
			{
				$id = $_POST["id"];
				echo getName($id,$token);
			}
			if ($value == "getVersion")
			{
				getVersion();
			}
			if ($value == "uploadFile")
			{			
				uploadFile();			
			}
			if ($value == "renameFile")
			{		
				renameFile();
			}
			if ($value == "renameFolder")
			{		
				renameFolder();
			}
			if ($value == "copy")
			{		
				copyFileOrFolder();
			}
			if ($value == "move")
			{		
				moveFileOrFolder();
			}
			if ($value == "getHash")
			{		
				getHash();
			}
			if ($value == "exists")
			{		
				exists();
			}
			if ($value == "createDir")
			{		
				newDir();
			}		
			if ($value == "deleteFile" || $value == "deleteFolder")
			{		
				delete();
			}
			
=======
			$method = $_POST["method"];
			switch($method)
			{
				case "getApiKey":
					echo getKey($_POST["userName"],$_POST["password"]);
					break;
				case "getFiles":
					$dir = $_POST["dir"];
					$token = $_POST["key"];
					echo getFiles($dir,$token);
					break;
				case "getFileHeadsAsXML":
					$dir = $_POST["dir"];
					$token = $_POST["key"];
					echo getFileHeadsAsXML($dir, $token);
					break;
				case "getPropertiesAsXML":
					$id = $_POST["id"];
					$token = $_POST["key"];
					echo getPropertiesAsXML($id,$token);
					break;
				case "getContent":
					$id = $_POST["id"];
					$token = $_POST["key"];
					getContent($id,$token);
					break;
				case "getName":
					$id = $_POST["id"];
					$token = $_POST["key"];
					echo getName($id,$token);
					break;
				case "getVersion":
					getVersion();
					break;
				case "uploadFile":
					uploadFile();
					break;					
				case "renameFile":	
					renameFile();
					break;
				case "renameFolder":	
					renameFolder();
					break;
				case "copy":	
					copyFileOrFolder();
					break;
				case "move":	
					moveFileOrFolder();
					break;
				case "getHash":	
					getHash();
					break;
				case "exists":
					exists();
					break;
				case "createDir":	
					newDir();
					break;	
				case "deleteFile":
				case "deleteFolder":	
					delete();
					break;
			}
>>>>>>> Update to 1.9.11-git-beta1-r3
		}
		
		// if ($_SESSION["authentificated"] == false)
		// {
			// $_SESSION["authentificated"] = login($_POST["userName"],$_POST["password"],false);
			// //if (isset($_POST["keyOnly"]))
			// {
				// if ($_SESSION["authentificated"] == true)
					// echo getKey($_POST["userName"]/*,$_POST["password"]*/);
				// else
					// echo "12";
			// }							
			// $token = getKeyByID(getIDByUsername($_POST["userName"]));
			
		// }			
		// foreach ($_POST as $keyValue => $value)
		// {
			// switch($value)
			// {
				// case "getFiles":
					// $dir = $_POST["dir"];
					// echo getFiles($dir,$token);
					// break;
				// // case "getFileIDsWithDisplaynamesAndFilenamesAndUploadDate":
					// // $dir = $_POST["dir"];
					// // echo getFileIDsWithDisplaynamesAndFilenamesAndUploadDate($dir, $token);
					// // break;
				// case "getFileHeadsAsXML":
					// $dir = $_POST["dir"];
					// $token = $_POST["token"];
					// echo getFileHeadsAsXML($dir, $token);
					// break;
				// // case "getProperties":
					// // $id = $_POST["id"];
					// // echo getProperties($id,$token);
					// // break;
				// case "getPropertiesAsXML":
					// $id = $_POST["id"];
					// echo getPropertiesAsXML($id,$token);
					// break;
				// case "getContent":
					// $id = $_POST["id"];
					// getContent($id,$token);
					// break;
				// case "getName":
					// $id = $_POST["id"];
					// echo getName($id,$token);
					// break;
				// case "getVersion":
					// getVersion();
					// break;
				// case "uploadFile":
					// uploadFile();
					// break;					
				// case "renameFile":	
					// renameFile();
					// break;
				// case "renameFolder":	
					// renameFolder();
					// break;
				// case "copy":	
					// copyFileOrFolder();
					// break;
				// case "move":	
					// moveFileOrFolder();
					// break;
				// case "getHash":	
					// getHash();
					// break;
				// case "exists":
					// exists();
					// break;
				// case "createDir":	
					// newDir();
					// break;	
				// case "getLatestFiles":
					// getLatestFiles($_POST["entries"],$token);
					// break;
				// case "deleteFile":
				// case "deleteFolder":	
					// delete();
					// break;
			// }
		// }
	}
?>