<?php 
	//Create a session if needed
	if (isset($_SESSION) == false)
		 session_start();
	//Case 1: the user wants to delete a file
	if ($_SESSION["role"] != 3){
		if (isset($_SESSION['user_name']) && (isset($_GET["file"]) || isset($_POST["file"]))) 
		{ 	 	 
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
			$localfilename = "";
			if (isset($_GET["file"]))
				$hash = mysql_real_escape_string($_GET["file"]);
			else
				$hash = mysql_real_escape_string($_POST["file"]);
			//step 1: get the Filename on the server file system
			$result = mysql_query("Select * from Files  where Hash = '$hash' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: 007 ".mysql_error());
			while ($row = mysql_fetch_object($result)) {
				$localfilename = $row->Filename;
			}	
			mysql_close($connect);
			//step 2: delete the database entry of the file
			//step 3: delete share entry if existand
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
			if ($localfilename != ""){
				$loesch = mysql_query("DELETE FROM `Files` WHERE `Filename` = '".$localfilename."' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: 008 ".mysql_error());	
				$loesch = mysql_query("DELETE FROM `Share` WHERE `Hash` = '".$hash."' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: 009 ".mysql_error());			
			}
			//Delete the file on the local server file system
			unlink ($_SESSION["Program_Dir"]."Storage/".$localfilename);
			$success = true;
		}
		//Case 2: the user wants to delete a directory
		else if (isset($_SESSION["user_name"]) &&  ((isset($_GET["dir"]) && $_GET["dir"] != "/") || (isset($_POST["dir"]) && $_POST["dir"] != "/")))
		{
			if (isset($_GET["dir"]))
				deleteDir($_GET["dir"]);
			else 	
				deleteDir($_POST["dir"]);
			$success = true;
		}
	}
	if (isset($_POST["api_key"]))
	{
		echo "Command_Result:{$success}";
		exit;	
	}	
	//Goto the main directory
	header("Location: index.php?module=list&dir=/");	
?>
<?php
	//Delete directory function
	function deleteDir($dirname)
	{
		//Create a session if needed
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
		$dir = mysql_real_escape_string($dirname);		
		$result = mysql_query("Select * from Files  where Directory = '$dir' and UserID = '".$_SESSION['user_id']."'") or die("Error: 010 ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			//get the Filename of the file
			$localfilename = $row->Filename;
			$hash = $row->Hash;
			//If the filename is equal to the displayname, we have a dictonary
			if ($row->Filename == $row->Displayname)
			{
				//Process dir delete recursively
				deleteDir($row->Filename);			
			}
			else
			{
				//Create new database isntance
				include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
				//Delete the file from the database
				mysql_query("delete from `Files` where  `Filename` = '".$localfilename."' and UserID = '".$_SESSION['user_id']."' and Directory = '$dir'")or die("Error: 011 ".mysql_error());		
				$loesch = mysql_query("DELETE FROM `Share` WHERE `Hash` = '".$hash."' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: 012 ".mysql_error());			
				//Delete it from the local server filesystem
				unlink ( $_SESSION["Program_Dir"]."Storage/".$localfilename);				
			}
		}
		//delete the directory entry itself (database)
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		//Delete the directory itself
		mysql_query("delete from `Files` where   UserID = '".$_SESSION['user_id']."' and Filename = '$dir' and Displayname = '$dir'") or die("Error: 012 ".mysql_error());	
		//close connection
		mysql_close($connect);
	}
?>
