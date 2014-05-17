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
	 * System entry point
	 **/
	namespace Redundancy;	
		//TODO: Include _every_ needed file here	
		//**********************************************Kernel modules************************************
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.User.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Constants.inc.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.DBLayer.class.php";
		//**********************************************Data classes**************************************
		require_once __REDUNDANCY_ROOT__."Includes/Classes/User.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/Role.class.php";
		//**********************************************third party stuff*********************************
		require_once __REDUNDANCY_ROOT__.'Lib/Doctrine/Doctrine/Common/ClassLoader.php';	
		class Kernel{
			public $Name = "Redundancy";
			public $Version = "1.9.15-lenticularis-beta5-0";
			public $ReleaseDate = "-";	
			public $UserKernel;
			public $Configuration;
			public function __construct(){
				//Initialize all the needed Kernel modules
				$this->UserKernel = new Kernel\UserKernel();
				//TODO: Add further kernel modules
				$this->Configuration = parse_ini_file(__REDUNDANCY_ROOT__."Redundancy.conf");
				//Add the current object to the globals that other Kernel parts kann access others (over this object)
				$GLOBALS["Kernel"] = $this;
															
			}
			/**
			* Return the current server version
			* @return the version as String
			*/		
			public function GetVersion(){			
				return $this->Version;
			}
			/**
			* print out the json equivalent of an object
			* @return the json string (printed out)
			*/
			public function Output($result){
				echo json_encode($result);
			}		
		}
?>
