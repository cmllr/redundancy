<h2><?php echo $GLOBALS["Language"]->FileChanged;?></h2>
<div class="alert alert-info"><?php echo $GLOBALS["Language"]->FileChangesInfo;?></div>
<?php if (!is_null($entries)) :?>
<?php foreach ($entries as $key => $value) :?>
	<div class="panel panel-default">
  <div class="panel-heading"><?php echo $key;?></div>
  <div class="panel-body">
  <ul class="list-group">
   	<?php foreach ($value as $k => $v): ?>
   		<?php
   			$path = $v[3];
   			if (is_null($path)){
   				$href = "?files&d=".($router->DoRequest('Kernel.FileSystemKernel','GetAbsolutePathById',json_encode(array($v[4],$_SESSION['Token']))));
   			}
   			else
   			{
   				$href = "?detail&f=".$v[5];
   			}
   			echo "<a href='$href'></a>";
   		?>
   		<li class="list-group-item"><span class="<?php echo ($v[2] =="new") ?"glyphicon glyphicon-plus" :"glyphicon glyphicon-pencil" ; ?>"></span><?php echo "<a href='$href'>".$v[0]."</a> - ";?>
   		<?php echo ($v[2] =="new") ? sprintf($GLOBALS["Language"]->FileAddedInfo,$v[1]) : sprintf($GLOBALS["Language"]->FileChangedInfo,$v[1])  ;?>
   		</li>
   		
   	<?php endforeach ?>
  </div>
  </ul>
</div>
<?php endforeach;?>
<?php endif;?>
<!--Display an info message if there are no entries to list-->
<?php if (is_null($entries)) :?>
<div class="alert alert-info"><?php echo $GLOBALS["Language"]->FilesChangesNoFiles;?></div>	
<?php endif;?>