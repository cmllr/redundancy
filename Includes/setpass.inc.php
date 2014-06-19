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
	exit;
}
if (isset($_POST["newpass"])){	
	if ($_POST["newpass"] == $_POST["newpass_repeat"] && $GLOBALS["config"]["User_Enable_Recover"] == 1)
		setNewPassword($_SESSION["user_name"],$_POST["pass"],$_POST["newpass"]);
	else{
		header("Location: ./index.php?module=setpass&message=setpass_fail");
		exit;
	}
}
?>
<div class="panel-body">
<form class="form-horizontal" method="POST" action="index.php?module=setpass" id="login">	
	<div class="form-group">
		<label for="pass" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Old_Pass"];?></label>
		<div class="col-lg-9">
			<input type="password" class="form-control" id="pass" name="pass" placeholder="<?php echo $GLOBALS["Program_Language"]["Old_Pass"];?>">
		</div>
	</div>
	
	<div class="form-group">
		<label for="newpass" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["New_Pass"];?></label>
		<div class="col-lg-9">
			<input type="password" class="form-control" id="newpass" name="newpass" placeholder="<?php echo $GLOBALS["Program_Language"]["New_Pass"];?>">
		</div>
	</div>
	<div class="form-group">
		<label for="newpass_repeat" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["New_Pass_Repeat"];?></label>
		<div class="col-lg-9">
			<input type="password" class="form-control" id="newpass_repeat" name="newpass_repeat" placeholder="<?php echo $GLOBALS["Program_Language"]["New_Pass_Repeat"];?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<button type="submit" class="btn btn-default btn-block"><?php echo $GLOBALS["Program_Language"]["Set"];?></button>
		</div>
	</div>
</form>
</div>