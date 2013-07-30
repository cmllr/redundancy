<?php
	$start = microtime(true);
	//first - start a session if needed
	if (isset($_SESSION) == false)
		session_start();
	//session_destroy();
	include "./Includes/Program.inc.php";
	//if the wanted module is the module for displaying an image -> Include the Image layer.	
	$GLOBALS["config"] = parse_ini_file($GLOBALS["config_dir"]."Redundancy.conf");
	if (isset($_GET["module"]) && $_GET["module"] == "image" )
	{

		include "./Includes/image.inc.php";
			
		exit;
	}
	elseif (isset($_GET["share"]))
			include $GLOBALS["Program_Dir"]."Includes/share.inc.php";	
			header('Content-Type: charset=utf-8'); 
	if (isset($_GET["api"]) && $_GET["api"] == true)
		header("Location: ./Includes/API/api.inc.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php if ($GLOBALS["config"]["Program_Display_Generator_Tag"] == 1): ?>
<meta name="generator" content="<?php echo $GLOBALS["config"]["Program_Name_ALT"]." ".$GLOBALS["Program_Version"];?>" />
<?php endif;?>
<?php if ($GLOBALS["config"]["Program_Embed_GPL_Header"] == 1) include "./Includes/gpl.inc.php";?>
<?php
	if (isset($_SESSION["style"]))
		$style = $_SESSION["style"];
	else
		$style = "Style new.css";
?>
<link rel = "stylesheet" href="./<?php echo $style?>" type = "text/css"/>
<?php
	//$GLOBALS["config"] = parse_ini_file($GLOBALS["config_dir"]."Redundancy.conf");
	if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1)
		include "./Lib/JQuery.inc.php";
?>
<link rel="icon" href="../favicon.png" type="image/png">

<title>
<?php
	//Include the main program file		

	if (xss_check() == true)
	{
		echo "XSS Attack found</title>";
		echo "<div style = 'visibility:visible;' id = 'warning'>You did an XSS attack. Redundancy will stop here.</div><body></body></html>";
		exit;
	}	
	//Parse the config file	
	
	if (isset($_GET["lang"]) == false){
		if (isset($_SESSION["language"]) == false)
			$GLOBALS["Program_Language"] = parse_ini_file("./Language/".$GLOBALS["config"]["Program_Language"].".lng");	
		else if ($_SESSION["language"] != "..")
			$GLOBALS["Program_Language"] = parse_ini_file("./Language/".$_SESSION["language"].".lng");	
	}
	else if (isset($_GET["lang"]) && file_exists("./Language/".$GLOBALS["config"]["Program_Language"].".lng") && $_GET["lang"] != ".."){
		$GLOBALS["Program_Language"] = parse_ini_file("./Language/".$_GET["lang"].".lng");	
		if (!isset($_SESSION))
			session_start();
		$_SESSION["language"] = $_GET["lang"];
	}
	//$_SESSION["Path_Separator"] = $GLOBALS["config"]["Program_Path_Separator"];	
	if ($GLOBALS["config"]["Program_Debug"] == 1)
			error_reporting(E_ALL);
	
	$GLOBALS["Program_Dir"] = $GLOBALS["config"]["Program_Path"];
	//Display the Program name and calculate the user space
	echo $GLOBALS["config"]["Program_Name_ALT"];
	if (isset($_SESSION["user_name"])){
		//Set the user contingent and refresh the information about used space
		fs_setUsedSpace($_SESSION['user_name']);	
		if (user_check_session() == true)
		{
			echo "SQL Injection found</title>";
			echo "<div style = 'visibility:visible;' id = 'warning'>SQL Injection found.</div><body></body></html>";
			exit;
		}		
	}	
	if ($GLOBALS["config"]["Program_HTTPS_Redirect"] == 1)
	{
		if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){			
			header("Location: $redirect"."https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
	}
?>
</title>
<script type="text/javascript" src="Core.js">
</script>
</head>
<body> 
<?php	
	
	if ($GLOBALS["config"]["Program_Enable_Plugins"] == 1)
	{
		$handle=opendir ($GLOBALS["Program_Dir"]."Includes/Plugins/BeforeModule/");
		while ($file = readdir ($handle)) {
			if (strpos($file,"inc.php") !== false)
				include $GLOBALS["Program_Dir"]."Includes/Plugins/BeforeModule/".$file;
		}
		closedir($handle);
	}	
?>
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
			include "./Includes/mainteance.inc.php";
			//echo "<div style = 'visibility:visible;' id = 'warning'>".$GLOBALS["Program_Language"]["Offline"]."<a href='javascript:void(0)' onclick='displayorhideWarning();'>OK</a></div>";
			exit;
		}
	}
	if (isset($_SESSION["user_logged_in"]) == false)
	{
		include "./Includes/branding.inc.php";
	}
	if (isset($_SESSION["user_logged_in"]))
	{		
		user_apply_Informations();
		//Include the status bar and menu and the wanted file
		include "./Includes/statusbar.inc.php";
		//Include the menu bar
		include "./Includes/menubar.inc.php";
		//Display content itself
		echo "<div id = 'content'>";
		if (isset($_GET["module"]) && strpos($_GET["module"],"..") === false && strpos($_GET["module"],".") === false){
			//Include the requested file			
			$path = $GLOBALS["Program_Dir"]."Includes/".$_GET["module"].".inc.php";			
			if (file_exists($path))
				include $path;		
		}
		else if (isset($_GET["module"]) == false && isset($_GET["share"]) == false){
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
	else if (isset($_GET["share"]))
		include "./Includes/share.inc.php";		
	else if (isset($_GET["module"]) && $_GET["module"] == "setpass")
		include "./Includes/setpass.inc.php";		
	else if (isset($_GET["module"]) && $_GET["module"] == "health")
		include "./Includes/health.inc.php";	
	else
		include "./Includes/Login.inc.php";	
	
?>
</div>
<?php
	//Display the version if wanted
	if ($GLOBALS["config"]["Program_Display_Version"])
		echo "<div id = 'version'>".$GLOBALS["Program_Version"]."";
	$end = microtime(true);
	if ($GLOBALS["config"]["Program_Display_Loadtime"])
		echo "<br><small>". sprintf($GLOBALS["Program_Language"]["Loadtime"],round($end-$start,4))."</small></div>";
	else
		echo "</small></div>";
?>
<?php
	if ($GLOBALS["config"]["Program_Enable_Plugins"] == 1)
	{
		$handle=opendir ($GLOBALS["Program_Dir"]."Includes/Plugins/AfterModule/");
		while ($file = readdir ($handle)) {
			if (strpos($file,"inc.php") !== false)
				include $GLOBALS["Program_Dir"]."Includes/Plugins/AfterModule/".$file;
		}
		closedir($handle);
	}
?>
<?php
	if (isset($_GET["message"]))
	{		
		$message = $_GET["message"];
		$image = "./Images/error.png";			
		if (isset($_GET["img"]))
			$image = "./Images/".$_GET["img"].".png";		
		if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1){			
			echo "<div id='message' title='Information'><p><img style='margin-right:5px'src = '$image'>".$GLOBALS["Program_Language"][$message]."</p></div>";
		}
		else
		{
			echo "<div id='warning'><p><img style='margin-right:5px'src = '$image'>".$GLOBALS["Program_Language"][$message]."</p></div>";
		}
	}
?>
</body>
</html>
