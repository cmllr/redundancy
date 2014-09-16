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
			if (!isset($_POST['username'],$_POST['password'])){				
				include 'Views/LogIn.php';
			}else
			{
				$args = array($_POST['username'],$_POST['password'],true);
				
				$result = $router->DoRequest('Kernel.UserKernel','LogIn',json_encode($args));							
				if (is_numeric($result)){
					$ERROR=$GLOBALS['Language']->wrongcredentials;					
					include 'Views/LogIn.php';
				}
				else{					
					if (!isset($_SESSION['Token'])){
						$_SESSION['Token'] = $result;
						$_SESSION['Language'] = $_POST['lang'];
					}								
					$router->DoRedirect('main');
				}					
			}
		}
		/**
		* Display the LogOut-Page
		* @param $router the Router-Object to be used.
		*/
		public function LogOut($router){
			$args = array($_SESSION['Token']);
			$router->DoRequest('Kernel.UserKernel','KillSessionByToken',json_encode($args));			
			session_destroy();			
			$router->DoRedirect('login');
		}
		/**
		* Display the Main-Page
		* @param $router the Router-Object to be used.
		*/
		public function Main($router){			
			$data = $this->InjectSessionData($router);		
			$innerContent = 'StartPage.php';				
			//Set the varaiables to be injected.
			$storageStats = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetStorage',json_encode(array($_SESSION['Token'])));	
			$storageSize = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetCorrectedUnit',json_encode(array($storageStats->sizeInByte)));	
			$usedStorageSize = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetCorrectedUnit',json_encode(array($storageStats->usedStorageInByte)));	 		
			$percentage = ($storageStats->usedStorageInByte != 0) ? 100/($storageStats->sizeInByte/$storageStats->usedStorageInByte) : 0;			
			$storageInfo = $usedStorageSize.' '.$GLOBALS['Language']->of.' '.$storageSize.' '.$GLOBALS['Language']->used;		
			include 'Views/Main.php';
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
					$mediaPreview = $GLOBALS['Router']->DoRequest('Kernel.InterfaceKernel','MediaPreview',json_encode(array($filePath,"./nys/Views/Partials","preview")));
					
					$_SESSION["fileInject"] = $filePath;
					$innerContent = 'Detail.php';			
					include 'Views/Main.php';
				}	
			}					
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
			$currentDirectory = $router->DoRequest('Kernel.FileSystemKernel','GetEntryByAbsolutePath',json_encode(array($_SESSION['currentFolder'],$_SESSION['Token'])));			
			$absolutePathCurrentDirectory = $router->DoRequest('Kernel.FileSystemKernel','GetAbsolutePathById',json_encode(array($currentDirectory->Id,$_SESSION['Token'])));
			if (isset($_POST['directory'])){
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
		* Display a shared file
		* @param $router the Router-Object to be used.
		* @todo language??
		*/
		function Share($router){
			$router->SetLanguage("de");
			$entry =  $GLOBALS['Router']->DoRequest('Kernel.SharingKernel','GetEntryByShareCode',json_encode(array($_GET["c"])));
			$shareCode = $_GET["c"];
			if (is_null($entry) || is_numeric($entry))
				$router->DoRedirect("");
			$filePath = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetSystemDir',json_encode(array(0))).$entry->FilePath;
			$mediaPreview = $GLOBALS['Router']->DoRequest('Kernel.InterfaceKernel','MediaPreview',json_encode(array($filePath,"./nys/Views/Partials","preview")));
			$_SESSION["fileInject"] = $filePath;
			include 'Views/Share.php';
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
			$data = $this->InjectSessionData($router);					
			$fileHash = $_GET["f"];
			$token = $_SESSION["Token"];
			if (!is_null($token))
				$entry = $router->DoRequest('Kernel.FileSystemKernel','GetEntryByHash',json_encode(array($fileHash,$token)));		
			if (!is_null($entry)){
				ob_end_clean();	
				header("Content-Type: ".$entry->MimeType);
			    header("Content-Disposition: attachment; filename=\"".$entry->DisplayName."\"");		
				$resp = $router->DoRequest('Kernel.FileSystemKernel','GetContentOfFile',json_encode(array($fileHash,$_SESSION['Token'])));			
				echo $resp;
				exit();
			}	
			else{
				$this->DoRedirect("main");
			}		
		}
		/**
		* Display the account info
		* @param $router the Router-Object to be used.
		*/
		public function Shares($router){	
			$data = $this->InjectSessionData($router);		
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
			$data = $this->InjectSessionData($router);	
			$storageStats = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetStorage',json_encode(array($_SESSION['Token'])));	
			$storageSize = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetCorrectedUnit',json_encode(array($storageStats->sizeInByte)));	
			$usedStorageSize = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetCorrectedUnit',json_encode(array($storageStats->usedStorageInByte)));	 		
			$percentage = ($storageStats->usedStorageInByte != 0) ? 100/($storageStats->sizeInByte/$storageStats->usedStorageInByte) : 0;			
			$storageInfo = $usedStorageSize.' '.$GLOBALS['Language']->of.' '.$storageSize.' '.$GLOBALS['Language']->used;						
			$PermissionSet = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetPermissionSet',json_encode(array($_SESSION['Token'])));
			$allowPasswordChange = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],6)));
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
			$absolutePathCurrentDirectory = $_SESSION['currentFolder'];				
			if (isset($_FILES['file'])){
				$fileToUpload = array();
				$_FILES['file'] = $this->diverse_array($_FILES['file']);
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
						//TODO: Fallback handling
						/*if (!is_numeric($result) && $result)
							echo 'YEAH BITCHEZ';
						else
							echo 'ERROR $result';*/
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
			$router->SetLanguage($_SESSION['Language']);
			$args = array($_SESSION['Token']);			
			$user = $router->DoRequest('Kernel.UserKernel','GetUser',json_encode($args));		
			$data = array();
			$data['user'] = $user;
			return $data;
		}
	}