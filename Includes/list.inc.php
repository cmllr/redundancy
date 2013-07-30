  <style>
  .ui-menu { width: 120px; }
  </style>
<?php	
	//start session if needed
	if (isset($_SESSION) == false)
			session_start();
	/*
		Step 1 -> Determine the wanted directory. 	
	*/
	if (isset($_GET["dir"])){
		$dir = $_GET["dir"];
		$_SESSION["currentdir"]	= $dir;	
	}
	else
		$dir = $_SESSION["currentdir"];
	//Includes DataBase and broadcrumbs and DataBase
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
	//$i is needed to alternate the css for each line
	$i = 0;
	
	$id = getDirectoryID(mysqli_real_escape_string($connect,$dir));
	
	if (!isset($_SESSION["user_logged_in"]))
	{
		//Changed: The user is not allowed to display files if he is not logged in
		exit;		
	}	
	else
	{
		//Determine the user ID and run the statement.
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$result = mysqli_query($connect,"Select Uploaded,Displayname,Filename,Hash,MimeType,Filename_only,Size,Directory from Files  where UserID = '$user' and Directory_ID ='".$id."' and Bin = 0") or die("Error: 014 ".mysqli_error());
	}
	if (isset($_GET["move"]) || isset($_GET["copy"]))
		echo "<h3 id = 'copyhint' >".$GLOBALS["Program_Language"]["Paste_Description"]." <a href = 'index.php?module=list'>".$GLOBALS["Program_Language"]["Abort"]."</a></h3><br>";
	if (isset($_GET["file"]))
		$fileToCopyOrToMove = mysqli_real_escape_string($connect,$_GET["file"]);
	if (isset($_GET["source"]))
		$fileToCopyOrToMove = mysqli_real_escape_string($connect,$_GET["source"]);
	if (mysqli_affected_rows($connect) > 0){
		//Only start the table if we have files in there.	
		if ($GLOBALS["config"]["Program_Enable_Action_Buttons"] == 1)
		{		
			if (isset($fileToCopyOrToMove))
				echo "<table id = 'filetable'><tr><th></th><th>".$GLOBALS["Program_Language"]["Name"]."</th><th>".$GLOBALS["Program_Language"]["Uploaded"]."</th><th>".$GLOBALS["Program_Language"]["Size"]."</th><th>".$GLOBALS["Program_Language"]["Actions"]."</th></tr>";
			else
				echo "<table id = 'filetable'><tr><th></th><th>".$GLOBALS["Program_Language"]["Name"]."</th><th>".$GLOBALS["Program_Language"]["Uploaded"]."</th><th>".$GLOBALS["Program_Language"]["Size"]."</th><th>".$GLOBALS["Program_Language"]["Actions"]."</th><th>".$GLOBALS["Program_Language"]["Share_Status"]."</th></tr>";
		}			
		else
		{
			if (isset($fileToCopyOrToMove))
				echo "<table id = 'filetable'><tr><th></th><th>".$GLOBALS["Program_Language"]["Name"]."</th><th>".$GLOBALS["Program_Language"]["Uploaded"]."</th><th>".$GLOBALS["Program_Language"]["Size"]."</th><th>".$GLOBALS["Program_Language"]["Actions"]."</th></tr>";
			else
				echo "<table id = 'filetable'><tr><th></th><th>".$GLOBALS["Program_Language"]["Name"]."</th><th>".$GLOBALS["Program_Language"]["Uploaded"]."</th><th>".$GLOBALS["Program_Language"]["Size"]."</th><th>".$GLOBALS["Program_Language"]["Share_Status"]."</th></tr>";
		}
		
	}
<<<<<<< HEAD
	else
		echo "<br>";
	/*
		Case: Management of files when the user tries to copy or to move.
		Remembering of the needed file for later.
	*/

	/*
		Display the filelist
	*/
