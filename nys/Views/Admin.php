<h1 class="light-header"><?php echo $GLOBALS["Language"]->Administration;?></h1>
<!-- Nav tabs -->
<ul class="nav nav-pills" role="tablist">
	<li><a href="#start" role="tab" data-toggle="tab">Start</a></li>
	<li><a href="#edit" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->admin_edit;?></a></li>
	<li><a href="#delete" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->Delete_Account;?></a></li>
	<li><a href="#group" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->admin_groups;?></a></li>
	<li><a href="#groupdelete" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->Delete_Group;?></a></li>
	<li><a href="#ipunban" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->UnlockIP;?></a></li>
	<li><a href="#settings" role="tab" data-toggle="tab"><?php echo $GLOBALS["Language"]->SystemSettings;?></a></li>
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
				<div class="form-group">
					<input type="text" class="form-control" name="username" placeholder="<?php echo $GLOBALS["Language"]->Username;?>">
			    </div>
			    <div class="form-group">
			 	   <button type="submit" class="btn btn-primary"><?php echo $GLOBALS["Language"]->admin_edit;?></button>
			    </div>
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
					<!--<input type="number" class="form-control" name="contingent" placeholder="<?php echo $GLOBALS["Language"]->user_storage_in_mb;?>" value="<?php echo round($user->ContingentInByte/1024,0);?>">-->
					<input id="ex1" data-slider-id='ex1Slider' name="contingent" type="text" data-slider-min="0" data-slider-max="<?php echo $maxStorage;?>" data-slider-step="1" data-slider-value="<?php echo round($user->ContingentInByte/1024,0);?>"/>
				</div>
			</div>
			<script>
			$('#ex1').slider({
				formatter: function(value) {
					return  Math.round(value/1024) + " MB";
				}
			});
			$(".slider-horizontal").css("width","100%");
			</script>
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
				<input type ="hidden" name="groupid" value ="<?php echo (isset($group->Id)) ? $group->Id : "-1";?>">
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
<div class="tab-pane" id="ipunban">
	<div class="alert alert-info"><?php echo $GLOBALS["Language"]->UnlockIPDescription;?></div>
	<?php if (count($ips) > 0) :?>
	<form action ="?admin&t=uip" method='POST' class="form-horizontal" autocomplete="off">
		<div class="form-group">
			<label for="iptounlock" class="control-label col-xs-2"><?php echo $GLOBALS["Language"]->UnlockIP;?></label>
			<div class="col-xs-10">
				<select class="form-control" name="iptounlock">
					<?php foreach($ips as $key => $value) :?>
						<option><?php echo $key;?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-offset-2 col-xs-10">
				<button type="submit" class="btn btn-danger"><?php echo $GLOBALS["Language"]->UnlockIPButton;?></button>
			</div>
		</div>
	</form>
	<?php endif;?>
	<?php if (count($ips) == 0) :?>
		<div class="alert alert-info"><?php echo $GLOBALS["Language"]->UnlockIPNothing;?></div>
	<?php endif;?>
</div>
	<div class="tab-pane" id="settings">
		<div class="alert alert-info"><?php echo $GLOBALS["Language"]->SystemSettingsDescription;?></div>
		<form action ="?admin&t=settings" method='POST' class="form-horizontal" autocomplete="off">
			<input type="hidden" value="whereismysauerkraut" name="settings"></input>
			<?php foreach($settings as $key => $value) :?>
				<div class="form-group">
					<label for="<?php echo $value->Name;?>" class="control-label col-xs-3"><?php echo $value->Name;?></label>
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
	   else if (window.location.href.indexOf("?admin&t=uip") != -1)
	   		$('a[href="#ipunban"]').click()	
	   	else if (window.location.href.indexOf("?admin&t=settings") != -1)
	   		$('a[href="#settings"]').click()	
	});

	 $('a[href="#start"]').tab('show');
</script>
