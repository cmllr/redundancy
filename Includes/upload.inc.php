<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * The upload process is runned out of this file
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
if (isset($_SESSION) == false)
	session_start();
	$success = false;
	
	if ($_SESSION["role"] != 3){
		$filecount = 0;
		if (isset($_FILES["userfile"]))
		{		
			foreach($_FILES['userfile']['tmp_name'] as $key => $tmp_name ){			
				$filecount++;
			}
			foreach ($_FILES['userfile']['tmp_name'] as $key => $tmp_name ){
				if (isset($_FILES["userfile"]) && file_exists($_FILES['userfile']['tmp_name'][$key]))
				{				
					$basepath = $GLOBALS["Program_Dir"];		
					$uploaddir =$basepath.$GLOBALS["config"]["Program_Storage_Dir"]."/";
					$time = date("Ymdhs", time());
					$found =false;
					$code = getRandomKey(50);
					do{				
						include $basepath ."Includes/DataBase.inc.php";
						mysqli_query($connect,"Select ID  from `Files` where  Filename = '$code.dat'");
						if (mysqli_affected_rows($connect) > 0)
						{
							$code = getRandomKey(50);
							$found = true;					
						}
					}while($found == true );			
					$newfilename = $code.".dat";//$time.".dat"; 
					if ($_FILES['userfile']['name'][$key] != ".htaccess" && $_FILES['userfile']['name'][$key] != "index.php" && $_FILES['userfile']['name'][$key] != "index.html" && strpos($_FILES['userfile']['name'][$key],"<") === false) { 	
						$dbpath = $basepath ."Includes/DataBase.inc.php";					
						$userid = $_SESSION['user_id'];	
						$hash = md5($newfilename);	
						$client_ip = getIP();
						$timestamp = time();
						$uploadtime= date("D M j G:i:s T Y",$timestamp);
						$dir = $_SESSION['currentdir'];				
						$oldfilename = mysqli_real_escape_string($connect,($_FILES['userfile']['name'][$key]));
						$size = filesize($_FILES['userfile']['tmp_name'][$key]);
						$directory_id =  getDirectoryID($dir);				
						$file_mime = file_get_contents($_FILES['userfile']['tmp_name'][$key]); 
						$finfo = new finfo(FILEINFO_MIME_TYPE);		
						$mimetype =  $finfo->buffer($file_mime);
						//TODO FIX ERROR HERE
						if ((getUsedSpace($_SESSION["user_id"])  + $size < $_SESSION["space"] * 1024 * 1024) && fs_file_exists($oldfilename,$dir) == false){
							include $dbpath;	
							$insert = "INSERT INTO Files (Filename,Displayname,Hash,UserID,IP,Uploaded,Size,Directory,Directory_ID, Client,MimeType) VALUES ('$newfilename','$oldfilename','$hash','$userid','$client_ip','$uploadtime','$size','$dir','$directory_id','".$_SERVER['HTTP_USER_AGENT']."','$mimetype')";
							//echo $insert;
							$inser_query = mysqli_query($connect,$insert) or die ("Error: 030:" .mysqli_error());
							if ($inser_query == true)
								 move_uploaded_file($_FILES['userfile']['tmp_name'][$key], $uploaddir.$newfilename);
							
							/*if ($GLOBALS["config"]["Program_Crypt_FileSystem"] == 1)
							{
								$datei = fopen($uploaddir.$newfilename,"r+");
								$content =  fgets($datei);
								fclose($datei);
								$datei = fopen($uploaddir.$newfilename,"w+");
								fwrite($datei,fs_file_crypt($content,$_SESSION["fs_hash"])); 
								fclose($datei);
								mysqli_query($connect,"Update Files Set Crypted = 1 where Hash = '$hash'");
							}*/
							mysqli_close($connect);	
							$success =true;
						}
						else
						{
							header("Location: index.php?message=nospace");
						}
						
					} else {
						header("Location: index.php?message=notallowed");
					}
				}				
			}			
		}		
	}
	else
	{
			header("Location: index.php?message=readonly");
	}
	if (isset($_POST["api_key"]))
	{
		echo "Command_Result:{$success}";
		exit;	
	}		
	else
	{
		if ($success == true && $GLOBALS["config"]["Program_Redirect_Upload"] == 1 && $filecount == 1 )
		{
			header("Location: index.php?module=file&file=$hash");
		}
		else if ($success == true && $GLOBALS["config"]["Program_Redirect_Upload"] == 1 && $filecount != 1)
		{
			header("Location: index.php?module=list");
		}		
	}
?>
<div class ="contentWrapper">
<?php
	if (isset($_GET["upload"]) == false)
		include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
?>
<?php	
	echo "<h2>".$GLOBALS["Program_Language"]["Upload_Title"]." ".$_SESSION['currentdir'].".</h2>";
?>
<form enctype="multipart/form-data" action="index.php?module=upload" method="POST">
<p> 
	<input name="userfile[]" type="file" multiple/>
</p>
<small><?php echo $GLOBALS["Program_Language"]["Upload_SubTitle"];?></small>
    <input  type='submit' value='<?php echo $GLOBALS["Program_Language"]["Upload"];?>'>
</form>
</div>