<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 30%;">
    30%
  </div>
</div>
<div class="alert alert-warning">
	Note: This step checks if the permissions are properly set. 
	If you wan't to use the directories somewhere else, please check that these direcotries are writable for PHP, too!
</div>
<?php
	//Run the checks
	$fail = false;
	//Check Redundancy.conf
	$fail = runCheck("../Redundancy.conf",777);
	//Check the DataBase configuration
	if (runCheck("../Includes/DataBase.inc.php",777))
		$fail = true;
	//Check the permissions of the storage
	if (runCheck("../Storage/",777))
		$fail = true;
	//Check the permissions of the temp folder
 	if (runCheck("../Temp/",777))
 		$fail = true;
 	//Check the permissions of the Snapshots directory
 	if (runCheck("../Snapshots/",777))
		$fail = true;
?>
<div class="installerButtons">
<?php if ($fail == false):?>
	<a href="./index.php?step=2" class="btn btn-default">Continue</a>
<?php endif;?>
<?php if ($fail == true):?>
	<a href="./index.php?step=1" class="btn btn-default">Recheck</a>
<?php endif;?>
<a href="about:blank" class="btn btn-default">Abort</a>
</div>
<?php
	/**
	* Checks the file permissions, the result of the check will be printed out and if there is a fail true will be returned
	* @param $file the file to check
	* @param $expect the expected numeric value (3 digits!)
	* @return Boolean value if there was a fail.
	**/
	function runCheck($file,$expect){
		$check = substr(sprintf('%o', fileperms($file)), -3);
		$fail = false;
		if ($check != $expect ){		
			$fail = true;			
		}
		?>
			<div class="panel panel-default">
			<div class="panel-heading"><?php if (is_dir($file)) echo "Folder"; else echo "File";?> "<?php echo $file ;?>"</div>
				<div class="panel-body alert-<?php if($check == $expect) echo "info"; else echo "danger";?>">
					Expected permission <?php echo $expect ;?>, got <?php echo $check;?>.
				</div>
			</div>
		<?php
		return $fail;
	}
?>