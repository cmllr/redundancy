<div class="panel panel-default">
	<div class="panel-body">				
			<h2><?php echo $entry->DisplayName; ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<?php 
						if (is_numeric($mediaPreview)){
							$error = $router->DoRequest('Kernel.InterfaceKernel','GetErrorCodeTranslation',json_encode(array("R_ERR_".$mediaPreview,$_SESSION["Language"])));
    						echo $error;
							unset($_SESSION["fileInject"]);
						}
						else
							echo $mediaPreview;
					 ?>
				</div>
			</div>
			<div class="btn-group" id="fileActionBtnGroup">

				<a type="a" href="?shared&c=<?php echo $shareCode; ?>" target="_blank" class="btn btn-default">
					<span class="elusive icon-screen glyphIcon">
					</span><span class="hidden-xs">
					<?php echo $GLOBALS['Language']->Download;?></span>
				</a>	

			</div>
	</div>
</div>