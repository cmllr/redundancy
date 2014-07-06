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
			$path = "";			
			if (strpos($configValue,__REDUNDANCY_ROOT__) === false)
			{
				if (file_exists(__REDUNDANCY_ROOT__.$configValue))
					$path =  __REDUNDANCY_ROOT__.$configValue;
				else
					$path =  $configValue;
			}							
			else{
				$path =  __REDUNDANCY_ROOT__.$configValue;
			}
			if (substr($path, -1)  != "/")
				$path = $path ."/";
			return $path;
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
			if (!$this->IsDisplayNameAllowed($escapedName))
				return \Redundancy\Classes\Errors::DisplayNameNotAllowed;		
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowCreatingFolder))
				return \Redundancy\Classes\Errors::NotAllowed;
			if ($this->IsEntryExisting($escapedName,$escapedRoot,$escapedToken))
				return \Redundancy\Classes\Errors::EntryExisting;
			
			$uploadDateTime = date('Y-m-d H:i:s');
			$userAgent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? "The platform could not be detected!" : $_SERVER['HTTP_USER_AGENT'];
			$hash = $this->GetUniqueHash($name);
			$dbinsertion = sprintf("Insert into FileSystem (sizeInByte,filePath,displayName,uploadDateTime,lastChangeDateTime,uploadUserAgent,hash,ownerID,parentFolder,mimeType) values ('%u',null,'%s','%s','%s','%s','%s','%d', '%d','%s')",0,$escapedName,$uploadDateTime, $uploadDateTime,$userAgent,$hash,$ownerId,$escapedRoot,'inode/directory');		
			DBLayer::GetInstance()->RunInsert($dbinsertion);
			return $this->IsEntryExisting($escapedName,$escapedRoot,$escapedToken);
		}
		/**
		* Get the storage informations
		* @param string $token a valid session token to identify the user
		* @returns \Redundancy\Classes\FileSystemAnalysis object or an errorcode
		*/
		public function GetStorage($token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;						
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;	
			$result = new \Redundancy\Classes\FileSystemAnalysis();
			$result->sizeInByte = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ContingentInByte;			
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select sizeInByte from FileSystem where ownerID = '%d'",$ownerId));			
			if (is_null($dbquery))
				$result->usedStorageInByte = 0;
			else{
				foreach ($dbquery as $value){
					//only proceed if the token was valid				
					$result->usedStorageInByte = $result->usedStorageInByte + $value["sizeInByte"];
				}
			}				
			return $result;	
		}
		/**
		* Checks if a displayname is allowed in the system
		* @param string $displayname the displayname of the entry
		* @return bool the result of the check
		*/
		public function IsDisplayNameAllowed($displayname){
			$array = \Redundancy\Classes\SystemConstants::NotAllowedChars;
			$array = explode(";",$array);
			for ($i=0;$i<count($array);$i++){
				if ($array[$i] != "" && strpos($displayname,$array[$i]) !== false)
					return false;
			}
			return true;
		}		
		/**
		* Upload a file to a given folder
		* Note: You have to send the single file in an field named "file" in $_FILES.
		* @param int $root the id of the new root folder
		* @param string $token a valid session token
		* @return bool if the action was successfull or an errorcode to describe the problem
		*/
		public function UploadFile($root,$token){		
			$escapedRoot = DBLayer::GetInstance()->EscapeString($root,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;			
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowUpload))
				return \Redundancy\Classes\Errors::NotAllowed;
			//For reverting
			$newAddedFiles = [];				
			if (is_null($_FILES["file"]))
				return false;
			$displayName =  DBLayer::GetInstance()->EscapeString($_FILES["file"]["name"],true);
			if ($this->IsEntryExisting($displayName,$escapedRoot,$escapedToken))
				return \Redundancy\Classes\Errors::EntryExisting;
			if (!$this->IsDisplayNameAllowed($displayName))
				return \Redundancy\Classes\Errors::DisplayNameNotAllowed;				
			$sizeInByte = DBLayer::GetInstance()->EscapeString($_FILES["file"]["size"],true);
			//Do the insertion only if there is enough space
			if ($this->GetStorage($escapedToken)->usedStorageInByte + $sizeInByte < $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ContingentInByte){
				$uploadDateTime = date('Y-m-d H:i:s');			
				$type =  DBLayer::GetInstance()->EscapeString($_FILES["file"]["type"],true);
				$tempPath = $_FILES["file"]["tmp_name"];
				$hash = $this->GetUniqueHash($displayName);
				$filePath = $this->GetUniqueStorageFileName($displayName);
				$userAgent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? "The platform could not be detected!" : $_SERVER['HTTP_USER_AGENT'];
				//A little workaround if the program is runned in a test environment (e. g. PHPUnit)			
				if ($GLOBALS["Kernel"]->SystemKernel->IsInTestEnvironment()){
					if (!file_exists($tempPath))
						return \Redundancy\Classes\Errors::TempFileCouldNotBeMoved;
					$uploadResult = copy($tempPath,$this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage).$filePath);
				}else{
					$uploadResult = move_uploaded_file($tempPath,$this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage).$filePath);
				}	
				if ($uploadResult){
					$query = sprintf("Insert into FileSystem (sizeInByte,filePath,displayName,uploadDateTime,lastChangeDateTime,uploadUserAgent,hash,ownerId,parentFolder,mimeType) values('%d','%s','%s','%s','%s','%s','%s','%d','%d','%s')",$sizeInByte,$filePath,$displayName,$uploadDateTime,$uploadDateTime,$userAgent,$hash,$ownerId,$root,$type);	
					DBLayer::GetInstance()->RunInsert($query);
					
					return 	$this->IsEntryExisting($displayName,$escapedRoot,$escapedToken);
				}
				else{
					return \Redundancy\Classes\Errors::TempFileCouldNotBeMoved;
				}
			}
			else{
				return \Redundancy\Classes\Errors::NoSpaceLeft;
			}	
		}
		/**
		* Delete an directory
		* @param string $name the absolute path of the directory, e. g. /test/test2/
		* @param string $token the valid session token
		* @return bool the result of the deletion.
		*/
		public function DeleteDirectory($name,$token){
			$escapedName = DBLayer::GetInstance()->EscapeString($name,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowDeletingFolder))
				return false;
			$folder = $this->GetEntryByAbsolutePath($name,$token);
			if (is_null($folder))
				return false;
			$query = sprintf("Select * from FileSystem where OwnerID = '%u' and parentFolder = '%d'",$ownerId,$folder->Id);			
			$result = DBLayer::GetInstance()->RunSelect($query);	
			if (count($result) != 0){
				foreach ($result as $value){	
					if (is_null($value["filePath"]) && $value["sizeInByte"] == 0)
					{
						//It's a folder
						$this->DeleteDirectory($this->GetAbsolutePathById($value["id"],$escapedToken),$escapedToken);
					}
					else{
						//Its a file
						$this->DeleteFile($this->GetAbsolutePathById($value["id"],$escapedToken),$escapedToken);
					}	
				}
			}		
			
			//Delete Directory itself
			$dbquery = DBLayer::GetInstance()->RunDelete(sprintf("Delete from FileSystem where Id = '%d' and ownerId = '%u' limit 1",$folder->Id,$ownerId));
			return !$this->IsEntryExisting($folder->DisplayName,$folder->ParentID,$escapedToken);
		}
		/**
		* Deletes an file
		* @param string $absolutePath the absolute path to the file
		* @param string $token a valid session token
		* @return bool to describe the physical deletion on the server | Errorcode if failed
		*/
		public function DeleteFile($absolutePath,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedabsolutePath = DBLayer::GetInstance()->EscapeString($absolutePath,true);
			$entry = $this->GetEntryByAbsolutePath($escapedabsolutePath,$token);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if (is_null($entry))
				return \Redundancy\Classes\Errors::EntryNotExisting;
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowDeletingFile))
				return \Redundancy\Classes\Errors::NotAllowed;
			$fileToDelete = $this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage).$entry->FilePath;
			if (unlink($fileToDelete)){
				$dbquery = DBLayer::GetInstance()->RunDelete(sprintf("Delete from FileSystem where Id = '%d' and ownerId = '%u' limit 1",$entry->Id,$ownerId));
				return true;
			}else{
				return false;
			}
		}
		/**
		* Check if an folder is a parent of its own children to prevent recursive loops
		* @param \Redundancy\Classes\FileSystemItem (or inheriting) $toSearch the item to get moved/copied etc.
		* @param \Redundancy\Classes\FileSystemItem (or inheriting) $toSearch the target item which should be the new root of $toSearch
		* @param string $token a valid session token
		* @return bool the result of the check
		*/
		private function CheckIfEntryIsInBranch($toSearch,$branch,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedtoSearch =$toSearch;
			$escapedbranch = $branch;
			//Folder that is searched
			$toSearchAbsolutePath = $this->GetAbsolutePathById($toSearch->Id,$escapedToken);
			$branchAbsolutePath = $this->GetAbsolutePathById($branch->Id,$escapedToken);				
			//if ($toSearchAbsolutePath == $branchAbsolutePath)
			//	return true;
			$idToSearch = $toSearch->Id;
			$lastGotId = $branch->ParentID;
			do{
				$entry = $this->GetEntryById($lastGotId,$escapedToken);
				$lastGotId = $entry->ParentID;		
				if ($lastGotId == $idToSearch || $idToSearch == $entry->Id)
					return true;
			}while($lastGotId != -1);
			return false;
		}
		/**
		* Check if a entry is a directory and 
		* @param string $absolutePath the absolute path of the entry
		* @param string $token a valid session token
		* @return bool the result of the check
		*/
		private function IsDirectory($absolutePath,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedabsolutePath = DBLayer::GetInstance()->EscapeString($absolutePath,true);
			$entry = $this->GetEntryByAbsolutePath($escapedabsolutePath,$escapedToken);
			if (!is_null($entry) && $entry->MimeType == "inode/directory"){
				return true;
			}
			else{
				return false;
			}
		}
		/**
		* Move an entry to another root dir
		* @param string $oldAbsolutePath the old absolute path
		* @param string $newRoot the path of the target dir to get the entry moved into
		* @return bool | An errorcode describing the problem
		*/
		public function MoveEntry($oldAbsolutePath,$newRoot,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedoldAbsolutePath = DBLayer::GetInstance()->EscapeString($oldAbsolutePath,true);
			$escapednewRoot = DBLayer::GetInstance()->EscapeString($newRoot,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if (!$this->IsDirectory($escapednewRoot,$escapedToken))
				return \Redundancy\Classes\Errors::TargetIsNoDirectory;
			if ($escapedoldAbsolutePath == "/")
				return \Redundancy\Classes\Errors::RootCannotbeMoved;
			$entry = $this->GetEntryByAbsolutePath($escapedoldAbsolutePath,$token);
			$targetEntry = $this->GetEntryByAbsolutePath($newRoot,$token);
			if (is_null($entry) || is_null($targetEntry))
				return \Redundancy\Classes\Errors::EntryNotExisting;
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowMoving))
				return \Redundancy\Classes\Errors::NotAllowed;
			//Update the folder information			
			if (!$this->CheckIfEntryIsInBranch($entry,$targetEntry,$token) ){
				//Check if the entry is not already existing in the target directory
				if (!$this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken)){
						$query = sprintf("Update FileSystem set parentFolder = '%d' where OwnerID = '%u' and ID = '%u'",$targetEntry->Id,$ownerId,$entry->Id);		
						DBLayer::GetInstance()->RunUpdate($query);
						return $this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken);
				}
				else{
						return \Redundancy\Classes\Errors::EntryExisting ;	
				}
			}
			else{
				return \Redundancy\Classes\Errors::CanNotPasteIntoItself;
			}			
		}
		/**
		* Copy an filesystementry to a given directory
		* @param string $oldAbsolutePath the old absolute Path of the file/ folder
		* @param string $newRoot the new root path of the file/ folder
		* @param string $token a valid session token
		* @return mixed The result of the action or an error code
		* @todo Implement a fucking test for this algorithm *hating*
		*/
		public function CopyEntry($oldAbsolutePath,$newRoot,$token){			
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedoldAbsolutePath = DBLayer::GetInstance()->EscapeString($oldAbsolutePath,true);
			$escapednewRoot = DBLayer::GetInstance()->EscapeString($newRoot,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;			
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if (!$this->IsDirectory($escapednewRoot,$escapedToken))
				return \Redundancy\Classes\Errors::TargetIsNoDirectory;
			if ($escapedoldAbsolutePath == "/")
				return \Redundancy\Classes\Errors::RootCannotbeMoved;			
			$entry = $this->GetEntryByAbsolutePath($escapedoldAbsolutePath,$token);
			$targetEntry = $this->GetEntryByAbsolutePath($escapednewRoot,$token);
			if (is_null($entry) || is_null($targetEntry))
				return \Redundancy\Classes\Errors::EntryNotExisting;
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowMoving))
				return \Redundancy\Classes\Errors::NotAllowed;			
			if ($entry->MimeType != "inode/directory"){
				//Its a file				
				if ($this->GetStorage($escapedToken)->usedStorageInByte + $entry->SizeInBytes < $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ContingentInByte){
					if (!$this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken)){
						$uploadDateTime = date('Y-m-d H:i:s');			
						$type =  $entry->MimeType;
						$tempPath = $this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage).$entry->FilePath;
						$hash = $this->GetUniqueHash($entry->DisplayName);
						$filePath = $this->GetUniqueStorageFileName($entry->DisplayName);
						$userAgent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? "No_User_Agent" : $_SERVER['HTTP_USER_AGENT'];
						//A little workaround if the program is runned in a test environment (e. g. PHPUnit)						
						$uploadResult = copy($tempPath,$this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage).$filePath);
						
						if ($uploadResult){							
							$query = sprintf("Insert into FileSystem (sizeInByte,filePath,displayName,uploadDateTime,lastChangeDateTime,uploadUserAgent,hash,ownerId,parentFolder,mimeType) values('%d','%s','%s','%s','%s','%s','%s','%d','%d','%s')",$entry->SizeInBytes,$filePath,$entry->DisplayName,$uploadDateTime,$uploadDateTime,$userAgent,$hash,$ownerId,$targetEntry->Id,$type);	
								
									
							DBLayer::GetInstance()->RunInsert($query);
							return $this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken);
						}
						else{
							return \Redundancy\Classes\Errors::TempFileCouldNotBeMoved;
						}
					}
					else{
						return \Redundancy\Classes\Errors::EntryExisting ;	
					}
				}
				else{
					return \Redundancy\Classes\Errors::NoSpaceLeft;
				}	
			}
			else{	
						
				if ($this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken))
					return \Redundancy\Classes\Errors::EntryExisting ;
					
				if ($this->CheckIfEntryIsInBranch($entry,$targetEntry,$escapedToken))
					return \Redundancy\Classes\Errors::CanNotPasteIntoItself;
				//It is a directory					
				if ($this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken))	{								
					return \Redundancy\Classes\Errors::CopyingFailed;	
				}
				else{									
					$this->CreateDirectory($entry->DisplayName, $targetEntry->Id,$escapedToken);
				}							
				$queryToGetFiles = sprintf("Select * from FileSystem where ParentFolder ='%d' and ownerId = '%d'",$entry->Id,$ownerId);				
				
				$absolutePath = $this->GetAbsolutePathById($targetEntry->Id,$escapedToken).$entry->DisplayName."/";				
				$result = DBLayer::GetInstance()->RunSelect($queryToGetFiles);					
				if (count($result) != 0){
					foreach ($result as $value){							
						if (is_null($value["filePath"]) && $value["sizeInByte"] == 0 && $value["mimeType"] == "inode/directory")
						{
							//It's a folder, copy it to the new created root	
							$this->CopyEntry($this->GetAbsolutePathById($value["id"],$escapedToken),$absolutePath,$escapedToken);
						}
						else{
							//Its a file
							$this->CopyEntry($this->GetAbsolutePathById($value["id"],$escapedToken),$absolutePath,$escapedToken);
						}	
					}
				}
				return $this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken);				
				
			}			
		}
		/**
		* Get an entry by an absolute path, e. g. /test/test2
		* @param string $absolutePath the absolute path to the entry
		* @param string $token a valid session token
		* @return \Redundancy\Classes\File | \Redundancy\Classes\Folder | null (if failed) or Errors::TokenNotValid if the token is not valid
		*/
		public function GetEntryByAbsolutePath($absolutePath,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedabsolutePath = DBLayer::GetInstance()->EscapeString($absolutePath,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if ($absolutePath == "/")
				return $this->GetEntryById(-1,$escapedToken);
			///root/bla
			$pathParts = explode("/",$escapedabsolutePath);				
			$lastId = -1;		
			for ($i = 0; $i < count($pathParts);$i++){
				if ($pathParts[$i] != ""){	
								
					$checkquery = sprintf("Select id from FileSystem where DisplayName = '%s' and OwnerID = '%u' and parentFolder = '%d'",$pathParts[$i],$ownerId,$lastId);					
					$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);	
								
					$lastId = $checkresult[0]["id"];													
				}
			}	
								
			return $this->GetEntryById($lastId,$escapedToken);
		}
		/**
		* Rename a filesystem entry
		* @param int $id the entry's ID
		* @param string $newName the new displayed name 
		* @param string $token a valid session token
		* @return bool the result of the change or an error code
		*/
		public function RenameEntry($id,$newName,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedId = DBLayer::GetInstance()->EscapeString($id,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			$escapedDisplayName = DBLayer::GetInstance()->EscapeString($newName,true);
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowRenaming))
				return \Redundancy\Classes\Errors::NotAllowed;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if ($escapedId == -1)
				return false;
			if (!$this->IsDisplayNameAllowed($escapedDisplayName))
				return \Redundancy\Classes\Errors::DisplayNameNotAllowed;
			$entry = $this->GetEntryById($escapedId,$token);
			//Only continue if the entry is existing and there is not another entry with this name
			if (!is_null($entry) && !$this->IsEntryExisting($newName,$entry->ParentID,$escapedToken)){
				$query = sprintf("Update FileSystem set DisplayName = '%s' where OwnerID = '%u' and parentFolder = '%d'",$escapedDisplayName,$ownerId,$entry->ParentID);		
				DBLayer::GetInstance()->RunUpdate($query);
				return true;
			}
			else{
				return false;
			}			
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
				$dir->OwnerID = $ownerId;
				$dir->ParentID = -1;
				$dir->CreateDateTime = null;
				$dir->LastChangeDateTime = null;
				$dir->Hash = null;
				$dir->MimeType ="inode/directory";
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
				$dir->OwnerID = $checkresult[0]["ownerId"];
				$dir->ParentID = $checkresult[0]["parentFolder"];
				$dir->CreateDateTime = $checkresult[0]["uploadDateTime"];
				$dir->LastChangeDateTime = $checkresult[0]["lastChangeDateTime"];
				$dir->Hash = $checkresult[0]["hash"];
				$dir->MimeType = $checkresult[0]["mimeType"];
				return $dir;
			}
			else if (!is_null($checkresult[0]["filePath"])){
				$file = new \Redundancy\Classes\File();
				$file->Id = $checkresult[0]["id"];
				$file->DisplayName = $checkresult[0]["displayName"];
				$file->OwnerID = $checkresult[0]["ownerId"];
				$file->ParentID = $checkresult[0]["parentFolder"];
				$file->CreateDateTime = $checkresult[0]["uploadDateTime"];
				$file->LastChangeDateTime = $checkresult[0]["lastChangeDateTime"];
				$file->Hash = $checkresult[0]["hash"];
				$file->SizeInBytes = $checkresult[0]["sizeInByte"];
				$file->FilePath = $checkresult[0]["filePath"];
				$file->UsedUserAgent = $checkresult[0]["uploadUserAgent"];
				$file->MimeType = $checkresult[0]["mimeType"];
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
		* Return an unique storage filename for a new filesystem entry.
		* @param string $value the name of the new filesystem entry (will be concatenated with the current timestamp)
		* @return string an unique filename for a new filesystem entry
		*/
		private function GetUniqueStorageFileName($value){
			$escapedHash = DBLayer::GetInstance()->EscapeString($value,true);
			$timeStamp = date('Y-m-d H:i:s u');
			$hashToSearch = $this->Hash($escapedHash.$timeStamp);
			$checkquery = sprintf("Select Id from FileSystem where filePath = '%s'",$hashToSearch);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);
			do{
				$hashToSearch = sha1($escapedHash.$timeStamp);
				$checkquery = sprintf("Select Id from FileSystem where filePath = '%s'",$hashToSearch);
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
