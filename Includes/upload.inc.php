<?php
if (isset($_SESSION) == false)
	session_start();
	if (isset($_FILES["userfile"]))
	{		
		$basepath = $_SESSION["Program_Dir"];		
		$uploaddir =$basepath."Storage/";
		$time = date("Ymdhs", time());
		$newfilename = $time.".dat"; 
		if ($_FILES['userfile']['name'] != "index.php" && $_FILES['userfile']['name'] != "index.html" && move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$newfilename)) { 	
			$dbpath = $basepath ."Includes/DataBase.inc.php";					
			$userid = $_SESSION['user_id'];	
			$hash = md5($newfilename);	
			$client_ip = getIP();
			$timestamp = time();
			$uploadtime= date("D M j G:i:s T Y",$timestamp);
			$dir = $_SESSION['currentdir'];			
			$oldfilename = $_FILES['userfile']['name'];
			$size = filesize($uploaddir.$newfilename);
			
			if (getUsedSpace($_SESSION["user_name"]) *1024 *1024+ $size <= $_SESSION["space"] * 1024 * 1024){
				include $dbpath;	
				$insert = "INSERT INTO Files (Filename,Displayname,Hash,UserID,IP,Uploaded,Size,Directory) VALUES ('$newfilename','$oldfilename','$hash','$userid','$client_ip','$uploadtime','$size','$dir')";
				$inser_query = mysql_query($result) or die ("Error:" .mysql_error());
				mysql_close($connect);	
			}
			else
			{
				header("Location: index.php?module=error&reason=Error Code 2&size=$size");
			}
			
		} else {
			
		}
	}
?>
<div id = "login">
<center>
<div id ="upload">
<?php	
	echo "Upload file into " .$_SESSION['currentdir'].".";
?>
<br>
<br>
<form enctype="multipart/form-data" action="index.php?module=upload" method="POST">
Datei <input  name="userfile" type="file">
<input  type='submit' value='Upload'>
</form>
</div>
</center>
</div>
