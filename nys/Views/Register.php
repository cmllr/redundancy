<div class='col-md-4'></div>
<div class='col-md-4'>
<?php
	//Display the error message if needed.
	if (isset($ERROR))
		include 'Partials/ErrorMessage.php';
?>
<div class="hidden-xs">
<img class='logo' src='./nys/Views/img/logo.png'>
</div>
<h2 class='appname'><?php  echo $GLOBALS['Language']->Register;?></h2>
<div class='panel panel-default'>
<form class ='panel-body' role='form' method='POST' action='?register'>
	 <div class='form-group '>
    <label for='name'><?php echo $GLOBALS['Language']->Name;?></label>
    <input type='text' class='form-control' name='name' placeholder='<?php echo $GLOBALS['Language']->Name;?>'>
  </div>
   <div class='form-group '>
    <label for='username'><?php echo $GLOBALS['Language']->Username;?></label>
    <input type='text' class='form-control' name='username' placeholder='<?php echo $GLOBALS['Language']->Username;?>'>
  </div>
  <div class='form-group '>
    <label for='email'><?php echo $GLOBALS['Language']->Email;?></label>
    <input type='email' class='form-control' name='email' placeholder='<?php echo $GLOBALS['Language']->Email;?>'>
  </div>
  <div class='form-group'>
    <label for='password'><?php echo $GLOBALS['Language']->Password; ?></label>
    <input type='password' class='form-control' name='password' placeholder='<?php echo $GLOBALS['Language']->Password;?>'>
  </div> 
   <div class='form-group'>
    <label for='password'><?php echo $GLOBALS['Language']->Repeat_Password; ?></label>
    <input type='password' class='form-control' name='passwordrepeat' placeholder='<?php echo $GLOBALS['Language']->Repeat_Password;?>'>
  </div> 
  <!--
  <div class='form-group'>
	  <label for='password'><?php echo $GLOBALS['Language']->Lang; ?></label>
		<select class='form-control' id='lang' name='lang'>
				<?php 
					$languages = $GLOBALS['Router']->DoRequest('Kernel.InterfaceKernel','GetInstalledLanguages',json_encode(array()));				
				?>
				<?php foreach($languages as $key=>$value): ?>			  
			        <option><?php echo $value; ?></option>				   
			    <?php endforeach; ?>	
		</select>
  </div>-->
  <button type='submit' class='btn btn-default'><?php echo $GLOBALS['Language']->Log_In;?></button>
</form>
</div>
</div>
<div class='col-md-4'></div>