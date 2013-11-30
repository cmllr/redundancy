<script>
  $(function() {
    $( "#slider-range-max" ).slider({
      range: "max",
      min: <?php echo (round(getUsedSpace("fury")/1024/1024,0,PHP_ROUND_HALF_UP)); ?>,
      max: 1000000,
      value: <?php echo (round(getUsedSpace("fury")/1024/1024,0,PHP_ROUND_HALF_UP)); ?>,
      slide: function( event, ui ) {        
		$("#inputStorage" ).val( ui.value);
      }
    }); 
	$("#inputStorage").keypress(function(e) {				
		if (isNaN($("#inputStorage" ).val()) == true){			
			e.preventDefault();			
		}
		else{	
			if ($("#inputStorage" ).val() <= $( "#slider-range-max" ).slider( "option", "max" ))
			{
				$("#save").prop('disabled', false);
			}
			else
			{				
				e.preventDefault();						
			}			
		}
	});
	$("#inputStorage").keyup(function(e) {	
		$( "#slider-range-max" ).slider( "value", $("#inputStorage" ).val() );		
		if (isNaN($("#inputStorage" ).val()) == true ){
			$("#save").prop('disabled', true);
			e.preventDefault();			
		}
		else{	
			if ($("#inputStorage" ).val() <= $( "#slider-range-max" ).slider( "option", "max" ))
			{
				$("#save").prop('disabled', false);
			}
			else
			{
				$("#save").prop('disabled', true);
				e.preventDefault();						
			}			
		}
	})
  });
  </script>
<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * This file provides the administration panel
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed								
	if (isset($_SESSION) == false)
			session_start();	
?>
<ul class="nav nav-tabs" id="dataTabs">
	<li>
		<a href="#Administration" data-toggle="tab"><?php echo $GLOBALS["Program_Language"]["list_users"];?></a>
	</li>
	<li>
		<a href="#Status" data-toggle="tab"><?php echo $GLOBALS["Program_Language"]["admin_status"];?></a>
	</li>
	<li>
		<a href="#Edit" data-toggle="tab"><?php echo $GLOBALS["Program_Language"]["admin_edit"];?></a>
	</li>
	<li>
		<a href="#New" data-toggle="tab"><?php echo $GLOBALS["Program_Language"]["admin_new"];?></a>
	</li>
</ul>
<script>
	$(function(){
		$('#dataTabs li:eq(0) a').tab('show');
	});
