<?php
	/**
	* Kernel.System.class.php
	*/	
	namespace Redundancy\Kernel;
	/**
	* This class contains functions of the system, e. g. banning of users or system checks
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
	* 
	*/
	class SystemKernel{
		/**
		* Checks if the system is runned in a test environment
		* @return bool
		*/
		public function IsInTestEnvironment(){
			if (!isset($_SERVER["argv"]))
				return false;
			foreach ($_SERVER["argv"] as $value)
			{
				if (strpos($value,"phpunit") !== false){
					return true;
				}
			}
			return false;
		}
		/**
		* Bans a user with the given IP 
		* @param string $ip the users IP
		* @param string $why the reason for the ban
		*/
		public function BanUser($ip,$why){
			$escapedIP = DBLayer::GetInstance()->EscapeString($ip,true);
			$escapedWhy = DBLayer::GetInstance()->EscapeString($why,true);
			if (!$this->IsBanned($escapedIP)){
				$timeout = $GLOBALS["Kernel"]->GetConfigValue("Program_XSS_Timeout");
				$banDate = date("Y-m-d H:i:s",time());
				$query = sprintf("Insert into Bans (Ip,Reason,BanDateTime) Values ('%s','%s','%s')",$escapedIP,$escapedWhy,$banDate);
				DBLayer::GetInstance()->RunInsert($query);	
			}
		}
		/**
		* Check if a given IP is banned
		* @param string $ip the ip to check
		* @return bool
		*/
		public function IsBanned($ip){
			$escapedIP = DBLayer::GetInstance()->EscapeString($ip,true);
			$query = sprintf("Select id from Bans where Ip = '%s'",$escapedIP);
			$checkresult = DBLayer::GetInstance()->RunSelect($query);		
			if (count($checkresult) == 0)
				return false;
			else
				return true;
		}
		/**
		* Check if the current IP is banned
		* @return bool
		*/
		public function IsMyIPBanned(){
			$escapedIP = $GLOBALS["Kernel"]->UserKernel->GetIP();
			$query = sprintf("Select id from Bans where Ip = '%s'",$escapedIP);
			$checkresult = DBLayer::GetInstance()->RunSelect($query);		
			if (count($checkresult) == 0)
				return false;
			else
				return true;
		}
		/**
		* Unban the users IP
		* @param string $ip the IP to unban
		* @param string $token the admin session token
		* @return the result if the ban was deleted
		*/
		public function UnBan($ip,$token){
			$escapedIP = DBLayer::GetInstance()->EscapeString($ip,true);
			$escapedToken= DBLayer::GetInstance()->EscapeString($token,true);
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowAdministration))
				return \Redundancy\Classes\Errors::NotAllowed;
			$query = sprintf("Delete from Bans where Ip = '%s'",$escapedIP);
			DBLayer::GetInstance()->RunDelete($query);
			return !$this->IsBanned($escapedIP);
		}
		/**
		* Get a list of the banned IP's 
		* @param string $token the admin session token 
		* @return array | errocode (if not allowed)
		*/
		public function GetBannedIPs($token){
			$escapedToken= DBLayer::GetInstance()->EscapeString($token,true);
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowAdministration))
				return \Redundancy\Classes\Errors::NotAllowed;
			$query = "Select Ip,BanDateTime from Bans";
			$results = array();
			$dbquery = DBLayer::GetInstance()->RunSelect($query);
			if (is_null($dbquery))
				return array();
			foreach ($dbquery as $value){
				$results[$value["Ip"]] = $value["BanDateTime"];
			}
			return $results;
		}
		/**
		* Get the current settings
		* @return array with setting objects
		*/
		public function GetSettings(){
			$query = "Select * from Settings";
			$results = array();
			$dbquery = DBLayer::GetInstance()->RunSelect($query);
			foreach ($dbquery as $value){
				$s = new \Redundancy\Classes\Setting();
				$s->Name = $value["SettingName"];
				$s->Type = $value["SettingType"];
				$s->Value = $value["SettingValue"];
				$results[] = $s;
			}
			return $results;
		}
		/**
		* Get a specific setting
		* @param string $name the settings name
		* @return the setting or null;
		*/
		public function GetSetting($name) {
			$escapedName =  DBLayer::GetInstance()->EscapeString($name,true);
			$query = sprintf("Select * from Settings where SettingName = '%s'",$escapedName);			
			$dbquery = DBLayer::GetInstance()->RunSelect($query);
			if (is_null($dbquery) || count($dbquery) == 0)
				return null;
			foreach ($dbquery as $value){
				$s = new \Redundancy\Classes\Setting();
				$s->Name = $value["SettingName"];
				$s->Type = $value["SettingType"];
				//A special bool handling
				if ($s->Type =="Boolean")
					$s->Value = ($value["SettingValue"] == "true")? true : false;
				else
					$s->Value = $value["SettingValue"];
				return $s;
			}
			return null;
		}
		/**
		* Set a specific setting
		* @param string $token the admin session token
		* @param string $name the name of the setting
		* @param mixed $value the value to set.
		* @return errorcode, if failed, otherwise true
		*/
		public function SetSetting($token,$name,$value) {
			$escapedToken= DBLayer::GetInstance()->EscapeString($token,true);
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowAdministration))
				return \Redundancy\Classes\Errors::NotAllowed;
			$escapedName =  DBLayer::GetInstance()->EscapeString($name,true);
			$escapedValue=  DBLayer::GetInstance()->EscapeString($value,true);
			$query = sprintf("Update Settings set SettingValue = '%s' where SettingName = '%s'",$escapedValue, $escapedName);	
			$dbquery = DBLayer::GetInstance()->RunUpdate($query);	
			return true;
		}
		/**
		* Check if an data array contains XSS parts
		* @param string | array $data the data to check
		* @return bool
		*/
		public function IsAffectedByXSS($data){
			$chars = explode(";", \Redundancy\Classes\SystemConstants::XSSChars);

			if (!is_array($data))
			{
				foreach ($chars as $key => $value) {
					if (!empty($value) && strpos($data, $value) !== false)
						return true;
				}
				return false;
			}
			else{
				for ($i=0;$i<count($data);$i++){
					foreach ($chars as $key => $value) {
						if (!empty($value) && strpos($data[$i], $value) !== false)
							return true;
					}
					return false;
				}
			}
		}
	}
?>
