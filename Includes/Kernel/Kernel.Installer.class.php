<?php
	/**
	* Kernel.INstaller.class.php
	*/	
	namespace Redundancy\Kernel;

	//**********************************************third party stuff*********************************
	require_once __REDUNDANCY_ROOT__.'Lib/Doctrine/Doctrine/Common/ClassLoader.php';	
	use Doctrine\Common\ClassLoader;
	use Doctrine\DBAL;
	use Doctrine\Common;
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
		public function TestDBConnection($user,$pass,$host,$dbname,$driver){
			$result = false;
			try{
				$classLoader = new ClassLoader('Doctrine\DBAL', __REDUNDANCY_ROOT__."Lib/Doctrine");
				$commonLoader = new ClassLoader('Doctrine\Common', __REDUNDANCY_ROOT__."Lib/Doctrine");
				$classLoader->register();
				$commonLoader->register();
				$config = new \Doctrine\DBAL\Configuration();
				$connectionParams = array(
					'dbname' =>$dbname,
				    'user' =>  $user,
				    'password' => $pass,
				    'host' =>$host,
				);
				if (file_exists($dbname))
					$connectionParams["path"] = $dbname;
				if ($driver = "MySQL")
					$connectionParams["driver"] = "pdo_mysql";
				$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
				$conn->connect();
				$result =  $conn->isConnected();
			}catch(\Exception $e){
				$result = false;
			}
			return $result;
		}
		public function WriteDBConfig($user,$pass,$host,$dbname,$driver){
			$content = file_get_contents(__REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php");
			$content =preg_replace("/const\s+DBName+\s+=\s+\".{0,}\";/", "const DBName = \"$dbname\";", $content);
			$content =preg_replace("/const\s+DBUser+\s+=\s+\".{0,}\";/", "const DBUser = \"$user\";", $content);
			$content =preg_replace("/const\s+DBPassword+\s+=\s+\".{0,}\";/", "const DBPassword = \"$pass\";", $content);
			$content =preg_replace("/const\s+DBHost+\s+=\s+\".{0,}\";/", "const DBHost = \"$host\";", $content);

			if ($driver == "MySQL")
				$driver = "pdo_mysql";
			$content =preg_replace("/const\s+DBDriver+\s+=\s+\".{0,}\";/", "const DBDriver = \"$driver\";", $content);
			if (file_exists($dbname))
				$content =preg_replace("/const\s+DBPath+\s+=\s+\".{0,}\";/", "const DBDriver = \"$DBPath\";", $content);
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
			return $dirs;
		}
		public function DoTheImport(){
			require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php";
			$content = file_get_contents(__REDUNDANCY_ROOT__."Dump.sql");
			$queries = explode(";", $content);
			try{
				$classLoader = new ClassLoader('Doctrine\DBAL', __REDUNDANCY_ROOT__."Lib/Doctrine");
				$commonLoader = new ClassLoader('Doctrine\Common', __REDUNDANCY_ROOT__."Lib/Doctrine");
				$classLoader->register();
				$commonLoader->register();
				$config = new \Doctrine\DBAL\Configuration();
				$connectionParams = array(
					'dbname' => \Redundancy\Kernel\Config::DBName,
				    'user' =>  \Redundancy\Kernel\Config::DBUser,
				    'password' =>\Redundancy\Kernel\Config::DBPassword,
				    'host' => \Redundancy\Kernel\Config::DBHost,
				    'driver' => \Redundancy\Kernel\Config::DBDriver,
				);
				if (!empty(\Redundancy\Kernel\Config::DBPath))
					$connectionParams["path"] =  \Redundancy\Kernel\Config::DBPath;
				$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
				$conn->connect();
				try{
					foreach ($queries as $key => $value) {
						$result =  $conn->query($value);
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
		public function SetUser($user,$pass){
			require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Config.class.php";
			try{
				$classLoader = new ClassLoader('Doctrine\DBAL', __REDUNDANCY_ROOT__."Lib/Doctrine");
				$commonLoader = new ClassLoader('Doctrine\Common', __REDUNDANCY_ROOT__."Lib/Doctrine");
				$classLoader->register();
				$commonLoader->register();
				$config = new \Doctrine\DBAL\Configuration();
				$connectionParams = array(
					'dbname' => \Redundancy\Kernel\Config::DBName,
				    'user' =>  \Redundancy\Kernel\Config::DBUser,
				    'password' =>\Redundancy\Kernel\Config::DBPassword,
				    'host' => \Redundancy\Kernel\Config::DBHost,
				    'driver' => \Redundancy\Kernel\Config::DBDriver,
				);
				if (!empty(\Redundancy\Kernel\Config::DBPath))
					$connectionParams["path"] =  \Redundancy\Kernel\Config::DBPath;
				$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
				$conn->connect();
				try{					
					$pass = $this->HashPassword($pass);
					$result =  $conn->query("Update User set loginName = '$user',passwordHash = '$pass' where ID = 1");				
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