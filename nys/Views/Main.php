<!-- Mobile view navbar-->
<nav class='navbar navbar-default visible-xs' role='navigation'>
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class='navbar-header'>
        <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-ex1-collapse'>
            <span class='sr-only'>Toggle navigation</span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
        </button>
        <a class='navbar-brand' href='index.php'>Redundancy</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class='collapse navbar-collapse navbar-ex1-collapse hidden-lg'>
        <ul class='nav navbar-nav '>
            <li>
                <a href='?account'>
                    <span class='fa fa-user'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->My_Account;?></a>
            </li>
            <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],12)))) :?>
            <li>
                <a href='?settings'>
                    <span class='fa fa-user'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->Account_Settings;?></a>
            </li>
            <?php endif;?>
            <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],9)))) :?>
            <li>
                <a href='?admin'>
                    <span class='fa fa-wrench'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->Administration;?></a>
            </li>
            <li>
                <a href='?update'>
                    <span class='fa fa-download'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->Update;?></a>
            </li>
            <?php endif;?>  
            <li>
                <a href='?files'>
                    <span class='fa fa-cloud'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->Files;?>
                </a>
            </li>
            <li>
                <a href='?history'>
                    <span class='fa fa-clock-o'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->changes;?>
                </a>
            </li>
            <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],0))) && isset($_GET["files"]))  :?>
            <li>
                <a href='?upload'>
                    <span class='fa fa-cloud-upload'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->Upload;?>
                </a>
            </li>
            <?php endif;?>
            <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],1)))) :?>
            <li>
                <a href='?newfolder'>
                    <span class='fa fa-folder-open'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->New_Directory_Short;?>
                </a>
            </li>
            <?php endif;?>
            <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],10)))) :?>
            <li>
                <a href='?shares'>
                    <span class='fa fa-share'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->ShareMenu;?>
                </a>
            </li>
            <?php endif;?>
            <li class='divider'></li>
            <li>
                <form method="POST" action="?search">
                    <input class="form-control search" type="text" value="<?php echo (isset($_POST[" Search "])) ? $_POST["Search "] : "";?>" name="Search" placeholder="<?php echo $GLOBALS['Language']->Search;?>">
                </form>
            </li>  
            <li class='divider'></li>      
            <li>
                <a href='?info'><span class='fa fa-info'>&nbsp;</span>Info</a>
            </li>          
            <li>
                <a href='?logout'>
                    <span class='fa fa-power-off'>&nbsp;</span>
                    <?php echo $GLOBALS['Language']->LogOut;?></a>
            </li>
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</nav>
<div class='hidden-xs  col-md-2 col-lg-2'>
    <div class='affix-top' id='leftSidebar' data-spy='affix' data-offset-top='0'>
        <div class='dropdown'>
            <button type='button' class='btn btn-primary btn-block dropdown-toggle' data-toggle='dropdown'>
                <span class='userbadge glyphicon glyphicon-userx'></span>
                <?php echo $data[ 'user']->DisplayName; ?>
                <span class='caret'></span>
            </button>
            <ul class='dropdown-menu' role='menu'>
                <li>
                    <a href='?account'>
                        <?php echo $GLOBALS['Language']->My_Account;?></a>
                </li>
                <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],12)))) :?>
                <li>
                    <a href='?settings'>
                        <?php echo $GLOBALS['Language']->Account_Settings;?></a>
                </li>
                <?php endif;?>
                <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],9)))) :?>
                <li>
                    <a href='?admin'>
                        <?php echo $GLOBALS['Language']->Administration;?></a>
                </li>
                <li>
                    <a href='?update'>
                        <?php echo $GLOBALS['Language']->Update;?></a>
                </li>
                <?php endif;?>
                <li>
                    <a href='?info'>Info</a>
                </li>
                <li class='divider'></li>
                <li>
                    <a href='?logout'>
                        <?php echo $GLOBALS['Language']->LogOut;?></a>
                </li>
            </ul>
        </div>

        <div class="white-flat">
            <ul class='nav nav-pills nav-stacked'>
                <!--<li>
                    <a href='index.php'>
                        <span class='fa fa-home'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->Home;?>
                    </a>
                </li>-->
                <li>
                    <a href='?files'>
                        <span class='fa fa-cloud'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->Files;?>
                    </a>
                </li>
                <li>
                    <a href='?history'>
                        <span class='fa fa-clock-o'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->changes;?>
                    </a>
                </li>
                <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],0)))) :?>
                <li>
                  
                        <?php if (isset($_GET["files"])) :?>
                        <div id="uploadHrefProgress"></div>            
                        <a id="uploadHref" href='#'>         
                            <span class='fa fa-cloud-upload'>&nbsp;</span>
                            <?php echo $GLOBALS['Language']->Upload;?>   <span id="uploadPercentage"></span>                              
                        </a>
                    <script>
                    $("#uploadHref").click(function(){
                        DisplayUploadDialog();
                    });
                    $(window).bind('beforeunload', function(){
                    if ($("#uploadPercentage").text() != "")
                        return "<?php echo $GLOBALS['Language']->Abort_Upload_Message;?>";
                    if ($("#statusExtract").attr("style") == undefined)
                        return "<?php echo $GLOBALS['Language']->Abort_Extract_Message;?>";
                    });
                    </script>
                    <?php endif;?>
                </li>
                <?php endif;?>
                <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],1)))) :?>
                <li>
                    <?php if (isset($_GET["files"])) :?>
                     <a id ="newDirHref" href='#'>
                        <span class='fa fa-folder-open'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->New_Directory_Short;?>
                    </a>
                    <script>
                    $("#newDirHref").click(function(){
                        NewDirDialog();
                    });
                    </script>
                    <?php endif;?>                   
                </li>
                <?php endif;?>
                <?php if($GLOBALS[ 'Router']->DoRequest('Kernel.UserKernel','IsActionAllowed',json_encode(array($_SESSION['Token'],10)))) :?>
                <li>
                    <a href='?shares'>
                        <span class='fa fa-share'>&nbsp;</span>
                        <?php echo $GLOBALS['Language']->ShareMenu;?>
                    </a>
                </li>
                <?php endif;?>
                <li>
                    <form method="POST" action="?search">
                        <input class="form-control search" type="text" value="<?php echo (isset($_POST[" Search "])) ? $_POST["Search "] : "";?>" name="Search" placeholder="<?php echo $GLOBALS['Language']->Search;?>">
                    </form>
                </li>
                 <li id="statusMove" style="display: none;">
                     <a href="#"><i class='fa fa-spinner fa-spin'></i> <?php echo $GLOBALS["Language"]->Move;?>...</a>
                 </li>
                 <li id = "statusExtract" style="display:none;">
                    <a href="#"><i class='fa fa-spinner fa-spin'></i> <?php echo $GLOBALS["Language"]->Unzip;?>...</a>
                 </li>
            </ul>
        </div>
    </div>
