<?php
	//var_dump($_FILES);
	if (isset($_POST["method"]) == false && isset($_GET["upload"]) == false && isset($_FILES["userfile"]) == false)
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
	//var_dump($_FILES);
	//var_dump($_SESSION);
	//var_dump($_POST);
if (isset($_SESSION) == false)
	session_start();
	$success = false;
	$toobig = false;
	$alreadyExisting = false;
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$config_size = $max_upload*1024*1024;
	$filecount = 0;
	$results = array();
	if ($GLOBALS["config"]["Program_Debug"] == 1){
		log_event("upload",print_r($_FILES, true));
		log_event("upload",isset($_FILES));
	}
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
					$uploaddir =getStoragePath();
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
					$oldfilename = mysqli_real_escape_string($connect,utf8_decode($_FILES['userfile']['name'][$key]));					
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
				##<?php				
				foreach ($results as $key => $value ){										
					echo $key.": ";
					if ($value == Result::TooBig)
						echo $GLOBALS["Program_Language"]["upload_toobig"];
					else if ($value == Result::FileIsExisting)
						echo $GLOBALS["Program_Language"]["upload_exists"];
					else if ($value == Result::OK)
						echo $GLOBALS["Program_Language"]["upload_done"];	
					if ($value == 0)
						header("HTTP/1.0 200 OK");	
					else
						header("HTTP/1.0 409 Conflict");		
				}
				?>##
			<?php			
		}
	}
?>
<?php	
	echo "<h2>".$GLOBALS["Program_Language"]["Upload_Title"]." ".$_SESSION['currentdir'].".</h2>";
?>

<div id = "result">

</div>

<div class="panel panel-default">
	<?php
		echo "<h3 class=\"text-center\">";	
		echo $GLOBALS["Program_Language"]["dictUploadTitle"]."</h3>";
	?>
<form class ="dropzone panel-body" id = "my-awesome-dropzone" action="index.php?module=upload2" method="POST" >
 <div class = "dz-message">
	<center>
		<span class="elusive icon-file-new glyphIcon text-center"></span>
	</center>
 </div>
	<div class="fallback">
    <input name="userfile" type="file" multiple />
  </div>
</form>

</div>
<script>
Dropzone.options.myAwesomeDropzone = {
  paramName: "userfile", 
  uploadMultiple: true,
  addRemoveLinks: true,
  parallelUploads: 1,
  accept: function(file, done) {
    if (file.name == "Weltherrschaft-ToDo.rtf") {
      done("Naha, you don't.");
    }
    else { done(); }
  },
  dictRemoveFile: "<?php echo $GLOBALS["Program_Language"]["dictRemoveFile"];?>",
  dictCancelUpload: "<?php  echo $GLOBALS["Program_Language"]["dictCancelUpload"];?>",
  dictDefaultMessage: "<?php  echo $GLOBALS["Program_Language"]["dictDefaultMessage"];?>",  
  dictCancelUploadConfirmation: "<?php  echo $GLOBALS["Program_Language"]["dictCancelUploadConfirmation"];?>",  
};
</script>
