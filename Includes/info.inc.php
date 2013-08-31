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
	 * This file displays informations about the program
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
?> 
 <p>
	<img style='align:middle;padding:10px;width:128px' src = "./Images/Logo.png">
	<br>
<?php
	echo $GLOBALS["config"]["Program_Name_ALT"]." ".$GLOBALS["Program_Version"]."<br>";
	echo "Release: ". $GLOBALS["Program_Release"]."<br>";	
?>
<a href = "https://github.com/squarerootfury/redundancy/issues"><?php echo $GLOBALS["Program_Language"]["Bugreport"]?></a>
<br style='clear:both'>
  </p>
