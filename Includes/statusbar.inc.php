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
	 * This file creates the sidebar of the program (containg the progressbar)
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
?>
<div id = "statusbar">     
<?php if ($GLOBALS["config"]["Program_Enable_JQuery"] == 1) : ?>
<script>   
$(document).ready(function(){
 
    $("#sidebar").hide();
    $("#accountname").show();	
    $('#accountname').click(function(){
		$("#sidebar").slideToggle(200); 
    });
	$('#sidebar').mouseleave(function(){
		$("#sidebar").slideToggle(200);
	});
	
});
</script>
<?php endif; ?>
<?php
	if (isset($_SESSION["user_logged_in"])){
		include $GLOBALS["Program_Dir"]."Includes/Menu.inc.php";
		if ($GLOBALS["config"]["Program_Enable_JQuery"] == 0)
			echo "<a id = 'accountname'  href = 'javascript:void(0)' onclick = 'displayorhide();'><img src = './Images/user_orange.png'>".$_SESSION['user_name']." (".$_SESSION['user_email'].")</a>";
		else
			echo "<a id = 'accountname'  href = '#'><img src = './Images/user_orange.png'>".$_SESSION['user_name']." (".$_SESSION['user_email'].")</a>";
		
	}	
?>
</div>