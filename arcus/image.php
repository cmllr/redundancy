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

	$filePath =$_GET["i"];
	displayImage("../Storage/".$filePath);
	/**
	 * create an image by a full path
	 * @param $imagepath the path of the image
	 */
	function displayImage($imagepath)
	{			
		//Display image if existing
		//supported are: jpeg,jpg,bmp,png (atm)
		if (file_exists($imagepath)){	
			$file = file_get_contents($imagepath);
			$finfo = new finfo(FILEINFO_MIME_TYPE);		
			$mimetype = $finfo->buffer($file);	
			header('Content-Type: ' .$mimetype);			
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
	}		
?> 