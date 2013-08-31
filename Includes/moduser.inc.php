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
	 * User modify task are done with this file
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
	 if (isset($_SESSION) == false)
			session_start();	
	if (isset($_GET["task"]) && $_SESSION["role"] == 0 && is_admin() && $GLOBALS["config"]["Program_Enable_Web_Administration"] == 1)
	{
		if ($_GET["task"] == "delete")
		{
			
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$user = mysqli_real_escape_string($connect, $_GET["user"]);
			user_delete($user);
		}
	}
	else
	{
		echo "You don't have enought permissions";
	}
?>