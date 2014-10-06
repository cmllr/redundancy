<?php	
	/**
	* Role.class.php
	*/	
	namespace Redundancy\Classes;
	/**
	 * This file contains the role data
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
	class Role{
		/**
		* the role id
		*/
		public $Id;
		/**
		* The description/ name of the role, e. g. "root".
		*/
		public $Description;
		/**
		* The permissions of the role. It is an binary value describing if several actions are allowed or not.
		*/
		public $Permissions;
		/**
		* Is the role the systems default one?
		*/	
		public $IsDefault;			
	}
?>
