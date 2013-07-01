<?php
	//System functions
	function xss_check()
	{
		$found = false;		
		if (isset($_GET)){
			$found = check($_GET);
		}
		if (isset($_POST)){
			if ($found != true)
				$found = check($_POST);
		}		
		if (isset($_FILES)){
			if ($found != true)
				$found = check($_POST);
		}
		if (isset($_SESSION)){
			if ($found != true)
				$found = check($_POST);
		}	
		if (isset($_REQUEST)){
			if ($found != true)
				$found = check($_POST);
		}	
		if (isset($_COOKIE)){
			if ($found != true)
				$found = check($_POST);
		}	
		if ($found == true)
		banUser(getIP(),$_SERVER['HTTP_USER_AGENT'],"XSS");
		return $found;
	}
	function check($array)
	{
		$result = false;
		foreach($array as $key => $value) {			
			if (strpos($key,">") !== false || strpos($array[$key],">") !== false || strpos($key,"<") !== false || strpos($array[$key],"<") !== false)
			{				
				$result = true;
			}
		}
		return $result;
	}
	function listLanguages()
	{
		$lng_path = "./Language/";
		$languages = scandir($lng_path);
		echo "<select name = 'languages'>";
		foreach($languages as $entry) {
			if (is_file($lng_path.$entry) ){
				if ($GLOBALS["config"]["Program_Language"].".lng" != $entry)
					echo "<option value='$entry'>".str_replace(".lng","",$entry)."</option>";
				else
					echo "<option selected value='$entry'>".str_replace(".lng","",$entry)."</option>";
			}
				
		}
		echo "</select>";
	}
	function secureCheck()
	{
		if  (empty($_SERVER['HTTPS']))
		{
			return false;
		}
		else
		{
			return true;
		}		
	}
	function banUser($client_ip,$client,$reason)
	{
		//Create new database isntance
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$client_ip = mysqli_real_escape_string($connect,$client_ip);
		$client = mysqli_real_escape_string($connect,$client);
		$date = date("m.d.y H:i:s",time());
		$query = "Insert into Banned (IP,Client,Date,Reason) Values('".$client_ip."','".$client."','$date','$reason')";
		mysqli_query($connect,$query);
	}
	
?>