<h1 class="light-header"><?php echo $GLOBALS["Language"]->MyAccount;?></h1>
<form action="?account" method='POST' class="form-horizontal">
 <div class="well">
    <h3 class="header-form"><?php echo $GLOBALS["Language"]->AccountOf;?>
    <?php echo $data['user']->LoginName?></h3>
    <div class="form-group">
        <label for="inputEmail" class="control-label col-xs-2">
            <?php echo $GLOBALS[ "Language"]->Username;?></label>
        <div class="col-xs-10">
            <input type="email" class="form-control" id="inputEmail" readonly value="<?php echo $data['user']->LoginName?>">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail" class="control-label col-xs-2">
            <?php echo $GLOBALS[ "Language"]->Group;?></label>
        <div class="col-xs-10">
            <input type="email" class="form-control" id="inputEmail" readonly value="<?php echo $data['user']->Role->Description?>">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail" class="control-label col-xs-2">
            <?php echo $GLOBALS[ "Language"]->Storage;?></label>
        <div class="col-xs-10">
            <input type="email" class="form-control" id="inputEmail" readonly value="<?php echo $storageSize?>">
        </div>
    </div>
    <p class="form">
        <?php echo $GLOBALS[ "Language"]->Files;?>
        -
        <?php echo "&nbsp;". "&nbsp;(".$storageInfo. ")";?>
    </p>
    <div class="progress">
        <div class="progress-bar progressbar-info" role="progressbar" aria-valuenow="<?php echo $percentage;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage;?>%;">
        </div>
        <?php echo round($percentage,2); ?>%
    </div>
 </div>
  <div class="well">   
   <h3 class="header-form"><?php echo $GLOBALS["Language"]->MyPermissions;?></h3>     
 <table class="table table-hover table-bordered">
 <tr>
    <th><?php echo $GLOBALS["Language"]->GroupPermissions;?></th>
    <th></th>
 </tr>
   <?php foreach($PermissionSet as $key=>$value):?>
     <tr class="<?php echo ($PermissionSet[$key] == 1) ? "success": "danger";?>">
        <td><?php echo $permissionNames[$key];?></td>
        <td><i class="<?php echo ($PermissionSet[$key] == 1) ? "fa fa-check": "fa fa-remove";?>"/></td>
     </tr>
<?php endforeach;?>
 </table>
 </div>
 <?php if ($allowPasswordChange) :?>
    <div class="well">        
        <h3 class="header-form"><?php echo $GLOBALS["Language"]->ChangePassword;?></h3>
        <div class="form-group">
            <label for="inputPassword" class="control-label col-xs-2">
                <?php echo $GLOBALS["Language"]->Password;?></label>
            <div class="col-xs-10">
                <input type="password" class="form-control" name="oldpassword" placeholder="<?php echo $GLOBALS["Language"]->Password;?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword" class="control-label col-xs-2">
                <?php echo $GLOBALS["Language"]->New_Pass;?></label>
            <div class="col-xs-10">
                <input type="password" class="form-control" name="password" placeholder="<?php echo $GLOBALS["Language"]->New_Pass;?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword" class="control-label col-xs-2">
                <?php echo $GLOBALS["Language"]->New_Pass_Repeat;?></label>
            <div class="col-xs-10">
                <input type="password" class="form-control" name="repeatpassword" placeholder="<?php echo $GLOBALS["Language"]->New_Pass_Repeat;?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-10">
                <button type="submit" class="btn btn-primary">
                    <?php echo $GLOBALS["Language"]->ChangePassword;?></button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($allowAccountDelete) :?>
    <div class="panel panel-danger">
        <div class="panel-body bg-danger">
            <h3 class="header-form"><?php echo $GLOBALS["Language"]->Delete_Account;?></h3>
            <p><?php echo $GLOBALS["Language"]->Delete_Account_Warning;?></p>
            <div class="form-group">
                <label for="deletepassword" class="control-label col-xs-2">
                    <?php echo $GLOBALS["Language"]->Password;?></label>
                <div class="col-xs-10">
                    <input type="password" class="form-control" name="deletepassword" placeholder="<?php echo $GLOBALS["Language"]->Password;?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-10">
                    <button type="submit" class="btn btn-danger">
                        <?php echo $GLOBALS["Language"]->Delete_Account;?></button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</form>
<script type="text/javascript">
$('.collapse').collapse()
</script>