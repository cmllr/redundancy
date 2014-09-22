<?php
	/**
	* Kernel.Constants.inc.php
	*/
	namespace Redundancy\Classes;
	/**
	 * This file contains a kind of enumerations for having global Errors, Messages etc.
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
	 **/
	abstract class Errors{
		const ModuleMissing = 1;
		const MethodMissing = 2;
		const UserOrEmailAlreadyGiven = 3;
		const RoleNotFound = 4;
		const MultipleUserAccountsFound = 5;
		const DataBaseError = 6;
		const PasswordOrUserNameWrong = 7;
		const TokenGenerationFailed = 8;
		const SystemDirectoryNotExisting = 9;
		const TokenNotValid = 10;
		const DirectoryNotFound = 11;
		const EntryExisting = 12;
		const NoSpaceLeft = 13;
		const TempFileCouldNotBeMoved = 14;
		const NotAllowed = 15;
		const EntryNotExisting = 16;
		const RootCannotbeMoved = 17;
		const CanNotPasteIntoItself = 18;
		const DisplayNameNotAllowed = 19;
		const CopyingFailed = 20;
		const TargetIsNoDirectory = 21;
        const EntryAlreadyShared = 22;
		const ArgumentMissing = 23;
		const NoTargetsExisting = 24;
		const NoPreviewPossible = 25;
		const NoShares = 26;
		const ShareWasNotDeleted=27;
		const UserNotExisting = 28;
		const CannotShareToMyself = 29;	
		const RegistrationNotEnabled = 30;	
		const ZipFileCreationFailed = 31;
		const ZipFileExisting = 32;
		const CannotDeleteFolder = 33;
		const CannotResetPassword = 34;
		const SystemAdminAccountNotAllowedToModify = 35;
	} 
	/**
	* The permission set in a human readable form
	*/	
	abstract class PermissionSet{
		const AllowUpload = 0;
		const AllowCreatingFolder = 1;
		const AllowDeletingFolder = 2;
		const AllowDeletingFile = 3;
		const AllowRenaming = 4;
		const AllowDeletingUser = 5;
		const AllowChangingPassword = 6;
		const AllowMoving = 7;
		const AllowCopying = 8;
		const AllowAdministration = 9;
	}
	/**
	* File system constants to use
	*/
	abstract class SystemDirectories
	{
	    const Storage = 0;
	    const Temp = 1;
        const Snapshots = 2;
        const Thumbnails = 3;
	}
	/**
	* This class contains contanst for the system
	*/
	abstract class SystemConstants{
	    const NotAllowedChars = '/';
	}
    /**
    * The systems share modes
    */
    abstract class ShareMode{
        const ByCode = 0;
        const ToUser = 1;
    }
    /**
    * The share direction
    */
    abstract class ShareDirection{
    	const ToMe = 0;
    	const ByMe = 1;
    }
?>
