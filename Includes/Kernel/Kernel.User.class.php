<?php
	/**
	* Kernel.User.class.php
	*/	
	namespace Redundancy\Kernel;
	/**
	* This file contains the user kernel, which contains all needed functions for managing the users of the program.
	* @license
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
	* @author  squarerootfury <me@0fury.de>	
	* 
	* @todo implement functions to delete, edit user. Also the rights management.
	*/
	class UserKernel{
		/**
		* Hashes a given password
		* @param $password the given password
		* @return the password hash
		*/
		private function HashPassword($password){
			$options = [
				    'cost' => 11,
				    'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
				];
			return password_hash($password, PASSWORD_BCRYPT, $options);
		}
		/**
		* Create a new system user, will be created using the settings User_Default_Role and User_Contingent
		* @param $loginName the username of the new user, e . g. "chuck"
		* @param $displayName the displayname of the new user, e. g. "Master of the Universe"
		* @param $mailAddress a mail adress of the user or null
		* @param $password the password of the user
		* @return \Redundancy\Classes\User an new object of Redundancy\Classes\User.class.php or an int value containing the error (taken from Redundancy\Classes\Errors)
		*/					
		public function RegisterUser($loginName,$displayName,$mailAddress,$password){
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$escapedMailAddress = DBLayer::GetInstance()->EscapeString($mailAddress,true);
			$escapedDisplayName = DBLayer::GetInstance()->EscapeString($displayName,true);
			$escapedPassword = DBLayer::GetInstance()->EscapeString($password,true);
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select count(id) as Amount from User where loginName = '%s' or mailAddress = '%s'",$escapedLoginName,$escapedMailAddress));
			if ($dbquery[0]["Amount"] == 0){
				//Only proceed if there is no existing account with this email or loginName		
				$safetypass = $this->HashPassword($password);
				$registered= date("Y-m-d H:i:s",time());
				$role = $GLOBALS["Kernel"]->Configuration["User_Default_Role"];
				$storage = $GLOBALS["Kernel"]->Configuration["User_Contingent"]*1024*1024;
				$user = new \Redundancy\Classes\User();				
				$user->LoginName = $escapedLoginName;
				$user->DisplayName = $escapedDisplayName;
				$user->MailAddress = $escapedMailAddress;
				$user->RegistrationDateTime = $registered;
				$user->LastLoginDateTime = null;
				$user->PasswordHash = $safetypass;
				$user->IsEnabled = true;
				$user->ContingentInByte = $storage;
				$systemRoles = $this->GetInstalledRoles();			
				foreach ($systemRoles as $value){
					if ($value->Id == $role)
					{
						$user->Role = $value;					
						break;
					}
				}				
				if (is_null($user->Role))
					return \Redundancy\Classes\Errors::RoleNotFound;
				$dbinsertion = sprintf("Insert into User (loginName,displayName,mailAddress,registrationDateTime,lastLoginDateTime,passwordHash,isEnabled,contingentInByte,roleID) values ('%s','%s','%s','%s','%s','%s','%u','%u','%u')",$user->LoginName,$user->DisplayName,$user->MailAddress,$user->RegistrationDateTime,$user->LastLoginDateTime,$user->PasswordHash,$user->IsEnabled,$user->ContingentInByte,$user->Role->Id);
				DBLayer::GetInstance()->RunInsert($dbinsertion);
				//Check if the user was created
				$checkquery = sprintf("Select id from User where loginName = '%s'",$user->LoginName);
				$checkresult = DBLayer::GetInstance()->RunSelect($checkquery);
				if (count($checkresult) == 1){
					$user->ID = $checkresult[0]["id"];
					return $user;			
				}
				else{
					return \Redundancy\Classes\Errors::MultipleUserAccountsFound;			
				}
			}else{
				return \Redundancy\Classes\Errors::UserOrEmailAlreadyGiven;				
			}			
		}
		/**
		* Deletes the user after an successfull authentification
		* @todo add methods to delete the files, shares etc.
		* @param $loginName the users login name
		* @param $password the users password
		* @return bool the result of the deletion
		*/
		public function DeleteUser($loginName,$password){			
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$escapedPassword = DBLayer::GetInstance()->EscapeString($password,true);
			//Delete the sessions			
			if (!$this->Authentificate($escapedLoginName,$escapedPassword))
				return false;
			//Kill all sessions
			$dbquery = DBLayer::GetInstance()->RunDelete(sprintf("Delete from Session where userID = (Select  id from User where User.loginName = '%s')",$escapedLoginName));
			$sessioncheck = DBLayer::GetInstance()->RunSelect(sprintf("Select count(s.id) as Amount from Session s inner join User u on u.id = s.userID where u.loginName = '%s' ",$escapedLoginName));
			//If the check returns values, there must be a problem and the deletion failed					
			//Delete the user			
			$dbquery = DBLayer::GetInstance()->RunDelete(sprintf("Delete from User where loginName = '%s'",$escapedLoginName));
			$check = DBLayer::GetInstance()->RunSelect(sprintf("Select count(u.id) as Amount from User u where u.loginName = '%s' ",$escapedLoginName));
			//If the check returns values, there must be a problem and the deletion failed		
			if ($check[0]["Amount"] != 0 || $sessioncheck[0]["Amount"] != 0)
				return false;	
			return true;
		}
		/**
		* Changes the password of the user
		* @param $token the valid session token for the user
		* @param $oldPassword the old user password
		* @param $newPassword the new user password
		* @return bool the result of the change
		*/
		public function ChangePassword($token,$oldPassword,$newPassword){
			$username = $this->GetUser($token)->LoginName;
			if (is_null($username))
				return false;
			if (!$this->Authentificate($username,$oldPassword))	
				return false;
			$newHash = $this->HashPassword($newPassword);
			//Set the new password
			DBLayer::GetInstance()->RunUpdate("Update User set PasswordHash = '$newHash' where LoginName = '$username'");
			//Check if the password was set
			$check = DBLayer::GetInstance()->RunSelect(sprintf("Select count(id) as Amount from User where PasswordHash = '%s'",$newHash));
			if (is_null($check))
				return false;			
			if ($check[0]["Amount"] != "0")
				return true;
			else
				return false;	
		}
	
		/**
		* Generates a new random password
		* @todo generate an stronger password
		* @param length the length of the password. If not set, the value from User_Recover_Password_Length will be used
		* @return string the new password
		*/
		public function GeneratePassword($length = -1){
			//https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
			if ($length == -1)
				$length = $GLOBALS["Kernel"]->Configuration["User_Recover_Password_Length"];
			$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
			$pass = '';                           //password is a string
			$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
			for ($i = 0; $i < $length; $i++) {
				$n = mt_rand(0, $alphaLength);    
				$pass = $pass.$alphabet[$n];      //append a random character
			}
			return ($pass); 
		}
		/**
		* Resets a password by a mail
		* @param mailAddress the email of the account which password should be resetted
		* @todo check the systems email configuration to prevent mail sending when mail is not configured
		* @todo mail body
		* @todo Function not complete implemented
		*/
		public function ResetPasswordByMail($mailAddress){
			$mailAddress = DBLayer::GetInstance()->EscapeString($mailAddress);
			$check = DBLayer::GetInstance()->RunSelect(sprintf("Select count(id) as Amount from User where MailAddress = '%s'",$mailAddress));
			if (is_null($check))
				return false;			
			if ($check[0]["Amount"] != "0")
			{
				//Send mail
			}
			else
				return false;	
		}
		/**
		* Return the currently installed roles (e. g. admin, user, guest)
		* @return \Redundancy\Classes\Role[]|null an array containing the roles or null (in case of no roles and if the query failed)
		*/
		public function GetInstalledRoles(){
			$result = null;
			$dbquery = DBLayer::GetInstance()->RunSelect("Select * from Role");
			if (is_null($dbquery))
				return null;
			foreach ($dbquery as $value){
				if (is_null($result))
					$result = array();
				$role = new \Redundancy\Classes\Role();
				$role->Id = $value["id"];
				$role->Description = $value["description"];
				$role->Permissions = $value["permissions"];				
				$result[] = $role;
			}
			return $result;			
		}
		/**
		* Get the users role 
		* @param $loginName the user login name
		* @return \Redundancy\Classes\Role|null or (if the query failed) null
		*/
		private function GetUserRole($loginName){
			$result = null;
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select * from Role r inner join User u on u.roleID = r.id where  u.loginName = '%s' ",$escapedLoginName));
			if (is_null($dbquery))
				return null;
			foreach ($dbquery as $value){
				$role = new \Redundancy\Classes\Role();
				$role->Id = $value["id"];
				$role->Description = $value["description"];
				$role->Permissions = $value["permissions"];				
				$result = $role;
			}
			return $result;	
		}
		/**
		* Get the user object by the current session. If there is no fitting session to this token, null will be returned
		* Also, when the session is expired, this will be null
		* @param $token the current session token
		* @return \Redundancy\Classes\User|null user or, if failed, null
		*/ 
		public function GetUser($token){
			$result = null;
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select * from User u inner join Session s on s.userId = u.ID where  s.token = '%s' ",$escapedToken));
			if (is_null($dbquery))
				return null;
			foreach ($dbquery as $value){
				//only proceed if the token was valid
				if ($this->IsNewSessionNeeded($value["loginName"]) == "true")
					return null;
				$user = new \Redundancy\Classes\User();
				$user->ID = $value["id"];
				$user->LoginName = $value["loginName"];
				$user->DisplayName = $value["displayName"];
				$user->MailAddress = $value["mailAddress"];
				$user->RegistrationDateTime = $value["registrationDateTime"];
				$user->LastLoginDateTime = $value["lastLoginDateTime"];
				//@todo security?
				$user->PasswordHash = $value["passwordHash"];
				$user->IsEnabled = $value["isEnabled"];
				$user->ContingentInByte = $value["contingentInByte"];
				$user->Role = $this->GetUserRole($user->LoginName);
				$user->FailedLogins = $value["failedLogins"];
				$result = $user;
			}
			return $result;	
		}
		/**
		* Generates an unique Token to store the session
		* @param $loginName the user's login name
		* @param $dateTime the current date and time
		* @param $ip the clients IP
		* @return string a string containing the token
		*/
		private function GenerateToken($loginName,$dateTime,$ip){
			$token = \Redundancy\Classes\Errors::TokenGenerationFailed;
			$token = md5(md5($loginName).md5($dateTime).md5($ip).md5($_SERVER['HTTP_USER_AGENT']));
			return $token;		
		}
		/**
		* Do an single check of the user credentials
		* @param $loginName the user's login name
		* @param $password the user's password
		* @return bool the result of the check
		*/
		public function Authentificate($loginName,$password){
			$result = false;
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$escapedPassword = DBLayer::GetInstance()->EscapeString($password,true);
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select passwordHash from User where loginName ='%s'",$escapedLoginName));
			if (is_null($dbquery))
				return false;			
			if(password_verify($escapedPassword,$dbquery[0]["passwordHash"])){
				$result = true;
			}
			else
			{
				$result = false;		
			}		
			return $result;
		}
		/**
		* Get the clients IP
		* @returns string the IP
		*/
		private function GetIP(){
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			   	if (isset($_SERVER['REMOTE_ADDR']))
			    	$ip = $_SERVER['REMOTE_ADDR'];
			    else
			    	$ip = "127.0.0.1";
			}
			return $ip;
		}
		/**
		* Logs the user in and returns an Errorcode from Errors if failure or the session token.  Uses the session timeout from Program_Session_Timeout
		* @param $loginName the user's login name
		* @param $password the user's password
		* @param $stayLoggedIn determines if the user want's to keep logged in.
		* @return string a string containing the token or an error code
		*/	
		public function LogIn($loginName,$password,$stayLoggedIn){
			$result = \Redundancy\Classes\Errors::PasswordOrUserNameWrong;
			$passwordToCheck = "";
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$escapedPassword = DBLayer::GetInstance()->EscapeString($password,true);
			$ip =$this->GetIP();
 			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select Id,passwordHash from User where loginName ='%s'",$escapedLoginName));
			$sessionStartedDateTime = date("Y-m-d H:i:s",time());
			if (password_verify($escapedPassword,$dbquery[0]["passwordHash"])){
				$this->ResetFailedLoginsCounter($escapedLoginName);
				$this->SetLastLoginDateTime($escapedLoginName);
				$sessionNeeded = $this->IsNewSessionNeeded($escapedLoginName);
				if ($sessionNeeded != "true"){
					return $sessionNeeded;				
				}
				else{
					$token =  $this->GenerateToken($escapedLoginName,$sessionStartedDateTime,$ip);
					$userId = $dbquery[0]["Id"];
					if ($stayLoggedIn || $GLOBALS["Kernel"]->Configuration["Program_Session_Timeout"] == -1){
						$dbquery = sprintf("Insert into Session (userId,token,sessionStartedDateTime,sessionEndDateTime) values('%u','%s','%s','%s')",$userId,$token,$sessionStartedDateTime,null);
					}else{
					
						$minutes = $GLOBALS["Kernel"]->Configuration["Program_Session_Timeout"];					
						$sessionEndDateTime = date("Y-m-d H:i:s",strtotime("+$minutes minutes"));
						$dbquery = sprintf("Insert into Session (userId,token,sessionStartedDateTime,sessionEndDateTime) values('%u','%s','%s','%s')",$userId,$token,$sessionStartedDateTime,$sessionEndDateTime);
					}
					DBLayer::GetInstance()->RunInsert($dbquery);
					return $token;
				}
			}
			else{
				$this->IncreaseFailedLoginsCounter($escapedLoginName);
			}
			return $result;
		}
		/**
		* Set the last Login Date and time
		* @param $loginName the username of the logging in user
		*/
		private function SetLastLoginDateTime($loginName){
			$value = date("Y-m-d H:i:s",time());
			$loginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$query = sprintf("Update User set LastLoginDateTime = '$value' where loginName = '%s'",$loginName);	
			DBLayer::GetInstance()->RunUpdate($query);

		}
		/**
		* Increases the failure counter of failed logins
		* @param $loginName the username
		*/
		private function IncreaseFailedLoginsCounter($loginName){
			$query = sprintf("Update User set FailedLogins = FailedLogins +1 where loginName = '%s'",$loginName);	
			DBLayer::GetInstance()->RunUpdate($query);
		}
		/**
		* Resets the failure counter.
		* @param $loginName the username
		*/
		private function ResetFailedLoginsCounter($loginName){
			$query = sprintf("Update User set FailedLogins = 0 where loginName = '%s'",$loginName);	
			DBLayer::GetInstance()->RunUpdate($query);
		}
		/**
		* Delete a single token from the database
		* @param $token the token to delete
		* @return bool the result of the deletion. If the token was not existing, the function returns false;
		*/
		public function KillSessionByToken($token){
			//["fuxry","test",false ]
			if (!$this->IsSessionExisting($token))
				return false;
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			DBLayer::GetInstance()->RunDelete(sprintf("Delete from Session where Token = '%s'",$escapedToken));
			if (!$this->IsSessionExisting($token))
				return true;
		}
		/**
		* Check if a single token exists in the database;
		* @param $token the token to search
		* @return bool he result of the check
		*/
		public function IsSessionExisting($token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$check = DBLayer::GetInstance()->RunSelect(sprintf("Select count(id) as Amount from Session where Token = '%s'",$escapedToken));
			if (is_null($check))
				return false;			
			if ($check[0]["Amount"] != "0")
				return true;
			else
				return false;
		}
		/**
		* Checks if the user can create a new session or an old one is still active
		* @param $loginName the username to search
		* @return bool|string The result of the check or the token
		*/
		private function IsNewSessionNeeded($loginName){
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select Token,sessionStartedDateTime,sessionEndDateTime from Session inner join User u on u.Id = userId where u.loginName = '%s'",$escapedLoginName));

			//If there is no token, a new one can be created
			if (is_null($dbquery))
				return true;
			foreach ($dbquery as $value){
				$ip =$this->GetIP();
				$sessionStartedDateTime = date("Y-m-d H:i:s",strtotime($value["sessionStartedDateTime"]));
				$compareToken = $this->GenerateToken($escapedLoginName,$sessionStartedDateTime,$ip);			
				if ($compareToken == $value["Token"]){
					if ($value["sessionEndDateTime"] == "0000-00-00 00:00:00")
						return $compareToken;
					else{
						$currentDateTime = date("Y-m-d H:i:s",time());
						$sessionEndDateTime = date("Y-m-d H:i:s",strtotime($value["sessionEndDateTime"]	));					
						if ($currentDateTime >= $sessionEndDateTime)
							return true;
						else
							return $compareToken;			
					}
				}
			}		
			return true;	
		}				
	}
?>
