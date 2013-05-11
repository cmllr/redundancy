<?php
//Only proceed if a post param named user is iset
if (isset($_POST["user"])){
	//start a session if needed.
	if (isset($_SESSION) == false)
		session_start();	
	//Include Program file
	include $_SESSION["Program_Dir"]."Includes/Program.inc.php";
	//Login and/or rediret the user
	if (login($_POST["user"],$_POST["pass"]) == true)
		header('Location: ../index.php');
	else
		header('Location: ../index.php?message=3');
}
?>
<p id="logo">
    <img src="./Images/Logo.png" />
</p>
<form method="POST" action="./Includes/Login.inc.php" id="login">

<p>
    <label for="user">User</label>
    <input class ="text" name="user" />
</p>
<p>
    <label for="pass">Password</label>
    <input class ="text"  name="pass" type="password" />
</p>
<p class="loginSubmit">
    <input type="submit" value="Log in" />
</p>
<a class = "actions" href = "index.php?module=register"><img src="./Images/user_add.png">Register new account</a>
<a class = "actions" href = "index.php?module=recover"><img src="./Images/key_go.png">Recover password</a>
</form>