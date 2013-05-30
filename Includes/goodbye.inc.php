<div class = "contentWrapper">
<?php
	//Use with caution
	//TODO: Find the bug which causes a complete loss of any data?! :(
	if (isset($_SESSION) == false)
			session_start();
	if ($_SESSION["role"] != 3 && isset($_GET["sure"])){	
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$getFiles = mysqli_query($connect,"Select * from Files where UserID = '$userID'") ;
		while ($row = mysqli_fetch_object($getFiles)) {
			echo "<br>Deleting ".$row->Displayname ."...";
			if ($row->Filename != $row->Displayname)
			unlink($GLOBALS["Program_Dir"]."Storage/".$row->Filename);
			echo "<br>Removing database entry...";
			mysqli_query($connect,"Delete from Files where UserID = '$userID'");			
			mysqli_query($connect,"Delete from Share where UserID = '$userID'");
			echo "<br>Removing shares entry entry...".mysql_affected_rows()." found.";
			echo "..Done";			
		}
		echo "Delete from Users where UserID = '$userID'";
		mysqli_query($connect,"Delete from Users where ID = '$userID'");
		mysqli_close($connect);	
		header("Location: index.php?module=logout");
	}	
	else
	{
		echo "A deletion will cause the loss of <b>any</b> of your stored data!<br><br>";
		echo "<a href = 'index.php?module=goodbye&sure=true'>I still want to delete this account.</a>";
	}
?>
</div>