<h2>
	<?php echo $GLOBALS["Program_Language"]["Files"];?>
</h2>
<div class="progress">
	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo getUsedStorage();?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getUsedStorage();?>%;">
	</div>
</div>
<div>	
	<a href="#" class="list-group-item active">
    <?php echo getUsedStoragePercentage()." (".(getUsedStorageStatus()).")";?>
  </a>
	<ul class="list-group">
		<?php getFileSystemLegend();?>
	</ul>
</div>