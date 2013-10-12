<?php	
	exit;
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
			$authentificated = acknoledge($_POST["ACK"]);
			if (isset($_POST["ACKONLY"]))
				echo "ACK:".$authentificated;	
			$token = $_POST["ACK"];
		}			
		foreach ($_POST as $keyValue => $value) 
		{		
			if ($value == "getFiles")
			{
				$dir = $_POST["dir"];
				echo "LIST:".getFiles($dir,$token);
			}
			if ($value == "getProperty")
			{
				$id = $_POST["id"];
				echo "LIST:".getProperty($id,$token);
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