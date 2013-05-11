<?php	
	//start a session if needed
	if (isset($_SESSION) == false)
		session_start();
	//exit everything
	session_unset();
	session_destroy();
	header('Location: ./index.php');
?>
