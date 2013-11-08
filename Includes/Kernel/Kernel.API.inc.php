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
	 * validates the key
	 * @param $key the api key
	 * @return valid/invalid key
	 */
	function checkApiKey($key)
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
				$_SESSION["acknowledge"] = false;				
			else		
				$_SESSION["acknowledge"] = true;
		}
		mysqli_close($connect);

		echo getSingleNodeXMLDoc($_SESSION["acknowledge"] ? "true" : "false");
	}
	
	function setSession($key)
	{
			include "../DataBase.inc.php";			
		$key = mysqli_real_escape_string($connect,$key);	
		$result = mysqli_query($connect,"Select * from Users  where API_Key = '$key' limit 1") or die("Error: ".mysqli_error($connect));
		if (isset($_SESSION) == false)
				session_start();	
		while ($row = mysqli_fetch_object($result))
		{
			$_SESSION['user_id'] = $row->ID;	
			$_SESSION["user_name"] = $row->User;
			$_SESSION["role"] = $row->Role;	
			$_SESSION["space"] = $row->Storage;	
		}
		mysqli_close($connect);
	}

	function getFileHeadsAsXML($dir,$key)
	{
		include "../DataBase.inc.php";		
		$id = "";
		$doc = new SimpleXMLElement("<entries></entries>");
		$dir = mysqli_real_escape_string($connect,$dir);
		$result = mysqli_query($connect,"Select * from Users where API_Key = '$key' limit 1") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$id = $row->ID;
		}			
		$result = mysqli_query($connect,"Select ID, Displayname, Filename, Uploaded from Files where UserID = '$id' and Directory = '$dir'") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result))
		{
			$child = $doc->addChild('entry');
			$child->addAttribute('id', $row->ID);
			$child->addAttribute('displayName', ($row->Displayname));
			$child->addAttribute('fileName', ($row->Filename));
			$child->addAttribute('creationTime', $row->Uploaded);
		}			
		mysqli_close($connect);
		return $doc->asXML();
	}
	
	function getPropertiesAsXML($id,$key)
	{
		include "../DataBase.inc.php";		
		$files = "";
		$doc = new SimpleXMLElement("<entry></entry>");
		$id = mysqli_real_escape_string($connect,$id);	
		$result = mysqli_query($connect,"Select * from Files  where ID = '$id' limit 1") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result))
		{	
			$doc->addAttribute('id', ($row->ID));
			$doc->addAttribute('fileName',  ($row->Filename));
			$doc->addAttribute('displayName', ($row->Displayname));
			$doc->addAttribute('fileNameOnly',  ($row->Filename_only));
			$doc->addAttribute('hash', $row->Hash);
			$doc->addAttribute('creationTime', $row->Uploaded);
			$doc->addAttribute('sizeInByte', $row->Size);
			$doc->addAttribute('userAgent', $row->Client);
			$doc->addAttribute('mimeType', $row->MimeType);
			$doc->addAttribute('directory', $row->Directory);
		}		
		mysqli_close($connect);
		return $doc->asXML();	
	}

	function getContent($id,$key)
	{		
		include "../DataBase.inc.php";		
		$filename = "";
		$id = mysqli_real_escape_string($connect,$id);	
		$result = mysqli_query($connect,"Select * from Files  where ID = '$id' limit 1") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result))
		{	
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

	function getVersion()
	{		
		echo getSingleNodeXMLDoc($GLOBALS["Program_Version"]);
	}

	function uploadFile()
	{	
		$_SESSION["currentdir"] = $_POST["currentdir"];				
		setSession($_POST["key"]);		
		include "../upload.inc.php";		
	}
	/**
	 * renames a file
	 * Sets $_SESSION["currentdir"]
	 * Folder: Needs $_POST["newname"],$_POST["source"], $_POST["old_root"]
	 * Folder: Example hello_world, /home/hello, /home/
	 * @return the result of the action
	 */
	function renameFile()
	{	
		$_SESSION["currentdir"] = $_POST["currentdir"];		
		setSession($_POST["key"]);
		include "../rename.inc.php";			
	}
	/**
	 * renames a folder
	 * Sets $_SESSION["currentdir"]
	 * Folder: Needs $_POST["newname"],$_POST["file"]
	 * Folder: hello_world.txt 412421a421409412c04 (md5 hash)
	 * @return the result of the action
	 */
	function renameFolder()
	{
		$_SESSION["currentdir"] = $_POST["currentdir"];	
		setSession($_POST["key"]);
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
	function copyFileOrFolder()
	{
		$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;	
		setSession($_POST["key"]);
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
	function moveFileOrFolder()
	{
		$_SESSION["space"] = getUsedSpace($_SESSION["user_id"]) ;
		setSession($_POST["key"]);
		include "../move.inc.php";		
	}
	/**
	 * Get the file hash (print's it to default output via echo)
	 * @return the result of the action
	 */
	function getHash()
	{		
		setSession($_POST["key"]);
		echo getHashByFileAndDir($_POST["file"],$_POST["dir"]);
	}
	/**
	 * Check if a file exists over $_POST values (print's it to default output via echo)
	 * Needs $_POST["entry"] and $_POST["dir"]
	 * Dirs: Use the name of the folder, not the path, for example at the dir /home/test/ use test as $_POST["entry"] and for $_POST["dir"] "/home/" to check if the folder test exists in the dir "/home/"
	 * @return result will be printed to default output via echo
	 */
	function exists()
	{	
		setSession($_POST["key"]);
		$res = isFileExisting($_POST["entry"],$_POST["dir"]);
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
	function newDir()
	{
			setSession($_POST["key"]);
		createDir($_POST["dir"],$_POST["entry"]);
	}
	/**
	 * Delete a file or dir
	 * Files: $_POST["file"] (hash)
	 * Folders: $_POST["dir"] complete path
	 * @return the result will be printed in the output
	 */
	function delete()
	{
		setSession($_POST["key"]);
		include "../delete.inc.php";		
	}
	
	function getKey($username,$password)
	{
		$key = false;	
		$key = getKeyByUsername($username,$password);		
		if ($key != "false")
			setSession($key);
		
		echo getSingleNodeXMLDoc($key);
	}
	
?>