<div class='col-md-4'></div>
<div class='col-md-4'>
<?php //Display the error message if needed.
    if (isset($ERROR))
      include 'Partials/ErrorMessage.php';
?>
    <div class="hidden-xs">
        <img class='img-responsive logo' src='./nys/Views/img/logoWithText.png'>
    </div>
    <div class='panel panel-default white-flat'>
        <div class="panel-body">
            <h1 class="light header-form gray">
                <?php echo $GLOBALS['Language']->Register;?></h1>
            <form class='form' role='form' method='POST' action='?register'>
                <div class='form-group '>
                    <label for='name'>
                        <?php echo $GLOBALS['Language']->Name;?></label>
                    <input type='text' class='form-control' name='name' placeholder='<?php echo $GLOBALS['Language']->Name;?>'>
                </div>
                <div class='form-group '>
                    <label for='username'>
                        <?php echo $GLOBALS['Language']->Username;?></label>
                    <input type='text' class='form-control' name='username' placeholder='<?php echo $GLOBALS['Language']->Username;?>'>
                </div>
                <div class='form-group '>
                    <label for='email'>
                        <?php echo $GLOBALS['Language']->Email;?></label>
                    <input type='email' class='form-control' name='email' placeholder='<?php echo $GLOBALS['Language']->Email;?>'>
                </div>
                <div class='form-group'>
                    <label for='password'>
                        <?php echo $GLOBALS['Language']->Password; ?></label>
                    <input type='password' class='form-control' name='password' placeholder='<?php echo $GLOBALS['Language']->Password;?>'>
                </div>
                <div class='form-group'>
                    <label for='password'>
                        <?php echo $GLOBALS['Language']->Repeat_Password; ?></label>
                    <input type='password' class='form-control' name='passwordrepeat' placeholder='<?php echo $GLOBALS['Language']->Repeat_Password;?>'>
                </div>
                <div class="btn-group">
                    <button type='submit' class='btn btn-primary'>
                        <?php echo $GLOBALS['Language']->Log_In;?></button>
                    <a class="btn btn-default" href="?login"><?php echo $GLOBALS['Language']->Abort;?></a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class='col-md-4'></div>
