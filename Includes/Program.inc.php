<?php	
	$_GLOBALS["Program_Version"] = "1.9.2-git-nightly";	
	$_GLOBALS["config_dir"] = "./";
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
		if ($passOK == true && $free == true)
			$ergebnis = mysql_query("Insert into Users (User, Email,Password,Salt,Registered,Role,Storage,Enabled,API_Key,Enable_API) Values('$user','$email','$safetypass','$salt','$registered','$role',$storage,$enabled,'$api_key',1)") or die("Error: 019 ".mysql_error());
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
				mail($pEmail, "Account activation required", $text, "From: $name <$name_email>");
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
	function recover($email)
	{
		//todo: recover
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
		$result = mysql_query("Select * from Files where UserID = '$userID'") or die("Error 023: ".mysql_error());
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
			$storage_used = 1;
		return round($storage_used,2)." $measure of ".$_SESSION['space']." Megabytes used";
	}
	function getPercentage()
	{
		if (isset($_SESSION) == false)
			session_start();
		$storage = $_SESSION["space"];		
		$storage = $storage * 1024 * 1024;
		$storage_used = getUsedSpace($_SESSION["user_name"]);
		if ($storage_used == 0)
			$storage_used = 1;
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
?>
