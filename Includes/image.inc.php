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
	 //Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed
	if (isset($_SESSION) == false)
		session_start();		
	//if the file parameter is set -> Get a image to display
	
//$GLOBALS["config"]["Program_Storage_Dir"] = "Storage";
	if (isset($_GET["file"]) )
	{
		//Include DataBase file
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$filename = "";
		$result = mysqli_query($connect,"Select * from Files  where Hash = '".mysqli_real_escape_string($connect,$_GET["file"])."' limit 1");	
		//echo $_SESSION["current_file"];
		while ($row = mysqli_fetch_object($result)) {
			$filename = $GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$row->Filename;
		}
		
		//Set the current file
		//echo $filename;	
		$_SESSION["current_file"] = $filename;
		//Delete database connection and display the image		
		display();
	}
	else if (isset($_SESSION["current_file"]) && $_SESSION["current_file"] != "-1"){	
		
		display();
	}
	function display()
	{
		
		//Display image if existing
		//supported are: jpeg,jpg,bmp,png (atm)
		if (file_exists($_SESSION["current_file"])){
			header('Content-Type: ' .mime_content_type($_SESSION["current_file"])); 
			$mimetype = mime_content_type($_SESSION["current_file"]);	
		
			if ($mimetype == "image/jpeg"){
				$im = imagecreatefromjpeg($_SESSION["current_file"]);	
				if (isset($_GET["t"]) && $_GET["t"] == 1)
				{
					list($width, $height) = getimagesize($_SESSION["current_file"]);
					$newimage = imagecreatetruecolor(32,32);
					imagecopyresampled($newimage,$im,0,0,0,0,32,32,$width,$height);
					imagejpeg($newimage);
					imagedestroy($newimage);
					imagedestroy($im);
				}
				else
				{
					imagejpeg($im);					
					imagedestroy($im);	
				}				
			}
			if ($mimetype == "image/bmp"){					
				$im = imagecreatefromwbmp($_SESSION["current_file"]);
				if (isset($_GET["t"]) && $_GET["t"] == 1)
				{
					list($width, $height) = getimagesize($_SESSION["current_file"]);
					$newimage = imagecreatetruecolor(32,32);
					imagecopyresampled($newimage,$im,0,0,0,0,32,32,$width,$height);
					imagewbmp($newimage);
					imagedestroy($newimage);
					imagedestroy($im);
				}
				else
				{
					imagewbmp($im);
					imagedestroy($im);		
				}				
			}
			if ($mimetype == "image/png"){		
				$im = imagecreatefrompng($_SESSION["current_file"]);
				if (isset($_GET["t"]) && $_GET["t"] == 1)
				{
					list($width, $height) = getimagesize($_SESSION["current_file"]);
					$newimage = imagecreatetruecolor(32,32);
					imagecopyresampled($newimage,$im,0,0,0,0,32,32,$width,$height);
					imagepng($newimage);
					imagedestroy($newimage);
					imagedestroy($im);
				}
				else
				{
					imagepng($im);
					imagedestroy($im);		
				}			 		
			}	
			mysqli_close($connect);				
		}
		else
			echo "error";
	}
?> 