<?php
	/**
	* Kernel.Updater.class.php
	*/	
	namespace Redundancy\Kernel;
	/**
	* This file containts methods to update the system
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
	*/
	class UpdateKernel{
		private $pattern = "/(?<major>\d+).(?<minor>\d+).(?<patch>\d+)+-(?<branch>[^-]+)-(?<stage>[^-]+)-(?<update>\d+)/";
		private $updateSource = "https://raw.githubusercontent.com/squarerootfury/redundancy/%s/Includes/Kernel/Kernel.Program.class.php";
		private $updatePackage = "https://github.com/squarerootfury/redundancy/archive/%s.zip";
		/**
		* Compares to verision structures and returns, if an update is needed
		* @param array $remoteVersion the remote version
		* @param array $localVersion the local version
		* @return bool
		*/
		public function IsUpdateAvailable($remoteVersion,$localVersion){			
			if ($remoteVersion == "" || $localVersion == "")
				return false;
			if ($remoteVersion["major"] > $localVersion["major"])
				return true;
			if ($remoteVersion["minor"] > $localVersion["minor"])
				return true;
			if ($remoteVersion["patch"] > $localVersion["patch"])
				return true;
			if ($remoteVersion["update"] > $localVersion["update"])
				return true;
			return false;
		}
		/*
		* Check if an update is needed
		* @return bool if an update is needed
		*/
		public function IsUpdateNeeded(){
			$remoteVersion = $this->GetLatestVersion();
			$localVersion = $this->GetVersion();
			return $this->IsUpdateAvailable($remoteVersion,$localVersion);
		}
		/**
		* Update the system update
		* @param string $token an administrative session token
		* @return bool
		*/
		public function Update($token){
			$escapedToken = DBLayer::GetInstance()->EscapeString($token,true);
			if (!$GLOBALS["Kernel"]->UserKernel->IsActionAllowed($escapedToken,\Redundancy\Classes\PermissionSet::AllowAdministration))
				return \Redundancy\Classes\Errors::NotAllowed;

			//
			$url = sprintf($this->updatePackage,$this->GetBranch());
			$branch = $this->GetBranch();
			$targetPath = $GLOBALS["Kernel"]->FileSystemKernel->GetSystemDir(\Redundancy\Classes\SystemDirectories::Temp);
			$extractPath = $GLOBALS["Kernel"]->FileSystemKernel->GetSystemDir(\Redundancy\Classes\SystemDirectories::Temp)."update";
			$updateTargetPath = $GLOBALS["Kernel"]->FileSystemKernel->GetSystemDir(\Redundancy\Classes\SystemDirectories::Temp);
			$result =  file_put_contents($targetPath."update.zip", fopen($url,'r'));

			$zip = new \ZipArchive;
			$entries = array();
			//Ignore following files which are not needed for the retail-system
			$ignoredItems = array("/Storage/",
				"Temp/",
				"Snapshots/",
				"Includes/Kernel/Kernel.Config.class.php",
				"webclient/",
				"runTests.xml",
				"test",
				"tests/",
				".travis.yml",	
				"Language/.htaccess",
				"System.log",
				"README.md",
				"install.php",
				"redundancy-$branch"				
				);
			if ($zip->open($targetPath."update.zip") === true) {
				for($i = 0; $i < $zip->numFiles; $i++) { 
					$oldName = $zip->getNameIndex($i);		
					$newName = 	str_replace("redundancy-".$branch."/", "", $oldName);
					if ($newName == "")
						$newName = "oldName";	
					$zip->renameIndex($i,$newName ); 
					$entry = $zip->getNameIndex($i);
					$itemBlacklisted = false;
					foreach ($ignoredItems as $key => $value) {
						if ($entry == $value || strpos($entry, $value) === 0){ 
							$itemBlacklisted = true;
							//error_log("File $entry blacklisted!");
							break;
						}
					}			
					if (!$itemBlacklisted)
						$entries[] = $entry;			
				}  	
				$targetPath = __REDUNDANCY_ROOT__;
				$zip->ExtractTo($targetPath,$entries);
				$zip->close();
			}			
			$content = file_get_contents(__REDUNDANCY_ROOT__."Dump.sql");
			$queries = explode(";", $content);
			try{
				foreach ($queries as $key => $value) {
					try{

						DBLayer::GetInstance()->RunSelect($value);//$result =  $conn->query($value);
					}
					catch (\Exception $e){

					}					
				}
			}catch(\Exception $e){
				//return false;
			}
			//Cleanup
			//TODO: Cleanup the mess...
			return true;
		}
		/**
		* Get the current version (as array/ dictionary)
		* @return array | empty string, if failure
		*/
		public function GetVersion(){					
			$currentVersion = $GLOBALS["Kernel"]->Version;
			$matches;
			if (!preg_match($this->pattern,$currentVersion,$matches))
				return "";
			else
				return $matches;
		}
		/**
		* Get the current branch name
		* @return the branch name (or empty string)
		*/
		public function GetBranch(){
			$matches =$this->GetVersion();
			if($matches == "")
				return "";
			return $matches["branch"];
		}
		/**
		* Grabs the latest version from the repository
		* return an array containing the latest version
		*/
		public function GetLatestVersion(){
			//grab the content from the latest version		
			$branch = $this->GetBranch();
			$url = sprintf($this->updateSource,$branch);	
			$content  = file_get_contents($url);
			$matches;
			if (!preg_match($this->pattern,$content,$matches)){
				return "";
			}
			else{
				return $matches;
			}
		}
		/**
		* Grabs the latest version from the repository and returns it as string. 
		* return an array containing the latest version"/(?<major>\d+).(?<minor>\d+).(?<patch>\d+)+-(?<branch>[^-]+)-(?<stage>[^-]+)-(?<update>\d+)/";
		*/
		public function GetLatestVersionAsString(){
			$result = $this->GetLatestVersion();
			return sprintf("%s.%s.%s-%s-%s-%s",$result["major"],$result["minor"],$result["patch"],$result["branch"],$result["stage"],$result["update"]);
		}
		/**
		* Get the source for updating
		* @return string the source
		*/
		public function GetUpdateSource(){
			$branch = $this->GetBranch();
			$url = sprintf($this->updatePackage,$branch);	
			return $url;
		}
	}
?>