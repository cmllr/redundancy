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
	 * This file provides a dialog to create a new directory
	 */	
	//Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//only proceed if a post parameter is set
	$failed = false;
	if ($_SESSION["role"] != 3 && isset($_POST["directory"]) && endsWith($_POST["directory"],"/") == false && $_POST["directory"] != "")
	{			
		//only proceed if the user is logged in and we have a valid user_id
		if (isset($_SESSION['user_id']))
		{					
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
	
			$dirs = explode(";",$_POST["directory"]);
			for ($i = 0; $i < count($dirs);$i++){
				$dir_parts = explode("/",$dirs[$i]);
				$dir_parts_before = $_SESSION["currentdir"];
				$last = $_SESSION["currentdir"];
				for ($x = 0; $x < count($dir_parts);$x++)
				{
					$basedir = mysqli_real_escape_string($connect,$dir_parts_before);
					$newdir = mysqli_real_escape_string($connect,$dir_parts[$x]);
					$validatorResult = Guard::createDirValidator($basedir,$newdir);					
					if ($dir_parts[$x] != "" && empty($dir_parts[$x]) == false && $validatorResult == 0){
						echo "dir".$dir_parts[$x]."<br>";					
						echo "in ".$dir_parts_before."<br>";					
						$exists = isFileExisting($dir_parts[$x],$dir_parts_before);
						if ($exists == true)
							$failed = true;
						
						if (strlen($dir_parts[$x]) <= $GLOBALS["config"]["Program_FileSystem_Name_Max_Length"]){
							
							createDir(mysqli_real_escape_string($connect,$dir_parts_before),mysqli_real_escape_string($connect,$dir_parts[$x]));			
						}
						else
						{									
							break;
						}
						$dir_parts_before .= $dir_parts[$x]."/";		
					}
					else
					{
						$failed = true;
						break;
					}
				} 					
			}
			if ($failed == true)
			{
				if (isset($_POST["method"]) == false)
					header("Location: ./index.php?module=list&message=createdir_fail");
			}
			else
			{
				if (isset($_POST["method"]) == false)
					header("Location: ./index.php?module=list&message=createdir_success");
			}
		}		
	}
	else if(isset($_POST["directory"]) == true && (endsWith($_POST["directory"],"/") == true || $_POST["directory"] == "" || $failed == true))
	{
		header("Location: ./index.php?module=list&message=wronginput");
	}	
	else if ($_SESSION["role"] == 3)
	{
		header("Location: index.php?module=list&message=readonly");
	}	
?>
<h2><?php echo $GLOBALS["Program_Language"]["New_Directory"]." ". $_SESSION["currentdir"];?></h2>
<div class="panel-body">
<form class="form-horizontal" method="POST" action="index.php?module=createdir">	
	<div class="form-group">
		<div class="alert alert-info"><?php
				echo $GLOBALS["Program_Language"]["multiple_dirs"];
			?>
		</div>	
		<label for="pass" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["New_Directory_Short"];?></label>
		<div class="col-lg-9">
			<input type="text" class="form-control"  name="directory" placeholder="<?php echo $GLOBALS["Program_Language"]["New_Directory_Short"];?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<input class = 'btn-block btn btn-default' type=submit name=submit value="<?php echo $GLOBALS["Program_Language"]["New_Directory_Button"];?>">		
		</div>
	</div>
</form>
</div>