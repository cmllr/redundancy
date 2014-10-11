<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap -->
    <link rel='stylesheet' href='./Lib/Lenticularis/css/theme.css' type='text/css' />
    <script src='./Lib/Bootstrap/js/bootstrap.min.js'></script>
    <title>Redundancy</title>
</head>
<?php
    $path = str_replace("install.php","", __file__);
    /**
    * The systems root dir, ending with "/"
    */      
    define("__REDUNDANCY_ROOT__",$path);
    //Some stuff...
    require_once "./Includes/Kernel/Kernel.Installer.class.php";
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
    }
    if (isset($_GET["step"]) && $_GET["step"] == "license"){
        $step = 3;
    }
    if (isset($_GET["step"]) && $_GET["step"] == "db"){
        $step = 4;
    }

    if (isset($_POST["hostname"])){
        //$user,$pass,$host,$dbname,$driver
        $dbConnResult =  $k->TestDBConnection($_POST["username"],$_POST["password"],$_POST["hostname"],$_POST["db"],$_POST["driver"]);
        $write = $k->WriteDBConfig($_POST["username"],$_POST["password"],$_POST["hostname"],$_POST["db"],$_POST["driver"]);
        $dbimport = $k->DoTheImport();
        if ($dbConnResult && $write )
            $step = 5;
    }
    else if (isset($_POST["username"])){
        if ($k->SetUser($_POST["username"],$_POST["password"]))
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
                                            echo $GLOBALS["Language"]["CannotWrite"];
                                            $fail = true;
                                        }                                           
                                        else
                                            echo $GLOBALS["Language"]["CanWrite"];
                                       ?></li>
                                    <?php endforeach;?>
                                </ul>
                                <a class="btn btn-primary" href ="./install.php"><?php echo $GLOBALS["Language"]["Back"];?></a>
                                <?php if (!isset($fail)) :?>
                                <a class="btn btn-default" href ="./install.php?step=license&lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["NextLicense"];?></a>
                            <?php endif;?>
                            <?php endif ;?>
                            <?php if ($step == 3) :?>
                                <h1 class="light header-form gray"><?php echo $GLOBALS["Language"]["Installation"];?></h1>
                                <div class="well"><?php echo $GLOBALS["Language"]["InstallationLicenseText"];?></div>
                                <a class="btn btn-primary" href ="./install.php?lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["Back"];?></a>
                                <a class="btn btn-default" href ="./install.php?step=db&lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["NextDB"];?></a>
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
                                        <label for='username'>
                                            <?php echo $GLOBALS['Language']["Username"];?></label>
                                        <input type='text' class='form-control' name='username' placeholder='<?php echo $GLOBALS['Language']["Username"];?>' 
                                        value ="<?php echo isset($_POST["username"]) ? $_POST["username"] : "";?>" required>
                                    </div>
                                    <div class='form-group '>
                                        <label for='password'>
                                            <?php echo $GLOBALS['Language']["Password"];?></label>
                                        <input type='password' class='form-control' name='password' placeholder='<?php echo $GLOBALS['Language']["Password"];?>' 
                                        value ="<?php echo isset($_POST["password"]) ? $_POST["password"] : "";?>"
                                        >
                                    </div>
                                    <div class='form-group '>
                                        <label for='db'>
                                            <?php echo $GLOBALS['Language']["Database"];?></label>
                                        <input type='text' class='form-control' name='db' placeholder='<?php echo $GLOBALS['Language']["Database"];?>' 
                                        value ="<?php echo isset($_POST["db"]) ? $_POST["db"] : "";?>"
                                        required>
                                    </div>
                                    <div class='form-group '>
                                        <label for='hostname'>
                                            Hostname</label>
                                        <input type='text' class='form-control' name='hostname' value='localhost' required>
                                    </div>
                                    <div class='form-group '>
                                        <label for='driver'>
                                              <?php echo $GLOBALS['Language']["Driver"];?></label>
                                            <select class = "form-control" name="driver" required>
                                                <option>MySQL</option>
                                            </select>
                                    </div>
                                     <a class="btn btn-primary" href ="./install.php?step=license&lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["Back"];?></a>
                                <input type ="submit" class="btn btn-default" value="<?php echo $GLOBALS["Language"]["NextUser"];?>"</input>
                                </form>
                               
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
                                    <a class="btn btn-primary" href ="./install.php?step=license&lng=<?php echo  $lng ;?>"><?php echo $GLOBALS["Language"]["Back"];?></a>
                                <input type ="submit" class="btn btn-default" value="<?php echo $GLOBALS["Language"]["NextFinish"];?>"</input>
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
