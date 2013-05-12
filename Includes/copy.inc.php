<?php
	if (isset($_SESSION) == false)
			session_start();
	$success = false;
	if ($_SESSION["role"] != 3){
		if (isset($_GET["file"]) || isset($_POST["file"])){
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
			if (isset($_GET["dir"]))
				$dir = mysql_real_escape_string($_GET["dir"]);
			else
				$dir = mysql_real_escape_string($_POST["dir"]);
			if (isset($_GET["file"]))			
				$file = mysql_real_escape_string($_GET["file"]);	
			else
				$file = mysql_real_escape_string($_POST["file"]);
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
				if (isset($_POST["api_key"]) == false){		
					header("Location: ./index.php?module=list&dir=$dir");
					exit;
				}
				else
				{
					echo "Command_Result:{$success}";
					exit;
				}
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
			//echo $insert;
			$insertquery = mysql_query($insert);
			if ($insertquery == true)
				copy($uploaddir.$Filename,$uploaddir.$newfilename);
			mysql_close($connect);	
			$success = true;
		}	
		else if ((isset($_GET["source"]) && isset($_GET["target"]) && isset($_GET["old_root"])) || (isset($_POST["source"]) && isset($_POST["target"]) && isset($_POST["old_root"])))
		{
			//Include database file
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
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
				$Filename =$row->Filename;
				$Displayname = $row->Displayname;
				$Hash = $row->Hash;
				$UserId = $row->UserID;
				$IP = getIP();
				$Uploaded = $row->Uploaded;
				$Size = $row->Size;
				$Directory = $row->Directory;
				if ($row->Filename != $target && (startsWith($row->Filename,$source) || startsWith($row->Directory,$source) )){
					//TODO: Display a "status monitor" while copying
					if ($row->Displayname == $row->Filename){					
					//	echo "<br>found dir: ".str_replace("//","/",$row->Displayname);			
					//	echo "<br>new  displayname & filename: ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));					
					//	echo "<br>new  directory:  ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
						$displayname = str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));	
						$directory = str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
						$hash = md5($directory.$uploadtime);
						$filenameonly =  getDisplayName($row->Displayname,$row->Filename);
						//echo "<br>SQL: Insert into Files (Filename, Displayname, Filename_only, Hash, UserID, IP, Uploaded, Size, Directory) Values ('$displayname','$displayname',$filenameonly','$Hash',$user,'$IP','$uploadtime',$Size,'$directory')";
						include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
						mysql_query("Insert into Files (Filename, Displayname, Filename_only, Hash, UserID, IP, Uploaded, Size, Directory) Values ('$displayname','$displayname','$filenameonly','$Hash',$user,'$IP','$uploadtime',$Size,'$directory')") or die("Error: 031: ".mysql_error());					
					}			
					else
					{
						//echo "<br>found file: ".$row->Directory.$row->Displayname;				
						//echo "<br>new  directory: ".str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));					
						$directory =str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));
						copyFile($row->Hash,$directory);
						//echo "<br>SQL file: Insert into Files (Filename, Displayname, Filename_only, Hash, UserID, IP, Uploaded, Size, Directory) Values ('$displayname','$displayname','$filenameonly','$Hash',$user,'$IP','$uploadtime',$Size,'$directory')";										
						//mysql_query("Insert into Files (Filename, Displayname, Filename_only, Hash, UserID, IP, Uploaded, Size, Directory) Values ('$displayname','$displayname','$filenameonly','$Hash',$user,'$IP','$uploadtime',$Size,'$directory')") or die("Error: 031: ".mysql_error());										
						//copyFile($row->Hash,
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
	header("Location: ./index.php?module=list&dir=/");
	exit;
	function copyFile($file,$dir)
	{
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";		
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
		//echo $insert;
		$insertquery = mysql_query($insert);
		if ($insertquery == true)
			copy($uploaddir.$Filename,$uploaddir.$newfilename);
		//mysql_close($connect);	
	}
?>