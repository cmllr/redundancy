<div class = "contentWrapper">
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
	 * This file deletes an account
	 * @TODO: Bugfixes
	 */
	//Include uri check
	require_once ("checkuri.inc.php");
	//Use with caution
	//TODO: Find the bug which causes a complete loss of any data?! :(
	if (isset($_SESSION) == false)
			session_start();
	if ($_SESSION["role"] != 3 && isset($_GET["sure"])){	
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$getFiles = mysqli_query($connect,"Select * from Files where UserID = '$userID'") ;
		while ($row = mysqli_fetch_object($getFiles)) {
			echo "<br>Deleting ".$row->Displayname ."...";
			if ($row->Filename != $row->Displayname)
			unlink($GLOBALS["Program_Dir"]."Storage/".$row->Filename);
			echo "<br>Removing database entry...";
			mysqli_query($connect,"Delete from Files where UserID = '$userID'");			
			mysqli_query($connect,"Delete from Share where UserID = '$userID'");
			echo "<br>Removing shares entries ...";
			echo "..Done";			
		}		
		mysqli_query($connect,"Delete from Users where ID = '$userID'");
		mysqli_close($connect);	
		if (isset($_SESSION) == false)
			session_start();
			//exit everything
			session_unset();
			session_destroy();
	}	
	else
	{
		echo $GLOBALS["Program_Language"]["Delete_Warning"]."<br><br>";
		echo "<a href = 'index.php?module=goodbye&sure=true'>".$GLOBALS["Program_Language"]["Delete_OK"]."</a>";
	}
?>
</div>