=======
	if (isset($_GET["file"]))
		$fileToCopyOrToMove = mysqli_real_escape_string($connect,$_GET["file"]);
	if (isset($_GET["source"]))
		$fileToCopyOrToMove = mysqli_real_escape_string($connect,$_GET["source"]);
	//Display the resultss
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
	while ($row = mysqli_fetch_object($result)) {
		$date = strtotime($row->Uploaded);		
		
		$Share_Status = "";	
		/*
			Determine which icon should be used;
		*/		
		
<<<<<<< HEAD
		$imagepath = fs_get_imagepath($row->Displayname,$row->Filename,$row->MimeType,$row->Hash);
		/*
			Get the share status
		*/
=======
			//Default image path, only change if the file is a image and the config allows it
			$imagepath = './Images/page.png';		
				
			if (isImage($row->Filename) && $GLOBALS["config"]["Program_Display_Icons_if_needed"] == 1)
			{
				$imagepath = "index.php?module=image&thumb=true&file=".$row->Hash;
			}	
			else if (isImage($row->Filename) == false )
			{			
				if (file_exists("./Images/mimetypes/".str_replace("/","-",$row->MimeType).".png"))
						$imagepath = "./Images/mimetypes/".str_replace("/","-",$row->MimeType).".png";	
			}		
		}
		else
		{
			$imagepath = "./Images/mimetypes/folder.png";
			if (isShared($row->Hash))
				$imagepath = "./Images/mimetypes/folder-publicshare.png";
		}
	
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
		$shared = isShared($row->Hash);
		
		if ($shared)
			$Share_Status = "<a class = 'shared' href = 'index.php?module=share&file=".$row->Hash."&delete=true'>".$GLOBALS["Program_Language"]["Shared"]."</a>";
		else
			$Share_Status = "<a href = 'index.php?module=share&file=".$row->Hash."&new=true'>".$GLOBALS["Program_Language"]["Share"]."</a>";
		if (isset($fileToCopyOrToMove))
			$Share_Status = "";
		//include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		
<<<<<<< HEAD
		/*
			Display filelinks for the different cases (regular, move file, copy file, move dir, copy dir)
		*/
		/*if (isset($_GET["move"]) && isset($_GET["file"]))
			$dirlink = "<a title = ".$row->Displayname." class = 'filelink' href = 'index.php?module=move&dir=".$row->Displayname."&file=".mysqli_real_escape_string($connect,$_GET["file"])."'>".ui_get_cropped_displayname($row->Filename_only)."</a>";
=======
	
		if (isset($_GET["move"]) && isset($_GET["file"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=move&dir=".$row->Displayname."&file=".mysqli_real_escape_string($connect,$_GET["file"])."'>".$row->Filename_only."</a>";
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
		else if (isset($_GET["copy"]) && isset($_GET["file"]))
			$dirlink = "<a title = ".$row->Displayname." class = 'filelink' href = 'index.php?module=copy&dir=".$row->Displayname."&file=".mysqli_real_escape_string($connect,$_GET["file"])."'>".ui_get_cropped_displayname($row->Filename_only)."</a>";
		else if (isset($_GET["move"]) && isset($_GET["source"]))
			$dirlink = "<a title = ".$row->Displayname." class = 'filelink' href = 'index.php?module=move&source=".$_GET["source"]."&target=".$row->Displayname."&old_root=".$_GET["old_root"]."'>".ui_get_cropped_displayname($row->Filename_only)."</a>";
		else if (isset($_GET["copy"]) && isset($_GET["source"]))
			$dirlink = "<a title = ".$row->Displayname." class = 'filelink' href = 'index.php?module=copy&source=".$_GET["source"]."&target=".$row->Displayname."&old_root=".$_GET["old_root"]."'>".ui_get_cropped_displayname($row->Filename_only)."</a>";
		else
<<<<<<< HEAD
			$dirlink = "<a title = ".$row->Displayname." class = 'filelink' href = 'index.php?module=list&dir=".$row->Displayname."'>".ui_get_cropped_displayname(getDisplayName($row->Filename_only,$row->Filename))."</a>";
		*/
		$dirlink = ui_get_dirlink($row->Displayname,$row->Filename,$row->Filename_only);
		/*
			$i is needed to alternate the row colors;
		*/
