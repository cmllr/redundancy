<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * Redundancy's system functions (security functions also) are located here
	 */
	/**
	 * check if xss is given
	 * @return if something is wrong with $_GET/$_POST/$_SESSION/$_REQUEST/$_COOKIE
	 */
	function isXSS()
	{
		$found = false;		
		if (isset($_GET)){
			$found = checkArray($_GET);
		}
		if (isset($_POST)){
			if ($found != true)
				$found = checkArray($_POST);
		}		
		if (isset($_FILES)){
			if ($found != true)
				$found = checkArray($_POST);
		}
		if (isset($_SESSION)){
			if ($found != true)
				$found = checkArray($_POST);
		}	
		if (isset($_REQUEST)){
			if ($found != true)
				$found = checkArray($_POST);
		}	
		if (isset($_COOKIE)){
			if ($found != true)
				$found = checkArray($_POST);
		}	
		if ($found == true){
			banUser(getIP(),$_SERVER['HTTP_USER_AGENT'],"XSS");
			log_event("Kernel.System","xss_check","XSS attack detected");
		}
		return $found;
	}
	/**
	 * check an array if any suspicios values are given (XSS protection)
	 * @param $array the array to be checked
	 * @return if something is wrong with the array
	 */
	function checkArray($array)
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
	/**
	 * listLanguages list langauges as a combobox	 
	 */
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
	/**
	 * secureCheck determine if https is used
	 */
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
	/**
	 * banUser ban an user
	 * @param $client_ip the ip of the user
	 * @param $client the user agent
	 * @param $reason the reason
	 */
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
	/**
	 * check if a user is banned
	 */
	function isBanned()
	{
		//Create new database isntance
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$client_ip = getIP();
		$query = "Select ID from Banned where IP = '$client_ip'";
		$ergebnis = mysqli_query($connect,$query);
		if (mysqli_affected_rows($connect) > 0){
			mysqli_close($connect);
			return true;
		}		
		else{
			mysqli_close($connect);
			return false;
		}
	}	
	/**
	 * get the link for activation
	 * @returns the activation link
	 */
	function getActivationLink(){
		$dir = str_replace("index.php","",$_SERVER["PHP_SELF"]);
		if ($GLOBALS["config"]["Program_HTTPS_Redirect"] == 1)			
			$link = "https://".$_SERVER["SERVER_NAME"].$dir."index.php?module=activate";
		else
			$link = "http://".$_SERVER["SERVER_NAME"].$dir."index.php?module=activate";
		return $link;
	}
	
	function setExceptionHandler(){
		set_exception_handler('exception_handler');
		setErrorHandler();
	}
	function setErrorHandler(){
		set_error_handler('error_handler');
	}
	function error_handler($errno, $errstr, $errfile, $errline){
		if ($GLOBALS["config"]["Program_Enable_Logging"] == 1){		
			log_event("error","$errno: \"$errstr\" in \"$errfile\" on $errline");
		}	
	}
	function exception_handler($exception) {
		if ($GLOBALS["config"]["Program_Enable_Logging"] == 1){		
			log_event("exception",$exception->getFile()." : Error on line \"".$exception->getLine()."\" Message: \"".$exception->getMessage()."\"");
		}		
	}		
?>