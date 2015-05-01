<?php
	/**
	* Readme file
	*/
	/**
	 * Read me for initial informations about the program structure.
	 * The Redundancy API elements can be accessed using the file "api.inc.php".
	 * The file needs following parameters:
	 *
         * -> HTTP GET "method": The method (attention: Case sensitive!)
	 *
         * -> HTTP GET "module": The name of the Kernel part, e. g. "Kernel" or "Kernel.UserKernel"
         *
         * -> HTTP POST "args": The arguments of the function call (as JSON - formatted string)
	 *
  	 * -> The result will be printed out as JSON - formatted string.
	 * 
	 * Example
	 * 
	 * server/Includes/api.inc.php?module=Kernel.User&method=Authentificate (the method needs an username and a password)
	 * 
	 * POST data: args -> ["username","password"]
	 * 
	 * Result: true
	 *
	 * This file is no sourcecode file.
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
	class README{
	
	}
?>
