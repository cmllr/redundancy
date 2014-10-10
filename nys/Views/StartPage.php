<h1 class="light">Redundancy 2 <span class="gray">- 
<?php
	$appName = $GLOBALS["Router"]->DoRequest("Kernel","GetAppName",json_encode(array()));
	$version = $router->DoRequest("Kernel","GetVersion",json_encode(array()));
	echo $appName . ' ' . $version;
?>
</span></h1>	
<h4>	
<?php 
	if (strpos($version,"eol") !== false)
		echo "<span class=\"label label-danger\">".$GLOBALS["Language"]->EOL."</span>";
	else if (strpos($version,"dev") !== false || strpos($version,"beta") !== false)
		echo "<span class=\"label label-warning\">".$GLOBALS["Language"]->Unstable."</span>";
	else
		echo "<span class=\"label label-success\">".$GLOBALS["Language"]->Stable."</span>";
	
?>
</h4>
<h3><?php echo $GLOBALS["Language"]->Files;?> <small> - <?php echo "&nbsp;"."&nbsp;(".$storageInfo.")";?></small></h3>
	
<div class="progress">	
	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentage;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage;?>%;">
	</div>
</div>
<!--
<div class="page-header">
  <h2><?php echo $GLOBALS["Language"]->QuickButtons;?><small> - <?php echo$GLOBALS["Language"]->QuickButtons_Description?></small></h2>
</div>	
<div class="btn-group btn-group-justified">
    <a type="a" href="./Change.log" class="btn btn-default">
        <span class="elusive icon-plus glyphIcon">
        </span>
        <span class='hidden-xs'>Changelog</span>
    </a>
    <a type="a" href="?account" class="btn btn-default">
        <span class="elusive icon-user glyphIcon">
        </span>
        <span class='hidden-xs'>
            <?php echo $GLOBALS[ "Language"]->My_Account;?></span>
    </a>
    <a type="a" href="?upload" class="btn btn-default">
        <span class="elusive icon-file-new glyphIcon">
        </span>
        <span class='hidden-xs'>
            <?php echo $GLOBALS[ "Language"]->Upload?></a>
    <a type="a" href="?newfolder" class="btn btn-default">
        <span class="elusive icon-folder glyphIcon">
        </span>
        <span class='hidden-xs'>
            <?php echo $GLOBALS[ "Language"]->New_Directory_Short;?></span>
    </a>
    <a type="a" href='?shares' class="btn btn-default">
        <span class="elusive icon-share glyphIcon"></span>
        <span class='hidden-xs'>
            <?php echo $GLOBALS[ "Language"]->Manage_shares;?></span>
    </a>
</div>
-->