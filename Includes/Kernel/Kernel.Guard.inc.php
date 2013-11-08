<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
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
	 * This checks filesystem operations if the given params are valid	 
	 */
	class Result{
		const OK = 0;
		const NullOrEmpty = 1;
		const RootDoesNotExist = 2;
		const NewDirExists = 3;
		const NewHashExists = 4;
		const NotClean = 5;
		const DirNotExisting = 6;
		const NewNameExists = 7;
		const NoChanges = 8;
		const FileNotExisting = 9;
		const FileIsExisting = 10;
		const SourceAndTargetEqual = 11;
		const TooBig = 12;
	} 
	class Guard{
		/**
		* check if the parameters for creating a dir are correct.
		* Determines following problems:
		* Parameters null or empty
		* SQL Injections
		* Root dir not existing
		* New dir already existing
		* Hash already given (if set)
		* @param $currentdir the current dir
		* @param $directory the entry to get created
		* @param $hashcode a given hashcode;
		* @return the result of the check
		*/
		public static function createDirValidator($currentdir,$directory,$hashcode = ""){			
			//*****************Start a session to continue*********************
			if (isset($_SESSION) == false)
				session_start();
			//*****************Check 1: Are params empty?**********************
			if (self::isNullOrEmpty($currentdir) || self::isNullOrEmpty($directory) || self::isNullOrEmpty($_SESSION['user_id']))
				return Result::NullOrEmpty;
			//*****************Check 2: Are params clean (SQL injection protection)?**********************
			if (self::isNotClean($currentdir) || self::isNotClean($directory) || self::isNotClean($_SESSION['user_id']) || self::isNotClean($hashcode))
				return Result::NotClean;	
			
			//*****************Check 3: Is the root dir existing?**************
			//*****************Only check if not base dir**********************
			if ($currentdir != "/")	{
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
				$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
				$currentdir =  mysqli_real_escape_string($connect,$currentdir);						 
				$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$currentdir' ") or die("Error 025: ".mysqli_error($connect));
				if (mysqli_affected_rows($connect) == 0)
					return Result::RootDoesNotExist;
			}
			//*****************Check 4: Is the new dir already existing?*******
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$directory =  mysqli_real_escape_string($connect,$directory);		
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$directory' ") or die("Error 025: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) != 0)
				return Result::NewDirExists;
			//*****************Check 5: If the hash is given, does it exists?*******
			if ($hashcode != ""){
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";				
				$hash =  mysqli_real_escape_string($connect,$hashcode);		
				$result = mysqli_query($connect,"Select ID from Files where Hash = '$hash' ") or die("Error 025: ".mysqli_error($connect));
				if (mysqli_affected_rows($connect) != 0)
					return Result::NewHashExists;
			}
			return Result::OK;
		}
		/**
		* check if the parameters for renaming a dir are correct.
		* Determines following problems:
		* Parameters null or empty
		* SQL Injections
		* Root dir not existing
		* Source dir not existing
		* newname already given
		* @param $source the source dir
		* @param $old_root the root of the source dir
		* @param $target the target name
		* @return the result of the check
		*/
		public static function renameDirValidator($source,$old_root,$target){
			//*****************Start a session to continue*********************
			if (isset($_SESSION) == false)
				session_start();
			//*****************Check 1: Are params empty?**********************
			if (self::isNullOrEmpty($source) || self::isNullOrEmpty($old_root) || self::isNullOrEmpty($target) || self::isNullOrEmpty($_SESSION['user_id']))
				return Result::NullOrEmpty;
			//*****************Check 2: Are params clean (SQL injection protection)?**********************
			if (self::isNotClean($source) || self::isNotClean($old_root) || self::isNotClean($target))
				return Result::NotClean;	
			//*****************Check 3: Is the root dir existing?**************
			//*****************Only check if not base dir**********************
			if ($old_root != "/")	{
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
				$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
				$currentdir =  mysqli_real_escape_string($connect,$old_root);						 
				$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$currentdir' ") or die("Error 025: ".mysqli_error($connect));
				if (mysqli_affected_rows($connect) == 0)
					return Result::RootDoesNotExist;
			}
			//*****************Check 4: Is the name of the new dir ok?************
			if (strpos($target,"/") !== false)
				return Result::NotClean;
			//*****************Check 5: Is the source dir already existing?*******
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$directory =  mysqli_real_escape_string($connect,$source);		
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$directory' ") or die("Error 025: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) == 0)
				return Result::DirNotExisting;
			//*****************Check 6: Is the name already given?*******
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$directory =  mysqli_real_escape_string($connect,$old_root.$target."/");			
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$directory' ") or die("Error 025: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) != 0)
				return Result::NewDirExists;			
			return Result::OK;
		}
		/**
		* check if the parameters for renaming a file are correct.
		* Determines following problems:
		* Parameters null or empty
		* SQL Injections
		* Root dir not existing
		* New name already given
		* No change needed (newname = oldname)
		* File does not exists
		* @param $newname the new filename
		* @param $hash hash of the file
		* @param $root the directory of the file
		* @return the result of the check
		*/
		public static function renameFileValidator($newname,$hash,$root){
			if (isset($_SESSION) == false)
				session_start();
			//*****************Check 1: Are params empty?**********************
			if (self::isNullOrEmpty($newname) || self::isNullOrEmpty($hash) || self::isNullOrEmpty($root) || self::isNullOrEmpty($_SESSION['user_id']) )
				return Result::NullOrEmpty;
			//*****************Check 2: Are params clean (SQL injection protection)?**********************
			if (self::isNotClean($newname) || self::isNotClean($hash) || self::isNotClean($root))
				return Result::NotClean;
			//*****************Only check if not base dir**********************
			if ($root != "/")	{
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
				$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
				$currentdir =  mysqli_real_escape_string($connect,$root);						 
				$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$currentdir' ") or die("Error 025: ".mysqli_error($connect));
				if (mysqli_affected_rows($connect) == 0)
					return Result::RootDoesNotExist;
			}
			//*****************Check 4: Is the name already given?*******
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$name = mysqli_real_escape_string($connect,$newname);	
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$name' and Directory = '$root' ") or die("Error 025: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) != 0)
				return Result::NewNameExists;	
			//*****************Check 5: Is the change not neccessary?*******
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$name = mysqli_real_escape_string($connect,$newname);	
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$name' and Directory = '$root' ") or die("Error 025: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) != 0)
				return Result::NoChanges;	
			
				//*****************Check 6: File does not exists?*******
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$hash = mysqli_real_escape_string($connect,$hash);	
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Hash = '$hash' and Directory = '$root' ") or die("Error 025: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) == 0)
				return Result::FileNotExisting;	
			return Result::OK;
		}
		/**
		* check if the parameters for copy/move of a file are correct.
		* Determines following problems:
		* Parameters null or empty
		* SQL Injections
		* target dir not existing		
		* File does not exists
		* File already existing in target dir
		* @param $newname the new filename
		* @param $hash hash of the file
		* @param $root the directory of the file
		* @return the result of the check
		*/
		public static function copyOrMoveFileValidator($hash,$newdir){
			if (isset($_SESSION) == false)
				session_start();
			//*****************Check 1: Are params empty?**********************
			if (self::isNullOrEmpty($hash) || self::isNullOrEmpty($newdir) || self::isNullOrEmpty($_SESSION['user_id']) )
				return Result::NullOrEmpty;
			//*****************Check 2: Are params clean (SQL injection protection)?**********************
			if (self::isNotClean($hash) || self::isNotClean($newdir))
				return Result::NotClean;
			//*****************Check 3: Target dir does not exists?*******
			if ($newdir != "/"){
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
				$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
				$newdir = mysqli_real_escape_string($connect,$newdir);	
				$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$newdir' ") or die("Error 025: ".mysqli_error($connect));
				if (mysqli_affected_rows($connect) == 0)
					return Result::DirNotExisting;	
			}
			//*****************Check 4: File does not exists?*******
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$hash = mysqli_real_escape_string($connect,$hash);	
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Hash = '$hash' ") or die("Error 0266: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) == 0)
				return Result::FileNotExisting;	
			//*****************Check 4: File does not exists?*******
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$hash = mysqli_real_escape_string($connect,$hash);	
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Hash = '$hash' ") or die("Error 0266: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) == 0)
				return Result::FileNotExisting;	
			//*****************Check 4: File already in target dir?*******
			$name = getFileByHash($hash);			
			echo $name;
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$hash = mysqli_real_escape_string($connect,$hash);	
			$newdir = mysqli_real_escape_string($connect,$newdir);	
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$name' and Directory = '$newdir ' ") or die("Error 0266: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) != 0)
				return Result::FileIsExisting;					
			return Result::OK;
		}
		/**
		* check if the parameters for copy/move of a folder are correct.
		* Determines following problems:
		* Parameters null or empty
		* SQL Injections
		* target dir not existing		
		* Folder does not exists
		* File already existing in target dir	
		* @param $newname the new filename
		* @param $hash hash of the file
		* @param $root the directory of the file
		* @return the result of the check
		*/
		public static function copyOrMoveFolderValidator($source,$old_root,$target){
			if (isset($_SESSION) == false)
				session_start();
			//*****************Check 1: Are params empty?**********************
			if (self::isNullOrEmpty($source) || self::isNullOrEmpty($old_root) || self::isNullOrEmpty($target) || self::isNullOrEmpty($_SESSION['user_id']) )
				return Result::NullOrEmpty;
			//*****************Check 2: Are params clean (SQL injection protection)?**********************
			if (self::isNotClean($source) || self::isNotClean($old_root) || self::isNotClean($target) || self::isNotClean($_SESSION['user_id']))
				return Result::NotClean;
			//*****************Check 3: Does the new root folder exists?**********************
			if ($old_root != "/"){
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
				$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
				$directory =  mysqli_real_escape_string($connect,$old_root);		
				$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$directory' ") or die("Error 025: ".mysqli_error($connect));
				if (mysqli_affected_rows($connect) == 0)
					return Result::RootDoesNotExist;
			}
			//*****************Check 4: Does the  target folder exists?**********************
			if ($target != "/"){
				include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
				$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
				$target =  mysqli_real_escape_string($connect,$target);		
				$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$target' ") or die("Error 025: ".mysqli_error($connect));
				if (mysqli_affected_rows($connect) == 0)
					return Result::DirNotExisting;
			}
			//*****************Check 3: Does the  source folder exists?**********************
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$owner_ID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
			$directory =  mysqli_real_escape_string($connect,$source);		
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Displayname = '$directory' ") or die("Error 025: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) == 0)
				return Result::DirNotExisting;
			//*****************Check 3: Does a folder already exist in the target folder? **********************
			$displayname = "";
			$result = mysqli_query($connect,"Select Filename_only from Files where UserID = '".$owner_ID."' and Displayname ='$directory' limit 1") or die("Error 025: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {
				$displayname = $row->Filename_only;
			}	
			$result = mysqli_query($connect,"Select ID from Files where UserID = '".$owner_ID."' and Filename_only = '$displayname' and Directory ='$target' ") or die("Error 025: ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) != 0)
				return Result::NewDirExists;
			if ($target == $source)
				return Result::SourceAndTargetEqual;
			return Result::OK;
		}
		/**
		* check if a given value is empty or null
		* @param $value the value
		* @return the result of the check
		*/
		public static function isNullOrEmpty($value){			
			if ($value == null)
				return true;
			if (empty($value) == true)
				return true;
			return false;
		}	
		/**
		* check if a given value is not clean and must be cleaned for database
		* @param $value the value
		* @return the result of the check
		*/
		public static function isNotClean($value){	
			if ($value == null)
				return false;
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
			if (mysqli_real_escape_string($connect,$value) != $value){
				mysqli_close($connect);
				return true;
			}else{
				mysqli_close($connect);
				return false;
			}			
		}		
	}	
?>