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
	 * This file creates the menu and sidebar of the program.
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
?>
<div id = "sidebar" <?php if ($GLOBALS["config"]["Program_Enable_JQuery"] == 0) :?>style="visibility:hidden;"<?php endif; ?>>
<div id = "progressbar">
<?php
	echo "<div id = 'progressbar_inner' style='width:".round(fs_get_Percentage(),0)."% ;'>";
	echo "<p>&nbsp;".fs_get_Percentage()."&nbsp;".$GLOBALS["Program_Language"]["used"]."</p<>";
?>
</div>
</div>
<p>	
<?php
	echo fs_get_Storage_Percentage();
?>
</p>
<a href = "index.php?module=account"><img src ="./Images/user_orange.png"><?php echo $GLOBALS["Program_Language"]["My_Account"]; ?></a>
<?php
	if (isset($_SESSION) == false)
		session_start();
	if ($_SESSION["role"] == 0)
		echo "<a href = 'index.php?module=admin'><img src ='./Images/group_gear.png'>".$GLOBALS["Program_Language"]["Administration"]."</a>";
?>
<a href = "index.php?module=info"><img src ="./Images/information.png">Info</a>
<a href = "index.php?module=logout"><img src ="./Images/user_go.png"><?php echo $GLOBALS["Program_Language"]["Exit"]; ?></a>
</div>