</div>
<div class='col-xs-12 col-sm-12 col-md-10 col-lg-10'>
    <?php 
        if (isset($_GET["rd"])){
            $ERROR="R_ERR_15";
            if (isset($ERROR)) include 'Partials/ErrorMessage.php'; 
        }else{
            //Display the error message if needed.
            if (isset($ERROR)) include 'Partials/ErrorMessage.php'; 
            //Display other messages
            if (isset($MESSAGE)) include 'Partials/Message.php';
        }
    ?>
    <div class='panel panel-default white-flat'>

        <div class='panel-body main'>
            <?php if (isset($innerContent)) include $innerContent; ?>
        </div>
    </div>
</div>
<div id="uploadbox" style="visibility:hidden;height:0px">
<a href="#" id="clearupload"><?php  echo $GLOBALS['Language']->Clear;?></a>
<div class="uploadDropper">
    <div class="filelists">
        <h5><?php echo $GLOBALS["Language"]->Finished;?></h5>
        <ol class="filelist complete">
        </ol>
        <h5><?php echo $GLOBALS["Language"]->Queued;?></h5>
        <ol class="filelist queue">
        </ol>
    </div>

</div>

<script type="text/javascript">
   // jquery file upload
   $(document).ready(function(){
    $filequeue = $(".filelist.queue");
        $filelist = $(".filelist.complete");
        $(".uploadDropper").upload({
            maxSize: <?php echo $data['maxUploadSize'];?>,
            action:"index.php?upload",
            postKey:"file",
            label: " <?php echo $GLOBALS['Language']->dictUploadTitle ;?>",
        }).on("start.upload", onStart)
          .on("complete.upload", onComplete)
          .on("filestart.upload", onFileStart)
          .on("fileprogress.upload", onFileProgress)
          .on("filecomplete.upload", onFileComplete)
          .on("fileerror.upload", onFileError);
   });
    function onStart(e, files) {
        console.log("Start");
        var html = '';
        for (var i = 0; i < files.length; i++) {
            html += '<li data-index="' + files[i].index + '"><span class="file">' + files[i].name + '</span><span class="progress">Queued</span></li>';
        }
        $filequeue.append(html);
    }

    function onComplete(e) {
        console.log("Complete");
        // All done!
    }

    function onFileStart(e, file) {
        console.log("File Start");
        $filequeue.find("li[data-index=" + file.index + "]")
                  .find(".progress").text("0%");
    }

    function onFileProgress(e, file, percent) {
        $("#uploadPercentage").text(Math.round(percent)+"%");
       $("#uploadHrefProgress").attr("style","height:100%;background: rgba(50, 102, 146, 0.15);width:"+percent+"%;position:absolute;");
        $filequeue.find("li[data-index=" + file.index + "]")
                  .find(".progress").text(percent + "%");
    }

    function onFileComplete(e, file, response) {
        console.log("File Complete");
        if (response.trim() === "" || response.toLowerCase().indexOf("error") > -1) {
            $filequeue.find("li[data-index=" + file.index + "]").addClass("error")
                      .find(".progress").text(response.trim());
        } else {
            var $target = $filequeue.find("li[data-index=" + file.index + "]");
            $target.find(".file").text(file.name);
            $target.find(".progress").remove();
            $target.appendTo($filelist);
        }
        $("#uploadPercentage").text("");       
        $("#uploadHrefProgress").removeAttr("style");       
        nys.Init();
    }

    function onFileError(e, file, error) {
        if (file.transfer){
            //file was too large
             var errorRawText = file.transfer.responseText;
            var errorText = /R_ERR_\d+/.exec(errorRawText)[0];
            var arguments = [];
            arguments.push(errorText);
           $.post('./Includes/api.inc.php', {
                module: 'Kernel.InterfaceKernel',
                method: 'GetLanguageValue',
                args: arguments,
            })
            .done(function(data) {  
                data = $.parseJSON(data);  
                var errorDisplayText = data.replace(/["']/g, "");
                $filequeue.find("li[data-index=" + file.index + "]").addClass("error")
                .find(".progress").text(errorDisplayText);
            })
            .fail(function(e) {
                $filequeue.find("li[data-index=" + file.index + "]").addClass("error")
                .find(".progress").text("Error: " + error);
            }); 
        }
        else{
            if (error == "Too large"){
                error="<?php echo $GLOBALS["Language"]->R_ERR_49;?>";
            }
             $filequeue.find("li[data-index=" + file.index + "]").addClass("error")
                .find(".progress").text(error);
        }
         $("#uploadPercentage").text("");       
        $("#uploadHrefProgress").removeAttr("style");             
        
    }
    $("#clearupload").click(function(){
        $(".complete").empty()
        $(".queue").empty()
    });
</script>
</div>