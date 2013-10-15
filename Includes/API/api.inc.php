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
	$authentificated = false;
	$token;
	if ($config["Api_Enable"] != 1)
	{
		echo "false";
		exit;
	}
	else
	{
		if ($authentificated == false)
		{
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
			
		}
	}
?>