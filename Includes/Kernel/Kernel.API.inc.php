<?php

	function acknoledge($key)
	{
		include "../DataBase.inc.php";			
		$key = mysqli_real_escape_string($connect,$key);	
		$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysqli_error($connect));	
		if (isset($_SESSION) == false)
				session_start();
		$_SESSION["acknoledge"] = false;
		while ($row = mysqli_fetch_object($result)) {
				
			$_SESSION['user_id'] = $row->ID;	
			$_SESSION["user_name"] = $row->User;
			$_SESSION["role"] = $row->Role;		
			if ($row->Enabled != 1 || $row->Enable_API != 1)
			{								
				$_SESSION["acknoledge"] = false;				
			}
			else
			{			
				$_SESSION["acknoledge"] = true;
			}
		}
		mysqli_close($connect);
		if ($_SESSION["acknoledge"] == true)
			return "true";
		else
			return "false";
	}
	function getFiles($dir,$key)
	{
		include "../DataBase.inc.php";		
		$files = "";
		$id = "";
		$dir = mysqli_real_escape_string($connect,$dir);
		$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$id = $row->ID;
		}			
		$result = mysqli_query($connect,"Select ID  from Files  where UserID = '$id' and Directory = '$dir'") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {		
			$files .= $row->ID.";";
		}			
		mysqli_close($connect);
		return $files;
	}
	function getProperty($id,$key)
	{
		include "../DataBase.inc.php";		
		$files = "";
		$id = mysqli_real_escape_string($connect,$id);	
		$result = mysqli_query($connect,"Select * from Files  where ID = '$id' limit 1") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {	
			
			$files .= $row->ID.";".utf8_encode ($row->Filename).";".utf8_encode ($row->Displayname).";".utf8_encode ($row->Filename_only).";".$row->Hash.";".$row->Uploaded.";".$row->Size.";".str_replace(";",",",$row->Client).";".$row->MimeType.";".$row->Directory;
		}			
		mysqli_close($connect);
		return $files;	
	}
	function getContent($id,$key)
	{		
		include "../DataBase.inc.php";		
		$filename = "";
		$id = mysqli_real_escape_string($connect,$id);	
		$result = mysqli_query($connect,"Select * from Files  where ID = '$id' limit 1") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {	
			
			$filename = $row->Filename;
		}			
		mysqli_close($connect);
		$fullpath = $GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/$filename";	
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($fullpath));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($fullpath));
		ob_clean();
		flush();
		readfile($fullpath);
		exit;
	}
	function getName($id,$key){
		include "../DataBase.inc.php";		
		$filename = "";
		$id = mysqli_real_escape_string($connect,$id);	
		$result = mysqli_query($connect,"Select * from Files  where ID = '$id' limit 1") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {	
			
			$filename = $row->Displayname;
			
		}	
		return $filename;
	}
	function getVersion(){		
		echo $GLOBALS["Program_Version"];
	}
	function uploadFile(){
		$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;			
		$_SESSION["currentdir"] = $_POST["currentdir"];
		include "../upload.inc.php";		
	}
	function renameFile(){
		$_SESSION["currentdir"] = $_POST["currentdir"];		
		include "../rename.inc.php";			
	}
	function renameFolder(){
		$_SESSION["currentdir"] = $_POST["currentdir"];	
		renameFile();		
	}
	function copyFileOrFolder(){
		$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;	
		include "../copy.inc.php";		
	}
	function moveFileOrFolder(){
		$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;	
		include "../move.inc.php";		
	}
	function getHash(){		
		echo getHashByFileAndDir($_POST["file"],$_POST["dir"]);
	}
	function exists(){	
		$res = fs_file_exists($_POST["entry"],$_POST["dir"]);
		if ($res == true)
			echo "true";
		else
			echo "false";
	}
	function newDir(){
		createDir($_POST["dir"],$_POST["entry"]);
	}
	function delete(){
		include "../delete.inc.php";		
	}
	
?>