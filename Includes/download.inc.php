<?php
	//start a session if needed
	if (isset($_SESSION) == false);
		session_start();
	//Include database file
	include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
	//get the display- and filename
	$result = mysql_query("Select * from Files  where UserID = \"" .  $_SESSION['user_id'] . "\" and Directory = \"" .$_SESSION['currentdir']."\" and Hash = \"".mysql_real_escape_string($_GET["file"])."\"") or die("Error 013: ".mysql_error());
	while ($row = mysql_fetch_object($result)) {																	
		$filenamenew = $row->Filename;
		$displayname = $row->Displayname;
	}	
	//close databse connection
	mysql_close($connect);
	$fullPath = $_SESSION["Program_Dir"]."Storage/".$filenamenew; 
	//Create the download if the file is existant
	if (file_exists($fullPath)) {
		header('Content-Description: File Transfer');
		header('Content-Type: ' .mime_content_type($filenamenew)); 
		header('Content-Disposition: attachment; filename='.$displayname);
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