=======
			$dirlink = "<a class = 'filelink' href = 'index.php?module=list&dir=".$row->Displayname."'>".getDisplayName($row->Filename_only,$row->Filename)."</a>";
		
		
		//This is needed to allow alternating colors
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
		if ($i%2 == 0 )
			$suffix = 0;
		else
			$suffix = 1;		
		$i++;		
	
		/*
			Display several kinds of links for cases like copy file, move filey, copy dir, move dir and the regular display
		*/
		$modulelink = ui_get_modulelink($row->Displayname);
		/*if (isset($_GET["move"]) && isset($_GET["file"]))
				$modulelink = "module=list&dir=".$row->Displayname."&move=true&file=".mysqli_real_escape_string($connect,$_GET["file"])."&dir=".$row->Displayname;
		else if (isset($_GET["copy"]) && isset($_GET["file"]))
				$modulelink = "module=list&dir=".$row->Displayname."&copy=true&file=".mysqli_real_escape_string($connect,$_GET["file"])."&dir=".$row->Displayname;
		else if (isset($_GET["copy"]) && isset($_GET["source"]))
				$modulelink = "module=list&dir=".$row->Displayname."&copy=true&source=".mysqli_real_escape_string($connect,$_GET["source"])."&old_root=".mysqli_real_escape_string($connect,$_GET["old_root"])."&target=".$row->Displayname;
		else if (isset($_GET["move"]) && isset($_GET["source"]))
				$modulelink = "module=list&dir=".$row->Displayname."&move=true&source=".mysqli_real_escape_string($connect,$_GET["source"])."&old_root=".mysqli_real_escape_string($connect,$_GET["old_root"])."&target=".$row->Displayname;
		*/
		if ($row->Displayname == $row->Filename)
		{
<<<<<<< HEAD
			echo "<tr class = 'filetype$suffix'><td><img  src='$imagepath'></td><td   id = ".$row->Hash." >$dirlink</td><td>".date("j.n.Y H:i",$date)."</td><td class ='size'>".fs_get_fitting_DisplayStyle(getDirectorySize($row->Displayname))."</td>";
			if ( isset($_SESSION["user_logged_in"])&& isset($_GET["move"]) == false && isset($_GET["copy"]) == false)
			{
				if ($GLOBALS["config"]["Program_Enable_Action_Buttons"] == 1)
					echo "<td class =  'actions' ><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Delete"]."' href ='index.php?module=delete&dir=".$row->Filename."'><img  src = './Images/folder_delete.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Cut"]."' href ='index.php?module=list&move=true&source=".$row->Filename."&old_root=".$row->Directory."'><img  src = './Images/cut_red.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Copy"]."' href = 'index.php?module=list&copy=true&source=".$row->Filename."&old_root=".$row->Directory."'><img src= './Images/page_copy.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Rename_title"]."' href ='index.php?module=rename&source=".$row->Displayname."&old_root=".$_SESSION["currentdir"]."'><img  src = './Images/textfield_rename.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Zip"]."' href ='index.php?module=zip&dir=".$row->Displayname."'><img  src = './Images/page_white_zip.png'></a>";
			}
			else if (isset($_SESSION["user_logged_in"]) )
