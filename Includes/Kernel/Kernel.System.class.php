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
		* Check if an data array contains XSS parts
		* @param string | array $data the data to check
		* @return bool
		*/
		public function IsAffectedByXSS($data){
			$chars = explode(";", \Redundancy\Classes\SystemConstants::XSSChars);
			if (!is_array($data))
			{
				foreach ($chars as $key => $value) {
					if (!empty($value) && strpos($data, $value))
						return true;
				}
				return false;
			}
			else{
				for ($i=0;$i<count($data);$i++){
					foreach ($chars as $key => $value) {
					if (!empty($value) && strpos($data[$i], $value))
						return true;
					}
					return false;
				}
			}
		}
	}
?>