</script>
<div class="tab-content" id ="tab-content">
	<div class="tab-pane" id="Administration">
		<div class="panel panel-default">
			<div class="panel-body">
				<?php if (isAdmin() && isset($_POST["list_users"])) { getUserList(); } ?>
				<?php if (isAdmin() == true && isset($_POST["list_users"]) == false) :?>
					<h3><?php echo $GLOBALS["Program_Language"]["list_users"]; ?></h3>
					<form class="form-horizontal" method="POST" action="index.php?module=admin">
							<input type="hidden" id="list_users" class="btn btn-primary"  value="hello" name = "list_users">
							<input type="submit" class="btn btn-primary"  value="<?php echo $GLOBALS["Program_Language"]["run_action"];?>">							
					</form>	
				<?php endif;?>
				<?php if (isAdmin() == false || $GLOBALS["config"]["Program_Enable_Web_Administration"] != 1) :?>
					You don't have the rights to access this page or the web interface is disabled
				<?php endif;?>				
			</div>
		</div>
	</div>
	<div class="tab-pane" id="Status">
		<div class="panel panel-default">
			<div class="panel-body">
					<?php						
						if ($_SESSION["role"] == 0 && isAdmin() && $GLOBALS["config"]["Program_Enable_Web_Administration"] == 1)
						{						
							include "health.inc.php";						
						}		
						else
						{
							echo "You don't have the rights to access this page or the web interface is disabled";
						}
					?>
				<br>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="Edit">
		<div class="panel panel-default">
			<div class="panel-body">			
				<?php if (isset($_POST["username_info"])) : ?>
					<script>
						$(function(){
							$('#dataTabs li:eq(2) a').tab('show');
						});
						$(function(){
							$('#inputStorage').tooltip('toggle');
							$('#inputStorage').tooltip('hide');
							$('#buttonDeleteUser').tooltip('toggle');
							$('#buttonDeleteUser').tooltip('hide');
							$('#user_new_name').tooltip('toggle');
							$('#user_new_name').tooltip('hide');
							
						});
					</script>
				<?php endif;?>
					<?php if ($_SESSION["role"] == 0 && isAdmin() && $GLOBALS["config"]["Program_Enable_Web_Administration"] == 1) : ?>
						<?php if (isset($_POST["username_info"])) : ?>		
							<?php if (isExisting("",$_POST["username_info"]) == false) :?>
								No such user
								<div class="form-group">
									<div class="col-lg-offset-0 col-lg-9">
										<a class="btn btn-default" href="index.php?module=admin"><?php echo $GLOBALS["Program_Language"]["Back"];?></a>
									</div>
								</div>
							<?php endif;?>
							<?php if (isset($_POST["role"]) == false ) :?>
								<form class="form-horizontal" role="form" method="POST" action="index.php?module=admin">				
									<input type="hidden" class="form-control" name="username_info" value="<?php echo $_POST["username_info"];?>">	
									<div class="form-group">
										<label class="col-lg-3 control-label">
											<?php echo $GLOBALS["Program_Language"]["Username"];?>
										</label>
										<div class="col-lg-9">
											<p class="form-control-static">
												<?php echo $_POST["username_info"];?>
											</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-3 control-label">
											<?php echo $GLOBALS["Program_Language"]["user_group"]?>
										</label>
										<div class="col-lg-9">
											<label class="radio-inline">
												<input type="radio" name="role" value = "0"
												<?php 
													if (getUserRole($_POST["username_info"]) == 0)
														echo " CHECKED />";
													else
														echo "/>";
													echo $GLOBALS["Program_Language"]["admin_admin"];
												?>											
											</label>
											<label class="radio-inline">
												<input type="radio" name="role" value="1" 	
												<?php 
													if (getUserRole($_POST["username_info"]) == 1)
														echo " CHECKED />";
													else
														echo "/>";
													echo $GLOBALS["Program_Language"]["admin_user"];
												?>
											</label>
											<label class="radio-inline">
												<input type="radio" name="role" value="3"
												<?php
													if (getUserRole($_POST["username_info"]) == 3)
														echo " CHECKED />";
													else
														echo "/>";
													echo $GLOBALS["Program_Language"]["admin_guest"];													
												?>
											</label>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-offset-3 col-lg-9">
											<div class="checkbox">
												<label>
													<input type="checkbox" name="lock"
													<?php	
														if (isUserEnabled($_POST["username_info"]) == 1)
															echo " checked=\"checked\"/> ".$GLOBALS["Program_Language"]["enabled_user"];
														else
															echo "/> ".$GLOBALS["Program_Language"]["enabled_user"];
													?>
												</label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["user_storage_in_mb"];?></label>
											<div class="col-lg-9">
												<p>
													<input value = "<?php echo getUserStorage($_POST["username_info"]); ?>" type="text" class="form-control" placeholder="<?php echo $GLOBALS["Program_Language"]["user_storage_in_mb"];  ?>" name="storage" data-toggle="tooltip" data-placement="right" id="inputStorage" title data-original-title="Minimum: <?php echo measurementCorrection(round(getUsedSpace($_POST["username_info"]),0,PHP_ROUND_HALF_UP)); ?>"/>
												</p>
												<div id="slider-range-max"></div>
											</div>
									</div>
									<div class="form-group">
										<label class="col-lg-3 control-label">
											<?php echo $GLOBALS["Program_Language"]["Password"]; ?>
										</label>
										<div class="col-lg-9">
											<input type="text" class="form-control" name="user_new_pass" placeholder="<?php echo $GLOBALS["Program_Language"]["Password"];?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-3 control-label">
											<?php echo $GLOBALS["Program_Language"]["pass_hint"];?>
										</label>
										<div class="col-lg-9">
											<p class="form-control-static">
											<?php echo getRandomPass($GLOBALS["config"]["User_Recover_Password_Length"]); ?>
											</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-3 control-label">
											<?php echo $GLOBALS["Program_Language"]["new_user_name"];?>
										</label>
										<div class="col-lg-9">
											<input type="text" type="text" class="form-control" name="user_new_name" data-toggle="tooltip" data-placement="right" id="user_new_name" title data-original-title="<?php echo $GLOBALS["Program_Language"]["new_user_name_desc"];?>" />
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-3 control-label">
											<?php echo $GLOBALS["Program_Language"]["user_save"];?>											
										</label>		
										<div class="col-lg-9">
												<input type="submit" class="btn btn-default" name="submit" value="<?php echo $GLOBALS["Program_Language"]["Save"]?>" />
										</div>
									</div>
								</form>	
								<form class="form-horizontal" method="POST" action="index.php?module=moduser&task=delete&user=<?php echo $_POST["username_info"];?>">
									<div class="form-group">
											<label class="col-lg-3 control-label">
												<?php $GLOBALS["Program_Language"]["user_delete_admin"];?>												
											</label>				
											<div class="col-lg-9">								
												<input type="submit" name="buttonDeleteUser" id="buttonDeleteUser" class="btn btn-danger" data-toggle="tooltip" data-placement="right" title data-original-title="<?php echo $GLOBALS['Program_Language']['user_delete_warning'];?>" value="<?php echo $GLOBALS["Program_Language"]["Delete"];?>">
											</div>
									</div>
								</form>	
								<div class="form-group">
									<div class="col-lg-12">
										<a class="btn btn-default" href="index.php?module=admin"><?php echo $GLOBALS["Program_Language"]["Back"];?></a>
									</div>
								</div>	
							<?php else: ?>
								<?php 
									if (isset($_POST["role"]) )
									{					
										saveUserChanges();			
									}
								?>
							<?php endif;?>
						<?php endif; ?>	
					<?php else: ?>
							You don't have the rights to access this page or the web interface is disabled
					<?php endif;?>	
			<?php if (isAdmin() && isset($_POST["username_info"]) == false): ?>
				<form role="form" method="POST" action="index.php?module=admin">
					<div class="form-group">
						<label for="inputUsername">
							<?php echo $GLOBALS["Program_Language"]["get_user_info"];?>
						</label>
						<div class="input-group" id="inputUsername">
							<span class="input-group-addon">
								<?php echo $GLOBALS["Program_Language"]["Username"];?>
							</span>
							<input name="username_info" type="text" class="form-control" placeholder="">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">
									<?php echo $GLOBALS["Program_Language"]["Search"];?>
								</button>
							</span>
						</div>
				  </div>
				</form>
			<?php endif;?>				
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="New">
		<div class="panel panel-default">
			<div class="panel-body">
				<?php if (isset($_POST["user_create"],$_POST["pass_create"])) :?>
					<script>
						$(function(){
							$('#dataTabs li:eq(3) a').tab('show');
						});
					</script>
				<?php endif; ?>
				<?php
					if (isset($_POST["user_create"],$_POST["pass_create"])){						
						$pEmail = $_POST["user_create"];
						$pPass = $_POST["pass_create"];
						$pPassRepeat = $pPass;
						$pSystem = 1;
						if (registerUser($pEmail,$pPass,$pPassRepeat,$pSystem) == true)
						{
							header("Location: index.php?module=admin&message=user_create_admin_success");
						}
						else
						{
							header("Location: index.php?module=admin&message=user_create_admin_fail");
						}
					}
				?>
			<?php if (isAdmin()): ?>
			<form name = "send" role="form" method="POST" action="index.php?module=admin">
				<div class="form-group">
				<label for="inputUsername"><?php echo $GLOBALS["Program_Language"]["Username"]; ?></label>
					<div class="input-group" id="inputUsername">
						<span class="input-group-addon">
							<?php echo $GLOBALS["Program_Language"]["Username"]; ?>
						</span>
						<input type="text" name="user_create" class="form-control" id="inputUsername">
					</div>
				</div>
				<div class="form-group">
					<div class="input-group" id="inputPassword">
						<span class="input-group-addon">
							<?php echo $GLOBALS["Program_Language"]["Password"]; ?>
						</span>
							<input type="password" name="pass_create" class="form-control" id="inputPassword">
					</div>
				</div>
				<button id="save" type="submit" class="btn btn-primary btn-block"><?php echo $GLOBALS["Program_Language"]["Save"];?></button>
			</form>
			<?php endif; ?>
			<?php
				if (isAdmin() == false)
				{
					echo "You don't have the rights to access this page or the web interface is disabled";		
				}
			?>
			</div>
		</div>
	</div>
</div>