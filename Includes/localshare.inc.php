<?php 
	if (!isset($_SESSION))
		session_start();
	if ($_SESSION["role"] == 3)
	{
		header("Location: index.php?module=list");
		exit;
	}
?>
<?php $file = $_GET["file"];?>
<div class="panel-body">
<h2 class="hidden-xs"><?php echo $GLOBALS["Program_Language"]["LocalShare"];?> - <a href = "?module=file&file=<?php echo $file;?>"><?php echo getShortenedDisplayname(getFileByHash($_GET["file"])) ;?></a></h2>
<h3 class="visible-xs"><?php echo $GLOBALS["Program_Language"]["LocalShare"];?> - <a href = "?module=file&file=<?php echo $file;?>"><?php echo getShortenedDisplayname(getFileByHash($_GET["file"])) ;?></a></h3>

<div class="panel-body">
<form class="form-horizontal" method="POST" action="index.php?module=localshare&file=<?php echo $_GET["file"];?>">	
	<div class="form-group">
		<div class="alert alert-info"><?php echo $GLOBALS["Program_Language"]["LocalShareDesc"];?></div>	
		<label for="pass" class="col-lg-3 control-label"><?php echo $GLOBALS["Program_Language"]["Username"];?></label>
		<div class="col-lg-9">
			<input type="text" class="form-control" name="username_info" placeholder="<?php echo $GLOBALS["Program_Language"]["Username"];?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<input class="btn-block btn btn-default" type="submit" name="submit" value="<?php echo $GLOBALS["Program_Language"]["SearchAndShare"];?>">		
		</div>
	</div>
	<?php
		getLocalShares($_GET["file"]);
	?>
</form>
</div></div>
<?php if (isset($_GET["delete"]) && isset($_GET["user"]) && isset($_GET["file"])) :?>
<?php
	$user = $_GET["user"];
	$file = $_GET["file"];	
	if (!isGuest() && deleteLocalShare($file,$user)){
		if (isset($_GET["returnto"])){
			if ($_GET["returnto"] == "manageshares"){			
				header("Location: index.php?module=manageshares&message=ShareDeletedSuccess");
				exit;
			}
		}
		header("Location: index.php?module=localshare&file=".$hash."&message=ShareDeletedSuccess");
		exit;
	}
?>
<?php endif;?>
<?php if (isset($_POST["username_info"]) && isExisting("",$_POST["username_info"]) == true) :?>
	<?php
		$file = getFileIDByHash($_GET["file"]);
		$hash = $_GET["file"];
		$user = getUserID($_POST["username_info"]);
		if ($user == -1){
			header("Location: index.php?module=localshare&file=".$_GET["file"]."&message=NotShared");
			exit;
		}			
		if (isOwner($hash,$user)){
			header("Location: index.php?module=localshare&file=".$_GET["file"]."&message=SharedFailAlreadyOwner");
			exit;
		}	
		if (isLocalShared($_GET["file"],$user)){		
			header("Location: index.php?module=localshare&file=".$_GET["file"]."&message=SharedFailExists");
			exit;
		}
			
		if (createInternalShare($file,$user,4))
		{
			header("Location: index.php?module=localshare&file=".$_GET["file"]."&message=SharedSuccess");
			exit;
		}
		else{
			header("Location: index.php?module=localshare&file=".$_GET["file"]."&message=NotShared");
			exit;
		}
	?>
<?php endif;?>
<?php
	if (isset($_POST)  && isset($_POST["username_info"]) &&  isExisting("",$_POST["username_info"]) == false)
	{		
		header("Location: index.php?module=localshare&file=".$_GET["file"]."&message=NotSharedNoUser");
		exit;
	}
?>