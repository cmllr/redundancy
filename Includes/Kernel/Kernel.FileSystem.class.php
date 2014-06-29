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
		/**
		* Creates a new directory in the given root dir
		* @param string $name the name of the new directory (relative)
		* @param int $root int the Id of the current root dir
		* @param string $token a valid session token for the user
		* @return \Redundancy\Classes\Errors member if failed or the result of the creation
		*/
		public function CreateDirectory($name, $root,$token){
			$escapedName = DBLayer::GetInstance()->EscapeString($name,true);
			$escapedRoot = DBLayer::GetInstance()->EscapeString($root,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if ($this->IsEntryExisting($escapedName,$escapedRoot,$escapedToken))
				return \Redundancy\Classes\Errors::EntryExisting;
			$uploadDateTime = date('Y-m-d H:i:s');
			$hash = $this->GetUniqueHash($name);
			$dbinsertion = sprintf("Insert into FileSystem (sizeInByte,filePath,displayName,uploadDateTime,lastChangeDateTime,uploadUserAgent,hash,ownerID,parentFolder) values ('%u',null,'%s','%s','%s','%s','%s','%d', '%d')",0,$escapedName,$uploadDateTime, $uploadDateTime,$uploadDateTime,$hash,$ownerId,$escapedRoot);
			DBLayer::GetInstance()->RunInsert($dbinsertion);
			return $this->IsEntryExisting($escapedName,$escapedRoot,$escapedToken);
		}
		/**
		* Check if an entry is existing in a given folder
		* @param string $name the name of the new entry
		* @param int $root the Id of the current directory
		* @param string $token an valid session token
		* @return bool the result of the check or an member of \Redundancy\Classes\Errors
		*/
		public function IsEntryExisting($name,$root,$token){
			$escapedName = DBLayer::GetInstance()->EscapeString($name,true);
			$escapedRoot = DBLayer::GetInstance()->EscapeString($root,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			$checkquery = sprintf("Select * from FileSystem where DisplayName = '%s' and OwnerID = '%u' and parentFolder = '%d'",$escapedName,$ownerId,$escapedRoot);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);	
			if (count($checkresult) != 0)
				return true;
			else
				return false;
		}
		/**
		* Returns an filesystem entry by the given id
		* @param int $id the entry's Id
		* @param string $token a valid session token	
		* @return \Redundancy\Classes\Folder | \Redundancy\Classes\File | null (if failed)
		*/
		public function GetEntryById($id,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedId = DBLayer::GetInstance()->EscapeString($id,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			//If the node is the root node, return an fitting object
			if ($id == -1){
				$dir = new \Redundancy\Classes\Folder();
				$dir->Id = -1;
				$dir->DisplayName = "Rootnode";
				$dir->OwnerId = $ownerId;
				$dir->ParentID = -1;
				$dir->CreateDateTime = null;
				$dir->LastChangeDateTime = null;
				$dir->Hash = null;
				return $dir;
			}			
			$checkquery = sprintf("Select * from FileSystem where Id = '%u' and OwnerID = '%u' limit 1",$escapedId,$ownerId);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);	
			//If the result was not successfull, return null			
			if (count($checkresult) == 0)
				return null;	
			//Return an folder or file object		
			if (is_null($checkresult[0]["filePath"]) && $checkresult[0]["sizeInByte"] == 0){
				$dir = new \Redundancy\Classes\Folder();
				$dir->Id = $checkresult[0]["id"];
				$dir->DisplayName = $checkresult[0]["displayName"];
				$dir->OwnerId = $checkresult[0]["ownerId"];
				$dir->ParentID = $checkresult[0]["parentFolder"];
				$dir->CreateDateTime = $checkresult[0]["uploadDateTime"];
				$dir->LastChangeDateTime = $checkresult[0]["lastChangeDateTime"];
				$dir->Hash = $checkresult[0]["hash"];
				return $dir;
			}
			else if (!is_null($checkresult[0]["filePath"])){
				$file = new \Redundancy\Classes\File();
				$file->Id = $checkresult[0]["id"];
				$file->DisplayName = $checkresult[0]["displayName"];
				$file->OwnerId = $checkresult[0]["ownerId"];
				$file->ParentID = $checkresult[0]["parentFolder"];
				$file->CreateDateTime = $checkresult[0]["uploadDateTime"];
				$file->LastChangeDateTime = $checkresult[0]["lastChangeDateTime"];
				$file->Hash = $checkresult[0]["hash"];
				$file->SizeInBytes = $checkresult[0]["sizeInByte"];
				$file->FilePath = $checkresult[0]["filePath"];
				$file->UsedUserAgent = $checkresult[0]["uploadUserAgent"];
				return $file;
			}
		}
		/**
		* Get the absolute Path of an given folder ID	
		* @param int $id the Id of the folder
		* @param string $token a valid session token
		* @return string the folder path, if failed, \Redundancy\Classes\Errors::DirectoryNotFound
		*/
		public function GetAbsolutePathById($id,$token){
			$escapedId = DBLayer::GetInstance()->EscapeString($id,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			$checkquery = sprintf("Select * from FileSystem where Id = '%s' and OwnerID = '%s' limit 1",$escapedId,$ownerId);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);	
			if (count($checkresult) == 0)
				return \Redundancy\Classes\Errors::DirectoryNotFound;
			$lastParentId = $checkresult[0]["parentFolder"];	
			$absolutePath = $checkresult[0]["displayName"];	
			if ($lastParentId == -1)
				return "/".$absolutePath."/";
			do{
				$checkquery = sprintf("Select * from FileSystem where Id = '%s' limit 1",$lastParentId);
				$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);	
				$lastParentId = $checkresult[0]["parentFolder"];	
				$absolutePath = $checkresult[0]["displayName"]."/".$absolutePath;				
			}while($lastParentId != -1);
			return "/".$absolutePath."/";
		}
		/**
		* Return an unique hash for a new filesystem entry.
		* @param string $value the name of the new filesystem entry (will be concatenated with the current timestamp)
		* @return string an unique hash for a new filesystem entry
		*/
		private function GetUniqueHash($value){
			$escapedHash = DBLayer::GetInstance()->EscapeString($value,true);
			$timeStamp = date('Y-m-d H:i:s u');
			$hashToSearch = $this->Hash($escapedHash.$timeStamp);
			$checkquery = sprintf("Select Id from FileSystem where hash = '%s'",$hashToSearch);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);
			do{
				$hashToSearch = sha1($escapedHash.$timeStamp);
				$checkquery = sprintf("Select Id from FileSystem where hash = '%s'",$hashToSearch);
				$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);
			}while(count($checkresult) != 0);
			return $hashToSearch;
		}
		/**
		* Get a hashcode of a value (current sha1)
		* @param mixed $value the value to hash
		* @return string the hashed value
		*/
		private function Hash($value){
			return sha1($value);
		}
	}
?>
