<?php
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
			if (!isset($_POST["username"],$_POST["password"])){				
				include "Views/LogIn.php";
			}else
			{
				$args = array($_POST["username"],$_POST["password"],true);
				
				$result = $router->DoRequest("Kernel.UserKernel","LogIn",json_encode($args));							
				if (is_numeric($result)){
					$ERROR=$GLOBALS["Language"]->wrongcredentials;					
					include "Views/LogIn.php";
				}
				else{					
					if (!isset($_SESSION["Token"])){
						$_SESSION["Token"] = $result;
						$_SESSION["Language"] = $_POST["lang"];
					}								
					$router->DoRedirect("main");
				}					
			}
		}
		/**
		* Display the LogOut-Page
		* @param $router the Router-Object to be used.
		*/
		public function LogOut($router){
			session_destroy();
			$router->DoRedirect("login");
		}
		/**
		* Display the Main-Page
		* @param $router the Router-Object to be used.
		*/
		public function Main($router){			
			$data = $this->InjectSessionData($router);		
			$innerContent = "StartPage.php";				
			//Set the varaiables to be injected.
			$storageStats = $GLOBALS["Router"]->DoRequest("Kernel.FileSystemKernel","GetStorage",json_encode(array($_SESSION["Token"])));	
			$storageSize = $GLOBALS["Router"]->DoRequest("Kernel.FileSystemKernel","GetCorrectedUnit",json_encode(array($storageStats->sizeInByte)));	
			$usedStorageSize = $GLOBALS["Router"]->DoRequest("Kernel.FileSystemKernel","GetCorrectedUnit",json_encode(array($storageStats->usedStorageInByte)));	 		
			$percentage = ($storageStats->usedStorageInByte != 0) ? 100/($storageStats->sizeInByte/$storageStats->usedStorageInByte) : 0;			
			$storageInfo = $usedStorageSize." ".$GLOBALS["Language"]->of." ".$storageSize." ".$GLOBALS["Language"]->used;		
			include "Views/Main.php";
		}
		/**
		* Display the Info-Page
		* @param $router the Router-Object to be used.
		*/
		public function Info($router){		
			$data = $this->InjectSessionData($router);					
			$innerContent = "Info.php";			
			include "Views/Main.php";
		}
		/**
		* Display the files list
		* @param $router the Router-Object to be used.
		*/
		public function Files($router){		
			$data = $this->InjectSessionData($router);					
			$entries = array("test","test2");
			$innerContent = "Files.php";			
			include "Views/Main.php";
		}
		/**
		* Display the dialog for creating a new directory
		* @param $router the Router-Object to be used.
		*/
		public function NewFolder($router){	
			if (!isset($_SESSION["currentFolder"]))				
				$_SESSION["currentFolder"] = "/";
			$currentDirectory = $router->DoRequest("Kernel.FileSystemKernel","GetEntryByAbsolutePath",json_encode(array($_SESSION["currentFolder"],$_SESSION["Token"])));			
			$absolutePathCurrentDirectory = $router->DoRequest("Kernel.FileSystemKernel","GetAbsolutePathById",json_encode(array($currentDirectory->Id,$_SESSION["Token"])));
			if (isset($_POST["directory"])){
				$values = array();			
				if (strpos($_POST["directory"],";") !== false){
					$values = explode(";",$_POST["directory"]);
				}
				else{
					$values[] = str_replace("/", "", $_POST["directory"]);
				}
				foreach($values as $key=>$value){
					$fullPath = sprintf("$absolutePathCurrentDirectory%s/",$value);
					if (!empty($value))
					{						
						//Create the directory, but only when there is no directory with that name.
						$args = array("$value",$currentDirectory->Id,$_SESSION["Token"]);					
						$exists =  $router->DoRequest("Kernel.FileSystemKernel","IsEntryExisting",json_encode($args));	
						if (!$exists){
							$creation = $router->DoRequest("Kernel.FileSystemKernel","CreateDirectory",json_encode($args));
							if (!isset($MESSAGE))
								$MESSAGE=array();
							if (!is_numeric($creation))
								$MESSAGE[]= sprintf($GLOBALS["Language"]->createdir_success,$fullPath);
							else
								$ERROR[] = sprintf($GLOBALS["Language"]->createdir_fail,$fullPath,$creation);
						}	
						else{						
							if (!isset($ERROR))
								$ERROR=array();
							$ERROR[] = sprintf($GLOBALS["Language"]->createdir_fail,$fullPath,$exists);
						}	
					}
					else{
						if (!isset($ERROR))
								$ERROR=array();
						$ERROR[] = sprintf($GLOBALS["Language"]->createdir_fail,$fullPath,"Null");
					}
				}	  
						
			}
			$data = $this->InjectSessionData($router);			
			
			$innerContent = "NewFolder.php";			
			include "Views/Main.php";
		}
		/**
		* Inject session data for the views
		* @param $router the Router-Object to be used.
		* @return an array containg the session data
		*/
		private function InjectSessionData($router){
			$router->SetLanguage($_SESSION["Language"]);
			$args = array($_SESSION["Token"]);			
			$user = $router->DoRequest("Kernel.UserKernel","GetUser",json_encode($args));		
			$data = array();
			$data["user"] = $user;
			return $data;
		}
	}