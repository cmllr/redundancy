<div class="col-sm-3 col-md-4"></div>
<div class="col-sm-6 col-md-4">
<?php
	include "./Includes/branding.inc.php";
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
	 * The register process and dialog is stored in this file
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
if (isset($_POST["regemail"])){
	
	if (isset($_SESSION) == false)
			session_start();	

	if (registerUser($_POST["regemail"],$_POST["regpass"],$_POST["regpass_repeat"]) == true)
		header("Location: ./index.php?message=registersuccess");
	else
		header("Location: ./index.php?module=register&message=registerfail");
}
if ($GLOBALS["config"]["Enable_register"] == 0 && isset($_GET["renew"]) == false){
	if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1){
		header("Location: index.php?message=Register_disabled");
	}
	else{
		echo "<form id = 'login'><p>".$GLOBALS["Program_Language"]["Register_disabled"]."</p>";
		echo "<a class = 'actions' href = 'index.php'><img src='./Images/arrow_left.png'><?php".$GLOBALS["Program_Language"]["Back"].";?></a>";
		exit;
	}
}
?>
<div class="panel panel-default">
<div class="panel-body">
<form class="form-horizontal" role="form"  method="POST" action="index.php?module=register&renew=true">
		<div class="form-group">
			<label for="regemail" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Email"];?></label>
			<div class="col-lg-9">
				<input type="email" class="form-control" id="regemail" name="regemail" placeholder="Email">
			</div>
		</div>
		<div class="form-group">
			<label for="regpass" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Password"];?></label>
			<div class="col-lg-9">
				<input type="password" class="form-control" id="regpass" name = "regpass" placeholder="<?php echo $GLOBALS["Program_Language"]["Password"];?>">
			</div>
		</div>	
		<div class="form-group">
			<label for="regpass_repeat" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Repeat_Password"];?></label>
			<div class="col-lg-9">
				<input type="password" class="form-control" id="regpass_repeat" name = "regpass_repeat" placeholder="Password">
			</div>
		</div>				
		<div class="form-group">
			<div class="col-lg-offset-3 col-lg-9">
				<button type="submit" class="btn btn-default btn-block">
					<?php echo $GLOBALS["Program_Language"]["Register"];?>
				</button>
			</div>
		</div>
	</form>
</div>
</div>