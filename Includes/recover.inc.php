<p id="logo">
    <img src="./Images/Logo.png" />
</p>
<?php
if (isset($_POST["email"])){
	
	if (isset($_SESSION) == false)
			session_start();	
	include $_SESSION["Program_Dir"]."Includes/Program.inc.php";		
	//TODO: NOT FINISHED YET
	recover($_POST["email"]);
	header("Location: ../index.php");
	
}
?>
<form method="POST" action="./Includes/recover.inc.php" id="login">
<p>
    <label for="email">Email</label>
    <input class ="text" name="email" />
</p>
<p class="loginSubmit">
    <input type="submit" value="Renew" />
</p>
</form>