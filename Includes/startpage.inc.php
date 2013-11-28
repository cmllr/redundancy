<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * This file contains the startpage of the program
	 */
?>
<div class="page-header">
  <h2>Redundancy 2 <small>- <?php echo $GLOBALS["Program_Version"];?></small></h2>
</div>			
<p class="form-control-static">
	<?php 
	if (strpos($GLOBALS["Program_Version"],"nightly" !== false) || strpos($GLOBALS["Program_Version"],"beta") !== false)
		echo "<div class=\"alert alert-warning\">".$GLOBALS["Program_Language"]["Unstable"]."</div>";
	else
		echo "<div class=\"alert alert-success\">".$GLOBALS["Program_Language"]["Stable"]."</div>";
	?>
</p>	
<?php 
if (strpos($GLOBALS["Program_Version"],"nightly" !== false) || strpos($GLOBALS["Program_Version"],"beta") !== false) 
	echo $GLOBALS["Program_Language"]["Unstable_Description"]; 
else
	echo $GLOBALS["Program_Language"]["Stable_Description"];
 ?>
<div class="page-header">
  <h2><?php echo $GLOBALS["Program_Language"]["Files"];?> <small> - <?php echo "&nbsp;".getUsedStoragePercentage()."&nbsp;(".getUsedStorageStatus().")";;?></small></h2>
</div>	
<div class="progress">
	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo getUsedStorage();?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getUsedStorage();?>%;">
	</div>
</div>
<div class="page-header">
  <h2><?php echo $GLOBALS["Program_Language"]["QuickButtons"];?><small> - <?php echo $GLOBALS["Program_Language"]["QuickButtons_Description"];?></small></h2>
</div>	
<div class="btn-group" >
<a type="a" href="./Change.log" class="btn btn-default">
<span class="elusive icon-plus glyphIcon">
</span>Changelog</a>
<a type="a" href="index.php?module=account" class="btn btn-default">
<span class="elusive icon-user glyphIcon">
</span><?php echo $GLOBALS["Program_Language"]["My_Account"];?></a>
<a type="a" href="index.php?module=<?php echo $GLOBALS["config"]["Program_Upload_Module"];?>" class="btn btn-default">
<span class="elusive icon-file-new glyphIcon">
</span><?php echo $GLOBALS["Program_Language"]["Upload"];?></a>
<a type="a" href="index.php?module=createdir" class="btn btn-default">
<span class="elusive icon-folder glyphIcon">
</span><?php echo $GLOBALS["Program_Language"]["New_Directory_Short"];?></a>
</div>