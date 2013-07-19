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
	$ergebnis = mysqli_query($connect,"Select * from Files  where Hash = '$hash'") or die("Error: ".mysqli_error($connect));	
	while ($row = mysqli_fetch_object($ergebnis)) {
		//Remember the file for further processes
		$_SESSION["current_file"] = $GLOBALS["Program_Dir"]."Storage/".$row->Filename;
		
		echo "<div class ='contentWrapper' >";
			include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
		//If the file is a image -> Display it
		if (isImage($row->Filename) == 1)
			echo "<p id = 'preview'><img src='index.php?module=image'>";
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
		//Check if file is shared
		$userID = $_SESSION["user_id"];
		$result = mysqli_query($connect,"Select * from Share  where UserID = '$userID' and Hash ='".$hash."' limit 1") or die("Error: ".mysqli_error($connect));	
		$shared = false;
		//Get Share infos (if existing)
		while ($rowShare = mysqli_fetch_object($result)) {
		
		if ($GLOBALS["config"]["Program_HTTPS_Redirect"] == 1)
			
			$sharetext = "https://".$_SERVER["SERVER_NAME"].$GLOBALS["config"]["Program_Share_Dir"]."index.php?share=".$rowShare->Extern_ID;
		else
			$sharetext = "http://".$_SERVER["SERVER_NAME"].$GLOBALS["config"]["Program_Share_Dir"]."index.php?share=".$rowShare->Extern_ID;
		echo "<p class = 'sharelink'>".$GLOBALS["Program_Language"]["Share_Link"]."</p><input type ='text' cols='70' rows='2' value ='$sharetext'></input>";	
		$shared = true;
		echo "<p class ='source'>".$rowShare->Used ." ".$GLOBALS["Program_Language"]["Share_Accessed"]."</p>";
		} 

			
		echo "<p class ='buttons'><a href ='index.php?module=download&file=$row->Hash'>".$GLOBALS["Program_Language"]["Download"]."</a>";		
		//Display links
		if ($shared == false)
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
