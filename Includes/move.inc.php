<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Remember a new date
	$uploadtime= date("D M j G:i:s T Y",time());
	$success = false;
	//Split between moving a file and moving a dir
	if ($_SESSION["role"] != 3){
		if ((isset($_GET["file"]) && isset($_GET["dir"])) || (isset($_POST["file"]) && isset($_POST["dir"]))){
			//Include database file
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
			//Remember values and process the (virtual) copy			
			if (isset($_GET["dir"]))
				$dir = mysql_real_escape_string($_GET["dir"]);
			else
				$dir = mysql_real_escape_string($_POST["dir"]);
			if (isset($_GET["file"]))		
				$file = mysql_real_escape_string($_GET["file"]);	
			else
				$file = mysql_real_escape_string($_POST["file"]);
			$sql = "UPDATE Files SET Directory='$dir',Uploaded='$uploadtime' WHERE Hash='$file'";			
			mysql_query($sql) or die("Error: 015 ".mysql_error());		
			mysql_close($connect);
			$success = true;
		}
		else if ((isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["target"]) && isset($_POST["old_root"])))				
		{
			//Include database file
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
			/*$source = mysql_real_escape_string($_GET["source"]); //directory to be moved
			$target = mysql_real_escape_string($_GET["target"]);	//target
			$old_root = mysql_real_escape_string($_GET["old_root"]); // old root dir*/
			if (isset($_GET["source"]))
				$source = mysql_real_escape_string($_GET["source"]); //directory to be moved
			else
				$source = mysql_real_escape_string($_POST["source"]); //directory to be moved
			if (isset($_GET["target"]))
				$target = mysql_real_escape_string($_GET["target"]);	//target
			else
				$target = mysql_real_escape_string($_POST["target"]);	//target
			if (isset($_GET["old_root"]))			
				$old_root = mysql_real_escape_string($_GET["old_root"]); // old root dir
			else
				$old_root = mysql_real_escape_string($_POST["old_root"]); // old root dir
			$uploadtime= date("D M j G:i:s T Y",time());
			$user = mysql_real_escape_string($_SESSION["user_id"]);		
			$getfiles_select = mysql_query("Select * from Files where Directory like '$old_root%' and UserID = '$user' ");	
			while ($row = mysql_fetch_object($getfiles_select) ) {		
				if ($row->Filename != $target && (startsWith($row->Filename,$source) || startsWith($row->Directory,$source) )){
					//TODO: Display a "status monitor" while copying
					if ($row->Displayname == $row->Filename){					
					//	echo "<br>found dir: ".str_replace("//","/",$row->Displayname);			
					//	echo "<br>new  displayname & filename: ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));					
					//	echo "<br>new  directory:  ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
						$displayname = str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));;
						$directory = str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
						include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
						mysql_query("Update Files SET Displayname ='$displayname', Filename ='$displayname',Directory='$directory' where ID =".$row->ID) or die("Error: 016: ".mysql_error());	
					}			
					else
					{
						//echo "<br>found file: ".$row->Directory.$row->Displayname;				
						//echo "<br>new  directory: ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));					
						$directory =str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));
						mysql_query("Update Files Set Directory='$directory' where ID =".$row->ID) or die("Error: 017 ".mysql_error());						
					}
				}			
			}		
			$success = true;
		}
	}
	if (isset($_POST["api_key"]))
	{
		echo "Command_Result:{$success}";
		exit;	
	}	
	//Redirect the user if needed
	
	if (!isset($_GET["noredir"])){
			header("Location: ./index.php?module=list&dir=/");
			exit;
	}	
?>