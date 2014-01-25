<?php
	$GLOBALS["repo"] = "unstable";
	$GLOBALS["target"] = "https://raw.github.com/squarerootfury/redundancy/$repo/Includes/Program.inc.php";
	$GLOBALS["repopath"] ="https://raw.github.com/squarerootfury/redundancy/$repo/";	
	$GLOBALS["databasepath"] = $repopath."Documents/Database%20Upgrade%20Queries/Latest.sql";
	$GLOBALS["updatepath"] = "https://github.com/squarerootfury/redundancy/archive/".$repo.".zip";
	/*
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
	 * This file runs a update process
	 */
?>
<?php
	//The update is only allowed if the current user is member of the administrative group
	if (!isAdmin())
	{
		header("Location: index.php");
		exit;
	}
?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Update</h3>
  </div>
  <div class="panel-body">   
  	<?php if (isset($_GET["t"]) == false): ?>
 		<?php echo $GLOBALS["Program_Language"]["UpdateWarningDoBackup"];?>
  	<br>
  		<?php echo $GLOBALS["Program_Language"]["UpdateWarningLifeTime"];?>

  	<a href = "index.php?module=update&t=true&branch=<?php echo $repo;?>"><?php echo $GLOBALS["Program_Language"]["UpdateStart"];?> (<?php echo $repo;?>)</a>  
  <?php endif;?>
