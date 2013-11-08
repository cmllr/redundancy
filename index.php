<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * Program start point
	 */
	 
	$start = microtime(true);
	//first - start a session if needed
	if (isset($_SESSION) == false)
		session_start();
	//Include the main program file
	include "./Includes/Program.inc.php";	
	//Parse the configuration file
	//User settings (if enabled) will overwrite some of them
	$GLOBALS["config"] = parse_ini_file($GLOBALS["config_dir"]."Redundancy.conf");
	//Set the program path (very important)
	$GLOBALS["Program_Dir"] = $GLOBALS["config"]["Program_Path"];
	if ($GLOBALS["config"]["Program_Enable_ErrorHandler"] == 1)
		setExceptionHandler();
	if ($GLOBALS["config"]["use_buffer"] == 1)
		ob_start();
	//Rename the user name value if the user is logged in and do a check if needed
	if (isset($_SESSION["user_name"])){
		renameUserSessionIfNeeded();
		if (isset($_SESSION["begin"])  == false || !checkSessionTimeout($_SESSION["begin"]))
			logoutUser("session_stopped_fail");
	}
	//Load user defined options from the database if enabled by config
	if (isset($_SESSION["user_name"]) && $GLOBALS["config"]["Program_Enable_User_Settings"] == 1)
		loadUserSettings();
	//******************************Modules, which can be included directly*************************
	if (isset($_GET["module"]) && $_GET["module"] == "image" )
	{
		include "./Includes/image.inc.php";			
		exit;
	}
	elseif (isset($_GET["share"])){
		include $GLOBALS["Program_Dir"]."Includes/share.inc.php";	
	}
	elseif (isset($_GET["module"]) && $_GET["module"] == "player" )
	{
		include "./Includes/player.inc.php";
	}	
	elseif (isset($_GET["module"]) && $_GET["module"] == "webdav" )
	{
		include "./Includes/Source.inc.php";
		exit;
	}	
	//****************************Exceptions for dynamically loaded content//**************************
	if (isset($_GET["search"]) == true || isset($_GET["upload"]) == true || isset($_GET["newdir"]) == true)
	{
		if (isset($GLOBALS["Program_language"]) == false)
		{	
			if (isset($_SESSION["language"]) && strpos($_SESSION["language"],"..") === false)
			{
				$GLOBALS["Program_Language"] = parse_ini_file("./Language/".$_SESSION["language"].".lng");	
			}
			else
			{
				$GLOBALS["Program_Language"] = parse_ini_file("./Language/".$GLOBALS["config"]["Program_Language"].".lng");
			}
		}
		if (isset($_GET["search"]))
			include "./Includes/search.inc.php";
		else if (isset($_GET["newdir"]))
			include "./Includes/createdir.inc.php";
		else
			include "./Includes/upload.inc.php";
		exit;
	}
 ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php
	//Internet Explorer fix
	if (isset($_FILES,$_POST) == false)
		header('Content-type: text/html; charset=utf-8');
?>
<?php if ($GLOBALS["config"]["Program_Display_Generator_Tag"] == 1): ?>
<meta name="generator" content="<?php echo $GLOBALS["config"]["Program_Name_ALT"]." ".$GLOBALS["Program_Version"];?>" />
<?php endif;?>
<link rel = "stylesheet" href="./Lib/bootstrap/css/bootstrap.min.css" type = "text/css"/>
<?php	
	if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1)
		include "./Lib/JQuery.inc.php";
?>
<?php
	if (isset($_SESSION["template"]))
		echo $_SESSION["template"]["Template_Header"];
?>
<link rel="icon" href="./favicon.ico" >
<title>
<?php	
	//Check xss problems and check if the user could be banned
	if (isXSS() == true || ($GLOBALS["config"]["Program_Enable_Banning"] && isBanned()))
	{
		echo "Attack found</title>";
		echo "<center><img src = \"./Images/AnimatedStop.gif\"><div style = 'visibility:visible;' id = 'warning'>You did an attack or your IP is banned. Redundancy will stop here.<br>*<br>This violation was reported<br>*<br>Dieser Vorgang wurde berichtet<br></div></center><body></body></html>";
		exit;
	}	
	//Language settings
	if (isset($GLOBALS["Program_language"]) == false)
	{	
		if (isset($_SESSION["language"]) && strpos($_SESSION["language"],"..") === false)
		{
			$GLOBALS["Program_Language"] = parse_ini_file("./Language/".$_SESSION["language"].".lng");	
		}
		else
		{
			$GLOBALS["Program_Language"] = parse_ini_file("./Language/".$GLOBALS["config"]["Program_Language"].".lng");
		}
	}	
	//Enable the debug mode (display errors) or not
	if ($GLOBALS["config"]["Program_Debug"] == 1)
			error_reporting(E_ALL);
	
	//Display the Program name and calculate the user space if a session is set
	echo $GLOBALS["config"]["Program_Name_ALT"];
	if (isset($_SESSION["user_name"])){
		//Set the user contingent and refresh the information about used space
		setUsedStorage($_SESSION['user_name']);	
		//Check the user session if any sql injections are done
		//if the $_SESSION value differs with the value result of mysqli_real_escape_string
		if (isSessionCorrupted() == true)
		{
			echo "SQL Injection found</title>";
			echo "<div style = 'visibility:visible;' id = 'warning'>SQL Injection found.</div><body></body></html>";
			exit;
		}		
	}	
	//Force https if enabled
	if ($GLOBALS["config"]["Program_HTTPS_Redirect"] == 1)
	{
		if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){			
			header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
	}
