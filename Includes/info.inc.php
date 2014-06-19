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
	require_once ("checkuri.inc.php");
?> 
<img src="<?php echo $GLOBALS["config"]["Program_Branding"];?>" style="margin: 0 auto;" class="img-responsive">
<h1 class="text-center">
	Redundancy<sup>2</sup>
</h1>
<div class="panel panel-default">
	<div class="panel-body">
		<form class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-lg-2 control-label">Version</label>
					<div class="col-lg-8">
					<p class="form-control-static">
						<?php echo $GLOBALS["Program_Version"];?>
					</p>
					<div class="col-lg-2"></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-2 control-label">Status</label>
					<div class="col-lg-8">
						<p class="form-control-static">
							<?php 
							if (strpos($GLOBALS["Program_Version"],"nightly" !== false) || strpos($GLOBALS["Program_Version"],"beta") !== false)
								echo "<span class=\"label label-warning\">".$GLOBALS["Program_Language"]["Unstable"]."</span>";
							else
								echo "<span class=\"label label-success\">".$GLOBALS["Program_Language"]["Stable"]."</span>";
							?>
						</p>
						<div class="col-lg-2"></div>
					</div>
			</div> 
			<div class="form-group">
				<label class="col-lg-2 control-label">
					<?php echo $GLOBALS["Program_Language"]["Source"];?></label>
					<div class="col-lg-8">
						<p class="form-control-static">
							<?php echo $GLOBALS["Program_Release"];?>
						</p>
					</div>
				<div class="col-lg-2"></div>
			</div> 
			<div class="form-group">
				<label class="col-lg-2 control-label">Codename</label>
				<div class="col-lg-8">
					<p class="form-control-static">
						<i>
							<?php echo $GLOBALS["Program_Codename"];?>
						</i>
					</p>
				</div>
				<div class="col-lg-2"></div>
			</div> 
			<div class="form-group">
				<label class="col-lg-2 control-label">Bugtracker</label>
				<div class="col-lg-8">
					<p class="form-control-static">
						<a href = "https://github.com/squarerootfury/redundancy/issues">
						<?php echo $GLOBALS["Program_Language"]["Bugreport"]?>
						</a>
					</p>
				</div>
				<div class="col-lg-2"></div>
			</div> 
			<div class="form-group">
				<label class="col-lg-2 control-label"><?php echo $GLOBALS["Program_Language"]["ChangeLog"]; ?></label>
				<div class="col-lg-8">
					<p class="form-control-static">
						<a href = "./Change.log">
						<?php echo $GLOBALS["Program_Language"]["ChangeLog"]?>
						</a>
					</p>
				</div>
				<div class="col-lg-2"></div>
			</div> 
			<?php if (isAdmin()) :?>
			<div class="form-group">
				<label class="col-lg-2 control-label">Update</label>
				<div class="col-lg-8">
					<p class="form-control-static">
						<a href = "?module=update">
						<?php echo $GLOBALS["Program_Language"]["UpdateStart"]?>
						</a>
					</p>
				</div>
				<div class="col-lg-2"></div>
			</div> 
			<?php endif; ?>
		</form>
	</div>
</div>