<?php
	if (isset($_GET["t"]) && $_GET["t"] == "true") {		
		if (isset($_GET["branch"])){
			$GLOBALS["repo"] = $_GET["branch"];
			$repo = $GLOBALS["repo"];
			$GLOBALS["target"] = "https://raw.github.com/squarerootfury/redundancy/$repo/Includes/Program.inc.php";
			$GLOBALS["repopath"] ="https://raw.github.com/squarerootfury/redundancy/$repo/";	
			$GLOBALS["databasepath"]= $repopath."Documents/Database%20Upgrade%20Queries/Latest.sql";
			$GLOBALS["updatepath"] = "https://github.com/squarerootfury/redundancy/archive/".$repo.".zip";
		}		
		$targetContent = file_get_contents($GLOBALS["target"]);
		$versionRegex = "/(?P<version>\d+.\d+.\d+)-(?P<repo>[^-]+)-(?P<state>[^\d-]+)(?P<number>\d+)-(?P<update>\d+)/";
		$matchesRemote = array();
		preg_match($versionRegex, $targetContent, $matchesRemote);	
		$matchesLocal = array();
		preg_match($versionRegex,$GLOBALS["Program_Version"],$matchesLocal);
		$upgrade = isUpgradeNeeded($matchesRemote,$matchesLocal) || isset($_GET["force"]);
		$failedcounter = 0;
		if ($upgrade){	
			createNewConfig("Redundancy.conf",$GLOBALS["repopath"]."Redundancy.conf");		
			downloadUpdate();  
			upgradeDataBase();	
			installUpdate();			
			cleanup(getTempPath()."redundancy-".$GLOBALS["repo"]."/",$GLOBALS["Program_Dir"]);
			unlink(getTempPath()."update.zip");				
			successbox($GLOBALS["Program_Language"]["Updated"].$matchesRemote["version"] . "-" . $matchesRemote["repo"] . "-" . $matchesRemote["state"] . $matchesRemote["number"] . "-" . $matchesRemote["update"]);
		}
		else{
			$forcelink = "<a href='index.php?module=update&branch=".$repo."&t=true&force=true'>".$GLOBALS["Program_Language"]["UpdateStart"]."</a>";
			successbox($GLOBALS["Program_Language"]["AlreadyUpdated"].$forcelink);
		}
	}
	//Create the new config
	//Does not update if the path is not correct or the local config file is not writeable.
	function createNewConfig($name,$remotename){		
		if (!is_writable($GLOBALS["Program_Dir"].$name) || !file_exists($GLOBALS["Program_Dir"].$name)){
			die("<div class=\"alert alert-danger\">".$GLOBALS["Program_Language"]["UpdateFailed"].$name."</div>");
			exit;
		}
		$config = file_get_contents($remotename);	
		if ($config == false){
			die("<div class=\"alert alert-danger\">".$GLOBALS["Program_Language"]["UpdateFailed"].$name."</div>");
			exit;
		}	
		$remoteConf = explode("\n",$config);
		$localConf = file($GLOBALS["Program_Dir"].$name);			
		$localconfigContent = file_get_contents($GLOBALS["Program_Dir"].$name);
		$mergedConf = array();	
		$valueRegex = "/(?P<value>[^=]+)/";		
		$newvalues = array();		
		for ($i =0;$i<count($remoteConf);$i++){			
			$currentValue;
			$matchRemote = array();
			preg_match($valueRegex,$remoteConf[$i],$matchRemote);
			if (startsWith($matchRemote["value"],";") != true &&  $remoteConf[$i] != ""){		
				
				if (strpos($localconfigContent,$matchRemote["value"]) === false){					
					$newvalues[$matchRemote["value"]] = $remoteConf[$i];
				}			
			}			
		}
		if (count($newvalues) != 0){
			$newcontent = "\n";
			foreach($newvalues AS $newvalue){
				$newcontent .= $newvalue . "\n";
			}
			if (!file_put_contents($GLOBALS["Program_Dir"].$name, $newcontent, FILE_APPEND))
				dangerbox($GLOBALS["Program_Language"]["UpdateFailed"].$name);		
		}
	}
	//Download the update files to /Temp/
	//Does not update if wrong path is given
	//Does not update if /Temp/ is not writable
	function downloadUpdate(){		
		$content = file_get_contents($GLOBALS["updatepath"]);
		if ($content == false){
			die("<div class=\"alert alert-danger\">".$GLOBALS["Program_Language"]["UpdateDownloadError"]."</div>");
			exit;
		}
		if (!file_put_contents(getTempPath()."update.zip",$content )){
			die("<div class=\"alert alert-danger\">".$GLOBALS["Program_Language"]["UpdateDownloadError"]."</div>");
			exit;
		}			
	}
	//Installs the update to the program dir
	//Does not update if zip could not be opened

	function installUpdate(){
		$zip = new ZipArchive;
		$res = $zip->open(getTempPath()."update.zip");
		$newentries = array();
		if ($res !== TRUE){
			die();
			exit;
		}			
		else
		{
			for( $i = 0; $i < $zip->numFiles; $i++ ){ 
				$stat = $zip->statIndex( $i ); 
				if (strpos($stat["name"], "Redundancy.conf") === false && 
					strpos($stat["name"], "DataBase.inc.php") === false && 
					strpos($stat["name"], "Storage") === false && 
					strpos($stat["name"], "Temp") === false && 
					strpos($stat["name"], "Snapshots") === false && 
					strpos($stat["name"], "Installer") === false && 
					strpos($stat["name"], "Documents") === false
					){
					array_push($newentries,$stat["name"]);
				}				
			}			
			$zip->extractTo(getTempPath(), $newentries);
			//TODO: Move to program dir.
   			$zip->close();   		
   			full_copy(getTempPath()."redundancy-".$GLOBALS["repo"]."/",$GLOBALS["Program_Dir"]);   
   			infobox($GLOBALS["Program_Language"]["UpdateFilesFinished"]);  				
		}
	}
	function full_copy( $source, $target ) {
    if ( is_dir( $source ) ) {
        $d = dir( $source );
        if (!is_dir($target))
      		@mkdir( $target );
        while ( FALSE !== ( $entry = $d->read() ) ) {
            if ( $entry == '.' || $entry == '..' ) {
                continue;
            }
            $Entry = $source . '/' . $entry; 
            if ( is_dir( $Entry ) ) {
                full_copy( $Entry, $target . '/' . $entry );
                continue;
            }
            if (!rename( $Entry, $target . '/' . $entry )) {
            	dangerbox($Entry." not copied".$target . '/' . $entry);
            	$failedcounter++;      
            }       
            	
        }
	    $d->close();
	    }else {
	        if (rename( $source, $target ))  {
	        	dangerbox($Entry." not copied to ".$target);
	        	$failedcounter++;
	        }  	
	    }
	}	
	function cleanup($source){
		if (is_dir($source)) {
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) ) {
	            if ( $entry == '.' || $entry == '..' ) {
	                continue;
	            }
	            $Entry = $source . '/' . $entry; 
	            if ( is_dir( $Entry ) ) {
	                cleanup( $Entry);
	                continue;
	            }          	
	        }
	    	$d->close();
	      	rmdir($source);
		}
	}
	//Updates the database
	//Does not update if file could not be downloaded
	function upgradeDataBase(){		
		$dbdump = file_get_contents($GLOBALS["databasepath"]);	
		if ($dbdump == false)
		{
			die("<div class=\"alert alert-danger\">".$GLOBALS["Program_Language"]["UpdateDownloadError"]."</div>");			
			exit;
		}
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		$queries = explode(";",$dbdump);
		for ($i =0;$i<count($queries) ;$i++){			
			if ($queries[$i] != "" && empty($queries[$i]) != true)	{		
				try{
					if (!mysqli_query($connect,$queries[$i])){						
						throw new Exception("");												
					}					 	
				}	
				catch (Exception $exception){
					
				}				
			}					
		}
		infobox($GLOBALS["Program_Language"]["DataBaseUpdated"]);
	}
	function isUpgradeNeeded($remote,$local){			
		if (version_compare($local["version"],$remote["version"]) == -1)
		{			
			return true;		
		}
		if ($local["update"] < $remote["update"]){
			return true;
		}			
		
		return false;
	}
	function dangerbox($text){
		echo "<div class=\"alert alert-danger\">".$text."</div>";  
	}
	function infobox($text){
		echo "<div class=\"alert alert-info\">".$text."</div>";
	}
	function successbox($text){
		echo "<div class=\"alert alert-success\">".$text."</div>";
	}
?> 
  </div>
</div>