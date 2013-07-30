<?php
	//Common functions
	function getIP()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == false) {
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
			$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		if ($GLOBALS["config"]["Program_Privacy_Mask"] == 1)
		{
			$client_ip = substr($client_ip, 0, 4)."[...]";
		}
		return $client_ip;
	}
	function getIP2()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == false) {
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
			$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}		
		return $client_ip;
	}
	function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if ($length == 0) {
		return TRUE;
		}
		$start  = $length * -1;
		return (substr($haystack, $start) === $needle);
	}	
	function getRandomKey($length) {
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		return $randomString;
    }
	function getRandomPass($length) {
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#-+?/"), 0, $length);
		return $randomString;
    }
	function startsWith($haystack,$needle)
	{
		$ref = "";
		for ($i = 0; $i < strlen($needle) && strlen($haystack) >= strlen($needle);$i++)
			$ref .= strtolower($haystack[$i]);
		if (strtolower($needle) == $ref)
			return true;
		else
			return false;		
<<<<<<< HEAD
<<<<<<< HEAD
	}	
=======
	}
	function ui_create_contextmenu($hashcode,$count)
	{
		++$count;
		$shared = isShared(str_replace("#","",$hashcode));
		if ($shared)
			$Share_Status = "<a class = 'shared' href = 'index.php?module=share&file=".str_replace("#","",$hashcode)."&delete=true'><img  src = './Images/link_go.png'> ".$GLOBALS["Program_Language"]["Shared"]."</a>";
		else
			$Share_Status = "<a href = 'index.php?module=share&file=".str_replace("#","",$hashcode)."&new=true'><img  src = './Images/link_go.png'> ".$GLOBALS["Program_Language"]["Share"]."</a>";
		$folder = fs_is_Dir(str_replace("#","",$hashcode));
		echo "<ul id='context_menu$count' style='position:absolute;font-size:small'>";
		if ($folder == true)
		{
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
			$select = "Select * from Files where UserID = '$user' and Hash = '".str_replace("#","",$hashcode)."' limit 1";
			$result= mysqli_query($connect,$select);
			while ($row = mysqli_fetch_object($result)) {
					echo "<li><a href ='index.php?module=list&dir=".$row->Displayname."'><img  src = './Images/folder_magnify.png'> ".$GLOBALS["Program_Language"]["open_generic"]."</a></li>";
				echo "<li><a href ='index.php?module=delete&dir=".$row->Filename."'><img  src = './Images/folder_delete.png'> ".$GLOBALS["Program_Language"]["Delete"]."</a></li>";
				echo "<li><a class = 'delete' href ='index.php?module=list&move=true&source=".$row->Filename."&old_root=".$row->Directory."'><img  src = './Images/cut_red.png'> ".$GLOBALS["Program_Language"]["Cut"]."</a></li>";
				echo "<li><a class = 'delete' href = 'index.php?module=list&copy=true&source=".$row->Filename."&old_root=".$row->Directory."'><img src= './Images/page_copy.png'> ".$GLOBALS["Program_Language"]["Copy"]."</a></li>";
				echo "<li><a class = 'delete'  href ='index.php?module=rename&source=".$row->Displayname."&old_root=".$_SESSION["currentdir"]."'><img  src = './Images/textfield_rename.png'> ".$GLOBALS["Program_Language"]["Rename_title"]."</a></li>";
				echo "<li><a href ='index.php?module=zip&dir=".$row->Displayname."'><img  src = './Images/page_white_zip.png'> ".$GLOBALS["Program_Language"]["Zip"]."</a></li>";			
			}			
			mysqli_close($connect);	
		}
		else
		{
			include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
			$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
			$select = "Select * from Files where UserID = '$user' and Hash = '".str_replace("#","",$hashcode)."' limit 1";
			$result= mysqli_query($connect,$select);
			while ($row = mysqli_fetch_object($result)) {
				echo "<li><a href ='index.php?module=file&file=".$row->Hash."'><img  src = './Images/page_white_magnify.png'> ".$GLOBALS["Program_Language"]["open_generic"]."</a></li>";
				echo "<li><a href ='index.php?module=delete&file=".$row->Hash."'><img  src = './Images/folder_delete.png'> ".$GLOBALS["Program_Language"]["Delete"]."</a></li>";
				echo "<li><a class = 'delete' href ='index.php?module=list&move=true&file=".$row->Hash."'><img  src = './Images/cut_red.png'> ".$GLOBALS["Program_Language"]["Cut"]."</a></li>";
				echo "<li><a class = 'delete' href = 'index.php?module=list&copy=true&file=".$row->Hash."'><img src= './Images/page_copy.png'> ".$GLOBALS["Program_Language"]["Copy"]."</a></li>";
				echo "<li><a class = 'delete'  href ='index.php?module=rename&file=".$row->Hash."'><img  src = './Images/textfield_rename.png'> ".$GLOBALS["Program_Language"]["Rename_title"]."</a></li>";
				echo "<li><a href ='index.php?module=download&file=".$row->Hash."'><img  src = './Images/arrow_down.png'> ".$GLOBALS["Program_Language"]["Download"]."</a></li>";			
			}			
			mysqli_close($connect);	
		}
		echo "<li >$Share_Status</li></ul>";
		echo "<script>$(function() {\$('#context_menu$count').menu();\$('#context_menu$count').toggle();\$('$hashcode').bind('contextmenu', function(e){var MouseX;var MouseY;e.preventDefault();MouseX = e.pageX;MouseY = e.pageY;$('#context_menu$count').css({'top':MouseY,'left':MouseX});\$('#context_menu$count').toggle('clip', {}, 100 );return false;});";
		echo "\$('#context_menu$count').mouseleave(function(){\$(this).hide();});});";  
		echo "</script>";
	}
>>>>>>> 5e9a750acf0acdacbe14df627db66d91f30d2191
=======
	}	
>>>>>>> Re-Release of 1.9.8
?>