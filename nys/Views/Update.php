<?php if (!isset($_GET["go"])) :?>
<h1 class="light"><?php echo $GLOBALS["Language"]->Update;?></h1>
<div class="alert alert-warning"><?php echo $GLOBALS["Language"]->UpdateWarningLifeTime;?></div>
<ul class="list-group">
  <li class="list-group-item list-group-item-info">
    <span class="badge"><?php echo $updateSource;?></span>  
      <?php echo $GLOBALS["Language"]->UpdateSource;?>
  </li>
  <li class="list-group-item">
    <span class="badge"><?php echo $localVersion;?></span>
    <?php echo $GLOBALS["Language"]->LocalVersion;?>
  </li>
  <li class="list-group-item">
    <span class="badge"><?php echo $remoteVersion;?></span>
    <?php echo $GLOBALS["Language"]->RemoteVersion;?>
  </li>
   <li class="list-group-item <?php echo ($updateState) ? "list-group-item-warning" : "list-group-item-success";?>">
    <?php echo ($updateState) ? $GLOBALS["Language"]->UpdateNeeded : $GLOBALS["Language"]->AlreadyUpdated;?>
    <?php if ($updateState) :?>
    	<a href="?update&go" class="btn btn-warning"><?php echo $GLOBALS["Language"]->UpdateStart;?></a>
	<?php endif;?>
  </li>
</ul>
<?php endif;?>
<?php if (isset($_GET["go"])) :?>
	<h1 class="light"><?php echo sprintf($GLOBALS["Language"]->Updating,$remoteVersion);?></h1>
	<?php
		//run the update!
		$r =  $GLOBALS['Router']->DoRequest('Kernel.UpdateKernel','Update',json_encode(array($_SESSION["Token"])));	
	?>
	<?php if($r == true) :?>
		<div class="alert alert-success"><?php echo sprintf($GLOBALS["Language"]->Updated,$remoteVersion);?></div>
	<?php endif;?>
	<?php if($r == false) :?>
		<div class="alert alert-danger"><?php echo $GLOBALS["Language"]->UpdateFailed;?></div>	
	<?php endif;?>
<?php endif;?>