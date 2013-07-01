<?php
if (isset($_SESSION) == false)
			session_start();	
	if ($_SESSION["role"] == 0 && is_admin())
	{
		create_fs_snapshot();
	}
?>