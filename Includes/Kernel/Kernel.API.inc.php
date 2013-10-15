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
	 * Any method used by the API is located here
	 */
	/**
	 * runs the acknolege by a key
	 * @param $key the api key
	 * @return the result of the acknoledge	
	 */
	function acknowledge($key)
	{
		include "../DataBase.inc.php";			
		$key = mysqli_real_escape_string($connect,$key);	
		$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysqli_error($connect));
		if (isset($_SESSION) == false)
				session_start();
		$_SESSION["acknowledge"] = false;
		while ($row = mysqli_fetch_object($result)) {
				
			$_SESSION['user_id'] = $row->ID;	
			$_SESSION["user_name"] = $row->User;
			$_SESSION["role"] = $row->Role;		
			if ($row->Enabled != 1 || $row->Enable_API != 1)
			{								
				$_SESSION["acknowledge"] = false;				
			}
			else
			{			
				$_SESSION["acknowledge"] = true;
			}
		}
		mysqli_close($connect);
		if ($_SESSION["acknowledge"] == true)
			return "true";
		else
			return "false";
	}
	/**
	 * get the files of a dir
	 * @param $dir the wanted directory
	 * @param $key the api key	
	 * @return and string containing the file ID's concatenated with ";"
	 */
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
	/**
	 * Get the property of one file by the ID of the file
	 * @param $id the file's ID
	 * @param $key the api key	
	 * @return an string containing ID;Filename;Displayname;Filename_only,Hash,Uploaded,Size,Client,Mimetype, Directory
	 */
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
	/**
	 * Gets the content of an file by the id
	 * @param $id the file's ID
	 * @param $key the api key	
	 * @return an stream containing the content
	 */
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
	/**
	 * Get the name of an file by the ID
	 * @param $id the file's ID
	 * @param $key the api key	
	 * @return the filename or ""
	 */
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
	/**
	 * Get the server version
	 * @return the server's version
	 */
	function getVersion(){		
		echo $GLOBALS["Program_Version"];
	}
	/**
	 * Upload of a file
	 * Sets $_SESSION["space"] and $_SESSION["currentdir"]
	 * Needs $_FILES['userfile'], and the $_SESSION values (filled at login/ acknoledge)
	 * @return the result of the action
	 */
	function uploadFile(){
		$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;			
		$_SESSION["currentdir"] = $_POST["currentdir"];
		include "../upload.inc.php";		
	}
	/**
	 * renames a file
	 * Sets $_SESSION["currentdir"]
	 * Folder: Needs $_POST["newname"],$_POST["source"], $_POST["old_root"]
	 * Folder: Example hello_world, /home/hello, /home/
	 * @return the result of the action
	 */
	function renameFile(){
		$_SESSION["currentdir"] = $_POST["currentdir"];		
		include "../rename.inc.php";			
	}
	/**
	 * renames a folder
	 * Sets $_SESSION["currentdir"]
	 * Folder: Needs $_POST["newname"],$_POST["file"]
	 * Folder: hello_world.txt 412421a421409412c04 (md5 hash)
	 * @return the result of the action
	 */
	function renameFolder(){
		$_SESSION["currentdir"] = $_POST["currentdir"];	
		renameFile();		
	}
	/**
	 * Copy a folder or an dir
	 * Sets $_SESSION["space"]
	 * Folder: Needs $_POST["source"],$_POST["target"],$_POST["old_root"]
	 * Folder: /test/,/new test/,/
	 * File: Needs $_POST["file"],$_POST["dir"]
	 * File: 423424124ac4214f (md 5hash)
	 * @return the result of the action
	 */
	function copyFileOrFolder(){
		$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;	
		include "../copy.inc.php";		
	}
	/**
	 * Move a folder or an dir
	 * Sets $_SESSION["space"]
	 * Folder: Needs $_POST["source"],$_POST["target"],$_POST["old_root"]
	 * Folder: /test/,/new test/,/
	 * File: Needs $_POST["file"],$_POST["dir"]
	 * File: 423424124ac4214f (md 5hash)
	 * @return the result of the action
	 */
	function moveFileOrFolder(){
		$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;	
		include "../move.inc.php";		
	}
	/**
	 * Get the file hash (print's it to default output via echo)
	 * @return the result of the action
	 */
	function getHash(){		
		echo getHashByFileAndDir($_POST["file"],$_POST["dir"]);
	}
	/**
	 * Check if a file exists over $_POST values (print's it to default output via echo)
	 * Needs $_POST["entry"] and $_POST["dir"]
	 * Dirs: Use the name of the folder, not the path, for example at the dir /home/test/ use test as $_POST["entry"] and for $_POST["dir"] "/home/" to check if the folder test exists in the dir "/home/"
	 * @return result will be printed to default output via echo
	 */
	function exists(){	
		$res = fs_file_exists($_POST["entry"],$_POST["dir"]);
		if ($res == true)
			echo "true";
		else
			echo "false";
	}
	/**
	 * Creates a new dir by given $_POST values
	 * $_POST["dir"] and $_POST["entry"]
	 * Example "/hello/" and "hello"
	 * @return the result will be printed in the output
	 */
	function newDir(){
		createDir($_POST["dir"],$_POST["entry"]);
	}
	/**
	 * Delete a file or dir
	 * Files: $_POST["file"] (hash)
	 * Folders: $_POST["dir"] complete path
	 * @return the result will be printed in the output
	 */
	function delete(){
		include "../delete.inc.php";		
	}
	
?>