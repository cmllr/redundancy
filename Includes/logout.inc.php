<?php	
	//start a session if needed
	if (isset($_SESSION) == false)
		session_start();

	
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	$id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
	echo $id;
	$query = "Update Users SET Session_Closed = '1' where ID = ".$id;
	echo $query;
	
	$erg = mysqli_query($connect,$query) or die ("error".mysqli_error($connect));
	echo "erg:".$erg;
	mysqli_close($connect);
		//exit everything
	
	session_unset();
	session_destroy();
	header('Location: ./index.php');
?>
