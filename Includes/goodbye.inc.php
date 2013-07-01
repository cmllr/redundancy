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
			echo "<br>Removing shares entries ...";
			echo "..Done";			
		}		
		mysqli_query($connect,"Delete from Users where ID = '$userID'");
		mysqli_close($connect);	
		if (isset($_SESSION) == false)
			session_start();
			//exit everything
			session_unset();
			session_destroy();
	}	
	else
	{
		echo $GLOBALS["Program_Language"]["Delete_Warning"]."<br><br>";
		echo "<a href = 'index.php?module=goodbye&sure=true'>".$GLOBALS["Program_Language"]["Delete_OK"]."</a>";
	}
?>
</div>