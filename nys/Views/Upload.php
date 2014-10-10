<?php	
	echo '<h1 class="light"><span class="gray">'.$GLOBALS['Language']->Upload_Title.'</span> '.$absolutePathCurrentDirectory.'<span class="gray">.</span></h1>';
?>

<div id = 'result'></div>

<form class ='dropzone' id = 'my-awesome-dropzone' action='?upload' method='POST' >
<div class = 'dz-message'>
    <h3 class="text-center"><span class='fa fa-file'>&nbsp;</span>
       <?php echo $GLOBALS['Language']->dictUploadTitle ;?>
    </h3>
</div>
	<div class='fallback'>
    <input name='file' type='file'/>
  </div>
</form>

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