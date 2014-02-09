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
<form role="form" method="POST" action="index.php?module=localshare&file=<?php echo $_GET["file"];?>">

<div class="panel panel-default">
	<div class="panel-heading">	<?php echo $GLOBALS["Program_Language"]["LocalShare"];?> - <a href = "?module=file&file=<?php echo $file;?>"><?php echo getShortenedDisplayname(getFileByHash($_GET["file"])) ;?></a>
		</div>
	<div class="panel-body">
		<div class="well well-sm"><?php echo $GLOBALS["Program_Language"]["LocalShareDesc"];?></div>		
		<div class="input-group" id="inputUsername">
			<span class="input-group-addon">
				<?php echo $GLOBALS["Program_Language"]["Username"];?>
			</span>
			<input name="username_info" type="text" class="form-control" placeholder="">
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit">
					<?php echo $GLOBALS["Program_Language"]["SearchAndShare"];?>
				</button>
			</span>
		</div>
	<?php
		getLocalShares($_GET["file"]);
	?>
	</div>
</div>
</form>
<?php if (isset($_GET["delete"]) && isset($_GET["user"]) && isset($_GET["file"])) :?>
<?php
	$user = $_GET["user"];
	$file = $_GET["file"];	
	$hash = getFileHashByID($file);
	if (deleteLocalShare($file,$user)){
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