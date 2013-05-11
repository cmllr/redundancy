<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Remember a new date
	$uploadtime= date("D M j G:i:s T Y",time());
	//Split between moving a file and moving a dir
	if (isset($_GET["file"]) && isset($_GET["dir"])){
		//Include database file
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		//Remember values and process the (virtual) copy
		$dir = mysql_real_escape_string($_GET["dir"]);
		$file = mysql_real_escape_string($_GET["file"]);		
		$sql = "UPDATE Files SET Directory='$dir',Uploaded='$uploadtime' WHERE Hash='$file'";			
		mysql_query($sql) or die("Error: 015 ".mysql_error());		
		mysql_close($connect);
	}
	else if (isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"]))
	{
		//Include database file
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$source = mysql_real_escape_string($_GET["source"]); //directory to be moved
		$target = mysql_real_escape_string($_GET["target"]);	//target
		$old_root = mysql_real_escape_string($_GET["old_root"]); // old root dir
		$uploadtime= date("D M j G:i:s T Y",time());
		$user = mysql_real_escape_string($_SESSION["user_id"]);		
		$getfiles_select = mysql_query("Select * from Files where Directory like '$old_root%' and UserID = '$user' ");	
		while ($row = mysql_fetch_object($getfiles_select) ) {		
			if ($row->Filename != $target && (startsWith($row->Filename,$source) || startsWith($row->Directory,$source) )){
				//TODO: Display a "status monitor" while copying
				if ($row->Displayname == $row->Filename){					
					echo "<br>found dir: ".str_replace("//","/",$row->Displayname);			
					echo "<br>new  displayname & filename: ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));					
					echo "<br>new  directory:  ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
					$displayname = str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));;
					$directory = str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
					mysql_query("Update Files SET Displayname ='$displayname', Filename ='$displayname',Directory='$directory' where ID =".$row->ID) or die("Error: 016: ".mysql_error());	
				}			
				else
				{
					echo "<br>found file: ".$row->Directory.$row->Displayname;				
					echo "<br>new  directory: ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));					
					$directory =str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));
					mysql_query("Update Files Set Directory='$directory' where ID =".$row->ID) or die("Error: 017 ".mysql_error());						
				}
			}			
		}			
	}
	//Redirect the user if needed
	if (!isset($_GET["noredir"])){
			header("Location: ./index.php?module=list&dir=$dir");
			exit;
	}	
?>