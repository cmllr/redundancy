<?php
	/**
	* Kernel.Installer.class.php
	*/	
	namespace Redundancy\Kernel;

	//**********************************************third party stuff*********************************
	/**
	* This class contains needed functions to install Redundancy
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
	class Installer{
		public function IsLocked(){
			return file_exists("./lock");
		}
		public function Lock(){
			$myfile = fopen("./lock", "w");
			$txt = "locked\n";
			fwrite($myfile, $txt);
			fclose($myfile);
		}
		public function ParseLanguage($lng){
			if (strpos($lng, "..") !== true)
				$GLOBALS["Language"] = parse_ini_file("./Language/".$lng.".lng");
			else
				$GLOBALS["Language"] = parse_ini_file("./Language/de.lng");
		}
		public function TestDBConnection($user,$pass,$host,$dbname,$driver,$path){
			$result = false;
			try{
				require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.PDO.class.php";

				if ($driver == "MySQL"){				
					$connectionString = "mysql:dbname=".$dbname.";host=".$host;					
					$options = array(
						\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
					);
					$pdo =  new \PDO($connectionString,$user,$pass,$options);	
				}else{
					$connectionString="sqlite:".$path;
					$pdo =  new \PDO($connectionString);	
				}							
				return true;				
			}catch(\Exception $e){				
				$result = false;
			}
			return $result;
		}
		public function WriteDBConfig($user,$pass,$host,$dbname,$driver,$dbpath = ""){
			$content = file_get_contents(__REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php");
			$content =preg_replace("/const\s+DBName+\s+=\s+\".{0,}\";/", "const DBName = \"$dbname\";", $content);
			$content =preg_replace("/const\s+DBUser+\s+=\s+\".{0,}\";/", "const DBUser = \"$user\";", $content);
			$content =preg_replace("/const\s+DBPassword+\s+=\s+\".{0,}\";/", "const DBPassword = \"$pass\";", $content);
			$content =preg_replace("/const\s+DBHost+\s+=\s+\".{0,}\";/", "const DBHost = \"$host\";", $content);

			if ($driver == "MySQL")
				$driver = "pdo_mysql";
			else if ($driver == "SQLite")
				$driver = "pdo_sqlite";
			$content =preg_replace("/const\s+DBDriver+\s+=\s+\".{0,}\";/", "const DBDriver = \"$driver\";", $content);
			$content =preg_replace("/const\s+DBPath+\s+=\s+\".{0,}\";/", "const DBPath = \"$dbpath\";", $content);
			
			if (file_put_contents(__REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php", $content) === false)
				return false;
			else
				return true;
		}
		public function GetDirectoryPermissions(){
			$dirs = array();
			$dirs["/Storage/"] = is_writeable(__REDUNDANCY_ROOT__."Storage/");
			$dirs["/Temp/"] = is_writeable(__REDUNDANCY_ROOT__."Temp/");
			$dirs["/Snapshots/"] = is_writeable(__REDUNDANCY_ROOT__."Snapshots/");
			$dirs["Database-Config"] = is_writeable(__REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php");
                        $dirs["Lockfile"] = is_writeable(__REDUNDANCY_ROOT__ );
			return $dirs;
		}
		public function GetExtensionStatus(){
			$extensions = array();
			$extensions["php-gd"] = function_exists("gd_info");
			$extensions["mcrypt"] = function_exists("mcrypt_list_modes");
			$extensions["file"] = function_exists("finfo_open");
			return $extensions;
		}
		public function GetSettings(){
			$settings = array();
			$settings["upload_max_filesize"] = ini_get("upload_max_filesize") > "3M";
			$settings["post_max_size"] = ini_get("post_max_size") > "3M";
			$settings["max_execution_time"] = ini_get("max_execution_time") != "30";
			return $settings;
		}
		public function DoTheImport(){
			require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php";
			require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.PDO.class.php";
			$dump = str_replace("pdo_", "", \Redundancy\Kernel\Config::DBDriver);
			$content = file_get_contents(__REDUNDANCY_ROOT__."Dumps/".$dump.".sql");
			$queries = explode(";", $content);			
			try{
				try{
					foreach ($queries as $key => $value) {					
						if (!empty($value))
							$result =  \Redundancy\Kernel\DBLayer::GetInstance()->GetConnection()->query($value);
					}
				}catch(\Exception $e){					
					return false;
				}
				return true;
			}catch(\Exception $e){
			}
			return false;
		}
		/**
		* Hashes a given password
		* @param $password the given password
		* @return the password hash
		*/
		private function HashPassword($password){
			$options = [
				    'cost' => 11,
				    'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
				];
			return password_hash($password, PASSWORD_BCRYPT, $options);
		}
		public function SetUser($user,$pass,$email){
			require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php";
			require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.PDO.class.php";
			try{
				try{					
					$pass = $this->HashPassword($pass);
					$registered= date("Y-m-d H:i:s",time());
					$conn = \Redundancy\Kernel\DBLayer::GetInstance()->GetConnection();
					$result =  $conn->query("Update User set loginName = '$user',passwordHash = '$pass',mailAddress ='$email',registrationDateTime ='$registered' where Id = 1");				
				}catch(\Exception $e){
					return false;
				}
				return true;
			}catch(\Exception $e){
			}
			return false;
		}
	}
?>
