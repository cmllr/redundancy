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
	 * If the user sets a new password, it will be done using the dialog of this file.
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
if ($GLOBALS["config"]["User_Enable_Recover"] == 0)
{
	header("Location: index.php?message=recover_off_fail");
}
if (isset($_POST["regemail"])){	
	if ($_POST["newpass"] == $_POST["newpass_repeat"] && $GLOBALS["config"]["User_Enable_Recover"] == 1)
		setNewPassword($_POST["regemail"],$_POST["pass"],$_POST["newpass"]);
	}	
?>
<form method="POST" action="index.php?module=setpass" id="login">
<p>
    <label for="regemail"><?php echo $GLOBALS["Program_Language"]["Email"];?></label>
    <input class ="text" name="regemail" />
</p>
<p>
    <label for="pass"><?php echo $GLOBALS["Program_Language"]["Old_Pass"];?></label>
    <input class ="text"  name="pass" type="password" />
</p>
<p>
    <label for="newpass"><?php echo $GLOBALS["Program_Language"]["New_Pass"];?></label>
    <input class ="text"  name="newpass" type="password" />
</p>
<p>
    <label for="newpass_repeat"><?php echo $GLOBALS["Program_Language"]["New_Pass_Repeat"];?></label>
    <input class ="text"  name="newpass_repeat" type="password" />
</p>
<p class="loginSubmit">
    <input type="submit" value="<?php echo $GLOBALS["Program_Language"]["Set"];?>" />
</p>
</form>
