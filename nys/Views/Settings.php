<h2><?php echo $GLOBALS["Language"]->Account_Settings;?></h2>
<div class="alert alert-info"><?php echo $GLOBALS["Language"]->Account_Settings_Hint;?></div>
<form action ="?settings" method='POST' class="form-horizontal" autocomplete="off">	
<input type="hidden" name="submitted" value="true">
	<?php foreach($settings as $key => $value) :?>
		<div class="form-group">
			<label for="<?php echo $value->Name;?>" class="control-label col-xs-3"><?php 
			$name = $GLOBALS['Router']->DoRequest('Kernel.InterfaceKernel','GetLanguageValue',json_encode(array($value->Name)));

			echo $name;
			$settingValue = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetUserSetting',json_encode(array($value->Name,$_SESSION["Token"])));
			$value->Value = $settingValue->Value;
			?></label>
			<div class="col-xs-9">
				<?php if ($value->Type == "Boolean") :?>
					<input type="checkbox" name="<?php echo $value->Name;?>" <?php echo ($value->Value == "true") ? "checked" : "";?>>
				<?php endif;?>		
				<?php if ($value->Type == "Text") :?>
					<input type="text" class="form-control" name="<?php echo $value->Name;?>" value="<?php echo $value->Value;?>">
				<?php endif;?>	
				<?php if ($value->Type == "Number") :?>
					<input type="number" class="form-control" name="<?php echo $value->Name;?>" value="<?php echo $value->Value;?>">
				<?php endif;?>	
			</div>
		</div>
	<?php endforeach;?>
	<div class="form-group">
		<div class="col-xs-offset-3 col-xs-10">
			<button type="submit" class="btn btn-primary"><?php echo $GLOBALS["Language"]->Save;?></button>
		</div>
	</div>
</form>