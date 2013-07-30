<div class ="contentWrapper">
<h2>System status</h2>
<br>
<?php	
	echo "<b>Performing feature check</b><br>";
	if (class_exists("ZipArchive") == true)
	{
		echo "<img src = './Images/accept.png' alt = 'ok'> Zip Support<br>";
	}	
	else
	{
		echo "<img src = './Images/exclamation.png' alt = 'ok'> Zip Support<br>";
	}
	if (function_exists("imagecreate") == true)
	{
		echo "<img src = './Images/accept.png' alt = 'ok'> Image Support<br>";
	}
	else
	{
		echo "<img src = './Images/exclamation.png' alt = 'ok'> Image Support<br>";
	}
	if (function_exists("move_uploaded_file") == true)
	{
		echo "<img src = './Images/accept.png' alt = 'ok'> FileSystem Support<br>";
	}
	else
	{
		echo "<img src = './Images/exclamation.png' alt = 'ok'> FileSystem Support<br>";
	}
	if (function_exists("mysqli_query") == true)
	{
		echo "<img src = './Images/accept.png' alt = 'ok'> DataBase Support<br>";
	} 
	else
	{
		echo "<img src = './Images/exclamation.png' alt = 'ok'> DataBase Support<br>";
	}
	if (is_writable($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/") == true)
	{
		echo "<img src = './Images/accept.png' alt = 'ok'> Storage Access<br>";
	} 
	else
	{
		echo "<img src = './Images/exclamation.png' alt = 'ok'> Storage Access<br>";
	}
	if (is_writable($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Temp_Dir"]."/") == true)
	{
		echo "<img src = './Images/accept.png' alt = 'ok'> Temp Access<br>";
	} 
	else
	{
		echo "<img src = './Images/exclamation.png' alt = 'ok'> Temp Access<br>";
	}
	if (is_writable($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/") == true)
	{
		echo "<img src = './Images/accept.png' alt = 'ok'> Snapshots Access<br>";
	} 
	else
	{
		echo "<img src = './Images/exclamation.png' alt = 'ok'> Snapshots Access<br>";
	}
	include $GLOBALS["config"]["Program_Path"]."Includes/DataBase.inc.php";	
	$query = mysqli_query($connect,"Select * from Files");
	$count = 0;
	$countMissing = 0;	
	echo "<b>Performing database check</b><br>";
	while ($row = mysqli_fetch_object($query)) {		
		if ($row->Filename != $row->Displayname){
		$count++;			
			if (file_exists($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/".$row->Filename) == false)
			{
				$countMissing++;
				echo "<img src = './Images/exclamation.png' alt = 'ok'>File \"".$row->Displayname."\" (".$row->Filename.") in database, but not on filesystem!<br>";
			}
		}		
	}	
	if ($countMissing == 0)
		echo "<img src = './Images/accept.png' alt = 'ok'>";
	else
		echo "<img src = './Images/exclamation.png' alt = 'not ok'>";
	$percent = 0;
	if ($countMissing != 0)
	{
		$percent = round(100/($count/$countMissing),2);
	}
	echo "DataBase Check: Found ".$count." files in database, ".$countMissing." (".$percent."%)<br>";
	$count = 0;
	$countMissing = 0;
	echo "<b>Performing filesystem check</b><br>";
	if ($handle = opendir($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/")) {
		while (false !== ($file = readdir($handle))) {			
			if ($file != "." && $file != ".." && $file != ".htaccess" && $file != "index.php" )
			{
				$count++;
				$result = mysqli_query($connect,"Select * from Files where Filename = '$file'");
				if (mysqli_affected_rows($connect) == 0)
				{
					$countMissing++;					
					echo "<img src = './Images/exclamation.png' alt = 'ok'>File \"$file\" on filesystem, but not in database!<br>";
				}
			}
		}
		closedir($handle);
	}
	if ($countMissing == 0)
		echo "<img src = './Images/accept.png' alt = 'ok'>";
	else
		echo "<img src = './Images/exclamation.png' alt = 'not ok'>";
	if ($countMissing != 0)
	{
		$percent = round(100/($count/$countMissing),2);
	}
	echo "FileSystem Check: Found ".$count." files on filesystem, ".$countMissing." too much (".$percent."%)<br>";
	echo "<b>Performing security check</b><br>";
	$count = 0;
	$query = mysqli_query($connect,"Select * from Banned where Reason = 'XSS'");
	while ($row = mysqli_fetch_object($query)) {		
			$count++;
	}	
	if ($count == 0)
		echo "<img src = './Images/accept.png' alt = 'ok'>";
	else
		echo "<img src = './Images/error.png' alt = 'not ok'>";
	echo "$count XSS attacks blocked<br>";
	$count = 0;
	$query = mysqli_query($connect,"Select * from Users where Enabled = 0");
	while ($row = mysqli_fetch_object($query)) {		
			$count++;
	}	
	if ($count == 0)
		echo "<img src = './Images/accept.png' alt = 'ok'>";
	else
		echo "<img src = './Images/error.png' alt = 'not ok'>";
	echo "$count disabled user(s) found<br>";	
	echo "<b>Performing config check</b><br>";
	if ($GLOBALS["config"]["Enable_register"]== "1")
		echo "<img src = './Images/information.png' alt = 'ok'>Registration is on<br>";
	else
		echo "<img src = './Images/accept.png' alt = 'not ok'>Registration is off<br>";
	if ($GLOBALS["config"]["Program_HTTPS_Redirect"]=="1")
		echo "<img src = './Images/accept.png' alt = 'ok'>HTTP is on<br>";
	else
		echo "<img src = './Images/error.png' alt = 'not ok'>HTTPS is off<br>";
	if ($GLOBALS["config"]["Api_Enable"] == 1 )
		echo "<img src = './Images/information.png' alt = 'ok'>API is on<br>";
	else
		echo "<img src = './Images/accept.png' alt = 'not ok'>API is off<br>";
	if ($GLOBALS["config"]["User_Enable_Recover"]==1)
		echo "<img src = './Images/information.png' alt = 'ok'>User is allowed to recover passwords<br>";
	else
		echo "<img src = './Images/accept.png' alt = 'not ok'>Password recovery is off<br>";	
	if ($GLOBALS["config"]["Program_Enable_Plugins"]==1)
		echo "<img src = './Images/information.png' alt = 'ok'>Plugins are enabled<br>";
	else
		echo "<img src = './Images/accept.png' alt = 'not ok'>Plugins are disabled<br>";	
	if ($GLOBALS["config"]["Program_Debug"]==1)
		echo "<img src = './Images/error.png' alt = 'ok'>Debug mode is on<br>";
	else
		echo "<img src = './Images/accept.png' alt = 'not ok'>Debug mode is off<br>";	
	//Close the connection if finished
	mysqli_close($connect);

	
?>
</div>