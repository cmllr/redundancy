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
	 * This files offeres the possibility to change user settings (if enabled)
	 * @todo dynamic loading and saving of the settings
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
	if (isset($_SESSION) == false)
			session_start();	
	if ($_SESSION["role"] == 3)
			exit;
?>
<p>
<div class="btn-group">
	<?php
		if (count($_POST) > 0){
			if (isset($_POST["User_NoLogout_Warning"]))	
				$GLOBALS["config"]["User_NoLogout_Warning"] = 1;
			else
				$GLOBALS["config"]["User_NoLogout_Warning"] = 0;	
			if (isset($_POST["Program_Display_Icons_if_needed"]))	
				$GLOBALS["config"]["Program_Display_Icons_if_needed"] = 1;
			else
				$GLOBALS["config"]["Program_Display_Icons_if_needed"] = 0;
			if (isset($_POST["Program_Enable_JQuery"]))	
				$GLOBALS["config"]["Program_Enable_JQuery"] = 1;
			else
				$GLOBALS["config"]["Program_Enable_JQuery"] = 0;
			if (isset($_POST["Program_Enable_Preview"]))	
				$GLOBALS["config"]["Program_Enable_Preview"] = 1;
			else
				$GLOBALS["config"]["Program_Enable_Preview"] = 0;	
				
			saveUserSettings();
		}
	?>
	<form method="POST" action="index.php?module=account" >
		<input type= "checkbox" name="User_NoLogout_Warning" <?php
			if ($GLOBALS["config"]["User_NoLogout_Warning"] == 1)
				echo "checked=\"true\"";	
		?>> <?php echo $GLOBALS["Program_Language"]["User_NoLogout_Warning"];?><br>
		<input type= "checkbox" name="Program_Display_Icons_if_needed"<?php
			if ($GLOBALS["config"]["Program_Display_Icons_if_needed"] == 1)
			echo "checked=\"true\"";	
		?>> <?php echo $GLOBALS["Program_Language"]["Program_Display_Icons_if_needed"];?><br>
		<input type= "checkbox" name="Program_Enable_JQuery"<?php
			if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1)
				echo "checked=\"true\"";	
		?>> <?php echo $GLOBALS["Program_Language"]["Program_Enable_JQuery"];?><br>
		<input type= "checkbox" name="Program_Enable_Preview" <?php
			if ($GLOBALS["config"]["Program_Enable_Preview"] == 1)
				echo "checked=\"true\"";	
		?>> <?php echo $GLOBALS["Program_Language"]["Program_Enable_Preview"];?><br>
		 <input class="btn btn-default" type="submit" value="<?php echo $GLOBALS["Program_Language"]["Save"]; ?>" /> 
	</form>
	</p>
	<form method="POST" action="index.php?module=setpass" >
		<input type="submit" class="btn btn-default" value="<?php echo $GLOBALS["Program_Language"]["New_Pass"]; ?>" />
	</form>
</div>