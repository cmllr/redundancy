<ul>
<li><a href = "index.php">
<img src = "./Images/house.png">
<?php
if (isset($_SESSION) == false)
		session_start();
	echo $GLOBALS["Program_Language"]["Home"];
?>
</a></li>
<li><a href = "index.php?module=list"><img src = "./Images/folder_user.png"><?php echo $GLOBALS["Program_Language"]["Files"]; ?></a></li>
<li><a href = "index.php?module=upload"><img src = "./Images/add.png"><?php echo $GLOBALS["Program_Language"]["Upload"]; ?></a></li>
<li><a href = "index.php?module=createdir"><img src = "./Images/folder_add.png"><?php echo $GLOBALS["Program_Language"]["New_Directory_Short"]; ?></a></li>
</ul>