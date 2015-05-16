<h1 class="light-header"><span class="gray"><?php echo $GLOBALS["Language"]->New_Directory."</span> ". $absolutePathCurrentDirectory;?></h1>
<div class="panel-body">
<form class="form-horizontal" method="POST" action="?newfolder">	
	<div class="form-group">
		<div class="alert alert-info"><?php
				echo $GLOBALS["Language"]->multiple_dirs;
			?>
		</div>	
		<label for="pass" class="col-lg-3 control-label"><?php echo $GLOBALS["Language"]->New_Directory_Short;?></label>
		<div class="col-lg-9">
			<input type="text" class="form-control"  name="directory" placeholder="<?php echo $GLOBALS["Language"]->New_Directory_Short;?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<input class = 'btn-block btn btn-default' type=submit name=submit value="<?php echo $GLOBALS["Language"]->New_Directory_Button;?>">		
		</div>
	</div>
</form>
</div>