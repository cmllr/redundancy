<h1 class="hidden-xs light-header">
	<a href="?files"><span class='fa fa-chevron-left lightblue'></span></a>
	<?php
		echo $filenameParts[0] . '<span class="gray">' . $filenameParts[1] . '</span>';
	?>
</h1>
<h3 class="visible-xs"><?php echo $entry->DisplayName?></h3>

<?php if (is_numeric($mediaPreview)){
		  echo "R_ERR_$mediaPreview"; }
	  else
		  echo $mediaPreview;
?>
<div class="btn-group" id="fileActionBtnGroup">

    <a type="a" href="?download&f=<?php echo $entry->Hash; ?>" target="_blank" class="btn btn-default">
        <span class="elusive icon-screen glyphIcon">
        </span>
        <span class="hidden-xs">
            <?php echo $GLOBALS['Language']->Download;?></span>
    </a>

</div>
<div class="panel panel-default">
    <div class="panel-body">
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label class="col-lg-2 control-label">
                    <?php echo $GLOBALS['Language']->Size;?></label>
                <div class="col-lg-8">
                    <p class="form-control-static">
                        <?php echo $entry->SizeWithUnit; ?>
                    </p>
                    <div class="col-lg-2"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-2 control-label">
                    <?php echo $GLOBALS['Language']->Source;?></label>
                <div class="col-lg-8">
                    <p class="form-control-static">
                        <?php echo (strpos($entry->UsedUserAgent,"Mozilla") !== false) ? $GLOBALS['Language']->Uploaded_Browser : $GLOBALS['Language']->Uploaded_API; ?>
                    </p>
                    <div class="col-lg-2"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-2 control-label">
                    <?php echo $GLOBALS['Language']->FileUploaded;?></label>
                <div class="col-lg-8">
                    <p class="form-control-static">
                        <?php echo $entry->CreateDateTime; ?>
                    </p>
                    <div class="col-lg-2"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-2 control-label">
                    <?php echo $GLOBALS['Language']->LastChangedFile;?></label>
                <div class="col-lg-8">
                    <p class="form-control-static">
                        <?php echo $entry->LastChangeDateTime; ?>
                    </p>
                    <div class="col-lg-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>
