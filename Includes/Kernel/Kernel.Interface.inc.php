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
	 * This file contains function for creating the interface
	 */	
	/**
	 * ui_create_contextmenu create a JQuery based context menu
	 * @param $hashcode the hash of the file	
	 * @param $count the current number of context menus already created	
	 */
	function ui_create_contextmenu($hashcode,$count)
	{
		++$count;
		$shared = isShared(str_replace("#","",$hashcode));
		if ($shared)
			$Share_Status = "<a class = 'shared' href = 'index.php?module=share&file=".str_replace("#","",$hashcode)."&delete=true'><span class=\"elusive icon-share glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Shared"]."</a>";
		else
			$Share_Status = "<a href = 'index.php?module=share&file=".str_replace("#","",$hashcode)."&new=true'><span class=\"elusive icon-share glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Share"]."</a>";
		$folder = fs_is_Dir(str_replace("#","",$hashcode));
		echo "<ul id='context_menu$count' style='position:fixed;font-size:small;width:150px'>";
		if ($folder == true)
		{
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
			$select = "Select Displayname,Filename,Directory from Files where UserID = '$user' and Hash = '".str_replace("#","",$hashcode)."' limit 1";
			$result= mysqli_query($connect,$select);
			while ($row = mysqli_fetch_object($result)) {
				echo "<li><a href ='index.php?module=list&dir=".$row->Displayname."'><span class=\"elusive icon-folder-open glyphIcon\"></span> ".$GLOBALS["Program_Language"]["open_generic"]."</a></li>";
				echo "<li><a href ='index.php?module=delete&dir=".$row->Filename."'><span class=\"elusive icon-remove-sign glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Delete"]."</a></li>";
				echo "<li><a class = 'delete' href ='index.php?module=list&move=true&source=".$row->Filename."&old_root=".$row->Directory."'><span class=\"elusive icon-tag glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Cut"]."</a></li>";
				echo "<li><a class = 'delete' href = 'index.php?module=list&copy=true&source=".$row->Filename."&old_root=".$row->Directory."'><span class=\"elusive icon-tags glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Copy"]."</a></li>";
				echo "<li><a class = 'delete'  href ='index.php?module=rename&source=".$row->Displayname."&old_root=".$_SESSION["currentdir"]."'><span class=\"elusive icon-edit glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Rename_title"]."</a></li>";
				echo "<li><a href ='index.php?module=zip&dir=".$row->Displayname."'><span class=\"elusive icon-download-alt glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Zip"]."</a></li>";			
			}			
			mysqli_close($connect);	
		}
		else
		{
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
			$select = "Select Displayname,Filename,Directory,Hash from Files where UserID = '$user' and Hash = '".str_replace("#","",$hashcode)."' limit 1";
			$result= mysqli_query($connect,$select);
			while ($row = mysqli_fetch_object($result)) {
				echo "<li><a href ='index.php?module=file&file=".$row->Hash."'><span class=\"elusive icon-eye-open glyphIcon\"></span> ".$GLOBALS["Program_Language"]["open_generic"]."</a></li>";
				echo "<li><a href ='index.php?module=delete&file=".$row->Hash."'><span class=\"elusive icon-remove-sign glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Delete"]."</a></li>";
				echo "<li><a class = 'delete' href ='index.php?module=list&move=true&file=".$row->Hash."'><span class=\"elusive icon-tag glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Cut"]."</a></li>";
				echo "<li><a class = 'delete' href = 'index.php?module=list&copy=true&file=".$row->Hash."'><span class=\"elusive icon-tags glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Copy"]."</a></li>";
				echo "<li><a class = 'delete'  href ='index.php?module=rename&file=".$row->Hash."'><span class=\"elusive icon-file-edit glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Rename_title"]."</a></li>";
				echo "<li><a href ='index.php?module=download&file=".$row->Hash."'><span class=\"elusive icon-download-alt glyphIcon\"></span> ".$GLOBALS["Program_Language"]["Download"]."</a></li>";			
			}			
			mysqli_close($connect);	
		}
		echo "<li >$Share_Status</li></ul>";
		echo "<script>$(function() {\$('#context_menu$count').menu();\$('#context_menu$count').toggle();\$('$hashcode').bind('contextmenu', function(e){var MouseX;var MouseY;e.preventDefault();MouseX = e.clientX ;MouseY = e.clientY;$('#context_menu$count').css({'position':'fixed','top':MouseY,'left':MouseX,'z-index':'10'});\$('#context_menu$count').toggle('clip', {}, 100 );return false;});";
		echo "\$('#context_menu$count').mouseleave(function(){\$(this).hide();});});";  
		echo "</script>";
		echo "<script>
			$(\"$hashcode\").click(500, function(e){
				e.preventDefault();
			
				var MouseX;var MouseY;
				e.preventDefault();
				MouseX = e.clientX ;
				MouseY = e.clientY;
				$('#context_menu$count').css(
				{'position':'fixed','top':MouseY,'left':MouseX});
				\$('#context_menu$count').toggle('clip', {}, 100 );				
				return false;
			});
		</script>";
	}
	/**
	 * ui_get_cropped_displayname create a JQuery based context menu
	 * @param $displayname the long displayname
	 * @return a cropped displayname
	 */
	function ui_get_cropped_displayname($displayname)
	{
		$extension = explode(".",$displayname);
		$ext = $extension[count($extension)-1];
		if (strlen($displayname)  <= $GLOBALS["config"]["Program_Max_Displayname_Length"] + strlen($ext))
			return $displayname;
		else
		{
			
			return substr($displayname,0,-1*(strlen($displayname)-$GLOBALS["config"]["Program_Max_Displayname_Length"]))."[...].".strtolower($ext);
		}
	}
	/**
	 * ui_get_dirlink get the file link
	 * @param $Displayname the long displayname
	 * @param $Filename the filename (ending with .dat)
	 * @param $Filename_only the filename of the dir (only short name)
	 * @param $hash the hashcode of the file
	 * @return the displaylink for the several cases (listing, copying, moving)
	 */
	function ui_get_dirlink($Displayname,$Filename,$Filename_only,$hash)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$dirlink = "";
		$shared = "";
		$shared = isShared($hash);
		if ($shared)
			$shared = " <span class = 'label label-primary'>".$GLOBALS["Program_Language"]["Share_Title"]."</span>";
		if (isset($_GET["move"]) && isset($_GET["file"]))
			$dirlink = "<a title = '".$Displayname."'  href = 'index.php?module=move&dir=".$Displayname."&file=".mysqli_real_escape_string($connect,$_GET["file"])."'>".ui_get_cropped_displayname($Filename_only)."$shared</a>";
		else if (isset($_GET["copy"]) && isset($_GET["file"]))
			$dirlink = "<a title = '".$Displayname."' ' href = 'index.php?module=copy&dir=".$Displayname."&file=".mysqli_real_escape_string($connect,$_GET["file"])."'>".ui_get_cropped_displayname($Filename_only)."$shared</a>";
		else if (isset($_GET["move"]) && isset($_GET["source"]))
			$dirlink = "<a title = '".$Displayname."'  href = 'index.php?module=move&source=".$_GET["source"]."&target=".$Displayname."&old_root=".$_GET["old_root"]."'>".ui_get_cropped_displayname($Filename_only)."$shared</a>";
		else if (isset($_GET["copy"]) && isset($_GET["source"]))
			$dirlink = "<a title = '".$Displayname."'  href = 'index.php?module=copy&source=".$_GET["source"]."&target=".$Displayname."&old_root=".$_GET["old_root"]."'>".ui_get_cropped_displayname($Filename_only)."$shared</a>";
		else
			$dirlink = "<a title = '".$Displayname."'  href = 'index.php?module=list&dir=".$Displayname."'>".ui_get_cropped_displayname(getDisplayName($Filename_only,$Filename))."$shared</a>";
		return $dirlink;
	}
	/**
	 * ui_get_modulelink get the file module link
	 * @param $Displayname the long displayname	
	 * @return the dthe link with the several parameters for several cases
	 */
	function ui_get_modulelink($Displayname)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$modulelink = "";
		$shared = "";
	
		if (isset($_GET["move"]) && isset($_GET["file"]))
				$modulelink = "module=list&dir=".$Displayname."&move=true&file=".mysqli_real_escape_string($connect,$_GET["file"])."&dir=".$Displayname;
		else if (isset($_GET["copy"]) && isset($_GET["file"]))
				$modulelink = "module=list&dir=".$Displayname."&copy=true&file=".mysqli_real_escape_string($connect,$_GET["file"])."&dir=".$Displayname;
		else if (isset($_GET["copy"]) && isset($_GET["source"]))
				$modulelink = "module=list&dir=".$Displayname."&copy=true&source=".mysqli_real_escape_string($connect,$_GET["source"])."&old_root=".mysqli_real_escape_string($connect,$_GET["old_root"])."&target=".$Displayname;
		else if (isset($_GET["move"]) && isset($_GET["source"]))
				$modulelink = "module=list&dir=".$Displayname."&move=true&source=".mysqli_real_escape_string($connect,$_GET["source"])."&old_root=".mysqli_real_escape_string($connect,$_GET["old_root"])."&target=".$Displayname;
		return $modulelink;
	}
	/**
	 * ui_get_Styles prints a list of styles
	 * @param $stylesdir the directory containing the styles
	 */
	function ui_get_Styles($stylesdir)
	{
		$languages = scandir($stylesdir);
		echo "<select class=\"selectpicker\" id = 'Style' name = 'Style'>";
		foreach($languages as $entry) {
			if (is_file($stylesdir.$entry) && endsWith($entry,".css") && $entry == "Bootstrap.css"){
				echo "<option value='Styles/$entry'>".str_replace(".css","",$entry)."</option>";			
			}				
		}
		echo "</select>";
	}	
	/**
	 * get the share status as a link from a hashcode
	 * @param $hashcode the hashcode
	 * @return the share link or -1
	 */
	function ui_get_Share_Status($hashcode)
	{
		$shared = isShared($hashcode);	
		$Share_Status = "-1";		
		if ($shared){
			$Share_Status = str_replace(
			array("##hash","##shared"),
			array("$hashcode",$GLOBALS["Program_Language"]["Shared"]),
			$_SESSION["template"]["Status_template_shared"]
			);	}				
		else{
			$Share_Status = str_replace(
			array("##hash","##share"),
			array("$hashcode",$GLOBALS["Program_Language"]["Share"]),
			$_SESSION["template"]["Status_template_share"]
			);	
		}
		return $Share_Status;
	}
	function ui_enable_keyhooks()
	{
		if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1)
		{
			?>
			<style>
			  .ui-menu { width: 120px; }
			 </style>
			<script>
			$(document).ready(function(e){
				$('#toolbar').load("index.php?search=true");
				$('#toolbar').slideToggle();
			})
			$(document).keydown(function(e){
				  //CTRL + V keydown combo	 
				  var empty;
				  if ($('#toolbar').text() == "")
					empty = true;
					else
					empty = false;
				  if(e.ctrlKey && e.keyCode == 70){
						 e.preventDefault();			
						$('#toolbar').load("index.php?search=true");
						if (empty == false)
							$('#toolbar').slideToggle();		
						else
							$('#toolbar').Toggle();	
				  }
				  else if (e.ctrlKey && e.keyCode == 85){
				   e.preventDefault();
						$('#toolbar').load("index.php?upload=true");
						if (empty == false)
							$('#toolbar').slideToggle();		
						else
							$('#toolbar').Toggle();	
				  }
				  else if (e.ctrlKey && e.keyCode == 68){
					e.preventDefault();
						$('#toolbar').load("index.php?newdir=true");
						if (empty == false)
							$('#toolbar').slideToggle();		
						else
							$('#toolbar').Toggle();	
				  }
			})
			</script>
			<div id = "toolbar">
			<!--
			Content for JQuery features
			-->
			</div>
			<?php
		}
	}
	/**
	 * prints a title for the query
	 * @param $query the search term
	 */
	function ui_create_query_title($query)
	{
		echo "<ol class = 'breadcrumb'>";
		echo "<li ><a href= '#'><span class=\"elusive icon-search alt glyphIcon\"></span>".$GLOBALS["Program_Language"]["Search_to"]." \"".$query."\""."</a></li>";
		echo "</ol>";
	}
	/**
	 * prints a list of languages
	 * @param $languages the directory containing the languages
	 */
	function ui_get_Langs($languages)
	{
		$languages = scandir($languages);	
		foreach($languages as $entry) {
			if (endsWith($entry,".lng") ){
				echo "<option >".str_replace(".lng","",$entry)."</option>";			
			}			
		}		
	}	
	/**
	*prints a message
	*/ 
	function ui_get_messages()
	{
		if (isset($_GET["message"]) && isset($GLOBALS["Program_Language"][$_GET["message"]]))
		{		
			$message = $_GET["message"];
			$image = "./Images/error.png";			
			if (isset($_GET["img"]))
			$image = "./Images/".$_GET["img"].".png";			
			if (strpos($message,"success") === false){
			
				echo "<div class='alert alert-danger'>".$GLOBALS["Program_Language"][$message]."<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button></div>";
			}	
			else
			{
				echo "<div class='alert alert-success'>".$GLOBALS["Program_Language"][$message]."<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button></div>";
				
			}	
		}
	}
	/**
	 * prints account details by the given id
	 * @param $userid the id of the user
	 */
	function ui_get_account_details($userid)
	{	
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		//get the current user id an search for users with this id
		$id = mysqli_real_escape_string($connect,$userid);
		//Get the informations to get displayed for the user
		$result = mysqli_query($connect,"Select ID,Email,User,API_Key from Users  where ID = '$id' limit 1") or die("DataBase Error: 001 ".mysqli_error($connect));
		//Display the user informations (Email, Username, API_Key, User changes)
		while ($row = mysqli_fetch_object($result)) {
			echo "<b>".$GLOBALS["Program_Language"]["Email"].": </b> ".$row->Email;	
			echo "<br><b>".$GLOBALS["Program_Language"]["Username"].": </b> ".$row->User;
			//Display the api token 
			if ($_SESSION["role"] != 3 && is_guest() == false && $GLOBALS["config"]["Api_Enable"] == 1)
			echo"
			<div class=\"form-group\">
				<div class=\"input-group\" >
					<span class=\"input-group-addon\">API Key</span>
					<input type=\"text\" class=\"form-control\" value ='".$row->API_Key."'> 
					<span class=\"input-group-btn\">
						<button class=\"btn btn-default\" type=\"submit\"><a href = \"index.php?module=moduser&task=newtoken\"<span class=\"elusive icon-refresh glyphIcon\"></a>
					</span></button>
				</span>
				</div>
			</div>";
			echo "<h2>".$GLOBALS["Program_Language"]["Password_Management"]."</h2>";	
			echo "<h3>".$GLOBALS["Program_Language"]["Pass_Changes"]."</h2>";
			$result = mysqli_query($connect,"Select IP ,Changed from Pass_History  where Who = '".$row->ID."' limit 10") or die("DataBase Error: 001 ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {		
				echo $row->Changed." - " .$row->IP."<br>";
			}	
		}
		mysqli_close($connect);			
	}
	
?>