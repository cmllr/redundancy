<div class="alert alert-danger dismissable">
<?php
	if (!is_array($ERROR))
		echo $ERROR;
	else
	{
		echo "<ul>";
		foreach($ERROR as $key=>$value){
			echo "<li>$value</li>";
		}
		echo "</ul>";
	}
?>
</div>