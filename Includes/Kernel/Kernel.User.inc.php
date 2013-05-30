<?php
	//User functions	
	function login($pUser,$pPass)
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
			$ergebnis = mysqli_query($connect,"Select ID, User, Email, Password, Salt,Storage,Role, Enabled,Failed_Logins from Users where User = '$user' or Email = '$user' limit 1") or die("Error: 018 ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($ergebnis)) {	
				$internalpassword = $row->Password;	
				$tries = $row->Failed_Logins;
				echo "found fails:".$tries;
				$email = $row->Email;
				$name = $row->User;
				$enabled = $row->Enabled;
				if ($internalpassword == hash('sha512',$pass.$row->Salt."thehoursedoesnoteatchickenandbacon") && $row->Enabled == 1)
				{			
					$_SESSION['user_id'] = $row->ID;
					$_SESSION['user_name'] = $row->User;
					$_SESSION['user_email'] = $row->Email;
					$_SESSION["user_logged_in"] = true;
					$_SESSION["currentdir"] = "/";	
					$_SESSION["currentdir_hashed"] = "6666cd76f96956469e7be39d750cc7d9";	
					$_SESSION["space"] = $row->Storage;	
					$_SESSION["space_used"] = 0;
					$_SESSION["role"] = $row->Role;
					//Reset Login counter;
					mysqli_query($connect,"Update Users Set Failed_logins=0 where Email ='$user' or User='$user'");
					mysqli_close($connect);					
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
	function registerUser($pUser,$pEmail,$pPass,$pPassRepeat)
	{
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
				
				$query = mysqli_query($connect,"Select User, Email, Password, Salt from Users where Email ='$email' limit 1");
				$salt = getRandomKey(200);
				$name = "";			
				while ($row = mysqli_fetch_object($query)) {					
						$name = $row->User;
				}
				$safetypass = hash('sha512',$pass.$salt."thehoursedoesnoteatchickenandbacon");	
				$query = "Update Users Set Salt='$salt',Password='$safetypass' where Email ='$email'";
				if ($GLOBALS["config"]["User_Unlock_Recover"] == 1)
						mysqli_query($connect,"Update Users Set Enabled=1 where Email ='$email'");
				mysqli_query($connect,$query);
				sendMail($email,2,$name,"Redundancy",$pass,"Redundancy");				
				header("Location: ../index.php?module=recover&msg=success");
			}
			else
				header("Location: ../index.php?module=recover&msg=nosuccess");
		}		
		else
		{
			header("Location: ../index.php");
		}
	}
?>