<div class ="contentWrapper">
<script>
$(document).ready(function(){
  $("#expander1_content").hide()
  <?php if (isset($_POST["username_info"]) == false): ?>
  $("#expander2_content").hide()  
  <?php endif; ?>
  $("#expander3_content").hide()
  $('#expander1').click(function(){
		$("#expander1_content").slideToggle(200); 
    });	
	$('#expander2').click(function(){
		$("#expander2_content").slideToggle(200); 
    });		
	$('#expander3').click(function(){
		$("#expander3_content").slideToggle(200); 
    });	
});
</script>
<form method="POST" action="index.php?module=admin" >
<a id = "expander1" href ="#">System status</a>
<div id = "expander1_content">
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
	 * This file provides the administration panel
	 */
	//Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed		
		
	if (isset($_SESSION) == false)
			session_start();	
	//TODO: Implement admin check
	if ($_SESSION["role"] == 0 && is_admin() && $GLOBALS["config"]["Program_Enable_Web_Administration"] == 1)
	{
		echo "<h2>".$GLOBALS["Program_Language"]["Admin_Panel"]."</h2><br>";
		include "health.inc.php";
		$snapshotcount = 0;
		$last = "-";
		if ($handle = opendir($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/")) {
			while (false !== ($file = readdir($handle))) {			
				if ($file != "." && $file != ".." && endsWith($file,".zip") )
				{		
					$snapshotcount++;
					$last = date ("F d Y H:i:s", filemtime($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/".$file));
				}
			}		
		}
		closedir($handle);	
		if ($snapshotcount == 0)
			echo "<img src = './Images/error.png'>0 Snapshots<br>";
		else
			echo "<img src = './Images/accept.png'>".$snapshotcount ." Snapshot(s) (".$last.")<br>";	
		echo "<br><hr><img src = './Images/accept.png' alt = 'not ok'>Feature needed <b>or</b> security relevant feature is disabled<br>";
		echo "<img src = './Images/information.png' alt = 'not ok'>Security relevant feature is enabled<br>";
		echo "<img src = './Images/error.png' alt = 'not ok'>Possible security problem<br>";
		echo "<img src = './Images/exclamation.png' alt = 'not ok'>Problem<br>";
		echo "<br><hr><br><a href = '?module=snapshot'>Run Snapshot</a>";
	}		
	else
	{
		echo "You don't have the rights to access this page or the web interface is disabled";
		exit;
	}
	echo "<br>";
	echo "</div>";
?>
<br> 
<a id = "expander2" href ="#">User management</a>
<div id ="expander2_content">
<?php if (isset($_POST["username_info"])): ?>

<?php
	
		if (isExisting("",$_POST["username_info"]) == false)
		{
			echo "No such user";
			exit;
		}		
		if (isset($_POST["role"]) == false )
		{			
			echo "<input type=\"text\" name=\"username_info\" value=\"".$_POST["username_info"]."\" READONLY/><br>";
			echo "<p>".$GLOBALS["Program_Language"]["role"];
			echo " <input type=\"radio\" name=\"role\" value=\"0\"";
			if (user_get_role($_POST["username_info"]) == 0)
				echo "CHECKED />";
			else
				echo "/>";
			echo "Administrator ";
			echo "<input type=\"radio\" name=\"role\" value=\"1\"";
			if (user_get_role($_POST["username_info"]) == 1)
				echo "CHECKED />";
			else
				echo "/>";
			echo "User ";
			echo "<input type=\"radio\" name=\"role\" value=\"3\"";
			if (user_get_role($_POST["username_info"]) == 3)
				echo "CHECKED />";
			else
				echo "/>";
			echo "Guest ";	
			echo "</p>";
		}
		if (isset($_POST["role"]) && $_POST["delete"] != "Yes")
		{				
			user_save_administration();			
		}			
?>
  <?php echo $GLOBALS["Program_Language"]["Size"];?>
 <input name="storage" value="<?php
	echo user_get_storage($_POST["username_info"]);
  ?>"/>
  Minimum: <?php echo fs_get_fitting_DisplayStyle(round(getUsedSpace($_POST["username_info"]),0,PHP_ROUND_HALF_UP));?>
<br>
    <?php echo $GLOBALS["Program_Language"]["New_Pass"];?> <input name="user_new_pass">
<?php
	echo $GLOBALS["Program_Language"]["pass_hint"].": ".getRandomPass($GLOBALS["config"]["User_Recover_Password_Length"]);  
?>
<br>
<input type=submit name=submit value="<?php echo $GLOBALS["Program_Language"]["Save"];?>">
<br>
<a id = "expander3" href ="#">Dangerious!</a>
<div id = "expander3_content">
<form method="POST" action="index.php?module=moduser&task=delete&user=<?php echo $_POST["username_info"]?>">
<?php echo $GLOBALS["Program_Language"]["user_delete_admin"];?><input type="submit" value="<?php echo $GLOBALS["Program_Language"]["Delete"]; ?>" />
</form>
</div>
</div>
</form>
<?php endif; ?>
<?php if (isset($_POST["username_info"]) == false): ?>
<form method="POST" action="index.php?module=admin" align = "center">
<tag>Username</tag> <input name="username_info">
<input type=submit name=submit value="<?php echo $GLOBALS["Program_Language"]["get_user_info"];?>">
</form>
<?php endif; ?>
</div>
