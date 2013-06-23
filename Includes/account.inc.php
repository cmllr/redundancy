<div class = "contentWrapper">
<h1><?php echo $GLOBALS["Program_Language"]["User_Details"];?></h1>
<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Include DataBase file
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	//get the urrent user id an search for users with this id
	$id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
	$result = mysqli_query($connect,"Select * from Users  where ID = '$id' limit 1") or die("DataBase Error: 001 ".mysqli_error($connect));
	while ($row = mysqli_fetch_object($result)) {
		echo "<b>".$GLOBALS["Program_Language"]["Email"].": </b> ".$row->Email;	
		echo "<br><b>".$GLOBALS["Program_Language"]["Username"].": </b> ".$row->User;
		if ($_SESSION["role"] != 3)
			echo "<br><b>API Token </b><input type ='text' cols='70' rows='2' value ='".$row->API_Key."'></input></p>";
		//If the config allows user deletion by the user himself, display a link
		if ($GLOBALS["config"]["User_Allow_Delete"] == 1 )
			echo "<br><a href = 'index.php?module=goodbye'>".$GLOBALS["Program_Language"]["Delete_Account"]."</a>";
	
	}	
	//Close the connection if finished
	mysqli_close($connect);	
?>
<br><a href = "index.php?module=zip&dir=/"><?php echo $GLOBALS["Program_Language"]["Download_All_Files"];?></a>
</div>