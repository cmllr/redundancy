<p id="logo">
    <img src="./Images/Logo.png" />
</p>
<?php
if (isset($_POST["regemail"])){
	
	if (isset($_SESSION) == false)
			session_start();	

	if (registerUser($_POST["reguser"],$_POST["regemail"],$_POST["regpass"],$_POST["regpass_repeat"]) == true)
		header("Location: ./index.php");
	else
		header("Location: ./index.php?module=register");
}
if ($GLOBALS["config"]["Enable_register"] == 0){
	echo "<form id = 'login'><p>".$GLOBALS["Program_Language"]["Register_disabled"]."</p>";
	exit;
}
?>
<form method="POST" action="index.php?module=register" id="login">
<p>
    <label for="reguser"><?php echo $GLOBALS["Program_Language"]["Username"];?></label>
    <input class ="text" name="reguser" />
</p>
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
