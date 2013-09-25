<div class="col-md-4 hidden-xs"></div>
<div class="col-md-4 col-xs-12">
<div class="panel panel-default">
<div class="panel-body">
<form class="form-horizontal" role="form" method="POST" action="index.php?module=recover" >
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
	 * The recover process and dialog is stored in this file
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
if (isset($_POST["email"])){
	
	if (isset($_SESSION) == false)
			session_start();	
	
	recover($_POST["email"]);
}
else
{
	include "./Includes/branding.inc.php";
}
if (isset($_GET["msg"]) && $_GET["msg"] == "success")
{
	echo "<h2>".$GLOBALS["Program_Language"]["recovered"]."</h2>";
	exit;
}
?>		
<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Email"];?></label>
			<div class="col-lg-9">
				<input type="text" class="form-control" id="email" name="email" placeholder="Email">
			</div>
		</div>	
		<input class = "btn btn-default "type="submit" value="<?php echo $GLOBALS["Program_Language"]["Recover"]; ?>" />
	</form>	
</div>
</div>
<div class="col-md-4 hidden-xs"></div> 