<form method="POST" action="index.php?module=recover" id="login">
<?php
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