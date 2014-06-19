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
	//require_once ("checkuri.inc.php");
	//start a session if needed	
	if (isset($_SESSION) == false)
			session_start();
	if (isset($_GET["file"]) && !isOwner($_GET["file"],$_SESSION["user_id"])){		
		header("Location: ./index.php?module=list&message=rename_fail");
		exit;
	}	
	$success = false;
	//only proceed if a post parameter is set
	if ($_SESSION["role"] != 3  )
	{		
		//only proceed if the user is logged in and we have a valid user_id
		if (isset($_SESSION['user_id']))
		{		
			if (isset($_POST["newname"]) && strpos($_POST["newname"],"<") === false && strpos($_POST["newname"],"/") === false){
				if ((isset($_GET["source"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["old_root"]))){
					
					include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
					if (isset($_GET["source"]))
						$source = mysqli_real_escape_string($connect,$_GET["source"]);
					else
						$source = mysqli_real_escape_string($connect,$_POST["source"]);
					$target = mysqli_real_escape_string($connect,$_POST["newname"]);
					if (isset($_GET["old_root"]))
						$old_root= mysqli_real_escape_string($connect,$_GET["old_root"]);
					else
						$old_root= mysqli_real_escape_string($connect,$_POST["old_root"]);
					$old_hash = getHashByFile($source,$old_root);
					$res = Guard::renameDirValidator($source,$old_root,$target);					
					//echo "Source: $source, old_root: $old_root, newname: $target currentdir: ".$_SESSION["currentdir"]." length: ".$GLOBALS["config"]["Program_FileSystem_Name_Max_Length"];
					
					if ($res== 0 && isFileExisting($target,$old_root) == false && strlen($target) <= $GLOBALS["config"]["Program_FileSystem_Name_Max_Length"]){
						if (!isLocalShared($old_hash)){
							createDir($old_root,$target,$old_hash);
							//$Dir_ID = getDirectoryID($source);
							//TODO: DIR rename
							//Step 1 create new dir (new name) - check
							$uploaddate = getUploadDateOfDir($source);
							moveContents($source,$_SESSION["currentdir"].$target);
							
							//Step 2 move contents -check					
							//STep 3 delete old dir
							deleteDir($source);
							
							setUploadDateOfDir($_SESSION["currentdir"].$target,$uploaddate);
							updateLastWriteOfDirectory(getDirectoryID($old_root));
							$success = true;
						}						
					}
					else
					{
						$success = false;
					}
				}
				else{					
					//include the dataBase file
					include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
					
					$newname = 	mysqli_real_escape_string($connect, $_POST["newname"]);		
					
					if (isset($_GET["file"]))
						$hash = mysqli_real_escape_string($connect,$_GET["file"]);
					else
						$hash = mysqli_real_escape_string($connect,$_POST["file"]);
						
					if (Guard::renameFileValidator($newname,$hash,$_SESSION["currentdir"]) == 0){
						if (isFileExisting($newname,$_SESSION["currentdir"]) == false && strlen($newname) <= $GLOBALS["config"]["Program_FileSystem_Name_Max_Length"]) {
							$lastWrite= date("Y-m-d H:i:s",time());
							$newLastWrite = "Update Files set lastWrite = '$lastWrite' where Hash ='$hash'";	
							mysqli_query($connect,$newLastWrite);
							$insert = mysqli_query($connect,"Update Files Set Displayname='$newname' where Hash ='$hash'") or die("Error: 017 ".mysqli_error($connect));	
							
							updateLastWriteOfDirectory(getRootDirectoryByEntryHash($hash));
							$success = true;
						}
						else	
							$success = false;
						}
					else{
						$success = false;
					}
				}
				if (isset($_POST["method"]))
				{		
					if ($success == false)
						echo "false";
					else
						echo "true";
					exit;	
				}
				else{	
					if ($GLOBALS["config"]["Program_Debug"] != 1){						
						if ($success == true){
							header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]."&message=rename_success");
						}
						else
						{
							header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]."&message=rename_fail");
						}
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
<h2><?php echo $GLOBALS["Program_Language"]["Rename_Button"];?></h2>
<div class="panel-body">
<form class="form-horizontal" method="POST" action="index.php?module=rename<?php echo $suffix;?>">	
	<div class="alert alert-info"><?php echo $GLOBALS["Program_Language"]["renameDescription"];?></div>
	<div class="form-group">		
		<label class="col-lg-3 control-label"><?php echo sprintf($GLOBALS["Program_Language"]["Rename"],$file);?></label>
		<div class="col-lg-9">
			<input type="text" class="form-control" id="searchquery" name="newname">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<input class="btn-block btn btn-default" type="submit" name="submit" value="<?php echo $GLOBALS["Program_Language"]["Rename_Button"];?>">		
		</div>
	</div>
</form>
</div>