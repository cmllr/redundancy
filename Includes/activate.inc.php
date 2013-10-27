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
	 * This file is needed to activated the user when using emails at the registration process
	 */
	//Include uri check
	require_once ("checkuri.inc.php");
	//Only proceed if the email= parameter is set
	if (isset($_GET["email"]))
	{
		//Include DataBase file
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		//get the Email
		$email = mysqli_real_escape_string($connect,$_GET["email"]);			
		$sql = "UPDATE Users SET Enabled=1 WHERE Email='$email'" ;
		//Update the data row
		mysqli_query($sql) or die("DataBase Error: 002 ".mysqli_error($connect));
		header("Location: ./index.php?message=user_enabled");
		exit;
	}
?>