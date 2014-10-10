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
	 * This file displays informations about the program
	 */
	 //Include uri check
?> 
<img class="img-responsive" src="./nys/Views/img/logoWithText.png">

<table class="table table-responsive">
	<tr>
		<td>Version</td>
		<td><?php echo  $version = $router->DoRequest("Kernel","GetVersion",json_encode(array())); ?></td>
	</tr>
	<tr>
		<td>Status</td>
		<td>
			<?php 
				if (strpos($version,"eol") !== false)
					echo "<span class=\"label label-danger\">".$GLOBALS["Language"]->EOL."</span>";
				else if (strpos($version,"dev") !== false || strpos($version,"beta") !== false)
					echo "<span class=\"label label-warning\">".$GLOBALS["Language"]->Unstable."</span>";
				else
					echo "<span class=\"label label-success\">".$GLOBALS["Language"]->Stable."</span>";
			?>
		</td>
	</tr>
	<tr>
		<td>Codename</td>
		<td>Lenticularis</td>
	</tr>
</table>
