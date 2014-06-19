<html>
<head>
<meta charset="utf-8">
<title>Redundancy Installation</title>
<script src="Lib/jquery-1.10.2.min.js"></script>
<script src="Lib/bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="Lib/bootstrap/css/custom.css" type="text/css"/>
<link rel="stylesheet" href="Lib/bootstrap/css/bootstrap.min.css" type="text/css"/>
<link rel="stylesheet" href="Lib/bootstrap/css/elusive-webfont.css">
<link rel="stylesheet" href="./Styles/Installer.css">
<link rel="icon" href="./favicon.ico">
</head>
<body>
<div class="col-md-4 hidden-xs"></div>
<div class="col-md-4 col-xs-12">
<p style ="text-align:center">
<img src="./Images/bootstrapped_logo.png" style="margin: 0 auto;" class="img-responsive">
<h1 class="text-center">Redundancy<sup>2</sup></h1>
<?php
	include "Kernel.Installer.inc.php";	
	//Disable error reporting in final installer. Enable error reporting only when debugging.
	error_reporting(-1);
	if (isset($_GET["step"]) == false){		
		include "step0.inc.php";
		exit();
	}
	else if (isset($_GET["step"]) && $_GET["step"] == 1){
		include "step1.inc.php";
		exit();
	}
	else if (isset($_GET["step"]) && $_GET["step"] == 2){
		include "step2.inc.php";
		exit();
	}
	else if (isset($_GET["step"]) && $_GET["step"] == 3){
		include "step3.inc.php";
		exit();
	}
	else if (isset($_GET["step"]) && $_GET["step"] == 4){
		include "step4.inc.php";
		exit();
	}
	else
	{
		exit();
	}
?>
</body>
</html>