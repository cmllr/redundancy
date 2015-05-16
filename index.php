<?php
error_reporting(E_ALL);
if (!file_exists("./lock"))
{
    header("Location: install.php");
    exit;
}
require './nys/Nys.Router.php';
$router = new \Redundancy\Nys\Router();
ob_start();

//Set the cookies if needed
$router->CookieInteraction();
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <?php if (!isset($_SESSION["Token"])) :?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php endif;?>
    <?php if (isset($_SESSION["Token"])) :?>
    <?php $enableScrolling = $GLOBALS['Router']->DoRequest('Kernel.UserKernel','GetUserSetting',json_encode(array("ui-user-scalable",$_SESSION["Token"])));?>
    <?php if (!is_null($enableScrolling) && $enableScrolling->Value == true) :?>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <?php endif;?>
    <?php if (is_null($enableScrolling) || $enableScrolling->Value == false) :?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php endif;?>
    <?php endif;?>
    <!-- jQuery -->
    <script src="./Lib/jQuery/jquery-1.10.2.min.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="./Lib/FontAwesome/css/font-awesome.css">
    <!-- jQuery UI -->
    <script src="./Lib/jQuery/UI/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="./Lib/jQuery/UI/jquery-ui.min.css">
    <!-- jQuery Context Menu -->
    <script src='./Lib/jQuery/jquery.contextMenu.js'></script>
    <script src='./Lib/jQuery/jquery.ui.position.js'></script>
    <link rel="stylesheet" href="./Lib/jQuery/jquery.contextMenu.css">
    <!-- Spin.js -->
    <script src='./Lib/spin.min.js'></script>
    <!-- Dropzone.js -->
    <script src='./Lib/Dropzone.js/dropzone.js' charset='UTF-8'></script>
    <link rel='stylesheet' href='Lib/Dropzone.js/css/dropzone.css' type='text/css' />
    <!-- Bootstrap -->
    <link rel='stylesheet' href='./Lib/Lenticularis/css/theme.min.css' type='text/css' />
    <script src='./Lib/Bootstrap/js/bootstrap.min.js'></script>
    <!-- Bootstrap Slider -->
    <link rel='stylesheet' href='./Lib/Bootstrap-slider/bootstrap-slider.css' type='text/css' />
    <script src='./Lib/Bootstrap-slider/bootstrap-slider.js'></script>
    <!-- Others -->
    <link rel='stylesheet' href='./nys/Views/css/nys.css' type='text/css' />
    <script src='./nys/Views/js/Nys.Files.js'></script>
    <script src='./nys/Views/js/Nys.Dialogs.js'></script>
    <link rel="icon" type="image/png" href="./nys/Views/img/favicon.png">
     <script src='./Lib/jQuery.Bootstrap/jquery.bootstrap.min.js'></script>
    <title>Redundancy</title>
</head>

<body>
    <div class='container'>
        <div class='row'>
            <?php $router->Route($_SERVER['REQUEST_URI']); ?>
        </div>
    </div>   
</body>

</html>
