<?php
error_reporting(E_ALL);
require './nys/Nys.Router.php';
$router = new \Redundancy\Nys\Router();
ob_start();

//Set the cookies if needed
//$router->CookieInteraction();
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <!-- jQuery -->
    <script src="./Lib/jQuery/jquery-1.10.2.min.js"></script>
    <!-- Bootstrap -->
    <link rel='stylesheet' href='./Lib/Lenticularis/css/theme.css' type='text/css' />
    <script src='./Lib/Bootstrap/js/bootstrap.min.js'></script>
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
    <!-- Others -->
    <link rel='stylesheet' href='./nys/Views/css/nys.css' type='text/css' />
    <script src='./nys/Views/js/Nys.Helper.js'></script>
    <script src='./nys/Views/js/Nys.Files.js'></script>
    <script src='./nys/Views/js/Nys.Dialogs.js'></script>
    <title>Redundancy</title>
</head>

<body>
    <div class='container'>
        <div class='row'>
            <?php
$router->Route($_SERVER['REQUEST_URI']); ?>
        </div>
    </div>
    <img class='branding visible-xs' src='./nys/Views/img/logoWithTextSmall.png'>
</body>

</html>
