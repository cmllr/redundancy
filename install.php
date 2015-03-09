<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap -->
    <link rel='stylesheet' href='./Lib/Lenticularis/css/theme.min.css' type='text/css' />
    <script src="./Lib/jQuery/jquery-1.10.2.min.js"></script>
    <script src='./Lib/Bootstrap/js/bootstrap.min.js'></script>
    <title>Redundancy</title>
</head>
<?php
     error_reporting(E_ALL);
    $path = str_replace("install.php","", __file__);   
    /**
    * The systems root dir, ending with "/"
    */      
    define("__REDUNDANCY_ROOT__",$path);
    //Some stuff...  
    require_once __REDUNDANCY_ROOT__."Includes/Kernel/Kernel.Installer.class.php";
   // var_dump($k);
    $k = new \Redundancy\Kernel\Installer();
    if ($k->IsLocked())
    {
        header("Location: index.php");
        exit;
    }
    $step = 1;
    if (isset($_GET["lng"])){
        $k->ParseLanguage($_GET["lng"]);
        $step = 2;
        $lng = $_GET["lng"];
        $dirs = $k->GetDirectoryPermissions();
        $extensions = $k->GetExtensionStatus();
        $settings = $k->GetSettings();
    }
    if (isset($_GET["step"]) && $_GET["step"] == "license"){
        $step = 3;
    }
    if (isset($_GET["step"]) && $_GET["step"] == "db"){
        $step = 4;
    }

    if (isset($_POST["hostname"])){
        //$user,$pass,$host,$dbname,$driver
        $dbConnResult =  $k->TestDBConnection($_POST["username"],$_POST["password"],$_POST["hostname"],$_POST["db"],$_POST["driver"],$_POST["path"]);
        $write = $k->WriteDBConfig($_POST["username"],$_POST["password"],$_POST["hostname"],$_POST["db"],$_POST["driver"],$_POST["path"]);
        $dbimport = $k->DoTheImport();
        if ($dbConnResult && $write )
            $step = 5;
    }
    else if (isset($_POST["username"]) && $_POST["password"] == $_POST["password_repeat"]){
        if ($k->SetUser($_POST["username"],$_POST["password"],$_POST["email"]))
            $k->Lock();
        $step = 6;
    }
