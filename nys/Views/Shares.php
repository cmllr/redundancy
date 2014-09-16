<h2><?php echo $GLOBALS["Language"]->ShareMenu;?> <small> - <?php echo $GLOBALS["Language"]->SharedSubTitle;?></small></h2>
<h3><?php echo $GLOBALS["Language"]->SharedByMe;?></h3>
<?php
	//var_dump($shares);
?>
<ul class="list-group">
<?php foreach($shares as $key=>$value): ?>		
	<?php		
		$textForShare = "";		
		$href = "";		
		$displayName = "";
		if (is_null($value->Permissions) || $value->Permissions == "null"){
			//The file is shared by link.
			$textForShare = sprintf($GLOBALS["Language"]->SharedByLink,$value->ShareCode,$value->SharedDateTime);			
		}
		else{	

			$textForShare = sprintf($GLOBALS["Language"]->SharedToUser,$value->TargetUser->DisplayName,$value->SharedDateTime);			
		}
		if (!is_null($value->Entry->FilePath)){
			$href = "?detail&f=".$value->Entry->Hash;
			$displayName = $value->Entry->DisplayName;;
		}
		else{
			$displayName = $value->Entry->DisplayName;//$GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetAbsolutePathById',json_encode(array($value->Entry->Id,$_SESSION['Token'])));
			$href = "?files&d=".$displayName;	
		}
	?>		  
    <li  class="list-group-item"><a href='<?php echo $href;?>'><?php echo $displayName;?></a> <small> - <?php echo $textForShare;?></small> 
    <!-- Buttons for link shares -->
    <?php if (is_null($value->Permissions) || $value->Permissions == "null") :?>
   		 <a href="?shares&c=<?php echo $value->ShareCode;?>&d=true">
   		 	<span class="label label-danger"><?php echo $GLOBALS["Language"]->RemoveShare;?></span></a>
   		 <a href="?shares&c=<?php echo $value->ShareCode;?>&r=true">
   		 	<span class="label label-warning"><?php echo $GLOBALS["Language"]->NewCode;?></span></a>
   		 <a class = "displaylink" href="<?php echo $value->ShareCode;?>">
   		 	<span class="label label-success"><?php echo $GLOBALS["Language"]->ShowShareLink;?></span></a>
   		 </li>				   
	<?php endif;?>
	<!-- Buttons for user shares -->
	<?php if (!is_null($value->Permissions) && $value->Permissions != "null") :?>
   		 <a href="?shares&c=<?php echo $value->ShareCode;?>&d=true">
   		 	<span class="label label-danger"><?php echo $GLOBALS["Language"]->RemoveShare;?></span></a>   		 
   		 </li>				   
	<?php endif;?>
<?php endforeach; ?>	
</ul>
<h3><?php echo $GLOBALS["Language"]->SharedToMe;?></h3>
<ul class="list-group">
<?php foreach($sharesToMe as $key=>$value): ?>		
	<?php		
		$textForShare = "";		
		$href = "";		
		$fromUser = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetUserNameById',json_encode(array($value->UserID,$_SESSION['Token'])));
		$textForShare = sprintf($GLOBALS["Language"]->SharedToMeFromOthers,$fromUser->DisplayName,$value->SharedDateTime);			
		$displayName = "";
		if (!is_null($value->Entry->FilePath)){
			$href = "?detail&f=".$value->Entry->Hash;
			$displayName = $value->Entry->DisplayName;;
		}
		else{
			$displayName = $GLOBALS['Router']->DoRequest('Kernel.FileSystemKernel','GetAbsolutePathById',json_encode(array($value->Entry->Id,$_SESSION['Token'])));
			$href = "?files&d=".$displayName;	
		}
	?>		  
    <li  class="list-group-item"><a href='<?php echo $href;?>'><?php echo $displayName;?></a> <small> - <?php echo $textForShare;?></small>
	<!-- Buttons for user shares -->
	<?php if (!is_null($value->Permissions) && $value->Permissions != "null") :?>
   		 <a href="?shares&c=<?php echo $value->ShareCode;?>&d=true">
   		 	<span class="label label-danger"><?php echo $GLOBALS["Language"]->AbortShareToMe;?></span></a>   		 
   		 </li>				   
	<?php endif;?>
<?php endforeach; ?>	
</ul>
<script>
	$(".displaylink").click(function(e){
			e.preventDefault();			
			var link = e.currentTarget.attributes["href"].value;	
			var dialogTitle = "<?php echo $GLOBALS["Language"]->ShowShareLink;?>";  
			var code = window.location.origin+window.location.pathname+"?share&c="+link;
			var text = "<?php echo $GLOBALS["Language"]->LinkToShareText; ?>".replace("%s",code);  		
			DisplayShareLink(text,dialogTitle);			
	});		  
</script>