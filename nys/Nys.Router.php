<?php
	namespace Redundancy\Nys;
	/**
	 * PHP UI routing module
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
	 * PHP UI routing module
	 **/	
	class Router{
		/**
		* The current object of the UI Controller
		*/
		private $controller;	
		/**
		* The constructor
		* @todo grab language from settings/ config
		*/
		public function __construct(){
			$GLOBALS['Router'] = $this;
			$this->controller = new UIController();
			if (!isset($_SESSION))
				session_start();
			//GRAB from config
			$this->SetLanguage('de');
		}
		/**
		* Interacts with the cookies, creates or deletes them (if needed)
		*/
		public function CookieInteraction(){
			if (isset($_SESSION["StayLoggedIn"])){
				setcookie("SessionData", $_SESSION["Token"]);
				setcookie("SessionDataLang", $_SESSION["Language"]);
				unset($_SESSION["StayLoggedIn"]);
			}

			if (!empty($_COOKIE["SessionData"])){
				if(!isset($_GET["logout"])){
					//only set the token if it is not saved already.
					if (!isset($_SESSION["Token"]) ||empty($_SESSION["Token"])){
						$_SESSION["Token"] = $_COOKIE["SessionData"];
						$_SESSION["Language"] = $_COOKIE["SessionDataLang"];
					}					
				}
				else{
					unset($_COOKIE["SessionData"]);
					unset($_COOKIE["SessionDataLang"]);
					// empty value and expiration one hour before
					setcookie("SessionData", '', time() - 3600);
					setcookie("SessionDataLang", '', time() - 3600);		
				}
			}
		}
		/**
		* Sets the global language object
		* @param $languageCode the language code to use (e. g. de or en)
		*/
		public function SetLanguage($languageCode){
			$args = array($languageCode);		
			$GLOBALS['Language'] = $this->DoRequest('Kernel.InterfaceKernel','SetCurrentLanguage',json_encode($args));				
		}	
		/**
		* Routes the user to the wanted view
		* @param $url the current url
		*/	
		public function Route($url){				
			//Start the SESSION-Array if needed.			
			

			if (isset($_SESSION['Token']) && !empty($_SESSION["Token"])){
				if (isset($_GET['main']))
					$this->controller->Main($this);	
				else if (isset($_GET['info']))
					$this->controller->Info($this);		
				else if (isset($_GET['logout']))
					$this->controller->LogOut($this);
				else if (isset($_GET['files']))
					$this->controller->Files($this);	
				else if (isset($_GET['newfolder']))
					$this->controller->NewFolder($this);	
				else if (isset($_GET['upload']))
					$this->controller->Upload($this);	
				else if (isset($_GET['detail']))
					$this->controller->Detail($this);	
				else if (isset($_GET['download']))
					$this->controller->Download($this);		
				else if (isset($_GET['account']))
					$this->controller->Account($this);	
				else if (isset($_GET['shares']))
					$this->controller->Shares($this);
				else if (isset($_GET["zipfolder"]))
					$this->controller->DownloadZip($this);	
				else if (isset($_GET["history"]))
					$this->controller->Changes($this);	
				else if (isset($_GET["admin"]))
					$this->controller->Admin($this);									
				else
					$this->DoRedirect('main');
			}		
			else{
				if (isset($_GET['info']))
					$this->controller->Info($this);					
				else if (isset($_GET['login']))				
					$this->controller->LogIn($this);	
				else if (isset($_GET["share"]))
					$this->controller->Share($this);
				else if (isset($_GET["shared"]))
					$this->controller->SharedDownload($this);
				else if (isset($_GET["register"]))
					$this->controller->Register($this);
				else
					$this->controller->LogIn($this);		
			}									
		}		
		/**
		* POST-Request helper method
		* @param $module the module
		* @param $method the method
		* @param $args the arguments (json-decoded)
		* @return the response content
		*/
		public function DoRequest($module,$method,$args){		

			$curl = curl_init();	
			$domain = $_SERVER['HTTP_HOST'];
			$prefix = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
			$relative = str_replace('index.php','',$_SERVER['SCRIPT_NAME']).'Includes/api.inc.php';				
			// Set some options - we are passing in a useragent too here		
			curl_setopt_array($curl, array(
			    CURLOPT_VERBOSE => TRUE,
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $prefix.$domain.$relative,
			    CURLOPT_USERAGENT => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'Nys',
			    CURLOPT_POST => 1,
			    CURLOPT_POSTFIELDS => array(
					'module' => $module,
					'method' => $method,
					'args' => $args,
					'ip' => $_SERVER['REMOTE_ADDR']
			    ),			   
			    
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);	
			//When the file content is raw, dont do any json operations
			if ($method =="GetContentOfFile")
				return $resp;
			if (is_int(json_decode($resp))){	
				header('HTTP/1.1 403 Forbidden');				
				//Special handling if the file upload is used.
				if ($method=='UploadFileWrapper'){
					header('Content-type: text/plain');
					curl_close($curl);			
					exit('##R_ERR_'.$resp);
				}
			}					
			// Close request to clear up some resources
			curl_close($curl);			
			return json_decode($resp);
		}
		/**
		* Redirects the user to a page. POST-Data will be lost
		* @param $to the target page
		*/
		function DoRedirect($to){
			header('Location:?'.$to);
			exit;
		}		
	}	
?>
