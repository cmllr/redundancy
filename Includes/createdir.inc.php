<?php
		
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//only proceed if a post parameter is set
	
	if ($_SESSION["role"] != 3 && isset($_POST["directory"]) && endsWith($_POST["directory"],"/") == false && $_POST["directory"] != "")
	{			
		//only proceed if the user is logged in and we have a valid user_id
		if (isset($_SESSION['user_id']))
		{					
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Re-Release of 1.9.8
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
	
			$dirs = explode(";",$_POST["directory"]);
			for ($i = 0; $i < count($dirs);$i++){
				$dir_parts = explode("/",$dirs[$i]);
				$dir_parts_before = $_SESSION["currentdir"];
				$last = $_SESSION["currentdir"];
				for ($x = 0; $x < count($dir_parts);$x++)
				{
					
					echo "dir".$dir_parts[$x]."<br>";					
					echo "in ".$dir_parts_before."<br>";					
					createDir(mysqli_real_escape_string($connect,$dir_parts_before),mysqli_real_escape_string($connect,$dir_parts[$x]));			
					$dir_parts_before .= $dir_parts[$x]."/";		
				} 
				
					
			}		
<<<<<<< HEAD
=======
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";		
			if (substr_count($_POST["directory"],"/") != 1){
				createDir(mysqli_real_escape_string($connect,$_SESSION["currentdir"]),mysqli_real_escape_string($connect,$_POST["directory"]));					
			}			
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
=======
>>>>>>> Re-Release of 1.9.8
			//TODO: Display error messages
		}		
	}
	else if(isset($_POST["directory"]) == true && (endsWith($_POST["directory"],"/") == true || $_POST["directory"] == "" ))
	{
		header("Location: ./index.php?message=wronginput");
	}
	else if ($_SESSION["role"] == 3)
	{
			header("Location: index.php?message=readonly");
	}
?>
<form method="POST" action="index.php?module=createdir" align = "center">
<div class = 'contentWrapper'>
<?php
	include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
?>	
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Re-Release of 1.9.8
<small>
<?php
	echo $GLOBALS["Program_Language"]["multiple_dirs"];
?>
</small>
<br>
<tag><?php echo $GLOBALS["Program_Language"]["New_Directory"]." ". $_SESSION["currentdir"];?></tag><input name="directory">
<<<<<<< HEAD
=======
<tag><?php echo $GLOBALS["Program_Language"]["New_Directory"]." ". $_SESSION["currentdir"];?><r></tag><input name="directory">
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
=======
>>>>>>> Re-Release of 1.9.8
<input type=submit name=submit value="<?php echo $GLOBALS["Program_Language"]["New_Directory_Button"];?>"></div>
</form>