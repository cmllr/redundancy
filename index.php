<?php
	$start = microtime(true);
	//first - start a session if needed
	if (isset($_SESSION) == false)
		session_start();
	//session_destroy();

	//if the wanted module is the module for displaying an image -> Include the Image layer.
	if (isset($_GET["module"]) && $_GET["module"] == "image" && isset($_SESSION["user_logged_in"]))
	{
		include "./Includes/image.inc.php";
		exit;
	}
?>
<?php include "./Includes/gpl.inc.php";?>
<!doctype html>
<html>
<head>
<link rel = "stylesheet" href="./Style_Modern.css" type = "text/css"/>
<link rel="shortcut icon" href="./images/favicon.ico">
<title>
<?php
	//Include the main program file		
	include "./Includes/Program.inc.php";	
	//Parse the config file	
	$GLOBALS["config"] = parse_ini_file($GLOBALS["config_dir"]."Redundancy.conf");
	$GLOBALS["Program_Language"] = parse_ini_file("./Language/".$GLOBALS["config"]["Program_Language"].".lng");	
	//$_SESSION["Path_Separator"] = $GLOBALS["config"]["Program_Path_Separator"];	
	if ($GLOBALS["config"]["Program_Debug"] == 1)
			error_reporting(E_ALL);
	
	$GLOBALS["Program_Dir"] = $GLOBALS["config"]["Program_Path"];
	//Display the Program name and calculate the user space
	echo $GLOBALS["config"]["Program_Name_ALT"];
	if (isset($_SESSION["user_name"])){
		//Set the user contingent and refresh the information about used space
		setUsedSpace($_SESSION['user_name']);	
		xss_check();
	}		
	if ($GLOBALS["config"]["Program_HTTPS_Redirect"] == 1)
	{
		if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){			
			header("Location: $redirect"."https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
	}
?>
</title>
<script type="text/javascript" language="JavaScript"
src="Core.js">
</script>
</head>
<body> 
<?php
	//Display a warning if the user uses internet explorer
	//beta only.
	if (!(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE") === false))
	{
		if ($GLOBALS["config"]["IE_Warning"] == 1)
			echo "<div style = 'visibility:visible;' id = 'warning'>".$GLOBALS["Program_Language"]["IE_Warning"]."</div>";
	}	
	if ($GLOBALS["config"]["Enable"] != 1 ) 
	{
		if ((isset($_GET["module"]) && ($_GET["module"] == "admin" || $_GET["module"] == "login" || $_GET["module"] == "logout" )) == false){
			echo "<div style = 'visibility:visible;' id = 'warning'>".$GLOBALS["Program_Language"]["Offline"]."<a href='javascript:void(0)' onclick='displayorhideWarning();'>OK</a></div>";
			exit;
		}
	}
	if (isset($_SESSION["user_logged_in"]))
	{		
		//Include the status bar and menu and the wanted file
		include "./Includes/statusbar.inc.php";
		//Include the menu bar
		include "./Includes/menubar.inc.php";
		//Display content itself
		echo "<div id = 'content'>";
		if ($_SESSION["user_logged_in"] == true && isset($_GET["module"])){
			//Include the requestet file
			//TODO: Add security mechanism to avoid access to non accessible files
			$path = $GLOBALS["Program_Dir"]."Includes/".$_GET["module"].".inc.php";			
			if (file_exists($path))
				include $path;
		
		}
		else if ($_SESSION["user_logged_in"] == true && isset($_GET["module"]) == false){
			//The startpage is an exception, it will be displayed if the module= parameter is not set.
			include $GLOBALS["Program_Dir"]."Includes/startpage.inc.php";		
		}	
		
	}
	//Include other files (further exceptions)
	else if (isset($_GET["module"]) && $_GET["module"] == "activate")
		include "./Includes/activate.inc.php";	
	else if (isset($_GET["module"]) && $_GET["module"] == "register")
		include "./Includes/register.inc.php";	
	else if (isset($_GET["module"]) && $_GET["module"] == "recover")
		include "./Includes/recover.inc.php";		
	else
		include "./Includes/Login.inc.php";	
	 if (isset($_GET["share"]))
		include "./Includes/share.inc.php";
?>
</div>
<?php
	//Display the version if wanted
	if ($GLOBALS["config"]["Program_Display_Version"])
		echo "<div id = 'version'>".$GLOBALS["config"]["Program_Name_ALT"]." ". $GLOBALS["Program_Version"]."";
	$end = microtime(true);
	if ($GLOBALS["config"]["Program_Display_Loadtime"])
		echo "<br><small>". sprintf($GLOBALS["Program_Language"]["Loadtime"],round($end-$start,4))."</small></div>";
	else
		echo "</small></div>";
?>
</body>
</html>
