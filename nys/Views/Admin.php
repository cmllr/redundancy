<h2><?php echo $GLOBALS["Language"]->Administration;?></h2>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="active"><a href="#start" role="tab" data-toggle="tab">Start</a></li>
	<li ><a href="#resetpass" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->reset_pass;?></a></li>
	<li><a href="#delete" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->Delete_Account;?></a></li>
	<li><a href="#edit" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->admin_edit;?></a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active" id="start">
		<div class="well">
			We trust you have received the usual lecture from the local System</br>
			Administrator. It usually boils down to these three things:</br>
		</br>
		#1) Respect the privacy of others.</br>
		#2) Think before you type.</br>
		#3) With great power comes great responsibility.</br>
	</div>
</div>
<div class="tab-pane " id="resetpass">
	<div class="alert alert-info"><?php echo $GLOBALS["Language"]->ResetPasswortInfo;?></div>
	<form action ="?admin&t=r" method='POST' class="form-horizontal">
		<div class="form-group">
			<label for="inputPassword" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->Username;?></label>
			<div class="col-xs-10">
				<input type="text" class="form-control" name="username" placeholder="<?php echo $GLOBALS["Language"]->Username;?>">
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-offset-2 col-xs-10">
				<button type="submit" class="btn btn-warning"><?php echo $GLOBALS["Language"]->ChangePassword;?></button>
			</div>
		</div>
	</form>
</div>
<div class="tab-pane" id="delete">
	<div class="alert alert-info"><?php echo $GLOBALS["Language"]->Delete_Account_Warning;?></div>
	<form action ="?admin&t=d" method='POST' class="form-horizontal">
		<div class="form-group">
			<label for="inputPassword" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->Username;?></label>
			<div class="col-xs-10">
				<input type="text" class="form-control" name="username" placeholder="<?php echo $GLOBALS["Language"]->Username;?>">
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-offset-2 col-xs-10">
				<button type="submit" class="btn btn-danger"><?php echo $GLOBALS["Language"]->Delete_Account;?></button>
			</div>
		</div>
	</form>
</div>
<div class="tab-pane" id="edit">
	<div class="alert alert-info"><?php echo $GLOBALS["Language"]->EditUserInfo;?></div>
	<?php if (!isset($user)):?>
	<form action ="?admin&t=e" method='POST' class="form-horizontal">
		<div class="form-group">
			<label for="inputPassword" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->Username;?></label>
			<div class="col-xs-10">
				<input type="text" class="form-control" name="username" placeholder="<?php echo $GLOBALS["Language"]->Username;?>">
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-offset-2 col-xs-10">
				<button type="submit" class="btn"><?php echo $GLOBALS["Language"]->admin_edit;?></button>
			</div>
		</div>
	</form>
	<?php endif;?>
	<?php if (isset($user) && !is_numeric($user)) :?>
		<form action ="?admin&t=e" method='POST' class="form-horizontal">
			<div class="form-group">
				<label for="username" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->DisplayName;?></label>
				<div class="col-xs-10">
					<input type="text" class="form-control" name="username" placeholder="<?php echo $GLOBALS["Language"]->Username;?>" value="<?php echo $user->DisplayName;?>">
				</div>
			</div>
			<div class="form-group">
				<label for="enabled" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->enabled_user;?></label>
				<div class="col-xs-10">
					<input type="checkbox" checked="<?php ($user->IsEnabled) ? "true" : "false" ;?>">
				</div>
			</div>
			<div class="form-group">
				<label for="quota" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->user_storage_in_mb;?></label>
				<div class="col-xs-10">
					<input type="text" class="form-control" name="quota" placeholder="<?php echo $GLOBALS["Language"]->user_storage_in_mb;?>" value="<?php echo $user->DisplayName;?>">
				</div>
			</div>
			<div class='form-group'>
				<label for='password' class="control-label col-xs-2"><?php echo $GLOBALS['Language']->Group; ?></label>
				<div class="col-xs-10">
					<select class='form-control' id='lang' name='lang'>
						<?php 
						$languages = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetInstalledRoles',json_encode(array()));				
						?>
						<?php foreach($languages as $key=>$value): ?>			  
							<option><?php echo $value->Description; ?></option>				   
						<?php endforeach; ?>	
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-offset-2 col-xs-10">
					<button type="submit" class="btn btn-success"><?php echo $GLOBALS["Language"]->Save;?></button>
				</div>
			</div>
		</form>
	<?php endif;?>
</div>
</div>
