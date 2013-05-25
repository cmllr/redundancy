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
			$localfilename = "";
			$dir = "";
			while ($row = mysql_fetch_object($result)) {
				$localfilename = $row->Filename;
				$dir = $row->Directory;
			}	
			mysql_close($connect);	
			if ($localfilename != "" && $dir != "")
				deleteFile($localfilename,$dir,$hash);
			$success = true;
		}
		//Case 2: the user wants to delete a directory
		else if (isset($_SESSION["user_name"]) &&  ((isset($_GET["dir"]) && $_GET["dir"] != "/") || (isset($_POST["dir"]) && $_POST["dir"] != "/")))
		{
			//TODO: ADD setting responsible for this
			if (isset($_GET["dir"]))
					$todelete = $_GET["dir"];
				else 	
					$todelete = $_POST["dir"];						
			deleteDir($todelete);		
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
	
?>
