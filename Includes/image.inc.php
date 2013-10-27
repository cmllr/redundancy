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
	 * the image display module is located in this file.
	 */	
	//start a session if needed	
	if (isset($_SESSION) == false)
		session_start();	
	if (isset($_GET["file"]) )
	{
		//Include DataBase file
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$filename = "";
		$result = mysqli_query($connect,"Select * from Files  where Hash = '".mysqli_real_escape_string($connect,$_GET["file"])."' limit 1");	
		while ($row = mysqli_fetch_object($result)) {
			$filename = $GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$row->Filename;
		}	
		//Set the current file		
		$_SESSION["current_file"] = $filename;
		//Display the image itself
		displayImage($_SESSION["current_file"]);
	}	
	else if (isset($_SESSION["current_file"]) && $_SESSION["current_file"] != "-1"){			
		
		displayImage($_SESSION["current_file"]);	
	}	
?> 