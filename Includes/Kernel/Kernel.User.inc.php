<?php
	//User functions	
	function login($pUser,$pPass,$login = true)
	{		
		//start a new session
		if (isset($_SESSION) == false)
			session_start();
		//Include database file
		if (strpos($pUser,"<") === false && strpos($pPass,"<") === false)
		{
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
			$user = mysqli_real_escape_string($connect,$pUser);
			$pass = mysqli_real_escape_string($connect,$pPass);		
			$tries = 0;
			$email ="";
			$name = "";
			$enabled = 1;
			$ergebnis = mysqli_query($connect,"Select ID, User, Email, Password, Salt,Storage,Role, Enabled,Failed_Logins,Session_Closed from Users where User = '$user' or Email = '$user' limit 1") or die("Error: 018 ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($ergebnis)) {	
				$internalpassword = $row->Password;	
				$tries = $row->Failed_Logins;
				$email = $row->Email;
				$name = $row->User;
				$enabled = $row->Enabled;
				if ($internalpassword == hash('sha512',$pass.$row->Salt."thehoursedoesnoteatchickenandbacon") && $row->Enabled == 1)
				{			
					if ($login == true){
						$_SESSION['user_id'] = $row->ID;
						$_SESSION['user_name'] = $row->User;
						$_SESSION['user_email'] = $row->Email;
						$_SESSION["user_logged_in"] = true;
						$_SESSION["currentdir"] = "/";	
						$_SESSION["currentdir_hashed"] = "6666cd76f96956469e7be39d750cc7d9";	
						$_SESSION["space"] = $row->Storage;	
						$_SESSION["space_used"] = 0;
						$_SESSION["role"] = $row->Role;
						$_SESSION["fs_hash"] = hash('sha512',$pass.$row->Salt.$row->Email);
						$_SESSION["Session_Closed"] = $row->Session_Closed;
						
						//Reset Login counter;
						mysqli_query($connect,"Update Users Set Failed_logins=0,Session_Closed =0 where Email ='$user' or User='$user'");
						mysqli_close($connect);	
					}					
					return true;		
				}							
			}
			
		}		
		if ($tries == $GLOBALS["config"]["User_Max_Fails"]){
			if ($enabled != 0){
				mysqli_query($connect,"Update Users Set Enabled=0 where Email ='$user' or User='$user'");		
				sendMail($email,3,$name,getIP2(),"","");		
			}
		}		
		$tries = $tries +1;		
		mysqli_query($connect,"Update Users Set Failed_logins=$tries where Email ='$user' or User='$user'");		
		mysqli_close($connect);
		return false;
	}
	function getNewUserName($pEmail){
		$result = "";
		$go = true;
		$parts = explode("@",$pEmail);
		$result = $parts[0];
		$try = 0;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		do{
			$ergebnis = mysqli_query($connect,"Select ID, User, Email, Password, Salt,Storage,Role, Enabled,Failed_Logins from Users where User = '$result' or Email = '$pEmail' limit 1") or die("Error: 018 ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) == 0)
				$go = false;
			else{
				$try++;
				$result = $parts[i].$try;
			}
		}while($go == true);
		return $result;
	}
	function registerUser($pEmail,$pPass,$pPassRepeat)
	{
		$pUser = getNewUserName($pEmail);
		//Is the password and the repeated the same?
		if (strpos("<",$pUser) !== false || strpos("<",$pEmail) !== false || strpos("<",$pPass) !== false || strpos("<",$pPassRepeat) !== false)
			return false;
		if ($pPass == $pPassRepeat)
		{
			$passOK = true;
		}
		else
		{
			$passOK = false;
		}
		//Is there already a user with this username or email?
		if (isExisting($pEmail,$pUser) == false)
		{
			$free = true;
		}
		else
		{
			$free = false;
		}
		//start a new session
		if (isset($_SESSION) == false)
			session_start();
		//Include database file
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";			
		$user = mysqli_real_escape_string($connect,$pUser);
		$pass = mysqli_real_escape_string($connect,$pPass);	
		$email = mysqli_real_escape_string($connect,$pEmail);
		if (strpos($email,"@") === false || strpos($email,".") === false)
			return false;
		$salt = getRandomKey(200);
		$safetypass = hash('sha512',$pass.$salt."thehoursedoesnoteatchickenandbacon");	
		$registered= date("D M j G:i:s T Y",time());
		$role = $GLOBALS["config"]["User_Default_Role"];		
		$storage = $GLOBALS["config"]["User_Contingent"];
		$api_key = hash('sha512',$Email.$salt.$user."thehoursedoesnoteatchickenandbacon");	
		//Determine if the user should be activated automatically or not
		if ($GLOBALS["config"]["User_Registration_AutoDisable"] == 1)
			$enabled = 0;		
				else 
			$enabled = 1;
		if ($passOK == true && $free == true){
			$ergebnis = mysqli_query($connect,"Insert into Users (User, Email,Password,Salt,Registered,Role,Storage,Enabled,API_Key,Enable_API) Values('$user','$email','$safetypass','$salt','$registered','$role',$storage,$enabled,'$api_key',1)") or die("Error: 019 ".mysqli_error($connect));
		
		}
		else
			return false;
		//Send a activation mail when the account is not activated automatically	
		if ($ergebnis == true){
			if ($GLOBALS["config"]["User_Registration_AutoDisable"] == 1  )
				sendMail($email,1,$user,"Redundancy",$GLOBALS["config"]["User_Activation_Link"]."&email=".$email,"Redundancy");
			return true;
		}
		else
			return false;
	}
	function sendMail($pEmail,$pMessageID,$arg0,$arg1,$arg2,$arg3)
	{
		//Start a new session if needed
		if (isset($_SESSION) == false)
			session_start();
		//Include database file
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$result = mysqli_query($connect,"Select * from Mails where  ID = '$pMessageID' limit 1") or die("Error 020 ". mysqli_error($connect));
		while ($row = mysqli_fetch_object($result)) {			
			$text = sprintf ($row->Text,$arg0,$arg1,$arg2,$arg3);
			$name = $GLOBALS["config"]["User_Activation_Link_Sender"];
			$name_email = $GLOBALS["config"]["User_Activation_Link_Sender_Email"];
			//Only send the email if configured
			if ($name != "" && $name_email != "")
				mail($pEmail, "Redundancy", $text, "From: $name <$name_email>");
		}
		//close connection
		mysqli_close($connect);
	}
	function isExisting($pEmail,$pUser)
	{
		if (isset($_SESSION) == false)
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$user = mysqli_real_escape_string($connect,$pUser);
		$email = mysqli_real_escape_string($connect,$pEmail);
		$ergebnis = mysqli_query($connect,"Select * from Users where  User = '$user' or Email = '$email'") or die("Error: 021 ".mysqli_error($connect));
		if (mysqli_affected_rows($connect) > 0)
		{
			mysqli_close($connect);	
			return true;
		}
		else		
		{
			mysqli_close($connect);	
			return false;
		}		
	}
	function recover($pEmail)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$email = mysqli_real_escape_string($connect,$pEmail);		
		if (isExisting($email,"") && strpos("<",$email) === false)
		{		
			if ($GLOBALS["config"]["User_Enable_Recover"] == 1){
				$pass = getRandomPass($GLOBALS["config"]["User_Recover_Password_Length"]);
				
				$query = mysqli_query($connect,"Select ID,User, Email, Password, Salt from Users where Email ='$email' limit 1");
				$salt = getRandomKey(200);
				$name = "";		
				$id = -1;
				while ($row = mysqli_fetch_object($query)) {					
						$name = $row->User;
						$id = $row->ID;
				}
				$safetypass = hash('sha512',$pass.$salt."thehoursedoesnoteatchickenandbacon");	
				$query = "Update Users Set Salt='$salt',Password='$safetypass' where Email ='$email'";
				if ($GLOBALS["config"]["User_Unlock_Recover"] == 1)
						mysqli_query($connect,"Update Users Set Enabled=1 where Email ='$email'");
				mysqli_query($connect,$query);
				$history = "Insert into Pass_History (Changed,IP,Who) Values ('".date("D M j G:i:s T Y", time())."','".getIP()."',$id)";
				mysqli_query($connect,$history);				
				sendMail($email,2,$name,"Redundancy",$pass,"Redundancy");				
				//header("Location: ./index.php?module=recover&msg=success");
			}
			else
				header("Location: ./index.php?module=recover&msg=nosuccess");
		}		
		else
		{
			header("Location: ./index.php");
		}
	}
	function setNewPassword($pEmail,$pass_old,$pass_new)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$email = mysqli_real_escape_string($connect,$pEmail);		
		$pass_new = mysqli_real_escape_string($connect,$pass_new);	
		$pass_old = mysqli_real_escape_string($connect,$pass_old);	
		if (isExisting($email,"") && strpos("<",$email) === false && login($pEmail,$pass_old,false) == true)
		{		
			if ($GLOBALS["config"]["User_Enable_Recover"] == 1){
				$pass = $pass_new;
				
				$query = mysqli_query($connect,"Select User, Email, Password, Salt from Users where Email ='$email' limit 1");
				$salt = getRandomKey(200);
				$name = "";			
				while ($row = mysqli_fetch_object($query)) {					
						$name = $row->User;
						$id = $row->ID;
				}
				$safetypass = hash('sha512',$pass.$salt."thehoursedoesnoteatchickenandbacon");	
				$query = "Update Users Set Salt='$salt',Password='$safetypass' where Email ='$email'";
				if ($GLOBALS["config"]["User_Unlock_Recover"] == 1)
						mysqli_query($connect,"Update Users Set Enabled=1 where Email ='$email'");
					
						
				mysqli_query($connect,$query);
				$history = "Insert into Pass_History (Changed,IP,Who) Values ('".date("D M j G:i:s T Y", time())."','".getIP()."',$id)";
				mysqli_query($connect,$history);							
				header("Location: ./index.php");
			}
			else
				header("Location: ./index.php?module=setpass");
		}		
		else
		{
			header("Location: ./index.php?module=setpass");
		}
	}
	function is_admin()
	{
		if (!isset($_SESSION))
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$user_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);
		$user_name = mysqli_real_escape_string($connect,$_SESSION['user_name']);
		$user_email = mysqli_real_escape_string($connect,$_SESSION['user_email']);
		$query = "Select ID, User, Email, Role from Users where Email = '$user_email' and User = '$user_name' and ID = '$user_id'";
		$mysql = mysqli_query($connect,$query);
		while ($row = mysqli_fetch_object($mysql)) {					
			if ($row->Role == 0)
				$result = true;
			else
				$result = false;
		}
		mysqli_close($connect);	
		return $result;
	}
	function user_apply_Informations()
	{
		if (!isset($_SESSION))
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$user = mysqli_real_escape_string($connect,$_SESSION['user_name']);
		$email = mysqli_real_escape_string($connect,$_SESSION['user_email']);
		$ergebnis = mysqli_query($connect,"Select ID, User, Email, Password, Salt,Storage,Role, Enabled,Failed_Logins from Users where User = '$user' or Email = '$email' limit 1") or die("Error: 018 ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($ergebnis)) {										
			$_SESSION["space"] = $row->Storage;	
			$_SESSION["role"] = $row->Role;		
		}							
		mysqli_close($connect);		
	}
	function user_check_session()
	{
		if (!isset($_SESSION))
			session_start();
		$found = false;
<<<<<<< HEAD
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		foreach($_SESSION as $key => $value)
		{			
		  if ($value != mysqli_real_escape_string($connect,$value))
			$found = true;
=======
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		for ($i = 0; $i < count($_SESSION);$i++)
		{
			if ($_SESSION[$i] != mysqli_real_escape_string($connect,$_SESSION[$i]))
				$found = true;
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
		}		
		if ($found == true)
			banUser(getIP(),$_SERVER['HTTP_USER_AGENT'],"SQLi");
		return $found;
	}
	
<<<<<<< HEAD
	
=======
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
?>