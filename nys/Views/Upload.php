<?php	
	echo '<h2>'.$GLOBALS['Language']->Upload_Title.' '.$absolutePathCurrentDirectory.'.</h2>';
?>

<div id = 'result'>

</div>

<div class='panel panel-default'>
	<?php
		echo '<h3 class=\'text-center\'>';	
		echo $GLOBALS['Language']->dictUploadTitle.'</h3>';
	?>
<form class ='dropzone panel-body' id = 'my-awesome-dropzone' action='?upload' method='POST' >
 <div class = 'dz-message'>
	<center>
		<span class='elusive icon-file-new glyphIcon text-center'></span>
	</center>
 </div>
	<div class='fallback'>
    <input name='file' type='file'/>
  </div>
</form>

</div>
<script>
Dropzone.options.myAwesomeDropzone = {
  paramName: 'file', 
  uploadMultiple: true,
  addRemoveLinks: true,
  parallelUploads: 1,
  accept: function(file, done) {
   done();
  },
 // error: function(e){
  //  console.log(e);
  //},
  dictRemoveFile: '<?php echo $GLOBALS['Language']->dictRemoveFile;?>',
  dictCancelUpload: '<?php  echo $GLOBALS['Language']->dictCancelUpload;?>',
  dictDefaultMessage: '<?php  echo $GLOBALS['Language']->dictDefaultMessage;?>',  
  dictCancelUploadConfirmation: '<?php  echo $GLOBALS['Language']->dictCancelUploadConfirmation;?>',  
};
</script>
<!-- Fallback
<form enctype='multipart/form-data' action='?upload' method='POST'>
<p> 
  <input class = 'btn btn-default'  name='file[]' type='file' multiple/>
</p>
    <input class = 'btn btn-default'  type='submit' value='x'> 
</form>-->