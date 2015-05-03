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
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.System.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.User.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Interface.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Constants.inc.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.PDO.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.FileSystem.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Sharing.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Updater.class.php";
		//require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Backup.class.php";
		//**********************************************Data classes**************************************
		require_once __REDUNDANCY_ROOT__."Includes/Classes/User.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/Role.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/FileSystemItem.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/Folder.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/File.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/FileSystemAnalysis.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/Share.class.php";
		require_once __REDUNDANCY_ROOT__."Includes/Classes/Setting.class.php";
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
			* Structurized in {1.9.X-codenameorbranch-state}
			*/
			public $Version = "1.10.0-Lenticularis-rc3-0";
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
			* The kernel part which handles the update process
			*/
			public $UpdateKernel;
			/**
			* The kernel part to handle the backup process
			*/
			public $BackupKernel;
			/**
			* The class does not have any parameters to deliver with the base constructor.
			*/
			public function __construct(){
				//Initialize all the needed Kernel modules
				$this->UserKernel = new \Redundancy\Kernel\UserKernel();
				//TODO: Add further kernel modules
				//$this->Configuration = parse_ini_file(__REDUNDANCY_ROOT__."Redundancy.conf");
				//Add the current object to the globals that other Kernel parts kann access others (over this object)
				$GLOBALS["Kernel"] = $this;
				$this->InterfaceKernel = new \Redundancy\Kernel\InterfaceKernel(-1);							
				$this->SystemKernel =  new \Redundancy\Kernel\SystemKernel();	
				$this->FileSystemKernel = new \Redundancy\Kernel\FileSystemKernel();
				$this->SharingKernel = new \Redundancy\Kernel\SharingKernel();
				$this->UpdateKernel = new \Redundancy\Kernel\UpdateKernel();
				//$this->BackupKernel = new \Redundancy\Kernel\BackupKernel();
			}
			/**
			* Return the current server version
			* @return string the version 
			*/		
			public function GetVersion(){			
				return $this->Version;
			}
			/**
			* Return the current server version - but shortened (only numeric parts)
			* @return string the version X.Y.Z
			*/
			public function GetShortenedVersion(){
				$pattern = "/(?<major>\d+).(?<minor>\d+).(?<patch>\d+)+-(?<branch>[^-]+)-(?<stage>[^-]+)-(?<update>\d+)/";
				$matches;
				preg_match($pattern,$this->Version,$matches);
				return sprintf("%s.%s.%s.%s",$matches["major"],$matches["minor"],$matches["patch"],$matches["update"]);
			}
			/**
			* Get the currently used branch
			* @return string
			*/
			public function GetBranch(){
				$pattern = "/(?<major>\d+).(?<minor>\d+).(?<patch>\d+)+-(?<branch>[^-]+)-(?<stage>[^-]+)-(?<update>\d+)/";
				$matches;
				preg_match($pattern,$this->Version,$matches);
				return $matches["branch"];
			}
			/**
			* Get a config value
			* @param string $key the key to find
			* @return mixed the value or false 
			*/
			public function GetConfigValue($key){
				//Get a _few_ values of config keys.							
				//But not everything, due security reasons.
				if (is_null($this->SystemKernel)){
					$this->SystemKernel =  new \Redundancy\Kernel\SystemKernel();	
				}				
				$result = false;
				if ($key == "Enable_Register")
					$result =  $this->SystemKernel->GetSetting("Enable_Register");//$this->Configuration["Enable_register"];
				else if ($key == "Program_XSS_Timeout")
					$result =  $this->SystemKernel->GetSetting("Program_XSS_Timeout");//$this->Configuration["Program_XSS_Timeout"];
				else if ($key == "Program_Storage_Dir")
					$result =  $this->SystemKernel->GetSetting("Program_Storage_Dir");
				else if ($key == "Program_Temp_Dir")
					$result =  $this->SystemKernel->GetSetting("Program_Temp_Dir");
				else if ($key == "Program_Snapshots_Dir")
					$result =  $this->SystemKernel->GetSetting("Program_Snapshots_Dir");
				else if ($key == "User_Contingent")
					$result =  $this->SystemKernel->GetSetting("User_Contingent");
				else if ($key == "User_Recover_Password_Length")
					$result =  $this->SystemKernel->GetSetting("User_Recover_Password_Length");
				else if ($key == "Program_Session_Timeout")
					$result =  $this->SystemKernel->GetSetting("Program_Session_Timeout");		
				else if ($key == "Program_Language")
					$result =  $this->SystemKernel->GetSetting("Program_Language");		
				else if ($key == "Program_Share_Link_Length")
					$result =  $this->SystemKernel->GetSetting("Program_Share_Link_Length");
				else if ($key == "Max_User_Storage")
					$result = $this->SystemKernel->GetSetting("Max_User_Storage");								
				else 
					return false;
				if (!is_null($result))
					return $result->Value;
				else
					return false;
			}
			/**
			* Check if a string is json
			* @param string $string the string to check
			* @return bool the rsult of the check
			*/
			function isJson($string) {
				json_encode($string);
				return (json_last_error() == JSON_ERROR_NONE);
			}
			/**
			* print out the json equivalent of an object
			* @param $result the object to print
			* @return string the json string (will be printed out)
			*/
			public function Output($result){
				if ($_POST["method"] == "GetContentOfFile"){
					echo $result;
					return;
				}
				if ($this->isJson($result)){
					$this->SetHTTPHeader($result);
					echo json_encode($result);
				}else{
					echo $result;
				}
				
			}			
			/**
			* Sets the HTTP status code
			* @param int $result the return code to represent. If $value is an errorcode (int), the Statuscode 503 Status Unavailable will be set. If the return value is not an errorcode, no additional status code will be set and 200 OK will be returned
			* @return int the statuscode or -1
			*/	
			private function SetHTTPHeader($result){
				if (is_int($result) || (is_bool($result) && (bool)$result == false))
					return http_response_code("503");
				return -1;
			}	
			/**
			* Get the installations display name
			* @return the application name
			*/
			public function GetAppName(){
				return $this->SystemKernel->GetSetting("Program_Name")->Value;//$this->Configuration["Program_Name_ALT"];
			}
		}
?>
