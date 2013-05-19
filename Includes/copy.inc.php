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
			copyFile($file,$dir);				
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
			copyDir($source,$target,$old_root);	
			mysql_close($connect);	
			$success = true;
		}
	}
	if (isset($_POST["api_key"]))
	{		
		echo "Command_Result:{$success}";
		exit;		
	}		
	//header("Location: ./index.php?module=list&dir=/");
	exit;
	function copyDir($dir,$target,$old_root)
	{	
		//Dir = /test/
		//old_root = /
		//target = /newdir/test/
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$select = "Select * from Files where Directory = '$old_root'  ";
		$replace_count = 1;
		if ($old_root == "/")
			$new_root = $target;
		else
			$new_root = str_replace($old_root,$target,$dir,$replace_count);
		$res = mysql_query($select);
		while ($row = mysql_fetch_object($res)){
			$ID = $row->ID;
			$Filename = $row->Filename;
			$Displayname = $row->Displayname;
			$Hash = $row->Hash;
			$UserID = $row->UserID;
			$IP = $row->IP;
			$Uploaded = $row->Uploaded;
			$Size = $row->Size;
			$Directory = $row->Directory;
			$Directory_ID = $row->Directory_ID;
			$Client = $row->Client;
			$filename_only = $row->Filename_only;
			if ($row->Filename == $row->Displayname && strpos($row->Filename,$dir) !== false && strpos($row->Filename,$dir) !== false && fs_file_exists($row->Displayname,$target) == false)
			{
				//Directory			
				echo "<br>param root".$old_root;	
				echo "<br>target ".$target;	
				$newDir = $target;
				$newName = str_replace($old_root,$target,$row->Displayname,$temp = 1);
				$newDir_ID = getDirectoryID($target);
				$insert = "Insert";
				$dir_id = getDirectoryID($target);
				echo "<br>Old entry name:".$row->Displayname;
				echo "<br>NEW entry name:".$target.$row->Filename_only."/";
				echo "<br>Old directory".$old_root;
				echo "<br>New Directory".$target;	
				$insert = "Insert into Files (Filename, Displayname,Filename_only, Hash, UserID, IP, Uploaded, Size, Directory,Directory_ID ) Values ('".$target.$row->Filename_only."/"."','".$target.$row->Filename_only."/"."','$filename_only','$Hash',$UserID,'$IP','$Uploaded',$Size,'$target',$dir_id)";
				echo $insert;
				copyDir($row->Filename,$target.$row->Filename_only."/",$row->Displayname);
				mysql_query($insert);
			}
			else if (strpos($row->Directory,$dir) !== false && fs_file_exists($row->Displayname,$target) == false)
			{
				//File
				echo "<br>param root".$old_root;	
				echo "<br>target ".$target;	
				$newDir = $target;
				$newName = str_replace($old_root,$target,$row->Displayname,$temp = 1);
				$newDir_ID = getDirectoryID($target);
				$insert = "Insert";
				echo "<br>Old entry name:".$row->Displayname;
				echo "<br>NEW entry name:".$target.$row->Displayname;
				echo "<br>Old directory".$old_root;
				echo "<br>New Directory".$target;	
				copyFile($row->Hash,$target);
			}
		}
	}
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
		$dir_id = getDirectoryID($dir);
		$insert = "Insert into Files (Filename, Displayname, Hash, UserID, IP, Uploaded, Size, Directory,Directory_ID ) Values ('$newfilename','$Displayname','$hash_new',$UserId,'$IP','$uploadtime',$Size,'$dir',$dir_id)";
		$insertquery = mysql_query($insert);
		if ($insertquery == true)
			copy($uploaddir.$Filename,$uploaddir.$newfilename);	
	}
?>