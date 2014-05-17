<?php
	/**
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
	$path = str_replace("Includes/api.inc.php","", __file__);		
	define("__REDUNDANCY_ROOT__",$path);
	define("__REDUNDANCY_DEBUG__",true);		
	include __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Program.class.php";	
	$Redundancy = new Redundancy\Kernel();	
	if (isset($_GET["method"])){
		$method = $_GET["method"];		
		$params = json_decode($_POST["args"]);	
		$module = $_GET["module"];
		if (!isset($module))
			die("Fatal Error :( ".Errors::ModuleMissing);		
		//TODO: A more professional solution
		switch($module){
			case "Kernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy,$method), $params)); 
			break;
			case "Kernel.UserKernel":
			$Redundancy->Output(call_user_func_array(array($Redundancy->UserKernel,$method), $params)); 
			break;
		}
	}
	else{
		die("Fatal Error :( ".Redundancy\Kernel\Errors::MethodMissing);	
	}	
?>
