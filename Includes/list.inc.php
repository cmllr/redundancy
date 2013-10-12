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
	 * This file is the core feature of the program. It displays the files of the user.
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
?>
<?php
	//start session if needed
	if (isset($_SESSION) == false)
			session_start();
	/*
		Enable keyhooks for using the keyboard shortcuts
	*/
	ui_enable_keyhooks();		
	/*
		Step 1 -> Determine the wanted directory. 	
	*/
	if (isset($_GET["dir"])){
		$dir = $_GET["dir"];
		if (fs_dir_exists($dir) == false)
		{		
			header("Location: index.php?module=list&dir=".$_SESSION["currentdir"]."&message=Dir_not_found");
		}
		$_SESSION["currentdir"]	= $dir;	
	}
	else
		$dir = $_SESSION["currentdir"];
	
	//Includes DataBase and broadcrumbs and DataBase
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	
	if (isset($_POST["searchquery"]) == false){
		include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";	
		$id = getDirectoryID(mysqli_real_escape_string($connect,$dir));
		$hashcode = getHashByFile(mysqli_real_escape_string($connect,$_SESSION["currentdir"]));
		if (isShared($hashcode))
		{
			$sharetext = fs_getShareLink($hashcode);		
		}
	}	
	else{		
		ui_create_query_title($_POST["searchquery"]);
	}
	//$i is needed to alternate the css for each line
	$i = 0;
	
	
	if (!isset($_SESSION["user_logged_in"]))
	{
		header("Location: index.php");
		exit;		
	}	
	else
	{
		//Determine the user ID and run the statement.
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);			
		if (isset($_POST["searchquery"]) == false)
			$result = mysqli_query($connect,"Select Uploaded,Displayname,Filename,Hash,MimeType,Filename_only,Size,Directory from Files  where UserID = $user and Directory_ID ='".$id."'") or die("Error: 014 ".mysqli_error($connect));
		else
		{
			$search = mysqli_real_escape_string($connect,$_POST["searchquery"]);
			$result = mysqli_query($connect,"Select Uploaded,Displayname,Filename,Hash,MimeType,Filename_only,Size,Directory from Files  where UserID = $user and ((Displayname like '%$search%' and Filename_only IS NULL ) or (Filename_only like'%$search%' and Directory not like '%$search%'))") or die("Error: 014 ".mysqli_error($connect));
		}
	}
	
	/*
		Case: Management of files when the user tries to copy or to move.
		Remembering of the needed file for later.
	*/
	if (isset($_GET["file"]))
		$fileToCopyOrToMove = mysqli_real_escape_string($connect,$_GET["file"]);
	if (isset($_GET["source"]))
		$fileToCopyOrToMove = mysqli_real_escape_string($connect,$_GET["source"]);
	if (isset($_GET["move"]) || isset($_GET["copy"]))
	{
		echo str_replace(array("##Paste_Description","##Abort"),
		array($GLOBALS["Program_Language"]["Paste_Description"],$GLOBALS["Program_Language"]["Abort"]),
		$_SESSION["template"]["Copy_Hint"]
		);
	}
	
	//echo "<h3 id = 'copyhint' >".$GLOBALS["Program_Language"]["Paste_Description"]." <a href = 'index.php?module=list'>".$GLOBALS["Program_Language"]["Abort"]."</a></h3><br>";
	
	if (mysqli_affected_rows($connect) > 0){
		//Only start the table if we have files in there.	
		$header = str_replace(array("##def","##name"),
		array($_SESSION["template"]["Table_Definition"],$GLOBALS["Program_Language"]["Name"]),
		$_SESSION["template"]["Table_Definition_Additional"]
		);			
		if (isset($search) == false){				
			if ($_SESSION["template"]["Uploaded"] == true){
				$header	= $header. str_replace(
				array("##uploaded"),
				array($GLOBALS["Program_Language"]["Uploaded"]),
				$_SESSION["template"]["Uploaded_template_header"]
				);
			}
			if ($_SESSION["template"]["Size"] == true){
				$header	= $header. str_replace(
				array("##size"),
				array($GLOBALS["Program_Language"]["Size"]),
				$_SESSION["template"]["Size_template_header"]
				);
			}
			if ($_SESSION["template"]["Actions"] == true){
				$header	= $header. str_replace(
				array("##actions"),
				array($GLOBALS["Program_Language"]["Actions"]),
				$_SESSION["template"]["Actions_template_header"]
				);
			}				
			if (isset($fileToCopyOrToMove) == false  && $_SESSION["template"]["Status"] == true){				
				$header	= $header. str_replace(
				array("##status"),
				array($GLOBALS["Program_Language"]["Share_Status"]),
				$_SESSION["template"]["Status_template_header"]
				);
			}
			echo $header;		
		 }
		 else
		 {
			if ($_SESSION["template"]["Uploaded"] == true){
				$header	= $header. str_replace(
				array("##uploaded"),
				array($GLOBALS["Program_Language"]["Uploaded"]),
				$_SESSION["template"]["Uploaded_template_header"]
				);
			}
			if ($_SESSION["template"]["Size"] == true){
				$header	= $header. str_replace(
				array("##size"),
				array($GLOBALS["Program_Language"]["Size"]),
				$_SESSION["template"]["Size_template_header"]
				);
			}
			echo $header;
		} 	
		echo "</tr>";
	}
	else{
		echo "<br>";
		$emptyFolder = true;
	}
	/*
		Display the filelist
	*/
	while ($row = mysqli_fetch_object($result)) {
		$date = strtotime($row->Uploaded);			
		/*
			Determine which icon should be used;
		*/				
		$imagepath = fs_get_imagepath($row->Displayname,$row->Filename,$row->MimeType,$row->Hash,1);
		/*
			Get the share status
		*/
		$shared = isShared($row->Hash);	
	
		$Share_Status = ui_get_Share_Status($row->Hash);
		if (isset($fileToCopyOrToMove))
			$Share_Status = "";			
		/*
			Display filelinks for the different cases (regular, move file, copy file, move dir, copy dir)
		*/		
		$dirlink = ui_get_dirlink($row->Displayname,$row->Filename,$row->Filename_only,$row->Hash);
		/*
			$i is needed to alternate the row colors;
		*/
		if ($i%2 == 0 )
			$suffix = 0;
		else
			$suffix = 1;		
		$i++;		
		/*
			Display several kinds of links for cases like copy file, move filey, copy dir, move dir and the regular display
		*/
		$modulelink = ui_get_modulelink($row->Displayname);
		
		if ($row->Displayname == $row->Filename)
		{
			//echo "<tr class = 'filetype$suffix'><td><img  src='$imagepath'></td><td   id = ".$row->Hash." >$dirlink</td><td>".date("d.m.y H:i:s",$date)."</td><td class ='size'>".fs_get_fitting_DisplayStyle(getDirectorySize($row->Displayname))."</td>";
			echo str_replace("##suffix",$suffix,$_SESSION["template"]["Table_Item_Definition"]);
			echo str_replace(
				array("##imagepath","##hash","##dirlink","##uploaded","##size"),
				array($imagepath,$row->Hash,$dirlink,date("d.m.y H:i:s",$date),fs_get_fitting_DisplayStyle(getDirectorySize($row->Displayname))),
				$_SESSION["template"]["Table_Item_template"]
				);
			if ( isset($_GET["move"]) == false && isset($_GET["copy"]) == false && isset($search) == false)
			{
				if ($GLOBALS["config"]["Program_Enable_Action_Buttons"] == 1 && $_SESSION["template"]["Actions"] == true || $GLOBALS["config"]["Program_Enable_JQuery"] == 0)
				{
					echo str_replace(
						array("##delete","##filename","##cut","##filename","##directory","##copy","##rename","##displayname","##currentdir","##download"),
						array($GLOBALS["Program_Language"]["Delete"],$row->Filename,$GLOBALS["Program_Language"]["Cut"],$row->Filename,$row->Directory,$GLOBALS["Program_Language"]["Copy"],$GLOBALS["Program_Language"]["Rename_Button"],$row->Displayname,$_SESSION["currentdir"],$GLOBALS["Program_Language"]["Download"]),
						$_SESSION["template"]["Actions_template_folder"]
					);
				}				
				//echo "<td class =  'actions' ><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Delete"]."' href ='index.php?module=delete&dir=".$row->Filename."'><img  src = './Images/folder_delete.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Cut"]."' href ='index.php?module=list&move=true&source=".$row->Filename."&old_root=".$row->Directory."'><img  src = './Images/cut_red.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Copy"]."' href = 'index.php?module=list&copy=true&source=".$row->Filename."&old_root=".$row->Directory."'><img src= './Images/page_copy.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Rename_title"]."' href ='index.php?module=rename&source=".$row->Displayname."&old_root=".$_SESSION["currentdir"]."'><img  src = './Images/textfield_rename.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Download"]."' href ='index.php?module=zip&dir=".$row->Displayname."'><img  src = './Images/arrow_down.png'></a>";
			}
			else if ( isset($search) == false)
			{
				echo str_replace(
						array("##open","##modulelink"),
						array($GLOBALS["Program_Language"]["Open"],$modulelink),
						$_SESSION["template"]["Table_Item_Search_template"]
				);				
				//echo "<td class =  'actions' ><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Open"]."' href ='index.php?$modulelink'><img  src = './Images/door_open.png'></a>";
			}			
		}			
		else
		{
			//echo "<tr class = 'filetype$suffix'><td><img src='$imagepath'></td><td  id = ".$row->Hash." ><a class = 'filelink' title = '".$row->Directory.$row->Displayname."' href = 'index.php?module=file&file=".$row->Hash."'>".htmlentities(utf8_decode(ui_get_cropped_displayname($row->Displayname)))."</a></td><td>".date("d.m.y H:i:s",$date)."</td><td class ='size'>".fs_get_fitting_DisplayStyle($row->Size)."</td>";
			echo str_replace("##suffix",$suffix,$_SESSION["template"]["Table_File_Definition"]);		
			$shared = isShared($row->Hash);
			if ($shared)
				$shared = " <span class = 'label label-primary'>".$GLOBALS["Program_Language"]["Share_Title"]."</span>";
			echo str_replace(
				array("##imagepath","##hash","##directory","##displayname","##hash","##croppeddisplayname","##uploaded","##size"),
				array($imagepath,$row->Hash,$row->Directory,$row->Displayname,$row->Hash,htmlentities((ui_get_cropped_displayname(fs_get_filename_lowercase_extension($row->Displayname)))).$shared,date("d.m.y H:i:s",$date),fs_get_fitting_DisplayStyle($row->Size)),
				$_SESSION["template"]["Table_File_template"]
				);
			if (isset($_SESSION["user_logged_in"]) && $GLOBALS["config"]["Program_Enable_Action_Buttons"] == 1 && isset($fileToCopyOrToMove) == false  && isset($search) == false && $_SESSION["template"]["Actions"] == true)
			{
				
				echo str_replace(
					array("##delete","##hash","##cut","##copy","##rename","##download"),
					array($GLOBALS["Program_Language"]["Delete"],$row->Hash,$GLOBALS["Program_Language"]["Cut"],$GLOBALS["Program_Language"]["Copy"],$GLOBALS["Program_Language"]["Rename_title"],$GLOBALS["Program_Language"]["Download"]),
					$_SESSION["template"]["Actions_template_file"]
				);
			}				
			 //rename.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Download"]."' href ='index.php?module=download&file=".$row->Hash."'><img  src = './Images/arrow_down.png'></a>";
		}
		if (isset($search) == false && $_SESSION["template"]["Status"] == true)
			echo "</td><td>$Share_Status";
		/*if ($shared && !isset($fileToCopyOrToMove) && isset($search) == false){		
			echo str_replace(
					array("##link","##Share_Link"),
					array(fs_getShareLink($row->Hash),$GLOBALS["Program_Language"]["Share_Link"]),
					$_SESSION["template"]["Share_Link"]
			);
		}*/
			//echo "<a href ='".fs_getShareLink($row->Hash)."'><img src = './Images/link_go.png' alt='link' title='".$GLOBALS["Program_Language"]["Share_Link"]."'></a>";
		echo "</td></tr>";		
		if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1)
			ui_create_contextmenu("#".$row->Hash,$i);	
	}	
	/*
		Display the individual filelinks
	*/
	if (isset($_GET["move"])    && isset($_GET["file"])){
		//	echo "<tr class = 'filetype$suffix'><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&dir=".$_SESSION["currentdir"]."&file=".$fileToCopyOrToMove."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
		echo str_replace(
			array("##suffix","##currentdir","##fileToCopyOrToMove","##Paste_Home"),
			array($suffix,$_SESSION["currentdir"],$fileToCopyOrToMove,$GLOBALS["Program_Language"]["Paste_Home"]),
			$_SESSION["template"]["Move_file"]
		);
	}
	else if ( isset($_GET["copy"])  && isset($_GET["file"])){
		//echo "<tr class = 'filetype$suffix'><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&dir=".$_SESSION["currentdir"]."&file=".$fileToCopyOrToMove."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
		echo str_replace(
			array("##suffix","##currentdir","##fileToCopyOrToMove","##Paste_Home"),
			array($suffix,$_SESSION["currentdir"],$fileToCopyOrToMove,$GLOBALS["Program_Language"]["Paste_Home"]),
			$_SESSION["template"]["Copy_file"]
		);
	}		
	else if (isset($_GET["move"])  && isset($_GET["source"])){
		echo str_replace(
			array("##suffix","##currentdir","##fileToCopyOrToMove","##old_root","##Paste_Home"),
			array($suffix,$_SESSION["currentdir"],$fileToCopyOrToMove,$_GET["old_root"],$GLOBALS["Program_Language"]["Paste_Home"]),
			$_SESSION["template"]["Move_folder"]
		);
	}
	//echo "<tr class = 'filetype$suffix'><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&target=".$_SESSION["currentdir"]."&source=".$fileToCopyOrToMove."&old_root=".$_GET["old_root"]."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
	else if (isset($_GET["copy"]) && isset($_GET["source"])){
		echo str_replace(
			array("##suffix","##currentdir","##fileToCopyOrToMove","##old_root","##Paste_Home"),
			array($suffix,$_SESSION["currentdir"],$fileToCopyOrToMove,$_GET["old_root"],$GLOBALS["Program_Language"]["Paste_Home"]),
			$_SESSION["template"]["Copy_folder"]
		);
	}
	//echo "<tr class = 'filetype$suffix'><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&target=".$_SESSION["currentdir"]."&source=".$fileToCopyOrToMove."&old_root=".$_GET["old_root"]."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
	//Close the table
	echo "</table>";	
	//Display the delete link
	if (isset($_POST["searchquery"]) == false)
	echo str_replace(
		array("##currentdir","##Delete_Folder"),
		array($_SESSION["currentdir"],$GLOBALS["Program_Language"]["Delete_Folder"]),
		$_SESSION["template"]["Delete_folder"]
	);
	//echo "<a class = 'diractions' href ='index.php?module=delete&dir=".$_SESSION['currentdir']."'>".$GLOBALS["Program_Language"]["Delete_Folder"]."</a>";
	if (isset($hashcode) && isShared($hashcode) && isset($_POST["searchquery"]) == false){
		if (isShared($hashcode))
		{
			$sharetext = fs_getShareLink($hashcode);		
		}		
		echo "<a type=\"a\" href = 'index.php?module=share&file=".$hashcode."&delete=true'class=\"btn btn-default\"><span class=\"elusive icon-remove-sign glyphIcon\"></span>".$GLOBALS["Program_Language"]["Unshare"]."</a></div>";
	}	
	
	else if (isset($hashcode) && isShared($hashcode) == false && isset($_POST["searchquery"]) == false)
	{
		if (isShared($hashcode) == false)
			echo "<a type=\"a\" href = 'index.php?module=download&module=share&file=".$hashcode."&new=true'class=\"btn btn-default\"><span class=\"elusive icon-share glyphIcon\"></span>".$GLOBALS["Program_Language"]["Share"]."</a></div>";
	}
	
?>

<?php if (isset($hashcode) && isShared($hashcode) && isset($_POST["searchquery"]) == false):?>
<div class="panel panel-default">
  <div class="panel-body">
	<form class="form-horizontal" role="form">  
  <div class="form-group">
    <label for="inputSharedLink" class="col-lg-2 control-label"><?php echo $GLOBALS["Program_Language"]["Share_Link"];?></label>
    <div class="col-lg-8">
      <input type="text" class="form-control" id="inputSharedLink" value="<?php echo $sharetext;?>">
    </div>		
  </div>
</form>
</div>
</div>
<?php endif ;?>