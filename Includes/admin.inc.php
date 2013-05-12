<?php
	//Lock unitl this function is implemented
	exit;
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();	
	if ($_SESSION["role"] == 0)
	{
		echo "<h2>Administration panel</h2><br>";
	}
	else
	{
		echo "You don't have the rights to access this page";
	}
?>
<form method="POST" action="index.php?module=admin" id="login">
<p>
    <label for="user">User</label>
    <input class ="text" name="user_to_lock" />
</p>
<p class="loginSubmit">
    <input type="submit" value="Lock" />
</p>
</form>