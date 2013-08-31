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
	 * The register process and dialog is stored in this file
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
if (isset($_POST["regemail"])){
	
	if (isset($_SESSION) == false)
			session_start();	

	if (registerUser($_POST["regemail"],$_POST["regpass"],$_POST["regpass_repeat"]) == true)
		header("Location: ./index.php");
	else
		header("Location: ./index.php?module=register");
}
if ($GLOBALS["config"]["Enable_register"] == 0 && isset($_GET["renew"]) == false){
	echo "<form id = 'login'><p>".$GLOBALS["Program_Language"]["Register_disabled"]."</p>";
	echo "<a class = 'actions' href = 'index.php'><img src='./Images/arrow_left.png'><?php".$GLOBALS["Program_Language"]["Back"].";?></a>";
	exit;
}
?>
<form method="POST" action="index.php?module=register&renew=true" id="login">
<p>
    <label for="regemail"><?php echo $GLOBALS["Program_Language"]["Email"];?></label>
    <input class ="text" name="regemail" />
</p>
<p>
    <label for="regpass"><?php echo $GLOBALS["Program_Language"]["Password"];?></label>
    <input class ="text"  name="regpass" type="password" />
</p>
<p>
    <label for="regpass_repeat"><?php echo $GLOBALS["Program_Language"]["Repeat_Password"];?></label>
    <input class ="text"  name="regpass_repeat" type="password" />
</p>
<p class="loginSubmit">
    <input type="submit" value="<?php echo $GLOBALS["Program_Language"]["Register"];?>" />
</p>
<a class = "actions" href = "index.php"><img src="./Images/arrow_left.png"><?php echo $GLOBALS["Program_Language"]["Back"];?></a>
</form>
