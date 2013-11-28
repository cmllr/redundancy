<html>
<head>
<meta charset="utf-8">
<title>Redundancy Installation</title>
<script src="Lib/jquery-1.10.2.min.js"></script>
<script src="Lib/bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="Lib/bootstrap/css/custom.css" type="text/css"/>
<link rel="stylesheet" href="Lib/bootstrap/css/bootstrap.min.css" type="text/css"/>
<link rel="stylesheet" href="Lib/bootstrap/css/elusive-webfont.css">
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
$GLOBALS["ERRORS"] = array();
error_reporting(E_ALL);
	if (isset($_POST)){
		if (isset($_POST["user"],$_POST["pass"],$_POST["server"],$_POST["db"],$_POST["dir"],$_POST["storage"],$_POST["temp"],$_POST["snapshots"],$_POST["rootname"],$_POST["rootpass"]) &&  !empty($_POST["user"]) && !empty($_POST["server"]) && !empty($_POST["db"]) && !empty($_POST["dir"]) && !empty($_POST["storage"]) && !empty($_POST["temp"]) && !empty($_POST["snapshots"]) && !empty($_POST["rootname"]) && !empty($_POST["rootpass"])){
			include "Kernel.Installer.inc.php";	
			echo "<h3 class=\"text-center\">Installation result</h3>";
			echo "<table>";
			echo "<tr><th></th><th></th></tr>";
			echo "<tr>";		
			if (strlen($_POST["user"]) != 0 && strlen($_POST["server"])  != 0  && strlen($_POST["db"]) != 0 )
			{	
				
				inst_create_DataBaseConfig($_POST["user"],$_POST["pass"],$_POST["server"],$_POST["db"]);		
			}
			else
			{
				$GLOBALS["fail"]++;
				echo "<td><span class=\"successValue elusive icon-remove glyphIcon\"></span></td>";
			}
			echo "<td>Creating database config</td></tr>";
			if (strlen($_POST["dir"]) != 0 && strlen($_POST["storage"])  != 0 && strlen($_POST["dir"]) != 0  && strlen($_POST["temp"])   != 0 && strlen($_POST["snapshots"])  != 0 )
			{	
				inst_check_directory_rights($_POST["dir"].$_POST["storage"],$_POST["dir"].$_POST["temp"],$_POST["dir"].$_POST["snapshots"]);
				
			}
			else
			{
				$GLOBALS["fail"]++;
			}		
			if (strlen($_POST["storage"]) != 0 && strlen($_POST["temp"])  != 0 && strlen($_POST["snapshots"])  != 0 )
			{
				inst_apply_configuration($_POST["dir"],$_POST["storage"],$_POST["temp"],$_POST["snapshots"]);		
				
			}
			else
			{
				$GLOBALS["fail"]++;			
			}
				
			echo "<tr>";
			if (strlen($_POST["rootpass"]) != 0 && strlen($_POST["rootname"])  != 0 )
			{	
				inst_create_root($_POST["rootname"],$_POST["rootpass"]);			
			}
			else
			{
				$GLOBALS["fail"]++;			
			}
			echo "<td>Creating the root user</td> </tr>";		
			echo "</table>";
			inst_check();
			exit;
		}	
		else if (isset($_POST)  && !empty($_POST))
		{			
			echo "<div class = \"alert  alert-info\">Fill out <b>all</b> the input values</div>";
		}
	}
?>
<div class="panel panel-default">
<div class="panel-body">
<form class="form-horizontal" role="form" method="POST" action="index.php">
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Database user</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="user" name="user" placeholder="user">
			</div>
		</div>
		<div class="form-group">
			<label for="inputPassword" class="col-lg-3 control-label">Database user password</label>
			<div class="col-lg-9">
				<input type="password" class="form-control" id="pass" name = "pass" placeholder="Password">
			</div>
		</div>	
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Server</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="server" name="server" value="localhost">
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
			<label for="inputEmail" class="col-lg-3 control-label">Admin user</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="rootname" name="rootname" >
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Admin password</label>
			<div class="col-lg-9">
				<input type="password" class="form-control" id="rootpass" name="rootpass" >
			</div>
		</div>
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Storage directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="storage" name="storage" value="Storage">
	</div>
</div>
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Temp directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="temp" name="temp" value="Temp">
	</div>
</div>
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Snapshots directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="snapshots" name="snapshots" value="Snapshots" >
	</div>
</div>

<p class="loginSubmit">
    <input type="submit" class = "btn btn-default" value="Save" />
</p>
</form>
</div>
</div>
</body>
</html>
