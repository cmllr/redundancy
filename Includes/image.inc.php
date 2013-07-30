<?php		
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
		mysqli_close($connect);	
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
			//TODO: Add thumbnail function
			if ($mimetype == "image/jpeg"){
				$im = imagecreatefromjpeg($_SESSION["current_file"]);				
				imagejpeg($im);			
				imagedestroy($im);				
			}
			if ($mimetype == "image/bmp"){					
				$im = imagecreatefromwbmp($_SESSION["current_file"]);
				imagewbmp($im);
				imagedestroy($im);					
			}
			if ($mimetype == "image/png"){		
				$im = imagecreatefrompng($_SESSION["current_file"]);	
				imagepng($im);
				imagedestroy($im);				 		
			}		
		}
		else
			echo "error";
	}
?> 