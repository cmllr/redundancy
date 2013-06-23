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
					$hash = mysqli_real_escape_string($connect,$_GET["file"]);
					if (fs_file_exists($newname,$_SESSION["currentdir"]) == false) 
						$insert = mysqli_query($connect,"Update Files Set Displayname='$newname' where Hash ='$hash'") or die("Error: 017 ".mysqli_error($connect));	
				}
				if (isset($_POST["api_key"]))
				{		
					echo "Command_Result:{$success}";
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