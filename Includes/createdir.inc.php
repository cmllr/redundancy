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
<small>
<?php
	echo $GLOBALS["Program_Language"]["multiple_dirs"];
?>
</small>
<br>
<tag><?php echo $GLOBALS["Program_Language"]["New_Directory"]." ". $_SESSION["currentdir"];?></tag><input name="directory">
<input type=submit name=submit value="<?php echo $GLOBALS["Program_Language"]["New_Directory_Button"];?>"></div>
</form>