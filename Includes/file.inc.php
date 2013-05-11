<div id = "files">
<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Include database file
	include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
	//remember the hash
	$hash = mysql_real_escape_string($_GET["file"]);
	//Searh for a file with this hash
	$ergebnis = mysql_query("Select * from Files  where Hash = '$hash'") or die("Error: ".mysql_error());	
	while ($row = mysql_fetch_object($ergebnis)) {
		//Remember the file for further processes
		$_SESSION["current_file"] = $_SESSION["Program_Dir"]."Storage/".$row->Filename;
		
		echo "<div class ='contentWrapper' >";
		//If the file is a image -> Display it
		if (isImage($_SESSION["Program_Dir"]."Storage/".$row->Filename) == 1)
			echo "<p id = 'preview'><img src='index.php?module=image'>";
		else
			echo "<p id = 'preview'><img  src='./Images/page.png'>";
		//Display the name
		echo "</p><p class = 'filename'>".$row->Displayname."</p>";	
		$date = strtotime($row->Uploaded);
		//Display the client, a browser is identified by the "Mozilla" entry in the user agent
		$client = $row->Client;
		if (strpos($client,"Mozilla") === false && $row->Client != NULL )
			echo "<p class ='source'>Uploaded via client</a></p>";
		else		
			echo "<p class ='source'>Uploaded via browser</a></p>";
		//Check if file is shared
		$userID = $_SESSION["user_id"];
		$result = mysql_query("Select * from Share  where UserID = '$userID' and Hash ='".$hash."'") or die("Error: ".mysql_error());	
		$shared = false;
		//Get Share infos (if existing)
		while ($rowShare = mysql_fetch_object($result)) {
			$sharetext = $_SERVER["SERVER_NAME"].$_GLOBALS["Program_Storage"]."index.php?share=".$rowShare->Extern_ID;
			echo "<p class = 'sharelink'>Share link</p><input type ='text' cols='70' rows='2' value ='$sharetext'></input>";	
			$shared = true;
		} 
		echo "<p class ='buttons'><a href ='index.php?module=download&file=$row->Hash'>Download</a>";		
		//Display links
		if ($shared == false)
			echo "<a href ='index.php?module=download&module=share&file=".$row->Hash."&new=true'>Share</a>";	
		else
				echo "<a href = 'index.php?module=share&file=".$row->Hash."&delete=true'>Unshare</a>";		
		//Display delete link
		echo "<a href ='index.php?module=delete&file=$row->Hash'>Delete</a></p></div>";	
	}
	//Close the connection if finished
	mysql_close($connect);
?>
</div>
