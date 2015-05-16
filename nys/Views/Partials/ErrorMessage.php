<div class="alert alert-danger dismissable">
<?php

	if (!is_array($ERROR))
	{
		if (isset($GLOBALS["Language"]->{$ERROR})){
			echo $GLOBALS["Language"]->{$ERROR};
		}
		else{
			echo $ERROR;
		}
	}
	else
	{
		echo "<ul>";
		foreach($ERROR as $key=>$value){
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