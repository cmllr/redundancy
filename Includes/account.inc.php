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
	 * This file represents the user account dialog.
	 * The file is only valid when the user is already logged in
	 */	
	require_once ("checkuri.inc.php");
?>
<h1 class="hidden-xs"><?php echo $GLOBALS["Program_Language"]["User_Details"];?></h1>
<h3 class="visible-xs"><?php echo $GLOBALS["Program_Language"]["User_Details"];?></h3>
<?php	
	if (isset($_SESSION) == false)
			session_start();
	getAccountDetails($_SESSION["user_id"]);	
?>
<?php if($GLOBALS["config"]["User_Enable_Recover"] == 1 && ($_SESSION["role"] != 3 || isGuest())) :?> 
	<div class="btn-group">	
		<a type="a" href = 'index.php?module=setpass'class="btn btn-default">
			<span class="elusive icon-edit glyphIcon">
			</span>
			<?php echo $GLOBALS["Program_Language"]["Set"]; ?>
		</a>	
	</div>
<?php endif;?>
<?php		
	if ($GLOBALS["config"]["Program_Enable_User_Settings"] == 1)
		include $GLOBALS["Program_Dir"]."Includes/settings.inc.php";	
?>
</br>
<?php if ($_SESSION["role"] != 3 && isGuest() == false):?>
	<div class="btn-group">
		<a class = 'btn btn-default' href = "index.php?module=zip&dir=/">
			<?php echo $GLOBALS["Program_Language"]["Download_All_Files"];?>
		</a>
	</div>
	<?php if ($GLOBALS["config"]["User_Allow_Delete"]) :?>
		<br><br>
		<h3>
			<?php echo $GLOBALS["Program_Language"]["Delete_Account"]?>
		</h3>
		<br>
		<a class = 'btn btn-default' href = 'index.php?module=goodbye'>
			<?php echo $GLOBALS["Program_Language"]["Delete_Account"]; ?>
		</a>
		<br>
	<?php endif;?>
<?php endif;?>
