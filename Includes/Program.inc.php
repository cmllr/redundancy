<?php	
	$GLOBALS["Program_Version"] = "1.9.3.1-git-nightly";		
	$GLOBALS["config_dir"] = "./";
?>
<?php
	function login($pUser,$pPass)
	{		
		//start a new session
		if (isset($_SESSION) == false)
			session_start();
		//Include database file
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";			
		$user = mysql_real_escape_string($pUser);
		$pass = mysql_real_escape_string($pPass);		
		$ergebnis = mysql_query("Select ID, User, Email, Password, Salt,Storage,Role, Enabled from Users where User = '$user' or Email = '$user' limit 1") or die("Error: 018 ".mysql_error());
		while ($row = mysql_fetch_object($ergebnis)) {	
			$internalpassword = $row->Password;		
			if ($internalpassword == hash('sha512',$pass.$row->Salt) && $row->Enabled == 1)
			{			
				$_SESSION['user_id'] = $row->ID;
				$_SESSION['user_name'] = $row->User;
				$_SESSION['user_email'] = $row->Email;
				$_SESSION["user_logged_in"] = true;
				$_SESSION["currentdir"] = "/";	
				$_SESSION["currentdir_hashed"] = "6666cd76f96956469e7be39d750cc7d9";	
				$_SESSION["space"] = $row->Storage;	
				$_SESSION["space_used"] = 0;
				$_SESSION["role"] = $row->Role;
				mysql_close($connect);
				return true;		
			}			
		}
		mysql_close($connect);
		return false;
	}
	function registerUser($pUser,$pEmail,$pPass,$pPassRepeat)
	{
		//Is the password and the repeated the same?
		if ($pPass == $pPassRepeat)
		{
			$passOK = true;
		}
		else
		{
			$passOK = false;
		}
		//Is there already a user with this username or email?
		if (isExisting($pEmail,$pUser) == false)
		{
			$free = true;
		}
		else
		{
			$free = false;
		}
		//start a new session
		if (isset($_SESSION) == false)
			session_start();
		//Include database file
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";			
		$user = mysql_real_escape_string($pUser);
		$pass = mysql_real_escape_string($pPass);	
		$email = mysql_real_escape_string($pEmail);
		$salt = getRandomKey(200);
		$safetypass = hash('sha512',$pass.$salt);	
		$registered= date("D M j G:i:s T Y",time());
		$role = $_SESSION["config"]["User_Default_Role"];		
		$storage = $_SESSION["config"]["User_Contingent"];
		$api_key = hash('sha512',$Email.$salt.$user);	
		//Determine if the user should be activated automatically or not
		if ($_SESSION["config"]["User_Registration_AutoDisable"] == 1)
			$enabled = 0;		
				else 
			$enabled = 1;
		if ($passOK == true && $free == true){
			$ergebnis = mysql_query("Insert into Users (User, Email,Password,Salt,Registered,Role,Storage,Enabled,API_Key,Enable_API) Values('$user','$email','$safetypass','$salt','$registered','$role',$storage,$enabled,'$api_key',1)") or die("Error: 019 ".mysql_error());
		
		}
		else
			return false;
		//Send a activation mail when the account is not activated automatically	
		if ($ergebnis == true){
			if ($_SESSION["config"]["User_Registration_AutoDisable"] == 1  )
				sendMail($email,1,$user,"Redundancy",$_SESSION["config"]["User_Activation_Link"]."&email=".$email,"Redundancy");
			return true;
		}
		else
			return false;
	}
	function sendMail($pEmail,$pMessageID,$arg0,$arg1,$arg2,$arg3)
	{
		//Start a new session if needed
		if (isset($_SESSION) == false)
			session_start();
		//Include database file
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
		$result = mysql_query("Select * from Mails where  ID = '$pMessageID' limit 1") or die("Error 020 ". mysql_error());
		while ($row = mysql_fetch_object($result)) {			
			$text = sprintf ($row->Text,$arg0,$arg1,$arg2,$arg3);
			$name = $_SESSION["config"]["User_Activation_Link_Sender"];
			$name_email = $_SESSION["config"]["User_Activation_Link_Sender_Email"];
			//Only send the email if configured
			if ($name != "" && $name_email != "")
				mail($pEmail, "Redundancy", $text, "From: $name <$name_email>");
		}
		//close connection
		mysql_close($connect);
	}
	function isExisting($pEmail,$pUser)
	{
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$user = mysql_real_escape_string($pUser);
		$email = mysql_real_escape_string($pEmail);
		$ergebnis = mysql_query("Select * from Users where  User = '$user' or Email = '$email'") or die("Error: 021 ".mysql_error());
		if (mysql_affected_rows() > 0)
		{
			mysql_close($connect);	
			return true;
		}
		else		
		{
			mysql_close($connect);	
			return false;
		}		
	}
	function recover($pEmail)
	{
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$email = mysql_real_escape_string($pEmail);		
		if (isExisting($email,""))
		{		
			if ($_SESSION["config"]["User_Enable_Recover"] == 1){
				$pass = getRandomPass($_SESSION["config"]["User_Recover_Password_Length"]);
				
				$query = mysql_query("Select User, Email, Password, Salt from Users where Email ='$email' limit 1");
				$salt = getRandomKey(200);
				$name = "";			
				while ($row = mysql_fetch_object($query)) {					
						$name = $row->User;
				}
				$safetypass = hash('sha512',$pass.$salt);	
				$query = "Update Users Set Salt='$salt',Password='$safetypass'";
				mysql_query($query);
				sendMail($email,2,$name,"Redundancy",$pass,"Redundancy");				
				header("Location: ../index.php?module=recover&msg=success");
			}
			else
				header("Location: ../index.php?module=recover&msg=nosuccess");
		}		
		else
		{
			header("Location: ../index.php");
		}
	}
	function getIP()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == false) {
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
			$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		return $client_ip;
	}
	//String function
	function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if ($length == 0) {
		return TRUE;
		}
		$start  = $length * -1;
		return (substr($haystack, $start) === $needle);
	}	
	function isImage($filename)
	{	
		if (isset($_SESSION) == false)
			session_start();	
		$mimetype = get_Mime_Type($filename);// mime_content_type($filename);
		if ($mimetype == "image/png" || $mimetype == "image/jpg" || $mimetype == "image/jpeg" || $mimetype == "image/bmp")
			return true;
		return false;		
	}
	function getUsedSpace($username)
	{	
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";			
		$userID = $username;
		$amount_in_Byte  = 0;
		$result = mysql_query("Select ID, User from Users where User = '$username' or ID = '$username' LIMIT 1") or die("Error 022: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$userID = $row->ID;				
		}
		$result = mysql_query("Select * from Files where UserID = '$userID'")  or die("Error 023: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysql_close($connect);		
		return $amount_in_Byte ;
	}
	function setUsedSpace($username)
	{	
		//echo $username;
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";			
		$userID = "";
		$amount_in_Byte  = 0;
		$result = mysql_query("Select ID, User from Users where User = '$username' LIMIT 1") or die("Error: 022: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$userID = $row->ID;				
		}
		$result = mysql_query("Select * from Files where UserID = $userID") or die("Error: 023: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysql_close($connect);		
		//Store the space information in the session
		$_SESSION["space_used"] =  $amount_in_Byte;	
	}
	function getTopDir($directory)
	{
		if (isset($_SESSION) == false)
			session_start();
		$dirs = explode("/",$_SESSION["curre	ntdir"]);
		if (count($dirs) -1 >= 0)		
			return $dirs[count($dirs) -1];
		else
			return "/";
	}
	function getStoragePercentage()
	{
		if (isset($_SESSION) == false)
			session_start();
		$storage = $_SESSION["space"];		
		$storage = $storage * 1024 * 1024;
		$storage_used = getUsedSpace($_SESSION["user_name"]); 
		
		$measure = "B";
		if ($storage_used > 1024)
		{
			$measure = "KB";
			$storage_used = $storage_used /1024;
		}
		else if ($storage_used > 1024 * 1024)
		{
			$measure = "MB";
			$storage_used = $storage_used /1024 / 1024;
		}
		else if ($storage_used > 1024 * 1024 * 1024)
		{
			$measure = "GB";
			$storage_used = $storage_used /1024 / 1024 / 1024;
		}
		if ($storage_used == 0)
			$storage_used = 0;
		return round($storage_used,2)." $measure of ".$_SESSION['space']." MB ".$GLOBALS["Program_Language"]["used"];
	}
	function getPercentage()
	{
		if (isset($_SESSION) == false)
			session_start();
		$storage = $_SESSION["space"];		
		$storage = $storage * 1024 * 1024;
		$storage_used = getUsedSpace($_SESSION["user_name"]);
		if ($storage_used == 0)
		return "0%";
			else
		return round(100/($storage/$storage_used),2)."%";
	}
	function isShared($file)
	{
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";			
		
		$result = mysql_query("Select * from Share where Hash = '".mysql_real_escape_string($file)."' limit 1") or die("Error: 024: ".mysql_error());
		if (mysql_affected_rows() > 0){
				return true;
		}		
		return false;
	}
	function getRandomKey($length) {
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		return $randomString;
    }
	function getRandomPass($length) {
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#-+?/"), 0, $length);
		return $randomString;
    }
	function getFittingDisplayStlye($value)
	{
		$measure = "B";
		if ($value > 1024)
		{
			$measure = "KB";
			$value = $value /1024;
		}
		else if ($value > 1024 * 1024)
		{
			$measure = "MB";
			$value = $value /1024 / 1024;
		}
		else if ($value > 1024 * 1024 * 1024)
		{
			$measure = "GB";
			$value = $value /1024 / 1024 / 1024;
		}
		return round($value,2) ." ". $measure;
	}
	function getDirectorySize($value)
	{
		if (isset($_SESSION) == false)
			session_start();
		$dirSize = 0;
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysql_query("Select * from Files where UserID = '".$_SESSION["user_id"]."'") or die("Error 025: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
				if (startsWith($row->Directory,$value))
					$dirSize += $row->Size;
		}
		mysql_close($connect);	
		return $dirSize;
	}
	function startsWith($haystack,$needle)
	{
		$ref = "";
		for ($i = 0; $i < strlen($needle) && strlen($haystack) >= strlen($needle);$i++)
			$ref .= strtolower($haystack[$i]);
		if (strtolower($needle) == $ref)
			return true;
		else
			return false;		
	}
	
	function getDisplayName($string,$filename)
	{
		if ($string != $filename)
			return $string;
		else{
			$path_parts = explode('/',$string);
			return $path_parts[count($path_parts) -2];
		}
	}
	function get_Mime_Type($filename) {
		$file = file_get_contents($filename);
		$finfo = new finfo(FILEINFO_MIME_TYPE);		
		return $finfo->buffer($file);
	}
	function getDirectoryID($directory)
	{	
		if (isset($_SESSION) == false)
			session_start();
		$filename = -1;
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysql_query("Select * from Files where UserID = '".$_SESSION["user_id"]."' and Displayname = '$directory' and Filename = '$directory' limit 1") or die("Error 025: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$filename = $row->ID;
		}		
		return $filename;
	}
	function fs_file_exists($file,$directory )
	{
		if (isset($_SESSION) == false)
			session_start();	
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysql_query("Select * from Files where UserID = '".$_SESSION["user_id"]."' and Displayname = '$file'  and Directory = '$directory'") or die("Error 025: ".mysql_error());
		
		if (mysql_affected_rows() > 0)
			return true;
		else
			return false;
	}
	function getFileByHash($hash)
	{
		if (isset($_SESSION) == false)
			session_start();
		$filename = "";
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysql_query("Select * from Files where UserID = '".$_SESSION["user_id"]."' and Hash = '$hash' limit 1") or die("Error 025: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$filename = $row->Displayname;
		}			
		return $filename;
	}
	function xss_check()
	{
			$cfg_server = parse_ini_file($GLOBALS["config_dir"]."Redundancy.conf");
			if (count($cfg_server) != count($_SESSION["config"]))
				exit;
			if (count(array_diff($cfg_server,$_SESSION["config"])) != 0)
				exit;
			if ($_SESSION["Program_Dir"] !=  $cfg_server["Program_Path"])
				exit;
	}
	function moveDir_old($source,$target,$old_root)
	{
		$uploadtime= date("D M j G:i:s T Y",time());
		$user = mysql_real_escape_string($_SESSION["user_id"]);		
		$getfiles_select = mysql_query("Select * from Files where Directory like '$old_root%' and UserID = '$user' ");	
		while ($row = mysql_fetch_object($getfiles_select) ) {		
			if ($row->Filename != $target && (startsWith($row->Filename,$source) || startsWith($row->Directory,$source) )){			
				if ($row->Displayname == $row->Filename){							
						$displayname = str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));;
						$directory = str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
						echo "<br>new dir:".$displayname;
						echo "<br>new root:".$directory;
						include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
						mysql_query("Update Files SET Displayname ='$displayname', Filename ='$displayname',Directory='$directory',Directory_ID = ".getDirectoryID($directory)." where ID =".$row->ID) or die("Error: 016: ".mysql_error());	
				}			
				else
				{				
						echo "<br>new file:".$row->Filename;
					
						$directory =str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));			
							echo "<br>new root:".$directory;
						moveFile($row->ID,$directory);
				}
			}			
		}		
	}
	function moveDir($dir,$target,$old_root)
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
				$displayname = $target.$row->Filename_only."/";
				mysql_query("Update Files SET Displayname ='$displayname', Filename ='$displayname',Directory='$target',Directory_ID = ".getDirectoryID($target)." where ID =".$row->ID) or die("Error: 016: ".mysql_error());	
					
				moveDir($row->Filename,$target.$row->Filename_only."/",$row->Displayname);
					
			}
			else if (strpos($row->Directory,$dir) !== false && fs_file_exists($row->Displayname,$target) == false)
			{
				//File
				echo "<br>param root".$old_root;	
				echo "<br>target ".$target;	
				$newDir = $target;
				$newName = str_replace($old_root,$target,$row->Displayname,$temp = 1);			
				$insert = "Insert";
				echo "<br>Old entry name:".$row->Displayname;
				echo "<br>NEW entry name:".$target.$row->Displayname;
				echo "<br>Old directory".$old_root;
				echo "<br>New Directory".$target;	
				moveFile($row->ID,$target);
			}
		}	
	}
	function moveFile($ID,$newdir)
	{
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
		mysql_query("Update Files Set Directory='$newdir',Directory_ID = ".getDirectoryID($newdir)." where ID =".$ID) or die("Error: 017 ".mysql_error());	
	}
	function moveContents($source,$target)
	{	
		$uploadtime= date("D M j G:i:s T Y",time());
		$user = mysql_real_escape_string($_SESSION["user_id"]);		
		$getfiles_select = mysql_query("Select * from Files where Directory like '$source' and UserID = '$user' ");	
		$new_id = getDirectoryID($target."/");
		while ($row = mysql_fetch_object($getfiles_select) ) {		
				if ($row->Filename == $row->Displayname)
				{					
					echo "<br>found dir".$row->Filename;
					echo "<br>new dir:" .$target."/".$row->Filename_only."/";
					echo "<br>new root:".$target."/";								
					mysql_query("Update Files set Filename ='".$target."/".$row->Filename_only."/"."', Displayname = '".$target."/".$row->Filename_only."/"."', Directory = '".$target."/"."',Directory_ID=".$new_id." where Hash = '".$row->Hash."'"); 	
					moveContents($row->Filename,$target."/".$row->Filename_only);
				}
				else
				{
					echo "<br>found file".$row->Filename;
					echo "<br>new filedir:" .$target."/";
					$file_id = getDirectoryID($target."/");
					mysql_query("Update Files set Directory = '".$target."/"."',Directory_ID = ".$file_id." where Hash = '".$row->Hash."'"); 
				}
		}		
	}
	function createDir($currentdir,$directory)
	{
		//include the dataBase file
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
			//remember the user id, the new directory, the current directory, the directory without path timestamp hash and so on...
			$userid = mysql_real_escape_string($_SESSION['user_id']);		
			$newdirectory =  $currentdir . $directory."/";			
			$uploaddirectory = $currentdir;
			$filenameonly = $directory;
			$timestamp = time();
			$uploadtime= date("D M j G:i:s T Y", $timestamp);
			$hash = md5($newdirectory.$uploadtime);	
			$client_ip = getIP();	
			$dir_id = getDirectoryID($uploaddirectory); 		
			if (fs_file_exists($directory,$uploaddirectory) == false)
			{			
				//create the new directory
				$insert = "INSERT INTO Files (Filename,Displayname,Filename_only,Hash,UserID,IP,Uploaded,Size,Directory,Directory_ID,Client,ReadOnly) VALUES ('$newdirectory','$newdirectory','$filenameonly','$hash','$userid','$client_ip','$uploadtime',0,'$uploaddirectory','$dir_id','".$_SERVER['HTTP_USER_AGENT']."',0)";			
				$inserquery = mysql_query($insert) or die("Error: 004 ".mysql_error());						
			}			
			mysql_close($connect);		
			if ($_SESSION["config"]["Program_Redirect_NewDir"] == 1)
				header("Location: index.php?module=list&dir=".$newdirectory);
	}
	function createBin($currentdir,$directory)
	{
		//include the dataBase file
			include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
			//remember the user id, the new directory, the current directory, the directory without path timestamp hash and so on...
			$userid = mysql_real_escape_string($_SESSION['user_id']);		
			$newdirectory =  $currentdir . $directory."/";			
			$uploaddirectory = $currentdir;
			$filenameonly = $directory;
			$timestamp = time();
			$uploadtime= date("D M j G:i:s T Y", $timestamp);
			$hash = md5($newdirectory.$uploadtime);	
			$client_ip = getIP();	
			$dir_id = getDirectoryID($uploaddirectory); 		
			if (fs_file_exists($directory,$uploaddirectory) == false)
			{			
				//create the new directory
				$insert = "INSERT INTO Files (Filename,Displayname,Filename_only,Hash,UserID,IP,Uploaded,Size,Directory,Directory_ID,Client,ReadOnly) VALUES ('$newdirectory','$newdirectory','$filenameonly','$hash','$userid','$client_ip','$uploadtime',0,'$uploaddirectory','$dir_id','".$_SERVER['HTTP_USER_AGENT']."',1)";			
				$inserquery = mysql_query($insert) or die("Error: 004 ".mysql_error());						
			}			
			mysql_close($connect);			
	}
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
				$insertDir = "Insert into Files (Filename, Displayname,Filename_only, Hash, UserID, IP, Uploaded, Size, Directory,Directory_ID ) Values ('".$target.$row->Filename_only."/"."','".$target.$row->Filename_only."/"."','$filename_only','$Hash',$UserID,'$IP','$Uploaded',$Size,'$target',$dir_id)";
				mysql_query($insertDir);
			
				copyDir($row->Filename,$target.$row->Filename_only."/",$row->Displayname);
					
			}
			else if (strpos($row->Directory,$dir) !== false && fs_file_exists($row->Displayname,$target) == false)
			{
				//File
				echo "<br>param root".$old_root;	
				echo "<br>target ".$target;	
				$newDir = $target;
				$newName = str_replace($old_root,$target,$row->Displayname,$temp = 1);			
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
	//Delete directory function
	function deleteDir($dirname)
	{
		//Create a session if needed
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
		$dir = mysql_real_escape_string($dirname);		
		$result = mysql_query("Select * from Files  where Directory = '$dir' and UserID = '".$_SESSION['user_id']."'") or die("Error: 010 ".mysql_error());
	
		while ($row = mysql_fetch_object($result)) {
			//get the Filename of the file
			$localfilename = $row->Filename;
			$hash = $row->Hash;
			if ($row->ReadOnly == 1	)
				return;
			//If the filename is equal to the displayname, we have a dictonary
			if ($row->Filename == $row->Displayname)
			{
				//Process dir delete recursively
				deleteDir($row->Filename);			
			}
			else
			{
				deleteFile($localfilename,$dir,$hash);	
			}
		}
		//delete the directory entry itself (database)
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
		//Delete the directory itself
		mysql_query("delete from `Files` where   UserID = '".$_SESSION['user_id']."' and Filename = '$dir' and Displayname = '$dir'") or die("Error: 012 ".mysql_error());	
		//close connection
		mysql_close($connect);
	}
	function deleteFile($filename,$directory,$hash)
	{
		//Create new database isntance
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
		//Delete the file from the database
		mysql_query("delete from `Files` where  `Filename` = '".$filename."' and UserID = '".$_SESSION['user_id']."' and Directory = '$directory'")or die("Error: 011 ".mysql_error());		
		$result = mysql_query("DELETE FROM `Share` WHERE `Hash` = '".$hash."' and UserID = '".$_SESSION['user_id']."' limit 1") or die("Error: 012 ".mysql_error());			
		//Delete it from the local server filesystem
		if ($result == true)
			unlink ( $_SESSION["Program_Dir"]."Storage/".$localfilename);	
	}
?>
