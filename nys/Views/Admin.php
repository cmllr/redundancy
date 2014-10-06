<h2><?php echo $GLOBALS["Language"]->Administration;?></h2>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li><a href="#start" role="tab" data-toggle="tab">Start</a></li>
	<li><a href="#edit" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->admin_edit;?></a></li>
	<li><a href="#delete" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->Delete_Account;?></a></li>
	<li><a href="#group" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->admin_groups;?></a></li>
	<li><a href="#groupdelete" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->Delete_Group;?></a></li>
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
<div class="tab-pane" id="delete">
	<div class="alert alert-info"><?php echo $GLOBALS["Language"]->Delete_Account_Warning;?></div>
	<form action ="?admin&t=d" method='POST' class="form-horizontal" autocomplete="off">
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
	<form action ="?admin&t=e" method='POST' class="form-horizontal" autocomplete="off">
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
		<form action ="?admin&t=e" method='POST' class="form-horizontal" autocomplete="off">
			<div class="form-group">
				<label for="username" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->Username;?></label>
				<div class="col-xs-10">
					<input type="text" class="form-control" name="username" placeholder="<?php echo $GLOBALS["Language"]->Username;?>" value="<?php echo $user->LoginName;?>" readonly>
				</div>
			</div>
			<div class="form-group">
				<label for="displayname" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->DisplayName;?></label>
				<div class="col-xs-10">
					<input type="text" class="form-control" name="displayname" placeholder="<?php echo $GLOBALS["Language"]->DisplayName;?>" value="<?php echo $user->DisplayName;?>">
				</div>
			</div>
			<div class="form-group">
				<label for="enabled" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->enabled_user;?></label>
				<div class="col-xs-10">
					<input type="checkbox" name="enabled" <?php echo ($user->IsEnabled == "1") ? "checked" : "";?>>
				</div>
			</div>
			<div class="form-group">
				<label for="contingent" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->user_storage_in_mb;?></label>
				<div class="col-xs-10">
					<input type="number" class="form-control" name="contingent" placeholder="<?php echo $GLOBALS["Language"]->user_storage_in_mb;?>" value="<?php echo round($user->ContingentInByte/1024,0);?>">
				</div>
			</div>
			<div class="form-group">
				<label for="newPassword" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->New_Pass;?></label>
				<div class="col-xs-10">
					<?php
						//Generate a random password
						$pass = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GeneratePassword',json_encode(array()));	
					?>
					<input type="text" class="form-control" id="newPassword" name="newPassword" placeholder="<?php echo $GLOBALS["Language"]->New_Pass;?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo sprintf($GLOBALS["Language"]->PasswordDescription,$pass);?>">
				</div>
			</div>
			<div class='form-group'>
				<label for='group' class="control-label col-xs-2"><?php echo $GLOBALS['Language']->Group; ?></label>
				<div class="col-xs-10">
					<select class='form-control' id='group' name='group'>
						<?php 
						$groups = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetInstalledRoles',json_encode(array()));				
						?>
						<?php foreach($groups as $key=>$value): ?>			  
							<option <?php echo ($user->Role->Description == $value->Description) ? "selected": "" ;?>><?php echo $value->Description; ?></option>				   
						<?php endforeach; ?>	
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-offset-2 col-xs-10">
					<button type="submit" class="btn btn-success"><?php echo $GLOBALS["Language"]->Save;?></button>
					<a href="?admin&t=e" class="btn btn-info"><?php echo $GLOBALS["Language"]->Abort;?></a>
				</div>
			</div>
		</form>
	<?php endif;?>
