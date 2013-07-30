<div class = "contentWrapper">
<h1><?php echo $GLOBALS["Program_Language"]["User_Details"];?></h1>
<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Include DataBase file
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	//get the current user id an search for users with this id
	$id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
	$result = mysqli_query($connect,"Select Email,User,API_Key from Users  where ID = '$id' limit 1") or die("DataBase Error: 001 ".mysqli_error($connect));
	//Display the user informations (Email, Username, API_Key)
	while ($row = mysqli_fetch_object($result)) {
		echo "<b>".$GLOBALS["Program_Language"]["Email"].": </b> ".$row->Email;	
		echo "<br><b>".$GLOBALS["Program_Language"]["Username"].": </b> ".$row->User;
		if ($_SESSION["role"] != 3 && $GLOBALS["config"]["Api_Enable"])
			echo "<br><b>API Token </b><input type ='text' cols='70' rows='2' value ='".$row->API_Key."'></input></p>";
	}	
	echo "<h2>".$GLOBALS["Program_Language"]["Password_Management"]."</h2>";	
	echo "<h3>".$GLOBALS["Program_Language"]["Pass_Changes"]."</h2>";
	$result = mysqli_query($connect,"Select IP from Pass_History  where Who = '$id' limit 10") or die("DataBase Error: 001 ".mysqli_error($connect));
	while ($row = mysqli_fetch_object($result)) {		
		echo $row->Changed." - " .$row->IP."<br>";
	}	
	//Display the passwort recovery link if allowed
	if ($GLOBALS["config"]["User_Enable_Recover"] == 1 && $_SESSION["role"] != 3)
		echo "<br><a href = 'index.php?module=setpass'>".$GLOBALS["Program_Language"]["Set"]."</a>"; 
	//Close the connection if finished	
	mysqli_close($connect);	
?>
<h2><?php echo $GLOBALS["Program_Language"]["Files"];?></h2>
<div id = "progressbar">
<?php
	echo "<div id = 'progressbar_inner' style='width:".round(fs_get_Percentage(),0)."% ;'>";
	echo "<p>&nbsp;".fs_get_Percentage()."&nbsp;".$GLOBALS["Program_Language"]["used"]."</p<>";
?>
</div>
</div>
<p>	
<?php
	echo fs_get_Storage_Percentage();
?>
</p>
<?php
	if ($_SESSION["role"] == 3)
		exit;
?>
<br><a href = "index.php?module=zip&dir=/"><?php echo $GLOBALS["Program_Language"]["Download_All_Files"];?></a>
<?php
	if ($GLOBALS["config"]["User_Allow_Delete"] == 1 && $_SESSION["role"] != 3)
<<<<<<< HEAD
		echo "<br><br><h3>".$GLOBALS["Program_Language"]["Delete_Account"]."</h3><br><a href = 'index.php?module=goodbye'>".$GLOBALS["Program_Language"]["Delete_Account"]."</a><br>";
=======
		echo "<br><br><h3>".$GLOBALS["Program_Language"]["Delete_Account"]."</h3><br><a href = 'index.php?module=goodbye'>".$GLOBALS

["Program_Language"]["Delete_Account"]."</a><br>";
	
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
?>
</div>