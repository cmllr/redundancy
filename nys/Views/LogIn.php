<div class='col-sm-12 col-lg-offset-4 col-lg-4'>
    <div>
        <?php //Display the error message if needed.
            if (isset($ERROR))
                include 'Partials/ErrorMessage.php';

                //Display other messages
                if (isset($MESSAGE))
                  include 'Partials/Message.php';
        ?>
        <div class="hidden-xs">
            <img class='img-responsive logo' src='./nys/Views/img/logoWithText.png'>
        </div>
        <!--<h2 class='appname'><?php  echo $GLOBALS['Router']->DoRequest('Kernel','GetAppName',json_encode(array())); ?></h2>-->
        <div class='panel panel-default white-flat'>
            <div class='panel-body'>
                <h1 class="light header-form gray">Login</h1>
                <form class='form' role='form' method='POST' action='?login'>
                    <div class='form-group '>
                        <label for='username'>
                            <?php echo $GLOBALS['Language']->Username;?></label>
                        <input type='text' class='form-control' name='username' placeholder='<?php echo $GLOBALS['Language']->Username;?>'>
                    </div>
                    <div class='form-group'>
                        <label for='password'>
                            <?php echo $GLOBALS[ 'Language']->Password; ?></label>
                        <input type='password' class='form-control' name='password' placeholder='<?php echo $GLOBALS['Language']->Password;?>'>
                    </div>
                    <div class='form-group'>
                        <label for='password'>
                            <?php echo $GLOBALS[ 'Language']->Lang; ?></label>
                        <select class='form-control' id='lang' name='lang'>
                            <?php $languages=$GLOBALS['Router']->DoRequest('Kernel.InterfaceKernel','GetInstalledLanguages',json_encode(array())); ?>
                            <?php foreach($languages as $key=>$value): ?>
                            <option>
                                <?php echo $value; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class='checkbox'>
                            <label>
                                <input name="stayloggedin" type='checkbox' value='true'>
                                <?php echo $GLOBALS['Language']->StayLoggedIn;?>
                            </label>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button type='submit' class='btn btn-primary'>
                            <?php echo $GLOBALS['Language']->Log_In;?></button>
                        <?php if ($isRegistrationEnabled) :?>
                        <a href="?register" class="btn btn-default">
                            <?php echo $GLOBALS['Language']->Register;?></a>
                        <?php endif;?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
