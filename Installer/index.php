<html>
<head>
<meta charset="utf-8">
<title>Redundancy Installation</title>
<script src="Lib/jquery-1.10.2.min.js"></script>
<script src="Lib/bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="Lib/bootstrap/css/custom.css" type="text/css"/>
<link rel="stylesheet" href="Lib/bootstrap/css/bootstrap.min.css" type="text/css"/>
<link rel="icon" href="./favicon.ico">
</head>
<body>
<div class="col-md-4 hidden-xs"></div>
<div class="col-md-4 col-xs-12">
<p style ="text-align:center">
<img src="./Images/bootstrapped_logo.png" style="margin: 0 auto;" class="img-responsive">
<h1 class="text-center">Redundancy<sup>2</sup></h1>
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
<div class="panel panel-default">
<div class="panel-body">
<form class="form-horizontal" role="form" method="POST" action="index.php">
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Username</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="user" name="user" placeholder="user">
			</div>
		</div>
		<div class="form-group">
			<label for="inputPassword" class="col-lg-3 control-label">Password</label>
			<div class="col-lg-9">
				<input type="password" class="form-control" id="pass" name = "pass" placeholder="Password">
			</div>
		</div>	
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Server</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="server" name="server" placeholder="localhost">
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Database</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="db" name="db" >
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Program directory</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" value="<?php
					echo str_replace("Installer/index.php","",$_SERVER["SCRIPT_FILENAME"]);
				?>" id = "dir" name="dir"  />
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Root User</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="rootname" name="rootname" >
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Root pass</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="rootpass" name="rootpass" >
			</div>
		</div>
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
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Storage directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="storage" name="storage" placeholder="Storage">
	</div>
</div>
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Temp directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="temp" name="temp" placeholder="Temp">
	</div>
</div>
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Snapshots directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="snapshots" name="snapshots" placeholder="Snapshots" >
	</div>
</div>
</div>

<p class="loginSubmit">
    <input type="submit" value="Save" />
</p>
</form>
</div>
</div>
<div id = "version">
Redundancy Installer 0.2 alpha
</div>
</body>
</html>
