<!-- Mobile view navbar-->
<nav class='navbar navbar-default visible-xs' role='navigation'>
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class='navbar-header'>
        <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-ex1-collapse'>
            <span class='sr-only'>Toggle navigation</span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
        </button>
        <a class='navbar-brand' href='index.php'>Redundancy</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class='collapse navbar-collapse navbar-ex1-collapse hidden-lg'>
        <ul class='nav navbar-nav '>
            <li>
                <a href='?account'>
                    <?php echo $GLOBALS['Language']->My_Account;?></a>
            </li>
            <li>
                <a href='?admin'>
                    <?php echo $GLOBALS['Language']->Administration;?></a>
            </li>
            <li>
                <a href='?info'>Info</a>
            </li>
            <li class='divider'></li>
            <li>
                <a href='?logout'>
                    <?php echo $GLOBALS['Language']->LogOut;?></a>
            </li>

        </ul>
    </div>
    <!-- /.navbar-collapse -->
</nav>
<div class='hidden-xs hidden-sm col-md-2 col-lg-2'>
    <div class='affix-top' id='leftSidebar' data-spy='affix' data-offset-top='0'>
        <div class='dropdown'>
            <button type='button' class='btn btn-primary btn-block dropdown-toggle' data-toggle='dropdown'>
                <span class='userbadge glyphicon glyphicon-userx'></span>
                <?php echo $data[ 'user']->DisplayName; ?>
                <span class='caret'></span>
            </button>
            <ul class='dropdown-menu' role='menu'>
                <li>
                    <a href='?account'>
                        <?php echo $GLOBALS['Language']->My_Account;?></a>
                </li>
                <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],9)))) :?>
                <li>
                    <a href='?admin'>
                        <?php echo $GLOBALS['Language']->Administration;?></a>
                </li>
                <li>
                    <a href='?update'>
                        <?php echo $GLOBALS['Language']->Update;?></a>
                </li>
                <?php endif;?>
                <li>
                    <a href='?info'>Info</a>
                </li>
                <li class='divider'></li>
                <li>
                    <a href='?logout'>
                        <?php echo $GLOBALS['Language']->LogOut;?></a>
                </li>
            </ul>
        </div>

        <div class="white-flat">
            <ul class='nav nav-pills nav-stacked'>
                <li>
                    <a href='index.php'>
                        <span class='fa fa-home'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->Home;?>
                    </a>
                </li>
                <li>
                    <a href='?files'>
                        <span class='fa fa-cloud'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->Files;?>
                    </a>
                </li>
                <li>
                    <a href='?history'>
                        <span class='fa fa-clock-o'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->changes;?>
                    </a>
                </li>
                <li>
                    <a href='?upload'>
                        <span class='fa fa-cloud-upload'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->Upload;?>
                    </a>
                </li>
                <li>
                    <a href='?newfolder'>
                        <span class='fa fa-folder-open'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->New_Directory_Short;?>
                    </a>
                </li>
                <li>
                    <a href='?shares'>
                        <span class='fa fa-share'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->ShareMenu;?>
                    </a>
                </li>
                <li>
                    <form method="POST" action="?search">
                        <input class="form-control search" type="text" value="<?php echo (isset($_POST[" Search "])) ? $_POST["Search "] : "";?>" name="Search" placeholder="<?php echo $GLOBALS['Language']->Search;?>">
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class='col-xs-12 col-sm-12 col-md-10 col-lg-10'>
    <?php 
        //Display the error message if needed.
        if (isset($ERROR)) include 'Partials/ErrorMessage.php'; 
        //Display other messages
        if (isset($MESSAGE)) include 'Partials/Message.php'; 
    ?>
    <div class='panel panel-default white-flat'>

        <div class='panel-body main'>
            <?php if (isset($innerContent)) include $innerContent; ?>
        </div>
    </div>
</div>
