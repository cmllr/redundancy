<?php
	//DataBase Connection file.
	//Needed for any operations of the program
	//replace user with your username
	//replace pass with your password
	//replace db with your database
	$connect = mysql_connect("localhost", "user", "pass") or die("Error: 005 ".mysql_error());
	mysql_select_db("db",$connect) or die("Error: 006 ".mysql_error());  
?>