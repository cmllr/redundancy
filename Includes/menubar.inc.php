<div id = "sidebar" style = "visibility:hidden;">
<div id = "progressbar">
<?php
	echo "<div id = 'progressbar_inner' style='width:".round(getPercentage(),0)."% ;'>";
	echo "<p>&nbsp;".getPercentage()."&nbsp;".$GLOBALS["Program_Language"]["used"]."</p<>";
?>
</div>
</div>
<p>	
<?php
	echo getStoragePercentage();
?>
</p>
<a href = "index.php?module=account"><img src ="./Images/user_orange.png"><?php echo $GLOBALS["Program_Language"]["My_Account"]; ?></a>
<?php
	if (isset($_SESSION) == false)
		session_start();
	if ($_SESSION["role"] == 0)
		echo "<a href = 'index.php?module=admin'><img src ='./Images/group_gear.png'>".$GLOBALS["Program_Language"]["Administration"]."</a>";
?>
<a href = "index.php?module=logout"><img src ="./Images/user_go.png"><?php echo $GLOBALS["Program_Language"]["Exit"]; ?></a>
</div>