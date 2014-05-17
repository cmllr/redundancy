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
	 * User data class
	 */	
	namespace Redundancy\Classes;
	class User{
		/**
		* The users ID
		*/
		public $ID = -1;
		/**
		* The Username
		*/
		public $LoginName = "";
		/**
		* The display name
		*/
		public $DisplayName = "";
		/**
		* The Email
		*/
		public $MailAddress = "";
		/**
		* Registration date and time
		*/
		public $RegistrationDateTime = null;
		/**
		* The date and time of the last login
		*/
		public $LastLoginDateTime = null;
		/**
		* The password hash.
		*/
		public $PasswordHash = "";
		/**
		* Determines if the user is enabled
		*/
		public $IsEnabled = false;
		/**
		* The User's storage size
		*/
		public $ContingentInByte = 0;
		/**
		* The object of the users role.
		*/
		public $Role = null;			
	}
?>
