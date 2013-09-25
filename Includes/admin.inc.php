<ul class="nav nav-tabs" id="dataTabs">
<li >
	<a href="#Administration" data-toggle="tab">Administration</a>
</li>
<li>
	<a href="#Status" data-toggle="tab">Status</a>
</li>
<li>
	<a href="#Edit" data-toggle="tab">Edit</a>
</li>
<li>
	<a href="#New" data-toggle="tab">New</a>
</li>
</ul>
<script>
$(function(){
	$('#dataTabs li:eq(0) a').tab('show');
});
</script>
<div class="tab-content" id ="tab-content">
<div class="tab-pane" id="Administration">
<div class="panel panel-default">
<div class="panel-body">
Add admin remarks here	
</div>
</div>
</div>
<div class="tab-pane" id="Status">
<div class="panel panel-default">
<div class="panel-body">
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
	 * This file provides the administration panel
	 */
	//Include uri check
	require_once ("checkuri.inc.php");
	//start a session if needed		
		
	if (isset($_SESSION) == false)
			session_start();	
	//TODO: Implement admin check
	if ($_SESSION["role"] == 0 && is_admin() && $GLOBALS["config"]["Program_Enable_Web_Administration"] == 1)
	{
		//echo "<h2>".$GLOBALS["Program_Language"]["Admin_Panel"]."</h2>";
		include "health.inc.php";
		$snapshotcount = 0;
		$last = "-";
		if ($handle = opendir($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/")) {
			while (false !== ($file = readdir($handle))) {			
				if ($file != "." && $file != ".." && endsWith($file,".zip") )
				{		
					$snapshotcount++;
					$last = date ("H:i:s d.m.y", filemtime($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/".$file));
				}
			}		
		}
		closedir($handle);	
		if ($snapshotcount == 0)
			echo "<span class=\"successValue elusive icon-remove glyphIcon\"></span>0 Snapshots<br>";
		else
			echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>".$snapshotcount ." Snapshot(s) (".$last.")<br>";	
	}		
	else
	{
		echo "You don't have the rights to access this page or the web interface is disabled";
		exit;
	}
	echo "<br>";
?>
<a type="a" href = 'index.php?module=snapshot' class="btn btn-default"><span class="elusive icon-camera glyphIcon"></span>Snapshot</a>
</div>
</div>
</div>
<div class="tab-pane fade" id="Edit">
<div class="panel panel-default">
<div class="panel-body">
<form method="POST" action="index.php?module=admin" >
<?php if (isset($_POST["username_info"])) : ?>
<script>
$(function(){
	$('#dataTabs li:eq(2) a').tab('show');
});
</script>
<?php	
		if (isset($_POST["username_info"])){
			if (isExisting("",$_POST["username_info"]) == false)
			{
				echo "No such user";
				exit;
			}		
			if (isset($_POST["role"]) == false )
			{			
				echo "<input type=\"text\" name=\"username_info\" value=\"".$_POST["username_info"]."\" READONLY/><br>";
				echo "<p>".$GLOBALS["Program_Language"]["role"];
				echo " <input type=\"radio\" name=\"role\" value=\"0\"";
				if (user_get_role($_POST["username_info"]) == 0)
					echo "CHECKED />";
				else
					echo "/>";
				echo "Administrator ";
				echo "<input type=\"radio\" name=\"role\" value=\"1\"";
				if (user_get_role($_POST["username_info"]) == 1)
					echo "CHECKED />";
				else
					echo "/>";
				echo "User ";
				echo "<input type=\"radio\" name=\"role\" value=\"3\"";
				if (user_get_role($_POST["username_info"]) == 3)
					echo "CHECKED />";
				else
					echo "/>";
				echo "Guest ";	
				echo "</p>";
			
				echo "<p><input type=\"checkbox\" name=\"lock\"";
				if (user_get_enabled($_POST["username_info"]) == 1)
					echo " checked=\"checked\"/> ".$GLOBALS["Program_Language"]["enabled_user"]."</p>";
				else
					echo "/> ".$GLOBALS["Program_Language"]["enabled_user"]."</p>";
				//TODO: bootstrap
				//echo "<br><a type=\"a\" href = 'index.php?module=admin#Edit 'class=\"btn btn-default\"><span class=\"elusive icon-user glyphIcon\"></span>Edit Other</a>";
			}			
			if (isset($_POST["role"]) )
			{				
				user_save_administration();			
			}
		}		
?>
 
  <?php echo $GLOBALS["Program_Language"]["Size"];?>
 <input name="storage" value="<?php
	echo user_get_storage($_POST["username_info"]);
  ?>"/>
  Minimum: <?php echo fs_get_fitting_DisplayStyle(round(getUsedSpace($_POST["username_info"]),0,PHP_ROUND_HALF_UP));?>
<br>
    <?php echo $GLOBALS["Program_Language"]["New_Pass"];?> <input name="user_new_pass">
<?php
	echo $GLOBALS["Program_Language"]["pass_hint"].": ".getRandomPass($GLOBALS["config"]["User_Recover_Password_Length"]);  
?>
<br>
<input type=submit name=submit value="<?php echo $GLOBALS["Program_Language"]["Save"];?>">
<br>
</form>
<form method="POST" action="index.php?module=moduser&task=delete&user=<?php echo $_POST["username_info"]?>">
<?php echo $GLOBALS["Program_Language"]["user_delete_admin"];?><input type="submit" value="<?php echo $GLOBALS["Program_Language"]["Delete"]; ?>" />
</form>
<?php endif;?>
<?php if (isset($_POST["username_info"]) == false): ?>
<form role="form" method="POST" action="index.php?module=admin">
  <div class="form-group">
    <label for="inputUsername"><?php echo $GLOBALS["Program_Language"]["get_user_info"];?></label>
      <div class="input-group" id="inputUsername">
  <span class="input-group-addon">User</span>
  <input name="username_info" type="text" class="form-control" placeholder="Benutzername">
  <span class="input-group-btn">
        <button class="btn btn-default" type="submit"><?php echo $GLOBALS["Program_Language"]["get_user_info"];?></button>
      </span>
</div>
  </div>
</form>
<?php endif;?>	
</div>
</div>
</div>
<div class="tab-pane fade" id="New">
<div class="panel panel-default">
<div class="panel-body">
<?php if (isset($_POST["user_create"],$_POST["pass_create"])) :?>
<script>
$(function(){
	$('#dataTabs li:eq(3) a').tab('show');
});
</script>
<?php endif; ?>
<?php
	if (isset($_POST["user_create"],$_POST["pass_create"])){
		
		$pEmail = $_POST["user_create"];
		$pPass = $_POST["pass_create"];
		$pPassRepeat = $pPass;
		$pSystem = 1;
		if (registerUser($pEmail,$pPass,$pPassRepeat,$pSystem) == true)
		{
			header("Location: index.php?module=admin&message=user_create_admin_success");
		}
		else
		{
			header("Location: index.php?module=admin&message=user_create_admin_fail");
		}
	}
?>
<form role="form" method="POST" action="index.php?module=admin">
  <div class="form-group">
    <label for="inputUsername"><?php echo $GLOBALS["Program_Language"]["Username"]; ?></label>
    <input type="text" name="user_create" class="form-control" id="inputUsername" placeholder="<?php echo $GLOBALS["Program_Language"]["Username"]; ?>">
  </div>
  <div class="form-group">
    <label for="inputPassword"><?php echo $GLOBALS["Program_Language"]["Password"]; ?></label>
    <input type="password" name="pass_create" class="form-control" id="inputPassword" placeholder="<?php echo $GLOBALS["Program_Language"]["Password"]; ?>">
  </div>
  <button type="submit" class="btn btn-default"><?php echo $GLOBALS["Program_Language"]["Save"];?></button>
</form>
</div>
</div>
</div>
</div>
