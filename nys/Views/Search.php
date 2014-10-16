<h2><?php echo $GLOBALS["Language"]->SearchResults;?> - <?php echo sprintf($GLOBALS["Language"]->SearchTerm,$_POST["Search"]);?></h2>
    <?php if (is_numeric($results)) :?>
    <div class="alert alert-danger"><?php echo "R_ERR_".$results;?></div>
  <?php endif;?>
  <?php if (!is_numeric($results)) :?>
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
 <?php endif;?>
</ul>
</div>
</div>