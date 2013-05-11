<div id = "sidebar" style = "visibility:hidden;">
<div id = "progressbar">
<?php
	echo "<div id = 'progressbar_inner' style='width:".round(getPercentage(),0)."% ;'>";
	echo "<p>&nbsp;".getPercentage()."&nbsp;used</p<>";
?>
</div>
</div>
<p>	
<?php
	echo getStoragePercentage();
?>
</p>
<a href = "index.php?module=account"><img src ="./Images/user_orange.png">My account</a>
<a href = "index.php?module=logout"><img src ="./Images/user_go.png">Exit</a>
</div>