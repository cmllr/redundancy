<div id = "files">
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
	 * This file displays the file properties and a preview (if enabled)
	 */
	//Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Include database file
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	//remember the hash
	$hash = mysqli_real_escape_string($connect,$_GET["file"]);
	//Searh for a file with this hash
	$ergebnis = mysqli_query($connect,"Select Filename,Displayname,Uploaded,Client,Hash,MimeType,Directory from Files  where Hash = '$hash' limit 1") or die("Error: ".mysqli_error($connect));	
	while ($row = mysqli_fetch_object($ergebnis)) {
		$_SESSION["currentdir"] = $row->Directory;
		//Remember the file for further processes
		$_SESSION["current_file"] = $GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$row->Filename;
		$_SESSION["current_file_hash"] = $row->Hash;
		echo "<div class ='contentWrapper' >";
			include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
		//Get file image or icon
		if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isImage($row->Filename) == 1)
			echo "<p id = 'preview'><img src='index.php?module=image'>";	
		else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isVideo($row->Filename) == true)
			echo "<p id = 'preview'><video src='./Includes/player.inc.php' controls>Your browser does not support the <code>video</code> element.</video>";
		else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isAudio($row->Filename) == true)
			echo "<p id = 'preview'><audio src='./Includes/player.inc.php' controls>Your browser does not support the <code>audio</code> element.</audio>";
		else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isText($row->Filename) == true) {
			echo "<p id = 'preview'><textarea  cols='120' rows='5'>";
			include "./Includes/player.inc.php";
			echo "</textarea>";	}
		else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1)
			echo "<p id = 'preview'><img  src='".fs_get_imagepath($row->Displayname,$row->Filename,$row->MimeType,$row->Hash,0)."'>";
		else
			echo "<p id = 'preview'><img  src='./Images/page.png'>";
		//Display the name
		echo "</p><p class = 'filename'>".htmlentities(utf8_decode($row->Displayname))."</p>";	
		$date = strtotime($row->Uploaded);
		//Display the client, a browser is identified by the "Mozilla" entry in the user agent
		$client = $row->Client;
		if (strpos($client,"Mozilla") === false && $row->Client != NULL )
			echo "<p class ='source'>".$GLOBALS["Program_Language"]["Uploaded_API"]."</a></p>";
		else		
			echo "<p class ='source'>".$GLOBALS["Program_Language"]["Uploaded_Browser"]."</a></p>";		
		if (isShared($hash))
		{
			$sharetext = fs_getShareLink($hash);
			echo "<p class = 'sharelink'>".$GLOBALS["Program_Language"]["Share_Link"]."</p><input type ='text' cols='70' rows='2' value ='$sharetext'></input>";	
		}			
		echo "<p class ='buttons'><a href ='index.php?module=download&file=$row->Hash'>".$GLOBALS["Program_Language"]["Download"]."</a>";		
		//Display links
		if (isShared($hash) == false)
			echo "<a href ='index.php?module=download&module=share&file=".$row->Hash."&new=true'>".$GLOBALS["Program_Language"]["Share"]."</a>";	
		else
			echo "<a href = 'index.php?module=share&file=".$row->Hash."&delete=true'>".$GLOBALS["Program_Language"]["Unshare"]."</a>";		
		//Display delete link
		echo "<a href ='index.php?module=delete&file=$row->Hash'>".$GLOBALS["Program_Language"]["Delete"]."</a>";	
		echo "<a href ='index.php?module=rename&file=$row->Hash'>".$GLOBALS["Program_Language"]["Rename_title"]."</a></p></div>";	
		
	}
	//Close the connection if finished
	mysqli_close($connect);
?>
</div>