</div>
<div class="tab-pane" id="group">
	<?php if (!isset($group)) :?>
	<div class="alert alert-info"><?php echo $GLOBALS["Language"]->admin_groups_Info;?></div>
	<form action ="?admin&t=g" method='POST' class="form-horizontal" autocomplete="off">
		<div class="form-group">
			<label for="inputPassword" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->Groupname;?></label>
			<div class="col-xs-10">
				<input type="text" class="form-control" name="group" placeholder="<?php echo $GLOBALS["Language"]->Groupname;?>">
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-offset-2 col-xs-10">
				<button type="submit" class="btn btn-default"><?php echo $GLOBALS["Language"]->CreateGroup;?></button>
			</div>
		</div>
	</form>
	<hr>
	<form action ="?admin&t=g" method='POST' class="form-horizontal" autocomplete="off">
		<div class="form-group">
			<label for="inputPassword" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->Groupname;?></label>
			<div class="col-xs-10">
				<select class='form-control' id='group' name='group'>
				<?php 
					$groups = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetInstalledRoles',json_encode(array()));				
				?>
				<?php foreach($groups as $key=>$value): ?>			  
					<option><?php echo $value->Description; ?></option>				   
				<?php endforeach; ?>	
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-offset-2 col-xs-10">
				<button type="submit" class="btn btn-default"><?php echo $GLOBALS["Language"]->EditGroup;?></button>
			</div>
		</div>
	</form>
	<?php endif;?>
	<?php if (isset($group)) :?>
		<form action ="?admin&t=g" method='POST' class="form-horizontal" autocomplete="off">
		<div class="alert alert-info"><?php echo sprintf($GLOBALS["Language"]->admin_group_name,$group->Description);?></div>
		<form action ="?admin&t=g" method='POST' class="form-horizontal" autocomplete="off">
		<div class="form-group">
			<label for="groupname" class="control-label col-xs-3"><?php echo $GLOBALS["Language"]->Groupname;?></label>
			<div class="col-xs-9">
				<input type="text" class="form-control" name="groupname" value="<?php echo $group->Description;?>">
			</div>
		</div>
		<?php foreach ($group->Permissions as $key => $value) :?>
			<div class="form-group">
				<label for="<?php echo $key;?>" class="control-label col-xs-3"><?php echo $key; ?></label>
				<div class="col-xs-9">
					<input type="checkbox" name="<?php echo $key;?>" <?php echo ($value == "1") ? "checked" : "";?>>
				</div>
			</div>
		<?php endforeach; ?>
		<div class="alert alert-warning">
			<?php echo $GLOBALS["Language"]->DefaultGroupExplanation;?>
		</div>
		<div class="form-group">
				<label for="IsDefault" class="control-label col-xs-3"><?php echo $GLOBALS["Language"]->DefaultGroup; ?></label>
				<div class="col-xs-9">
					<input type="checkbox" name="IsDefault" <?php echo ($group->IsDefault == "1") ? "checked disabled" : "";?>>
				</div>
			</div>
		<div class="form-group">
			<div class="col-xs-offset-2 col-xs-10">
				<button type="submit" class="btn btn-success"><?php echo $GLOBALS["Language"]->Save;?></button>
				<a href="?admin&t=g" class="btn btn-info"><?php echo $GLOBALS["Language"]->Abort;?></a>
			</div>

		</div>
	</form>
	<?php endif;?>	
</div>
<div class="tab-pane" id="groupdelete">
	<div class="alert alert-info"><?php echo $GLOBALS["Language"]->Delete_Group_Warning;?></div>
	<form action ="?admin&t=dg" method='POST' class="form-horizontal" autocomplete="off">
		<div class="form-group">
			<label for="groupnameToDelete" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->Groupname;?></label>
			<div class="col-xs-10">
				<input type="text" class="form-control" name="groupnameToDelete" placeholder="<?php echo $GLOBALS["Language"]->Groupname;?>">
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-offset-2 col-xs-10">
				<button type="submit" class="btn btn-danger"><?php echo $GLOBALS["Language"]->Delete;?></button>
			</div>
		</div>
	</form>
</div>
</div>
<script type="text/javascript">
	
	$(window).load(function(){
		$('#newPassword').tooltip();
		if (window.location.href.indexOf("?admin&t=e") != -1)
	   		$('a[href="#edit"]').click()
	   	else if (window.location.href.indexOf("?admin&t=d") != -1)
	   		$('a[href="#delete"]').click()
	   	else if (window.location.href.indexOf("?admin&t=g") != -1)
	   		$('a[href="#group"]').click()
		else if (window.location.href.indexOf("?admin&t=dg") != -1)
	   		$('a[href="#groupdelete"]').click()
	   	
	});
</script>
