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
	 * This file is used for deleting files or folders
	 */
	//Include uri check
	//require_once ("checkuri.inc.php");
	//Create a session if needed

	if (isset($_SESSION) == false)
		 session_start();
	//Case 1: the user wants to delete a file
	//echo var_dump($_SERVER);
	$success = false;
	if (isset($_GET["file"])){
		if (!isOwner($_GET["file"],$_SESSION["user_id"])){		
			header("Location: ./index.php?module=list&message=file_delete_fail");
			exit;
		}
	}
	else if (isset($_GET["dir"])){
		if (!isDirOwner($_GET["dir"],$_SESSION["user_id"])){		
			header("Location: ./index.php?module=list&message=file_delete_fail");
			exit;
		}
	}
	
	if (isset($_GET["s"]) == false && isset($_POST["s"]) == false)
	{
		$agreed = false;
		$query =  $_SERVER["QUERY_STRING"];		
		
		?>
		<div class = 'contentWrapper'>
		<?php
			if (isset($_GET["file"]))
			{
				?>				
					<h2>
						<?php echo getFileByHash($_GET["file"])." " .$GLOBALS["Program_Language"]["Delete"];?>?
					</h2>
				<?php
			}
			else if (isset($_GET["dir"]))
			{
				?>				
					<h2>
						<?php echo $_GET["dir"]." " .$GLOBALS["Program_Language"]["Delete"];?>?
					</h2>
				<?php
			}
		?>
			<a href = 'index.php?<?php echo $query;?>&s=true'>
				<?php echo $GLOBALS["Program_Language"]["Delete_OK"];?>
			</a>
		</div>			
		<?php		
		exit;
	}
	else if (isset($_GET["s"]) || isset($_POST["s"]))
	{
		if (isset($_GET["s"]) && $_GET["s"] == "true")
			$agreed = true;
		if (isset($_POST["s"]) && $_POST["s"] == "true")
			$agreed = true;
	}
	if ($agreed = true && $_SESSION["role"] != 3 && isGuest() == false){
		$success = false;
		if (isset($_SESSION['user_name']) && (isset($_GET["file"]) || isset($_POST["file"]))) 
		{ 	 	 
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$localfilename = "";
			if (isset($_GET["file"]))
				$hash = mysqli_real_escape_string($connect,$_GET["file"]);
			else
				$hash = mysqli_real_escape_string($connect,$_POST["file"]);				
			$userid = mysqli_real_escape_string($connect,$_SESSION['user_id']);		
			if (!isLocalShared($hash)){
				$success = getFileByHashAndDelete($hash,$userid);
			}			
		}
		//Case 2: the user wants to delete a directory
		else if (isset($_SESSION["user_name"]) &&  ((isset($_GET["dir"]) ) || isset($_POST["dir"]) ))
		{			
			if (isset($_GET["dir"]))
					$todelete = $_GET["dir"];
				else 	
					$todelete = $_POST["dir"];	
			if ($GLOBALS["config"]["Program_Debug"] == 1)
				echo $todelete."<br>";
			if ($_SESSION['currentdir'] == $todelete)
				$_SESSION["currentdir"] = getRootDirectory($_SESSION['currentdir'],$_SESSION["user_id"]);
			if (!isLocalShared($todelete)){
				$success= deleteDir($todelete);	
			}
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
			$dir = $_SESSION["currentdir"];			
			if ($success == true)
				header("Location: ./index.php?module=list&dir=".$dir."&message=file_delete_success");
			else
				header("Location: ./index.php?module=list&dir=".$dir."&message=file_delete_fail");
			exit;
		}	
	}
?>