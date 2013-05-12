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
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://scribble.pf-control.de/piwik/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "6"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Piwik Code -->
<head>
<link rel = "stylesheet" href="./Style_Modern.css" type = "text/css"/>
<link rel="shortcut icon" href="./images/favicon.ico">
<title>
<?php
	//Include the main program file
	include "./Includes/Program.inc.php";	
	//Parse the config file	
	$_SESSION["config"] = parse_ini_file($_GLOBALS["config_dir"]."Redundancy.conf");
	//$_SESSION["Path_Separator"] = $_SESSION["config"]["Program_Path_Separator"];	
	if ($_SESSION["config"]["Program_Debug"] == 1)
			error_reporting(E_ALL);
	
	$_SESSION["Program_Dir"] = $_SESSION["config"]["Program_Path"];
	//Display the Program name and calculate the user space
	echo $_SESSION["config"]["Program_Name_ALT"];
	if (isset($_SESSION["user_name"])){
		//Set the user contingent and refresh the information about used space
		setUsedSpace($_SESSION['user_name']);		
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
		if ($_SESSION["config"]["IE_Warning"] == 1)
			echo "<div style = 'visibility:visible;' id = 'warning'><b>Warning:</b> You are using Internet Explorer (".$_SERVER['HTTP_USER_AGENT']."). This program does not support the Internet Explorer due technical problems with stylesheet management. You can try to use it, but the user-expierience will be bad. That's a promise.<a href='javascript:void(0)' onclick='displayorhideWarning();'> I understand.</a></div>";
	}	
	if ($_SESSION["config"]["Enable"] != 1 ) 
	{
		if ((isset($_GET["module"]) && ($_GET["module"] == "admin" || $_GET["module"] == "login" || $_GET["module"] == "logout" )) == false){
			echo "<div style = 'visibility:visible;' id = 'warning'>This Redundancy instance is currently offline.<a href='javascript:void(0)' onclick='displayorhideWarning();'> I understand.</a></div>";
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
			$path = $_SESSION["Program_Dir"]."Includes/".$_GET["module"].".inc.php";			
			if (file_exists($path))
				include $path;
		}
		else if ($_SESSION["user_logged_in"] == true && isset($_GET["module"]) == false){
			//The startpage is an exception, it will be displayed if the module= parameter is not set.
			include $_SESSION["Program_Dir"]."Includes/startpage.inc.php";		
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
	if ($_SESSION["config"]["Program_Display_Version"])
		echo "<div id = 'version'>".$_SESSION["config"]["Program_Name_ALT"]." ". $_GLOBALS["Program_Version"]."";
	$end = microtime(true);
	if ($_SESSION["config"]["Program_Display_Loadtime"])
		echo "<br><small>Needed ". round($end-$start,4)." seconds.</small></div>";
	else
		echo "</small></div>";
?>
</body>
</html>
