<?php
	function inst_create_DataBaseConfig($user,$pass,$server,$db)
	{		
		try{
			$ourFileName = "../Includes/DataBase.inc.php";
			if (is_writable($ourFileName) && file_exists($ourFileName)){
				$escapedollar = "$";
				$text = $escapedollar."connect = mysqli_connect('$server', '$user', '$pass') or die('Error: 005 '.mysqli_error());	
				mysqli_select_db(".$escapedollar."connect,'$db') or die('Error: 006 '.mysqli_error()); 
				if (isset(".$escapedollar."_POST[\"method\"])){
					mysqli_query(".$escapedollar."connect,\"SET NAMES 'utf8'\");
					mysqli_query(".$escapedollar."connect,\"SET CHARACTER SET 'utf8'\");
				}";
				
				$ourFileHandle = fopen($ourFileName, 'w') or die("cant open file");
				fwrite($ourFileHandle, "<?php $text?>");
				fclose($ourFileHandle);		
				$connect = mysqli_connect("$server", "$user", "$pass");
				if (mysqli_select_db($connect,"$db") ){			
					inst_create_DataBase_Structure($user,$pass,$server,$db);			
				}
			}
			else{				
				echo "<div class='alert alert-danger'>The database config could not be written</div>";
			}
		}
		catch (Exception $e){		
			echo "<div class='alert alert-danger'>".$e->getMessage()."</div>";
		}
	}
	function inst_create_DataBase_Structure($user,$pass,$server,$db)
	{		
		try{
			$error = false;
			$myFile = "./database.sql";
			$fh = fopen($myFile, 'r');
			$theData = "";
			while (!feof($fh)) {
				$theData = $theData ."\n".fgets($fh);			
			}
			$connect = @mysqli_connect("$server", "$user", "$pass") or die("Error: 005 ".mysqli_error($connect));
			@mysqli_select_db($connect,"$db") or die("Error: 006 ".mysqli_error($connect)); 	
			$array = explode("##",$theData);
			for ($i = 0; $i<count($array);$i++)	{
				if (!@mysqli_query($connect,$array[$i])){
					echo "<div class='alert alert-danger'>There was an error running the query. This may be an indicator that your database is not empty!</div>";
					$error = true;
				}
			}	
				
			mysqli_close($connect);
			fclose($fh);
			if (!$error){
				header("Location: index.php?step=3");
				exit;
			}			
		}
		catch (Exception $e)
		{
			echo "<div class='alert alert-danger'>".$e->getMessage()."</div>";
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
	function inst_create_root($name,$pass)
	{			
		$pUser = $name;			
		$passOK = true;
		//Include database file
		include $_POST["dir"]."Includes/DataBase.inc.php";			
		$user = mysqli_real_escape_string($connect,$pUser);
		$pass = mysqli_real_escape_string($connect,$pass);		
		$email = $user."@localhost";		
		$salt = getRandomKey(200);
		$safetypass = hash('sha512',$pass.$salt.$salt.$user);
		$registered= date("Y-m-d H:i:s",time());
		$role = $GLOBALS["config"]["User_Default_Role"];		
		$storage = 10;
		$api_key = hash('sha512',$Email.$salt.$user."thehoursedoesnoteatchickenandbacon");			
		$enabled = 1;	
		$ergebnis = @mysqli_query($connect,"Insert into Users (User, Email,Password,Salt,Registered,Role,Storage,Enabled,API_Key,Enable_API) Values ('$user','$email','$safetypass','$salt','$registered','$role',$storage,$enabled,'$api_key',1)");		
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
