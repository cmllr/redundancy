<?php
	function inst_create_DataBaseConfig($user,$pass,$server,$db)
	{		
		try{
			$ourFileName = $_POST["dir"]."Includes/DataBase.inc.php";
			if (is_writable($ourFileName)){
				$escapedollar = "$";
				$text = $escapedollar."connect = mysqli_connect('$server', '$user', '$pass') or die('Error: 005 '.mysqli_error());	mysqli_select_db(".$escapedollar."connect,'$db') or die('Error: 006 '.mysqli_error());";
				
				$ourFileHandle = fopen($ourFileName, 'w') or die("cant open file");
				fwrite($ourFileHandle, "<?php $text?>");
				fclose($ourFileHandle);		
				$connect = mysqli_connect("$server", "$user", "$pass");
				if (mysqli_select_db($connect,"$db") == false){				
					echo "<td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td>";
					$GLOBALS["fail"]++;		
					$GLOBALS["ERRORS"]["DB"] = "Could not establish connection to database";				
					return;	
				}else{			
					echo "<td><span class=\"successValue elusive icon-ok glyphIcon\"></span></td>";
					inst_create_DataBase_Structure($user,$pass,$server,$db);
				}
			}
			else{
				$GLOBALS["ERRORS"]["DB"] = "Could not write Database config";	
					echo "<td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td>";
			}
		}
		catch (Exception $e){
			echo "<td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td>";
			$GLOBALS["ERRORS"]["DB"] = "Could not write Database config";	
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
			$GLOBALS["ERRORS"]["DB"] = "Could not import database dump.";	
		}
	}
	function inst_check_directory_rights($storage,$temp,$snapshots)
	{
		try{			
			if (file_exists($storage."/") && is_writable($storage."/") == true)
			{
				echo "<tr><td><span class=\"successValue elusive icon-ok glyphIcon\"></span></td><td>Storage access</td></tr>";
				inst_sec_check($storage."/");
			} 
			else
			{
				echo "<tr><td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td><td>Storage access</td></tr>";
				$GLOBALS["fail"]++;
				if (file_exists($storage."/"))
					$GLOBALS["ERRORS"]["STORAGE"] = "Storage directory is not readable or does not exists";	
				else
					$GLOBALS["ERRORS"]["STORAGE"] = "Storage directory is does not exists";	
			}
			
			if (file_exists($temp."/") && is_writable($temp."/") == true)
			{
				echo "<tr><td><span class=\"successValue elusive icon-ok glyphIcon\"></span></td><td>Temp access</td></tr>";
				inst_sec_check($temp."/");
			} 
			else
			{
				echo "<tr><td><span class=\"successValue elusive icon-remove glyphIcon\"></span><td>Temp access</td></tr>";
				$GLOBALS["fail"]++;
				if (file_exists($temp."/"))
					$GLOBALS["ERRORS"]["TEMP"] = "Temp directory is not readable or does not exists";	
				else
					$GLOBALS["ERRORS"]["TEMP"] = "Temp directory is does not exists";	
			}
			if (file_exists($snapshots."/") && is_writable($snapshots."/") == true)
			{
				echo "<tr><td><span class=\"successValue elusive icon-ok glyphIcon\"></span></td><td>Snapshots access</td></tr>";
				inst_sec_check($snapshots."/");
			} 
			else
			{
				echo "<tr><td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td><td>Snapshots access</td></tr>";
				$GLOBALS["fail"]++;
				if (file_exists($snapshots."/"))
					$GLOBALS["ERRORS"]["SNAPSHOTS"] = "Snapshots directory is not readable";	
				else
					$GLOBALS["ERRORS"]["SNAPSHOTS"] = "Snapshots directory does not exists";	
			}
		}
		catch (Exception $e)
		{
			$GLOBALS["ERRORS"]["DIRS"] = "Please check the integrity of your installation";	
		}
	}
	function inst_apply_configuration($program_dir,$storage,$temp,$snapshots)
	{
		try{
			if (file_exists($program_dir."Redundancy.conf")){
				$myFile = $program_dir."Redundancy.conf";
				$fh = fopen($myFile, 'r');
				$theData = "";
				while (!feof($fh)) {
					$theData = $theData .fgets($fh);			
				}
				fclose($fh);
				if (strpos($theData,"##") === false){	
					
					$fh = fopen("./config.txt", 'r');
					$theData = "";
					while (!feof($fh)) {
						$theData = $theData .fgets($fh);			
					}
					fclose($fh);						
				}				
				$theData = str_replace("##dir##",$program_dir,$theData);	
				$theData = str_replace("##storage##",$storage,$theData);	
				$theData = str_replace("##temp##",$temp,$theData);	
				$theData = str_replace("##snapshots##",$snapshots,$theData);
				$fh = fopen($myFile, 'w') or die("can't open file");	
				fwrite($fh, $theData);
				fclose($fh);			
				echo "<tr><td><span class=\"successValue elusive icon-ok glyphIcon\"></span></td><td>Configuration creation successfull</td></tr>";
			}
			else
			{
				echo "<tr><td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td><td>Configuration creation failed</td></tr>";
				$GLOBALS["ERRORS"]["CONF"] = "Redundancy.conf does not exists";	
				$GLOBALS["fail"]++;
			}
		}
		catch (Exception $e)
		{
			echo "<tr><td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td><td>Configuration creation failed</td></tr>";
			$GLOBALS["fail"]++;
			$GLOBALS["ERRORS"]["CONF"] = "Please check if Redundancy.conf is writable by PHP";	
		}		
	}
	function inst_check()
	{
		if ($GLOBALS["fail"] == 0){			
			echo"<br><div class = 'alert alert-info'>Please delete the directory /Installer/ and its contents. The installation was successfully.</div>";
		}else{
			echo "<br><div class = 'alert alert-danger'>One or more steps failed. Please check your values<br>";
			echo "<ul>";
			foreach ($GLOBALS["ERRORS"] as $key => $value)
			{
				echo "<li>[$key] $value</li>";
			}
			echo "</ul>";			
			
			echo "Use the navigation of your browser to go back or retry the installation</div>";
		}
	}
	function inst_sec_check($dir)
	{
		if (file_exists($dir.".htaccess") == false)
		{
			echo "<div style = 'word-wrap: break-word;' class = 'alert alert-info'>Your data in dir \"$dir\" is maybe accessible from the internet. You can use a \".htaccess\" file to avoid access from the internet</div>";
		}
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
				echo "<td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td>";
				$GLOBALS["ERRORS"]["ROOT"] = "A root user already exists";	
					$GLOBALS["fail"]++;
			}
			else		
			{
				echo "<td><span class=\"successValue elusive icon-ok glyphIcon\"></span></td>";
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