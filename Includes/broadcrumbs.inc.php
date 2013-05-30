<ul id = 'broadcrumb'>
<?php
	//start a session if needed
	if (isset($_SESSION) == false)
		session_start();	
	//include the database file
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	//Get the path parts
	$dirs = explode("/",$_SESSION["currentdir"]);
	//parts_before is a kind of prefix to display a complete link step by step
	$parts_before = "";
	//link suffix to display
	$suffix = "";
	//Display several kinds of links for cases like copy file, move filey, copy dir, move dir and the regular display
	if (isset($_GET["move"]) && isset($_GET["file"]))
			$suffix = "module=list&dir=/&move=true&file=".mysqli_real_escape_string($connect,$_GET["file"])."&dir=/";
	else if (isset($_GET["copy"]) && isset($_GET["file"]))
			$suffix = "module=list&dir=/&copy=true&file=".mysqli_real_escape_string($connect,$_GET["file"])."&dir=/";
	else if (isset($_GET["copy"]) && isset($_GET["source"]))
			$suffix = "module=list&dir=/&copy=true&source=".mysqli_real_escape_string($connect,$_GET["source"])."&old_root=".mysqli_real_escape_string($connect,$_GET["old_root"])."&target=/";
				else if (isset($_GET["move"]) && isset($_GET["source"]))
			$suffix = "module=list&dir=/&move=true&source=".mysqli_real_escape_string($connect,$_GET["source"])."&old_root=".mysqli_real_escape_string($connect,$_GET["old_root"])."&target=/";
	else
			$suffix = "module=list&dir=/";
	//Display home link
	echo "<li ><a href= 'index.php?$suffix'><img src = './Images/folder_user.png'>Home</a></li>";
	//Display the links as a kind of broadcrumb
	for ($i = 0; $i < count($dirs); $i++)
	{
		if ($dirs[$i] != ""){
			echo " / <li id = 'broadcrumb'><a href= 'index.php?$suffix".$parts_before.$dirs[$i]."/'><img src = './Images/folder.png'>".$dirs[$i]."</a></li>";
			$parts_before = $parts_before.$dirs[$i]."/";
		}	
	}
?>
</ul>
<br>

