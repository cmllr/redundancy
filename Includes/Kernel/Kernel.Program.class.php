<?php
	/**
	 * this file contains the program entry point
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
	namespace Redundancy\Kernel;	
		//TODO: Include _every_ needed file here	
		//**********************************************Kernel modules************************************
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.System.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.User.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Interface.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Constants.inc.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.DBLayer.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.FileSystem.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Sharing.class.php";
		//**********************************************Data classes**************************************
		require_once __REDUNDANCY_ROOT__."Includes/Classes/User.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/Role.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/FileSystemItem.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/Folder.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/File.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/FileSystemAnalysis.class.php";
		//**********************************************third party stuff*********************************
		require_once __REDUNDANCY_ROOT__.'Lib/Doctrine/Doctrine/Common/ClassLoader.php';	
		/**
		 * This class boostraps the program itself
		 * @license
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
		 * @author  squarerootfury <me@0fury.de>
		 **/
		class Kernel{
			/**
			* The programs version
			* Structurized in {1.9.X-codenameorbranch-state-update}
			*/
			public $Version = "1.9.15-lenticularis-beta5-0";
			/**
			* The programs release date
			*/
			public $ReleaseDate = "-";
			/**
			* The kernel part object which contains the user functions.
			*/	
			public $UserKernel;
			/**
			* The kernel part object which contains the user functions.
			*/
			public $SystemKernel;
			/**
			* The Kernel part object which contains the functions to manage interface data.
			*/
			public $InterfaceKernel;
			/**
			* The Kernel part to handle files
			*/
			public $FileSystemKernel;
			/**
			* The kernel part which handles the filesystem shares
			*/
			public $SharingKernel;
			/**
			* The systems configuration. Parsed from __REDUNDANCY_ROOT__Redundancy.conf
			*/
			public $Configuration;
			/**
			* The class does not have any parameters to deliver with the base constructor.
			*/
			public function __construct(){
				//Initialize all the needed Kernel modules
				$this->UserKernel = new \Redundancy\Kernel\UserKernel();
				//TODO: Add further kernel modules
				$this->Configuration = parse_ini_file(__REDUNDANCY_ROOT__."Redundancy.conf");
				//Add the current object to the globals that other Kernel parts kann access others (over this object)
				$GLOBALS["Kernel"] = $this;
				$this->InterfaceKernel = new \Redundancy\Kernel\InterfaceKernel(-1);							
				$this->SystemKernel =  new \Redundancy\Kernel\SystemKernel();	
				$this->FileSystemKernel = new \Redundancy\Kernel\FileSystemKernel();
				$this->SharingKernel = new \Redundancy\Kernel\SharingKernel();
			}
			/**
			* Return the current server version
			* @return string the version 
			*/		
			public function GetVersion(){			
				return $this->Version;
			}
			/**
			* print out the json equivalent of an object
			* @param $result the object to print
			* @return string the json string (will be printed out)
			*/
			public function Output($result){
				$this->SetHTTPHeader($result);
				echo json_encode($result);
			}
			/**
			* Sets the HTTP status code
			* @param int $result the return code to represent. If $value is an errorcode (int), the Statuscode 503 Status Unavailable will be set. If the return value is not an errorcode, no additional status code will be set and 200 OK will be returned
			* @return int the statuscode or -1
			*/	
			private function SetHTTPHeader($result){
				if (is_int($result))
					return http_response_code("503");
				return -1;
			}	
		}
?>
