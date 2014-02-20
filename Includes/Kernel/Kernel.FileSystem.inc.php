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
	 * This file contains filesystem relevant methods.
	 * @todo the copy, rename and move algorithms must be tested more
	 */
	/*
	 * Determines if a file is an image
	 * @param $filename the full filename ({value}.dat)
	 * @return If the file is an image
	 */
	function isImage($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = getMimeType($filename);
			if ((strpos($mimetype,"image") !== false  && strpos($mimetype,"svg") ===  false) || $mimetype == "image/png" || $mimetype == "image/jpg" || $mimetype == "image/jpeg" || $mimetype == "image/bmp" )
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	/*
	 * isVectorGraphics determines if a file is an vector graphics
	 * @param $filename the full filename ({value}.dat)
	 * @return If the file is an vector graphics
	 */
	function isVectorGraphics($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = getMimeType($filename);
			if (strpos($mimetype,"/svg") !== false )
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	/**
	 * determines if a file is a video
	 * @param $filename the full filename ({value}.dat)
	 * @return If the file is a video
	 */
	function isVideo($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = getMimeType($filename);
			if (strpos($mimetype,"video") !== false ||  $mimetype == "video/mpeg" || $mimetype == "video/mp4" || $mimetype == "video/ogg" || $mimetype == "video/quicktime" || $mimetype == "video/webm" || $mimetype == "video/x-mastroska" || $mimetype == "video/x-ms-wmv" || $mimetype == "video/x-flv" || $mimetype == "video/x-ms-asf" )
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	/**
	 * determines if a file is an audio
	 * @param $filename the full filename ({value}.dat)
	 * @return If the file is an audio
	 */
	function isAudio($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = getMimeType($filename);
			if (strpos($mimetype,"audio") !== false || $mimetype == "audio/basic" || $mimetype == "audio/L24" || $mimetype == "audio/mp4" || $mimetype == "audio/mpeg" || $mimetype == "audio/ogg" || $mimetype == "audio/vorbis" || $mimetype == "audio/vnd.rn-realaudio" || $mimetype == "audio/vnd.wave" || $mimetype == "audio/webm" || $mimetype == "audio/mpeg")
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	/**
	 * isText determines if a file is a text
	 * @param $filename the full filename ({value}.dat)
	 * @return If the file is a text
	 */
	function isText($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = getMimeType($filename);
			if ($mimetype == "text/cmd" || $mimetype == "text/css" || $mimetype == "text/csv" || $mimetype == "text/html" || $mimetype == "text/plain" || $mimetype == "text/xml" || $mimetype == "text/x-asm")
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	/**
	 * get the user's storage in Byte
	 * @param $username the username or the user id (recommended)
	 * @return the used space in byte
	 */
	function getUsedSpace($username)
	{	
		if (isset($_SESSION) == false)
			session_start();		
		//Abort management
		if (isset($username) == false || empty($username) == true)
			return 0;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
		$userID = "";
		$user = mysqli_real_escape_string($connect,$username);
		$amount_in_Byte  = 0;
		$result = mysqli_query($connect,"Select ID, User from Users where User = '$user' or ID = '$user' LIMIT 1") or die("Error 022: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$userID = $row->ID;				
		}
		$result = mysqli_query($connect,"Select Size from Files where UserID = '$userID'")  or die("Error 023: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysqli_close($connect);		
		return $amount_in_Byte ;
	}
	/**
	 * sets the current used space into $_SESSION["space_used"]
	 * @param $username the username or the user id (recommended)	
	 */
	function setUsedStorage($username)
	{			
		if (isset($_SESSION) == false)
			session_start();
			//Abort management
		if (isset($username) == false || empty($username) == true)
			return 0;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
		$userID = "";		
		$user = mysqli_real_escape_string($connect,$username);
		$amount_in_Byte  = 0;
		$result = mysqli_query($connect,"Select ID, User from Users where User = '$user' or ID = '$user' LIMIT 1") or die("Error: 022: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$userID = $row->ID;				
		}
		$result = mysqli_query($connect,"Select Size from Files where UserID = $userID") or die("Error: 023: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysqli_close($connect);		
		//Store the space information in the session
		$_SESSION["space_used"] =  $amount_in_Byte;	
	}	
	/**
	 * get the used percentage by the current session	
	 * @return a string containg "X of Y measure used (XY %)"
	 */
	function getUsedStorageStatus()
	{
		if (isset($_SESSION) == false)
			session_start();
		$storage = $_SESSION["space"];		
		$storage = $storage * 1024 * 1024;
		$storage_used = getUsedSpace($_SESSION["user_name"]); 
		
		$measure = "B";
		if ($storage_used > 1024 && $storage_used < 1024 * 1024)
		{
			$measure = "KB";
			$storage_used = $storage_used /1024;
		}
		else if ($storage_used > 1024 * 1024 && $storage_used < 1024 * 1024 * 1024)
		{
			$measure = "MB";
			$storage_used = $storage_used /1024 / 1024;
		}
		else if ($storage_used > 1024 * 1024 * 1024 && $storage_used < 1024 * 1024 * 1024 * 1024)
		{
			$measure = "GB";
			$storage_used = $storage_used /1024 / 1024 / 1024;
		}
		else if ($storage_used > 1024 * 1024 * 1024 * 1024)
		{
			$measure = "TB";
			$storage_used = $storage_used /1024 / 1024 / 1024 / 1024;
		}
		if ($storage_used == 0)
			$storage_used = 0;
		return round($storage_used,2)." $measure ".$GLOBALS["Program_Language"]["of"]." ".measurementCorrection($_SESSION['space']*1024*1024)." ".$GLOBALS["Program_Language"]["used"];
	}	
	/**
	 * get the percents of used space	
	 * @return a string containg "x%"
	 */
	function getUsedStoragePercentage()
	{
		if (isset($_SESSION) == false)
			session_start();
		$storage = $_SESSION["space"];		
		$storage = $storage * 1024 * 1024;
		$storage_used = getUsedSpace($_SESSION["user_name"]);
		if ($storage_used == 0)
		return "0%";
			else
		return round(100/($storage/$storage_used),2)."%";
	}	
	/**
	 *  get the percents of used space	
	 * @return a string containg "x"
	 */
	function getUsedStorage()
	{
		if (isset($_SESSION) == false)
			session_start();
		$storage = $_SESSION["space"];		
		$storage = $storage * 1024 * 1024;
		$storage_used = getUsedSpace($_SESSION["user_name"]);
		if ($storage_used == 0)
		return "0";
			else
		return round(100/($storage/$storage_used),2);
	}	
	/**
	 * isShared determines if a file is already shared
	 * @param $file the hash of the file
	 * @return if the file is shared
	 */
	function isShared($file)
	{				
		if (isset($file) == false || empty($file) == true)
			return false;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$hash =mysqli_real_escape_string($connect,$file); 
		$result = mysqli_query($connect,"Select ID from Share where Hash = '".$hash."' limit 1") or die("Error: 024: ".mysqli_error($connect));
		if (mysqli_affected_rows($connect) > 0){
			return true;
		}		
		return false;
	}	
	/**
	 * gets the share link
	 * @param $file the hash of the file
	 * @return the sharelink or -1
	 */
	function getShareLink($file)
	{
		if (isset($file) == false || empty($file) == true)
			return -1;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysqli_query($connect,"Select Extern_ID from Share where Hash = '".mysqli_real_escape_string($connect,$file)."' limit 1") or die("Error: 024: ".mysqli_error($connect));
		$share_dir = str_replace("index.php","",$_SERVER["PHP_SELF"]);
		if ($row = mysqli_fetch_object($result)){
				if ($GLOBALS["config"]["Program_HTTPS_Redirect"] == 1)			
					$sharetext = "https://".$_SERVER["SERVER_NAME"].$share_dir."index.php?share=".$row->Extern_ID;
				else
					$sharetext = "http://".$_SERVER["SERVER_NAME"].$share_dir."index.php?share=".$row->Extern_ID;
				return $sharetext;
		}		
		return -1;
	}	
	/**
	 * gets a correct measurement for a size
	 * @param $value the file size in Byte
	 * @param $offset a file offset (default 1)
	 * @return a fitting measurement (from Byte to TerraByte)
	 */
	function measurementCorrection($value,$offset = 1)
	{
		
		$measure = "B";	
		$value = $value * $offset;		
		if ($value > 1024 && $value  < 1024 * 1024)
		{
			$measure = "KB";
			$value = $value /1024;
		}
		else if ($value > 1024 * 1024 && $value < 1024 * 1024 * 1024)
		{
			$measure = "MB";
			$value = $value /1024 / 1024;
		}
		else if ($value >= 1024 * 1024 * 1024 && $value < 1024 * 1024 * 1024 * 1024 )
		{
			$measure = "GB";
			$value = $value /1024 / 1024 / 1024;
		}
		else if ($value > 1024 * 1024 * 1024 * 1024 )
		{
			$measure = "TB";
			$value = $value /1024 / 1024 / 1024 / 1024;
		}
		return round($value,2) ." ". $measure;
	}	
	/**
	 * get the size of a directory in Byte, the user must be logged in
	 * @param $value the fullpath of the directory
	 * @return the size in Byte
	 */
	function getDirectorySize($value)
	{		
		if (isset($value) == false || empty($value) == true)
			return 0;
		if (isset($_SESSION) == false)
			session_start();
		$dirSize = 0;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$dirID = getDirectoryID($value);
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Size, Filename,Displayname from Files where UserID = '$userID' and Directory_ID = '$dirID'") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {	
			if ($row->Filename != $row->Displayname)
				$dirSize += $row->Size;
			else
				$dirSize += getDirectorySize($row->Filename);
		}
		mysqli_close($connect);	
		return $dirSize;
	}
	/**
	 * get the size of a file in byte
	 * @param $value the displayname
	 * @param $dir the directory, where the file is saved
	 * @return the size in byte
	 */
	function getFileSize($value,$dir)
	{
		if (isset($value) == false || isset($dir) == false || empty($value) == true || empty($dir) == true)
			return 0;
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$dirSize = 0;
		$dirID = getDirectoryID($value);	
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Size from Files where UserID = '$userID' and Displayname = '$value' and Directory = '$dir' and Directory_ID ='$dirID'") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {				
			$dirSize += $row->Size;
		}
		mysqli_close($connect);	
		return $dirSize;
	}
	/**
	 * getDisplayName get a fitting displaystyle for directories
	 * @param $string the displayname
	 * @param $filename the filename
	 * @return the displayname in the best display form
	 */
	function getDisplayName($string,$filename)
	{
		if ($string != $filename)
			return $string;
		else{
			$path_parts = explode('/',$string);
			return $path_parts[count($path_parts) -2];
		}
	}
	/**
	 * getMimeType determines the MIMEType
	 * @param $filename the full(!) path of the file ending with .dat
	 * @return the MIME of the file or if not found "null"
	 */
	function getMimeType($filename) {
		if (isset($filename) == false || empty($filename) == true)
			return "null";
		if ($GLOBALS["config"]["Program_Mime_Use_DataBase"] == 0){
			$file = file_get_contents(getStoragePath().$filename);
			$finfo = new finfo(FILEINFO_MIME_TYPE);		
			return $finfo->buffer($file);
		}
		else
		{
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
			$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
			$result = mysqli_query($connect,"Select MimeType from Files where Filename = '$filename' or Hash = '$filename' limit 1") or die("Error 025: ".mysqli_error($connect));
			$filename_mime = "";
			while ($row = mysqli_fetch_object($result)) {
				$filename_mime = $row->MimeType;
			}				
			return $filename_mime;
		}		
	}
	/**
	 * getDirectoryID get the database ID of a directory
	 * @param $directory the directory full path
	 * @return the ID or -1 if not found
	 */
	function getDirectoryID($directory)
	{	
		if (isset($directory) == false || empty($directory) == true)
			return -1;
		if (isset($_SESSION) == false)
			session_start();
		$filename = -1;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$folder = mysqli_real_escape_string($connect,$directory);
		$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$folder' and Filename = '$folder' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$filename = $row->ID;
		}		
		return $filename;
	}
	/**
	 * isFileExisting checks if a file or dir exists on the filesystem
	 * @param $file the displayname of the searched file or dir
	 * @param $directory the directory where the file or dir is searched
	 * @return True or false
	 */
	function isFileExisting($file,$directory )
	{		
		if (isset($_SESSION) == false)
			session_start();	
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$file = mysqli_real_escape_string($connect,$file);
		$directory =  mysqli_real_escape_string($connect,$directory);
		$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and (Filename_only = '$file' or Displayname = '$file'  or Hash = '$file')and Directory = '$directory'") or die("Error 025: ".mysqli_error($connect));
		
		if (mysqli_affected_rows($connect) > 0)
			return true;
		else
			return false;
	}
	/**
	 * checks if a dir exists on the filesystem
	 * @param $directory the directory
	 * @return True or false
	 */
	function isFolderExisting($directory ) 
	{			
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		if ($directory == "/")
			return true;
		$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$directory =  mysqli_real_escape_string($connect,$directory);		
		$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$directory' ") or die("Error 025: ".mysqli_error($connect));
		if (mysqli_affected_rows($connect) > 0)
			return true;
		else
			return false;
	}
	/**
	 * getFileByHash returns the displayname searched by a hash
	 * @param $hash the hash
	 * @return the displayname or ""
	 */
	function getFileByHash($hash)
	{
		if (isset($_SESSION) == false)
			session_start();
		$filename = "";
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hashSafe = mysqli_real_escape_string($connect,$hash);
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Displayname from Files where UserID = '".$userID."' and Hash = '$hashSafe' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$filename = $row->Displayname;
		}			
		return $filename;
	}
	/**
	 * getFileByHash returns the displayname searched by a hash
	 * @param $hash the hash
	 * @return the ID or -1
	 */
	function getFileIDByHash($hash)
	{
		if (isset($_SESSION) == false)
			session_start();
		$filename = -1;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hashSafe = mysqli_real_escape_string($connect,$hash);
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select ID from Files where  Hash = '$hashSafe' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$filename = $row->ID;
		}			
		return $filename;
	}
	/**
	 * getFileHashByID returns the hash searched by a id
	 * @param $id the id
	 * @return the hash or -1
	 */
	function getFileHashByID($id)
	{
		if (isset($_SESSION) == false)
			session_start();
		$filename = -1;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$idSafe = mysqli_real_escape_string($connect,$id);		
		$result = mysqli_query($connect,"Select Hash from Files where id = '$idSafe' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$filename = $row->Hash;
		}			
		return $filename;
	}
	/**
	 * getHashByFile the opposite function of @see getFileByHash
	 * @param $file the displayname
	 * @return the hash
	 * @todo possible bug because no directory is delivered
	 */
	function getHashByFile($file)
	{
		if (isset($_SESSION) == false)
			session_start();
		$filename = "";
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Hash from Files where UserID = '".$userID."' and Displayname = '$file' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$filename = $row->Hash;
		}			
		return $filename;
	}
	/**
	 * getHashByFile the opposite function of @see getFileByHash
	 * @param $file the displayname
	 * @param $dir the directory
	 * @return the hash
	 * @todo possible bug because no directory is delivered
	 */
	function getHashByFileAndDir($file,$dir)
	{
		if (isset($_SESSION) == false)
			session_start();
		$filename = "";
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$file = mysqli_real_escape_string($connect,$file);
		$dir = mysqli_real_escape_string($connect,$dir);
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Hash from Files where UserID = '".$userID."' and (Displayname = '$file' or Displayname = '/$file/') and Directory = '$dir' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$filename = $row->Hash;
		}			
		return $filename;
	}
	/**
	 * getFileRoot get the directory id of the dir where a file is saved
	 * @param $hash the hash of the file
	 * @return the ID or ""
	 */
	function getFileRoot($hash)
	{
		if (isset($_SESSION) == false)
			session_start();
		$dir = "";
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Directory_ID from Files where UserID = '".$userID."' and Hash = '$hash' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$dir = $row->Directory_ID;
		}	
		$res = "";
		$result = mysqli_query($connect,"Select Displayname from Files where ID = '$dir' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$res = $row->Displayname;
		}		
		return $res;
	}	
	/**
	 * moveDir moves a directory
	 * @param $dir the dir
	 * @param $target the target dir
	 * @param $old_root the directory, where the directory was saved	
	 */
	function moveDir($dir,$target,$old_root)
	{
		//Dir = /test/
		//old_root = /
		//target = /newdir/test/
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$dir = mysqli_real_escape_string($connect,$dir);
		$target = mysqli_real_escape_string($connect,$target);
		$old_root = mysqli_real_escape_string($connect,$old_root);
		$select = "Select * from Files where Directory = '$old_root' and UserID = '	$user' ";
		$replace_count = 1;
		if ($old_root == "/")
			$new_root = $target;
		else
			$new_root = str_replace($old_root,$target,$dir,$replace_count);
		$res = mysqli_query($connect,$select);
		while ($row = mysqli_fetch_object($res)){
			$ID = $row->ID;
			$Filename = $row->Filename;
			$Displayname = $row->Displayname;
			$Hash = $row->Hash;
			$UserID = $row->UserID;
			$IP = $row->IP;
			$Uploaded = $row->Uploaded;
			$Size = $row->Size;
			$Directory = $row->Directory;
			$Directory_ID = $row->Directory_ID;
			$Client = $row->Client;
			$filename_only = $row->Filename_only;
			if ($row->Filename == $row->Displayname && strpos($row->Filename,$dir) !== false && strpos($row->Filename,$dir) !== false && isFileExisting($row->Displayname,$target) == false)
			{
				//Directory		
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){				
					echo "<br>param root".$old_root;	
					echo "<br>target ".$target;
				}				
				$newDir = $target;
				$newName = str_replace($old_root,$target,$row->Displayname,$temp = 1);
				$newDir_ID = getDirectoryID($target);
				$insert = "Insert";
				$dir_id = getDirectoryID($target);
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "<br>Old entry name:".$row->Displayname;
					echo "<br>NEW entry name:".$target.$row->Filename_only."/";
					echo "<br>Old directory".$old_root;
					echo "<br>New Directory".$target;	
				}
				$displayname = $target.$row->Filename_only."/";
				$timestamp = time();
				$modified= date("Y-m-d H:i:s",$timestamp);	
				updateLastWriteOfDirectory(getDirectoryID($target));
				mysqli_query($connect,"Update Files SET Displayname ='$displayname', Filename ='$displayname',Directory='$target',Directory_ID = ".getDirectoryID($target).",lastWrite='$modified' where ID =".$row->ID) or die("Error: 016: ".mysqli_error($connect));	
					
				moveDir($row->Filename,$target.$row->Filename_only."/",$row->Displayname);
					
			}
			else if (strpos($row->Directory,$dir) !== false && isFileExisting($row->Displayname,$target) == false)
			{
				//File
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "<br>param root".$old_root;	
					echo "<br>target ".$target;	
				}
				$newDir = $target;
				$newName = str_replace($old_root,$target,$row->Displayname,$temp = 1);			
				$insert = "Insert";
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "<br>Old entry name:".$row->Displayname;
					echo "<br>NEW entry name:".$target.$row->Displayname;
					echo "<br>Old directory".$old_root;
					echo "<br>New Directory".$target;	
				}
				moveFile($row->ID,$target);
			}
		}	
	}
	/**
	 * moveFile moves a file
	 * @param $ID the ID
	 * @param $newdir the new directory
	 */
	function moveFile($ID,$newdir)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$timestamp = time();
		$modified= date("Y-m-d H:i:s",$timestamp);						
		$newdir = mysqli_real_escape_string($connect,$newdir);	
		$dir_id = getDirectoryID($newdir);
		updateLastWriteOfDirectory($dir_id);
		mysqli_query($connect,"Update Files Set Directory='$newdir',lastWrite='$modified', Directory_ID = ".getDirectoryID($newdir)." where ID =".$ID." and UserID = '$user'") or die("Error: 017 ".mysqli_error($connect));	
	}
	/**
	 * moveContents move the contents of a directory
	 * @param $source the source directory
	 * @param $target the target directory
	 * @todo if data is not moved, see line 641 
	 */
	function moveContents($source,$target)
	{			
		$uploadtime= date("Y-m-d H:i:s",time());
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$source = mysqli_real_escape_string($connect,$source);
		$target = mysqli_real_escape_string($connect,$target);
		if ($GLOBALS["config"]["Program_Debug"] == 1)
		{
			echo "moving content of $source<br>target: $target<br>";
		}	
		$getfiles_select = mysqli_query($connect,"Select * from Files where Directory like  '$source' and UserID = '$user' ");	
		$new_id = getDirectoryID($target."/");
		while ($row = mysqli_fetch_object($getfiles_select) ) {		
				if ($row->Filename == $row->Displayname)
				{					
					if ($GLOBALS["config"]["Program_Debug"] == 1 ){
						echo "<br>found dir".$row->Filename."<br>";
						echo "<br>new dir:" .$target."/".$row->Filename_only."/"."<br>";
						echo "<br>new root:".$target."/"."<br>";	
					}					
					mysqli_query($connect,"Update Files set Filename ='".$target."/".$row->Filename_only."/"."', Displayname = '".$target."/".$row->Filename_only."/"."', Directory = '".$target."/"."',lastWrite='$uploadtime',Directory_ID=".$new_id." where Hash = '".$row->Hash."'"); 	
					moveContents($row->Filename,$target."/".$row->Filename_only);
				}
				else
				{
					if ($GLOBALS["config"]["Program_Debug"] == 1 ){
						echo "<br>found file".$row->Filename."<br>";
						echo "<br>new file dir:" .$target."/"."<br>";
					}
					$file_id = getDirectoryID($target."/");
					mysqli_query($connect,"Update Files set Directory = '".$target."/"."',Directory_ID = ".$file_id.",lastWrite='$uploadtime' where Hash = '".$row->Hash."'"); 
				}
		}		
	}
	/**
	 * createDir creates a new directory
	 * @param $currentdir the root of the new directory
	 * @param $directory the new directory name (does not end with "/"!)
	 * @param $hashcode optional. Set the hash of the new directory directly
	 */
	function createDir($currentdir,$directory,$hashcode = "")
	{
		//an easy possibility to avoid xss 
		$success = true;
		if (strpos("<",$directory) === false){
			if (isset($_SESSION) == false)
				session_start();
			//include the dataBase file
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$currentdir = mysqli_real_escape_string($connect,$currentdir);
			$directory = mysqli_real_escape_string($connect,$directory);
			//remember the user id, the new directory, the current directory, the directory without path timestamp hash and so on...
			$userid = mysqli_real_escape_string($connect,$_SESSION['user_id']);		
			$newdirectory =  $currentdir . $directory."/";			
			$uploaddirectory = $currentdir;
			$filenameonly = $directory;
			$timestamp = time();
			$uploadtime= date("Y-m-d H:i:s", $timestamp);
			if ($hashcode == "")
				$hash = md5($newdirectory.$uploadtime.$userid);	
			else
				$hash = mysqli_real_escape_string($connect,$hashcode);
			$client_ip = getIP();	
			$dir_id = getDirectoryID($uploaddirectory); 	
				
			if (isFileExisting($directory,$uploaddirectory) == false)
			{			
				//create the new directory
				$insert = "INSERT INTO Files (Filename,Displayname,Filename_only,Hash,UserID,IP,Uploaded,Size,Directory,Directory_ID,Client,ReadOnly,lastWrite) VALUES ('$newdirectory','$newdirectory','$filenameonly','$hash','$userid','$client_ip','$uploadtime',0,'$uploaddirectory','$dir_id','".$_SERVER['HTTP_USER_AGENT']."',0,'$uploadtime')";			
				$inserquery = mysqli_query($connect,$insert) or die("Error: 004 ".mysqli_error($connect));						
				updateLastWriteOfDirectory($dir_id);
				$success = true;
			}		
			else{
				$success = false;
			}
			mysqli_close($connect);		
		}
		if (isset($_POST["method"]) == false)
		{
			if ($GLOBALS["config"]["Program_Redirect_NewDir"] == 1){
				if ($GLOBALS["config"]["Program_Debug"] != 1)
					header("Location: ./index.php?module=list&dir=".$currentdir.$directory."/&result=1&from=createdir");
			}	
		}	
		else
		{
			echo getSingleNodeXMLDoc($success ? "true" : "false");			
		}
	}	
	/**
	 * copies a dir
	 * @param $dir the directory
	 * @param $target the target directory
	 * @param $old_root the directory, where the directory was saved	
	 */
	function copyDir($dir,$target,$old_root)
	{	
		//Dir = /test/
		//old_root = /
		//target = /newdir/test/
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$owner_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);
		$dir = mysqli_real_escape_string($connect,$dir);
		$target = mysqli_real_escape_string($connect,$target);
		$old_root = mysqli_real_escape_string($connect,$old_root);
		$select = "Select * from Files where Directory = '$old_root' and UserID = '$owner_id' ";
		$replace_count = 1;
		if ($old_root == "/")
			$new_root = $target;
		else
			$new_root = str_replace($old_root,$target,$dir,$replace_count);
		$res = mysqli_query($connect,$select);
		while ($row = mysqli_fetch_object($res)){
			$ID = $row->ID;
			$Filename = $row->Filename;
			$Displayname = $row->Displayname;
			$timestamp = time();
			$uploadtime= date("Y-m-d H:i:s", $timestamp);
			$Hash = md5($Displayname.$uploadtime);	
			$UserID = $row->UserID;
			$IP = $row->IP;
			$Uploaded = $row->Uploaded;
			$Size = $row->Size;
			$Directory = $row->Directory;
			$Directory_ID = $row->Directory_ID;
			$Client = $row->Client;
			$filename_only = $row->Filename_only;
			if ($row->Filename == $row->Displayname && strpos($row->Filename,$dir) !== false && strpos($row->Filename,$dir) !== false && isFileExisting($row->Displayname,$target) == false)
			{
				//Directory		
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "<br>param root".$old_root;	
					echo "<br>target ".$target;	
				}
				$newDir = $target;
				$newName = str_replace($old_root,$target,$row->Displayname,$temp = 1);
				$newDir_ID = getDirectoryID($target);
				$insert = "Insert";
				$dir_id = getDirectoryID($target);
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "<br>Old entry name:".$row->Displayname;
					echo "<br>NEW entry name:".$target.$row->Filename_only."/";
					echo "<br>Old directory".$old_root;
					echo "<br>New Directory".$target;	
				}
				$insertDir = "Insert into Files (Filename, Displayname,Filename_only, Hash, UserID, IP, Uploaded, Size, Directory,Directory_ID,lastWrite ) Values ('".$target.$row->Filename_only."/"."','".$target.$row->Filename_only."/"."','$filename_only','$Hash',$UserID,'$IP','$Uploaded',$Size,'$target',$dir_id,'$Uploaded')";
				mysqli_query($connect,$insertDir);
				updateLastWriteOfDirectory($dir_id);
				copyDir($row->Filename,$target.$row->Filename_only."/",$row->Displayname);
					
			}
			else if (strpos($row->Directory,$dir) !== false && isFileExisting($row->Displayname,$target) == false)
			{
				//File
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "<br>param root".$old_root;	
					echo "<br>target ".$target;	
				}
				$newDir = $target;
				$newName = str_replace($old_root,$target,$row->Displayname,$temp = 1);			
				$insert = "Insert";
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "<br>Old entry name:".$row->Displayname;
					echo "<br>NEW entry name:".$target.$row->Displayname;
					echo "<br>Old directory".$old_root;
					echo "<br>New Directory".$target;	
				}
				copyFile($row->Hash,$target);
			}
		}
	}
	/**
	 * copies a file	
	 * @param $file the file
	 * @param $dir the directory which contains the file
	 */
	function copyFile($file,$dir)
	{
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		$uploadtime= date("Y-m-d H:i:s",time());
		$owner_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);
		$file = mysqli_real_escape_string($connect,$file);
		$dir = mysqli_real_escape_string($connect,$dir);
		$result = mysqli_query($connect,"Select * from Files  where Hash = '$file' and UserID = '$owner_id'") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$Filename =$row->Filename;
			$Displayname = $row->Displayname;
			$timestamp = time();
			$uploadtime= date("Y-m-d H:i:s", $timestamp);
			$Hash = md5($Filename.$uploadtime);	
			$UserId = $row->UserID;
			$IP = getIP();
			$Uploaded = $row->Uploaded;
			$Size = $row->Size;
			$Directory = $row->Directory;
			$MimeType = $row->MimeType;
			$Client = $row->Client;
		}
		if(getUsedSpace("/") + $Size >= $_SESSION["space"] * 1024 * 1024)
		{
			if (isset($_POST["method"]) == false){
				header("Location: ./index.php?module=list&dir=$dir");
				exit;
			}
		}
		$found =false;
		$code = getRandomKey(50);
		do{				
			include $GLOBALS["Program_Dir"] ."Includes/DataBase.inc.php";
			mysqli_query($connect,"Select ID  from `Files` where  where Filename = '$code.dat'");
			if (mysqli_affected_rows($connect) > 0)
			{
				$code = getRandomKey(50);
				$found = true;					
			}
		}while($found == true );	
		$hash_new = md5($code.".dat");	
		$newfilename = $code.".dat";	
		$uploaddir =getStoragePath();
		$dir_id = getDirectoryID($dir);
		$insert = "Insert into Files (Filename, Displayname, Hash, UserID, IP, Uploaded, Size, Directory,Directory_ID,MimeType,Client,lastWrite ) Values ('$newfilename','$Displayname','$hash_new',$UserId,'$IP','$uploadtime',$Size,'$dir',$dir_id,'$MimeType','$Client','$uploadtime')";
		$insertquery = mysqli_query($connect,$insert);
		if ($insertquery == true){
			//Set the change date of the parent directoy
			updateLastWriteOfDirectory($dir_id);
			copy($uploaddir.$Filename,$uploaddir.$newfilename);	
		}
	}
	/**
	 * update the last write date of a directory
	 * @param $id the dir id
	 */
	function updateLastWriteOfDirectory($id){
		if ($id != -1){
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";				
			$id = mysqli_real_escape_string($connect,$id);
			$lastWrite= date("Y-m-d H:i:s",time());
			$update = "Update Files set lastWrite = '$lastWrite' where ID = '$id'";		
			mysqli_query($connect,$update);
		}
	}
	/**
	 * deleteDir deletes a directory
	 * @param $hash the dir hash
	 * @param $userid the user id of the owner
	 * @return the result of the deletion
	 */
	function getFileByHashAndDelete($hash,$userid){
		$success = false;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		$hash = mysqli_real_escape_string($connect,$hash);
		$userid = mysqli_real_escape_string($connect,$userid);
		$result = mysqli_query($connect,"Select Filename,Directory from Files  where Hash = '$hash' and UserID = '$userid' limit 1") or die("Can't run database query to get the file to delete: ".mysqli_error($connect));
		if (mysqli_affected_rows($connect) == 1){			
			$localfilename = "";
			$dir = "";
			while ($row = mysqli_fetch_object($result)) {
				$localfilename = $row->Filename;
				$dir = $row->Directory;
				if ($GLOBALS["config"]["Program_Debug"] == 1)
					echo $localfilename."<br>";
			}	
			mysqli_close($connect);	
			if ($localfilename != "" && $dir != "")
				$success = deleteFile($localfilename,$dir,$hash);				
		}
		else{
			$success = false;
		}
		return $success;
	}
	/**
	 * deleteDir deletes a directory
	 * @param $dirname the directory name	
	 */
	function deleteDir($dirname)
	{		
		//Create a session if needed
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$dir = mysqli_real_escape_string($connect,$dirname);
		$owner_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);			
		$result = mysqli_query($connect,"Select * from Files  where Directory = '$dir' and UserID = '".$owner_id."'") or die("Error: 010 ".mysqli_error($connect));
		$success = false;
		if ($GLOBALS["config"]["Program_Debug"] == 1 ){
			echo "entry dir ".$dirname."<br>";					
		}
		while ($row = mysqli_fetch_object($result)) {
			//get the Filename of the file
			$localfilename = $row->Filename;
			$hash = $row->Hash;
		
			if ($row->ReadOnly == 1	)
				return;
			//If the filename is equal to the displayname, we have a dictonary
			if ($row->Filename == $row->Displayname)
			{
				//Process dir delete recursively
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "found dir ".$row->Filename."<br>";
				}
				deleteDir($row->Filename);				
			}
			else
			{
				//Process dir delete recursively
				if ($GLOBALS["config"]["Program_Debug"] == 1 ){
					echo "found file ".$row->Displayname."<br>";
				}
				deleteFile($localfilename,$dir,$hash);	
			}
		}
		//delete the directory entry itself (database)
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		//Delete the directory itself
		$res = mysqli_query($connect,"delete from `Files` where  UserID = '$owner_id' and Filename = '$dir' and Displayname = '$dir' limit 1") or die("Error: 012 ".mysqli_error($connect));	
		//check if a row was affected. If not, the action failed
		if (mysqli_affected_rows($connect) == 0){			
			$success = false;
			//Only for the base dir
			if ($dir == "/")
				$success = true;
		}
		else{
			$success = true;
		}
		if ($GLOBALS["config"]["Program_Debug"] == 1 ){
			echo "dir $dir deleted";
			if ($success == true)
				echo "success";			
		}
		mysqli_close($connect);
		return $success;
	}
	/**
	 * deleteFile deletes a file
	 * @param $filename the file (ending with .dat!)
	 * @param $directory the directory which contains the file
	 * @param $hash the file hash
	 */
	function deleteFile($filename,$directory,$hash)
	{
		//Create new database isntance
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		//Delete the file from the database
		$owner_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);	
		$filename = mysqli_real_escape_string($connect,$filename);
		$directory = mysqli_real_escape_string($connect,$directory);
		$hash = mysqli_real_escape_string($connect,$hash);		
		//Delete local share if given
		$fileid = getFileIDByHash($hash);
		deleteInternalShare($fileid);
		$success = false;
		mysqli_query($connect,"delete from `Files` where  `Filename` = '".$filename."' and UserID = '".$owner_id."' and Directory = '$directory'")or die("Error: 011 ".mysqli_error($connect));		
		if (mysqli_affected_rows($connect) != 1){			
			$success = false;
		}
		else{
			$success = true;
		}
		if ($success = true && mysqli_affected_rows($connect) == 1){			
			$result = mysqli_query($connect,"DELETE FROM `Share` WHERE `Hash` = '".$hash."' and UserID = '".$owner_id."' limit 1") or die("Error: 012 ".mysqli_error($connect));	
			if ($result == true)
				unlink ( getStoragePath().$filename);	
		}
		else{
			$success = false;
		}
		return $success;
	}
	/**
	 *  creates a database snapshot (can take long time!)
	 */
	function createSnapshot()
	{
		if (!isset($_SESSION))
			session_start();
		echo "Starting snapshotting at ".date("D M j G:i:s T Y", time())." launched by ".$_SESSION["user_name"]."<br><hr>";
		$filecount = 0;
		$date = date("H:i:s d.m.y", time());		
		$zipfile = new ZipArchive();	
		$fullPath = getSnapshotsPath().$date.".zip";
		if ($zipfile->open($fullPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		
		if ($handle = opendir(getStoragePath())) {
			while (false !== ($file = readdir($handle))) {			
				if ($file != "." && $file != "..")
				{		
					echo date("D M j G:i:s T Y", time()).": Adding \"".$file."\" to snapshot<br>"; 
					$zipfile->addFile(getStoragePath().$file,$file);	
					$filecount++;
				}
			}		
		}
		closedir($handle);
		echo "Finished snapshotting on ".$fullPath." [$filecount] files<br>";
		$zipfile->addFromString("database.sql",createTableBakup());
		echo "Added database snapshot on ".$fullPath." [1] file";
		$zipfile->Close();
		
	}
	/**
	 * backup the tables
	 * Source http://davidwalsh.name/backup-mysql-database-php
	 * @param $tables the wildcard for the tables
	 */
	function createTableBakup($tables = '*')
	{
		$return = "";
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		//get all of the tables
		if($tables == '*')
		{
			$tables = array();
			$result = mysqli_query($connect,'SHOW TABLES');
			while($row = mysqli_fetch_row($result))
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		
		//cycle through
		foreach($tables as $table)
		{
			$result = mysqli_query($connect,'SELECT * FROM '.$table);
			$num_fields = mysqli_num_fields($result);
			
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysqli_fetch_row(mysqli_query($connect,'SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) 
			{
				while($row = mysqli_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = str_replace("\n","\\n",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j<($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		
		//save file
		
		return $return;
	}
	/**
	 * createZipFile backup the tables
	 * @param $zipfile zipfile in Program_Dir/Temp/
	 * @param $dir the directory 
	 */
	function createZipFile($dir,$zipfile)
	{
		//Create a session if needed
		if (isset($_SESSION) == false)
			session_start();
			
		//Create new database isntance
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$dir = mysqli_real_escape_string($connect,$dir);
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$select = "Select * from Files where UserID = '$user' and Directory = '$dir'";
		$result= mysqli_query($connect,$select);
		while ($row = mysqli_fetch_object($result)) {
			if ($row->Filename == $row->Displayname)
			{
				$zipfile->addEmptyDir((substr($row->Displayname,1)));
				createZipFile(($row->Displayname),$zipfile);
			}
			else
			{				
				$zipfile->addFile(getStoragePath().$row->Filename,(substr( $dir.$row->Displayname, 1 )));
			}
		}		
	}
	/**
	 * startZipCreation backup the tables
	 * start function for createZipFile()
	 * @param $dir the directory 
	 */
	function startZipCreation($dir)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		
		$zipfile = new ZipArchive();	
		$fullPath = getTempPath().getRandomKey(50).".zip";
		if ($zipfile->open($fullPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		createZipFile($dir,$zipfile);
		$zipfile->addFromString("Timestamp.txt",date("D M j G:i:s T Y"));
		$zipfile->Close();
		$file = file_get_contents($fullPath);
		$finfo = new finfo(FILEINFO_MIME_TYPE);	
		if (file_exists($fullPath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $finfo->buffer($file)); 	
			if ($dir == "/")
				$filename = $GLOBALS["Program_Language"]["Files"];
			else
				$filename= str_replace("/","",$dir);
			header('Content-Disposition: attachment; filename='.$filename.'.zip');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($fullPath));
			ob_clean();
			flush();
			readfile($fullPath);
			unlink ($fullPath);				
			exit;
		}
	}
	/**
	 *  check if enough space is here for a copy of a directory
	 * @param $dir the directory 
	 * @return if enough space is here for copying of a dir
	 */
	function isSpaceLeft($dir)
	{
		$value = false;
		$size = 0;		
		//TODO: sql
		//TODO: Bug
		
		if (getDirectorySize($dir) == 0 && endsWith($dir,"/") == false)
		{
			$size = getFileSize($dir,$_SESSION["currentdir"])/1024;			
		}
		else if (getDirectorySize($dir) != 0 && endsWith($dir,"/") != false)
		{
			$size = getDirectorySize($dir);	
		}
		//echo "Size: ".$size;
		
		$complete = $size  + getUsedSpace($_SESSION["user_id"]);
		//echo "Complete used space including new file: ".$complete;
		//echo "Space available: ".$_SESSION["space"] * 1024 * 1024;
		if ($complete < $_SESSION["space"] * 1024 * 1024 )
			$value = true;
		else
			$value = false;
		return $value;
	}
	/**
	 * check if a entry is in a dir
	 * @param $hash the file or dir 
	 * @return the result of the search
	 */
	function isDirectory($hash)
	{
		$folder = false;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$hash = mysqli_real_escape_string($connect,$hash);
		$select = "Select * from Files where UserID = '$user' and Hash = '$hash'";
		$result= mysqli_query($connect,$select);
		while ($row = mysqli_fetch_object($result)) {
			if ($row->Displayname == $row->Filename)
				$folder = true;		
		}
		mysqli_close($connect);	
		return $folder;
	}
	/**
	 *  get a imagepath
	 * @param $Displayname the displayname
	 * @param $Filename the filename (ending with .dat)
	 * @param $MimeType the MimeType
	 * @param $Hash the hash of the file
	 * @param int $thumb create a thumbnail (1) or not (0)
	 * @return a imagepath to display
	 */
	function getImagePath($Displayname,$Filename,$MimeType,$Hash,$thumb=1)
	{
		$imagepath = './Images/mimetypes/page.png';	
		
		if ($Displayname != $Filename){									
			if (isImage($Filename) && $GLOBALS["config"]["Program_Display_Icons_if_needed"] == 1)
			{		
				if ($thumb == 1){
					if (isShared($Hash))
						$imagepath = "index.php?module=image&file=".$Hash."&e=s&t=1";
					else
						$imagepath = "index.php?module=image&file=".$Hash."&t=1";
				}
				else
				{
					$imagepath = "index.php?module=image&file=".$Hash;
				}
			}	
			else if (isImage($Filename) == false )
			{			
				if (file_exists("./Images/mimetypes/".str_replace("/","-",$MimeType).".png"))
						$imagepath = "./Images/mimetypes/".str_replace("/","-",$MimeType).".png";	
			}		
		}
		else
		{
			$imagepath = "./Images/mimetypes/folder.png";
			if (isShared($Hash))
				$imagepath = "./Images/mimetypes/folder-publicshare.png";
		}
		return $imagepath;
	}	
	/**
	 * get the user's storage	
	 * @return the used space in byte
	 */
	function getManagedDataSize()
	{			
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$amount_in_Byte  = 0;		
		$result = mysqli_query($connect,"Select Size from Files")  or die("Error 023: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysqli_close($connect);		
		return $amount_in_Byte ;
	}
	/**
	 * get the last changes of the user's storage
	 * @param $changes the amount of changes
	 */
	function getLastFileSystemChanges($changes = 10)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$changes = mysqli_real_escape_string($connect,$changes);
		if (isset($_SESSION) == false)
			session_start();
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Hash,Filename,Displayname,Uploaded,lastWrite from Files where UserID = '$userID' order by Uploaded desc limit ".$changes)  or die("Error 023: ".mysqli_error($connect));
		
		$array = array ();
		$timestamps = array();
		echo   "<script>
		  $(function() {
			$( \"#accordion\" ).accordion();
		  });
		  </script>";
		echo "<div id=\"accordion\">";
		$current_data = "";
		$i = 0;
		
		while ($row = mysqli_fetch_object($result)) {			
			$array[$row->Displayname] = date("d.m.y",strtotime($row->Uploaded));
			if (isset($timestamps[date("d.m.y",strtotime($row->Uploaded))]) == false){
				 if ($i != 0)
                echo "</ul></div>";
				echo "<h2>". date("d.m.y",strtotime($row->Uploaded))."</h2>";
				echo "<div>";
                echo "<ul class=\"list-group\">";
				$timestamps[date("d.m.y",strtotime($row->Uploaded))] = 1;
			}
			$timestamps[date("d.m.y",strtotime($row->Uploaded))]++;
			echo "<li style='list-style-type: none;'>";
			if ($row->Uploaded != $row->lastWrite){
				echo "<span class=\"elusive icon-edit\"></span> ";		
				if ($row->Displayname != $row->Filename)
					echo "<a href ='index.php?module=file&file=".$row->Hash."'>".$row->Displayname." (".date("d.m.y H:i:s",strtotime($row->lastWrite)).")</a>";
				else
					echo "<a href ='index.php?module=list&dir=".$row->Displayname."'>".$row->Displayname." (".date("d.m.y H:i:s",strtotime($row->lastWrite)).")</a>";
				
			}
			else
			{
				echo "<span class=\"elusive icon-file-new\"></span> ";
				if ($row->Displayname != $row->Filename)
					echo "<a href ='index.php?module=file&file=".$row->Hash."'>".$row->Displayname." (".date("d.m.y H:i:s",strtotime($row->Uploaded)).")</a>";
				else
					echo "<a href ='index.php?module=list&dir=".$row->Displayname."'>".$row->Displayname." (".date("d.m.y H:i:s",strtotime($row->Uploaded)).")</a>";
		}
		
			echo "</li>";	
			$i++;
		}		
		if (count($array) == 0)
		{
			header("Location: index.php?message=no_changes_information");
			exit;
		}			
		echo "</div></div>";
		mysqli_close($connect);		
	}
	//dev only
	/*function fs_transform_timestamp()
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$result = mysqli_query($connect,"Select * from Files")  or die("Error 023: ".mysqli_error($connect));
		$array = array ();
		while ($row = mysqli_fetch_object($result)) {		
			$datum = new DateTime($row->Uploaded);
			$datum_string = $datum->Format("d.m.y H:i:s");	
				//2008-08-07 18:11:31"
			echo "Transformed ".$row->Displayname." ".$row->Uploaded."->".$datum_string."->".$datum->Format("y-m-d H:i:s")."<br>";
			$datum_db = $datum->Format("y-m-d H:i:s");
			$query = mysqli_query($connect,"Update Files SET Uploaded = '$datum_db' where Hash = '".$row->Hash."'");			
		}	
		mysqli_close($connect);				
	}*/
	/**
	 * get the statistics about the filesystem usage
	 */
	function getFileSystemStats()
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Filename,Displayname from Files where UserID = '$userID'")  or die("Error 023: ".mysqli_error($connect));
		$array = array ();			
		while ($row = mysqli_fetch_object($result)) {
			if ($row->Filename != $row->Displayname){
				
				$extension = explode(".",$row->Displayname);				
				$extension = strtolower($extension[count($extension)-1]);
				if (isset($array[$extension]) == false)
				{
					$array[$extension] = 1;
				}
				else
				{
					$array[$extension]++;
				}
			}
		}
		if (count($array) != 0 ){
			$i = 0;
			foreach($array as $key => $val)
			{
				echo "['.".$key."',$val],";
				$i++;
				if ($i == 200)
					break;
			}
		}
		else
		{
				echo "";
		}
		mysqli_close($connect);	
	}
	function rand_color() {
		return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
	}
	function getFileSystemStats2()
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Filename,Displayname from Files where UserID = '$userID'")  or die("Error 023: ".mysqli_error($connect));
		$array = array ();		
		$allFiles = 0;
		while ($row = mysqli_fetch_object($result)) {
			if ($row->Filename != $row->Displayname){
				
				$extension = explode(".",$row->Displayname);				
				$extension = strtolower($extension[count($extension)-1]);
				if (isset($array[$extension]) == false)
				{
					$array[$extension] = 1;
				}
				else
				{
					$array[$extension]++;
				}
				$allFiles++;
			}
		}
		$GLOBALS["colors"] = array();
		if (count($array) != 0 ){
			$i = 1;			
			foreach($array as $key => $val)
			{
				
				$GLOBALS["colors"][$key] = rand_color();
				$color = $GLOBALS["colors"][$key];
				echo "<div class=\"progress-bar\" title = \"*.$key: $val\" style=\"width: ".(100/($allFiles/$val))."%;background-color: ".$color.";\"><span class=\"sr-only\">test</span> </div>";
				$i++;
				if ($i == 200)
					break;
			}				
		}
		else
		{
				echo "";
		}
		mysqli_close($connect);	
	}
	function getFileSystemLegend()
	{		
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select Filename,Displayname from Files where UserID = '$userID'")  or die("Error 023: ".mysqli_error($connect));
		$array = array ();		
		$allFiles = 0;
		while ($row = mysqli_fetch_object($result)) {
			if ($row->Filename != $row->Displayname){
				
				$extension = explode(".",$row->Displayname);				
				$extension = strtolower($extension[count($extension)-1]);
				if (isset($array[$extension]) == false)
				{
					$array[$extension] = 1;
				}
				else
				{
					$array[$extension]++;
				}
				$allFiles++;
			}
		}
		if (count($array) != 0 ){
			$i = 1;
			
			foreach($array as $key => $val)
			{
				echo "
					 <li class=\"list-group-item\">
						<span class=\"badge\" style=\"background-color: ".$GLOBALS["colors"][$key]."\"> $val</span>
						*.$key
					  </li>";
				$i++;
				if ($i == 200)
					break;
			}				
		}
		else
		{
				echo "";
		}
		mysqli_close($connect);	
	}
	/**
	 * correct the extension of a file (for statistics)
	 */
	function getFilenameWithLowercasedExtension($displayname)
	{
		$extension = explode(".",$displayname);		
		$oldextension = $extension[count($extension)-1];
		$extension = strtolower($extension[count($extension)-1]);
		return str_replace($oldextension,$extension,$displayname);
	}
	/**
	 * get the root of a dir
	 * @param $dirname the dir as a full name
	 * @param $userid the user id
	 */
	function getRootDirectory($dirname,$userid){
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$dir = mysqli_real_escape_string($connect,$dirname);
		$owner_id = mysqli_real_escape_string($connect,$userid);			
		$result = mysqli_query($connect,"Select Directory from Files  where Displayname = '$dir' and Filename = '$dir' and UserID = '$owner_id' limit 1") or die("Error: 010 ".mysqli_error($connect));
		$parentDirectory = "/";
		while ($row = mysqli_fetch_object($result)) {
			$parentDirectory = $row->Directory;
		}
		return $parentDirectory;
		mysqli_close($connect);	
	}
	/**
	 * get the root of a dir
	 * @param $hash the hash of an entry
	 * @returns the parent directory
	 */
	function getRootDirectoryByEntryHash($hash){
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		$hash = mysqli_real_escape_string($connect,$hash);			
		$result = mysqli_query($connect,"Select Directory_ID from Files  where Hash = '$hash' limit 1") or die("Error: 010 ".mysqli_error($connect));
		$parentDirectory = -1;
		while ($row = mysqli_fetch_object($result)) {
			$parentDirectory = $row->Directory_ID;
		}		
		mysqli_close($connect);	
		return $parentDirectory;
	}
	/**
	 * get the upload date of a dir
	 * @param $dir the dir of an entry
	 * @returns the upload date
	 */
	function getUploadDateOfDir($dir){
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		$dir = mysqli_real_escape_string($connect,$dir);			
		$owner_id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$result = mysqli_query($connect,"Select Uploaded from Files  where Filename = '$dir' and UserID = '$owner_id' limit 1") or die("Error: 010 ".mysqli_error($connect));
		$uploaded = -1;
		while ($row = mysqli_fetch_object($result)) {
			$uploaded = $row->Uploaded;
		}		
		mysqli_close($connect);	
		return $uploaded;
	}
	/**
	 * set the upload date of a dir
	 * @param $dir the dir of an entry
	 * @param $uploaded the timestamp
	 */
	function setUploadDateOfDir($dir,$uploaded){
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		$dir = mysqli_real_escape_string($connect,$dir."/");			
		$owner_id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$uploaded  = mysqli_real_escape_string($connect,$uploaded);			
		$result = mysqli_query($connect,"Update Files SET Uploaded =  '$uploaded' where Filename = '$dir' and UserID = '$owner_id' limit 1") or die("Error: 010 ".mysqli_error($connect));
				
		mysqli_close($connect);	
	}	
	/**
	 * Share a fileystem entry (external)
	 * @param $file the hash
	 */
	function createExternalShare($file)
	{
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$file = mysqli_real_escape_string($connect,$file);
		$found = false;
		if ($GLOBALS["config"]["Program_Share_Anonymously"] == 0)
			$code = getFileByHash($file).getRandomKey($GLOBALS["config"]["Program_Share_Link_Length"]);
		else
			$code = getRandomKey($GLOBALS["config"]["Program_Share_Link_Length"]);				
		do{					
			mysqli_query($connect,"Select *  from `Share` where  Extern_ID = '$code'")  or die("Error: 028");
			if (mysqli_affected_rows($connect) > 0)
			{
				if ($GLOBALS["config"]["Program_Share_Anonymously"] == 0)
					$code = getFileByHash($file).getRandomKey($GLOBALS["config"]["Program_Share_Link_Length"]);
				else
					$code = getRandomKey($GLOBALS["config"]["Program_Share_Link_Length"]);
				$found = true;					
			}
		}while($found == true );	
		$fileCheck = mysqli_query($connect,"Select * from Files  where Hash = \"".$file."\" limit 1") or die("Error: 029");
		while ($rowFile = mysqli_fetch_object($fileCheck)) {	
			if ($rowFile->Displayname == $rowFile->Filename){
				$isFile = false;
				$folder = $rowFile->Displayname;
			}else
				$isFile = true;
		}	
		$insert = "INSERT INTO Share (Hash,UserID,Extern_ID,Used) VALUES ('$file',$userID,'$code',0)";			
		$result = mysqli_query($connect,$insert) or die("Error: 028");
		mysqli_error($connect);	
		if ($isFile)
			header ("Location: index.php?module=file&file=$file");
		else
			header ("Location: index.php?module=list");
		exit;
	}
	/**
	 * delete the external share
	 * @param $file the hash
	 * @param $userID the owner user id
	 *  the user will be redirected
	 */
	function deleteExternalShare($file,$userID){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		mysqli_query($connect,"delete  from `Share` where  Hash = '$file' and UserID = $userID") or die("Error: 026");		
		$fileCheck = mysqli_query($connect,"Select * from Files  where Hash = \"".$file."\" limit 1") or die("Error: 029");
		while ($rowFile = mysqli_fetch_object($fileCheck)) {	
			if ($rowFile->Displayname == $rowFile->Filename){
				$isFile = false;
				$folder = $rowFile->Displayname;
			}else
				$isFile = true;
		}
		mysqli_close($connect);		
		if ($isFile)
			header ("Location: index.php?module=file&file=$file");
		else
			header ("Location: index.php?module=list");
	}
		/**
	 * get the hashcode from share
	 * @param $hash the share id	 * 
	 * @return the hashcode
	 */
	function getHashByShare($hash){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$share = mysqli_real_escape_string($connect,$hash);
		$result = mysqli_query($connect,"Select * from Share  where Extern_ID = '$share' limit 1") or die("Error: 029".mysqli_error($connect));	
		$hash = "";
		while ($row = mysqli_fetch_object($result)) {
			$hash = $row->Hash;			
		} 		
		mysqli_close($connect);	
		return $hash;
	}
	/**
	 * get the external share
	 * @param $share the share id
	 * @param $viewonly determines if the file should be displayed instead of downloaded
	 *  the user will get the content of the share
	 */
	function getExternalShare($share,$viewonly){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$result = mysqli_query($connect,"Select * from Share  where Extern_ID = '$share' limit 1") or die("Error: 029".mysqli_error($connect));	
		$used = 0;
		while ($row = mysqli_fetch_object($result)) {
			$hash = $row->Hash;
			$used = $row->Used;
			$resultDownload = mysqli_query($connect,"Select * from Files  where Hash = \"".$hash."\" limit 1") or die("Error: 029");
			
			while ($row = mysqli_fetch_object($resultDownload)) {																	
				$filenamenew = $row->Filename;
				$displayname = $row->Displayname;
				$user = $row->UserID;
			}				
			$used++;

			if (!isset($_SESSION["user_logged_in"]))
				$_SESSION["user_id"] = $user;
			mysqli_query($connect,"Update Share set Used = $used where Extern_ID = '$share'");
			if ($filenamenew != $displayname){
			$fullPath = $GLOBALS["Program_Dir"]."Storage/".$filenamenew; 	
			$_SESSION["current_file"] =  $fullPath;		
				if (file_exists($fullPath)) {
				
						header('Content-Description: File Transfer');
						header('Content-Type: ' .mime_content_type($fullPath)); 
						if ($viewonly == false)
							header('Content-Disposition: attachment; filename='.$displayname);
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						header('Content-Length: ' . filesize($fullPath));
						ob_clean();
						flush();
						readfile($fullPath);					
				}			
			}
			else
			{
				//include $GLOBALS["Program_Dir"]."Includes/Program.inc.php";
				//TODO: Possibility to leak by foreign user id?
				//echo $filenamenew;
				startZipCreation($filenamenew);
				if (!isset($_SESSION["user_logged_in"]))
					$_SESSION["user_id"] = -1;
			}			
		} 
		
		if (mysqli_affected_rows($connect) == 0)
		{
			header("Location: index.php?message=DeadLink"); 
		}
		mysqli_close($connect);	
	}
	/**
	 * get an free name for the internal storage folder
	 * @return an sting e. g. 12424892749724729.dat
	 */
	function getFreeStorageFileName(){		
		$found =false;
		$code = getRandomKey(50);
		do{				
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			mysqli_query($connect,"Select ID  from `Files` where  Filename = '$code.dat'");
			if (mysqli_affected_rows($connect) > 0)
			{
				$code = getRandomKey(50);
				$found = true;					
			}
		}while($found == true );			
		return $code;
	}
	/**
	* get the current storage path
	* When a directory exists in the local path, the program assumes that the storage dir is in the current dir
	* otherwise, the path will be assumed as absolute path
	* @return a storage path
	*/
	function getStoragePath(){
		if (file_exists($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]) == true){
			return $GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/";
		}
		else
		{
			return $GLOBALS["config"]["Program_Storage_Dir"];
		}
	}
	/**
	* get the current snapshots path
	* When a directory exists in the local path, the program assumes that the snapshots dir is in the current dir
	* otherwise, the path will be assumed as absolute path
	* @return a snapshots path
	*/
	function getSnapshotsPath(){
		if (file_exists($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]) == true){
			return $GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/";
		}
		else
		{
			return $GLOBALS["config"]["Program_Snapshots_Dir"];
		}
	}
	/**
	* get the current temp path
	* When a directory exists in the local path, the program assumes that the temp dir is in the current dir
	* otherwise, the path will be assumed as absolute path
	* @return a temp path
	*/
	function getTempPath(){
		if (file_exists($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Temp_Dir"]) == true){
			return $GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Temp_Dir"]."/";
		}
		else
		{
			return $GLOBALS["config"]["Program_Temp_Dir"];
		}
	}
	/**
	* Adds a new internal share for user $user
	* @param $id the file id
	* @param $user the user id
	* @param $mode the permission mode
	* @return the result of the action
	*/
	function createInternalShare($id,$user,$mode = 4)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$id = mysqli_real_escape_string($connect,$id);
		$user = mysqli_real_escape_string($connect,$user);		
		$mode = mysqli_real_escape_string($connect,$mode);	
		//echo "Insert into LocalShare (FileID,TargetUser,Mode) Values ($id,$user,$mode)";			
		$res = mysqli_query($connect,"Insert into LocalShare (FileID,TargetUser,Mode) Values ($id,$user,$mode)") or die (mysqli_error($connect));		
		if ($res === TRUE){			
			return true;
		}
		else
		{
			return false;
		}
	}	
	/**
	* Delete an existing internal share for user $user
	* @param $id the file id
	* @return the result of the action
	*/
	function deleteInternalShare($id)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$id = mysqli_real_escape_string($connect,$id);			
		$res = mysqli_query($connect,"Delete from LocalShare where FileID = '$id'") or die (mysqli_error($connect));		
		if ($res === TRUE){			
			return true;
		}
		else
		{
			return false;
		}
	}	
	/**
	* Delete an existing internal share for user $user
	* @param $id the file id
	* @return the result of the action
	*/
	function deleteLocalShare($fileid, $userid)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$id = mysqli_real_escape_string($connect,$fileid);			
		$userid = mysqli_real_escape_string($connect,$userid);			
		$res = mysqli_query($connect,"Delete from LocalShare where FileID = '$id' and TargetUser = '$userid'") or die (mysqli_error($connect));		
		if (mysqli_affected_rows($connect) != 0){			
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	* Check if a file is shared locally
	* @param $hash the file hash
	* @param $user the user id
	* @return the result of the action
	*/
	function isLocalShared($hash,$user){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hash = mysqli_real_escape_string($connect,$hash);		
		$user = mysqli_real_escape_string($connect,$user);		
		
		$res = mysqli_query($connect,"Select LocalShare.ID,TargetUser from LocalShare inner join Files f on f.ID = LocalShare.FileID where LocalShare.TargetUser = '$user' and f.Hash =  '$hash' or (LocalShare.TargetUser = '$user' and LocalShare.FileID = '$hash')") or die (mysqli_error($connect));		

		if (mysqli_affected_rows($connect) !=  0){
			return true;
		}
		else
		{
			return false;
		}
	}	
	/**
	* Check if a file is shared locally
	* @param $hash the file hash
	* @param $user the user id
	* @return the result of the action
	*/
	function isLocalSharedAnyUser($hash,$user){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hash = mysqli_real_escape_string($connect,$hash);
//TODO: FIX FAIL!		
		$user = mysqli_real_escape_string($connect,$user);		
		
		$res = mysqli_query($connect,"Select LocalShare.ID,TargetUser from LocalShare inner join Files f on f.ID = LocalShare.FileID where f.Hash =  '$hash' or (LocalShare.TargetUser = '$user' and LocalShare.FileID = '$hash')") or die (mysqli_error($connect));		

		if (mysqli_affected_rows($connect) !=  0){
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	* Get permissions on a file (0=nothing;4=r;6=w)
	* @param $hash the file hash
	* @return the result of the action
	*/
	function getLocalShareMode($hash){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hash = mysqli_real_escape_string($connect,$hash);			
		$res = mysqli_query($connect,"Select LocalShare.Mode from LocalShare inner join Files f on f.ID = LocalShare.FileID where f.Hash =  '$hash'") or die (mysqli_error($connect));		

		while ($row = mysqli_fetch_object($res)) {
			return $row->Mode;
		}
		return 0;
	}	
	/**
	* Get permissions on a file (0=nothing;4=r;6=w)
	* @param $hash the file hash
	* @return the result of the action
	*/
	function getLocalShares($hash){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hash = mysqli_real_escape_string($connect,$hash);			
		$res = mysqli_query($connect,"Select LocalShare.TargetUser,LocalShare.FileID,u.User,f.Displayname from LocalShare inner join Files f on f.ID = LocalShare.FileID inner join Users u on u.ID = LocalShare.TargetUser where f.Hash =  '$hash'") or die (mysqli_error($connect));		
		if (mysqli_affected_rows($connect) != 0)
			echo "<h3>".sprintf($GLOBALS["Program_Language"]["ShareInfo"],getFileByHash($hash))."</h3>";
		while ($row = mysqli_fetch_object($res)) {
			echo $GLOBALS["Program_Language"]["Shared"]." - <b>".$GLOBALS["Program_Language"]["Username"].": ".$row->User."</b> <a href ='index.php?module=localshare&delete=true&user=".$row->TargetUser."&file=".$row->FileID."'>".$GLOBALS["Program_Language"]["Delete"]."</a><br>";
		}	

	}
	function getSharesOfUser(){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);			
		$res = mysqli_query($connect,"Select f.Hash, LocalShare.TargetUser,LocalShare.FileID,u.ID,f.UserID,f.Displayname from LocalShare inner join Files f on f.ID = LocalShare.FileID inner join Users u on u.ID = LocalShare.TargetUser where u.ID =  '$user'") or die (mysqli_error($connect));		
		while ($row = mysqli_fetch_object($res)) {
			$link = "<a href = 'index.php?module=file&file=".$row->Hash."'>".$row->Displayname."</a>";
			echo $GLOBALS["Program_Language"]["Shared"].": ".$link." ".sprintf($GLOBALS["Program_Language"]["SharedBy"],getUserName($row->UserID))." <a href ='index.php?module=localshare&delete=true&user=".$row->TargetUser."&file=".$row->FileID."&returnto=manageshares'>".$GLOBALS["Program_Language"]["ShareRemove"]."</a><br>";
		}
		if (mysqli_affected_rows($connect) == 0){
			echo str_replace(
				array("##nothingshared"),
				array($GLOBALS["Program_Language"]["NothingShared"]),
				$GLOBALS["template"]["NothingShared"]
			);
		}		
	}
	function getSharesByUser(){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);			
		$res = mysqli_query($connect,"Select f.Hash,LocalShare.TargetUser,LocalShare.FileID,f.UserID,f.Displayname from LocalShare inner join Files f on f.ID = LocalShare.FileID where f.UserID =  '$id'") or die (mysqli_error($connect));		
		while ($row = mysqli_fetch_object($res)) {
			$link = "<a href = 'index.php?module=file&file=".$row->Hash."'>".$row->Displayname."</a>";
			echo $GLOBALS["Program_Language"]["Shared"].": ".$link." ".sprintf($GLOBALS["Program_Language"]["SharedTo"],getUserName($row->TargetUser))." <a href ='index.php?module=localshare&delete=true&user=".$row->TargetUser."&file=".$row->FileID."&returnto=manageshares'>".$GLOBALS["Program_Language"]["ShareRemove"]."</a><br>";
		}	
		if (mysqli_affected_rows($connect) == 0){
			echo str_replace(
				array("##nothingshared"),
				array($GLOBALS["Program_Language"]["NothingShared"]),
				$GLOBALS["template"]["NothingShared"]
			);
		}	
	}
	/**
	* Check if a user id is the user id of the owner of a file
	* @param $hash the file hash
	* @param $owner the owner id
	* @return the result of the action
	*/
	function isOwner($hash,$owner){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hash = mysqli_real_escape_string($connect,$hash);	
		$owner = mysqli_real_escape_string($connect,$owner);	
		$res = mysqli_query($connect,"Select UserID from Files where Hash =  '$hash' and UserID = '$owner'") or die (mysqli_error($connect));		

		if (mysqli_affected_rows($connect) !=  0){
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	* Check if a user id is the user id of the owner of a file
	* @param $hash the file hash
	* @param $owner the owner id
	* @return the result of the action
	*/
	function isDirOwner($dir,$owner){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hash = mysqli_real_escape_string($connect,$dir);	
		$owner = mysqli_real_escape_string($connect,$owner);	
		$res = mysqli_query($connect,"Select UserID from Files where Displayname =  '$dir' and UserID = '$owner'") or die (mysqli_error($connect));		

		if (mysqli_affected_rows($connect) !=  0){
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	* Get the username of the owner of a file
	* @param $hash the file hash
	* @return the result of the action
	*/
	function getOwner($hash){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$hash = mysqli_real_escape_string($connect,$hash);			
		$res = mysqli_query($connect,"Select u.User from Files f inner join Users u on u.ID = f.UserID where f.Hash = '$hash'") or die (mysqli_error($connect));		
		$user ="";
		while ($row = mysqli_fetch_object($res)) {
			$user = $row->User;
		}	
		return $user;
	}	
?>