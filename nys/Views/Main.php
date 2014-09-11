<!-- Mobile view navbar-->
<nav class='navbar navbar-default hidden-sm hidden-md hidden-lg' role='navigation'>
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class='navbar-header'>
		<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-ex1-collapse'>
			<span class='sr-only'>Toggle navigation</span>
			<span class='icon-bar'></span>
			<span class='icon-bar'></span>
			<span class='icon-bar'></span>
		</button>
		<a class='navbar-brand' href='index.php'>
			Redundancy		</a>
	</div>
	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class='collapse navbar-collapse navbar-ex1-collapse'>
		<ul class='nav navbar-nav'>
			<li class='active'>
				<a href='index.php'>Main</a>
			</li>
				
		</ul>		
	</div>
	<!-- /.navbar-collapse -->
</nav>
<div class='col-lg-2 col-md-2 col-sm-3 hidden-xs'>
	<div class='sidebar-nav-fixed affix-top' id='leftSidebar' data-spy='affix' data-offset-top='140'>				
		<ul class='nav nav-pills nav-stacked'>
			<li>
				<a href='index.php'>
				<span class='elusive icon-home glyphIcon'></span>
					<?php echo $GLOBALS['Language']->Home;?>
				</a>
			</li>	
			<li>
				<a href='?files'>
				<span class='elusive icon-file glyphIcon'></span>
					<?php echo $GLOBALS['Language']->Files;?>
				</a>
			</li>	
			<li>
				<a href='?history'>
				<span class='elusive icon-time glyphIcon'></span>
					<?php echo $GLOBALS['Language']->changes;?>
				</a>
			</li>	
			<li>
				<a href='?search'>
				<span class='elusive icon-search glyphIcon'></span>
					<?php echo $GLOBALS['Language']->Search;?>
				</a>
			</li>	
			<li>
				<a href='?upload'>
				<span class='elusive icon-file-new glyphIcon'></span>
					<?php echo $GLOBALS['Language']->Upload;?>
				</a>
			</li>	
			<li>
				<a href='?newfolder'>
				<span class='elusive icon-folder glyphIcon'></span>
					<?php echo $GLOBALS['Language']->New_Directory_Short;?>
				</a>
			</li>											
		</ul>		
	</div>
</div>
<div class='col-lg-8 col-md-8 col-sm-9 col-xs-12'>
	<?php
	//Display the error message if needed.
	if (isset($ERROR))
		include 'Partials/ErrorMessage.php';
	//Display other messages
	if (isset($MESSAGE))
		include 'Partials/Message.php';	
	?>	
	<div class='panel panel-default'> 

		<div class='panel-body'>
			<?php
					if (isset($innerContent))
						include $innerContent;
			?>		
		</div>
	</div>
</div>
<div class='col-lg-2 col-md-2 visible-md visible-lg'>
<div data-spy='affix' data-offset-top='140' class='affix-top'>
	<div class='dropdown'>
		<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>
			<span class='elusive icon-user glyphIcon'></span><?php echo $data['user']->LoginName; ?> <span class='caret'></span>
		</button>
		<ul class='dropdown-menu' role='menu'>
			<li>
				<a href='?account'<?php echo $GLOBALS['Language']->My_Account;?></a>
			</li>	
			<li>
				<a href='?storageinfo'>
					<?php echo $GLOBALS['Language']->Account_Storage_Info;?>				</a>
			</li>		
							<li>
				<a href='?admin'><?php echo $GLOBALS['Language']->Administration;?></a>
			</li>
					
			<li>
				<a href='?info'>Info</a>
			</li>
			<li class='divider'></li>
			<li>
				<a href='?logout'><?php echo $GLOBALS['Language']->LogOut;?></a>
			</li>
		</ul>
	</div>
</div>
</div>