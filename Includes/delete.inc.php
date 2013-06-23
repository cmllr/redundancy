<?php 
	//Create a session if needed
	if (isset($_SESSION) == false)
		 session_start();
	//Case 1: the user wants to delete a file
	if ($_SESSION["role"] != 3){
		if (isset($_SESSION['user_name']) && (isset($_GET["file"]) || isset($_POST["file"]))) 
		{ 	 	 
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$localfilename = "";
			if (isset($_GET["file"]))
				$hash = mysqli_real_escape_string($connect,$_GET["file"]);
			else
				$hash = mysqli_real_escape_string($connect,$_POST["file"]);
			//step 1: get the Filename on the server file system
			$result = mysqli_query($connect,"Select * from Files  where Hash = '$hash' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: 007 ".mysqli_error($connect));
			$localfilename = "";
			$dir = "";
			while ($row = mysqli_fetch_object($result)) {
				$localfilename = $row->Filename;
				$dir = $row->Directory;
			}	
			mysqli_close($connect);	
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
	else{	
		if ($GLOBALS["config"]["Program_Debug"] != 1){
			header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]);
			exit;
		}	
	}
?>