<?php
	/**
	* Kernel.FileSystem.class.php
	*/	
	namespace Redundancy\Kernel;
	/**
	* This file contains the filesystem kernel to handle files and folders
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
	class FileSystemKernel{
		/**
		* Returns the directory of the the wanted system part (storage, temp, snapshots etc.)
		* @param $Directory \Redundancy\Classes\SystemDirectories member
		* @return string or \Redundancy\Classes\Errors::SystemDirectoryNotExisting
		*/
		private function GetSystemDir($Directory){
			if ($Directory == \Redundancy\Classes\SystemDirectories::Storage)
				$configValue = $role = $GLOBALS["Kernel"]->Configuration["Program_Storage_Dir"];
			else if ($Directory == \Redundancy\Classes\SystemDirectories::Temp)
				$configValue = $role = $GLOBALS["Kernel"]->Configuration["Program_Temp_Dir"];	
			else if ($Directory == \Redundancy\Classes\SystemDirectories::Snapshots)
				$configValue = $role = $GLOBALS["Kernel"]->Configuration["Program_Snapshots_Dir"];
			else
				return \Redundancy\Classes\Errors::SystemDirectoryNotExisting;		
			//If the programs root dir is not mentioned, check if the storage dir is in the current folder
			if (strpos($configValue,__REDUNDANCY_ROOT__) === false)
			{
				if (file_exists(__REDUNDANCY_ROOT__.$configValue))
					return __REDUNDANCY_ROOT__.$configValue;
				else
					return $configValue;
			}							
			else{
				return __REDUNDANCY_ROOT__.$configValue;
			}
		}
	}
?>
