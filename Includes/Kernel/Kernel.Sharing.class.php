<?php
	/**
	* Kernel.Sharing.class.php
	*/	
	namespace Redundancy\Kernel;
	/**
	* This file contains the functions to share files and folders
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
	class SharingKernel{
                /**
                * Shares an entry by a share code
                * @param string $absolutePath the absolute path of the entry
                * @param string $token a valid session token
                * @return bool | Errorcode | the share code
                */
		public function ShareByCode($absolutePath,$token){
			$newCode = $this->GetFreeShareLink();
                        $escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedabsolutePath = DBLayer::GetInstance()->EscapeString($absolutePath,true);			
			$owner = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);
			if (is_null($owner))
				return \Redundancy\Classes\Errors::TokenNotValid;
			$ownerId = $owner->ID;
			$entry = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath($escapedabsolutePath,$escapedToken);
			if (is_null($entry))
				return \Redundancy\Classes\Errors::EntryNotExisting;                        
                        //Everything is OK, continue
                        if ($this->IsEntryShared($escapedabsolutePath,$escapedToken,\Redundancy\Classes\ShareMode::ByCode))
                                return \Redundancy\Classes\Errors::EntryAlreadyShared;
                        $shared = date('Y-m-d H:i:s');		
                        $insertQuery = sprintf("Insert into SharedFileSystem (entryID,userID,targetUserID,permissions,shareCode,shared) values ('%d','%d','null','null','%s','%s')",$entry->Id,$ownerId,$newCode,$shared);
                        $result = DBLayer::GetInstance()->RunInsert($insertQuery);
                        if ($this->IsEntryShared($escapedabsolutePath,$escapedToken,\Redundancy\Classes\ShareMode::ByCode))
				return $newCode;
			else
				return false;
		}
                /**
                * Checks if an entry is already shared
                * @param string $absolutePath the absolute path of the entry
                * @param string $token a valid session token
                * @param int $shareMode the sharemode, can be taken from \Redundancy\Classes\ShareMode
                * @return bool 
                */
                public function IsEntryShared($absolutePath,$token,$shareMode){
                     	$newCode = $this->GetFreeShareLink();
                        $escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedabsolutePath = DBLayer::GetInstance()->EscapeString($absolutePath,true);			
			$owner = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);
			if (is_null($owner))
				return false;			
			$ownerId = $owner->ID;
			$entry = $GLOBALS["Kernel"]->FileSystemKernel->GetEntryByAbsolutePath($escapedabsolutePath,$escapedToken);
			if (is_null($entry))
				return false;
                        if ($shareMode == \Redundancy\Classes\ShareMode::ByCode)
                                $query = sprintf("Select count(id) as amount from SharedFileSystem where entryID = '%d' and targetUserID = 0",$entry->Id);
                        else
                               $query = sprintf("Select count(id) as amount from SharedFileSystem where entryID = '%d' and targetUserID <> 0",$entry->Id);                        
                        $result = DBLayer::GetInstance()->RunSelect($query);
                        if ($result["0"]["amount"] == 0)
                                return false;
                        else
                                return true;
                }
                /**
                * Delete a code based share of an entry
                * @param string $code the code to delete
                * @param string $token a valid session token
                * @return bool | errorcode
                */
		public function DeleteCodeShare($code,$token){
                        $escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$escapedCode = DBLayer::GetInstance()->EscapeString($code,true);	
                        $owner = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);
                        if (is_null($owner))
				return \Redundancy\Classes\Errors::TokenNotValid;
			$isExistingQuery = sprintf("Select count(id) as amount from SharedFileSystem where shareCode = '%s' and userID = '%d'",$escapedCode,$owner->ID);
			$isExisting = DBLayer::GetInstance()->RunSelect($isExistingQuery);			
			if ($isExisting["0"]["amount"] == 0)
				return \Redundancy\Classes\Errors::EntryNotExisting;
                        $query = sprintf("Delete from SharedFileSystem where shareCode = '%s'",$escapedCode);
                      	DBLayer::GetInstance()->RunDelete($query);					
                        return true;
		}
		/**
		* Get an entry by it's sharecode
		* @param string $code the code of the share
		* @return \Redundancy\Classes\Folder | \Redundancy\Classes\File | errorcode, if failure
		*/
                public function GetEntryByShareCode($code){
			$escapedCode = DBLayer::GetInstance()->EscapeString($code,true);	
                        $query = sprintf("Select FileSystem.id from FileSystem inner join SharedFileSystem on SharedFileSystem.entryID = FileSystem.Id where shareCode ='%s'",$escapedCode);
                        $result = DBLayer::GetInstance()->RunSelect($query);
                        if (is_null($result))
                                return \Redundancy\Classes\Errors::EntryNotExisting;
                        return $this->GetSharedEntryByID($result[0]["id"]);
                }
		/**
		* Get an entry (init download)
		* @param int $id the id of the entry
		* @return nothing, but it initalizes a download
		* @todo implement this one.
		*/
		private function GetEntry($id){
						
		}
                /**
		* Returns an filesystem entry by the given id, for example displaying
		* @param int $id the entry's Id
		* @param string $token a valid session token	
		* @return \Redundancy\Classes\Folder | \Redundancy\Classes\File | null (if failed)
		*/
		private function GetSharedEntryByID($id){
			$escapedId = DBLayer::GetInstance()->EscapeString($id,true);
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
			$checkquery = sprintf("Select * from FileSystem where Id = '%u' limit 1",$escapedId);
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
		public function DeleteUserShare($code,$token){
			$escapedCode = DBLayer::GetInstance()->EscapeString($code,true);
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$owner = $GLOBALS["Kernel"]->UserKernel->GetUser($escapedToken);
                        if (is_null($owner))
				return \Redundancy\Classes\Errors::TokenNotValid;
			$entry = $this->GetEntryByShareCode($escapedCode);
			if (is_null($GLOBALS["Kernel"]->FileSystemKernel->GetEntryById($entry->Id,$escapedToken))){
				return false;
			}
			$query = "Delete from SharedFileSystem where shareCode = '%s'";
			DBLayer::GetInstance()->RunDelete($query);
			return true;
		}	
		/**
		* Returns an random string in the given length
		* @param int $length the length of the string
		* @return string the random string
		*/
		public function GetRandomString($length){
			$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$s_length = strlen($chars);
			$randomString = "";
			if ($length <= $s_length){
				$randomString = substr(str_shuffle($chars), 0, $length);
			}
			else
			{
				$iterations = $length/$s_length;
				$singlePart = $length/$iterations;
				for ($i = 0; $i < $iterations ;$i++)
				{
					$randomString = $randomString.substr(str_shuffle($chars), 0, $singlePart);
				}
			}
			return $randomString;		
		}	
		/**
		* Returns an random string which represents a new share code
		* @return string the share code to use
		*/
		private function GetFreeShareLink(){			
			$shareCode = $this->GetRandomString($GLOBALS["Kernel"]->Configuration["Program_Share_Link_Length"]);
			$checkquery = sprintf("Select shareCode from SharedFileSystem where shareCode = '%s'",$shareCode);
			$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);
			do{
				$hashToSearch =  $this->GetRandomString($GLOBALS["Kernel"]->Configuration["Program_Share_Link_Length"]);
				$checkquery = sprintf("Select shareCode from SharedFileSystem where shareCode = '%s'",$shareCode);
				$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);
			}while(count($checkresult) != 0);
			return $hashToSearch;
		}	
	}
?>
