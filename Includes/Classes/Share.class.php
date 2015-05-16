<?php	
	/**
	* Share.class.php
	*/	
	namespace Redundancy\Classes;
	/**
	 * This file contains the share class
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
	class Share{
		/**
		* the role id
		*/
		public $Id;
		/**
		* The entry which is shared
		*/
		public $Entry;
		/**
		* The user which shared the file
		*/
		public $UserID;		
		/**
		* The target user if the file is shared to another user
		*/
		public $TargetUser;	
		/**
		* The permissions if the file is shared to another user
		*/
		public $Permissions;	
		/**
		* The sharecode if the file is shared by link
		*/
		public $ShareCode;
		/**
		* The date when the file was shared
		*/	
		public $SharedDateTime;		
	}
?>
