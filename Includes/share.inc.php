<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * Any share functionality is stored in tis file.
	 */
	 //Include uri check
	require_once ("checkuri.inc.php");
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
		mysqli_query($connect,"delete  from `Share` where  Hash = '$file' and UserID = $userID") or die("Error: 026");
		
		$fileCheck = mysqli_query($connect,"Select * from Files  where Hash = \"".$file."\" limit 1") or die("Error: 029");
		while ($rowFile = mysqli_fetch_object($fileCheck)) {	
			if ($rowFile->Displayname == $rowFile->Filename){
				$isFile = false;
				$folder = $rowFile->Displayname;
			}else
				$isFile = true;
		}
			mysqli_close($connect);		
		if ($isFile)
			header ("Location: index.php?module=file&file=$file");
		else
			header ("Location: index.php?module=list");
	}
	else if (isset($_GET["new"]) && $_GET["new"] == "true" && $_SESSION["role"] != 3)
	{
		mysqli_query($connect,"Select *  from `Share` where  Hash = '$file' and UserID = $userID")  or die("Error: 027");
		if (mysql_affected_rows() > 0)
		{
			//File is already shared
		}
		else
		{
			$found =false;
			if ($GLOBALS["config"]["Program_Share_Anonymously"] == 0)
				$code = getFileByHash($file).getRandomKey($GLOBALS["config"]["Program_Share_Link_Length"]);
			else
				$code = getRandomKey($GLOBALS["config"]["Program_Share_Link_Length"]);
				
			do{
			
				echo $code;
				mysqli_query($connect,"Select *  from `Share` where  Extern_ID = '$code'")  or die("Error: 028");
				if (mysql_affected_rows() > 0)
				{
					if ($GLOBALS["config"]["Program_Share_Anonymously"] == 0)
						$code = getFileByHash($file).getRandomKey($GLOBALS["config"]["Program_Share_Link_Length"]);
					else
						$code = getRandomKey($GLOBALS["config"]["Program_Share_Link_Length"]);
					$found = true;					
				}
			}while($found == true );	
				$fileCheck = mysqli_query($connect,"Select * from Files  where Hash = \"".$file."\" limit 1") or die("Error: 029");
			
				while ($rowFile = mysqli_fetch_object($fileCheck)) {	
				if ($rowFile->Displayname == $rowFile->Filename){
					$isFile = false;
					$folder = $rowFile->Displayname;
				}else
					$isFile = true;
				}
			//TODO: FIx entry bug			
			$insert = "INSERT INTO Share (Hash,UserID,Extern_ID,Used) VALUES ('$file',$userID,'$code',0)";			
			echo mysqli_query($connect,$insert) or die("Error: 028");
			echo mysqli_error($connect);			
		}
		mysqli_close($connect);		
		if ($isFile)
			header ("Location: index.php?module=file&file=$file");
		else
			header ("Location: index.php?module=list");
	}	
	else if (isset($_GET["share"]))
	{
		$share = mysqli_real_escape_string($connect,$_GET["share"]);
		$result = mysqli_query($connect,"Select * from Share  where Extern_ID = '$share' limit 1") or die("Error: 029".mysqli_error($connect));	
		$used = 0;
		while ($row = mysqli_fetch_object($result)) {
			$hash = $row->Hash;
			$used = $row->Used;
			$resultDownload = mysqli_query($connect,"Select * from Files  where Hash = \"".$hash."\" limit 1") or die("Error: 029");
			
			while ($row = mysqli_fetch_object($resultDownload)) {																	
				$filenamenew = $row->Filename;
				$displayname = $row->Displayname;
				$user = $row->UserID;
			}				
			$used++;
			if (!isset($_SESSION["user_logged_in"]))
				$_SESSION["user_id"] = $user;
			mysqli_query($connect,"Update Share set Used = $used where Extern_ID = '$share'");
			if ($filenamenew != $displayname){
			$fullPath = $GLOBALS["Program_Dir"]."Storage/".$filenamenew; 			
				if (file_exists($fullPath)) {
				
						header('Content-Description: File Transfer');
						header('Content-Type: ' .mime_content_type($filenamenew)); 
						if (isset($_GET["viewonly"]) == false)
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
			else
			{
				//include $GLOBALS["Program_Dir"]."Includes/Program.inc.php";
				echo $filenamenew;
				startZipCreation($filenamenew);
				if (!isset($_SESSION["user_logged_in"]))
					$_SESSION["user_id"] = -1;
			}
			
		} 
		
		if (mysqli_affected_rows($connect) == 0)
		{
			header("Location: index.php?message=DeadLink"); 
		}
		mysqli_close($connect);			
	}
	//header("Location: index.php?module=list");
?>