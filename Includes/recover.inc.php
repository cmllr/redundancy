<p id="logo">
    <img src="./Images/Logo.png" />
</p>
<form method="POST" action="./Includes/recover.inc.php" id="login">
<?php
if (isset($_POST["email"])){
	
	if (isset($_SESSION) == false)
			session_start();	
	include $_SESSION["Program_Dir"]."Includes/Program.inc.php";	
	recover($_POST["email"]);
}
if (isset($_GET["msg"]) && $_GET["msg"] == "success")
{
	echo "<h2>".$GLOBALS["Program_Language"]["reovered"]."</h2>";
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
</form>