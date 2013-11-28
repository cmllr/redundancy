<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * Any functionality for the user (login, register etc.) is stored in this file.
	 */
	/**
	 * login login the user
	 * @param $pUser the user name or email
	 * @param $pPass the password
	 * @param $login determine if a log in should be done
	 * @return the result of the log in
	 */
	function login($pUser,$pPass,$login = true)
	{		
		//start a new session
		if (isset($_SESSION) == false)
			session_start();		
		if (strpos($pUser,"<") === false && strpos($pPass,"<") === false)
		{
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
			//Get the user and pass cleaned from any dangerous contents
			$user = mysqli_real_escape_string($connect,$pUser);
			$pass = mysqli_real_escape_string($connect,$pPass);		
			$tries = 0;
			$email ="";
			$name = "";
			$enabled = 1;
			$loginQuery = "Select ID, User, Email, Password, Salt,Storage,Role, Enabled,Failed_Logins,Session_Closed from Users where User = '$user' or Email = '$user' limit 1";
			$ergebnis = mysqli_query($connect,$loginQuery) or die("Error: Could not execute query to log in the user. Error message: ".mysqli_error($connect));
			while ($row = mysqli_fetch_object($ergebnis)) {	
				//Remember data of the current tuple
				$internalpassword = $row->Password;	
				$tries = $row->Failed_Logins;
				$email = $row->Email;
				$name = $row->User;
				$enabled = $row->Enabled;
				//Compare the given password on the database with the user input
				if ($internalpassword == hash('sha512',$pass.$row->Salt."thehoursedoesnoteatchickenandbacon") && $row->Enabled == 1)
				{			
					if ($login == true){
						//Set the current session values
						$_SESSION['user_id'] = $row->ID;
						$_SESSION['user_name'] = $row->User;
						$_SESSION['user_email'] = $row->Email;
						$_SESSION["user_logged_in"] = true;
						$_SESSION["currentdir"] = "/";	
						$_SESSION["currentdir_hashed"] = "6666cd76f96956469e7be39d750cc7d9";	
						$_SESSION["space"] = $row->Storage;	
						$_SESSION["space_used"] = 0;
						$_SESSION["role"] = $row->Role;
						$_SESSION["fs_hash"] = hash('sha512',$pass.$row->Salt.$row->Email.$pass);
						$_SESSION["Session_Closed"] = $row->Session_Closed;
						$_SESSION["begin"] = time();
						//Reset Login counter;
						$resetQuery = "Update Users Set Failed_logins=0,Session_Closed =0 where Email ='$user' or User='$user'";
						mysqli_query($connect,$resetQuery);
						mysqli_close($connect);	
					}	
					else{
						$_SESSION['user_id'] = $row->ID;
						$_SESSION['user_name'] = $row->User;
						$_SESSION['user_email'] = $row->Email;
					}
					return true;		
				}							
			}
			//Update the error counter and lock the account if needed
			if ($tries == $GLOBALS["config"]["User_Max_Fails"]){
				if ($enabled != 0){
					$disableQuery = "Update Users Set Enabled=0 where Email ='$user' or User='$user'";
					mysqli_query($connect,$disableQuery);		
					if ($GLOBALS["config"]["Program_Enable_Mail"] == 1)
						sendMail($email,3,$name,getIP2(),"","");		
				}
			}		
			$tries = $tries +1;		
			$updateTriesQuery = "Update Users Set Failed_logins=$tries where Email ='$user' or User='$user'";
			mysqli_query($connect,$updateTriesQuery);		
			mysqli_close($connect);
			return false;
		}			
	}
	/**
	 * getNewUserName create a new user name
	 * @param $pEmail the email
	 * @return a generated user name
	 */
	function getNewUserName($pEmail){
		if (isset($connect) == false)
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		//cleanup parameter
		$pEmail = mysqli_real_escape_string($connect,$pEmail);
		$result = "";
		$go = true;
		//create user with the first part of the email. If already used, add a number and increment this number again if this combination is already given
		$parts = explode("@",$pEmail);
		$result = $parts[0];
		$try = 0;			
		do{
			$ergebnis = mysqli_query($connect,"Select ID from Users where User = '$result' or Email = '$pEmail' limit 1") or die("Error: 018 ".mysqli_error($connect));
			if (mysqli_affected_rows($connect) == 0)
				$go = false;
			else{
				$try++;
				$result = $parts[i].$try;
			}
		}while($go == true);
		return $result;
	}
	/**
	 * getNewUserName create a new user name
	 * @param $pEmail the email
	 * @param $pPass the password
	 * @param $pPassRepeat the password repated
	 * @param $pSystem if the registration is done via the system
	 * @return the result of the registraiton (success/fail)
	 */
	function registerUser($pEmail,$pPass,$pPassRepeat,$pSystem = 0)
	{
		if ($pSystem == 0)
			$pUser = getNewUserName($pEmail);
		else
		{
			$pUser = $pEmail;			
		}
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
		if ($pSystem == 0)
			$email = mysqli_real_escape_string($connect,$pEmail);
		else
			$email = $user."@localhost";
		if ($pSystem == 0){
			if (strpos($email,"@") === false || strpos($email,".") === false)
				return false;
		}
		$salt = getRandomKey(200);
		$safetypass = hash('sha512',$pass.$salt."thehoursedoesnoteatchickenandbacon");	
		$registered= date("Y-m-d H:i:s",time());
		$role = $GLOBALS["config"]["User_Default_Role"];		
		$storage = $GLOBALS["config"]["User_Contingent"];
		$api_key = hash('sha512',$Email.$salt.$user."thehoursedoesnoteatchickenandbacon");	
		//Determine if the user should be activated automatically or not
		if ($GLOBALS["config"]["User_Registration_AutoDisable"] == 1)
			$enabled = 0;		
				else 
			$enabled = 1;
		if ($pSystem == 1)
			$enabled = 1;
		if ($passOK == true && $free == true){
			$ergebnis = mysqli_query($connect,"Insert into Users (User, Email,Password,Salt,Registered,Role,Storage,Enabled,API_Key,Enable_API) Values('$user','$email','$safetypass','$salt','$registered','$role',$storage,$enabled,'$api_key',1)") or die("Error: 019 ".mysqli_error($connect));
		
		}
		else
			return false;
		//Send a activation mail when the account is not activated automatically	
		if ($ergebnis == true){
			if ($GLOBALS["config"]["User_Registration_AutoDisable"] == 1 && $pSystem != 1 )
				sendMail($email,1,$user,"Redundancy",getActivationLink()."&email=".$email,"Redundancy");
			return true;
		}
		else
			return false;
	}
	/**
	 * sendMail sends a mail
	 * @param $pEmail the email
	 * @param $pMessageID the id of the message out of the database
	 * @param $arg0 argument
	 * @param $arg1 argument
	 * @param $arg2 argument
	 * @param $arg3 argument
	 */
	function sendMail($pEmail,$pMessageID,$arg0,$arg1,$arg2,$arg3)
	{
		//Start a new session if needed
		if ($GLOBALS["config"]["Program_Enable_Mail"] == 1){
			if (isset($_SESSION) == false)
				session_start();
			//Include database file
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$result = mysqli_query($connect,"Select * from Mails where  ID = '$pMessageID' limit 1") or die("Error 020 ". mysqli_error($connect));
			while ($row = mysqli_fetch_object($result)) {			
				$text = sprintf ($row->Text,$arg0,$arg1,$arg2,$arg3);
				$name = "Server";			
				$name_email = "server@".$_SERVER['SERVER_NAME'];
				//Only send the email if configured
				if ($name != "" && $name_email != "")
					mail($pEmail, "Redundancy", $text, "From: $name <$name_email>");
			}
			//close connection
			mysqli_close($connect);
		}
	}
	/**
	 * isExisting check if the user is already registered
	 * @param $pEmail the email
	 * @param $pUser the username
	 * @return true or false
	 */
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
	/**
	 * recover recover the pass with a mail
	 * @param $pEmail the email	
	 * @todo create a method if email support is not configured
	 */
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
				if ($GLOBALS["config"]["Program_Enable_Mail"])
					sendMail($email,2,$name,"Redundancy",$pass,"Redundancy");				
				header("Location: ./index.php?module=recover&msg=success");
			}
			else
				header("Location: ./index.php?module=recover&msg=nosuccess");
		}		
		else
		{
			header("Location: ./index.php");
		}
	}
	/**
	 * setNewPassword set a new password
	 * @param $pEmail the email	
	 * @param $pass_old the old password
	 * @param $pass_new the new password
	 * @param $redir redirect after changed
	 * @param $internalchange if the change is from the system or over the administration
	 * @todo create a method if email support is not configured
	 */
	function setNewPassword($pEmail,$pass_old,$pass_new,$redir = 1,$internalchange = 0)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$email = mysqli_real_escape_string($connect,$pEmail);		
		$pass_new = mysqli_real_escape_string($connect,$pass_new);	
		$pass_old = mysqli_real_escape_string($connect,$pass_old);	
		if ((isExisting($email,"") || isExisting("",$email))&& strpos("<",$email) === false && ($internalchange == 1 || login($pEmail,$pass_old,false) == true))
		{		
			if ($GLOBALS["config"]["User_Enable_Recover"] == 1 || $internalchange == 1){
				$pass = $pass_new;
				
				$query = mysqli_query($connect,"Select ID,User, Email, Password, Salt from Users where Email ='$email' or User = '$email' limit 1");
				$salt = getRandomKey(200);
				$name = "";			
				while ($row = mysqli_fetch_object($query)) {					
						$name = $row->User;
						$id = $row->ID;
				}
				$safetypass = hash('sha512',$pass.$salt."thehoursedoesnoteatchickenandbacon");	
				$query = "Update Users Set Salt='$salt',Password='$safetypass' where Email ='$email' or User = '$email' limit 1";
				if ($GLOBALS["config"]["User_Unlock_Recover"] == 1)
						mysqli_query($connect,"Update Users Set Enabled=1 where Email ='$email' or User = '$email' limit 1");
					
						
				mysqli_query($connect,$query);
				$history = "Insert into Pass_History (Changed,IP,Who) Values ('".date("D M j G:i:s T Y", time())."','".getIP()."',$id)";
				mysqli_query($connect,$history);							
				if ($redir == 1)
					header("Location: ./index.php");
			}
			else{
				if ($redir == 1)
					header("Location: ./index.php?module=setpass");
			}
		}		
		else
		{
				if ($redir == 1)
			header("Location: ./index.php?module=setpass");
		}
	}
	/**
	 * check if the logged in user is administrator
	 * @return if the user is admin
	 */
	function isAdmin()
	{
		if (!isset($_SESSION))
			session_start();
		if (isset($connect) == false)
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
	/**
	 * check if the logged in user is a guest
	 * @return true if  the user is a guest otherwise false
	 */
	function isGuest()
	{
		if (!isset($_SESSION))
			session_start();
		if (isset($connect) == false)
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		if (isset($_SESSION["user_email"]) == false)
			return true;
		if (isset($_SESSION["user_name"]) == false)
			return true;	
		if (isset($_SESSION["user_id"]) == false)
			return true;	
		$user_id = mysqli_real_escape_string($connect,$_SESSION['user_id']);
		$user_name = mysqli_real_escape_string($connect,$_SESSION['user_name']);
		$user_email = mysqli_real_escape_string($connect,$_SESSION['user_email']);
		$query = "Select ID, User, Email, Role from Users where Email = '$user_email' and User = '$user_name' and ID = '$user_id'";
		$mysql = mysqli_query($connect,$query);
		while ($row = mysqli_fetch_object($mysql)) {				
			if ($row->Role == 3)
				$result = true;
			else
				$result = false;
		}
		mysqli_close($connect);	
		return $result;
	}
	/**
	 * re-apply storage and role informations	
	 */
	function loadUserChanges()
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
	/**
	 * check the user session
	 */
	function isSessionCorrupted()
	{
		if (!isset($_SESSION))
			session_start();
		$found = false;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		foreach($_SESSION as $key => $value)
		{			
			if ($key != "template"){
			  if ($value != mysqli_real_escape_string($connect,$value))
				$found = true;
			}
		}		
		if ($found == true){
			banUser(getIP(),$_SERVER['HTTP_USER_AGENT'],"SQLi");
			log_event("Kernel.User","user_check_session","SQL injection detected");
		}
		return $found;
	}
	/**
	 * load user settings
	 */
	function loadUserSettings()
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		if (!isset($_SESSION))
		{
			exit;
		}
		$userID = $_SESSION["user_id"];
		$query = "Select * from Settings where UserID = $userID";
		$ergebnis = mysqli_query($connect,$query) or die("Error: 018 ".mysqli_error($connect));
		while ($row = mysqli_fetch_object($ergebnis)) {										
			$GLOBALS["config"]["User_NoLogout_Warning"] = $row->User_NoLogout_Warning;
			$GLOBALS["config"]["Program_Display_Icons_if_needed"] = $row->Program_Display_Icons_if_needed;
			$GLOBALS["config"]["Program_Enable_JQuery"] = $row->Program_Enable_JQuery;
			$GLOBALS["config"]["Program_Enable_Preview"] = $row->Program_Enable_Preview;	
			$GLOBALS["config"]["Program_Enable_KeyHooks"] = $row->Program_Enable_KeyHooks;		
		}							
		mysqli_close($connect);		
	}
	/**
	 * save user settings
	 */
	function saveUserSettings()
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		if (!isset($_SESSION))
		{
			exit;
		}
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$User_NoLogout_Warning = mysqli_real_escape_string($connect,$GLOBALS["config"]["User_NoLogout_Warning"]);
		$Program_Display_Icons_if_needed = mysqli_real_escape_string($connect,$GLOBALS["config"]["Program_Display_Icons_if_needed"]);
		$Program_Enable_JQuery = mysqli_real_escape_string($connect,$GLOBALS["config"]["Program_Enable_JQuery"]);
		$Program_Enable_Preview = mysqli_real_escape_string($connect,$GLOBALS["config"]["Program_Enable_Preview"]);
		$Program_Enable_KeyHooks = mysqli_real_escape_string($connect,$GLOBALS["config"]["Program_Enable_KeyHooks"]);
		$query = "Select * from Settings where UserID = $userID";
		$ergebnis = mysqli_query($connect,$query) or die("Error: 018 ".mysqli_error($connect));

		if (mysqli_affected_rows($connect) ==  0)
			$query = "Insert into Settings (UserID,User_NoLogout_Warning,Program_Display_Icons_if_needed,Program_Enable_JQuery,Program_Enable_Preview,Program_Enable_KeyHooks) values ($userID,'$User_NoLogout_Warning','$Program_Display_Icons_if_needed','$Program_Enable_JQuery','$Program_Enable_Preview','$Program_Enable_KeyHooks')";
		else			
			$query = "Update Settings SET User_NoLogout_Warning = $User_NoLogout_Warning ,Program_Display_Icons_if_needed=$Program_Display_Icons_if_needed,Program_Enable_JQuery=$Program_Enable_JQuery,Program_Enable_Preview=$Program_Enable_Preview,Program_Enable_KeyHooks=$Program_Enable_KeyHooks where UserID = $userID";
		
		$ergebnis = mysqli_query($connect,$query) or die("Error: 018 ".mysqli_error($connect));
		mysqli_close($connect);		
	}
	/**
	 * delete the user settings
	 */
	function deleteUserSettings()
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		if (!isset($_SESSION))
		{
			exit;
		}
		$userID = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$ergebnis = mysqli_query($connect,"Delete from Settings where UserID='userID' limit 1",$query) or die("Error: 018 ".mysqli_error($connect));
		mysqli_close($connect);	
	}
	/**
	 * get the user role
	 * @param $username the username or the id
	 * @return returns the role or -1
	 */
	function getUserRole($username)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$user = mysqli_real_escape_string($connect,$username);		
		$ergebnis = mysqli_query($connect,"Select ID, User, Email, Password, Salt,Storage,Role, Enabled,Failed_Logins from Users where User = '$user' or Email = '$user' limit 1") or die("Error: 018 ".mysqli_error($connect));
		$res = -1;
		while ($row = mysqli_fetch_object($ergebnis)) {		
			$res = $row->Role;		
		}							
		mysqli_close($connect);	
		return $res;
	}
	/**
	 * get the user storage
	 * @param $username the username or the id
	 * @return returns the role or -1
	 */
	function getUserStorage($username)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$user = mysqli_real_escape_string($connect,$username);		
		$ergebnis = mysqli_query($connect,"Select ID, User, Email, Password, Salt,Storage,Role, Enabled,Failed_Logins from Users where User = '$user' or Email = '$user' limit 1") or die("Error: 018 ".mysqli_error($connect));
		$res = -1;
		while ($row = mysqli_fetch_object($ergebnis)) {		
			$res = $row->Storage;		
		}							
		mysqli_close($connect);	
		return $res;
	}
	/**
	 * saves changes at the user profiles	
	 */
	function saveUserChanges($admin = true)
	{		
		if (!isset($_SESSION))
			session_start();
		$role = "";
		$user = "";
		$storage = 0;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
		if (isAdmin() || $admin == false)
		{
			if (isset($_POST["role"]) && $_POST["username_info"] != "")
			{
				$role = mysqli_real_escape_string($connect,$_POST["role"]);
				$user = mysqli_real_escape_string($connect,$_POST["username_info"]);
				$storage = mysqli_real_escape_string($connect,$_POST["storage"]);
				$newpass = mysqli_real_escape_string($connect,$_POST["user_new_pass"]);
				setUserStorage($user,$storage);				
				if ($_POST["user_new_pass"] != "")
				{					
					setNewPassword($user,$newpass,$newpass,0,1);
				}				
				setUserRole($user,$role);
				if (isset($_POST["lock"]))
					enableUser($user,true);
				else
					enableUser($user,false);
				if ($_POST["user_new_name"] != "")
					renameUser($user,mysqli_real_escape_string($connect,$_POST["user_new_name"]));	
				if ($admin == true)
					header("Location: index.php?module=admin&message=user_changes_success");
				else
					header("Location: index.php?module=account&message=user_changes_success");
			}
			else
			{
				if ($admin == true)
					header("Location: index.php?module=admin&message=user_changes_failed");
				else
					header("Location: index.php?module=account&message=user_changes_failed");
			}
		}				
	}
	/**
	 * renames the user
	 * @param $old the username (old)
	 * @param $new the new username
	 */
	function renameUser($old,$new){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		if (isExisting("",$new) == false){
			$ergebnis = mysqli_query($connect,"Update Users SET User='$new' where User = '$old' limit 1") or die("Error: 018 ".mysqli_error($connect));	
		}
	}
	/**
	 * renames the session value about the username if needed
	 */
	function renameUserSessionIfNeeded(){
		if (!isset($_SESSION))
			session_start();
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		$username = mysqli_real_escape_string($connect,$_SESSION['user_name']);
		if (isExisting("",$username) == false){
			$ergebnis = mysqli_query($connect,"Select User from  Users where ID = '$id' limit 1") or die("Error: 018 ".mysqli_error($connect));	
			while ($row = mysqli_fetch_object($ergebnis)) {		
				$_SESSION['user_name'] = $row->User;
			}	
			mysqli_close($connect);	
		}		
	}
	/**
	 * saves changes at the user profiles	
	 * @param $user the username or email
	 * @param $newstorage the new amount of the storage in MB
	 */
	function setUserStorage($user,$newstorage)
	{
		$used_storage =getUsedSpace($user); 
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$ergebnis = mysqli_query($connect,"Select Storage from  Users where User = '$user' limit 1") or die("Error: 018 ".mysqli_error($connect));	
		$res = -1;
		while ($row = mysqli_fetch_object($ergebnis)) {		
			$res = $row->Storage;		
		}	
	
		$used_storage =  $used_storage/1024/1024;			
		if ($res != $newstorage){
			if ($newstorage > $used_storage )
			{
				$ergebnis = mysqli_query($connect,"Update Users set Storage = $newstorage where User = '$user'") or die("Error: 018 ".mysqli_error($connect));	
			}
			else
			{			
				mysqli_close($connect);					
				header("Location: index.php?module=admin&message=storage_set_fail");
				exit;
			}
		}
		mysqli_close($connect);	
	}
	/**
	 * set the user role
	 * @param $username the username or the id
	 * @param $role the user role
	 * @return returns the role or -1
	 */
	function setUserRole($username,$role)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$user = mysqli_real_escape_string($connect,$username);		
		$ergebnis = mysqli_query($connect,"Update Users set Role = '$role' where User = '$user' or Email = '$user'") or die("Error: 018 ".mysqli_error($connect));
								
		mysqli_close($connect);	
	}
	/**
	 * deletes a user
	 * @param $username the username or Email or ID
	 */
	function deleteUser($username)
	{
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$userID = mysqli_real_escape_string($connect,user_get_id($username));	
		echo "user id".$userID;
		if ($userID != -1){
			$files_query = mysqli_query($connect,"Select * from Files where UserID = '$userID'   limit 1") or die(mysqli_error($connect)) ;
			
			while ($row = mysqli_fetch_object($files_query)) {
				echo "<br>Deleting ".$row->Displayname ."...";
				if ($row->Filename != $row->Displayname)
					unlink($GLOBALS["Program_Dir"]."Storage/".$row->Filename);
				echo "<br>Removing database entry...";
				mysqli_query($connect,"Delete from Files where UserID = '$userID'");			
				mysqli_query($connect,"Delete from Share where UserID = '$userID'");
				echo "<br>Removing shares entries ...";
				echo "..Done";			
			}		
			echo "<br>Deleteing user ...";
			mysqli_query($connect,"Delete from Users where ID = '$userID'");
			echo "<br>..Done";
			header("Location: ?message=user_delete_success");			
			exit;
		}		
		else
		{
			$changes_failed = true;
			header("Location: ?message=user_delete_fail");
			exit;
		}
		mysqli_close($connect);	
	}
	/**
	 * gets the user id
	 * @param $username the username or Email
	 */
	function getUserID($username)
	{
		$id = -1;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$userID = mysqli_real_escape_string($connect,$username);	
		$files_query = mysqli_query($connect,"Select * from Users where User = '$userID'  or Email = '$username' limit 1") or die(mysqli_error($connect)) ;
		
		while ($row = mysqli_fetch_object($files_query)) {
				$id = $row->ID;	
		}		
		mysqli_close($connect);	
		return $id;
	}
	/**
	 * gets the user status if enabled
	 * @param $username the username or Email
	 */
	function isUserEnabled($username)
	{
		$status = -1;
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$userID = mysqli_real_escape_string($connect,$username);	
		$files_query = mysqli_query($connect,"Select * from Users where User = '$userID'  or Email = '$username' limit 1") or die(mysqli_error($connect)) ;
		
		while ($row = mysqli_fetch_object($files_query)) {
				$status = $row->Enabled;	
		}		
		mysqli_close($connect);	
		return $status;
	}
	/**
	 * sets the user status if enabled
	 * @param $user the username or Email
	 * @param $enabled the status as boolean
	 */
	function enableUser($user,$enabled)
	{	
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$userID = mysqli_real_escape_string($connect,$user);	
		if ($enabled == true)
			$en = 1;
		else
			$en = 0;
		$files_query = mysqli_query($connect,"Update Users set Enabled = '$en' where User = '$userID' or Email = '$userID' limit 1") or die(mysqli_error($connect)) ;
		
		mysqli_close($connect);	
		
	}
	/**
	 * sets a new user api token
	 * @param $user the username 	
	 * @return the result of the operation
	 */
	function generateToken($user){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$salt = getRandomKey(200);		
		$user = mysqli_real_escape_string($connect,$user);	
		$api_key = hash('sha512',$user.$salt.$user);	
		$query = "Update Users SET API_Key='$api_key' where User = '$user' limit 1";
		mysqli_query($connect,$query);
		if (mysqli_affected_rows($connect) ==  0){		
			mysqli_close($connect);
			return false;
		}
		else{
			return true;
		}		
	}
	/**
	 * prints a list of the currently registered users with an edit button	
	 */
	function getUserList(){
		if (isAdmin()){
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
			$res = mysqli_query($connect,"Select * from Users");	
			while ($row = mysqli_fetch_object($res)){	
			
			echo "<form class=\"form-horizontal\" method=\"POST\" action=\"index.php?module=admin\">
						<div class=\"form-group\">
							<label class=\"col-lg-2 control-label\">".$row->User."</label>
							<label class=\"col-lg-3 control-label\">".$row->Registered."</label>	
								<div class=\"col-lg-3\">															
								<input type=\"hidden\" name = \"username_info\" value = '".$row->User."'>
								<input type=\"submit\" id=\"buttonDeleteUser\" class=\"btn btn-primary\"  value=\"Edit\">
								</div>
						</div>
					</form>	";		
			}
			mysqli_close($connect);
		}
	}
	/**
	 * get the api key by a user id
	 * @param $userid the numeric user id
	 * @return the key of the user or ""
	 */
	function getKeyByID($userid){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$id = mysqli_real_escape_string($connect,$userid);
		$Key = "";
		$res = mysqli_query($connect,"Select API_Key from Users where ID = '$id' limit 1");	
		while ($row = mysqli_fetch_object($res)){	
			$Key = $row->API_Key;	
		}
		mysqli_close($connect);
		return $Key;
	}
	/**
	 * get the user id by the name of the user
	 * @param $user the username or email
	 * @return the id of the user or -1
	 */
	function getIDByUsername($user){
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		$name = mysqli_real_escape_string($connect,$user);
		$ID = -1;
		$res = mysqli_query($connect,"Select ID from Users where User = '$name' or Email = '$name' limit 1");	
		while ($row = mysqli_fetch_object($res)){	
			$ID = $row->ID;	
		}
		mysqli_close($connect);
		return $ID;
	}
	/**
	 * get the user key by the name of the user
	 * @param $user the username or email
	 * @param $password the password
	 * @return the key of the user or false
	 */
	function getKeyByUsername($user,$password){
		if (login($user,$password,false) == true){	
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$name = mysqli_real_escape_string($connect,$user);
			$key = "false";
			$res = mysqli_query($connect,"Select API_Key from Users where User = '$name' or Email = '$name' limit 1");	
			while ($row = mysqli_fetch_object($res)){	
				$key = $row->API_Key;	
			}
			mysqli_close($connect);
			return $key;
		}
		else{
			return "";
		}
	}
	/**
	 * logs the user out, kills the session
	 * @param $message an optional parameter to get displayed
	 */
	function logoutUser($message = ""){
		if (isset($_SESSION) == false)
			session_start();		
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
		if ($GLOBALS["config"]["Program_Debug"] != 1){	
			echo $id;
		}
		$query = "Update Users SET Session_Closed = '1' where ID = ".$id;
		if ($GLOBALS["config"]["Program_Debug"] != 1){	
			echo $query;
		}
		$erg = mysqli_query($connect,$query) or die ("error".mysqli_error($connect));
		if ($GLOBALS["config"]["Program_Debug"] != 1){	
			echo "Result:".$erg;
		}
		mysqli_close($connect);
		//exit everything	
		session_unset();
		session_destroy();
		if ($message == "")
			header('Location: ./index.php');
		else
			header("Location: ./index.php?message=$message");
	}
	/**
	 * Check if the session is to old and should be terminated due security reasons
	 * @param $sessionBegin the begin of the session as a time object
	 * @return the result of the checking
	 */
	function checkSessionTimeout($sessionBegin){
		$datetime1 = $sessionBegin;
		$datetime2 = time();
		$interval  = abs($datetime2 - $datetime1);
		$minutes   = round($interval / 60);	
		if ($minutes < $GLOBALS["config"]["Program_Session_Timeout"] || $GLOBALS["config"]["Program_Session_Timeout"] == -1){
			return true;
		}
		else
		{
			return false;
		}
	}
?>