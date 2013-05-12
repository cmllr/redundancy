<?php
	error_reporting(E_ALL);
	if (isset($_SESSION) == false)
		session_start();	
	$config = parse_ini_file("../../Redundancy.conf");
	include "../Program.inc.php";
	if ($config["Api_Enable"] != 1)
	{
		echo "Error:{API_Enable=0;}\n";
		exit;
	}			
	//acknoledge	
	$_SESSION["acknoledge"] = false;
	$_SESSION['user_id'] = "";
	$_SESSION["user_name"] = "";
	if (isset($_POST["api_key"]))
	{	
		include "../DataBase.inc.php";			
		$key = mysql_real_escape_string($_POST["api_key"]);
		$result = mysql_query("Select * from Users  where API_Key = '$key'") or die("Error: ".mysql_error());		
		while ($row = mysql_fetch_object($result)) {
			$_SESSION['user_id'] = $row->ID;	
			$_SESSION["user_name"] = $row->User;
			$_SESSION["role"] = $row->Role;
			if ($row->Enabled != 1 || $row->Enable_API != 1)
			{
				echo "Error{User_Disabled=1;}\n";				
				$_SESSION["acknoledge"] = false;
			}
			else
			{
				//echo "Status:{User_Enabled=1;}\n";
				$_SESSION["acknoledge"] = true;
			}
		}
	}	
	include "../DataBase.inc.php";			
	if (isset($_POST["directory"]))
		$_SESSION["directory"] = mysql_real_escape_string($_POST["directory"]);
	else
		$_SESSION["directory"] = "/";
		
	$_SESSION["currentdir"] = "/";
	$_SESSION["Program_Dir"] = $config["Program_Path"];
	mysql_close($connect);
	foreach ($_POST as $keyValue => $value) 
	{		
		include "../DataBase.inc.php";
		if ($value == "getUserName"){			
			$result = mysql_query("Select * from Users  where API_Key = '$key' ") or die("Error: ".mysql_error());
			while ($row = mysql_fetch_object($result)) {
				echo "Command_Result:{".$row->User.";}\n";
			}	
			mysql_close($connect);			
		}
		if ($value == "getUserSpace"){
			$result = mysql_query("Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysql_error());
			while ($row = mysql_fetch_object($result)) {
				echo "Command_Result:{".$row->Storage.";}\n";
			}
			mysql_close($connect);
		}
		if ($value == "getFiles"){
			$files = "";
			$id = "";
			$result = mysql_query("Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysql_error());
			while ($row = mysql_fetch_object($result)) {
				$id = $row->ID;
			}			
			$result = mysql_query("Select * from Files  where UserID = '$id' and Directory = '".$_SESSION["directory"]."'") or die("Error: ".mysql_error());
			while ($row = mysql_fetch_object($result)) {
				if ($row->Displayname == $row->Filename)
					$files .= $row->Displayname.";".$row->Displayname.".dat;";
				else
					$files .= $row->Displayname.";".$row->Filename.";";
			}
			echo $files."\n";
			mysql_close($connect);
		}
		if ($value == "getUsedSpace"){
			$size = "";
			$id = "";
			$result = mysql_query("Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysql_error());
			while ($row = mysql_fetch_object($result)) {
				$id = $row->ID;
			}			
			$result = mysql_query("Select * from Files  where UserID = '$id'") or die("Error: ".mysql_error());
			while ($row = mysql_fetch_object($result)) {
				$size += $row->Size;
			}
			echo $size."\n";
			mysql_close($connect);
		}
		if ($value == "upload")
		{
			$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;			
			$_SESSION["currentdir"] = $_POST["currentdir"];
			include "../upload.inc.php";
		}
		if ($value == "move")
		{									
			include "../move.inc.php";		
		}
		if ($value == "copy")
		{
			$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;	
			include "../copy.inc.php";		
		}	
		if ($value == "delete")
		{			
			include "../delete.inc.php";		
		}	
		if ($value == "deleteDir")
		{			
			$result = mysql_query("Select * from Users  where API_Key = '$key' ") or die("Error: ".mysql_error());
			while ($row = mysql_fetch_object($result)) {
				$_SESSION["user_name"] =  $row->User ;
			}				
			include "../delete.inc.php";		
		}
		if ($value == "copyDir")
		{
			$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;	
			include "../copy.inc.php";		
		}	
		if ($value == "moveDir")
		{
			$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;	
			include "../move.inc.php";		
		}	
		if ($value == "createDir")
		{
			$_SESSION["currentdir"] = $_POST["currentdir"];	
			include "../move.inc.php";		
		}	
	}	
?>