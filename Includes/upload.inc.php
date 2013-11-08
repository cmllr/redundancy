<?php
	if (isset($_GET["upload"]) == false && isset($_FILES["userfile"]) == false)
		include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
?>
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
	//require_once ("checkuri.inc.php");

if (isset($_SESSION) == false)
	session_start();
	$success = false;
	$toobig = false;
	$alreadyExisting = false;
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$config_size = $max_upload*1024*1024;
	$filecount = 0;
	$results = array();
	if ($_SESSION["role"] != 3 && isGuest() == false){		
		if (isset($_FILES["userfile"]))
		{		
			$move_process = false;			
			//Get the amount of files 			
			foreach($_FILES['userfile']['tmp_name'] as $key => $tmp_name ){			
				$filecount++;
			}
			
			foreach ($_FILES['userfile']['tmp_name'] as $key => $tmp_name ){
				//Check if the file is to big for the configured size
				//If true, remark
				if ($config_size < filesize($_FILES['userfile']['tmp_name'][$key])){
					$success = false;
					$toobig = true;
				}
				//The next steps are only processed if the following conditions are true
				//File not to big
				//$_FILES['userfile'] set
				//The temp file is existing
				if ($toobig == false && isset($_FILES["userfile"]) && file_exists($_FILES['userfile']['tmp_name'][$key]))
				{				
					//**********************File diectories**********************
					$basepath = $GLOBALS["Program_Dir"];		
					$uploaddir =$basepath.$GLOBALS["config"]["Program_Storage_Dir"]."/";
					//**********************Get a fitting name for the internal filesystem**********************
					$time = date("Ymdhs", time());					
					$code = getFreeStorageFileName();
					$newfilename = $code.".dat";					
					$dbpath = $basepath ."Includes/DataBase.inc.php";	
					include "$dbpath";
					$userid = $_SESSION['user_id'];	
					//**********************Get the file properties**********************
					$hash = md5($newfilename);	
					$client_ip = getIP();					
					if (isset($_POST["timestamp"]) == true){
						$timestamp 	= strtotime(mysqli_real_escape_string($connect,$_POST["timestamp"]));
					}
					else{
						$timestamp = time();
					}						
					$uploadtime= date("Y-m-d H:i:s",$timestamp);
					$dir = $_SESSION['currentdir'];				
					$oldfilename = mysqli_real_escape_string($connect,($_FILES['userfile']['name'][$key]));					
					$size = filesize($_FILES['userfile']['tmp_name'][$key]);
					$directory_id =  getDirectoryID($dir);				
					$file_mime = file_get_contents($_FILES['userfile']['tmp_name'][$key]); 
					$finfo = new finfo(FILEINFO_MIME_TYPE);		
					$mimetype =  $finfo->buffer($file_mime);
					
					//**********************Insert file into filessytem**********************
					if ((getUsedSpace($_SESSION["user_id"])  + $size < $_SESSION["space"] * 1024 * 1024) && isFileExisting($oldfilename,$dir) == false){
						include $dbpath;	
						$insert = "INSERT INTO Files (Filename,Displayname,Hash,UserID,IP,Uploaded,Size,Directory,Directory_ID, Client,MimeType,lastWrite) VALUES ('$newfilename','$oldfilename','$hash','$userid','$client_ip','$uploadtime','$size','$dir','$directory_id','".$_SERVER['HTTP_USER_AGENT']."','$mimetype','$uploadtime')";
						//Insert file into db
						$insert_query = mysqli_query($connect,$insert) or die ("Error: 030:" .mysqli_error());
						//Was the action successfull?
						if ($insert_query == true){
							$move_process = move_uploaded_file($_FILES['userfile']['tmp_name'][$key], $uploaddir.$newfilename);
							if ($move_process != true)
								updateLastWriteOfDirectory($dir_id);
						}
						if ($move_process == false)
						{
							//if the file can't be moved, the file will be removed out of the filesystem
							$remove = "Delete from Files where Filename = '$newfilename'";								
							mysqli_query($connect,$remove);							
						}							
						mysqli_close($connect);	
						//Remark the result of the process
						if ($move_process != false){							
							$success =true;
							$results[$dir.$oldfilename] = Result::OK;
						}else{
							if ($GLOBALS["config"]["Program_Debug"] == 1)
								echo "fail:File could not be moved";
							$success = false;
						}
					}
					else if (isFileExisting($oldfilename,$dir) == false)
					{
						//Second case: The file is to big. Nothing was done.
						//Remember the reason.
						$success = false;
						$toobig = true;						
						if ($GLOBALS["config"]["Program_Debug"] == 1){
							echo "fail: No space left";
							var_dump($_SESSION);
						}
						if (!isset($_POST["method"]))
							header("Location: index.php?message=nospace");
						$results[$dir.$oldfilename] = Result::TooBig;
					}
					else if (isFileExisting($oldfilename,$dir) != false)
					{
						//Third case: The file was already existing
						//Remember the reason
						$success = false;
						$toobig = false;
						$alreadyExisting = true;
						if ($GLOBALS["config"]["Program_Debug"] == 1)
							echo "fail: File exists";		
						$results[$dir.$oldfilename] = Result::FileIsExisting;
					}					
				}				
			}			
		}		
	}
	else
	{
		if (!isset($_POST["method"]))
			header("Location: index.php?message=readonly");
	}
	if (isset($_POST["method"]))
	{
		echo getSingleNodeXMLDoc($success ? "true" : "false");
		exit();
	}		
	else if (isset($_POST["method"]) == false && isset($_FILES["userfile"]))
	{		
		if ($GLOBALS["config"]["Program_Redirect_Upload"] == 1){
			if ($success == true){			
				if ($filecount == 1)
				{
					header("Location: index.php?module=file&file=$hash&message=upload_success");
					exit();
				}
				else
				{
					header("Location: index.php?module=list&message=upload_success");
					exit();
				}			
			}
			else{				
				if ($toobig == false){				
					if ($alreadyExisting == false){
						header("Location: index.php?module=list&message=upload_failx");
						exit();
					}
					else{
						header("Location: index.php?module=list&message=file_exists");
						exit();
					}
				}
				else{
					header("Location: index.php?module=list&message=phpini");
					exit();
				}				
			}
		}
		else{
			?>
				<h2><?php echo $GLOBALS["Program_Language"]["upload_finished_title"]; ?></h2>
				<ul>
				<?php				
				foreach ($results as $key => $value ){
					?>
						<?php if($value != 0) :?>
							<li style='list-style: none;'><span class="errorValue elusive icon-remove glyphIcon"></span>
						<?php else :?>
							<li style='list-style: none;'><span class=" elusive icon-ok glyphIcon"></span>
						<?php endif;?>
					<?php						
					echo $key.": ";
					if ($value == Result::TooBig)
						echo $GLOBALS["Program_Language"]["upload_toobig"];
					else if ($value == Result::FileIsExisting)
						echo $GLOBALS["Program_Language"]["upload_exists"];
					else if ($value == Result::OK)
						echo $GLOBALS["Program_Language"]["upload_done"];
					echo "</li>";
				}
				?>
				</ul>
			<?php			
		}
	}
?>
<?php	
	echo "<h2>".$GLOBALS["Program_Language"]["Upload_Title"]." ".$_SESSION['currentdir'].".</h2>";
?>
<form enctype="multipart/form-data" action="index.php?module=upload" method="POST">
<p> 
	<input class = 'btn btn-default'  name="userfile[]" type="file" multiple/>
</p>
<small>Maximum: <?php echo measurementCorrection($config_size).". ". $GLOBALS["Program_Language"]["Upload_SubTitle"];?></small>
    <input class = 'btn btn-default'  type='submit' value='<?php echo $GLOBALS["Program_Language"]["Upload"];?>'>	
</form>