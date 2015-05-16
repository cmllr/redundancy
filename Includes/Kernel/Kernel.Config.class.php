<?php
	/**
	* Kernel.Config.class.php
	*/	
	namespace Redundancy\Kernel;
	/**
	* This class contains needed functions to deliver configuration data for the system
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
	abstract class Config{
		const DBName = "Lenticularis";
		const DBUser = "root";
		const DBPassword = "";
		const DBHost = "localhost";
		const DBDriver = "pdo_mysql";
		const DBPath = "";
	}

?>