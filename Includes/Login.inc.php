<div class="col-md-4 hidden-xs"></div>
	<div class="col-md-4 col-xs-12">
		<?php
			//Message display stack
			getMessage();
		?>
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
			 * This file offeres the login functionality
			 */
			 //Include uri check
			require_once ("checkuri.inc.php");
		//Only proceed if a post param named user is iset
		if (isset($_POST["user"])){
			//start a session if needed.
			if (isset($_SESSION) == false)
				session_start();		
			//Login and/or redirect the user
			$redir = "";
			if ($GLOBALS["config"]["Enable"] != 1 ) 
			{
				$redir = "?module=admin";
			}
			if (login($_POST["user"],$_POST["pass"]) == true){				
				$_SESSION["language"] = htmlspecialchars($_POST["lang"], ENT_QUOTES, 'UTF-8');
				if ($_SESSION["Session_Closed"] == 1 )
					header('Location: ./index.php'.$redir);
				else if ($GLOBALS["config"]["User_NoLogout_Warning"] == 1 && $_SESSION["Session_Closed"] == 0)
					header("Location: ./index.php?message=session");	
				else 
					header("Location: ./index.php");	
			}else
				header('Location: ./index.php?message=wrongcredentials');
		} 
		else
		{
			include "./Includes/branding.inc.php";
		}
		?>
		<div class="panel panel-default">
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="POST" action="index.php?module=login">
					<div class="form-group">
						<label for="inputEmail" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Username"];?></label>
						<div class="col-lg-9">
							<input type="text" class="form-control" id="user" name="user" placeholder="<?php echo $GLOBALS["Program_Language"]["Username"];?>">
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Password"];?></label>
						<div class="col-lg-9">
							<input type="password" class="form-control" id="pass" name = "pass" placeholder="<?php echo $GLOBALS["Program_Language"]["Password"];?>">
						</div>
					</div>		
					 <div class="form-group">
						<label for="inputLanguage" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Lang"];?></label>
						<div class="col-lg-9">
							<select class="form-control" id="lang" name = "lang">
							   <?php
									getLanguages("./Language/");
							   ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-3 col-lg-9">
							<button type="submit" class="btn btn-default btn-block">
								<?php echo $GLOBALS["Program_Language"]["Log_In"];?>
							</button>
						</div>
					</div>
					<?php if ($GLOBALS["config"]["Enable_register"] || $GLOBALS["config"]["User_Enable_Recover"]) :?>							
					<?php if ($GLOBALS["config"]["Enable_register"]) :?>
						<div class="form-group">
							<div class="col-lg-offset-3 col-lg-9">		
								<a class="btn btn-default btn-block" href = "index.php?module=register">
									<?php echo $GLOBALS["Program_Language"]["Register"]; ?>
								</a>
							</div>
						</div>	
					<?php endif; ?>
					<?php if ($GLOBALS["config"]["User_Enable_Recover"]) :?>
						<div class="form-group">
							<div class="col-lg-offset-3 col-lg-9">	
								<a class="btn btn-default btn-block" href = "index.php?module=recover">
									<?php echo $GLOBALS["Program_Language"]["Recover"]; ?>
								</a>
							</div>
						</div>	
					<?php endif; ?>								
				<?php endif; ?>
				</form>
				
			</div>
		</div>
	</div>
<div class="col-md-4 hidden-xs"></div> 