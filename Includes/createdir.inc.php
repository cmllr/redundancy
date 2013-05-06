<?php
	if (isset($_SESSION) == false)
			session_start();
	if (isset($_POST["directory"]))
	{		
		if (isset($_SESSION['user_id']))
		 {				
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";				
			$userid = mysql_real_escape_string($_SESSION['user_id']);		
			$uploadfile =  $_SESSION['currentdir'] . $_POST['directory']."/";			
			$uploaddirectory = $_SESSION['currentdir'];
			$timestamp = time();
			$uploadtime= date("D M j G:i:s T Y", $timestamp);
			$hash = md5($uploadfile.$uploadtime);	
			$client_ip = getIP();	
			$result = mysql_query("Select * from Files  where UserID = '$userid' and Filename ='".mysql_real_escape_string($uploadfile)."'") or die("Error: ".mysql_error());
			$i = 0;
			while ($row = mysql_fetch_object($result)) {
			  $i++;					
			}	
			//If whe already have a directory like this -> abort
			if ($i > 0)
			{
				header("Location: index.php?module=createdir");
				exit;
			}	
			$insert = "INSERT INTO Files (Filename,Displayname,Hash,UserID,IP,Uploaded,Size,Directory) VALUES ('$uploadfile','$uploadfile','$hash','$userid','$client_ip','$uploadtime',0,'$uploaddirectory')";
			$inserquery = mysql_query($insert);
			mysql_close($connect);					
		}		
	}
?>
<div id = "login">
<br>
<form method="POST" action="index.php?module=createdir" align = "center">
<tag>Create a new directory <?php echo $_SESSION["currentdir"];?><r></tag><input name="directory">
.<input type=submit name=submit value="Create">
</form>
</div>
