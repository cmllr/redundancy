<div class='col-md-4'></div>
<div class='col-md-4'>
<?php //Display the error message if needed.
    if (isset($ERROR))
      include 'Partials/ErrorMessage.php';
?>
<?php //Display the error message if needed.
    if (isset($MESSAGE))
      include 'Partials/Message.php';
?>
    <div class="hidden-xs">
        <a href="index.php"><img class='img-responsive logo' src='./nys/Views/img/logoWithText.png'></a>
    </div>
    <div class='panel panel-default white-flat'>
        <div class="panel-body">
            <h1 class="light header-form gray">
                <?php echo $GLOBALS['Language']->reset_pass;?></h1>
            <form class='form' role='form' method='POST' action='?resetpass&token=<?php echo $_GET["token"];?>'>                
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
                        <?php echo $GLOBALS['Language']->reset_pass;?></button>
                    <a class="btn btn-default" href="index.php"><?php echo $GLOBALS['Language']->Back;?></a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class='col-md-4'></div>
