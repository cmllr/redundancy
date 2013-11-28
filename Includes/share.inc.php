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
	 * Any share functionality is stored in tis file.
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
	if (isset($_SESSION) == false)
		session_start();
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	if (isset($_GET["file"]))
		$file = mysqli_real_escape_string($connect,$_GET["file"]);

	if (isset($_SESSION["user_id"]))
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
	$code = "";

	if (isset($_GET["delete"]) && $_GET["delete"] == "true" && $_SESSION["role"] != 3 && isGuest() == false)
	{					
		deleteExternalShare($file,$userID);
	}
	else if (isset($_GET["new"]) && $_GET["new"] == "true" && $_SESSION["role"] != 3 && isGuest() == false)
	{
		createExternalShare($file);		
	}	
	else if (isset($_GET["share"]))
	{
		$share = mysqli_real_escape_string($connect,$_GET["share"]);
		getExternalShare($share,isset($_GET["viewonly"]));		
	}
?>