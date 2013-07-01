<?php
if (isset($_POST["regemail"])){	
	if ($_POST["newpass"] == $_POST["newpass_repeat"] && $GLOBALS["config"]["User_Enable_Recover"] == 1)
		setNewPassword($_POST["regemail"],$_POST["pass"],$_POST["newpass"]);
	}	
?>
<form method="POST" action="index.php?module=setpass" id="login">
<p>
    <label for="regemail"><?php echo $GLOBALS["Program_Language"]["Email"];?></label>
    <input class ="text" name="regemail" />
</p>
<p>
    <label for="pass"><?php echo $GLOBALS["Program_Language"]["Old_Pass"];?></label>
    <input class ="text"  name="pass" type="password" />
</p>
<p>
    <label for="newpass"><?php echo $GLOBALS["Program_Language"]["New_Pass"];?></label>
    <input class ="text"  name="newpass" type="password" />
</p>
<p>
    <label for="newpass_repeat"><?php echo $GLOBALS["Program_Language"]["New_Pass_Repeat"];?></label>
    <input class ="text"  name="newpass_repeat" type="password" />
</p>
<p class="loginSubmit">
    <input type="submit" value="<?php echo $GLOBALS["Program_Language"]["Set"];?>" />
</p>
</form>
