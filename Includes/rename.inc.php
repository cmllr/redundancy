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
	 * A file can be renamed using the dialog of this file.
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
			
	//only proceed if a post parameter is set
	if ($_SESSION["role"] != 3  )
	{		
		//only proceed if the user is logged in and we have a valid user_id
		if (isset($_SESSION['user_id']))
		{		
			if (isset($_POST["newname"]) && strpos($_POST["newname"],"<") === false){
				if (isset($_GET["source"]) && isset($_GET["old_root"])){
					include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
					$source = mysqli_real_escape_string($connect,$_GET["source"]);
					$target = mysqli_real_escape_string($connect,$_POST["newname"]);
					$old_root= mysqli_real_escape_string($connect,$_GET["old_root"]);
					createDir($old_root,$target);
					$Dir_ID = getDirectoryID($source);
					//TODO: DIR renmae
					//Step 1 create new dir (new name) - check
					moveContents($source,$_SESSION["currentdir"].$target);
					//Step 2 move contents -check					
					//STep 3 delete old dir
					deleteDir($source);
				}
				else{
					//include the dataBase file
					include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
					$newname = mysqli_real_escape_string($connect,$_POST["newname"]);
					if (isset($_GET["file"]))
						$hash = mysqli_real_escape_string($connect,$_GET["file"]);
					else
						$hash = mysqli_real_escape_string($connect,$_POST["file"]);
					if (fs_file_exists($newname,$_SESSION["currentdir"]) == false) 
						$insert = mysqli_query($connect,"Update Files Set Displayname='$newname' where Hash ='$hash'") or die("Error: 017 ".mysqli_error($connect));	
				}
				if (isset($_POST["api_key"]))
				{		
					echo "Command_Result:Done";
					exit;		
				}
				else{	
					if ($GLOBALS["config"]["Program_Debug"] != 1){
						header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]);
						exit;
					}	
				}
			}		
		}		
	}
	
?>
<?php
	if (isset($_GET["file"])){
		$suffix = "&file=".$_GET["file"];
		$file = getFileByHash($_GET["file"]);
	}
	else
	{
		$suffix = "&source=".$_GET["source"]."&old_root=".$_GET["old_root"];
		$file = $_GET["source"];
	}	
?>
<form method="POST" action="index.php?module=rename<?php echo $suffix;?>" align = "center">
<div class = 'contentWrapper'><tag><?php echo sprintf($GLOBALS["Program_Language"]["Rename"],$file);?> <r></tag><input name="newname">
<input type=submit name=submit value="<?php echo $GLOBALS["Program_Language"]["Rename_Button"];?>"></div>
</form>