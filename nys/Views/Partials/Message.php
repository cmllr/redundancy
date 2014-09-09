<div class="alert alert-info dismissable">
<?php
	if (!is_array($MESSAGE))
		echo $MESSAGE;
	else
	{
		echo "<ul>";
		foreach($MESSAGE as $key=>$value){
			echo "<li>$value</li>";
		}
		echo "</ul>";
	}
?>
</div>