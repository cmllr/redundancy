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
	//Exit the page if the user is not logged in	
	if (!isset($_SESSION["user_logged_in"]))
	{
		header("Location: index.php");
		exit;		
	}
	/*
		Enable keyhooks for using the keyboard shortcuts (like STRG+F, STRG+D ...)
	*/
	if (isset($GLOBALS["config"]["Program_Enable_KeyHooks"]) && $GLOBALS["config"]["Program_Enable_KeyHooks"] == 1)
		enableKeyHooks();		
	/*
		Step 1 -> Determine the wanted directory. 
		From $_GET parameter or given by session value
	*/
	if (isset($_GET["dir"])){
		$dir = $_GET["dir"];
		//Redirect the user to the current folder if the folder does not exists.
		if (isFolderExisting($dir) == false)
		{		
			header("Location: index.php?module=list&dir=".$_SESSION["currentdir"]."&message=Dir_not_found");
		}
		$_SESSION["currentdir"]	= $dir;	
	}
	else
		$dir = $_SESSION["currentdir"];
	
	//Includes DataBase file to create a database connection
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	
	if (isset($_POST["searchquery"]) == false){
		include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";	
		$id = getDirectoryID(mysqli_real_escape_string($connect,$dir));
		$hashcode = getHashByFile(mysqli_real_escape_string($connect,$_SESSION["currentdir"]));
		if (isShared($hashcode))
		{
			$sharetext = getShareLink($hashcode);		
		}
	}	
	else{		
		createQueryTitle($_POST["searchquery"]);
	}
	//$i is needed to alternate the css for each line
	$i = 0;		
	//get the User
	$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
	//Determine the user ID and run the statement.
	$current = -1;
	$start = -1;
	$end = -1;
	$count = -1;
	
	if (isset($_POST["searchquery"]) == false){
		$query = "Select Uploaded,Displayname,Filename,Hash,MimeType,Filename_only,Size,Directory from Files  where UserID = $user and Directory_ID ='".$id."'";
		if ($GLOBALS["config"]["Program_Enable_Pagination"] == 1 ){
			mysqli_query($connect,$query)  or die("Error: 014 ".mysqli_error($connect));
			$count = mysqli_affected_rows($connect);			
		}
		if ($_SESSION["currentdir"] == "/")
			$query .= "UNION ALL 
			Select Uploaded,Displayname,Filename,Hash,MimeType,Filename_only,Size,Directory 
			from Files 		
			inner join LocalShare ls on ls.FileID = Files.ID where ls.TargetUser = '$user'";
		if (isset($_GET["current"]) && isset($_GET["start"]) && isset($_GET["end"]))
		{
			if (!isset($_GET["move"]) &&  !isset($_GET["copy"])){
				$current = mysqli_real_escape_string($connect,$_GET["current"]);
				$start = mysqli_real_escape_string($connect,$_GET["start"]);
				$end = $GLOBALS["config"]["Program_Pagination_Count"];			
				$query .= " limit $start, $end";
			}
		}
		else if ($GLOBALS["config"]["Program_Enable_Pagination"] == 1){
			if (!isset($_GET["move"]) &&  !isset($_GET["copy"])){
				$current = 0;
				$start = 0;
				$end = 	$GLOBALS["config"]["Program_Pagination_Count"];		
				$query .= " limit $start, $end";	
			}			
		}
		$result = mysqli_query($connect,$query) or die("Error: 014 ".mysqli_error($connect));
		
	}
	else
	{
		$search = mysqli_real_escape_string($connect,$_POST["searchquery"]);
		$result = mysqli_query($connect,"Select Uploaded,Displayname,Filename,Hash,MimeType,Filename_only,Size,Directory from Files  where UserID = $user and ((Displayname like '%$search%' and Filename_only IS NULL ) or (Filename_only like'%$search%' and Directory not like '%$search%'))") or die("Error: 014 ".mysqli_error($connect));
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
		$GLOBALS["template"]["Copy_Hint"]
		);
	}
	//Only start the table if we have files in there.	
	if (mysqli_affected_rows($connect) > 0){		
		$header = str_replace(array("##def","##name"),
		array($GLOBALS["template"]["Table_Definition"],$GLOBALS["Program_Language"]["Name"]),
		$GLOBALS["template"]["Table_Definition_Additional"]
		);			
		if (isset($search) == false){				
			if ($GLOBALS["template"]["Uploaded"] == true){
				$header	= $header. str_replace(
				array("##uploaded"),
				array($GLOBALS["Program_Language"]["Uploaded"]),
				$GLOBALS["template"]["Uploaded_template_header"]
				);
			}
			if ($GLOBALS["template"]["Size"] == true){
				$header	= $header. str_replace(
				array("##size"),
				array($GLOBALS["Program_Language"]["Size"]),
				$GLOBALS["template"]["Size_template_header"]
				);
			}
			if ($GLOBALS["template"]["Actions"] == true){
				$header	= $header. str_replace(
				array("##actions"),
				array($GLOBALS["Program_Language"]["Actions"]),
				$GLOBALS["template"]["Actions_template_header"]
				);
			}				
			if (isset($fileToCopyOrToMove) == false  && $GLOBALS["template"]["Status"] == true){				
				$header	= $header. str_replace(
				array("##status"),
				array($GLOBALS["Program_Language"]["Share_Status"]),
				$GLOBALS["template"]["Status_template_header"]
				);
			}
			echo $header;		
		 }
		 else
		 {
			if ($GLOBALS["template"]["Uploaded"] == true){
				$header	= $header. str_replace(
				array("##uploaded"),
				array($GLOBALS["Program_Language"]["Uploaded"]),
				$GLOBALS["template"]["Uploaded_template_header"]
				);
			}
			if ($GLOBALS["template"]["Size"] == true){
				$header	= $header. str_replace(
				array("##size"),
				array($GLOBALS["Program_Language"]["Size"]),
				$GLOBALS["template"]["Size_template_header"]
				);
			}
			echo $header;
		} 	
		echo "</tr>";
	}
	else{
		echo "
			<div class='alert alert-info alert-dismissable'>
			  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
				".$GLOBALS["Program_Language"]["Dir_Empty"]."
			</div>
		";	
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
		$imagepath = getImagePath($row->Displayname,$row->Filename,$row->MimeType,$row->Hash,1);
		/*
			Get the share status
		*/
		$shared = isShared($row->Hash);	
		/*
			Get fitting text for the share status
		*/
		$Share_Status = getShareStatus($row->Hash);
		if (isset($fileToCopyOrToMove))
			$Share_Status = "";			
		/*
			Display filelinks for the different cases (regular, move file, copy file, move dir, copy dir)
		*/			
		$dirlink = getDirectoryHyperlink($row->Displayname,$row->Filename,$row->Filename_only,$row->Hash);
		
		
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
		$modulelink = getFileHyperlink($row->Displayname);
		/*
			Create the table content, based if it's a folder or a file
		*/
		if ($row->Displayname == $row->Filename)
		{		
			echo str_replace("##suffix",$suffix,$GLOBALS["template"]["Table_Item_Definition"]);
			echo str_replace(
				array("##imagepath","##hash","##dirlink","##uploaded","##size"),
				array($imagepath,$row->Hash,$dirlink,date("d.m.y H:i:s",$date),measurementCorrection(getDirectorySize($row->Displayname))),
				$GLOBALS["template"]["Table_Item_template"]
				);
			if ( isset($_GET["move"]) == false && isset($_GET["copy"]) == false && isset($search) == false)
			{
				if ($GLOBALS["config"]["Program_Enable_Action_Buttons"] == 1 && $GLOBALS["template"]["Actions"] == true || $GLOBALS["config"]["Program_Enable_JQuery"] == 0)
				{
					echo str_replace(
						array("##delete","##filename","##cut","##filename","##directory","##copy","##rename","##displayname","##currentdir","##download"),
						array($GLOBALS["Program_Language"]["Delete"],$row->Filename,$GLOBALS["Program_Language"]["Cut"],$row->Filename,$row->Directory,$GLOBALS["Program_Language"]["Copy"],$GLOBALS["Program_Language"]["Rename_Button"],$row->Displayname,$_SESSION["currentdir"],$GLOBALS["Program_Language"]["Download"]),
						$GLOBALS["template"]["Actions_template_folder"]
					);
				}			
			}
			else if ( isset($search) == false)
			{
				if (isset($_GET["source"]) && $_GET["source"] !=  $row->Displayname){
					echo str_replace(
							array("##open","##modulelink"),
							array($GLOBALS["Program_Language"]["Open"],$modulelink),
							$GLOBALS["template"]["Table_Item_Search_template"]
					);
				}
				else if (isset($_GET["source"]) == false)
				{
					echo str_replace(
							array("##open","##modulelink"),
							array($GLOBALS["Program_Language"]["Open"],$modulelink),
							$GLOBALS["template"]["Table_Item_Search_template"]
					);
				}
			}			
		}			
		else 
		{			
			echo str_replace("##suffix",$suffix,$GLOBALS["template"]["Table_File_Definition"]);		
			$shared = isShared($row->Hash);
			if ($shared == false){					
				$shared = isLocalSharedAnyUser($row->Hash,$_SESSION["user_id"]);
			}
			
			if ($shared)
				$shared = " <span class = 'label label-primary'>".$GLOBALS["Program_Language"]["Share_Title"]."</span>";
			echo str_replace(
				array("##imagepath","##hash","##directory","##displayname","##hash","##croppeddisplayname","##uploaded","##size"),
				array($imagepath,$row->Hash,$row->Directory,$row->Displayname,$row->Hash,htmlentities((getShortenedDisplayname(getFilenameWithLowercasedExtension($row->Displayname)))).$shared,date("d.m.y H:i:s",$date),measurementCorrection($row->Size)),
				$GLOBALS["template"]["Table_File_template"]
				);
			if (isset($_SESSION["user_logged_in"]) && $GLOBALS["config"]["Program_Enable_Action_Buttons"] == 1 && isset($fileToCopyOrToMove) == false  && isset($search) == false && $GLOBALS["template"]["Actions"] == true)
			{				
				echo str_replace(
					array("##delete","##hash","##cut","##copy","##rename","##download"),
					array($GLOBALS["Program_Language"]["Delete"],$row->Hash,$GLOBALS["Program_Language"]["Cut"],$GLOBALS["Program_Language"]["Copy"],$GLOBALS["Program_Language"]["Rename_title"],$GLOBALS["Program_Language"]["Download"]),
					$GLOBALS["template"]["Actions_template_file"]
				);
			}				
		}
		if (isset($search) == false && $GLOBALS["template"]["Status"] == true)
			echo "</td><td>$Share_Status";	
		echo "</td></tr>";		
		if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1)
			createContextMenu("#".$row->Hash,$i);	
	}	
	/*
		Display the individual filelinks
	*/
	if (isset($_GET["move"])    && isset($_GET["file"])){		
		echo str_replace(
			array("##suffix","##currentdir","##fileToCopyOrToMove","##Paste_Home"),
			array($suffix,$_SESSION["currentdir"],$fileToCopyOrToMove,$GLOBALS["Program_Language"]["Paste_Home"]),
			$GLOBALS["template"]["Move_file"]
		);
	}
	else if ( isset($_GET["copy"])  && isset($_GET["file"])){	
		echo str_replace(
			array("##suffix","##currentdir","##fileToCopyOrToMove","##Paste_Home"),
			array($suffix,$_SESSION["currentdir"],$fileToCopyOrToMove,$GLOBALS["Program_Language"]["Paste_Home"]),
			$GLOBALS["template"]["Copy_file"]
		);
	}		
	else if (isset($_GET["move"])  && isset($_GET["source"])){
		echo str_replace(
			array("##suffix","##currentdir","##fileToCopyOrToMove","##old_root","##Paste_Home"),
			array($suffix,$_SESSION["currentdir"],$fileToCopyOrToMove,$_GET["old_root"],$GLOBALS["Program_Language"]["Paste_Home"]),
			$GLOBALS["template"]["Move_folder"]
		);
	}	
	else if (isset($_GET["copy"]) && isset($_GET["source"])){
		echo str_replace(
			array("##suffix","##currentdir","##fileToCopyOrToMove","##old_root","##Paste_Home"),
			array($suffix,$_SESSION["currentdir"],$fileToCopyOrToMove,$_GET["old_root"],$GLOBALS["Program_Language"]["Paste_Home"]),
			$GLOBALS["template"]["Copy_folder"]
		);
	}	
	//Close the table
	echo "</table>";	
	//Display the delete link
	if (isset($_POST["searchquery"]) == false)
	echo str_replace(
		array("##currentdir","##Delete_Folder"),
		array($_SESSION["currentdir"],$GLOBALS["Program_Language"]["Delete_Folder"]),
		$GLOBALS["template"]["Delete_folder"]
	);
	echo  "<a type=\"a\" href = 'index.php?module=createdir' class=\"btn btn-default\">
					<span class=\"elusive icon-folder glyphIcon\"></span><span class='hidden-xs'>".$GLOBALS["Program_Language"]["New_Directory_Short"]."
				</span></a>";				
	echo str_replace(
		array("##Manage_shares"),
		array($GLOBALS["Program_Language"]["Manage_shares"]),
		$GLOBALS["template"]["Manage_shares"]
	);
	if ($_SESSION["currentdir"] != "/" && isset($hashcode) && isShared($hashcode) && isset($_POST["searchquery"]) == false){
		if (isShared($hashcode))
		{
			$sharetext = getShareLink($hashcode);		
		}		
		echo "<a type=\"a\" href = 'index.php?module=share&file=".$hashcode."&delete=true'class=\"btn btn-default\"><span class=\"elusive icon-remove-sign glyphIcon\"></span>".$GLOBALS["Program_Language"]["Unshare"]."</a></div>";
	}		
	else if ($_SESSION["currentdir"] != "/" &&  isset($hashcode) && isShared($hashcode) == false && isset($_POST["searchquery"]) == false)
	{
		if (isShared($hashcode) == false)
			echo "<a type=\"a\" href = 'index.php?module=download&module=share&file=".$hashcode."&new=true'class=\"btn btn-default\"><span class=\"elusive icon-share glyphIcon\"></span><span class='hidden-xs'>".$GLOBALS["Program_Language"]["Share"]."</span></a></div>";
	}	
	else if ($_SESSION["currentdir"] == "/" && !isset($_POST["searchquery"]))
	{	
		echo "</div>";
	}
?>
<?php if (isset($hashcode) && isShared($hashcode) && isset($_POST["searchquery"]) == false):?>
	<div class="panel panel-default">
		<div class="panel-body">
			<form class="form-horizontal" role="form">  
				<div class="form-group">
					<label for="inputSharedLink" class="col-lg-2 control-label">
						<?php echo $GLOBALS["Program_Language"]["Share_Link"];?>
					</label>
					<div class="col-lg-8">
						<input type="text" class="form-control" id="inputSharedLink" value="<?php echo $sharetext;?>">
					</div>		
				</div>
			</form>
		</div>
	</div>
<?php endif ;?>
<?php if (isset($_POST["searchquery"]) == false && $GLOBALS["config"]["Program_Enable_Pagination"] == 1  && $start != -1 && $end != -1):?>
<form class="form-horizontal" role="form">  	
	<?php getPagination($current,$GLOBALS["config"]["Program_Pagination_Count"],$count,$_SESSION["currentdir"]); ?>
</form>
<?php endif ;?>