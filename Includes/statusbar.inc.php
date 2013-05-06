<div id = "statusbar">
<?php
	if (isset($_SESSION) == false)
		session_start();
	echo "<a href = 'index.php?module=account'>".$_SESSION['user_name']." (".$_SESSION['user_email'].")</a>";
?>
</div>