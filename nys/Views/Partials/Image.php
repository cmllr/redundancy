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
	if (!isset($_SESSION))
		session_start();
	$filePath =$_SESSION["fileInject"];
	displayImage($filePath);
	/**
	 * create an image by a full path
	 * @param $imagepath the path of the image
	 */
	function displayImage($imagepath)
	{			
		//Display image if existing
		//supported are: jpeg,jpg,bmp,png (atm)
		if (file_exists($imagepath)){		
			header('Content-Type: ' .mime_content_type($imagepath));		
			$mimetype = mime_content_type($imagepath);		
			if ($mimetype == "image/jpeg"){
				$im = imagecreatefromjpeg($imagepath);					
				imagejpeg($im);					
				imagedestroy($im);									
			}
			if ($mimetype == "image/bmp"){					
				$im = imagecreatefromwbmp($imagepath);				
				imagewbmp($im);
				imagedestroy($im);										
			}
			if ($mimetype == "image/png"){		
				$im = imagecreatefrompng($imagepath);
				imagesavealpha ($im,true);				
				imagepng($im);
				imagedestroy($im);								 		
			}			
		}	
		unset($_SESSION["fileInject"]);
	}		
?> 