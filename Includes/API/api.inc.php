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
		}
	}
?>