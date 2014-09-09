<?php
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
		*/
		public function __construct(){
			$GLOBALS["Router"] = $this;
			$this->controller = new UIController();
		}
		/**
		* Sets the global language object
		* @param $languageCode the language code to use (e. g. de or en)
		*/
		public function SetLanguage($languageCode){
			$args = array($languageCode);		
			$GLOBALS["Language"] = $this->DoRequest("Kernel.InterfaceKernel","SetCurrentLanguage",json_encode($args));				
		}	
		/**
		* Routes the user to the wanted view
		* @param $url the current url
		*/	
		public function Route($url){				
			//Start the SESSION-Array if needed.			
			if (!isset($_SESSION))
						session_start();
			$this->SetLanguage("de");

			if (isset($_SESSION["Token"])){
				if (isset($_GET["main"]))
					$this->controller->Main($this);	
				else if (isset($_GET["info"]))
					$this->controller->Info($this);		
				else if (isset($_GET["logout"]))
					$this->controller->LogOut($this);
				else if (isset($_GET["files"]))
					$this->controller->Files($this);	
				else if (isset($_GET["newfolder"]))
					$this->controller->NewFolder($this);							
				else
					$this->DoRedirect("main");
			}		
			else{
				if (isset($_GET["info"]))
					$this->controller->Info($this);					
				else if (isset($_GET["login"]))				
					$this->controller->LogIn($this);	
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
			$relative = str_replace("index.php","",$_SERVER["SCRIPT_NAME"]).'Includes/api.inc.php';				
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
			    CURLOPT_VERBOSE => TRUE,
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $prefix.$domain.$relative,
			    CURLOPT_USERAGENT => (isset($_SERVER['HTTP_USER_AGENT'])) ? 'Nys@'.$_SERVER['HTTP_USER_AGENT'] : 'Nys',
			    CURLOPT_POST => 1,
			    CURLOPT_POSTFIELDS => array(
				'module' => $module,
				'method' => $method,
				'args' => $args
			    )
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);						
			// Close request to clear up some resources
			curl_close($curl);			
			return json_decode($resp);
		}
		/**
		* Redirects the user to a page. POST-Data will be lost
		* @param $to the target page
		*/
		function DoRedirect($to){
			header("Location:?$to");
			exit;
		}		
	}	
?>
