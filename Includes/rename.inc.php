<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
			
	//only proceed if a post parameter is set
	if ($_SESSION["role"] != 3  )
	{		
		//only proceed if the user is logged in and we have a valid user_id
		if (isset($_SESSION['user_id']))
		{		
			if (isset($_POST["newname"])){
				if (isset($_GET["source"]) && isset($_GET["old_root"])){
						include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
					$source = mysql_real_escape_string($_GET["source"]);
					$target = mysql_real_escape_string($_POST["newname"]);
					$old_root= mysql_real_escape_string($_GET["old_root"]);
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
					include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
					$newname = mysql_real_escape_string($_POST["newname"]);
					$hash = mysql_real_escape_string($_GET["file"]);
					if (fs_file_exists($newname,$_SESSION["currentdir"]) == false) 
						$insert = mysql_query("Update Files Set Displayname='$newname' where Hash ='$hash'") or die("Error: 017 ".mysql_error());	
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