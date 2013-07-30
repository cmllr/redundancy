<?php	
	function fs_isImage($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = get_Mime_Type($filename);
			if (strpos($mimetype,"image") !== false || $mimetype == "image/png" || $mimetype == "image/jpg" || $mimetype == "image/jpeg" || $mimetype == "image/bmp" )
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	function fs_isVideo($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = get_Mime_Type($filename);
			if (strpos($mimetype,"video") !== false ||  $mimetype == "video/mpeg" || $mimetype == "video/mp4" || $mimetype == "video/ogg" || $mimetype == "video/quicktime" || $mimetype == "video/webm" || $mimetype == "video/x-mastroska" || $mimetype == "video/x-ms-wmv" || $mimetype == "video/x-flv" || $mimetype == "video/x-ms-asf" )
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	function fs_isAudio($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = get_Mime_Type($filename);
			if (strpos($mimetype,"audio") !== false || $mimetype == "audio/basic" || $mimetype == "audio/L24" || $mimetype == "audio/mp4" || $mimetype == "audio/mpeg" || $mimetype == "audio/ogg" || $mimetype == "audio/vorbis" || $mimetype == "audio/vnd.rn-realaudio" || $mimetype == "audio/vnd.wave" || $mimetype == "audio/webm" || $mimetype == "audio/mpeg")
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	function fs_isText($filename)
	{	
		try{
			if (isset($_SESSION) == false)
				session_start();	
			$mimetype = get_Mime_Type($filename);
			if ($mimetype == "text/cmd" || $mimetype == "text/css" || $mimetype == "text/csv" || $mimetype == "text/html" || $mimetype == "text/plain" || $mimetype == "text/xml" || $mimetype == "text/x-asm")
				return true;
			return false;
		}
		catch (Exception $e){
			return false;
		}
	}
	function getUsedSpace($username)
	{	
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
		$userID = $username;
		$amount_in_Byte  = 0;
		$result = mysqli_query($connect,"Select ID, User from Users where User = '$username' or ID = '$username' LIMIT 1") or die("Error 022: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$userID = $row->ID;				
		}
		$result = mysqli_query($connect,"Select * from Files where UserID = '$userID'")  or die("Error 023: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysqli_close($connect);		
		return $amount_in_Byte ;
	}
	
	function fs_setUsedSpace($username)
	{			
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
		$userID = "";
		$amount_in_Byte  = 0;
		$result = mysqli_query($connect,"Select ID, User from Users where User = '$username' LIMIT 1") or die("Error: 022: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$userID = $row->ID;				
		}
		$result = mysqli_query($connect,"Select * from Files where UserID = $userID") or die("Error: 023: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysqli_close($connect);		
		//Store the space information in the session
		$_SESSION["space_used"] =  $amount_in_Byte;	
	}
	//Obsolete since 1.9.8
	function _getTopDir($directory)
	{
		if (isset($_SESSION) == false)
			session_start();
		$dirs = explode("/",$_SESSION["currentdir"]);
		if (count($dirs) -1 >= 0)		
			return $dirs[count($dirs) -1];
		else
			return "/";
	}
	function fs_get_Storage_Percentage()
	{
		if (isset($_SESSION) == false)
			session_start();
		$storage = $_SESSION["space"];		
		$storage = $storage * 1024 * 1024;
		$storage_used = getUsedSpace($_SESSION["user_name"]); 
		
		$measure = "B";
		if ($storage_used > 1024 && $storage_used < 1024 * 1024)
		{
			$measure = "KB";
			$storage_used = $storage_used /1024;
		}
		else if ($storage_used > 1024 * 1024 && $storage_used < 1024 * 1024 * 1024)
		{
			$measure = "MB";
			$storage_used = $storage_used /1024 / 1024;
		}
		else if ($storage_used > 1024 * 1024 * 1024 && $storage_used < 1024 * 1024 * 1024 * 1024)
		{
			$measure = "GB";
			$storage_used = $storage_used /1024 / 1024 / 1024;
		}
		else if ($storage_used > 1024 * 1024 * 1024 * 1024)
		{
			$measure = "TB";
			$storage_used = $storage_used /1024 / 1024 / 1024 / 1024;
		}
		if ($storage_used == 0)
			$storage_used = 0;
		return round($storage_used,2)." $measure ".$GLOBALS["Program_Language"]["of"]." ".fs_get_fitting_DisplayStyle($_SESSION['space']*1024*1024)." ".$GLOBALS["Program_Language"]["used"];
	}	 
	function fs_get_Percentage()
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
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
		
		$result = mysqli_query($connect,"Select ID from Share where Hash = '".mysqli_real_escape_string($connect,$file)."' limit 1") or die("Error: 024: ".mysqli_error($connect));
		if (mysqli_affected_rows($connect) > 0){
				return true;
		}		
		return false;
	}	
	function fs_getShareLink($file)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysqli_query($connect,"Select * from Share where Hash = '".mysqli_real_escape_string($connect,$file)."' limit 1") or die("Error: 024: ".mysqli_error($connect));
		$share_dir = str_replace("index.php","",$_SERVER["PHP_SELF"]);
		if ($row = mysqli_fetch_object($result)){
				if ($GLOBALS["config"]["Program_HTTPS_Redirect"] == 1)			
					$sharetext = "https://".$_SERVER["SERVER_NAME"].$share_dir."index.php?share=".$row->Extern_ID;
				else
					$sharetext = "http://".$_SERVER["SERVER_NAME"].$share_dir."index.php?share=".$row->Extern_ID;
				return $sharetext;
		}		
		return -1;
	}	
	function fs_get_fitting_DisplayStyle($value,$offset = 1)
	{
		$measure = "B";
	
		$value = $value * $offset;
		
		if ($value > 1024 && $value  < 1024 * 1024)
		{
			$measure = "KB";
			$value = $value /1024;
		}
		else if ($value > 1024 * 1024 && $value < 1024 * 1024 * 1024)
		{
			$measure = "MB";
			$value = $value /1024 / 1024;
		}
		else if ($value >= 1024 * 1024 * 1024 && $value < 1024 * 1024 * 1024 * 1024 )
		{
			$measure = "GB";
			$value = $value /1024 / 1024 / 1024;
		}
		else if ($value > 1024 * 1024 * 1024 * 1024 )
		{
			$measure = "TB";
			$value = $value /1024 / 1024 / 1024 / 1024;
		}
		return round($value,2) ." ". $measure;
	}	
	function getDirectorySize($value)
	{
		if (isset($_SESSION) == false)
			session_start();
		$dirSize = 0;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysqli_query($connect,"Select * from Files where UserID = '".$_SESSION["user_id"]."'") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
				if (startsWith($row->Directory,$value))
					$dirSize += $row->Size;
		}
		mysqli_close($connect);	
		return $dirSize;
	}
	function getFileSize($value,$dir)
	{
		if (isset($_SESSION) == false)
			session_start();
		$dirSize = 0;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysqli_query($connect,"Select * from Files where UserID = '".$_SESSION["user_id"]."' and Displayname = '$value' and Directory = '$dir'") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
				if (startsWith($row->Directory,$value))
					$dirSize += $row->Size;
		}
		mysqli_close($connect);	
		return $dirSize;
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
		if ($GLOBALS["config"]["Program_Mime_Use_DataBase"] == 0){
			$file = file_get_contents($GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$filename);
			$finfo = new finfo(FILEINFO_MIME_TYPE);		
			return $finfo->buffer($file);
		}
		else
		{
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
			$result = mysqli_query($connect,"Select MimeType from Files where UserID = '".$_SESSION["user_id"]."' and Filename = '$filename' or Hash = '$filename' limit 1") or die("Error 025: ".mysqli_error($connect));
			$filename_mime = "";
			while ($row = mysqli_fetch_object($result)) {
				$filename_mime = $row->MimeType;
			}					
			return $filename_mime;
		}		
	}
	function getDirectoryID($directory)
	{	
		if (isset($_SESSION) == false)
			session_start();
		$filename = -1;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);		
		$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$directory' and Filename = '$directory' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$filename = $row->ID;
		}		
		return $filename;
	}
	function fs_file_exists($file,$directory )
	{
		if (isset($_SESSION) == false)
			session_start();	
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$result = mysqli_query($connect,"Select * from Files where UserID = '".$owner_ID."' and (Displayname = '$file'  or Hash = '$file')and Directory = '$directory'") or die("Error 025: ".mysqli_error($connect));
		
		if (mysqli_affected_rows($connect) > 0)
			return true;
		else
			return false;
	}
	function getFileByHash($hash)
	{
		if (isset($_SESSION) == false)
			session_start();
		$filename = "";
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$result = mysqli_query($connect,"Select * from Files where UserID = '".$_SESSION["user_id"]."' and Hash = '$hash' limit 1") or die("Error 025: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$filename = $row->Displayname;
		}			
		return $filename;
	}
	//obsolete since 1.9.5
	function moveDir_old($source,$target,$old_root)
	{
		$uploadtime= date("D M j G:i:s T Y",time());
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);		
		$getfiles_select = mysqli_query($connect,"Select * from Files where Directory like '$old_root%' and UserID = '$user' ");	
		while ($row = mysqli_fetch_object($getfiles_select) ) {		
			if ($row->Filename != $target && (startsWith($row->Filename,$source) || startsWith($row->Directory,$source) )){			
				if ($row->Displayname == $row->Filename){							
						$displayname = str_replace("//","/",$target.str_replace($old_root,"/",$row->Displayname));;
						$directory = str_replace("//","/",$target.str_replace($old_root,"/",$row->Directory));	
						echo "<br>new dir:".$displayname;
						echo "<br>new root:".$directory;
						include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
						mysqli_query($connect,"Update Files SET Displayname ='$displayname', Filename ='$displayname',Directory='$directory',Directory_ID = ".getDirectoryID($directory)." where ID =".$row->ID) or die("Error: 016: ".mysqli_error($connect));	
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
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$select = "Select * from Files where Directory = '$old_root' and UserID = '	$user' ";
		$replace_count = 1;
		if ($old_root == "/")
			$new_root = $target;
		else
			$new_root = str_replace($old_root,$target,$dir,$replace_count);
		$res = mysqli_query($connect,$select);
		while ($row = mysqli_fetch_object($res)){
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
				mysqli_query($connect,"Update Files SET Displayname ='$displayname', Filename ='$displayname',Directory='$target',Directory_ID = ".getDirectoryID($target)." where ID =".$row->ID) or die("Error: 016: ".mysqli_error($connect));	
					
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
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		mysqli_query($connect,"Update Files Set Directory='$newdir',Directory_ID = ".getDirectoryID($newdir)." where ID =".$ID." and UserID = '$user'") or die("Error: 017 ".mysqli_error($connect));	
	}
	function moveContents($source,$target)
	{	
		$uploadtime= date("D M j G:i:s T Y",time());
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);		
		$getfiles_select = mysqli_query($connect,"Select * from Files where Directory like '$source' and UserID = '$user' ");	
		$new_id = getDirectoryID($target."/");
		while ($row = mysqli_fetch_object($getfiles_select) ) {		
				if ($row->Filename == $row->Displayname)
				{					
					echo "<br>found dir".$row->Filename;
					echo "<br>new dir:" .$target."/".$row->Filename_only."/";
					echo "<br>new root:".$target."/";								
					mysqli_query($connect,"Update Files set Filename ='".$target."/".$row->Filename_only."/"."', Displayname = '".$target."/".$row->Filename_only."/"."', Directory = '".$target."/"."',Directory_ID=".$new_id." where Hash = '".$row->Hash."'"); 	
					moveContents($row->Filename,$target."/".$row->Filename_only);
				}
				else
				{
					echo "<br>found file".$row->Filename;
					echo "<br>new filedir:" .$target."/";
					$file_id = getDirectoryID($target."/");
					mysqli_query($connect,"Update Files set Directory = '".$target."/"."',Directory_ID = ".$file_id." where Hash = '".$row->Hash."'"); 
				}
		}		
	}
	function createDir($currentdir,$directory)
	{
		//an easy possibility to avoid xss 
		if (strpos("<",$directory) === false){
			//include the dataBase file
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			//remember the user id, the new directory, the current directory, the directory without path timestamp hash and so on...
			$userid = mysqli_real_escape_string($connect,$_SESSION['user_id']);		
			$newdirectory =  $currentdir . $directory."/";			
			$uploaddirectory = $currentdir;
			$filenameonly = $directory;
			$timestamp = time();
			$uploadtime= date("D M j G:i:s T Y", $timestamp);
			$hash = md5($newdirectory.$uploadtime.$userid);	
			$client_ip = getIP();	
			$dir_id = getDirectoryID($uploaddirectory); 		
			if (fs_file_exists($directory,$uploaddirectory) == false)
			{			
				//create the new directory
				$insert = "INSERT INTO Files (Filename,Displayname,Filename_only,Hash,UserID,IP,Uploaded,Size,Directory,Directory_ID,Client,ReadOnly) VALUES ('$newdirectory','$newdirectory','$filenameonly','$hash','$userid','$client_ip','$uploadtime',0,'$uploaddirectory','$dir_id','".$_SERVER['HTTP_USER_AGENT']."',0)";			
				$inserquery = mysqli_query($connect,$insert) or die("Error: 004 ".mysqli_error($connect));						
			}			
			mysqli_close($connect);		
		}
		if ($GLOBALS["config"]["Program_Redirect_NewDir"] == 1){
			if ($GLOBALS["config"]["Program_Debug"] != 1)
				header("Location: ./index.php?module=list&dir=".$currentdir.$directory."/&result=1&from=createdir");
		}			
	}
	function createBin($currentdir,$directory)
	{
		//include the dataBase file
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			//remember the user id, the new directory, the current directory, the directory without path timestamp hash and so on...
			$userid = mysqli_real_escape_string($connect,$_SESSION['user_id']);		
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
				$inserquery = mysqli_query($connect,$insert) or die("Error: 004 ".mysqli_error($connect));						
			}			
			mysqli_close($connect);			
	}
	function fs_copyDir($dir,$target,$old_root)
	{	
		//Dir = /test/
		//old_root = /
		//target = /newdir/test/
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$owner_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);
		$select = "Select * from Files where Directory = '$old_root' and UserID = '$owner_id' ";
		$replace_count = 1;
		if ($old_root == "/")
			$new_root = $target;
		else
			$new_root = str_replace($old_root,$target,$dir,$replace_count);
		$res = mysqli_query($connect,$select);
		while ($row = mysqli_fetch_object($res)){
			$ID = $row->ID;
			$Filename = $row->Filename;
			$Displayname = $row->Displayname;
			$timestamp = time();
			$uploadtime= date("D M j G:i:s T Y", $timestamp);
			$Hash = md5($Displayname.$uploadtime);	
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
				mysqli_query($connect,$insertDir);
			
				fs_copyDir($row->Filename,$target.$row->Filename_only."/",$row->Displayname);
					
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
				fs_copyFile($row->Hash,$target);
			}
		}
	}
	function fs_copyFile($file,$dir)
	{
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		$uploadtime= date("D M j G:i:s T Y",time());
		$owner_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);
		$result = mysqli_query($connect,"Select * from Files  where Hash = '$file' and UserID = '$owner_id'") or die("Error: ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {
			$Filename =$row->Filename;
			$Displayname = $row->Displayname;
			$timestamp = time();
			$uploadtime= date("D M j G:i:s T Y", $timestamp);
			$Hash = md5($Filename.$uploadtime);	
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
			include $GLOBALS["Program_Dir"] ."Includes/DataBase.inc.php";
			mysqli_query($connect,"Select *  from `Files` where  where Filename = '$code.dat'");
			if (mysqli_affected_rows($connect) > 0)
			{
				$code = getRandomKey(50);
				$found = true;					
			}
		}while($found == true );	
		$hash_new = md5($code.".dat");	
		$newfilename = $code.".dat";	
		$uploaddir =$GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Storage_Dir"]."/";	
		$dir_id = getDirectoryID($dir);
		$insert = "Insert into Files (Filename, Displayname, Hash, UserID, IP, Uploaded, Size, Directory,Directory_ID ) Values ('$newfilename','$Displayname','$hash_new',$UserId,'$IP','$uploadtime',$Size,'$dir',$dir_id)";
		$insertquery = mysqli_query($connect,$insert);
		if ($insertquery == true)
			copy($uploaddir.$Filename,$uploaddir.$newfilename);	
	}
	//Delete directory function
	function deleteDir($dirname)
	{
		//Create a session if needed
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$dir = mysqli_real_escape_string($connect,$dirname);
		$owner_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);	
		$result = mysqli_query($connect,"Select * from Files  where Directory = '$dir' and UserID = '".$owner_id."'") or die("Error: 010 ".mysqli_error($connect));
	
		while ($row = mysqli_fetch_object($result)) {
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
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		//Delete the directory itself
		mysqli_query($connect,"delete from `Files` where   UserID = '".$_SESSION['user_id']."' and Filename = '$dir' and Displayname = '$dir'") or die("Error: 012 ".mysqli_error($connect));	
		//close connection
		mysqli_close($connect);
	}
	function deleteFile($filename,$directory,$hash)
	{
		//Create new database isntance
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		//Delete the file from the database
		$owner_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);	
		mysqli_query($connect,"delete from `Files` where  `Filename` = '".$filename."' and UserID = '".$owner_id."' and Directory = '$directory'")or die("Error: 011 ".mysqli_error($connect));		
		$result = mysqli_query($connect,"DELETE FROM `Share` WHERE `Hash` = '".$hash."' and UserID = '".$owner_id."' limit 1") or die("Error: 012 ".mysqli_error($connect));			
		//Delete it from the local server filesystem
		if ($result == true)
			unlink ( $GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$filename);	
	}
	function create_fs_snapshot()
	{
		if (!isset($_SESSION))
			session_start();
		echo "Starting snapshotting at ".date("D M j G:i:s T Y", time())." launched by ".$_SESSION["user_name"]."<br><hr>";
		$filecount = 0;
		$date = date("D M j G:i:s T Y", time());		
		$zipfile = new ZipArchive();	
		$fullPath = $GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/".$date.".zip";
		if ($zipfile->open($fullPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		
		if ($handle = opendir($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/")) {
			while (false !== ($file = readdir($handle))) {			
				if ($file != "." && $file != "..")
				{		
					echo date("D M j G:i:s T Y", time()).": Adding \"".$file."\" to snapshot<br>"; 
					$zipfile->addFile($GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$file,$file);	
					$filecount++;
				}
			}		
		}
		closedir($handle);
		echo "Finished snapshotting on ".$fullPath." [$filecount] files<br>";
		$zipfile->addFromString("database.sql",backup_tables());
		echo "Added database snapshot on ".$fullPath." [1] file";
		$zipfile->Close();
		
	}
	//Source:http://davidwalsh.name/backup-mysql-database-php
	function backup_tables($tables = '*')
	{
		$return = "";
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		//get all of the tables
		if($tables == '*')
		{
			$tables = array();
			$result = mysqli_query($connect,'SHOW TABLES');
			while($row = mysqli_fetch_row($result))
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		
		//cycle through
		foreach($tables as $table)
		{
			$result = mysqli_query($connect,'SELECT * FROM '.$table);
			$num_fields = mysqli_num_fields($result);
			
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysqli_fetch_row(mysqli_query($connect,'SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) 
			{
				while($row = mysqli_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = str_replace("\n","\\n",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j<($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		
		//save file
		
		return $return;
	}
	function createZipFile($dir,$zipfile)
	{
		//Create a session if needed
		if (isset($_SESSION) == false)
			session_start();
			
		//Create new database isntance
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$dir = mysqli_real_escape_string($connect,$dir);
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$select = "Select * from Files where UserID = '$user' and Directory = '$dir'";
		$result= mysqli_query($connect,$select);
		while ($row = mysqli_fetch_object($result)) {
			if ($row->Filename == $row->Displayname)
			{
				$zipfile->addEmptyDir((substr($row->Displayname,1)));
				createZipFile(($row->Displayname),$zipfile);
			}
			else
			{				
				$zipfile->addFile($GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$row->Filename,(substr( $dir.$row->Displayname, 1 )));
			}
		}		
	}
	function startZipCreation($dir)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		
		$zipfile = new ZipArchive();	
		$fullPath = $GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Temp_Dir"]."/".getRandomKey(50).".zip";
		if ($zipfile->open($fullPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		createZipFile($dir,$zipfile);
		$zipfile->addFromString("Timestamp.txt",date("D M j G:i:s T Y"));
		$zipfile->Close();
		$file = file_get_contents($fullPath);
		$finfo = new finfo(FILEINFO_MIME_TYPE);	
		if (file_exists($fullPath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $finfo->buffer($file)); 	
			if ($dir == "/")
				$filename = $GLOBALS["Program_Language"]["Files"];
			else
				$filename= str_replace("/","",$dir);
			header('Content-Disposition: attachment; filename='.$filename.'.zip');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($fullPath));
			ob_clean();
			flush();
			readfile($fullPath);
			unlink ($fullPath);				
			exit;
		}
	}
	function fs_enough_space($dir)
	{
		$value = false;
		$size = 0;		
		//TODO: sql
		//TODO: Bug
		
		if (getDirectorySize($dir) == 0 && endsWith($dir,"/") == false)
		{
			$size = getFileSize($dir,$_SESSION["currentdir"])/1024;			
		}
		else if (getDirectorySize($dir) != 0 && endsWith($dir,"/") != false)
		{
			$size = getDirectorySize($dir);	
		}
		echo "Size: ".$size;
		
		$complete = $size  + getUsedSpace($_SESSION["user_id"]);
		echo "Complete used space including new file: ".$complete;
		echo "Space available: ".$_SESSION["space"] * 1024 * 1024;
		if ($complete < $_SESSION["space"] * 1024 * 1024 )
			$value = true;
		else
			$value = false;
		return $value;
	}
	function fs_is_Dir($hash)
	{
		$folder = false;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$select = "Select * from Files where UserID = '$user' and Hash = '$hash'";
		$result= mysqli_query($connect,$select);
		while ($row = mysqli_fetch_object($result)) {
			if ($row->Displayname == $row->Filename)
				$folder = true;		
		}
		mysqli_close($connect);	
		return $folder;
	}
	function fs_get_imagepath($Displayname,$Filename,$MimeType,$Hash)
	{
		$imagepath = './Images/page.png';		
		if ($Displayname != $Filename){									
			if (fs_isImage($Filename) && $GLOBALS["config"]["Program_Display_Icons_if_needed"] == 1)
			{		
				$imagepath = "index.php?module=image&file=".$Hash;
			}	
			else if (fs_isImage($Filename) == false )
			{			
				if (file_exists("./Images/mimetypes/".str_replace("/","-",$MimeType).".png"))
						$imagepath = "./Images/mimetypes/".str_replace("/","-",$MimeType).".png";	
			}		
		}
		else
		{
			$imagepath = "./Images/mimetypes/folder.png";
			if (isShared($Hash))
				$imagepath = "./Images/mimetypes/folder-publicshare.png";
		}
		return $imagepath;
	}
	
?>