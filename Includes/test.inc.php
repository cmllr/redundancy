  <style>
  .ui-menu { width: 150px; }
  </style>

<?php
	function ui_create_contextmenu($hashcode,$count)
	{
		++$count;
		echo "<small><ul id='context_menu$count' style ='position:absolute;'><li ><a href='?module=share&file=$hashcode'>".$GLOBALS["Program_Language"]["Share"]."</a></li></ul></small>";
		echo "<script>$(function() {\$('#context_menu$count').toggle();\$('#context_menu$count').menu();\$('$hashcode').bind('contextmenu', function(e){e.preventDefault();\$('#context_menu$count').toggle();return false;});";
		echo "\$('#context_menu$count').mouseleave(function(){\$(this).hide();});});";  
		echo "</script>";
	}
	ui_create_contextmenu("#lol2",1);
?>
 <p id ="lol2" href="#">gurke2</p>