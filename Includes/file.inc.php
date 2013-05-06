<div id = "files">
<?php
	if (isset($_SESSION) == false)
			session_start();
	include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
	
		$hash = mysql_real_escape_string($_GET["file"]);
		$ergebnis = mysql_query("Select * from Files  where Hash = '$hash'") or die("Error: ".mysql_error());
	while ($row = mysql_fetch_object($ergebnis)) {
		if (isImage($row->Displayname) == 1)
			echo "<img id = 'preview' src='".$_GLOBALS["Program_Storage"].$row->Filename."'><br>";
		echo $row->Displayname."<br>";	
		$date = strtotime($row->Uploaded);
		echo date("j.n.Y H:i",$date) ."<br>";
		echo round($row->Size/1024,2)." KByte<br>";	
		echo "<a href ='index.php?module=delete&file=$row->Hash'>Delete</a>";	
	}
	mysql_close($connect);
?>
</div>
