<?php
if (isset($_POST["user"])){
	if (isset($_SESSION) == false)
			session_start();	
	include $_SESSION["Program_Dir"]."Includes/Program.inc.php";	
	if (login($_POST["user"],$_POST["pass"]) == true)
		header('Location: ../index.php');
	else
		header('Location: ../index.php?message=1');
}
?>
<div id = "login">
<form method="POST" action="./Includes/Login.inc.php" align = "center">
<tag>User<br> </tag><input id ="text" name="user"><p>
<tag>Password<br> </tag><input id ="text"  name="pass" type=password>
<p>
<input type=submit name=submit value="Log in">
</form>
</div>
