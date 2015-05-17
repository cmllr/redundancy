<?php	
	echo '<h1 class="light"><span class="gray">'.$GLOBALS['Language']->Upload_Title.'</span> '.$absolutePathCurrentDirectory.'<span class="gray">.</span></h1>';
?>
<div class="alert alert-danger">This view is only fallback. It will be removed in the next updates. Please drop files in the file view instead.</div>
<div id = 'result'></div>
<form action="?upload" method="post">
<input type="file" name="file" multiple>
<input type="submit">

</form>