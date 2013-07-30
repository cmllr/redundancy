<ul>
<<<<<<< HEAD
<li>
<img id='imagelogo' src = "./Images/Logo_notext.png">
</li>
<li>
<a href = "index.php">
=======
<li><a href = "index.php">
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
<img src = "./Images/house.png">
<?php
if (isset($_SESSION) == false)
		session_start();
	echo $GLOBALS["Program_Language"]["Home"];
?>
<<<<<<< HEAD
</a>
</li>
=======
</a></li>
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
<li><a href = "index.php?module=list"><img src = "./Images/folder_user.png"><?php echo $GLOBALS["Program_Language"]["Files"]; ?></a></li>
<li><a href = "index.php?module=upload"><img src = "./Images/add.png"><?php echo $GLOBALS["Program_Language"]["Upload"]; ?></a></li>
<li><a href = "index.php?module=createdir"><img src = "./Images/folder_add.png"><?php echo $GLOBALS["Program_Language"]["New_Directory_Short"]; ?></a></li>
</ul>