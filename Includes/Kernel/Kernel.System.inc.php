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
	/**
	 * create an image by a full path
	 * @param $imagepath the path of the image
	 */
	function displayImage($imagepath)
	{			
		//Display image if existing
		//supported are: jpeg,jpg,bmp,png (atm)
		if (file_exists($_SESSION["current_file"])){
			header('Content-Type: ' .mime_content_type($_SESSION["current_file"]));		
			$mimetype = mime_content_type($_SESSION["current_file"]);		
			if ($mimetype == "image/jpeg"){
				$im = imagecreatefromjpeg($_SESSION["current_file"]);	
				if (isset($_GET["t"]) && $_GET["t"] == 1)
				{
					list($width, $height) = getimagesize($_SESSION["current_file"]);
					$newimage = imagecreatetruecolor(32,32);
					imagecopyresampled($newimage,$im,0,0,0,0,32,32,$width,$height);
					imagejpeg($newimage);
					imagedestroy($newimage);
					imagedestroy($im);
				}
				else
				{
					imagejpeg($im);					
					imagedestroy($im);	
				}				
			}
			if ($mimetype == "image/bmp"){					
				$im = imagecreatefromwbmp($_SESSION["current_file"]);
				if (isset($_GET["t"]) && $_GET["t"] == 1)
				{
					list($width, $height) = getimagesize($_SESSION["current_file"]);
					$newimage = imagecreatetruecolor(32,32);
					imagecopyresampled($newimage,$im,0,0,0,0,32,32,$width,$height);
					imagewbmp($newimage);
					imagedestroy($newimage);
					imagedestroy($im);
				}
				else
				{
					imagewbmp($im);
					imagedestroy($im);		
				}				
			}
			if ($mimetype == "image/png"){		
				$im = imagecreatefrompng($_SESSION["current_file"]);
				imagesavealpha ($im,true);
				if (isset($_GET["t"]) && $_GET["t"] == 1)
				{
					list($width, $height) = getimagesize($_SESSION["current_file"]);							
					$newimage = imagecreatetruecolor(32,32);
					imagealphablending($newimage, false);
					imagesavealpha ($newimage,true);					
					imagecopyresampled($newimage,$im,0,0,0,0,32,32,$width,$height);		
				
					imagepng($newimage);
					
					imagedestroy($newimage);
					imagedestroy($im);
				}
				else
				{
					imagepng($im);
					imagedestroy($im);		
				}			 		
			}			
		}		
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