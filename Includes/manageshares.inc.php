<div class="panel-body">
<h2 class="hidden-xs"><?php echo $GLOBALS["Program_Language"]["Manage_shares"];?></h2>
<h3 class="visible-xs"><?php echo $GLOBALS["Program_Language"]["Manage_shares"];?></h3>

<div class="panel-body">
<form class="form-horizontal">		
	<div class="form-group">
		<div class="alert alert-info"><?php echo $GLOBALS["Program_Language"]["ShareManageTip"];?></div>		
	</div>	
	<h3 class="hidden-xs"><?php echo $GLOBALS["Program_Language"]["GotShared"];?></h3>
	<h4 class="visible-xs"><?php echo $GLOBALS["Program_Language"]["GotShared"];?></h4>
	<?php
		getSharesOfUser();
	?>
	<h3 class="hidden-xs"><?php echo $GLOBALS["Program_Language"]["SharedSelf"];?></h3>
	<h4 class="visible-xs"><?php echo $GLOBALS["Program_Language"]["SharedSelf"];?></h4>
	<?php
		getSharesByUser();
	?>	
</form>
</div>
</div>