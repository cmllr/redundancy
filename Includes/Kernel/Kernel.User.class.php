<?php
	/**
	 * @file
	 * @author  squarerootfury <me@0fury.de>	 
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
	 * User Kernel
	 */	
	namespace Redundancy\Kernel;
	class UserKernel{
		/**
		* Create a new system user, will be created using the settings User_Default_Role and User_Contingent
		* @param $loginName the username of the new user, e . g. "chuck"
		* @param $displayName the displayname of the new user, e. g. "Master of the Universe"
		* @param $mailAddress a mail adress of the user or null
		* @param $password the password of the user
		* @return an new object of Redundancy\Classes\User.class.php or an int value containing the error (taken from Redundancy\Kernel\Errors)
		*/					
		public function RegisterUser($loginName,$displayName,$mailAddress,$password){
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$escapedMailAddress = DBLayer::GetInstance()->EscapeString($mailAddress,true);
			$escapedDisplayName = DBLayer::GetInstance()->EscapeString($displayName,true);
			$escapedPassword = DBLayer::GetInstance()->EscapeString($password,true);
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select count(id) as Amount from User where loginName = '%s' or mailAddress = '%s'",$escapedLoginName,$escapedMailAddress));
			if ($dbquery[0]["Amount"] == 0){
				//Only proceed if there is no existing account with this email or loginName		
				$options = [
				    'cost' => 11,
				    'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
				];
				$safetypass = password_hash($escapedPassword, PASSWORD_BCRYPT, $options);
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
				$roles = $this->GetInstalledRoles();
				foreach($roles as $r)
				{
					if ($r->Id == $role){
						$user->Role = $r;
						break;		
					}			
				}
				if ($user->Role == null)
					return Errors::RoleNotFound;
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
					return Errors::MultipleUserAccountsFound;			
				}
			}else{
				return Errors::UserOrEmailAlreadyGiven;				
			}			
		}
		/**
		* Return the currently installed roles (e. g. admin, user, guest)
		* @return an array containing the roles or null (in case of no roles and if the query failed)
		*/
		public function GetInstalledRoles(){
			$result = null;
			$dbquery = DBLayer::GetInstance()->RunSelect("Select * from Role");
			if (is_null($dbquery))
				return null;
			foreach ($dbquery as $value){
				if (is_null($result))
					$result = array();
				$role = new \Redundancy\DataClasses\Role();
				$role->Id = $value["id"];
				$role->Description = $value["description"];
				$role->Permissions = $value["permissions"];				
				$result[] = $role;
			}
			return $result;			
		}
		/**
		* Generates an unique Token to store the session
		* @param $loginName the user's login name
		* @param $dateTime the current date and time
		* @param $ip the clients IP
		* @return a string containing the token
		*/
		private function GenerateToken($loginName,$dateTime,$ip){
			$token = Errors::TokenGenerationFailed;
			$token = md5(md5($loginName).md5($dateTime).md5($ip).md5($_SERVER['HTTP_USER_AGENT']));
			return $token;		
		}
		/**
		* Logs the user in and returns an Errorcode from Errors if failure or the session token.  Uses the session timeout from Program_Session_Timeout
		* @param $loginName the user's login name
		* @param $password the user's password
		* @param $stayLoggedIn determines if the user want's to keep logged in.
		* @return a string containing the token or an error code
		*/	
		public function LogIn($loginName,$password,$stayLoggedIn){
			$result = Errors::PasswordOrUserNameWrong;
			$passwordToCheck = "";
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$escapedPassword = DBLayer::GetInstance()->EscapeString($password,true);
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			    $ip = $_SERVER['REMOTE_ADDR'];
			}
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select Id,passwordHash from User where loginName ='%s'",$escapedLoginName));
			$sessionStartedDateTime = date("Y-m-d H:i:s",time());
			if (password_verify($escapedPassword,$dbquery[0]["passwordHash"])){
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
			return $result;
		}
		/**
		* Delete a single token from the database
		* @param $token the token to delete
		* @return the result of the deletion. If the token was not existing, the function returns false;
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
		* @return The result of the check
		*/
		public function IsSessionExisting($token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			$check = DBLayer::GetInstance()->RunSelect(sprintf("Select count(id) as Amount from Session where Token = '%s'",$escapedToken));
			if ($check[0]["Amount"] != "0")
				return true;
			else
				return false;
		}
		/**
		* Checks if the user can create a new session or an old one is still active
		* @param $loginName the username to search
		* @return The result of the check or the token
		*/
		public function IsNewSessionNeeded($loginName){
			$escapedLoginName = DBLayer::GetInstance()->EscapeString($loginName,true);
			$dbquery = DBLayer::GetInstance()->RunSelect(sprintf("Select Token,sessionStartedDateTime,sessionEndDateTime from Session inner join User u on u.Id = userId where u.loginName = '%s'",$escapedLoginName));

			//If there is no token, a new one can be created
			if (is_null($dbquery))
				return true;
			foreach ($dbquery as $value){
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				    $ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
				    $ip = $_SERVER['REMOTE_ADDR'];
				}	
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
