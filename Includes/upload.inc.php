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
		if ($GLOBALS["config"]["Program_Debug"] == 1){
			echo "success:".($success ? "true" : "false");
			echo "toobig:".($toobig ? "true" : "false");
			echo "already:".($alreadyExisting? "true" : "false");
		}
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
				foreach ($uploadresults as $key => $value ){
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