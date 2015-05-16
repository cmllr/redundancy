<?php
	namespace Redundancy\Nys;
	/**
	 * base controller
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
	 * The base controller.
	 **/
	/**
	* THe UI controller who displays the pages and does operations to prepare the data to display
	*/
	class UIController{
		/**
		* Display the LogIn-Page
		* @param $router the Router-Object to be used.
		*/
		public function LogIn($router){	
			$isRegistrationEnabled = $router->DoRequest('Kernel','GetConfigValue',json_encode(array("Enable_Register")));			
			$old = $_SERVER["REQUEST_URI"];
			$returnTo = $router->DoRequest('Kernel.InterfaceKernel','GetReturnTo',json_encode(array($old)));	
			//correct if the requested route was the logout
			if ($returnTo == "login")
				$returnTo = "main";			
			if (!isset($_POST['username'],$_POST['password'])){							
				include 'Views/LogIn.php';
			}else
			{
				$args = array($_POST['username'],$_POST['password'],isset($_POST["stayloggedin"]));
				
				$result = $router->DoRequest('Kernel.UserKernel','LogIn',json_encode($args));

				if (is_numeric($result)){
					$ERROR=$GLOBALS['Language']->wrongcredentials;					
					include 'Views/LogIn.php';
				}
				else{					
					if (!isset($_SESSION['Token'])){
						if (isset($_POST["stayloggedin"]) && $_POST["stayloggedin"] == "true")
							$_SESSION["StayLoggedIn"] = true;
						$_SESSION['Token'] = $result;
						$_SESSION['Language'] = $_POST['lang'];
					}											
					$router->DoRedirect($_POST["returnTo"]);
				}					
			}
		}
		/**
		* Display the password reset request page when the user resets the password using mail
		* @param $router the Router-Object to be used.
		*/
		public function RequestPass($router){
			if (isset($_POST["email"])){
				//request the pass
				$got = $router->DoRequest('Kernel.UserKernel','ResetPasswordByMail',json_encode(array($_POST["email"])));		
				if ((bool)$got){
					$MESSAGE = $GLOBALS["Language"]->CheckMail;
				}
				else{
					$ERROR = $GLOBALS["Language"]->wronginput;
				}
			}
			include 'Views/RequestPassword.php';
		}
		/**
		* Display the password reset page when the user resets the password using mail
		* @param $router the Router-Object to be used.
		*/
		public function ResetPass($router){
			if (!isset($_GET["token"])){
				$router->DoRedirect("login");
			}
			else{
				$isValid = $router->DoRequest('Kernel.UserKernel','IsResetTokenValid',json_encode(array($_GET["token"])));
				if (!(bool)$isValid){
					$router->DoRedirect("login");
				}
				if (isset($_POST["password"]) && isset($_POST["passwordrepeat"])){
					if ($_POST["password"] == $_POST["passwordrepeat"]){
						//DO it
						$reset = $router->DoRequest('Kernel.UserKernel','ResetPasswordByMailToken',json_encode(array($_GET["token"],$_POST["password"])));			
						if ((bool)$reset == true){
							$MESSAGE = $GLOBALS["Language"]->setpass_success;
						}
						else{
							$ERROR = $GLOBALS["Language"]->wronginput;
						}
					}
					else
					{
						$ERROR = $GLOBALS["Language"]->wronginput;
					}
				}
				include 'Views/Reset.php';
			}			
		}
		/**
		* Does the configuration
		* @param $router the router object
		*/
		public function Register($router){
			$isRegistrationEnabled = $router->DoRequest('Kernel','GetConfigValue',json_encode(array("Enable_Register")));				
			//Display the login screen and a error if the regsitration is disabled.
			if ($isRegistrationEnabled == false)
			{
				$ERROR=$GLOBALS['Language']->Register_disabled;	
				include 'Views/LogIn.php';	
				return;
			}
			//Only proceed if every value is set.
			if (!isset($_POST['username'],$_POST['password'],$_POST["email"],$_POST["passwordrepeat"],$_POST["name"])){				
				include 'Views/Register.php';
			}
			else
			{		
				//Check if every data field has a value	
				$isEveryValueSet = !empty($_POST['username'])&& !empty($_POST['password'])&& !empty($_POST["email"])&& !empty($_POST["passwordrepeat"]) && !empty($_POST["name"]);
				if (!$isEveryValueSet){
					$ERROR=$GLOBALS['Language']->RegisterMissingValues;	
					include 'Views/Register.php';	
				}else{
					if ($_POST["password"] != $_POST["passwordrepeat"]){
						$ERROR=$GLOBALS["Language"]->RegisterPasswordsDifferent;
						include 'Views/Register.php';	
					}
					else{
						$args = array($_POST['username'],$_POST["name"],$_POST['email'],$_POST['password'],true);
						//Register the User!
						$result = $router->DoRequest('Kernel.UserKernel','RegisterUser',json_encode($args));							
						if (is_numeric($result)){
							$ERROR="R_ERR_".$result;				
							include 'Views/Register.php';
						}
						else{					
							$MESSAGE=$GLOBALS['Language']->RegisterSuccess;					
							include 'Views/LogIn.php';
						}	
					}
				}										
			}
		}


		/**
		* Display the LogOut-Page
		* @param $router the Router-Object to be used.
		*/
		public function LogOut($router){
			if (isset($_SESSION["Token"])){
				$args = array($_SESSION['Token']);
				$router->DoRequest('Kernel.UserKernel','KillSessionByToken',json_encode($args));			
				session_destroy();	
			}		
			$router->DoRedirect('login');
		}
		/**
		* Display the Main-Page
		* @param $router the Router-Object to be used.
		* @param string $e an error to get displayed
		*/
		public function Main($router,$e = ""){	
			$router->DoRedirect('files');
			return;					
		}
		/**
		* Display the Info-Page
		* @param $router the Router-Object to be used.
		*/
		public function Info($router){		
			$data = $this->InjectSessionData($router);					
			$innerContent = 'Info.php';			
			include 'Views/Main.php';
		}
		/**
		* Display the Info-Page
		* @param $router the Router-Object to be used.
		*/
		public function Banned($router){		
			$ip = $GLOBALS["Router"]->DoRequest("Kernel.UserKernel","GetIP",json_encode(array()));
			include 'Views/Partials/Banned.php';
		}
		/**
		* Display the file info-Page
		* @param $router the Router-Object to be used.
		*/
		public function Detail($router){		
			$data = $this->InjectSessionData($router);	
			if (!isset($_GET["f"]) || $_GET["f"] == "")
				$this->Files($router);
			else{
				$file = $_GET["f"];
				$entry = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetEntryByHash',json_encode(array($_GET["f"],$_SESSION['Token'])));
				//if the entry is null, it may be shared..
				if (is_null($entry))
					$entry = $GLOBALS["Router"]->DoRequest("Kernel.SharingKernel","GetSharedEntry",json_encode(array($file,$_SESSION['Token'])));
				if (is_null($entry) || is_numeric($entry)){
					$router->DoRedirect('files');
				}
				else{
					$filePath = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetSystemDir',json_encode(array(0))).$entry->FilePath;
					$mediaPreview = $GLOBALS['Router']->DoRequest('Kernel.InterfaceKernel','MediaPreview',json_encode(array($filePath,"./nys/Views/Partials","img-responsive img-preview")));
					$filenameParts = $GLOBALS['Router']->DoRequest('Kernel.InterfaceKernel','GetEllipsedDisplayName',json_encode(array($entry->DisplayName))); 
					$_SESSION["fileInject"] = $filePath;
					if (is_numeric($mediaPreview)){
						$MESSAGE= "R_ERR_".$mediaPreview;
						$mediaPreview = "";
					}

					$innerContent = 'Detail.php';			
					include 'Views/Main.php';
				}	
			}					
		}
		/**
		* Displays the search results
		* @param $router the router object
		*/
		public function Search($router){
			$data = $this->InjectSessionData($router);	
			if (!isset($_POST["Search"]))
				$router->DoRedirect('main');
			$results = $GLOBALS["Router"]->DoRequest("Kernel.FileSystemKernel","SearchFileSystem",json_encode(array($_POST["Search"],$_SESSION["Token"])));
			$searchTerm = $_POST["Search"];
			$innerContent = 'Search.php';
			include 'Views/Main.php';	
		}
		public function Settings($router){
			$data = $this->InjectSessionData($router);	
			$allowed  = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],12)));
			if (!$allowed)
				$router->DoRedirect("main",true);
			$settings = $GLOBALS["Router"]->DoRequest("Kernel.UserKernel","GetDefaultSettingsSet",json_encode());
			if (isset($_POST["submitted"])){
				//Save the settings
				foreach ($settings as $key => $value) {
					$valueToStore = null;
					if (isset($_POST[$value->Name])){
						//The value was set
						$valueToStore = $_POST[$value->Name];									
						$valueToStore = ($valueToStore == "on") ?  "true" : "false";
					}
					else{
						//the value was deselected
						$valueToStore = $value->Value;
					}
					$stored = $GLOBALS["Router"]->DoRequest("Kernel.UserKernel","SetUserSetting",json_encode(array($value->Name,$valueToStore,$_SESSION["Token"])));

				}
			}
			
			
			$innerContent = 'Settings.php';
			include 'Views/Main.php';	
		}
		/**
		* Display the files list
		* @param $router the Router-Object to be used.
		*/
		public function Admin($router){		
			$data = $this->InjectSessionData($router);		
			$allowed  = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],9)));
			if (!$allowed)
				$router->DoRedirect("main",true);
			$settings = $GLOBALS['Router']->DoRequest('Kernel.SystemKernel','GetSettings',json_encode(array()));
			if (isset($_POST["username"])){
				if (isset($_GET["t"]) && $_GET["t"] == "d"){
					//Delete the account
					$result = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','DeleteUserByAdminPanel',json_encode(array($_POST["username"],$_SESSION['Token'])));
					
					if ($result == 1 && $result == true)
						$MESSAGE = $GLOBALS["Language"]->UserDeleted;
					else
						$ERROR = "R_ERR_".$result;
				}
				else if (isset($_GET["t"]) && $_GET["t"] == "e"){
					//Edit user
					if (!isset($_POST["group"])){
						//Get
						$result = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetUserByAdminPanel',json_encode(array($_POST["username"],$_SESSION['Token'])));
						
						if (is_null($result) || is_numeric($result)){
							$ERROR = (is_null($result)) ? "R_ERR_NULL" : "R_ERR_".$result;
						}
						else{
							$maxStorage = $GLOBALS['Router']->DoRequest('Kernel','GetConfigValue',json_encode(array("Max_User_Storage")));	
							$user = $result;
						}
					}
					else{
						//Set
						$args = array($_SESSION["Token"],$_POST["username"],$_POST["displayname"],(isset($_POST["enabled"])) ? $_POST["enabled"] : false,$_POST["contingent"]*1024,$_POST["newPassword"],$_POST["group"]);
						$result = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','SetUserByAdminPanel',json_encode($args));
						$result = ($result == 1) ? true : false;
						if ($result != true){
							$ERROR = "R_ERR_39";
						}
						else{
							$MESSAGE = $GLOBALS["Language"]->user_changes_success;
						}
					}
				}
			}
			else if (isset($_POST["group"])){
				$group=$GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetRoleByName',json_encode(array($_POST["group"])));
				if (is_numeric($group) && $group == 40){
					//the group is new
					$group = new \stdClass();
					$group->Description = $_POST["group"];
					$group->IsDefault = false;
					$permissions = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetPermissionValues',json_encode(array()));
					foreach ($permissions as $key => $value) {
						$group->Permissions[$value] = "0";
					}
				}
				else{
					//group is existing
					$permissions = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetPermissionValues',json_encode(array()));
					$permissionsValues = $group->Permissions;
					$group->Permissions = array();
					for ($i=0; $i < count($permissions); $i++) { 
						$group->Permissions[$permissions[$i]] = $permissionsValues[$i];	
					}
				}
			}
			else if (isset($_POST["groupname"])){
				//Store or update the new or old group
				//Build the permission set
				$permissions = "";
				$permissionsToBeSet = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetPermissionValues',json_encode(array()));
				foreach ($permissionsToBeSet as $key => $value) {
					if (isset($_POST[$value])){
						$permissions .= "1";
					}
					else{
						$permissions .= "0";	
					}
				}
				$result = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','UpdateOrCreateGroup',json_encode(array($_POST["groupname"],$_POST["groupid"],$permissions,$_SESSION["Token"])));
				//Update default user group
				if (isset($_POST["IsDefault"])){
					$default = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','SetAsDefaultGroup',json_encode(array($_POST["groupname"],$_SESSION["Token"])));
				}
				
				if (is_numeric($result) && $result == 0 || (isset($default) && !$default))
					$ERROR = "R_ERR_41";
				else
					$MESSAGE = $GLOBALS["Language"]->GroupEditedSuccess;
			}
			else if (isset($_POST["groupnameToDelete"])){
				$result = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','DeleteGroup',json_encode(array($_POST["groupnameToDelete"],$_SESSION["Token"])));
			
				if (is_numeric($result))
					$ERROR = "R_ERR_".$result;
				else if ($result == "")
					$ERROR = "R_ERR_42";
				else
					$MESSAGE = $GLOBALS["Language"]->GroupDeleted;
			}
			else if (isset($_POST["iptounlock"]) && !empty($_POST["iptounlock"])){
				$result = $GLOBALS['Router']->DoRequest('Kernel.SystemKernel','UnBan',json_encode(array($_POST["iptounlock"],$_SESSION["Token"])));
				if ($result == true)
					$MESSAGE = $GLOBALS["Language"]->UnlockIPOk;
				else	
					$ERROR = $GLOBALS["Language"]->UnlockIPFail;
			}
			else if (isset($_POST["settings"])){			
				foreach ($settings as $key => $value) {
					if (isset($_POST[$value->Name])){
						//Setting was set						
						$valToSet = ($value->Type =="Boolean") ? "true" : $_POST[$value->Name];
						$r = $GLOBALS['Router']->DoRequest('Kernel.SystemKernel','SetSetting',json_encode(array($_SESSION['Token'],$value->Name,$valToSet)));	
					}
					else
					{					
						$r = $GLOBALS['Router']->DoRequest('Kernel.SystemKernel','SetSetting',json_encode(array($_SESSION['Token'],$value->Name,"false")));	
					}
				}
				//Grab the results
				$settings = $GLOBALS['Router']->DoRequest('Kernel.SystemKernel','GetSettings',json_encode(array()));
			
			}
			else if (isset($_POST["newUserPass"])){
				var_dump($_POST);
			}
			$ips = $GLOBALS['Router']->DoRequest('Kernel.SystemKernel','GetBannedIPs',json_encode(array($_SESSION['Token'])));
			$innerContent = 'Admin.php';	
			include 'Views/Main.php';
		}
		/**
		* Display the update dialog
		* @param $router the Router-Object to be used.
		*/
		public function Update($router){
			$data = $this->InjectSessionData($router);		
			$allowed  = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],9)));
			if (!$allowed)
				$router->DoRedirect("main",true);
			$remoteVersion=  $router->DoRequest("Kernel.UpdateKernel","GetLatestVersionAsString",json_encode(array()));		
			if (isset($_GET["go"])){
				//Start the update, will be started over the view
			}
			else{
				$localVersion = $router->DoRequest("Kernel","GetShortenedVersion",json_encode(array()));
				$updateState =  $router->DoRequest("Kernel.UpdateKernel","IsUpdateNeeded",json_encode(array()));
				$updateSource =  $router->DoRequest("Kernel.UpdateKernel","GetUpdateSource",json_encode(array()));
			}
			$innerContent = 'Update.php';
			include 'Views/Main.php';
		}
		/**
		* Display the files list
		* @param $router the Router-Object to be used.
		*/
		public function Files($router){		
			$data = $this->InjectSessionData($router);					
			$entries = array('test','test2');
			$innerContent = 'Files.php';	
			if (isset($_GET["d"]))
				$_SESSION["currentFolder"] = $_GET["d"];		
			include 'Views/Main.php';
		}
		/**
		* Display the dialog for creating a new directory
		* @param $router the Router-Object to be used.
		*/
		public function NewFolder($router){	
			if (!isset($_SESSION['currentFolder']))				
				$_SESSION['currentFolder'] = '/';		
			if (!$GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],1))))
			{
				$MESSAGE="R_ERR_15";
				$data = $this->InjectSessionData($router);
				$innerContent = 'Files.php';			
				include 'Views/Main.php';
				return;
			}
			$currentDirectory = $router->DoRequest('Kernel.FileSystemKernel','GetEntryByAbsolutePath',json_encode(array($_SESSION['currentFolder'],$_SESSION['Token'])));			
			$absolutePathCurrentDirectory = $router->DoRequest('Kernel.FileSystemKernel','GetAbsolutePathById',json_encode(array($currentDirectory->Id,$_SESSION['Token'])));
			if (isset($_POST['directory'])){
				$this->InjectSessionData($router);		
				$values = array();			
				if (strpos($_POST['directory'],';') !== false){
					$values = explode(';',$_POST['directory']);
				}
				else{
					$values[] = str_replace('/', '', $_POST['directory']);
				}
				foreach($values as $key=>$value){
					$fullPath = sprintf($absolutePathCurrentDirectory."%s/",$value);
					if (!empty($value))
					{						
						//Create the directory, but only when there is no directory with that name.
						$args = array($value,$currentDirectory->Id,$_SESSION['Token']);					
						$exists =  $router->DoRequest('Kernel.FileSystemKernel','IsEntryExisting',json_encode($args));	
						if (!$exists){
							$creation = $router->DoRequest('Kernel.FileSystemKernel','CreateDirectory',json_encode($args));
							if (!isset($MESSAGE))
								$MESSAGE=array();
							if (!is_numeric($creation))
								$MESSAGE[]= sprintf($GLOBALS['Language']->createdir_success,$fullPath);
							else
								$ERROR[] = sprintf($GLOBALS['Language']->createdir_fail,$fullPath,$creation);
						}	
						else{						
							if (!isset($ERROR))
								$ERROR=array();
							$ERROR[] = sprintf($GLOBALS['Language']->createdir_fail,$fullPath,$exists);
						}	
					}
					else{
						if (!isset($ERROR))
								$ERROR=array();
						$ERROR[] = sprintf($GLOBALS['Language']->createdir_fail,$fullPath,'Null');
					}
				}	  
						
			}
			$data = $this->InjectSessionData($router);			
			
			$innerContent = 'NewFolder.php';			
			include 'Views/Main.php';
		}
		function diverse_array($vector) { 
		    $result = array(); 
		    foreach($vector as $key1 => $value1) 
		        foreach($value1 as $key2 => $value2) 
		            $result[$key2][$key1] = $value2; 
		    return $result; 
		} 		
		/**
		* Display the file changes site.
		* @param $router the Router-Object to be used.
		*/
		function Changes($router){
			$data = $this->InjectSessionData($router);	
			$entries = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetLastChangesOfFileSystem',json_encode(array($_SESSION["Token"])));
			$tmpEntries = array();
			if (count($entries) != 0){
				foreach ($entries as $key => $value) {
					if (!isset($tmpEntries[$value->Day])){
						$tmpEntries[$value->Day] = array();
					}
					$tmpEntries[$value->Day][] = array($value->displayName,$value->DayTime,$value->Source,$value->FilePath,$value->id,$value->Hash);
				}
			}
			$entries = $tmpEntries;
			$innerContent = "Changes.php";
			include 'Views/Main.php';
		}
		/**
		* Display a shared file
		* @param $router the Router-Object to be used.
		* @todo language??
		*/
		function Share($router){
			$isAllowed = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],10)));
			if (!$isAllowed)
			{
				$ERROR = "R_ERR_15";
				$innerContent ="Files.php";
				include "Views/Main.php";
				return;
			}

			//$router->SetLanguage("de");
			$entry =  $GLOBALS['Router']->DoRequest('Kernel.SharingKernel','GetEntryByShareCode',json_encode(array($_GET["c"])));
			$shareCode = $_GET["c"];
			if (is_null($entry) || is_numeric($entry))
				$router->DoRedirect("");
			//folder handling
			if (is_null($entry->FilePath)){
				echo "Folder.";
				return;
			}
			$filePath = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetSystemDir',json_encode(array(0))).$entry->FilePath;
			$mediaPreview = $GLOBALS['Router']->DoRequest('Kernel.InterfaceKernel','MediaPreview',json_encode(array($filePath,"./nys/Views/Partials","preview")));
			$_SESSION["fileInject"] = $filePath;
			
			if (!isset($_SESSION["Token"])){
				//If the user is not logged in -> Display share dialog
				include 'Views/Share.php';
			}
			else{
				$data = $this->InjectSessionData($router);
				//display the regular share dialog included in the main frame
				$innerContent = "Share.php";
				include "Views/Main.php";
			}
		}
		/**
		* Prepare the download of a shared file
		* @param $router the Router-Object to be used.
		*/
		function SharedDownload($router){
			$entry =  $GLOBALS['Router']->DoRequest('Kernel.SharingKernel','GetEntryByShareCode',json_encode(array($_GET["c"])));		
			if (!is_null($entry) && !is_numeric($entry)){
				$fileHash = $entry->Hash;
				ob_end_clean();	
				header("Content-Type: ".$entry->MimeType);
			    header("Content-Disposition: attachment; filename=\"".$entry->DisplayName."\"");				
				if (is_null($entry))
					return \Redundancy\Classes\Errors::EntryNotExisting;		   
			   	$dir = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetSystemDir',json_encode(array(0)));
			    echo file_get_contents($dir.$entry->FilePath);	
				exit();
			}	
			else{
				$router->DoRedirect("main");
			}		
		}
		/**
		* Prepare the download
		* @param $router the Router-Object to be used.
		*/
		public function Download($router){	
			if (!is_null($_SESSION["Token"]))	
				$data = $this->InjectSessionData($router);					
			$fileHash = $_GET["f"];
			$token = $_SESSION["Token"];			
			if (!is_null($token))
				$entry = $router->DoRequest('Kernel.FileSystemKernel','GetEntryByHash',json_encode(array($fileHash,$token)));		
			if (!is_null($entry)){
				ob_end_clean();	
				header("Content-Type: ".$entry->MimeType);
			    header("Content-Disposition: inline; filename='".$entry->DisplayName."'");		
				$resp = $router->DoRequest('Kernel.FileSystemKernel','GetContentOfFile',json_encode(array($fileHash,$token)));			
				echo $resp;
				exit();
			}	
			else{
				$router->DoRedirect("main");
			}		
		}
		/**
		* Download a zipped folder (only works when already zipped and the session is the one of the owner)
		* @param route $router object
		*/
		public function DownloadZip($router){			
			if (!isset($_GET["d"]))
				$router->DoRedirect("main");
			$folderPath= urldecode(str_replace("/", "",$_GET["d"]));
			$tempPath = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetSystemDir',json_encode(array(1)));
			$entry =  $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetEntryByAbsolutePath',json_encode(array($_GET["d"],$_SESSION["Token"])));
			
			$zipCreation = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','StartZipCreation',json_encode(array($entry->Id,$_SESSION["Token"],$entry->ParentID)));
			//($id,$token,$rootpath)
		
			if (file_exists($tempPath.$zipCreation.".zip")){			
				ob_end_clean();	

				header("Content-type: application/zip");

				header("Content-Disposition: attachment; filename='".$entry->DisplayName.".zip'");	
		   		readfile($tempPath.$zipCreation.".zip");
				unlink($tempPath.$zipCreation.".zip");
				
			}	
			else{
				//$router->DoRedirect("main");
				echo $tempPath.$zipCreation."zip";
			}		
		}
		/**
		* Display the shares info
		* @param $router the Router-Object to be used.
		*/
		public function Shares($router){	
			$data = $this->InjectSessionData($router);	
			$isAllowed = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],10)));
			if (!$isAllowed)
			{
				$ERROR = "R_ERR_15";
				$innerContent ="Files.php";
				include "Views/Main.php";
				return;
			}	
			//$GLOBALS['Router']->DoRequest('Kernel.SharingKernel','ShareToUser',json_encode(array("/PerfTests/",84,$_SESSION['Token'])));
			if (isset($_GET["d"])){
				if (isset($_GET["c"])){
					//DeleteCodeShare($code,$token){
					$result = $GLOBALS['Router']->DoRequest('Kernel.SharingKernel','DeleteCodeShare',json_encode(array($_GET["c"],$_SESSION['Token'])));
					if (!is_numeric($result))
						$MESSAGE=$GLOBALS["Language"]->ShareWasDeleted;
					else if (is_numeric($result))
						$ERROR="R_ERR_".$result;
					else
						$ERROR="R_ERR_27";
				}
			}
			//RefreshShareLink
			if (isset($_GET["r"])){
				if (isset($_GET["c"])){
					//DeleteCodeShare($code,$token){
					$result = $GLOBALS['Router']->DoRequest('Kernel.SharingKernel','RefreshShareLink',json_encode(array($_GET["c"],$_SESSION['Token'])));
					if (!is_numeric($result))
						$MESSAGE=$GLOBALS["Language"]->ShareCodeWasRefreshed;
					else if (is_numeric($result))
						$ERROR="R_ERR_".$result;
					else
						$ERROR="R_ERR_27";
				}
			}
			$shares = $GLOBALS['Router']->DoRequest('Kernel.SharingKernel','GetSharesOfUser',json_encode(array($_SESSION['Token'],1)));
			$sharesToMe = 	$GLOBALS['Router']->DoRequest('Kernel.SharingKernel','GetSharesOfUser',json_encode(array($_SESSION['Token'],0)));		
			$innerContent = "Shares.php";
			include 'Views/Main.php';
		}
		/**
		* Display the account info
		* @param $router the Router-Object to be used.
		*/
		public function Account($router){
			$data = $this->InjectSessionData($router);		
			$permissionNames = 	$GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetListOfInstalledPermissions',json_encode(array($_SESSION['Token'])));
			if (isset($_POST["password"])){
				
				if ($_POST["password"] != $_POST["repeatpassword"] || $_POST["password"]=="" || $_POST["repeatpassword"] == "")
					$ERROR=$GLOBALS['Language']->PasswordNotChanged;
				else{
					//$token,$oldPassword,$newPassword
					$res = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','ChangePassword',json_encode(array($_SESSION['Token'],$_POST["oldpassword"],$_POST["repeatpassword"])));
					if ($res == true)
						$MESSAGE=$GLOBALS['Language']->PasswordChanged;
					else 
						$ERROR=$GLOBALS['Language']->PasswordNotChanged;
				}
			}	
			else if (isset($_POST["deletepassword"])){
				$deleteResult = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','DeleteUser',json_encode(array($data["user"]->LoginName,$_POST["deletepassword"])));
				if (!$deleteResult){
					$ERROR=$GLOBALS['Language']->Delete_Account_Fail;
				}	
				else{
					$this->LogOut($router);
				}
			}
			$storageStats = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetStorage',json_encode(array($_SESSION['Token'])));	
			$storageSize = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetCorrectedUnit',json_encode(array($storageStats->sizeInByte)));	
			$usedStorageSize = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetCorrectedUnit',json_encode(array($storageStats->usedStorageInByte)));	 		
			$percentage = ($storageStats->usedStorageInByte != 0) ? 100/($storageStats->sizeInByte/$storageStats->usedStorageInByte) : 0;			
			$storageInfo = $usedStorageSize.' '.$GLOBALS['Language']->of.' '.$storageSize.' '.$GLOBALS['Language']->used;						
			$PermissionSet = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetPermissionSet',json_encode(array($_SESSION['Token'])));
			$allowPasswordChange = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],6)));
			$allowAccountDelete  = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],5)));
			$innerContent = "Account.php";
			include 'Views/Main.php';
		}
		
		/**
		* Display the files list
		* @param $router the Router-Object to be used.
		*/
		public function Upload($router){		
			$data = $this->InjectSessionData($router);	
			if (!isset($_SESSION['currentFolder']))						
				$_SESSION['currentFolder'] = '/';;
			if (!$GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],0))))
			{
				$MESSAGE="R_ERR_15";
				$data = $this->InjectSessionData($router);
				$innerContent = 'Files.php';			
				include 'Views/Main.php';
				return;
			}
			$absolutePathCurrentDirectory = $_SESSION['currentFolder'];				
			if (isset($_FILES['file'])){
				$fileToUpload = array();
				error_log(print_r($_FILES,true));
				//$_FILES['file'] = $this->diverse_array($_FILES['file']);
				if (!isset($_FILES['file']['name'])){
					for ($i =0 ; $i < count($_FILES['file']);$i++){
						$fileToUpload[] = $_FILES['file'][$i];
					}			
				}
				else{
					$fileToUpload[] = $_FILES['file'];
				}
				for ($i = 0; $i < count($fileToUpload);$i++){

					$value = $fileToUpload[$i];
					$file = $value;
					$oldPath = $value['tmp_name'];						
					move_uploaded_file($oldPath,$oldPath.'REDUNDANCY');
					$file['tmp_name'] = $oldPath.'REDUNDANCY';		

					$entry = $router->DoRequest('Kernel.FileSystemKernel','GetEntryByAbsolutePath',json_encode(array($_SESSION["currentFolder"],$_SESSION['Token'],json_encode($file))));

					if (isset($value)){						
						$result =  $router->DoRequest('Kernel.FileSystemKernel','UploadFileWrapper',json_encode(array($entry->Id,$_SESSION['Token'],json_encode($file))));	
						
						if (!is_numeric($result) && $result){
							error_log($result);
							http_response_code(200);
							echo 'YEAH BITCHEZ';

						}
						else{
							error_log($result);
							http_response_code(403);
							echo 'ERROR $result';
						}		
						exit();	
					}
					
				}
			}						
			$innerContent = 'Upload.php';			
			include 'Views/Main.php';
		}
		/**
		* Inject session data for the views
		* @param $router the Router-Object to be used.
		* @return an array containg the session data
		*/
		private function InjectSessionData($router){			
			$router->SetLanguage(isset($_SESSION['Language']) ? $_SESSION['Language'] :  $GLOBALS["Language"]);
			$args = array($_SESSION['Token']);			
			$user = $router->DoRequest('Kernel.UserKernel','GetUser',json_encode($args));		
			$data = array();
			$data['user'] = $user;
			$data['maxUploadSize'] = $router->DoRequest('Kernel.SystemKernel','GetMaxUploadSize',json_encode(array(ini_get("upload_max_filesize"),ini_get("post_max_size"))));	
			
			if (is_null($user))
				$router->DoRedirect("login");
			return $data;
		}
	}