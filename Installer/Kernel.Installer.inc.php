<?php
	function inst_create_DataBaseConfig($user,$pass,$server,$db)
	{		
		try{
			$escapedollar = "$";
			$text = $escapedollar."connect = mysqli_connect('$server', '$user', '$pass') or die('Error: 005 '.mysqli_error());	mysqli_select_db(".$escapedollar."connect,'$db') or die('Error: 006 '.mysqli_error());";
			$ourFileName = $_POST["dir"]."Includes/DataBase.inc.php";
			$ourFileHandle = fopen($ourFileName, 'w') or die("cant open file");
			fwrite($ourFileHandle, "<?php $text?>");
			fclose($ourFileHandle);
			echo "<img src = './Images/accept.png'> Config file created successfully<br>";
			echo "<i>Testing database config...</i><br>";
			$connect = mysqli_connect("$server", "$user", "$pass");
			if (mysqli_select_db($connect,"$db") == false){
				echo "<img src = './Images/exclamation.png'> Database connection failed<br>";
				$GLOBALS["fail"]++;
				echo "Aborting...<br>";		
				return;	
			}else{
				echo "<img src = './Images/accept.png'> Database connection successfull<br>";
				inst_create_DataBase_Structure($user,$pass,$server,$db);
			}
		}
		catch (Exception $e){
			echo "<img src = './Images/exclamation.png'> Config file failed<br>";
			$GLOBALS["fail"]++;
		}
	}
	function inst_create_DataBase_Structure($user,$pass,$server,$db)
	{
		if ($GLOBALS["fail"] > 0)
		{
			echo "Aborting...<br>";		
			return;			
		}
		try{
			$myFile = "./database.sql";
			$fh = fopen($myFile, 'r');
			$theData = "";
			while (!feof($fh)) {
				$theData = $theData ."\n".fgets($fh);			
			}
			$connect = mysqli_connect("$server", "$user", "$pass") or die("Error: 005 ".mysqli_error($connect));
			mysqli_select_db($connect,"$db") or die("Error: 006 ".mysqli_error($connect)); 	
			$array = explode("##",$theData);
			for ($i = 0; $i<count($array);$i++)		
				mysqli_query($connect,$array[$i]) or die(mysqli_error($connect));
			mysqli_close($connect);
			fclose($fh);
		}
		catch (Exception $e)
		{
			$GLOBALS["fail"]++;
		}
	}
	function inst_check_directory_rights($storage,$temp,$snapshots)
	{
		if ($GLOBALS["fail"] > 0)
		{
			echo "Aborting...<br>";		
			return;			
		}
		if (is_writable($storage."/") == true)
		{
			echo "<img src = './Images/accept.png' alt = 'ok'> Storage Access<br>";
		} 
		else
		{
			echo "<img src = './Images/exclamation.png' alt = 'ok'> Storage Access<br>";
			$GLOBALS["fail"]++;
		}
		if (is_writable($temp."/") == true)
		{
			echo "<img src = './Images/accept.png' alt = 'ok'> Temp Access<br>";
		} 
		else
		{
			echo "<img src = './Images/exclamation.png' alt = 'ok'> Temp Access<br>";
			$GLOBALS["fail"]++;
		}
		if (is_writable($snapshots."/") == true)
		{
			echo "<img src = './Images/accept.png' alt = 'ok'> Snapshots Access<br>";
		} 
		else
		{
			echo "<img src = './Images/exclamation.png' alt = 'ok'> Snapshots Access<br>";
			$GLOBALS["fail"]++;
		}
	}
	function inst_apply_configuration($program_dir,$storage,$temp,$snapshots)
	{
		if ($GLOBALS["fail"] > 0)
		{
			echo "Aborting...<br>";		
			return;			
		}
			
		try{
			$myFile = $program_dir."Redundancy.conf";
			$fh = fopen($myFile, 'r');
			$theData = "";
			while (!feof($fh)) {
				$theData = $theData .fgets($fh);			
			}
		
			fclose($fh);
		
			$theData = str_replace("##dir##",$program_dir,$theData);	
			$theData = str_replace("##storage##",$storage,$theData);	
			$theData = str_replace("##temp##",$temp,$theData);	
			$theData = str_replace("##snapshots##",$snapshots,$theData);	
			
			$fh = fopen($myFile, 'w') or die("can't open file");	
			fwrite($fh, $theData);
			fclose($fh);
			echo "<img src = './Images/accept.png' alt = 'ok'> Configuration creation successfull<br>";
		}
		catch (Exception $e)
		{
			echo "<img src = './Images/exclamation.png' alt = 'ok'> Configuration creation failed<br>";
			$GLOBALS["fail"]++;
		}		
	}
	function inst_check()
	{
		if ($GLOBALS["fail"] == 0){			
			echo"<i><b>Note:</b> Please delete the directory /Installer/ and its contents</i>";
		}else
			echo "One or more steps failed. Please check your configuration";
	}
	function inst_create_root($name,$pass)
	{		
		$pUser = $name;
		//Is the password and the repeated the same?
		if (strpos("<",$name) !== false || strpos("<",$name) !== false || strpos("<",$name) !== false )
			return;
		$passOK = true;		
		//Include database file
		$connect = mysqli_connect($_POST["server"], $_POST["user"], $_POST["pass"]) or die('Error: 005 '.mysqli_error());	
		mysqli_select_db($connect,$_POST["db"]) or die('Error: 006 '.mysqli_error());
		$user = mysqli_real_escape_string($connect,$name);
		$pass = mysqli_real_escape_string($connect,$pass);	
		$email = mysqli_real_escape_string($connect,"root@localhost.local");
		$salt = getRandomKey(200);
		$safetypass = hash('sha512',$pass.$salt."thehoursedoesnoteatchickenandbacon");	
		$registered= date("D M j G:i:s T Y",time());
		$role = 0;	
		$storage = "10";
		$api_key = hash('sha512',$email.$salt.$user."thehoursedoesnoteatchickenandbacon");	
		$enabled = 1;
		if ($passOK == true){
			$ergebnis = mysqli_query($connect,"Select * from Users where  User = '$user' or Email = '$email'") or die("Error: 021 ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) > 0)
			{
				echo "<img src = './Images/exclamation.png' alt = 'ok'> Root user was not created<br>";
			}
			else		
			{
				echo "<img src = './Images/accept.png' alt = 'ok'> Root user was created<br>";
				$ergebnis = mysqli_query($connect,"Insert into Users (User, Email,Password,Salt,Registered,Role,Storage,Enabled,API_Key,Enable_API) Values('$user','$email','$safetypass','$salt','$registered','$role',$storage,$enabled,'$api_key',1)") or die("Error: 019 ".mysqli_error($connect));
			}	
			
		}		
		
	}	
	/**
	 * getRandomKey: get random key
	 * @todo Improve of the function due security reasons
	 * @param $length - The length of the wanted string
	 * @return A random string with a determined length
	 */
	function getRandomKey($length) {
		
		$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$s_length = strlen($chars);
		$randomString = "";
		if ($length <= $s_length){
			$randomString = substr(str_shuffle($chars), 0, $length);
		
		}
		else
		{
			$iterations = $length/$s_length;
			$singlePart = $length/$iterations;
			for ($i = 0; $i < $iterations ;$i++)
			{
				$randomString = $randomString.substr(str_shuffle($chars), 0, $singlePart);
			}
		}
		return $randomString;
    }
?>