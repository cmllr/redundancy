<h2><?php echo $GLOBALS["Language"]->SearchResults;?></h2>
<div class="panel panel-default">
  <div class="alert alert-info"><?php echo sprintf($GLOBALS["Language"]->SearchTerm,$_POST["Search"]);?></div>
  <div class="panel-body">
  <ul class="list-group">
   	<?php foreach ($results as $k => $v): ?>
   		<?php
   			$path = $v->FilePath;
   			if (is_null($path)){
   				$href = "?files&d=".($router->DoRequest('Kernel.FileSystemKernel','GetAbsolutePathById',json_encode(array($v->Id,$_SESSION['Token']))));
   			}
   			else
   			{
   				$href = "?detail&f=".$v->Hash;
   			}
   			echo "<a href='$href'></a>";
   		?>
   		<li class="list-group-item">
   		<?php echo "<a href='$href'>".$v->DisplayName."</a>";?>
   		</li>
   		
   	<?php endforeach ?>
  </div>
  </ul>
</div>