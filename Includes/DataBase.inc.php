<?php
	//DataBase Connection file.
	//Needed for any operations of the program
	//replace user with your username
	//replace pass with your password
	//replace db with your database
	$connect = mysqli_connect("localhost", "user", "pass") or die("Error: 005 ".mysqli_error());
	mysqli_select_db($connect,"db") or die("Error: 006 ".mysqli_error()); 	
?>