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
	 * This file offeres the login functionality
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
//Only proceed if a post param named user is iset
if (isset($_POST["user"])){
	//start a session if needed.
	if (isset($_SESSION) == false)
		session_start();		
	//Login and/or redirect the user
	$redir = "";
	if ($GLOBALS["config"]["Enable"] != 1 ) 
	{
		$redir = "?module=admin";
	}
	if (login($_POST["user"],$_POST["pass"]) == true){
		$_SESSION["style"] = $_POST["Style"];
		if ($_SESSION["Session_Closed"] == 1 )
			header('Location: ./index.php'.$redir);
		else if ($GLOBALS["config"]["User_NoLogout_Warning"] == 1 && $_SESSION["Session_Closed"] == 0)
			header("Location: ./index.php?message=session");	
		else 
			header("Location: ./index.php");	
	}else
		header('Location: ./index.php?message=wrongcredentials');
} 
else
{
	include "./Includes/branding.inc.php";
}
?>
<form method="POST" action="index.php?module=login" id="login">

<p>
    <label for="user"><?php echo $GLOBALS["Program_Language"]["Username"]; ?></label>
    <input class ="text" id ="user" name="user" />
</p>
<p>
    <label for="pass"><?php echo $GLOBALS["Program_Language"]["Password"]; ?></label>
    <input class ="text"  id = "pass" name="pass" type="password" />
</p>
<p>
	<label for= "Style">Style</label>
	<?php
		ui_get_Styles("./");
	?>
</p>
<p class="loginSubmit">
    <input type="submit" value="<?php echo $GLOBALS["Program_Language"]["Log_In"]; ?>" />
</p>

<a class = "actions" href = "index.php?module=register"><img alt ="New User" src="./Images/user_add.png"><?php echo $GLOBALS["Program_Language"]["Register"]; ?></a>
<a class = "actions" href = "index.php?module=recover"><img alt="Recover Password" src="./Images/key_go.png"><?php echo $GLOBALS["Program_Language"]["Recover"]; ?></a>
</form>


