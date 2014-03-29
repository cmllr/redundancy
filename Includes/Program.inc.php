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
	 * Program entry point
	 */
	$GLOBALS["Program_Version"] = "1.9.14-git-beta4-2";
	$GLOBALS["Program_Release"] = "29.03.2014";
	$GLOBALS["Program_Codename"] = "Cumulus";
	$GLOBALS["config_dir"] = "./";	
	$GLOBALS["testing"] = false;
	$GLOBALS["template"] = parse_ini_file("./Styles/Bootstrap.tpl");
	Include "Kernel/Kernel.User.inc.php";
	Include "Kernel/Kernel.Common.inc.php";
	Include "Kernel/Kernel.FileSystem.inc.php";
	Include "Kernel/Kernel.System.inc.php";
	Include "Kernel/Kernel.Interface.inc.php";
	Include "Kernel/Kernel.Logging.inc.php";
	Include "Kernel/Kernel.API.inc.php";
	Include "Kernel/Kernel.Guard.inc.php";
?>