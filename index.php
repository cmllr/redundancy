<html>
<head>
<link rel = "stylesheet" href="./Lib/bootstrap/css/bootstrap.min.css" type = "text/css"/>
<link rel = "stylesheet" href="./nys/Views/css/nys.css" type = "text/css"/>
<link rel="stylesheet" href="Lib/bootstrap/css/elusive-webfont.css">
<link rel="stylesheet" href="Lib/bootstrap/css/custom.css">
<script src="./Lib/jquery-1.10.2.min.js"></script>
<script src="./Lib/bootstrap/js/bootstrap.min.js"></script>

<title>Redundancy</title>
</head>
<body >
<div class="container">
<div class="row">	
<?php	
	error_reporting(E_ALL);	
	include "./nys/Nys.Controller.php";
	include "./nys/Nys.Router.php";
	$router = new Router();				
	$router->Route($_SERVER["REQUEST_URI"]);			
?>
</div>
</div>
</body>
</html>
