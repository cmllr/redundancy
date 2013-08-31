<?php
	error_reporting(E_ALL);
	if (isset($_SESSION) == false)
		session_start();	
	$config = parse_ini_file("../../Redundancy.conf");
	$GLOBALS["Program_Dir"] = $GLOBALS["config"]["Program_Path"];
	include "../Program.inc.php";
	
	if ($config["Api_Enable"] != 1)
	{
		echo "Error:API_Enable\n";
		exit;
	}			
	//acknoledge	
	$_SESSION["acknoledge"] = false;
	$_SESSION['user_id'] = "";
	$_SESSION["user_name"] = "";	
	if (isset($_POST["api_key"]))
	{	
		include "../DataBase.inc.php";			
		$key = mysqli_real_escape_string($connect,$_POST["api_key"]);	
		$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key'") or die("Error: ".mysqli_error($connect));	
		while ($row = mysqli_fetch_object($result)) {
			$_SESSION['user_id'] = $row->ID;	
			$_SESSION["user_name"] = $row->User;
			$_SESSION["role"] = $row->Role;		
			if ($row->Enabled != 1 || $row->Enable_API != 1)
			{								
				$_SESSION["acknoledge"] = false;
				echo "User_privileges=false";
				exit;
			}
			else
			{			
				$_SESSION["acknoledge"] = true;
			}
		}
	}
	else
	{
		echo "Error:Key_Missing\n";
		exit;
	}	
	include "../DataBase.inc.php";			
	if (isset($_POST["directory"]))
		$_SESSION["directory"] = mysqli_real_escape_string($connect,$_POST["directory"]);
	else
		$_SESSION["directory"] = "/";
		
	$_SESSION["currentdir"] = "/";
	$GLOBALS["Program_Dir"] = $config["Program_Path"];
	mysqli_close($connect);
	foreach ($_POST as $keyValue => $value) 
	{		
		include "../DataBase.inc.php";
		if ($value == "getUserName"){			
			$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' ") or die("Error: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {
				echo "Result:".$row->User."\n";
			}	
			mysqli_close($connect);			
		}
		if ($value == "getUserSpace"){
			$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {
				echo "Result:".$row->Storage."\n";
			}
			mysqli_close($connect);
		}
		if ($value == "getFiles"){
			$files = "";
			$id = "";
			$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {
				$id = $row->ID;
			}			
			$result = mysqli_query($connect,"Select * from Files  where UserID = '$id' and Directory = '".$_SESSION["directory"]."'") or die("Error: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {
			$date = date_create($row->Uploaded);
			$uploaded = date_format($date, 'Y-m-d H:i:s');
				if ($row->Displayname == $row->Filename)
					$files .= "Dir:".$row->Displayname.";".$row->Filename_only.";".$uploaded.";".$row->Hash.";".getDirectorySize($row->Displayname)."\n";
				else
					$files .= "File:".$row->Displayname.";".$row->Filename.";".$uploaded.";".$row->Hash.";".$row->Size."\n";
			}
			echo "Result:\n".$files."\n";
			mysqli_close($connect);
		}
		if ($value == "getUsedSpace"){
			$size = "";
			$id = "";
			$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {
				$id = $row->ID;
			}			
			$result = mysqli_query($connect,"Select * from Files  where UserID = '$id'") or die("Error: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {
				$size += $row->Size;
			}
			echo "Result:".$size."\n";
			mysqli_close($connect);
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
			$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' ") or die("Error: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {
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
			include "../createDir.inc.php";		
		}
		if ($value == "downloadFile")
		{
			$_SESSION["currentdir"] = $_POST["currentdir"];	
			//include "../download.inc.php";		
			header("Location: ../download.inc.php?file=".$_POST["file"]);
		}
		if ($value == "renameFile")
		{		
			include "../rename.inc.php";		
		}
		if ($value == "getFileByHash")
		{			
			echo getHashByFile($_POST["file"]);
		}
		if ($value == "getServer")
		{
			echo $_SERVER["SERVER_NAME"];
		}
		if ($value == "isExisting")
		{
			$file = $_POST["file"];
			$directory = $_POST["directory"];			
			$res = fs_file_exists($file,$directory );
			echo "Command_Result:".$res;
		}
	}	
?>