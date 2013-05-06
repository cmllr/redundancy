<?php 
if (isset($_SESSION) == false)
	 session_start();
//Case 1: the user wants to delete a file
if (isset($_SESSION['user_name']) && isset($_GET["file"]))
{ 	 	 
include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
$localfilename = "";
$hash = mysql_real_escape_string($_GET["file"]);
//step 1: get the Filename on the server file system
$result = mysql_query("Select * from Files  where Hash = '$hash' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: ".mysql_error());
while ($row = mysql_fetch_object($result)) {
	$localfilename = $row->Filename;
}	
mysql_close($connect);
//step 2: delete the database entry of the file
include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
if ($localfilename != ""){
	$loesch = mysql_query("DELETE FROM `Files` WHERE `Filename` = '".$localfilename."' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: ".mysql_error());		
}
//Delete the file on the local file system
unlink ( $_SESSION["Program_Dir"]."Storage/".$localfilename);
}
//Case 2: the user wants to delete a directory
else if (isset($_SESSION["user_name"]) && isset($_GET["dir"]) && $_GET["dir"] != "/")
{
	$dir = mysql_real_escape_string($_GET["dir"]);	
	include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
	$result = mysql_query("Select * from Files  where Directory = '$dir' and UserID = '".$_SESSION['user_id']."'") or die("Error: ".mysql_error());
	while ($row = mysql_fetch_object($result)) {
		//TODO: Delete recursively!
		$localfilename = $row->Filename;
		if ($row->Filename == $row->Displayname)
		{
			//delete directories in the directory
			mysql_query("delete from `Files` where  `Filename` = '".$row->Filename."' and `Displayname` = '".$row->Displayname."' and UserID = '".$_SESSION['user_id']."' and Directory = '$dir'");
		}
		else
		{
			//remove filename
			mysql_query("delete from `Files` where  `Filename` = '".$localfilename."' and UserID = '".$_SESSION['user_id']."' and Directory = '$dir'");		
			unlink ( $_SESSION["Program_Dir"]."Storage/".$localfilename);
		}
	}
	//delete the directory entry itself (database)
	mysql_query("delete from `Files` where   UserID = '".$_SESSION['user_id']."' and Filename = '$dir' and Displayname = '$dir'");
	mysql_close($connect);
}
//Goto the main directory
header("Location: index.php?module=list&dir=/");
	
?>
