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
	if (isset($_GET["s"]) == false && isset($_POST["s"]) == false)
	{
		$agreed = false;
		$query =  $_SERVER["QUERY_STRING"];		
		
		if (isset($_GET["file"]))
		{
			echo "<div class = 'contentWrapper'><h2>".getFileByHash($_GET["file"])." " .$GLOBALS["Program_Language"]["Delete"]."?</h2>";
		}
		else if (isset($_GET["dir"]))
		{
			
			echo "<div class = 'contentWrapper'><h2>".$_GET["dir"]." " .$GLOBALS["Program_Language"]["Delete"]."?</h2>";
		}
		echo "<a href = 'index.php?".$query."&s=true'>".$GLOBALS["Program_Language"]["Delete_OK"]."</a></div>";
		exit;
	}
	else if (isset($_GET["s"]) || isset($_POST["s"]))
	{
		if (isset($_GET["s"]) && $_GET["s"] == "true")
			$agreed = true;
		if (isset($_POST["s"]) && $_POST["s"] == "true")
			$agreed = true;
	}
	if ($agreed = true && $_SESSION["role"] != 3){
		if (isset($_SESSION['user_name']) && (isset($_GET["file"]) || isset($_POST["file"]))) 
		{ 	 	 
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$localfilename = "";
			if (isset($_GET["file"]))
				$hash = mysqli_real_escape_string($connect,$_GET["file"]);
			else
				$hash = mysqli_real_escape_string($connect,$_POST["file"]);
			//step 1: get the Filename on the server file system
			$result = mysqli_query($connect,"Select * from Files  where Hash = '$hash' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: 007 ".mysqli_error($connect));
			$localfilename = "";
			$dir = "";
			while ($row = mysqli_fetch_object($result)) {
				$localfilename = $row->Filename;
				$dir = $row->Directory;
				if ($GLOBALS["config"]["Program_Debug"] == 1)
					echo $localfilename."<br>";
			}	
			mysqli_close($connect);	
			if ($localfilename != "" && $dir != "")
				deleteFile($localfilename,$dir,$hash);
			$success = true;
		}
		//Case 2: the user wants to delete a directory
		else if (isset($_SESSION["user_name"]) &&  ((isset($_GET["dir"]) ) || (isset($_POST["dir"]) && $_POST["dir"] != "/")))
		{
			//TODO: ADD setting responsible for this
			if (isset($_GET["dir"]))
					$todelete = $_GET["dir"];
				else 	
					$todelete = $_POST["dir"];	
			echo $todelete."<br>";
			deleteDir($todelete);		
			$success = true;		
		}
	}
	if (isset($_POST["ACK"]))
	{		
		if ($success == false)
			echo "false";
		else
			echo "true";
		exit;		
	}
	else{	
		if ($GLOBALS["config"]["Program_Debug"] != 1){
			header("Location: ./index.php?module=list&dir=".$_SESSION["currentdir"]);
			exit;
		}	
	}
?>