<p id="logo">
    <img src="./Images/Logo.png" />
</p>
<?php
if (isset($_POST["regemail"])){
	
	if (isset($_SESSION) == false)
			session_start();	
	include $_SESSION["Program_Dir"]."Includes/Program.inc.php";		
	if (registerUser($_POST["reguser"],$_POST["regemail"],$_POST["regpass"],$_POST["regpass_repeat"]) == true)
		header("Location: ../index.php");
	else
		header("Location: ../index.php?module=register");
}
if ($_SESSION["config"]["Enable_register"] == 0){
	echo "<form id = 'login'><p>Registering is disabled</p>";
	exit;
}
?>


<form method="POST" action="./Includes/register.inc.php" id="login">
<p>
    <label for="reguser">User</label>
    <input class ="text" name="reguser" />
</p>
<p>
    <label for="regemail">Email</label>
    <input class ="text" name="regemail" />
</p>
<p>
    <label for="regpass">Password</label>
    <input class ="text"  name="regpass" type="password" />
</p>
<p>
    <label for="regpass_repeat">Repeat Password</label>
    <input class ="text"  name="regpass_repeat" type="password" />
</p>
<p class="loginSubmit">
    <input type="submit" value="Register" />
</p>
<!--<p>
<input type=submit name=submit value="Log in">-->
<a class = "actions" href = "index.php"><img src="./Images/arrow_left.png">Back</a>
</form>
