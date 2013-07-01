<form method="POST" action="index.php?module=admin" id="login">
<?php
	
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();	
	//TODO: Implement admin check
	if ($_SESSION["role"] == 0 && is_admin())
	{
		echo "<h2>".$GLOBALS["Program_Language"]["Admin_Panel"]."</h2><br>";
		include "health.inc.php";
		$snapshotcount = 0;
		$last = "-";
		if ($handle = opendir($GLOBALS["config"]["Program_Path"]."Snapshots/")) {
			while (false !== ($file = readdir($handle))) {			
				if ($file != "." && $file != ".." && endsWith($file,".zip") )
				{		
					$snapshotcount++;
					$last = date ("F d Y H:i:s", filemtime($GLOBALS["config"]["Program_Path"]."Snapshots/".$file));
				}
			}		
		}
		closedir($handle);	
		if ($snapshotcount == 0)
			echo "<img src = './Images/error.png'>0 Snapshots<br>";
		else
			echo "<img src = './Images/accept.png'>".$snapshotcount ." Snapshot(s) (".$last.")<br>";	
		echo "<br><hr><img src = './Images/accept.png' alt = 'not ok'>Feature needed <b>or</b> security relevant feature is disabled<br>";
		echo "<img src = './Images/information.png' alt = 'not ok'>Security relevant feature is enabled<br>";
		echo "<img src = './Images/error.png' alt = 'not ok'>Possible security problem<br>";
		echo "<img src = './Images/exclamation.png' alt = 'not ok'>Problem<br>";
		echo "<br><hr><br><a href = '?module=snapshot'>Run Snapshot</a>";
	}		
	else
	{
		echo "You don't have the rights to access this page";
	}


	?>
</form>