=======
			echo "<tr class = 'filetype$suffix'><td><img  src='$imagepath'></td><td   id = ".$row->Hash." >$dirlink</td><td>".date("j.n.Y H:i",$date)."</td><td class ='size'>".getFittingDisplayStlye(getDirectorySize($row->Displayname))."</td><td class =  'actions' >";
			if (isset($_SESSION["user_logged_in"])&& isset($_GET["move"]) == false && isset($_GET["copy"]) == false)
				echo "<a class = 'delete' title = '".$GLOBALS["Program_Language"]["Delete"]."' href ='index.php?module=delete&dir=".$row->Filename."'><img  src = './Images/folder_delete.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Cut"]."' href ='index.php?module=list&move=true&source=".$row->Filename."&old_root=".$row->Directory."'><img  src = './Images/cut_red.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Copy"]."' href = 'index.php?module=list&copy=true&source=".$row->Filename."&old_root=".$row->Directory."'><img src= './Images/page_copy.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Rename_title"]."' href ='index.php?module=rename&source=".$row->Displayname."&old_root=".$_SESSION["currentdir"]."'><img  src = './Images/textfield_rename.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Zip"]."' href ='index.php?module=zip&dir=".$row->Displayname."'><img  src = './Images/page_white_zip.png'></a>";
			else if (isset($_SESSION["user_logged_in"]))
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
			{
					echo "<td class =  'actions' ><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Open"]."' href ='index.php?$modulelink'><img  src = './Images/door_open.png'></a>";
			}
			echo "</td><td>$Share_Status";
			if ($shared)
				echo "<a href ='".fs_getShareLink($row->Hash)."'><img src = './Images/link_go.png' alt='link' title='".$GLOBALS["Program_Language"]["Share_Link"]."'></a>";
			echo "</td></tr>";
			if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1)
				ui_create_contextmenu("#".$row->Hash,$i);
		}			
		else
		{
<<<<<<< HEAD
			 echo "<tr class = 'filetype$suffix'><td><img src='$imagepath'></td><td  id = ".$row->Hash." ><a class = 'filelink' title = ".$row->Displayname." href = 'index.php?module=file&file=".$row->Hash."'>".htmlentities(utf8_decode(ui_get_cropped_displayname($row->Displayname)))."</a></td><td>".date("j.n.Y H:i",$date)."</td><td class ='size'>".fs_get_fitting_DisplayStyle($row->Size)."</td>";
			if (isset($_SESSION["user_logged_in"]) && $GLOBALS["config"]["Program_Enable_Action_Buttons"] == 1 && isset($fileToCopyOrToMove) == false)
=======
			 echo "<tr class = 'filetype$suffix'><td><img src='$imagepath'></td><td  id = ".$row->Hash." ><a class = 'filelink' href = 'index.php?module=file&file=".$row->Hash."'>".htmlentities(utf8_decode($row->Displayname))."</a></td><td>".date("j.n.Y H:i",$date)."</td><td class ='size'>".getFittingDisplayStlye($row->Size)."</td>";
			if (isset($_SESSION["user_logged_in"]) )
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
				echo "<td class =  'actions' ><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Delete"]."' href ='index.php?module=delete&file=".$row->Hash."'><img  src = './Images/page_delete.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Cut"]."' href ='index.php?module=list&move=true&file=".$row->Hash."'><img  src = './Images/cut_red.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Copy"]."' href ='index.php?module=list&copy=true&file=".$row->Hash."'><img  src = './Images/page_copy.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Rename_title"]."' href ='index.php?module=rename&file=".$row->Hash."'><img  src = './Images/textfield_rename.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Download"]."' href ='index.php?module=download&file=".$row->Hash."'><img  src = './Images/arrow_down.png'></a>";
				
			echo "</td><td>$Share_Status";
			if ($shared)
<<<<<<< HEAD
				echo "<a href ='".fs_getShareLink($row->Hash)."'><img src = './Images/link_go.png' alt='link' title='".$GLOBALS["Program_Language"]["Share_Link"]."'></a>";
=======
				echo "<a href ='".getShareLink($row->Hash)."'><img src = './Images/link_go.png' alt='link' title='".$GLOBALS["Program_Language"]["Share_Link"]."'></a>";
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
			echo "</td></tr>";		
			if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1)
				ui_create_contextmenu("#".$row->Hash,$i);
		}
			
	}
	
	/*
		Display the individual filelinks
	*/
	if (isset($_GET["move"])    && isset($_GET["file"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&dir=".$_SESSION["currentdir"]."&file=".$fileToCopyOrToMove."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
	else if ( isset($_GET["copy"])  && isset($_GET["file"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&dir=".$_SESSION["currentdir"]."&file=".$fileToCopyOrToMove."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
	else if (isset($_GET["move"])  && isset($_GET["source"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&target=".$_SESSION["currentdir"]."&source=".$fileToCopyOrToMove."&old_root=".$_GET["old_root"]."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
	else if (isset($_GET["copy"]) && isset($_GET["source"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&target=".$_SESSION["currentdir"]."&source=".$fileToCopyOrToMove."&old_root=".$_GET["old_root"]."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
	//Close the table
	echo "</table>";	
	//Display the delete link if we are not in HOME ("/")
	if ($_SESSION["currentdir"] != "/" && isset($_GET["share"]) == false)
		echo "<a class = 'diractions' href ='index.php?module=delete&dir=".$_SESSION['currentdir']."'>".$GLOBALS["Program_Language"]["Delete_Folder"]."</a>";
<<<<<<< HEAD

=======
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
?>