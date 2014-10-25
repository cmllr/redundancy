<div class="alert alert-info dismissable">
<?php
	if (!is_array($MESSAGE))
		if (isset($GLOBALS["Language"]->{$MESSAGE})){
			echo $GLOBALS["Language"]->{$MESSAGE};
		}
		else{
			echo $MESSAGE;
		}
	else
	{
		echo "<ul>";
		foreach($MESSAGE as $key=>$value){
			if (isset($GLOBALS["Language"]->{$value})){
				echo "<li>".$GLOBALS["Language"]->{$value}."</li>";
			}
			else{
				echo "<li>$value</li>";
			}
		}
		echo "</ul>";
	}
?>
</div>