<form method="POST" action="index.php?module=recover" id="login">
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
	 * The recover process and dialog is stored in this file
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
if (isset($_POST["email"])){
	
	if (isset($_SESSION) == false)
			session_start();	
	
	recover($_POST["email"]);
}
if (isset($_GET["msg"]) && $_GET["msg"] == "success")
{
	echo "<h2>".$GLOBALS["Program_Language"]["recovered"]."</h2>";
	exit;
}
?>

<p>
    <label for="email"><?php echo $GLOBALS["Program_Language"]["Email"]; ?></label>
    <input class ="text" name="email" />
</p>
<p class="loginSubmit">
    <input type="submit" value="<?php echo $GLOBALS["Program_Language"]["Recover"]; ?>" />
</p>
<a class = "actions" href = "index.php"><img src="./Images/arrow_left.png"><?php echo $GLOBALS["Program_Language"]["Back"];?></a>
</form>