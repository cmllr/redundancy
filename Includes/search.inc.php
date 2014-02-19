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
	 * This file contains the search dialog
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
?>
<h2><?php echo $GLOBALS["Program_Language"]["Search"];?></h2>
<div class="panel-body">
<form class="form-horizontal" method="POST" action="index.php?module=list">	
	<div class="form-group">
		<div class="alert alert-info"><?php	echo $GLOBALS["Program_Language"]["Search_Description"];?>
		</div>	
		<label class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Search_to"];?></label>
		<div class="col-lg-9">
			<input type="text" class="form-control" id = "searchquery" name="searchquery">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<input class="btn-block btn btn-default" type="submit" name="submit" value="<?php echo $GLOBALS["Program_Language"]["Search"];?>">		
		</div>
	</div>
</form>
</div>