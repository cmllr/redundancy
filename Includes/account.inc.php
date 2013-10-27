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
<h1><?php echo $GLOBALS["Program_Language"]["User_Details"];?></h1>
<?php	
	if (isset($_SESSION) == false)
			session_start();
<<<<<<< HEAD
	ui_get_account_details($_SESSION["user_id"]);	
?>
<?php if($GLOBALS["config"]["User_Enable_Recover"] == 1 && ($_SESSION["role"] != 3 || is_guest())) :?> 
	<div class="btn-group">	
		<a type="a" href = 'index.php?module=setpass'class="btn btn-default"><span class="elusive icon-edit glyphIcon"></span><?php echo $GLOBALS["Program_Language"]["Set"]; ?></a>	
	</div>
<?php endif;?>
<h2><?php echo $GLOBALS["Program_Language"]["Files"];?></h2>
=======
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
<h2>
	<?php echo $GLOBALS["Program_Language"]["Files"];?>
</h2>
>>>>>>> Update to 1.9.11-git-beta1-r3
<div class="progress">
	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo getUsedStorage();?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getUsedStorage();?>%;">
	</div>
</div>
<?php		
	//Display percentage, the chart (if enabled) and the user settings (if enabled)
<<<<<<< HEAD
	echo "&nbsp;".fs_get_Percentage()."&nbsp;(".fs_get_Storage_Percentage().")";
=======
	echo "&nbsp;".getUsedStoragePercentage()."&nbsp;(".getUsedStorageStatus().")";
>>>>>>> Update to 1.9.11-git-beta1-r3
	//Include of the third party calls for drawing the chart
	if ($GLOBALS["config"]["Program_Enable_Chart"] == 1)
		include $GLOBALS["Program_Dir"]."Lib/jqplot.inc.php";
	if ($GLOBALS["config"]["Program_Enable_User_Settings"] == 1)
		include $GLOBALS["Program_Dir"]."Includes/settings.inc.php";	
?>
<p>
<<<<<<< HEAD
<?php if ($_SESSION["role"] != 3 && is_guest() == false):?>
	<div class="btn-group">
	<a class = 'btn btn-default' href = "index.php?module=zip&dir=/"><?php echo $GLOBALS["Program_Language"]["Download_All_Files"];?></a>
	</div>
	<?php if ($GLOBALS["config"]["User_Allow_Delete"]) :?>
		<br><br><h3><?php echo $GLOBALS["Program_Language"]["Delete_Account"]?></h3><br><a class = 'btn btn-default' href = 'index.php?module=goodbye'><?php echo $GLOBALS["Program_Language"]["Delete_Account"]; ?></a><br>
=======
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
>>>>>>> Update to 1.9.11-git-beta1-r3
	<?php endif;?>
<?php endif;?>