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
		$hash = mysqli_real_escape_string($connect,$_GET["file"]);
		$result = mysqli_query($connect,"Select Filename from Files  where Hash = '".$hash."' limit 1");	
		
		while ($row = mysqli_fetch_object($result)) {
			$filename = getStoragePath().$row->Filename;
		}	
		//Set the current file		
		$_SESSION["current_file"] = $filename;
		//Display the image itself
		displayImage($_SESSION["current_file"]);
	}	
	else if (isset($_SESSION["current_file"]) && $_SESSION["current_file"] != "-1"){				
		displayImage($_SESSION["current_file"]);	
	}
	/**
	 * create an image by a full path
	 * @param $imagepath the path of the image
	 */
	function displayImage($imagepath)
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
				imagesavealpha ($im,true);
				if (isset($_GET["t"]) && $_GET["t"] == 1)
				{
					list($width, $height) = getimagesize($_SESSION["current_file"]);							
					$newimage = imagecreatetruecolor(32,32);
					imagealphablending($newimage, false);
					imagesavealpha ($newimage,true);					
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
		}		
	}		
?> 