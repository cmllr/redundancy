<html>
<head>
<meta charset="utf-8">
<title>Redundancy Installation</title>
<link rel = "stylesheet" href="./Style new.css" type = "text/css"/>
<!--todo: dirs -->
 <link rel="stylesheet" href="../Demo/Lib/themes/cupertino/jquery-ui.css" />
<script src="../Demo/Lib/jquery-1.10.2.min.js"></script>
<script src="../Demo/Lib/ui/jquery-ui.js"></script>
<script type="text/javascript" src="../Demo/Lib/jquery-notify.js"></script>
<link rel="stylesheet" href="../Demo/Lib/notify.css"></link>
</head>
<body>
<div id = "contentWrapper">
<p style ="text-align:center">
<img src = "./Images/Logo.png" style="display:float">
<br>

<?php	
$GLOBALS["fail"] = 0;
error_reporting(E_ALL);
	
	if (isset($_POST["user"],$_POST["pass"],$_POST["server"],$_POST["db"],$_POST["dir"],$_POST["storage"],$_POST["temp"],$_POST["snapshots"],$_POST["rootname"],$_POST["rootpass"])){
		include "Kernel.Installer.inc.php";	
		echo "<i>Creating database config...</i><br>";
		if ($GLOBALS["fail"] == 0 && strlen($_POST["user"]) != 0 && strlen($_POST["pass"])  != 0 && strlen($_POST["server"])  != 0  && strlen($_POST["db"]) != 0 )
		{	
			inst_create_DataBaseConfig($_POST["user"],$_POST["pass"],$_POST["server"],$_POST["db"]);
			echo "<img src = './Images/accept.png'>...done<br>";
		}
		else
		{
			$GLOBALS["fail"]++;
			echo "<img src = './Images/exclamation.png'>...failed<br>";
		}
		echo "<i>Checking directory permissions...</i><br>";
		if ($GLOBALS["fail"] == 0 && strlen($_POST["dir"]) != 0 && strlen($_POST["storage"])  != 0 && strlen($_POST["dir"]) != 0  && strlen($_POST["temp"])   != 0 && strlen($_POST["snapshots"])  != 0 )
		{	
			inst_check_directory_rights($_POST["dir"].$_POST["storage"],$_POST["dir"].$_POST["temp"],$_POST["dir"].$_POST["snapshots"]);
			echo "<img src = './Images/accept.png'>...done<br>";
		}
		else
		{
			$GLOBALS["fail"]++;
			echo "<img src = './Images/exclamation.png'>...failed<br>";
		}
		echo "<i>Creating the configuration ...</i><br>";
		if ($GLOBALS["fail"] == 0 && strlen($_POST["storage"]) != 0 && strlen($_POST["temp"])  != 0 && strlen($_POST["snapshots"])  != 0 )
		{
			inst_apply_configuration($_POST["dir"],$_POST["storage"],$_POST["temp"],$_POST["snapshots"]);		
			echo "<img src = './Images/accept.png'>...done<br>";
		}
		else
		{
			$GLOBALS["fail"]++;
			echo "<img src = './Images/exclamation.png'>...failed<br>";
		}
		echo "<i>Creating the root user ...</i><br>";
		if ($GLOBALS["fail"] == 0 && strlen($_POST["rootpass"]) != 0 && strlen($_POST["rootname"])  != 0 )
		{	
			inst_create_root($_POST["rootname"],$_POST["rootpass"]);
			
			echo "<img src = './Images/accept.png'>...done<br>";
		}
		else
		{
			$GLOBALS["fail"]++;
			echo "<img src = './Images/exclamation.png'>...failed<br>";
		}
		if ($GLOBALS["fail"] != 0)
			echo "<img src = './Images/accept.png'> Installation failed";
		else
		{
			inst_check();
		}
		exit;
	}	
?>
</p>
<form method="POST" action="index.php" id="login">

<p>
    <label for="user">Username</label>
    <input class ="text" id ="user" name="user" />
</p>
<p>
    <label for="pass">Password</label>
    <input class ="text"  id = "pass" name="pass" type="password" />
</p>
<p>
    <label for="server">Server</label>
    <input class ="text"  id = "server" name="server"  value="localhost"/>
</p>
<p>
    <label for="db">Database</label>
    <input class ="text"  id = "db" name="db"  />
</p>
<p>
    <label for="dir">Program directory</label>
    <input  class ="text"  value="
<?php
	echo str_replace("Installer/index.php","",$_SERVER["SCRIPT_FILENAME"]);
?>" id = "dir" name="dir"  />
</p>
<p>
    <label for="rootname">Root User</label>
    <input class ="text"  id = "rootname" name="rootname"  />
</p>
<p>
    <label for="rootpass">Root pass</label>
    <input class ="text"  id = "rootpass" name="rootpass"  />
</p>
<script>
   
	   
$(document).ready(function(){
  $("#profi").hide()
  $('#expander').click(function(){
		$("#profi").slideToggle(200); 
    });
	
});

</script>
<a id = "expander" href ="#">Advanced...</a>
<div id = "profi">
<p>
    <label for="storage">Storage directory</label>
    <input value = "Storage" class ="text"  id = "storage" name="storage"  />
</p>
<p>
    <label for="temp">Temp directory</label>
    <input value = "Temp" class ="text"  id = "temp" name="temp"  />
</p>
<p>
    <label for="snapshots">Snapshots directory</label>
    <input value = "Snapshots" class ="text"  id = "snapshots" name="snapshots"  />
</p>
</div>
<p class="loginSubmit">
    <input type="submit" value="Save" />
</p>
</form>
</div>
<div id = "version">
Redundancy Installer 0.1 alpha
</div>
</body>
</html>
