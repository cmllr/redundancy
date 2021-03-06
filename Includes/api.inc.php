<?php 	
	/**
         * the api file is a central point which grabs the queries and executes them in the wanted kernel parts
	 * @file
	 * @author  squarerootfury <me@0fury.de>	 
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
	 * Central api layer to send requests to.
	 */	
	namespace Redundancy;	

	//Damn Windows!
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		$path = str_replace("Includes\api.inc.php","", __file__);
	else
		$path = str_replace("Includes/api.inc.php","", __file__);
	/**
	* The systems root dir, ending with "/"
	*/		
	define("__REDUNDANCY_ROOT__",$path);
	/**
	* A flag determining if the system is in debug mode (for later purposes)
	*/
	define("__REDUNDANCY_DEBUG__",true);
	/**
	* A flag determining if the system is in a testing mode
	*/	
	include __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Program.class.php";	
	/**
	* blalbla
	*/
	$Redundancy = new \Redundancy\Kernel\Kernel();	
	if (__REDUNDANCY_DEBUG__ == true){		
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
	}	
	if (isset($_POST["method"])){	
		$method = $_POST["method"];

		if (isset($_POST["args"])){
			$params = is_array($_POST["args"]) ? $_POST["args"] : json_decode($_POST["args"]);				
		}
		else
			$params = array();
		//If the regular parsing fails, try to decode the data first (for example if files are send)	
		//Wenn maybe same solution of line 55
		if (is_null($params))
			$params = json_decode(urldecode($_POST["args"]));			
		$module = $_POST["module"];
		
		if ($Redundancy->SystemKernel->IsAffectedByXSS($params)){			
			$ip = $Redundancy->UserKernel->GetIP();
			error_log("Redundancy [Warning]: Propably XSS attack from $ip");
			//The user does not get banned, but the request ist stopped here!
			//die($Redundancy->Output(\Redundancy\Classes\Errors::XSSAttack));	
		}
		//Clean up the parameters		
		$cleanedArgs = array();			
		if (!empty($params)){				
			foreach ($params as $key => $value) {
				$cleanedArgs[] = htmlspecialchars(strip_tags($value),ENT_NOQUOTES);
			}
			$params = $cleanedArgs;
		}			
		if ($Redundancy->SystemKernel->IsMyIPBanned() && $method != "GetIP")
		{
			die ($Redundancy->Output(\Redundancy\Classes\Errors::Banned));
		}
		if (!isset($module))
			die("Fatal Error :( ".\Redundancy\Classes\Errors::ModuleMissing);		
		//RAW Date formatter set
		if (isset($_POST["rawDate"])){
			$Redundancy->InterfaceKernel->SetRawDateFormatter();
		}
		//TODO: A more professional solution			
		switch($module){
			case "Kernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy,$method), $params)); 
			break;
			case "Kernel.UserKernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy->UserKernel,$method), $params)); 
			break;
			case "Kernel.InterfaceKernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy->InterfaceKernel,$method), $params)); 
			break;
			case "Kernel.FileSystemKernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy->FileSystemKernel,$method), $params));
			break;
			case "Kernel.SharingKernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy->SharingKernel,$method), $params));
			break;
			case "Kernel.SystemKernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy->SystemKernel,$method), $params));
			break;
			case "Kernel.UpdateKernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy->UpdateKernel,$method), $params));
			break;
		}	
	}		
?>
