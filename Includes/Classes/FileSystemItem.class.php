<?php	
	/**
	* FileSystemItem.class.php
	*/	
	namespace Redundancy\Classes;
	/**
	 * This file contains the base class for folders and files
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
	class FileSystemItem{
		/**
		* The item ID
		*/
		public $Id;
		/**
		* The items name to get displayed
		*/
		public $DisplayName;
		/**
		* The file owner ID (ID of Redundancy\Classes\User)
		*/
		public $OwnerID;
		/**
		* The parent items ID (ID of Redundancy\Classes\Folder)
		* or -1, if the file or folder is located in the root dir
		*/
		public $ParentID;
		/**
		* The items creation date and time
		*/
		public $CreateDateTime;
		/**
		* The time and date when the item was last changed
		*/
		public $LastChangeDateTime;
		/**
		* The hashcode of the item to verify integrity or to identify it
		*/
		public $Hash;
		/**
		* The mimetype of th entry
		*/
		public $MimeType;
		/**
		* The file physical path on the servers FS 
		*/
		public $FilePath;
	}
?>
