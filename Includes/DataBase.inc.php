<?php
	//DataBase Connection file.
	$connect = mysql_connect("localhost", "user", "pass") or die("DataBase Error: '".mysql_error());
	mysql_select_db("Scambio",$connect) or die("");
?>
