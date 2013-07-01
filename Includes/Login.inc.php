<?php
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
	if (login($_POST["user"],$_POST["pass"]) == true)
		header('Location: ./index.php'.$redir);
	else
		header('Location: ./index.php?message=3');
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
<p class="loginSubmit">
    <input type="submit" value="<?php echo $GLOBALS["Program_Language"]["Log_In"]; ?>" />
</p>
<a class = "actions" href = "index.php?module=register"><img alt ="New User" src="./Images/user_add.png"><?php echo $GLOBALS["Program_Language"]["Register"]; ?></a>
<a class = "actions" href = "index.php?module=recover"><img alt="Recover Password" src="./Images/key_go.png"><?php echo $GLOBALS["Program_Language"]["Recover"]; ?></a>
</form>
