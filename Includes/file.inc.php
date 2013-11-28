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
	//Cleanup the hash and the current user id
	$hash = mysqli_real_escape_string($connect,$_GET["file"]);
	$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
	//Search for a file with this hash
	$ergebnis = mysqli_query($connect,"Select Filename,Displayname,Uploaded,Client,Hash,MimeType,Directory,Size from Files  where Hash = '$hash' and UserID = '$userID' limit 1") or die("Error: ".mysqli_error($connect));	
	//redirect the user to an error site if the file does not exists
	if (mysqli_affected_rows($connect) ==  0)
	{			
		header("Location: index.php?module=list&dir=".$_SESSION["currentdir"]."&message=File_not_found");
		exit;
	}
	while ($row = mysqli_fetch_object($ergebnis)) {	
		//Remember file and set the current directory to the file's root if the file is an image
		//needed to show images without any parameters
		$_SESSION["currentdir"] = $row->Directory;		
		$_SESSION["current_file"] = getStoragePath().$row->Filename;
		$_SESSION["current_file_hash"] = $row->Hash;	
		//Display the folder navigation bar
		include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
		//Display the headline
		echo "<h1>".getShortenedDisplayname(getFilenameWithLowercasedExtension($row->Displayname))."</h1>";
		//The following features are only available if the user enables the preview and the file can be previewed.
		if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 &&(isImage($row->Filename) == 1 || isVideo($row->Filename) == true || isAudio($row->Filename) == true || isText($row->Filename) == true  ||isVectorGraphics($row->Filename) == true)){
			?>
			<div class = "panel panel-default">
				<div class = "panel-body">
					<?php		
					//Get file image or icon
					if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && isImage($row->Filename) == 1)
					{
						?>
							<img src='index.php?module=image' class="img-responsive" style="margin: 0 auto">	
						<?php			
					}
					else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && isVideo($row->Filename) == true)
					{
						?>
							<video class="img-responsive" style="margin: 0 auto" src='./Includes/player.inc.php' controls>Your browser does not support the <code>video</code> element.</video>
						<?php
					}			
					else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && isAudio($row->Filename) == true)
					{
						?>
							<audio  src='./Includes/player.inc.php' controls>Your browser does not support the <code>audio</code> element.</audio>
						<?php
					}
					else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && isText($row->Filename) == true) 
					{
						?>
							<textarea class="img-responsive" style="margin: 0 auto" cols='120' rows='5'><?php include "./Includes/player.inc.php";?></textarea>
						<?php
					}
					else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1 && isVectorGraphics($row->Filename) == true)
					{
						?>
							<div style="margin: 0 auto" class = "svg"><?php include "./Includes/player.inc.php";?></div>
						<?php
					}			
					else if ($GLOBALS["config"]["Program_Enable_Preview"] == 1)
					{
						?>
							<img src="<?php getImagePath($row->Displayname,$row->Filename,$row->MimeType,$row->Hash,0);?>">
						<?php
					}				
					else{
						?>
							<img  src='./Images/page.png'>
						<?php
					}
					//Display the name
					?>
				</div>
			</div>
		<?php
		}
		//Remember file properties
		$date = strtotime($row->Uploaded);
		$size = measurementCorrection($row->Size);
		$shared = isShared($hash);		
		$client = $row->Client;
		//Get the fitting description where the file was uploaded
		if (strpos($client,"Mozilla") === false && $row->Client != NULL )
			$source =  $GLOBALS["Program_Language"]["Uploaded_API"];
		else		
			$source = $GLOBALS["Program_Language"]["Uploaded_Browser"];	
		?>
			<div class = "btn-group" id = "fileActionBtnGroup">
			<?php
				//Display download link
				?>
				<a type="a" href = 'index.php?module=download&file=<?php echo $row->Hash;?>'class="btn btn-default">
					<span class="elusive icon-download-alt glyphIcon">
					</span>
					<?php echo $GLOBALS["Program_Language"]["Download"];?>
				</a>
				<?php			
				//Display sharelinks
				if ($shared == false)
				{
					?>
					<a type="a" href = 'index.php?module=share&file=<?php echo $row->Hash;?>&new=true' class="btn btn-default">
						<span class="elusive icon-share glyphIcon">
						</span>
						<?php echo $GLOBALS["Program_Language"]["Share"];?>
					</a>
					<?php
				}		
				else
				{
					$sharetext = getShareLink($hash);	
					//Display share link
					?>
					<a type="a" href = 'index.php?module=share&file=<?php echo $row->Hash;?>&delete=true' class="btn btn-default">
						<span class="elusive icon-remove-sign glyphIcon">
						</span>
						<?php echo $GLOBALS["Program_Language"]["Unshare"];?>
					</a>
					<?php 
				}
				//Display delete  and rename link	
				?>
				<a type="a" href = 'index.php?module=delete&file=<?php echo $row->Hash;?>' class="btn btn-default">
					<span class="elusive icon-trash glyphIcon">
					</span>
					<?php echo $GLOBALS["Program_Language"]["Delete"];?>
				</a>				
				<a type="a" href = 'index.php?module=rename&file=<?php echo $row->Hash;?>' class="btn btn-default">
					<span class="elusive icon-edit glyphIcon">
					</span>
					<?php echo $GLOBALS["Program_Language"]["Rename_title"];?>
				</a>
			</div>
			<?php 	
	}
	//Close the connection if finished
	mysqli_close($connect);
	//Display further properties and the sharelink, if shared.
?>
<div class="panel panel-default">
	<div class="panel-body">
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-lg-2 control-label">
				<?php echo $GLOBALS["Program_Language"]["Size"];?>
			</label>
			<div class="col-lg-8">
				<p class="form-control-static">
					<?php echo $size;?>
				</p>
			<div class="col-lg-2"></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label">
				<?php echo $GLOBALS["Program_Language"]["Source"];?>
			</label>
			<div class="col-lg-8">
				<p class="form-control-static">
					<?php echo $source;?>
				</p>
			</div>
			<div class="col-lg-2"></div>
		</div>
		<?php if (isShared($hash)):?>
		<div class="form-group">
			<label for="inputSharedLink" class="col-lg-2 control-label">
				<?php echo $GLOBALS["Program_Language"]["Share_Link"];?>
			</label>
			<div class="col-lg-8">
				<input type="text" class="form-control" id="inputSharedLink" value="<?php echo $sharetext;?>">
			</div>		
		</div>   
		<?php endif ;?>
	</form>
	</div>
</div>