?>
<body>
    <div class='container'>
        <div class='row'>
            <div class='col-sm-offset-2 col-sm-8 col-lg-offset-4 col-lg-4'>
                <div>
                    <div class="hidden-xs">
                        <img class='img-responsive logo' src='./nys/Views/img/logoWithText.png'>
                    </div>
                    <!--<h2 class='appname'>Redundancy</h2>-->
                    <div class='panel panel-default white-flat'>
                        <div class='panel-body'>
                            <?php if ($step == 1) :?>
                                <h1 class="light header-form gray">Sprache/ Language</h1>
                                <a class="btn btn-default" href ="./install.php?lng=de">Deutsch</a>
                                <a class="btn btn-default" href ="./install.php?lng=en">English</a>
                                <a class="btn btn-default" href ="./install.php?lng=fr">Français</a>
                            <?php endif ;?>
                            <?php if ($step == 2) :?>
                                <h1 class="light header-form gray"><?php echo $GLOBALS["Language"]["Installation"];?></h1>
                                <div class="well"><?php echo $GLOBALS["Language"]["InstallationWelcomeText"];?></div>
                                <div class="alert alert-danger"><?php echo $GLOBALS["Language"]["UnstableWarning"];?></div>
                                <div class="alert alert-info "><?php echo $GLOBALS["Language"]["Dirs"];?></div>
                                <ul class="list-group">
                                    <?php foreach($dirs as $key => $value) :?>
                                       <li class="list-group-item"><?php echo $key;?> - <?php
                                        if ($value == false){
                                            echo "<span class='label label-danger'>".$GLOBALS["Language"]["CannotWrite"]."</span>";
                                            $fail = true;
                                        }                                           
                                        else
                                            echo "<span class='label label-success'>".$GLOBALS["Language"]["CanWrite"]."</span>";
                                       ?></li>
                                    <?php endforeach;?>
                                </ul>
                                <div class="alert alert-info "><?php echo $GLOBALS["Language"]["Extensions"];?></div>
                                <ul class="list-group">
                                    <?php foreach($extensions as $key => $value) :?>
                                       <li class="list-group-item"><?php echo $key;?> - <?php
                                        if ($value == false){
                                            echo  "<span class='label label-danger'>".$GLOBALS["Language"]["Extension_Missing"]."</span>";
                                            $fail = true;
                                        }                                           
                                        else
                                            echo "<span class='label label-success'>".$GLOBALS["Language"]["Extension_Existing"]."</span>";
                                       ?></li>
                                    <?php endforeach;?>
                                </ul>
                                 <div class="alert alert-info "><?php echo $GLOBALS["Language"]["Configuration"];?></div>
                                <ul class="list-group">
                                    <?php foreach($settings as $key => $value) :?>
                                       <li class="list-group-item"><?php echo $key;?> - <?php
                                        if ($value == false){
                                            echo  "<span class='label label-danger'>".$GLOBALS["Language"]["Configuration_Fail"]."</span>";
                                        }                                           
                                        else
                                            echo "<span class='label label-success'>".$GLOBALS["Language"]["Configuration_OK"]."</span>";
                                       ?></li>
                                    <?php endforeach;?>
                                </ul>
                                <a class="btn btn-default" href ="./install.php"><?php echo $GLOBALS["Language"]["Back"];?></a>
                                <?php if (!isset($fail)) :?>
                                <a class="btn btn-primary" href ="./install.php?step=license&lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["NextLicense"];?></a>
                            <?php endif;?>
                            <?php endif ;?>
                            <?php if ($step == 3) :?>
                                <h1 class="light header-form gray"><?php echo $GLOBALS["Language"]["Installation"];?></h1>
                                <div class="well"><?php echo $GLOBALS["Language"]["InstallationLicenseText"];?></div>
                                <a class="btn btn-default" href ="./install.php?lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["Back"];?></a>
                                <a class="btn btn-primary" href ="./install.php?step=db&lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["NextDB"];?></a>
                            <?php endif ;?>
                            <?php if ($step == 4) :?>
                                <h1 class="light header-form gray"><?php echo $GLOBALS["Language"]["Installation"];?></h1>
                                <div class="well"><?php echo $GLOBALS["Language"]["InstallationDB"];?></div>
                                 <?php if (isset($dbConnResult) && $dbConnResult == false) :?>
                                    <div class="alert alert-warning"><?php echo $GLOBALS["Language"]["DBConFailed"];?></div>
                                <?php endif ;?>
                                 <?php if (isset($dbConnResult) && $dbConnResult == true) :?>
                                    <div class="alert alert-success"><?php echo $GLOBALS["Language"]["DBConTrue"];?></div>
                                <?php endif ;?>
                                <form class='form' role='form' method='POST' action='./install.php?step=db&lng=<?php echo  $lng ;?>' autocomplete ="off" >
                                     <div class='form-group '>
                                        <label for='driver'>
                                              <?php echo $GLOBALS['Language']["Driver"];?></label>
                                            <select id="driver" class = "form-control" name="driver" required>
                                                <option>MySQL</option>
                                                <option>SQLite</option>
                                            </select>
                                    </div>
                                    <div class='form-group' id="usernameFormGroup">
                                        <label for='username'>
                                            <?php echo $GLOBALS['Language']["Username"];?></label>
                                        <input type='text' class='form-control' name='username' placeholder='<?php echo $GLOBALS['Language']["Username"];?>' 
                                        value ="<?php echo isset($_POST["username"]) ? $_POST["username"] : "";?>" required>
                                    </div>
                                    <div class='form-group ' id="passwordFormGroup">
                                        <label for='password'>
                                            <?php echo $GLOBALS['Language']["Password"];?></label>
                                        <input type='password' class='form-control' name='password' placeholder='<?php echo $GLOBALS['Language']["Password"];?>' 
                                        value ="<?php echo isset($_POST["password"]) ? $_POST["password"] : "";?>"
                                        >
                                    </div>
                                    <div class='form-group ' id="dbFormGroup">
                                        <label for='db'>
                                            <?php echo $GLOBALS['Language']["Database"];?></label>
                                        <input type='text' class='form-control' name='db' placeholder='<?php echo $GLOBALS['Language']["Database"];?>' 
                                        value ="<?php echo isset($_POST["db"]) ? $_POST["db"] : "";?>"
                                        required>
                                    </div>
                                    <div class='form-group ' id="hostnameFormGroup">
                                        <label for='hostname'>
                                            Hostname</label>
                                        <input type='text' class='form-control' name='hostname' value='localhost' required>
                                    </div>
                                    <div class='form-group ' id="pathFormGroup" style="display: none;">
                                        <label for='path'>
                                            <?php echo $GLOBALS['Language']["DatabasePath"];?></label>
                                        <input type='text' class='form-control' name='path' value=''>
                                    </div>                                   
                                     <a class="btn btn-default" href ="./install.php?step=license&lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["Back"];?></a>
                                  <input type ="submit" class="btn btn-primary" value="<?php echo $GLOBALS["Language"]["NextUser"];?>"</input>
                                </form>
                               <script>
                                    var serverBased = ["MySQL"];
                                    var fileBased = ["SQLite"];
                                    $("#driver").change(function(){                                       
                                        var driver = $('#driver').find(":selected").text();
                                        if (serverBased.indexOf(driver) != -1){
                                            //server based
                                            $("#pathFormGroup").hide();
                                            $("#hostnameFormGroup").show();
                                            $("#dbFormGroup").show();
                                            $("[name='hostname']").attr("required");
                                            $("[name='username']").attr("required");
                                            $("[name='db']").attr("required");
                                            $("[name='password']").attr("required");
                                            $("[name='hostname']").attr("required");
                                            $("#passwordFormGroup").show();
                                            $("#usernameFormGroup").show();
                                        }
                                        else{
                                            //file based
                                            $("#pathFormGroup").show();
                                            $("#dbFormGroup").hide();
                                            $("#hostnameFormGroup").hide(); 
                                            $("#passwordFormGroup").hide();
                                            $("#usernameFormGroup").hide();
                                            $("[name='password']").removeAttr("required");
                                            $("[name='hostname']").removeAttr("required");
                                            $("[name='username']").removeAttr("required");
                                            $("[name='db']").removeAttr("required");
                                            $("[name='password']").removeAttr("required");
                                            $("[name='hostname']").removeAttr("required");   

                                           
                                        }
                                    });
                               </script>
                            <?php endif ;?>
                             <?php if ($step == 5) :?>
                                <h1 class="light header-form gray"><?php echo $GLOBALS["Language"]["Installation"];?></h1>
                                <div class="well"><?php echo $GLOBALS["Language"]["AlmostDone"];?></div>
                                <form class='form' role='form' method='POST' action='./install.php?step=db&lng=<?php echo  $lng ;?>' autocomplete ="off" >
                                    <div class='form-group '>
                                        <label for='username'>
                                            <?php echo $GLOBALS['Language']["Username"];?></label>
                                        <input type='text' class='form-control' name='username' placeholder='<?php echo $GLOBALS['Language']["Username"];?>' required>
                                    </div>
                                    <div class="form-group ">
                                        <label for="email"><?php echo $GLOBALS['Language']["Email"];?></label>
                                        <input type="email" class="form-control" name="email" placeholder="E-Mail">
                                    </div>
                                    <div class='form-group '>
                                        <label for='password'>
                                            <?php echo $GLOBALS['Language']["Password"];?></label>
                                        <input type='password' class='form-control' name='password' placeholder='<?php echo $GLOBALS['Language']["Password"];?>' required>
                                    </div>                                    
                                    <div class='form-group '>
                                        <label for='password_repeat'>
                                            <?php echo $GLOBALS['Language']["Repeat_Password"];?></label>
                                        <input type='password' class='form-control' name='password_repeat' placeholder='<?php echo $GLOBALS['Language']["Repeat_Password"];?>' >
                                    </div>
                                    <a class="btn btn-default" href ="./install.php?step=license&lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["Back"];?></a>
                                <input type ="submit" class="btn btn-primary" value="<?php echo $GLOBALS["Language"]["NextFinish"];?>"</input>
                                </form>
                            <?php endif ;?>
                            <?php if ($step == 6) :?>
                                <h1 class="light header-form gray"><?php echo $GLOBALS["Language"]["Installation"];?></h1>
                                <div class="well"><?php echo $GLOBALS["Language"]["Installed"];?></div>
                                <a class="btn btn-primary" href ="./index.php"><?php echo $GLOBALS["Language"]["GoToInstance"];?></a>
                            <?php endif ;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <img class='branding visible-xs' src='./nys/Views/img/logoWithTextSmall.png'>
</body>

</html>
