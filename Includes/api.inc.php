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
	if (isset($_GET["method"])){	
		$method = $_GET["method"];		
		$params = json_decode($_POST["args"]);	
		$module = $_GET["module"];
		if (!isset($module))
			die("Fatal Error :( ".\Redundancy\Classes\Errors::ModuleMissing);		
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
		}	
	}		
?>
