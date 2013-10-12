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
	 * This file displays the file properties and a preview (if enabled)
	 */
	//Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Include database file
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	//remember the hash
	$hash = mysqli_real_escape_string($connect,$_GET["file"]);
	$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
	//Searh for a file with this hash
	$ergebnis = mysqli_query($connect,"Select Filename,Displayname,Uploaded,Client,Hash,MimeType,Directory,Size from Files  where Hash = '$hash' and UserID = '$userID' limit 1") or die("Error: ".mysqli_error($connect));	
		if (mysqli_affected_rows($connect) ==  0){			
			header("Location: index.php?module=list&dir=".$_SESSION["currentdir"]."&message=File_not_found");
	}
	while ($row = mysqli_fetch_object($ergebnis)) {		
		$_SESSION["currentdir"] = $row->Directory;
		//Remember the file for further processes
		$_SESSION["current_file"] = $GLOBALS["Program_Dir"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$row->Filename;
		$_SESSION["current_file_hash"] = $row->Hash;	
		include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
		echo "<h1>".htmlentities(fs_get_filename_lowercase_extension($row->Displayname))."</h1>";
	
		if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 &&(fs_isImage($row->Filename) == 1 || fs_isVideo($row->Filename) == true || fs_isAudio($row->Filename) == true || fs_isText($row->Filename) == true  ||fs_isVector($row->Filename) == true)){
				echo "<div class = \"panel panel-default\">";
			echo "<div class = \"panel-body\">";
			//Get file image or icon
			if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isImage($row->Filename) == 1)
				echo "<img src='index.php?module=image' class=\"img-responsive\" style=\"margin: 0 auto\">";	
			else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isVideo($row->Filename) == true)
				echo "<video class=\"img-responsive\" style=\"margin: 0 auto\" src='./Includes/player.inc.php' controls>Your browser does not support the <code>video</code> element.</video>";
			else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isAudio($row->Filename) == true)
				echo "<audio  src='./Includes/player.inc.php' controls>Your browser does not support the <code>audio</code> element.</audio>";
			else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isText($row->Filename) == true) {
				echo "<textarea class=\"img-responsive\" style=\"margin: 0 auto\" cols='120' rows='5'>";
				include "./Includes/player.inc.php";
				echo "</textarea>";	}
			else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && fs_isVector($row->Filename) == true)
				{echo "<div style=\"margin: 0 auto\" class = \"svg\">";
				include "./Includes/player.inc.php";
				echo "</div>"; }			
			else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1)
				echo "<img  src='".fs_get_imagepath($row->Displayname,$row->Filename,$row->MimeType,$row->Hash,0)."'>";
			else
				echo "<img  src='./Images/page.png'>";
			//Display the name
			echo "</div>";	
				echo "</div>";
		}
		$date = strtotime($row->Uploaded);
		$size = fs_get_fitting_DisplayStyle($row->Size);
		
		//Display the client, a browser is identified by the "Mozilla" entry in the user agent
		$client = $row->Client;
		if (strpos($client,"Mozilla") === false && $row->Client != NULL )
			$source =  $GLOBALS["Program_Language"]["Uploaded_API"];
		else		
			$source = $GLOBALS["Program_Language"]["Uploaded_Browser"];	
	
		echo "<div class = \"btn-group\" id = \"fileActionBtnGroup\">";
		if (isShared($hash))
		{
			$sharetext = fs_getShareLink($hash);		
		}	
		echo "<a type=\"a\" href = 'index.php?module=download&file=$row->Hash'class=\"btn btn-default\"><span class=\"elusive icon-download-alt glyphIcon\"></span>".$GLOBALS["Program_Language"]["Download"]."</a>";
			
		//Display links
		if (isShared($hash) == false)
			echo "<a type=\"a\" href = 'index.php?module=download&module=share&file=".$row->Hash."&new=true'class=\"btn btn-default\"><span class=\"elusive icon-share glyphIcon\"></span>".$GLOBALS["Program_Language"]["Share"]."</a>";
		else
				echo "<a type=\"a\" href = 'index.php?module=share&file=".$row->Hash."&delete=true'class=\"btn btn-default\"><span class=\"elusive icon-remove-sign glyphIcon\"></span>".$GLOBALS["Program_Language"]["Unshare"]."</a>";
		//Display delete link	
		echo "<a type=\"a\" href = 'index.php?module=delete&file=$row->Hash'class=\"btn btn-default\"><span class=\"elusive icon-trash glyphIcon\"></span>".$GLOBALS["Program_Language"]["Delete"]."</a>";
		echo "<a type=\"a\" href = 'index.php?module=rename&file=$row->Hash'class=\"btn btn-default\"><span class=\"elusive icon-edit glyphIcon\"></span>".$GLOBALS["Program_Language"]["Rename_title"]."</a>";
		echo "</div>";
		
	}
	//Close the connection if finished
	mysqli_close($connect);


?>
<div class="panel panel-default">
  <div class="panel-body">
	<form class="form-horizontal" role="form">
   <div class="form-group">
    <label class="col-lg-2 control-label"><?php echo $GLOBALS["Program_Language"]["Size"];?></label>
    <div class="col-lg-8">
      <p class="form-control-static"><?php echo $size;?></p>
	  	<div class="col-lg-2"></div>
</div>
</div>
  <div class="form-group">
    <label class="col-lg-2 control-label"><?php echo $GLOBALS["Program_Language"]["Source"];?></label>
    <div class="col-lg-8">
      <p class="form-control-static"><?php echo $source;?></p>
    </div>
	<div class="col-lg-2"></div>
  </div>
  <?php if (isShared($hash)):?>
  <div class="form-group">
    <label for="inputSharedLink" class="col-lg-2 control-label"><?php echo $GLOBALS["Program_Language"]["Share_Link"];?></label>
    <div class="col-lg-8">
      <input type="text" class="form-control" id="inputSharedLink" placeholder="Freigabelink" value="<?php echo $sharetext;?>">
    </div>		
  </div>   
  <?php endif ;?>
</form>
</div>
</div>

