<?php
		
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//only proceed if a post parameter is set
	
	if ($_SESSION["role"] != 3 && isset($_POST["directory"]) && endsWith($_POST["directory"],"/") == false )
	{			
		//only proceed if the user is logged in and we have a valid user_id
		if (isset($_SESSION['user_id']))
		{					
			//include the dataBase file
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
			//remember the user id, the new directory, the current directory, the directory without path timestamp hash and so on...
			$userid = mysql_real_escape_string($_SESSION['user_id']);		
			$newdirectory =  $_SESSION['currentdir'] . $_POST['directory']."/";			
			$uploaddirectory = $_SESSION['currentdir'];
			$filenameonly = $_POST['directory'];
			$timestamp = time();
			$uploadtime= date("D M j G:i:s T Y", $timestamp);
			$hash = md5($newdirectory.$uploadtime);	
			$client_ip = getIP();	
			$dir_id = getDirectoryID($uploaddirectory); 		
			if (fs_file_exists($_POST['directory'],$uploaddirectory) == false)
			{			
				//create the new directory
				$insert = "INSERT INTO Files (Filename,Displayname,Filename_only,Hash,UserID,IP,Uploaded,Size,Directory,Directory_ID,Client) VALUES ('$newdirectory','$newdirectory','$filenameonly','$hash','$userid','$client_ip','$uploadtime',0,'$uploaddirectory','$dir_id','".$_SERVER['HTTP_USER_AGENT']."')";			
				$inserquery = mysql_query($insert) or die("Error: 004 ".mysql_error());						
			}			
			mysql_close($connect);					
		}		
	}
?>
<form method="POST" action="index.php?module=createdir" align = "center">
<div class = 'contentWrapper'><tag>Create a new directory <?php echo $_SESSION["currentdir"];?><r></tag><input name="directory">
<input type=submit name=submit value="Create"></div>
</form>