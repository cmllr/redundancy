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

<form class="form-horizontal" role="form">
	<div class="form-group">
	<img class="infologo hidden-xs" src="./nys/Views/img/logo.png">
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label">Version</label>
			<div class="col-lg-8">
			<p class="form-control-static">
				<?php
					echo  $GLOBALS["Router"]->DoRequest("Kernel","GetAppName",json_encode(array()));
					echo " ";
					$version = $router->DoRequest("Kernel","GetVersion",json_encode(array()));
					echo $version;
				?>
			</p>
			<div class="col-lg-2"></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label">Status</label>
			<div class="col-lg-8">
				<p class="form-control-static">
					<?php 
					if (strpos($version,"eol") !== false)
						echo "<span class=\"label label-danger\">".$GLOBALS["Language"]->EOL."</span>";
					else if (strpos($version,"dev") !== false || strpos($version,"beta") !== false)
						echo "<span class=\"label label-warning\">".$GLOBALS["Language"]->Unstable."</span>";
					else
						echo "<span class=\"label label-success\">".$GLOBALS["Language"]->Stable."</span>";
					
					?>
				</p>
				<div class="col-lg-2"></div>
			</div>
	</div> 			
	<div class="form-group">
		<label class="col-lg-2 control-label">Codename</label>
		<div class="col-lg-8">
			<p class="form-control-static">
				<i>
					Lenticularis
				</i>
			</p>
		</div>
		<div class="col-lg-2"></div>
	</div> 			
</form>

