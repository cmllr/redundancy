<?php
	//Program values
	$_GLOBALS["Program_Name"] = "Redundancy<sup> nightly</sup>";
	$_GLOBALS["Program_Name_ALT"] = "Redundancy";
	$_GLOBALS["Program_Path"] = "/var/www/Redundancy2/";
	$_GLOBALS["Program_Storage"] = "/Redundancy2/Storage/";
	$_GLOBALS["Program_Version"] = "1.9.0.0-dev0";
	//privacy settings
	$_GLOBALS["Session_Lifespan"] = 100;
?>
<?php
	function login($pUser,$pPass)
	{		
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";			
		$user = mysql_real_escape_string($pUser);
		$pass = mysql_real_escape_string($pPass);	
		//sha512
		$ergebnis = mysql_query("Select ID, User, Email, Password, Salt,Storage from Users where User = '$user'") or die("Error: ".mysql_error());
		while ($row = mysql_fetch_object($ergebnis)) {
	
			$internalpassword = $row->Password;		
			if ($internalpassword == hash('sha512',$pass.$row->Salt))
			{
				//ini_set('session.use_only_cookies', 1);
				$_SESSION['user_id'] = $row->ID;
				$_SESSION['user_name'] = $row->User;
				$_SESSION['user_email'] = $row->Email;
				$_SESSION["user_logged_in"] = true;
				$_SESSION["currentdir"] = "/";	
				$_SESSION["currentdir_hashed"] = "6666cd76f96956469e7be39d750cc7d9";	
				$_SESSION["space"] = $row->Storage;					
				mysql_close($connect);
				return true;		
			}			
		}
		mysql_close($connect);
		return false;
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
		$types= array(".png",".jpeg",".jpg",".gif",".tiff",".svg");		
		
		for ($i = 0; $i< count($types);$i++)
		{		
			if (endsWith($filename,$types[$i]) == 1);
				return true;
		}
		return false;		
	}
	function getUsedSpace($username)
	{	
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";			
		$userID = "";
		$amount_in_Byte  = 0;
		$result = mysql_query("Select ID, User from Users where User = '$username' LIMIT 1") or die("Error: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$userID = $row->ID;				
		}
		$result = mysql_query("Select * from Files where UserID = $userID") or die("Error: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysql_close($connect);
		
		return $amount_in_Byte;
	}
	function setUsedSpace($username)
	{	
		if (isset($_SESSION) == false)
			session_start();
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";			
		$userID = "";
		$amount_in_Byte  = 0;
		$result = mysql_query("Select ID, User from Users where User = '$username' LIMIT 1") or die("Error: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$userID = $row->ID;				
		}
		$result = mysql_query("Select * from Files where UserID = $userID") or die("Error: ".mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$amount_in_Byte = $amount_in_Byte + $row->Size;			
		}
		mysql_close($connect);		
		$_SESSION["space"] =  $amount_in_Byte;
	}
	function getTopDir($directory)
	{
		if (isset($_SESSION) == false)
			session_start();
		$dirs = explode("/",$_SESSION["currentdir"]);
		if (count($dirs) -1 >= 0)		
			return $dirs[count($dirs) -1];
		else
			return "/";
	}
?>
