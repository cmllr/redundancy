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
	 * The user logout process will be done with this file
	 */
	//Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed
	if (isset($_SESSION) == false)
		session_start();

	
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	$id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
	echo $id;
	$query = "Update Users SET Session_Closed = '1' where ID = ".$id;
	echo $query;
	
	$erg = mysqli_query($connect,$query) or die ("error".mysqli_error($connect));
	echo "erg:".$erg;
	mysqli_close($connect);
		//exit everything
	
	session_unset();
	session_destroy();
	header('Location: ./index.php');
?>
