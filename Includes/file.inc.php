<div id = "files">
<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Include database file
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	//remember the hash
	$hash = mysqli_real_escape_string($connect,$_GET["file"]);
	//Searh for a file with this hash
	$ergebnis = mysqli_query($connect,"Select Filename,Displayname,Uploaded,Client,Hash,MimeType from Files  where Hash = '$hash' limit 1") or die("Error: ".mysqli_error($connect));	
	while ($row = mysqli_fetch_object($ergebnis)) {
		//Remember the file for further processes
		$_SESSION["current_file"] = $GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$row->Filename;
		
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
		else 
			echo "<p id = 'preview'><img  src='".fs_get_imagepath($row->Displayname,$row->Filename,$row->MimeType,$row->Hash)."'>";
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
		echo "<a href ='index.php?module=delete&file=$row->Hash'>".$GLOBALS["Program_Language"]["Delete"]."</a></p></div>";	
	}
	//Close the connection if finished
	mysqli_close($connect);
?>
</div>
