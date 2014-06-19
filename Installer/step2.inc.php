<?php
	$fail = false;
	if (isset($_POST["Host"])){
		$host = $_POST["Host"];
		$user = $_POST["Username"];
		$pass = $_POST["Password"];
		$db = $_POST["Databasename"];		
		$connect = @mysqli_connect("$host", "$user", "$pass");		
		if (mysqli_connect_error() || $connect == false) {           
            ?>
				<div class="panel panel-default">
				<div class="panel-heading">No connection could be etablished!</div>
					<div class="panel-body alert-danger">
						<?php echo mysqli_connect_error();?>
					</div>
				</div>
			<?php
			$fail = true;
        }
        if (!@mysqli_select_db($connect,"$db")){
        	?>
				<div class="panel panel-default">
				<div class="panel-heading">Could not select the database!</div>
					<div class="panel-body alert-danger">
						The database seems not to exist. Please check your spelling.
					</div>
				</div>
			<?php
			$fail = true;
        }
		@mysqli_close($connect);	
		if ($fail == false){
			inst_create_DataBaseConfig($user,$pass,$host,$db);
		}	
	}

?>
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
    60%
  </div>
</div>
<form class="form-horizontal" role="form" method="POST" action="./index.php?step=2">
	<div class="form-group">
		<label for="inputEmail" class="col-lg-3 control-label">Host</label>
		<div class="col-lg-9">
			<input type="text" class="form-control" id="Host" name="Host" placeholder="Host" value="<?php if (isset($_POST["Host"])) echo $_POST["Host"];?>">
		</div>
	</div>
	<div class="form-group">
		<label for="inputEmail" class="col-lg-3 control-label">Username</label>
		<div class="col-lg-9">
			<input type="text" class="form-control" id="Username" name="Username" placeholder="Username" value="<?php if (isset($_POST["Username"])) echo $_POST["Username"];?>">
		</div>
	</div>
	<div class="form-group">
		<label for="inputPassword" class="col-lg-3 control-label">Password</label>
		<div class="col-lg-9">
			<input type="password" class="form-control" id="Password" name="Password" placeholder="Password" value="<?php if (isset($_POST["Password"])) echo $_POST["Password"];?>">
		</div>
	</div>		
	<div class="form-group">
		<label for="inputPassword" class="col-lg-3 control-label">Database name</label>
		<div class="col-lg-9">
			<input type="text" class="form-control" id="Databasename" name="Databasename" placeholder="Database name" value="<?php if (isset($_POST["Databasename"])) echo $_POST["Databasename"];?>">
		</div>
	</div>	
	 <div class="form-group">
		<label for="inputLanguage" class="col-lg-3 control-label">Type</label>
		<div class="col-lg-9">
			<select class="form-control" id="Type" name="Type">
			   <option>MySQL</option>
			</select>
		</div>
	</div>
	<div class="alert alert-warning">
		<b>Warning:</b> The installer will automatically create the database structure. Please check if your database is really empty. The installer can not check if the database is really empty.
	</div>
	<div class="installerButtons">
	<input type = "submit" href="./index.php?step=2" class="btn btn-default" value="Continue"/>
				
	<a href="about:blank" class="btn btn-default">Abort</a>		
	</div>												
</form>