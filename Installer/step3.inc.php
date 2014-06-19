<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%;">
    90%
  </div>
</div>
<div class="alert alert-info">
	The installation of Redundancy 2 is almost done.</br>
	Enter the user credentials you want to use as the administrative account
</div>
<?php
	if (isset($_POST["dir"])){
		if ($_POST["rootname"] == "" || $_POST["rootpass"] == ""){
			?>
				<div class="panel panel-default">
				<div class="panel-heading">Root user</div>
					<div class="panel-body alert-danger">
						The root user and/ or its password is empty. Redundancy does not allow empty passwords.
					</div>
				</div>
			<?
			$fail = true;
		}
		if ($fail == false){
			if ($_POST["storage"] != "" && $_POST["temp"] != "" && $_POST["snapshots"] != ""){
				inst_apply_configuration($_POST["dir"],$_POST["storage"],$_POST["temp"],$_POST["snapshots"]);	
				inst_create_root($_POST["rootname"],$_POST["rootpass"]);	
				header("Location: index.php?step=4");
				exit;
			}
		}
	}
?>
<div class="panel panel-default">
<div class="panel-body">
<form class="form-horizontal" role="form" method="POST" action="index.php?step=3">		
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Program directory</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" value="<?php
					echo str_replace("Installer/step3.inc.php","",__FILE__);
				?>" id = "dir" name="dir"  />
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Admin user</label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="rootname" name="rootname" value ="<?php if (isset($_POST["rootname"])) echo $_POST["rootname"];?>">
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">Admin password</label>
			<div class="col-lg-9">
				<input type="password" class="form-control" id="rootpass" name="rootpass" value ="<?php if (isset($_POST["rootpass"])) echo $_POST["rootpass"];?>">
			</div>
		</div>
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Storage directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="storage" name="storage" value="<?php if (isset($_POST["storage"]) && $_POST["storage"] != "") echo $_POST["storage"]; else echo "Storage";?>">
	</div>
</div>
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Temp directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="temp" name="temp" value="<?php if (isset($_POST["temp"]) && $_POST["temp"] != "") echo $_POST["temp"]; else echo "Temp";?>">
	</div>
</div>
<div class="form-group">
	<label for="inputEmail" class="col-lg-3 control-label">Snapshots directory</label>
	<div class="col-lg-9">
		<input type="text" class="form-control" id="snapshots" name="snapshots" value="<?php if (isset($_POST["snapshots"]) && $_POST["snapshots"] != "") echo $_POST["snapshots"]; else echo "Snapshots";?>">
	</div>
</div>
<div class="installerButtons">
	<input type = "submit" href="./index.php?step=3" class="btn btn-default" value="Continue"/>
				
	<a href="about:blank" class="btn btn-default">Abort</a>		
	</div>	
</form>
</div>
</div>
