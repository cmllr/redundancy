<h1 class="light-header">
	<a href="?files"><span class='fa fa-chevron-left lightblue'></span></a>
	<?php
		echo $filenameParts[0] . '<span class="gray">' . $filenameParts[1] . '</span>';
	?>
</h1>

<?php if (is_numeric($mediaPreview)){
		  echo "R_ERR_$mediaPreview"; }
	  else {
		  echo '<div class="img-preview">' . $mediaPreview . '</div>';
	  }
?>
<div class="btn-group" id="fileActionBtnGroup">

    <a type="a" href="?download&f=<?php echo $entry->Hash; ?>" target="_blank" class="btn btn-link">
        <span class="elusive icon-screen glyphIcon">
        </span>
        <span>
            <?php echo $GLOBALS['Language']->Download;?></span>
    </a>

</div>

<table class="table table-hover table-subtile table-first-bold">
	<tr>
		<td><?php echo $GLOBALS['Language']->Size;?></td>
		<td><?php echo $entry->SizeWithUnit; ?></td>
	</tr>
	<tr>
		<td><?php echo $GLOBALS['Language']->Source;?></td>
		<td><?php echo (strpos($entry->UsedUserAgent,"Mozilla") !== false) ? $GLOBALS['Language']->Uploaded_Browser : $GLOBALS['Language']->Uploaded_API; ?></td>
	</tr>
	<tr>
		<td><?php echo $GLOBALS['Language']->FileUploaded;?></td>
		<td><?php echo $entry->CreateDateTime; ?></td>
	</tr>
	<tr>
		<td><?php echo $GLOBALS['Language']->LastChangedFile;?></td>
		<td> <?php echo $entry->LastChangeDateTime; ?></td>
	</tr>
</table>