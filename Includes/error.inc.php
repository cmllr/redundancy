<div id ="error">
<img src = "./Images/error.png">
<?php
	if (isset($_GET["reason"]))
		echo "<br>".$_GET["reason"];
?>
</div>
