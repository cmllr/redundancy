<?php
	if (isset($_SESSION) == false)
			session_start();
	if (isset($_GET["file"])){
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$dir = mysql_real_escape_string($_GET["dir"]);
		$file = mysql_real_escape_string($_GET["file"]);	
		$uploadtime= date("D M j G:i:s T Y",time());
		$result = mysql_query("Select * from Files  where Hash = '$file'") or die("Error: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$Filename =$row->Filename;
			$Displayname = $row->Displayname;
			$Hash = $row->Hash;
			$UserId = $row->UserID;
			$IP = getIP();
			$Uploaded = $row->Uploaded;
			$Size = $row->Size;
			$Directory = $row->Directory;
		}
		if(getUsedSpace("/") + $Size >= $_SESSION["space"] * 1024 * 1024)
		{
			header("Location: ./index.php?module=list&dir=$dir");
			exit;
		}
		$found =false;
		$code = getRandomKey(50);
		do{				
			include $_SESSION["Program_Dir"] ."Includes/DataBase.inc.php";
			mysql_query("Select *  from `Files` where  where Filename = '$code.dat'");
			if (mysql_affected_rows() > 0)
			{
				$code = getRandomKey(50);
				$found = true;					
			}
		}while($found == true );	
		$hash_new = md5($code.".dat");	
		$newfilename = $code.".dat";	
		$uploaddir =$_SESSION["Program_Dir"]."Storage/";
		
		$insert = "Insert into Files (Filename, Displayname, Hash, UserID, IP, Uploaded, Size, Directory) Values ('$newfilename','$Displayname','$hash_new',$UserId,'$IP','$uploadtime',$Size,'$dir')";
		echo $insert;
		$insertquery = mysql_query($insert);
		if ($insertquery == true)
			copy($uploaddir.$Filename,$uploaddir.$newfilename);
		mysql_close($connect);	
	}	
	else
	{
		exit;
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$source = mysql_real_escape_string($_GET["source"]); //directory to be moved
		$target = mysql_real_escape_string($_GET["target"]);	//target
		$old_root = mysql_real_escape_string($_GET["old_root"]); // old root dir
		$uploadtime= date("D M j G:i:s T Y",time());
		$user = $_SESSION["user_id"];		
		$getfiles_select = mysql_query("Select * from Files where Directory like '$old_root%' and UserID = '$user' ");	
		while ($row = mysql_fetch_object($getfiles_select) ) {				
			if ($row->Filename != $target && (startsWith($row->Filename,$source) || startsWith($row->Directory,$source) )){
				$Filename =$row->Filename;
				$Displayname = $row->Displayname;
				$Hash = $row->Hash;
				$UserId = $row->UserID;
				$IP = getIP();
				$Uploaded = $row->Uploaded;
				$Size = $row->Size;
				$Directory = $row->Directory;			
				if(getUsedSpace("/") + $Size >= $_SESSION["space"] * 1024 * 1024)
				{
					header("Location: ./index.php?module=list&dir=$dir");
					exit;
				}
				$found =false;
				$code = getRandomKey(50);
				do{				
					include $_SESSION["Program_Dir"] ."Includes/DataBase.inc.php";
					mysql_query("Select *  from `Files` where  where Filename = '$code.dat'");
					if (mysql_affected_rows() > 0)
					{
						$code = getRandomKey(50);
						$found = true;					
					}
				}while($found == true );	
				$hash_new = md5($code.".dat");	
				$newfilename = $code.".dat";	
				$uploaddir =$_SESSION["Program_Dir"]."Storage/";
				if ($row->Displayname == $row->Filename){					
					echo "<br>found dir: ".str_replace("//","/",$row->Displayname);			
					echo "<br>new  displayname & filename: ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));					
					echo "<br>new  directory:  ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
					$displayname = str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));;
					$directory = str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
					mysql_query("Update Files SET Displayname ='$displayname', Filename ='$displayname',Directory='$directory' where ID =".$row->ID) or die("Error: ".mysql_error());	
				}			
				else
				{
					echo "<br>found file: ".$row->Directory.$row->Displayname;				
					echo "<br>new  directory: ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));					
					$directory =str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));
					mysql_query("Update Files Set Directory='$directory' where ID =".$row->ID) or die("Error: ".mysql_error());						
				}
			}			
		}
	}
	header("Location: ./index.php?module=list&dir=$dir");
	exit;
?>