<li>
<?php
	if (isset($_SESSION) == false)
		session_start();
	$dirs = explode("/",$_SESSION["currentdir"]);
	$parts_before = "";
	echo "<ul id = 'broadcrumb'><a href= 'index.php?module=list&dir=/'>Home</a></ul>";
	for ($i = 0; $i < count($dirs); $i++)
	{
		if ($dirs[$i] != ""){
			echo "<ul id = 'broadcrumb'><a href= 'index.php?module=list&dir=".$parts_before.$dirs[$i]."'>".$dirs[$i]."</a></ul>";
			$parts_before = $parts_before.$dirs[$i]."/";
		}	
	}
?>
</li>
<br>
