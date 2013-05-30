<?php
	//start a session if needed
	if (isset($_SESSION) == false);
		session_start();
	//Include database file
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
	//get the display- and filename
	$result = mysqli_query($connect,"Select * from Files  where UserID = \"" .  $_SESSION['user_id'] . "\" and Directory = \"" .$_SESSION['currentdir']."\" and Hash = \"".mysqli_real_escape_string($connect,$_GET["file"])."\"") or die("Error 013: ".mysqli_error($connect));
	while ($row = mysqli_fetch_object($result)) {																	
		$filenamenew = $row->Filename;
		$displayname = $row->Displayname;
	}	
	//close databse connection
	mysqli_close($connect);
	$fullPath = $GLOBALS["Program_Dir"]."Storage/".$filenamenew; 
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
