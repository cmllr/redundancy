<form method="POST" action="index.php?module=admin" id="login">
<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();	
	if ($_SESSION["role"] == 0)
	{
		echo "<h2>".$GLOBALS["Program_Language"]["Admin_Panel"]."</h2><br>";
		echo $GLOBALS["Program_Language"]["System_Language"]." ";
		listLanguages();
		echo "<br>".$GLOBALS["Program_Language"]["User_Storage"]." <input  value ='".$GLOBALS["config"]["User_Contingent"]."'></input> MB</p>";
	}
	else
	{
		echo "You don't have the rights to access this page";
	}
?>

</form>
