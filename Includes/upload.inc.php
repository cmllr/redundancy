<?php
if (isset($_SESSION) == false)
	session_start();
	$success = false;
	
	if ($_SESSION["role"] != 3){
		if (isset($_FILES["userfile"]) && file_exists($_FILES['userfile']['tmp_name']))
		{		
			$basepath = $GLOBALS["Program_Dir"];		
			$uploaddir =$basepath."Storage/";
			$time = date("Ymdhs", time());
			$found =false;
			$code = getRandomKey(50);
			do{				
				include $basepath ."Includes/DataBase.inc.php";
				mysqli_query($connect,"Select *  from `Files` where  Filename = '$code.dat'");
				if (mysqli_affected_rows($connect) > 0)
				{
					$code = getRandomKey(50);
					$found = true;					
				}
			}while($found == true );			
			$newfilename = $code.".dat";//$time.".dat"; 
			if ($_FILES['userfile']['name'] != ".htaccess" && $_FILES['userfile']['name'] != "index.php" && $_FILES['userfile']['name'] != "index.html" && strpos($_FILES['userfile']['name'],"<") === false) { 	
				$dbpath = $basepath ."Includes/DataBase.inc.php";					
				$userid = $_SESSION['user_id'];	
				$hash = md5($newfilename);	
				$client_ip = getIP();
				$timestamp = time();
				$uploadtime= date("D M j G:i:s T Y",$timestamp);
				$dir = $_SESSION['currentdir'];				
				$oldfilename = mysqli_real_escape_string($connect,($_FILES['userfile']['name']));
				$size = filesize($_FILES['userfile']['tmp_name']);
				$directory_id =  getDirectoryID($dir);				
				$file_mime = file_get_contents($_FILES['userfile']['tmp_name']); 
				$finfo = new finfo(FILEINFO_MIME_TYPE);		
				$mimetype =  $finfo->buffer($file_mime);
				//TODO FIX ERROR HERE
				if ((getUsedSpace($_SESSION["user_id"])  + $size < $_SESSION["space"] * 1024 * 1024) && fs_file_exists($oldfilename,$dir) == false){
					include $dbpath;	
					$insert = "INSERT INTO Files (Filename,Displayname,Hash,UserID,IP,Uploaded,Size,Directory,Directory_ID, Client,MimeType) VALUES ('$newfilename','$oldfilename','$hash','$userid','$client_ip','$uploadtime','$size','$dir','$directory_id','".$_SERVER['HTTP_USER_AGENT']."','$mimetype')";
					//echo $insert;
					$inser_query = mysqli_query($connect,$insert) or die ("Error: 030:" .mysqli_error());
					if ($inser_query == true)
						 move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$newfilename);
					mysqli_close($connect);	
					$success =true;
				}
				else
				{
					header("Location: index.php?message=4");
				}
				
			} else {
				header("Location: index.php?message=5");
			}
		}			
	}
	if (isset($_POST["api_key"]))
	{
		echo "Command_Result:{$success}";
		exit;	
	}		
	else
	{
		if ($success == true && $GLOBALS["config"]["Program_Redirect_Upload"] == 1)
		{
			header("Location: index.php?module=file&file=$hash");
		}
	}
?>
<div class ="contentWrapper">
<?php	
	echo "<h2>".$GLOBALS["Program_Language"]["Upload_Title"]." ".$_SESSION['currentdir'].".</h2>";
?>
<form enctype="multipart/form-data" action="index.php?module=upload" method="POST">
<p> 
	<input name="userfile" type="file"/>
</p>
    <input  type='submit' value='<?php echo $GLOBALS["Program_Language"]["Upload"];?>'>
</form>
</div>