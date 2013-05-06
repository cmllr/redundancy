<?php
	//DataBase Connection file.
	$connect = mysql_connect("localhost", "root", "90104e58") or die("DataBase Error: '".mysql_error());
	mysql_select_db("Scambio",$connect) or die("");
?>
