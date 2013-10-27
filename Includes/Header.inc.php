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
	 * This file contains the menu of Redundancy.
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
	if (isset($_SESSION) == false)
		session_start();
?>
 <nav class="navbar navbar-default hidden-sm hidden-md hidden-lg" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="index.php">
			<?php echo $GLOBALS["config"]["Program_Name_ALT"];?>
		</a>
	</div>
	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
			<li <?php if (isset($_GET["module"]) == false ): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php">Start</a>
			</li>
			<li <?php if (isset($_GET["module"]) == true && $_GET["module"] == "list"): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php?module=list">
					<?php echo $GLOBALS["Program_Language"]["Files"];?>
				</a>
			</li>
			<li <?php if (isset($_GET["module"]) == true && $_GET["module"] == "changes"): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php?module=changes">
					<?php echo $GLOBALS["Program_Language"]["changes"];?>
				</a>
			</li>
			<li <?php if (isset($_GET["module"]) == true && $_GET["module"] == "search"): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php?module=search">
					<?php echo $GLOBALS["Program_Language"]["Search"];?>
				</a>
			</li>
			<li <?php if (isset($_GET["module"]) == true && $_GET["module"] == "upload"): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php?module=upload">
					<?php echo $GLOBALS["Program_Language"]["Upload"];?>
				</a>
			</li>
			<li <?php if (isset($_GET["module"]) == true && $_GET["module"] == "createdir"): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php?module=createdir">
					<?php echo $GLOBALS["Program_Language"]["New_Directory"];?>
				</a>
			</li>
			<li <?php if (isset($_GET["module"]) == true && $_GET["module"] == "account"): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php?module=account">
					<?php echo $GLOBALS["Program_Language"]["My_Account"];?>
				</a>
			</li>
			<?php if ($_SESSION["role"] == 0 && isAdmin()): ?>
				<li <?php if (isset($_GET["module"]) == true && $_GET["module"] == "admin"): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php?module=admin">
					<?php echo $GLOBALS["Program_Language"]["Administration"];?>
				</a>
				</li>
			<?php endif;?>		
			<li <?php if (isset($_GET["module"]) == true && $_GET["module"] == "info"): ?> <?php echo "class=\"active\"";?> <?php endif;?>>
				<a href="index.php?module=info">
					Info
				</a>
			</li>
			<li>
				<a href="index.php?module=logout">
					<?php echo $GLOBALS["Program_Language"]["Exit"];?>
				</a>
			</li>			
		</ul>
		<form method="POST" action="index.php?module=list" class="navbar-form navbar-left" role="search">
			<div class="form-group">
				<input id = "searchquery" name = "searchquery" type="text" class="form-control" placeholder="<?php echo $GLOBALS["Program_Language"]["Search"];?>">
			</div>
			<button type="submit" class="btn btn-default">
				<?php echo $GLOBALS["Program_Language"]["Search"];?>
			</button>
		</form>
	</div>
	<!-- /.navbar-collapse -->
</nav>	 
<div class="col-lg-2 col-md-2 col-sm-3 hidden-xs">
	<div class="sidebar-nav-fixed" id="leftSidebar" data-spy="affix" data-offset-top="140">
		<?php if (isset($_SESSION["user_logged_in"])): ?>
		<div class="hidden-md hidden-lg">									
			<div class="dropdown">
				<button id = "userMenu" type="button" class="btn btn-block btn-default dropdown-toggle" data-toggle="dropdown">
					<span class = "elusive icon-user glyphIcon">
					</span>
					<?php echo $_SESSION["user_name"];?>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="?module=account">
							<?php echo $GLOBALS["Program_Language"]["My_Account"];?>
						</a>
					</li>
					<?php if ($_SESSION["role"] == 0 && isAdmin()): ?>
						<li>
						<a href="?module=admin">
							<?php echo $GLOBALS["Program_Language"]["Administration"];?>
						</a>
					</li>
					<?php endif;?>		
					<li>
						<a href="index.php?module=info">Info</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="?module=logout">
							<?php echo $GLOBALS["Program_Language"]["Exit"];?>
						</a>
					</li>
				</ul>
			</div>			
		</div>
		<?php endif;?>
		<ul class="nav nav-pills nav-stacked">
			<li>
				<a href="index.php">
				<span class="elusive icon-home glyphIcon"></span>
					Start
				</a>
			</li>
			<li >
				<a href="index.php?module=list">
				<span class="elusive icon-file glyphIcon"></span>
					<?php echo $GLOBALS["Program_Language"]["Files"];?>
				</a>
			</li>
			<li>
				<a href="index.php?module=changes">
				<span class="elusive icon-time glyphIcon"></span>
					<?php echo $GLOBALS["Program_Language"]["changes"];?>
				</a>
			</li>
			<li>
				<a href="index.php?module=search">
				<span class="elusive icon-search glyphIcon"></span>
					<?php echo $GLOBALS["Program_Language"]["Search"];?>
				</a>
			</li>
			<li>
				<a href="index.php?module=upload">
				<span class="elusive icon-file-new glyphIcon"></span>
					<?php echo $GLOBALS["Program_Language"]["Upload"];?>
				</a>
			</li>
			<li>
				<a href="index.php?module=createdir">
				<span class="elusive icon-folder glyphIcon"></span>
					<?php echo $GLOBALS["Program_Language"]["New_Directory_Short"];?>
				</a>
			</li>
			<?php if ($GLOBALS["config"]["Program_Display_Version"] == 1): ?>
			<li class="disabled">
				<a><?php echo $GLOBALS["Program_Version"];?></a>
			</li>			
			<?php endif ;?>		
			<?php if ($GLOBALS["config"]["Program_Display_Loadtime"] == 1): ?>
			<li>
				<span class="label label-info">
				<?php 
					$end = microtime(true);
					 printf($GLOBALS["Program_Language"]["Loadtime"],round($end-$start,4));				
				?>
				</span>
			</li>	
			<?php endif ;?>
			<?php if ($GLOBALS["config"]["Program_Debug"] == 1): ?>
			<li>
				<span class="label label-warning">
				Debug is on
				</span>
			</li>
			<li>
				<span class="label label-info">
				<?php echo measurementCorrection(memory_get_usage());?> Memory
				</span>
			</li>			
			
			<?php endif ;?>			
		</ul>
		
	</div>
</div>

 <div class="col-lg-8 col-md-8 col-sm-9 col-xs-12">
<?php
	//Message display stack
	getMessage();
?>