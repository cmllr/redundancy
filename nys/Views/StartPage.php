<h1 class="light">Redundancy 2 <span class="gray">- 
<?php
	$appName = $GLOBALS["Router"]->DoRequest("Kernel","GetAppName",json_encode(array()));
	$version = $router->DoRequest("Kernel","GetVersion",json_encode(array()));
	echo $appName . ' ' . $version;
?>
</span></h1>	
	
<?php 
	if (strpos($version,"eol") !== false)
		echo "<span  class=\"label label-danger\">".$GLOBALS["Language"]->EOL."</span>";
	else  if (strpos($version,"beta") !== false)
		echo "<span class=\"label label-warning\">".$GLOBALS["Language"]->Unstable."</span>";
	else if (strpos($version,"rc") !== false)
		echo "<span class=\"label label-warning\">".$GLOBALS["Language"]->RC."</span>";
	else
		echo "<span class=\"label label-success\">".$GLOBALS["Language"]->Stable."</span>";
	
?>
<h3><?php echo $GLOBALS["Language"]->Files;?> <small> - <?php echo "&nbsp;"."&nbsp;(".$storageInfo.")";?></small></h3>
	
<div class="progress">	
	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentage;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage;?>%;">
	</div>
</div>