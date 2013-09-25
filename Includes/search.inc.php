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

  <div class="panel-body">
	<form method="POST" action="index.php?module=list" class="form-horizontal" role="form">  
  <div class="form-group"> 
<?php
	echo $GLOBALS["Program_Language"]["Search_Description"];
?>
<br>

<?php echo $GLOBALS["Program_Language"]["Search_to"];?> <input id = "searchquery" name="searchquery">
<input class = 'btn btn-default' type="submit"  value="<?php echo $GLOBALS["Program_Language"]["Search"];?>">
</div>
</form>
</div>