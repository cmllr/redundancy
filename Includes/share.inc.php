<?php
	if (isset($_SESSION) == false)
		session_start();
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
		if (isset($_GET["file"]))
	$file = mysqli_real_escape_string($connect,$_GET["file"]);
	if (isset($_SESSION["user_id"]))
		$userID = $_SESSION["user_id"];	
	$code = "";
	if (isset($_GET["delete"]) && $_GET["delete"] == "true" && $_SESSION["role"] != 3)
	{					
		mysqli_query($connect,"delete from `Share` where  Hash = '$file' and UserID = '$userID'") or die("Error: 026");
		mysqli_close($connect);	
		header ("Location: index.php?module=list");
	}
	else if (isset($_GET["new"]) && $_GET["new"] == "true" && $_SESSION["role"] != 3)
	{
		mysqli_query($connect,"Select *  from `Share` where  Hash = '$file' and UserID = '$userID'")  or die("Error: 027");
		if (mysql_affected_rows() > 0)
		{
			//File is already shared
		}
		else
		{
			$found =false;
			$code = getRandomKey(50);
			do{
			
				echo $code;
				mysqli_query($connect,"Select *  from `Share` where  Extern_ID = '$code'")  or die("Error: 028");
				if (mysql_affected_rows() > 0)
				{
					$code = getRandomKey(50);
					$found = true;					
				}
			}while($found == true );				
			//TODO: FIx entry bug			
			$insert = "INSERT INTO Share (Hash,UserID,Extern_ID) VALUES ('$file',$userID,'$code')";			
			echo mysqli_query($connect,$insert) or die("Error: 028");
			echo mysqli_error($connect);			
		}
		mysqli_close($connect);			
			header ("Location: index.php?module=list");
	}
	else if (isset($_GET["display"]) && $_GET["display"] == "true" )
	{	
		//[Obsolete]
		$result = mysqli_query($connect,"Select * from Share  where UserID = '$userID' and Hash ='".$file."'") or die("Error: 029".mysqli_error($connect));	
		while ($row = mysqli_fetch_object($result)) {
			$sharetext = $_SERVER["SERVER_NAME"].$GLOBALS["config"]["Program_Share_Dir"]."index.php?share=".$row->Extern_ID;
			echo "<textarea cols='70' rows='2'>$sharetext</textarea><br>";
			echo "<a href = 'index.php?module=file&file=".$row->Hash."'>View</a> ";			
			echo "<a href = 'index.php?module=share&file=".$row->Hash."&delete=true'>Unshare</a>";			
		} 
		mysqli_close($connect);	
	}
	else if (isset($_GET["share"]))
	{
		$share = mysqli_real_escape_string($connect,$_GET["share"]);
		$result = mysqli_query($connect,"Select * from Share  where Extern_ID = '$share' limit 1") or die("Error: 029".mysqli_error($connect));	
		while ($row = mysqli_fetch_object($result)) {
			$hash = $row->Hash;
			$resultDownload = mysqli_query($connect,"Select * from Files  where Hash = \"".$hash."\" limit 1") or die("Error: 029");
			while ($row = mysqli_fetch_object($resultDownload)) {																	
				$filenamenew = $row->Filename;
				$displayname = $row->Displayname;
			}				
			$fullPath = $GLOBALS["Program_Dir"]."Storage/".$filenamenew; 			
			if (file_exists($fullPath)) {
				header('Content-Description: File Transfer');
				header('Content-Type: ' .mime_content_type($filenamenew)); 
				header('Content-Disposition: attachment; filename='.$displayname);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fullPath));
				ob_clean();
				flush();
				readfile($fullPath);			  
			}			
		} 
		mysqli_close($connect);			
	}
	header("Location: index.php?module=list");
?>