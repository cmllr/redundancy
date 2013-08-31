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
	 * This file contains the menu of Redundancy.
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
?>
<ul>
<li>
<img title="<?php echo $GLOBALS["config"]["Program_Name_ALT"]." ".$GLOBALS["Program_Version"];?>" id='imagelogo' src = "./Images/Logo_notext.png">
</li>
<li>
<a href = "index.php">
<img src = "./Images/house.png">
<?php
if (isset($_SESSION) == false)
		session_start();
	echo $GLOBALS["Program_Language"]["Home"];
?>
</a> 
</li>
<li><a  href = "index.php?module=list"><img src = "./Images/folder_user.png"><?php echo $GLOBALS["Program_Language"]["Files"]; ?></a></li>
<li><a href = "index.php?module=upload"><img src = "./Images/add.png"><?php echo $GLOBALS["Program_Language"]["Upload"]; ?></a></li>
<li><a  href = "index.php?module=createdir"><img src = "./Images/folder_add.png"><?php echo $GLOBALS["Program_Language"]["New_Directory_Short"]; ?></a></li>
<?php
	if ($GLOBALS["config"]["Program_Enable_JQuery"] == 0 || isset($_SESSION["no_js"]) == true)
		echo "<li><a  href = \"index.php?module=search\"><img src = \"./Images/folder_magnify.png\">".$GLOBALS["Program_Language"]["Search"]."</a></li>";
?>

</ul>