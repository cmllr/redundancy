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
	 * This file contains common function without determined context.
	 */
	/**
	 * getIP Gets the user ip with privacy settings	
	 * @return The user ip (anonymized)
	 */
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
	/**
	 * getIP2 Gets the user ip without privacy settings (used only for banning at injections or attacks)
	 * @return The full user ip
	 */
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
	/**
	 * endsWith checks if the string ends with the needle
	 * @param $haystack - The haystack
	 * @param $needle - The needle
	 * @return: If the haystack ends with the needle
	 */
	function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if ($length == 0) {
		return TRUE;
		}
		$start  = $length * -1;
		return (substr($haystack, $start) === $needle);
	}		
	/**
	 * getRandomKey: get random key
	 * @todo Improve of the function due security reasons
	 * @param $length - The length of the wanted string
	 * @return A random string with a determined length
	 */
	function getRandomKey($length) {
		
		$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$s_length = strlen($chars);
		$randomString = "";
		if ($length <= $s_length){
			$randomString = substr(str_shuffle($chars), 0, $length);
		
		}
		else
		{
			$iterations = $length/$s_length;
			$singlePart = $length/$iterations;
			for ($i = 0; $i < $iterations ;$i++)
			{
				$randomString = $randomString.substr(str_shuffle($chars), 0, $singlePart);
			}
		}
		return $randomString;
    }
	/**
	 * getRandomPass generates a pass (unsafe)
	 * @todo Improve of the function due security reasons
	 * @param $length - The length of the wanted string
	 * @return A random string with for usage as a pass
	 */
	function getRandomPass($length) {
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#-+?/"), 0, $length);
		return $randomString;
    }
	/**
	 * startsWith determines if a haystack ends with a needle
	 * @param $haystack - The haystack
	 * @param $needle - The needle
	 * @return If the haystack starts with the needle
	 */
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