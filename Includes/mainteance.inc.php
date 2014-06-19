<?php
//Include uri check
require_once ("checkuri.inc.php");
?>
<html>
	<body> 
		<div class = 'container'>
			<div class = 'row'>
				<div class="col-md-4 hidden-xs"></div>
				<div class="col-md-4 col-xs-12">
					<img src="./Images/bootstrapped_logo.png" style="margin: 0 auto;" class="img-responsive">
					<h1 class="text-center">Redundancy</h1>		
					<div class="form-group ">						
						<div class="text-center">
							<span class="label label-important" style="background:#b94a48">
								<?php 
								if (isset($GLOBALS["Program_Language"]))
									echo $GLOBALS["Program_Language"]["mainteance"];
								else
									echo "Mainteance is on";
								?>
							</span>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</body>
</html>
