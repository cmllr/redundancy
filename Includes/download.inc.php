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
	 * This file is used to generated downloads
	 */
	//Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed
	if (!isset($_SESSION) == false);
		session_start();
	//Include database file
	if (isset($GLOBALS["Program_Dir"]) != false)
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
	else{
		include "./DataBase.inc.php";
		$GLOBALS["config"] = parse_ini_file("../"."Redundancy.conf");
		$GLOBALS["Program_Dir"] = $GLOBALS["config"]["Program_Path"];
	}
	//get the display- and filename
	$userID = mysqli_real_escape_string($connect,$_SESSION['user_id']);
	$currentdir = mysqli_real_escape_string($connect,$_SESSION['currentdir']);
	$hash = $_GET["file"];
	$result;
	$localfilename = "";
	$displayname =  "";
	$mimetype = "";
	
	if (isLocalShared(getFileIDByHash($hash),$userID) == true){	
		$result = mysqli_query($connect,"Select Filename,Displayname,MimeType from Files  where  Hash = \"".mysqli_real_escape_string($connect,$_GET["file"])."\"") or die("Error 013: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {																	
			$localfilename = $row->Filename;
			$displayname = $row->Displayname;	
			$mimetype = $row->MimeType;
		}		
	}
	else{
		$result = mysqli_query($connect,"Select Filename,Displayname,MimeType from Files  where UserID = \"" .  $userID . "\" and Directory = \"" .$currentdir."\" and Hash = \"".mysqli_real_escape_string($connect,$_GET["file"])."\"") or die("Error 013: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {																	
			$localfilename = $row->Filename;
			$displayname = $row->Displayname;	
			$mimetype = $row->MimeType;
		}
	}	
	//close databse connection
	mysqli_close($connect);
	//Start the download
	$fullPath = getStoragePath().$localfilename; 
		ob_clean();
	if (file_exists($fullPath)) {
		header('Content-Description: File Transfer');
		header('Content-Type: ' . $mimetype); 
		header("Content-Disposition: attachment; filename=".$displayname."");
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($fullPath));
		ob_clean();
		flush();
		readfile($fullPath);	
		exit;
	}
	else
	{
		echo "<h2>The File does not exists</h2>";
		exit;
	}
?>