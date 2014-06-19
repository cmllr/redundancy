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
	if (isset($_POST["method"]) == false && isset($_GET["upload"]) == false && isset($_FILES["userfile"]) == false)
		include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
	if (isset($_SESSION) == false)
		session_start();
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$config_size = $max_upload*1024*1024;
	$uploadresults = null;	
	if ($_SESSION["role"] != 3 && isGuest() == false){				
		if (isset($_FILES["userfile"]))
			$uploadresults = uploadFiles($_FILES,$_SESSION["currentdir"]);	
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
				foreach ($uploadresults as $key => $value ){										
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
