<?php
	//Common functions
	function getIP()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == false) {
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
			$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		if ($GLOBALS["config"]["Program_Privacy_Mask"] == 1)
		{
			$client_ip = substr($client_ip, 0, 4)."[...]";
		}
		return $client_ip;
	}
	function getIP2()
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
	function getRandomKey($length) {
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		return $randomString;
    }
	function getRandomPass($length) {
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#-+?/"), 0, $length);
		return $randomString;
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
?>