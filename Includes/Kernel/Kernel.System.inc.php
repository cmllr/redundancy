<?php
	//System functions
	function xss_check()
	{
			$cfg_server = parse_ini_file($GLOBALS["config_dir"]."Redundancy.conf");
			if (count($cfg_server) != count($GLOBALS["config"]))
				exit;
			if (count(array_diff($cfg_server,$GLOBALS["config"])) != 0)
				exit;
			if ($GLOBALS["Program_Dir"] !=  $cfg_server["Program_Path"])
				exit;
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
	function banUser($client_ip,$client)
	{
		//Create new database isntance
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$client_ip = mysqli_real_escape_string($connect,$client_ip);
		$client = mysqli_real_escape_string($connect,$client);
		$date = date("m.d.y H:i:s",time());
		$query = "Insert into Banned (IP,Client,Date) Values('".$client_ip."','".$client."','$date')";
		mysqli_query($connect,$query);
	}
	
?>