?>
</title>
</head>
<body> 
<div class = 'container'>
<div class = 'row'>
<?php	
	//Pre plugin loading
	if ($GLOBALS["config"]["Program_Enable_Plugins"] == 1)
	{
		$handle=opendir ($GLOBALS["Program_Dir"]."Includes/Plugins/");
		while ($file = readdir ($handle)) {
			if (strpos($file,"inc.php") !== false){				
				if (isset($GLOBALS["plugins"]) == false){
					$title = file_get_contents ($GLOBALS["Program_Dir"]."Includes/Plugins/".str_replace(".inc.php",".nav.php",$file));
					$GLOBALS["plugins"] = array($title => "Plugins/".str_replace(".inc.php","",$file) );
				}					
				else if (isset($GLOBALS["plugins"][$file]) == false){
					$title = file_get_contents ($GLOBALS["Program_Dir"]."Includes/Plugins/".str_replace(".inc.php",".nav.php",$file));
					$GLOBALS["plugins"][$title] = "Plugins/".str_replace(".inc.php","",$file) ;
				}
			}
		}
		closedir($handle);
	}	
?>
<noscript>
<div style = 'visibility:visible;' id = 'warning'><?php echo $GLOBALS["Program_Language"]["No_JS"];?></div>
</noscript>
<?php
	//Display a warning if the user uses internet explorer
	if ($GLOBALS["config"]["IE_Warning"] == 1 && !(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE") === false))
	{	
		echo "<div style = 'visibility:visible;' id = 'warning'>".$GLOBALS["Program_Language"]["IE_Warning"]."</div>";
	}	
	//Display mainteance message if Redundancy is not enabled
	if ($GLOBALS["config"]["Enable"] != 1 ) 
	{
		if ((isset($_GET["module"]) && ($_GET["module"] == "admin" || $_GET["module"] == "login" || $_GET["module"] == "logout" )) == false){
			include "./Includes/mainteance.inc.php";		
			exit;
		}
	}
	if (isset($_SESSION["user_logged_in"]))
	{		
		//apply user informations
		loadUserChanges();
		//Include the status bar and menu and the wanted file	
		include "./Includes/Header.inc.php";
		//Display content itself		
		echo "<div class=\"panel panel-default\"> " ;
		echo "<div class=\"panel-body\">";
			//Include the requested file	
		if (isset($_GET["module"]) && strpos($_GET["module"],"..") === false && strpos($_GET["module"],".") === false){
			$path = $GLOBALS["Program_Dir"]."Includes/".$_GET["module"].".inc.php";			
			if (file_exists($path))
				include $path;		
		}
		else if (isset($_GET["module"]) == false && isset($_GET["share"]) == false){
			//The startpage is an exception, it will be displayed if the module= parameter is not set.
			include $GLOBALS["Program_Dir"]."Includes/startpage.inc.php";		
		}		
		echo "</div>";
		echo "</div>";
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
<!--Display the right container, but only if logged in-->
<?php if (isset($_SESSION["user_logged_in"])): ?>
<div class="col-lg-2 col-md-2 visible-md visible-lg">
<div data-spy="affix" data-offset-top="140" class="affix-top">
	<div class="dropdown">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<span class = "elusive icon-user glyphIcon"></span><?php echo $_SESSION["user_name"];?> <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li>
				<a href="?module=account"><?php echo $GLOBALS["Program_Language"]["My_Account"];?></a>
			</li>		
			<?php if ($_SESSION["role"] == 0 && isAdmin()): ?>
				<li>
				<a href="?module=admin"><?php echo $GLOBALS["Program_Language"]["Administration"];?></a>
			</li>
			<?php endif;?>		
			<li>
				<a href="index.php?module=info">Info</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="?module=logout"><?php echo $GLOBALS["Program_Language"]["Exit"];?></a>
			</li>
		</ul>
	</div>
</div>
</div>
<?php endif;?>
<?php 
	if ($GLOBALS["config"]["use_buffer"] == 1)
		ob_end_flush();
?>
</div>
</body>
</html>
