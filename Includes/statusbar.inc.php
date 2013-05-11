<div id = "statusbar">
<p id = "title">
<a href = "index.php">
<img src = "./Images/Logo.png">
<?php
if (isset($_SESSION) == false)
		session_start();
	echo $_SESSION["config"]["Program_Name_ALT"];
?>
</a>
</p>
<?php
	if (isset($_SESSION["user_logged_in"])){
		include $_SESSION["Program_Dir"]."Includes/Menu.inc.php";
		echo "<a id = 'accountname'  href = 'javascript:void(0)' onclick = 'displayorhide();'><img src = './Images/user_orange.png'>".$_SESSION['user_name']." (".$_SESSION['user_email'].")</a>";
	}	
?>
</div>