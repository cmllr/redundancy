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
	 * This is the main database entry point
	 */
	//DataBase Connection file.
	//Needed for any operations of the program
	//replace user with your username
	//replace pass with your password
	//replace db with your database
	$connect = mysqli_connect("localhost", "user", "pass") or die(header("Location: ./Installer/"));//die("Error: 005 ".mysqli_error());
	mysqli_select_db($connect,"db") or die("Error: 006 ".mysqli_error()); 	
	if (isset($_POST["ACK"])){
		mysqli_query($connect,"SET NAMES 'utf8'");
		mysqli_query($connect,"SET CHARACTER SET 'utf8'");
	}
?>