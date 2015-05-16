</br>
<div class="jumbotron">
	<h1><?php echo $GLOBALS["Language"]->Startpage_Title;?></h1>
	<p class="lead"><b><?php echo $GLOBALS["Language"]->Startpage_Title_Description;?></b></p>
</div>
  <div class="row marketing">
        <div class="col-lg-6">
          <h4><i class="fa fa-life-ring"></i> <?php echo $GLOBALS["Language"]->Startpage_Wiki;?></h4>
          <p><?php echo $GLOBALS["Language"]->Startpage_Wiki_Description;?></p>

          <h4><i class="fa fa-group"></i> <?php echo $GLOBALS["Language"]->Startpage_Board;?></h4>
          <p><?php echo $GLOBALS["Language"]->Startpage_Board_Description;?></p>
        </div>

        <div class="col-lg-6">

          <h4><i class="fa fa-cogs"></i> <?php echo $GLOBALS["Language"]->Startpage_VersionInfo;?></h4>
          <p><?php echo $state;?></p>

          <h4><i class="fa fa-database"></i> <?php echo $GLOBALS["Language"]->Startpage_Version;?></h4>
          <p><?php echo sprintf($GLOBALS["Language"]->Version_used,$version);?></p>
        </div>
      </div>
<h3><?php echo $GLOBALS["Language"]->Files;?> <small> - <?php echo "&nbsp;"."&nbsp;(".$storageInfo.")";?></small></h3>
<div class="progress">	
	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentage;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage;?>%;">
	</div>
</div>