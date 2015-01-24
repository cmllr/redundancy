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
		public function GetSystemDir($Directory){			
			if ($Directory == \Redundancy\Classes\SystemDirectories::Storage)
				$configValue = $GLOBALS["Kernel"]->GetConfigValue("Program_Storage_Dir");
			else if ($Directory == \Redundancy\Classes\SystemDirectories::Temp)
				$configValue = $GLOBALS["Kernel"]->GetConfigValue("Program_Temp_Dir");	
			else if ($Directory == \Redundancy\Classes\SystemDirectories::Snapshots)
				$configValue = $GLOBALS["Kernel"]->GetConfigValue("Program_Snapshots_Dir");
			else if ($Directory == \Redundancy\Classes\SystemDirectories::Thumbnails)
				$configValue = "Thumbs";
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
		* Alias Function for CreateDirectory(string,int,string)
		* @param string $name the name of the new dir
		* @param string $current the current absolute path
		* @param string $token the valid session token
		* @return the result of CreateDirectory();
		*/
		public function CreateDirectoryFromCurrentFolder($name,$current,$token){
			if (!isset($current)){
				return false;
			}
			else{
				$folder = $this->GetEntryByAbsolutePath($current,$token);
				if (is_null($folder))
					return false;
				else
				{
					return $this->CreateDirectory($name,$folder->Id,$token);
				}
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
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);					
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
                        $ownerId = $ownerId->ID;
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
		* @return \Redundancy\Classes\FileSystemAnalysis object or an errorcode
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
		* Calculates the correct unit for a storage information
		* @param $value the value to calculate
		* @return a string containg the unit
		*/
		public function GetCorrectedUnit($value){
			if ($value <= 1024)
				return round($value,2)." B";
			else if ($value > 1024 && $value < (1024*1024))
				return round($value/(1024),2). " KB";
			elseif ($value > 1024*1024 && $value < (1024*1024*1024))
				return round($value/(1024*1024),2). " MB";
			elseif ($value > 1024*1024 && $value < (1024*1024*1024*1024))
				return round($value/(1024*1024*1024),2). " GB";
			elseif ($value > 1024*1024*1024 && $value < (1024*1024*1024*1024*1024))
				return round($value/(1024*1024*1024*1024),2). " TB";
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
		* Wrapper vor redirecting the files
		* @param string $root the root dir id
		* @param string $token a valid session token
		*@param string $files the files-array
		* @return bool if the action was successfull or an errorcode to describe the problem
		*/
		public function UploadFileWrapper($root,$token,$files){
			$_FILES["file"] = json_decode($files);			
			return $this->UploadFile($root,$token);
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
			$_FILES["file"] =  (array) $_FILES["file"];
			$tempPath = $_FILES["file"]["tmp_name"];					
			if (is_null($ownerId)){
				//Delete the temporary file!
				$this->CleanUpTemp($tempPath);	
				return \Redundancy\Classes\Errors::TokenNotValid;
			}
				
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowUpload)){
				//Delete the temporary file!
				$this->CleanUpTemp($tempPath);	
				return  \Redundancy\Classes\Errors::NotAllowed;
			}
				
			//For reverting
			$newAddedFiles = array();				
			if (is_null($_FILES["file"])){
				//Delete the temporary file!
				$this->CleanUpTemp($tempPath);	
				return "false";
			}			
			

			$displayName =  DBLayer::GetInstance()->EscapeString($_FILES["file"]["name"],true);
			if ($this->IsEntryExisting($displayName,$escapedRoot,$escapedToken)){
				//Delete the temporary file!
				$this->CleanUpTemp($tempPath);	
				return \Redundancy\Classes\Errors::EntryExisting;
			}			
				
			if (!$this->IsDisplayNameAllowed($displayName)){
				//Delete the temporary file!
				$this->CleanUpTemp($tempPath);	
				return \Redundancy\Classes\Errors::DisplayNameNotAllowed;	
			}
			//When the file is not existing..return an error
			if (!file_exists($tempPath))
			{
				return \Redundancy\Classes\Errors::TempFileCouldNotBeMoved;
			}				
			$sizeInByte = DBLayer::GetInstance()->EscapeString($_FILES["file"]["size"],true);			
			//Do the insertion only if there is enough space
			if ($this->GetStorage($escapedToken)->usedStorageInByte + $sizeInByte < $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ContingentInByte){
				$uploadDateTime = date('Y-m-d H:i:s');			
				//$type =  DBLayer::GetInstance()->EscapeString($_FILES["file"]["type"],true);	
				$filecontent = file_get_contents($tempPath);
				$finfo = new \finfo(FILEINFO_MIME_TYPE);		
				$type = $finfo->buffer($filecontent);			
				$hash = $this->GetUniqueHash($displayName);
				$filePath = $this->GetUniqueStorageFileName($displayName);
				$userAgent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? "The platform could not be detected!" : $_SERVER['HTTP_USER_AGENT'];
				//A little workaround if the program is runned in a test environment (e. g. PHPUnit)						
				if ($GLOBALS["Kernel"]->SystemKernel->IsInTestEnvironment() || strpos($tempPath, "REDUNDANCY") !== false){
					if (!file_exists($tempPath))
						return \Redundancy\Classes\Errors::TempFileCouldNotBeMoved;
					$uploadResult = copy($tempPath,$this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage).$filePath);
				}else{
					$uploadResult = move_uploaded_file($tempPath,$this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage).$filePath);
				}	
				//Delete the temporary file!
				$this->CleanUpTemp($tempPath);				
				if ($uploadResult){
					$query = sprintf("Insert into FileSystem (sizeInByte,filePath,displayName,uploadDateTime,lastChangeDateTime,uploadUserAgent,hash,ownerId,parentFolder,mimeType) values('%d','%s','%s','%s','%s','%s','%s','%d','%d','%s')",$sizeInByte,$filePath,$displayName,$uploadDateTime,$uploadDateTime,$userAgent,$hash,$ownerId,$root,$type);	
					DBLayer::GetInstance()->RunInsert($query);
					$this->RefreshLastChangeDateTimeOfParent($escapedRoot,$escapedToken);
					return 	$this->IsEntryExisting($displayName,$escapedRoot,$escapedToken);
				}
				else{					
					return \Redundancy\Classes\Errors::TempFileCouldNotBeMoved;
				}				
			}
			else{
				$this->CleanUpTemp($tempPath);			
				return \Redundancy\Classes\Errors::NoSpaceLeft;
			}	
		}
		/**
		* Deletes a temporary file
		* @param $tempPath string the temporary file path
		*/
		private function CleanUpTemp($tempPath){
			if (strpos($tempPath, "REDUNDANCY") !== false || !$GLOBALS["Kernel"]->SystemKernel->IsInTestEnvironment())
					unlink($tempPath);
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
			$this->RefreshLastChangeDateTimeOfParent($folder->Id,$escapedToken);
			//Delete sshares
			$GLOBALS["Kernel"]->SharingKernel->DeleteAllSharesOfEntry($folder->Hash,$escapedToken);
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
				//Delete the shares of the file
				$GLOBALS["Kernel"]->SharingKernel->DeleteAllSharesOfEntry($entry->Hash,$escapedToken);
				$dbquery = DBLayer::GetInstance()->RunDelete(sprintf("Delete from FileSystem where Id = '%d' and ownerId = '%u' limit 1",$entry->Id,$ownerId));
				
				return true;
			}else{
				return false;
			}
		}
		/**
		* Check if an folder is a parent of its own children to prevent recursive loops
		* @param \Redundancy\Classes\FileSystemItem $toSearch the item to get moved/copied etc.
		* @param \Redundancy\Classes\FileSystemItem $branch the target item which should be the new root of $toSearch
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
			if ($toSearchAbsolutePath == $branchAbsolutePath)
				return true;
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
		* Get the content of a directory
		* @param string $absolutePath the absolute path of the folder to get displayed;
		* @param string $token a valid session token
		* @return array containing the entries
		*/
		public function GetContent($absolutePath,$token){		
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedabsolutePath = DBLayer::GetInstance()->EscapeString($absolutePath,true);			
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);				
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			$ownerId = $ownerId->ID;
			$entry = $this->GetEntryByAbsolutePath($escapedabsolutePath,$escapedToken);
			if (is_null($entry)){
				return \Redundancy\Classes\Errors::EntryNotExisting;
			}			
			if (!$this->IsDirectory($escapedabsolutePath,$escapedToken))
				return \Redundancy\Classes\Errors::TargetIsNoDirectory;
			
			$queryToGetFiles = sprintf("Select id from FileSystem where ParentFolder ='%d' and ownerId = '%d'",$entry->Id,$ownerId);				
			$result = DBLayer::GetInstance()->RunSelect($queryToGetFiles);					
			$files = array();
			if (count($result) != 0){
				foreach ($result as $value){							
					$entry = $this->GetEntryById($value["id"],$escapedToken);
					if ($entry->MimeType == "inode/directory")
						$entry->SizeInBytes = $this->CalculateFolderSize($this->GetAbsolutePathById($entry->Id,$escapedToken),$escapedToken);
					$files[] = $entry;
				}		
			}

			return $files;
		}
		/**
		* Get a list from the user's folders.
		* @param string $token the session token
		* @return array the list of the folders
		*/
		public function GetFolderList($token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);			
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);				
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;		
			$ownerId = $ownerId->ID;				
			$queryToGetFiles = sprintf("Select id from FileSystem where ownerId = '%d'",$ownerId);				
			$result = DBLayer::GetInstance()->RunSelect($queryToGetFiles);							
			$files = array("/");
			if (count($result) != 0){
				foreach ($result as $value){							
					$entry = $this->GetEntryById($value["id"],$escapedToken);		
					if (is_null($entry->FilePath))			
						$files[] = $this->GetAbsolutePathById($entry->Id,$escapedToken);
				}		
			}
			return $files;
		}
		/**
		* Refreshs the last Change information of a folder branch
		* @param int $entryID the id of the child entry
		* @param string $token a valid session token
		* @return bool false if the entry does not exists		
		*/
		public function RefreshLastChangeDateTimeOfParent($entryID,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedentryID = DBLayer::GetInstance()->EscapeString($entryID,true);	
			$entry = $this->GetEntryById($escapedentryID,$escapedToken);
			if (is_null($entry))
				return false;		
			do{
				$changeDateTime = date('Y-m-d H:i:s');		
				$query = sprintf("Update FileSystem set lastChangeDateTime ='%s' where ID ='%d' or ID='%d'",$changeDateTime,$entry->ParentID,$entry->Id);
				DBLayer::GetInstance()->RunUpdate($query);
				$entry = $this->GetEntryById($entry->ParentID,$escapedToken);										
			}while($entry->ParentID != -1);		
			return true;			
		}
		/**
		* Calculate the size of a folder
		* @param string $absolutePath the absolute path of the directory
		* @param string $token a valid session token
		* @return the size or -1 in case of an error
		*/
		public function CalculateFolderSize($absolutePath,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);			
			$entry = $this->GetEntryByAbsolutePath($absolutePath,$escapedToken);
			$escapedabsolutePath = DBLayer::GetInstance()->EscapeString($absolutePath,true);
			$folderSize = 0;
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if (is_null($entry) || $entry->MimeType != "inode/directory")
				return -1;			
			$queryToGetFiles = sprintf("Select id,mimeType,sizeInByte from FileSystem where ParentFolder ='%d' and ownerId = '%d'",$entry->Id,$ownerId);
			$result = DBLayer::GetInstance()->RunSelect($queryToGetFiles);					
			$files = array();
			if (count($result) != 0){
				foreach ($result as $value){							
					if ($value["mimeType"] == "inode/directory"){
						$folderSize += $this->CalculateFolderSize($this->GetAbsolutePathById($value["id"],$escapedToken),$escapedToken);
					}
					else{
						$folderSize += $value["sizeInByte"];
					}
				}
			}	
			return $folderSize;	
		}
		/**
		* Move an entry to another root dir
		* @param string $id the entries id
		* @param string $newRoot the path of the target dir to get the entry moved into
		* @param string $token a valid session token
		* @return bool | An errorcode describing the problem
		*/
		public function MoveEntryById($id,$newRoot,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedId = DBLayer::GetInstance()->EscapeString($id,true);
			$escapednewRoot = DBLayer::GetInstance()->EscapeString($newRoot,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken)->ID;
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			if (!$this->IsDirectory($escapednewRoot,$escapedToken))
				return \Redundancy\Classes\Errors::TargetIsNoDirectory;		
			$entry = $this->GetEntryById($escapedId,$token);
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
						$this->RefreshLastChangeDateTimeOfParent($entry->Id,$escapedToken);
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
		* Move an entry to another root dir
		* @param string $oldAbsolutePath the old absolute path
		* @param string $newRoot the path of the target dir to get the entry moved into
		* @param string $token a valid session token
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
						$this->RefreshLastChangeDateTimeOfParent($entry->Id,$escapedToken);
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
		* @param string $id the entry id
		* @param string $newRoot the new root path of the file/ folder
		* @param string $token a valid session token
		* @return mixed The result of the action or an error code
		* @todo Implement a fucking test for this algorithm *hating*
		*/
		function CopyEntryById($id,$newRoot,$token){
			$absolutePath = $this->GetAbsolutePathById($id,$token);
			return $this->CopyEntry($absolutePath,$newRoot,$token);
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
							$this->RefreshLastChangeDateTimeOfParent($targetEntry->Id,$escapedToken);
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
				$queryToGetFiles = sprintf("Select * from FileSystem where ParentFolder ='%d' and ownerId = '%d'",$entry->Id,$ownerId);						
				if ($this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken))
					return \Redundancy\Classes\Errors::EntryExisting ;
					
				if ($this->CheckIfEntryIsInBranch($entry,$targetEntry,$escapedToken))
					return \Redundancy\Classes\Errors::CanNotPasteIntoItself;
				//It is a directory					
				if ($this->IsEntryExisting($entry->DisplayName,$targetEntry->Id,$escapedToken))	{								
					return \Redundancy\Classes\Errors::CopyingFailed;	
				}
				else{	
					$result = DBLayer::GetInstance()->RunSelect($queryToGetFiles);		
					$neededSize = 0;
					if (count($result) != 0){
						foreach ($result as $value){	
							if (is_null($value["filePath"]))
								$neededSize += $this->CalculateFolderSize($this->GetAbsolutePathById($value["id"],$escapedToken),$escapedToken);
							else
								$neededSize += $value["sizeInByte"];	
						}		
					}
					$freeSpace = $this->GetStorage($escapedToken)->sizeInByte - $this->GetStorage($escapedToken)->usedStorageInByte;
					error_log("needed $neededSize given $freeSpace for".$entry->Id); 
					if ($neededSize > $freeSpace)
						return \Redundancy\Classes\Errors::NoSpaceLeft;											
					$this->CreateDirectory($entry->DisplayName, $targetEntry->Id,$escapedToken);
				}							
					
				
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
				$this->RefreshLastChangeDateTimeOfParent($targetEntry->Id,$escapedToken);
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
				$query = sprintf("Update FileSystem set displayName = '%s' where OwnerID = '%u' and ID = '%d'",$escapedDisplayName,$ownerId,$entry->Id);		
				DBLayer::GetInstance()->RunUpdate($query);
				$this->RefreshLastChangeDateTimeOfParent($entry->Id,$escapedToken);
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
		* Returns an filesystem entry by the given hash
		* @param string $hash the entry's hash
		* @param string $token a valid session token	
		* @return \Redundancy\Classes\Folder | \Redundancy\Classes\File | null (if failed)
		* @todo fix the folder size issue.
		*/
		function GetEntryByHash($hash,$token){		
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedhash = DBLayer::GetInstance()->EscapeString($hash,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			$checkquery = sprintf("Select * from FileSystem where Hash = '%s' and OwnerID = '%u' limit 1",$escapedhash,$ownerId->ID);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);	
			if (count($checkresult) == 0)
				return null;	
			if (count($checkresult) == 1){
				return $this->GetEntryById($checkresult[0]["id"],$escapedToken);
			}
		}
		/**
		* Returns an filesystem entry by the given id
		* @param int $id the entry's Id
		* @param string $token a valid session token	
		* @return \Redundancy\Classes\Folder | \Redundancy\Classes\File | null (if failed)
		* @todo fix the folder size issue.
		*/
		public function GetEntryById($id,$token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedId = DBLayer::GetInstance()->EscapeString($id,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid;
			$ownerId = $ownerId->ID;
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
				$dir->CreateDateTime = $GLOBALS["Kernel"]->InterfaceKernel->FormatDate($checkresult[0]["uploadDateTime"]);
				$dir->LastChangeDateTime =  $GLOBALS["Kernel"]->InterfaceKernel->FormatDate($checkresult[0]["lastChangeDateTime"]);
				$dir->Hash = $checkresult[0]["hash"];
				$dir->MimeType = $checkresult[0]["mimeType"];		
				//error_log($this->CalculateFolderSize($this->GetAbsolutePathById($dir->Id,$escapedToken),$escapedToken));
				//$dir->SizeInBytes = 512;/// $this->CalculateFolderSize($this->GetAbsolutePathById($dir->Id,$escapedToken),$escapedToken);	
				//$dir->SizeWithUnit = $this->GetCorrectedUnit($dir->SizeInBytes);
				return $dir;
			}
			else if (!is_null($checkresult[0]["filePath"])){
				$file = new \Redundancy\Classes\File();
				$file->Id = $checkresult[0]["id"];
				$file->DisplayName = $checkresult[0]["displayName"];
				$file->OwnerID = $checkresult[0]["ownerId"];
				$file->ParentID = $checkresult[0]["parentFolder"];
				$file->CreateDateTime = $GLOBALS["Kernel"]->InterfaceKernel->FormatDate($checkresult[0]["uploadDateTime"]);
				$file->LastChangeDateTime = $GLOBALS["Kernel"]->InterfaceKernel->FormatDate($checkresult[0]["lastChangeDateTime"]);
				$file->Hash = $checkresult[0]["hash"];
				$file->SizeInBytes = $checkresult[0]["sizeInByte"];
				$file->FilePath = $checkresult[0]["filePath"];
				$file->UsedUserAgent = $checkresult[0]["uploadUserAgent"];
				$file->MimeType = $checkresult[0]["mimeType"];
				$file->SizeWithUnit = $this->GetCorrectedUnit($file->SizeInBytes);
				$thumbPath = $this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Thumbnails);
			
				if (file_exists($thumbPath.$file->FilePath."thumb")){
					$file->Thumbnail = true;
				}
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
			//Return the root dir if the function should find the root node.
			if ($id == -1)
				return "/";
			//Workaround 4 files and folder 
			$entry = $this->GetEntryById($escapedId,$escapedToken);	
			$checkquery = sprintf("Select * from FileSystem where Id = '%s' and OwnerID = '%s' limit 1",$escapedId,$ownerId);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);				
			if (count($checkresult) == 0)
				return \Redundancy\Classes\Errors::DirectoryNotFound;
			$lastParentId = $checkresult[0]["parentFolder"];	
			$absolutePath = $checkresult[0]["displayName"];	
			if ($lastParentId == -1){
				if (is_null($entry->FilePath))				
					return "/".$absolutePath."/";
				else
					return "/".$absolutePath;
			}
			
			do{
				$checkquery = sprintf("Select * from FileSystem where Id = '%s' limit 1",$lastParentId);
				$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);	
				$lastParentId = $checkresult[0]["parentFolder"];	
				$absolutePath = $checkresult[0]["displayName"]."/".$absolutePath;				
			}while($lastParentId != -1);
			if (is_null($entry->FilePath))				
				return "/".$absolutePath."/";
			else
				return "/".$absolutePath;
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
				$hashToSearch = $this->Hash($escapedHash.$timeStamp);
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
			$timeStamp = date('u s:i:h d-m-Y Y-m-d H:i:s u');
			$hashToSearch = $this->Hash($escapedHash.$timeStamp);
			$checkquery = sprintf("Select Id from FileSystem where filePath = '%s'",$hashToSearch);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);
			do{
				$hashToSearch = $this->Hash($escapedHash.$timeStamp);
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
		/**
		* Get the content of a file
		* @param string $hash the hashcode of the file
		* @param string $token the session token
		* @return the content of the file.
		*/		
		public function GetContentOfFile($hash,$token) {   
			$escapedHash = DBLayer::GetInstance()->EscapeString($hash,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$ownerId = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);			
			if (is_null($ownerId))
				return \Redundancy\Classes\Errors::TokenNotValid; 
			$ownerId = $ownerId->ID;
			$entry = $this->GetEntryByHash($escapedHash,$escapedToken);
			if (is_null($entry))
				return \Redundancy\Classes\Errors::EntryNotExisting;		   
		   	$dir = $this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage);
		    return file_get_contents($dir.$entry->FilePath);		    
		}
		/**
		* Initialize the creation of the zip file.
		* @param int $id the folder id
		* @param string $token the session token
		* @param int $rootpath the root folder id
		* @return an string containt the filename or errorcode.
		*/
		public function StartZipCreation($id,$token,$rootpath){
			$escapedId = DBLayer::GetInstance()->EscapeString($id,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedRootPath = DBLayer::GetInstance()->EscapeString($rootpath,true);
			$owner = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);	
			if (is_null($owner))
				return \Redundancy\Classes\Errors::TokenNotValid; 
			$ownerId = $owner->ID;
			$entry = $this->GetEntryById($escapedId,$escapedToken);
			if (is_null($entry))
				return \Redundancy\Classes\Errors::EntryNotExisting;
			//Start Zipfile creation
			$path =  $this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Temp);
			$filename = $owner->LoginName."".str_replace("/", "", $this->GetAbsolutePathById($escapedRootPath,$escapedToken));
			if (file_exists($path.$filename.".zip"))
				return \Redundancy\Classes\Errors::ZipFileExisting;
			$zip = new \ZipArchive;
			$res = $zip->open($path.$filename.".zip", \ZipArchive::CREATE);
			if ($res !== true){
				return \Redundancy\Classes\Errors::ZipFileCreationFailed;
			}
			$this->CreateZipFileOfFolder($zip,$entry,$escapedRootPath,$escapedToken);
			$zip->close();
			return $this->GetAbsolutePathById($escapedRootPath,$escapedToken)	;
		}
		/**
		* Create a zip file
		* @param mixed $zip the zip object
		* @param FileSystemItem $entry the entry
		* @param string $escapedRootPath the root folder
		* @param string $escapedToken a valid session token
		* @return an string containt the filename or errorcode.
		*/
		private function CreateZipFileOfFolder($zip,$entry,$escapedRootPath,$escapedToken){
			$zip->addEmptyDir(iconv("UTF-8","CP437",$this->GetAbsolutePathById($entry->Id,$escapedToken)));
			$entries = $this->GetContent($this->GetAbsolutePathById($entry->Id,$escapedToken),$escapedToken);
			$storagePath = $this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage);
			foreach ($entries as $key => $value) {
				if (!is_null($value->FilePath)){
					$path = iconv("UTF-8","CP437",$this->GetAbsolutePathById($value->Id,$escapedToken));
					$zip->addFile($storagePath.$value->FilePath,$path);
				}
				else{
					$this->CreateZipFileOfFolder($zip,$value,$escapedRootPath,$escapedToken);
				}
			}
		}
		/**
		* Get a list of the newest entries
		* @param string $token the session token
		* @return an array containg the changed files
		*/
		public function GetLastChangesOfFileSystem($token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$owner = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);	
			if (is_null($owner))
				return \Redundancy\Classes\Errors::TokenNotValid; 
			$ownerId = $owner->ID;
			$query =sprintf("Select id,displayName,date(uploadDateTime) as Day,time(uploadDateTime) as DayTime,\"new\" as Source,FilePath,Hash from FileSystem where ownerID = %d union all Select id,displayName,date(lastChangeDateTime) as Day,time(lastChangeDateTime) as DayTime,\"changed\" as Source,FilePath,Hash from FileSystem where ownerID = %d order by Day desc, DayTime desc limit 25",$ownerId,$ownerId);
			$result = DBLayer::GetInstance()->RunSelect($query);
			if (count($result) != 0){
				foreach ($result as $key => $value) {
					$result[$key] = $value;
					$result[$key]["Day"] = $GLOBALS["Kernel"]->InterfaceKernel->FormatDateDayOnly($value["Day"]);
				}
			}			
			return $result;
		}
		/**
		* Extracts the needed values and builds a SQL query.
		* @param string $SearchTerm the search term containing the values
		* @return the SQL query or errorcode
		*/
		public function GetSearchTerms($SearchTerm){
			$partials = explode(",",$SearchTerm);
			$SearchTerms = array();
			for ($i = 0;$i<(count($partials));$i++){
				if ($partials[$i] != ""){
					$pattern ="/(?<column>[^\s=<>%;$]+)\s{0,}(?<operator>[=<>%]{1,2})\s{0,}(?<term>[^$,;]*)/";
					$result;
					preg_match($pattern, $partials[$i], $result);
					if (!empty($result["column"]) && !empty($result["operator"]) && !empty($result["term"])){
						$operator = ($result["operator"] == "%") ? " like " : $result["operator"];

						$SearchTerms[] = array($result["column"],$operator,$result["term"]);
					}
					else
						return "";
				}
			}
			return $this->BuildQuery($SearchTerms);
		}
		/**
		* Builds from the given values a sql query
		* @param array $Terms the terms
		* @return the SQL query
		*/
		public function BuildQuery($Terms){
			$query = "Select Id from FileSystem where ";
			for ($i = 0;$i<(count($Terms));$i++){
				$like = ($Terms[$i][1] == " like ") ? "%" :"";
				$query = $query.$Terms[$i][0]. " ". $Terms[$i][1]. " '$like".$Terms[$i][2]."$like'";
				if ($i !=  count($Terms) -1)
					$query = $query ." and ";
			}
			return $query;
		}
		/**
		* Search in the file system
		* @param string $searchTerm the term to search
		* @param string $token the session token
		* @returns array | Errorcode 
		*/
		public function SearchFileSystem($searchTerm, $token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedSearchTerm = DBLayer::GetInstance()->EscapeString($searchTerm,true);
			$owner = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);	
			if (is_null($owner))
				return \Redundancy\Classes\Errors::TokenNotValid; 
			$ownerId = $owner->ID;
			$results = "";//query result;
			$query ="";
			if (preg_match("/[=<>%]{1,}/", $escapedSearchTerm)){
				$query = $this->GetSearchTerms($escapedSearchTerm);
				if ($query == "")
					return \Redundancy\Classes\Errors::SearchSyntaxWrong;
			}
			else{
				$query = "Select Id from FileSystem where displayName like '%".sprintf("%s",$escapedSearchTerm)."%'"; 
			}
			$query = $query .sprintf(" and ownerID = '%d'",$ownerId);
			$result = DBLayer::GetInstance()->RunSelect($query);
			if (count($result) == 0 || is_null($result)){
				return \Redundancy\Classes\Errors::NoSearchResults;	
			}
			else{
				$entries = array();
				foreach ($result as $key => $value) {
					$id = $value["Id"];
					$entries[] = $this->GetEntryById($id,$escapedToken);
				}
				return $entries;
			}
		}
		/**
		* Maps a physical folder into the R2-Filesystem.
		* @param string $folder the folder path
		* @param string $token a valid session token
		* @param the root dir to path the folder in (path, not ID)
		* @return bool | Errorcode 
		* @todo test
		*/
		public function MapTreeInFS($folder,$token,$root){
			$escapedFolder = DBLayer::GetInstance()->EscapeString($folder,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedRoot = DBLayer::GetInstance()->EscapeString($root,true);
			$entries = $this->CreateList($escapedFolder);
			foreach ($entries as $key => $value) {
				if (is_array($value)){
					//IT is an directory!
					//Create the directory
					$rootFolder = $this->GetEntryByAbsolutePath($escapedRoot,$escapedToken);
					if (is_null($rootFolder))
						return false;
					$result = $this->CreateDirectory($key, $rootFolder->Id,$escapedToken);					
					//Get the new created folder ID					
					$parent = $this->GetEntryByAbsolutePath($escapedRoot.$key,$escapedToken);
					$this->MapTreeInFS($folder.$key."/",$escapedToken,$escapedRoot.$key."/");	
				}
				else{
					//its an file
					$rootFolder = $this->GetEntryByAbsolutePath($escapedRoot,$escapedToken);
					$filecontent = file_get_contents($escapedFolder.$value);
					$finfo = new \finfo(FILEINFO_MIME_TYPE);		
					$_FILES["file"]["name"] = $value;
					$_FILES["file"]["type"] = $finfo->buffer($filecontent);	
					$_FILES["file"]["tmp_name"] = $folder."/".$value;
					$_FILES["file"]["error"] = "UPLOAD_ERR_OK";
					$_FILES["file"]["size"] = filesize($folder."/".$value);
					$this->UploadFile($rootFolder->Id,$escapedToken);
				}
			}
			return rmdir($folder);
		}
		/**
		* Create a flat list of an folder (physical, not in r2!)
		* @param string dir the path;
		* @return array an array of the contents, when an error occurs, empty.
		*/
		public function CreateList($dir){
			$result = array(); 
			if (!is_dir($dir))
				return array();
			$cdir = scandir($dir); 
			foreach ($cdir as $key => $value) 
			{ 
			  if (!in_array($value,array(".",".."))) 
			  { 
			     if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
			     { 
			        $result[$value] = $this->CreateList($dir . DIRECTORY_SEPARATOR . $value); 
			     } 
			     else 
			     { 
			        $result[] = $value; 
			     } 
			  } 
			} 			   
  			return $result; 
		}
		/**
		* Unzip an Zip Archive in place
		* @param string $hash the hash of the file to identify
		* @param string $token the session token
		* @param string $path the current root dir (path, not int!);
		* @return Boolean  | Errorcode the result of the hole process
		* @todo test!
		* @todo problem with runtime limitation -> nasty kill needed.
		*/
		public function UnzipInPlace($hash,$token,$path){
			$escapedHash = DBLayer::GetInstance()->EscapeString($hash,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedPath = DBLayer::GetInstance()->EscapeString($path,true);
			$owner = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);	
			if (is_null($owner))
				return \Redundancy\Classes\Errors::TokenNotValid; 
			$entry = $this->GetEntryByHash($escapedHash,$escapedToken);
			if (is_null($entry))
				return \Redundancy\Classes\Errors::EntryNotExisting;			
			$zip = new \ZipArchive;
			$storagePath = $this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Storage);	
			$opened = 	$zip->open($storagePath.$entry->FilePath);
			if ($opened === true)
			{				
				$tmpFolder = $this->GetSystemDir(\Redundancy\Classes\SystemDirectories::Temp);
				$name = $this->GetUniqueStorageFileName($entry->FilePath);
				mkdir($tmpFolder.$name."REDUNDANCY");
				$zip->extractTo($tmpFolder.$name."REDUNDANCY");
				$entries = $this->CreateList($tmpFolder.$name."REDUNDANCY");
				if (count($entries) == 0)
					return \Redundancy\Classes\Errors::EmptyZip;
				return $this->MapTreeInFS($tmpFolder.$name."REDUNDANCY/",$escapedToken,$escapedPath);
			}
			else{
				return \Redundancy\Classes\Errors::CouldNotOpenZip;
			}
		}
	}
?>
