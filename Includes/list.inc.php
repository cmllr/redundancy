<div id = "lst" >
<?php
	
	if (isset($_SESSION) == false)
			session_start();
	if (isset($_GET["dir"])){
		$dir = $_GET["dir"];
		$_SESSION["currentdir"]	= $dir;	
	}
	else
		$dir = $_SESSION["currentdir"];

	include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
	include $_SESSION["Program_Dir"]."Includes/broadcrumbs.inc.php";	
	$user = $_SESSION["user_id"];	
	$result = mysql_query("Select * from Files  where UserID = '$user' and Directory ='".mysql_real_escape_string($dir)."'") or die("Error: ".mysql_error());
	if (mysql_affected_rows() > 0)
		echo "<table id = 'filetable'><tr><th></th><th>Name</th><th>Uploaded</th><th>Actions</th></tr>";
	while ($row = mysql_fetch_object($result)) {
		$date = strtotime($row->Uploaded);
		if ($row->Displayname == $row->Filename)
			echo "<tr><td><img id='filetype' src='./Images/directory.png'></td><td>".$row->Displayname."</td><td>".date("j.n.Y H:i",$date)."</td><td><a href = 'index.php?module=list&dir=".$row->Filename."'>View</a> <a href = 'index.php?module=delete&dir=".$row->Filename."'>Delete</a></td></tr>";
		else
			echo "<tr><td><img id='filetype' src='./Images/directory.png'></td><td>".$row->Displayname."</td><td>".date("j.n.Y H:i",$date)."</td><td><a href = 'index.php?module=file&file=".$row->Hash."'>View</a> <a href = 'index.php?module=delete&file=".$row->Hash."'>Delete</a></td></tr>";
	}
	echo "</table>";
	mysql_close($connect);
if ($_SESSION["currentdir"] != "/")
	echo "<a href ='index.php?module=delete&dir=".$_SESSION['currentdir']."'>Delete</a>";
?>
</div>