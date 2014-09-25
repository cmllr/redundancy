<?php 
error_reporting(E_ALL);		
include './nys/Nys.Controller.php';
include './nys/Nys.Router.php';
$router = new \Redundancy\Nys\Router();		
ob_start();
//Set the cookies if needed
//$router->CookieInteraction();
?>
<!DOCTYPE html>
<head>
<meta charset="UTF-8">	
<link rel = 'stylesheet' href='./Lib/bootstrap/css/bootstrap.min.css' type = 'text/css'/>
<link rel = 'stylesheet' href='./nys/Views/css/nys.css' type = 'text/css'/>
<link rel='stylesheet' href='Lib/bootstrap/css/elusive-webfont.css'>
<link rel='stylesheet' href='Lib/bootstrap/css/custom.css'>
<link rel="stylesheet" href="./Lib/themes/flat/jquery-ui-1.9.2.custom.css">
<link rel="stylesheet" href="./Lib/font-awesome.min.css">
<script src='./Lib/jquery-1.10.2.min.js'></script>
<script src='./Lib/bootstrap/js/bootstrap.min.js'></script>
<script src='./Lib/Dropzone.js/dropzone.js'  charset='UTF-8'></script>
<script src="./Lib/ui/jquery-ui.js"></script>
<script src='./Lib/spin.min.js'></script>
<script src='./Lib/jquery.contextMenu.js'></script>
<script src='./Lib/jquery.ui.position.js'></script>

<link rel="stylesheet" href="./Lib/jquery.contextMenu.css">
<script src='./nys/Views/js/Nys.Helper.js'></script>
<script src='./nys/Views/js/Nys.Files.js'></script>
<script src='./nys/Views/js/Nys.Dialogs.js'></script>
<link rel='stylesheet' href='Lib/Dropzone.js/css/dropzone.css' type='text/css'/>

<title>Redundancy</title>
</head>
<body >
<div class='container'>
<div class='row'>	
<?php				
	$router->Route($_SERVER['REQUEST_URI']);			
?>
</div>
</div>
</body>
</html>
