<?php	
	//start session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Set path variable
	if (isset($_GET["dir"])){
		$dir = $_GET["dir"];
		$_SESSION["currentdir"]	= $dir;	
	}
	else
		$dir = $_SESSION["currentdir"];
	//Includes DataBase and broadcrumbs and DataBase
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	if (isset($_GET["share"]) == false)
		include $GLOBALS["Program_Dir"]."Includes/broadcrumbs.inc.php";		
	else
		exit;
	$i = 0;

	//	echo $dir;
	$id = getDirectoryID(mysqli_real_escape_string($connect,$dir));
	
	if (!isset($_SESSION["user_logged_in"]))
	{
	exit;
		$result = mysqli_query($connect,"Select * from Files  where Directory_ID ='".$id."'") or die("Error: 014 ".mysqli_error());
		if (startsWith(mysqli_real_escape_string($connect,$dir),$_SESSION["shared_dir"]) != true)
			header("Location: index.php");
		
	}	
	else
	{
		$user = mysqli_real_escape_string($connect,$_SESSION["user_id"]);	
		$result = mysqli_query($connect,"Select * from Files  where UserID = '$user' and Directory_ID ='".$id."'") or die("Error: 014 ".mysqli_error());
	}
	
	$rows = mysqli_affected_rows($connect);	
	if (mysqli_affected_rows($connect) > 0){
		//Only start the table if we have files in there.
		//Display an information if the program should process copy or movement
		if (isset($_GET["move"]) || isset($_GET["copy"]))
			echo "<h3 id = 'copyhint' >".$GLOBALS["Program_Language"]["Paste_Description"]." <a href = 'index.php?module=list'>".$GLOBALS["Program_Language"]["Abort"]."</a></h3><br>";
		echo "<table id = 'filetable'><tr><th></th><th>".$GLOBALS["Program_Language"]["Name"]."</th><th>".$GLOBALS["Program_Language"]["Uploaded"]."</th><th>".$GLOBALS["Program_Language"]["Size"]."</th><th>".$GLOBALS["Program_Language"]["Actions"]."</th><th>".$GLOBALS["Program_Language"]["Share_Status"]."</th></tr>";
	}
	//Display the resultss
	while ($row = mysqli_fetch_object($result)) {
		$date = strtotime($row->Uploaded);		
		$Share_Status = "";	
		if ($row->Displayname != $row->Filename){
			//Check if file is shared
		
			//Default image path, only change if the file is a image and the config allows it
			$imagepath = './Images/page.png';			
			if (isImage($row->Filename) && $GLOBALS["config"]["Program_Display_Icons_if_needed"] == 1)
			{
				$imagepath = "index.php?module=image&thumb=true&file=".$row->Hash;
			}			
		}
		else
		{
			$imagepath = "./Images/folder.png";
			if ($_SESSION["currentdir"] == "/" && $row->Filename == "/Bin/" && $row->ReadOnly == 1)
			{
				$imagepath = './Images/bin_closed.png';	
			}			
		}
	
		$shared = isShared($row->Hash);
		if ($shared)
			$Share_Status = "<a class = 'shared' href = 'index.php?module=share&file=".$row->Hash."&delete=true'>".$GLOBALS["Program_Language"]["Shared"]."</a>";
		else
			$Share_Status = "<a href = 'index.php?module=share&file=".$row->Hash."&new=true'>".$GLOBALS["Program_Language"]["Share"]."</a>";
		//if ($row->Displayname == $row->Filename)
		//	$Share_Status = "";
		//Create move links if needed
		//Include database file
		include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";
		
		if (isset($_GET["file"]))
			$fileToCopyOrToMove = mysqli_real_escape_string($connect,$_GET["file"]);
		if (isset($_GET["source"]))
			$fileToCopyOrToMove = mysqli_real_escape_string($connect,$_GET["source"]);
		if (isset($_GET["move"]) && isset($_GET["file"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=move&dir=".$row->Displayname."&file=".mysqli_real_escape_string($connect,$_GET["file"])."'>".$row->Filename_only."</a>";
		else if (isset($_GET["copy"]) && isset($_GET["file"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=copy&dir=".$row->Displayname."&file=".mysqli_real_escape_string($connect,$_GET["file"])."'>".$row->Filename_only."</a>";
		else if (isset($_GET["move"]) && isset($_GET["source"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=move&source=".$_GET["source"]."&target=".$row->Displayname."&old_root=".$_GET["old_root"]."'>".$row->Filename_only."</a>";
		else if (isset($_GET["copy"]) && isset($_GET["source"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=copy&source=".$_GET["source"]."&target=".$row->Displayname."&old_root=".$_GET["old_root"]."'>".$row->Filename_only."</a>";
		else
			$dirlink = "<a class = 'filelink' href = 'index.php?module=list&dir=".$row->Displayname."'>".getDisplayName($row->Filename_only,$row->Filename)."</a>";
	
		
		//This is needed to allow alternating colors
		if ($i%2 == 0 )
			$suffix = 0;
		else
			$suffix = 1;		
		$i++;
		//Display the file or directory links itself
		if (isset($_GET["share"]))
			$Share_Status = "";
		if ($row->Displayname == $row->Filename)
		{
			echo "<tr class = 'filetype$suffix'><td><img  src='$imagepath'></td><td>$dirlink</td><td>".date("j.n.Y H:i",$date)."</td><td class ='size'>".getFittingDisplayStlye(getDirectorySize($row->Displayname))."</td><td class =  'actions' >";
			if (isset($_SESSION["user_logged_in"]))
				echo "<a class = 'delete' title = '".$GLOBALS["Program_Language"]["Delete"]."' href ='index.php?module=delete&dir=".$row->Filename."'><img  src = './Images/folder_delete.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Cut"]."' href ='index.php?module=list&move=true&source=".$row->Filename."&old_root=".$row->Directory."'><img  src = './Images/cut_red.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Copy"]."' href = 'index.php?module=list&copy=true&source=".$row->Filename."&old_root=".$row->Directory."'><img src= './Images/page_copy.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Rename"]."' href ='index.php?module=rename&source=".$row->Displayname."&old_root=".$_SESSION["currentdir"]."'><img  src = './Images/textfield_rename.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Zip"]."' href ='index.php?module=zip&dir=".$row->Displayname."'><img  src = './Images/page_white_zip.png'></a>";
			echo "</td><td>$Share_Status";
			if ($shared)
				echo "<a href ='".getShareLink($row->Hash)."'><img src = './Images/link_go.png' alt='link' title='".$GLOBALS["Program_Language"]["Share_Link"]."'></a>";
			echo "</td></tr>";
		}			
		else
		{
			 echo "<tr class = 'filetype$suffix'><td><img src='$imagepath'></td><td><a class = 'filelink' href = 'index.php?module=file&file=".$row->Hash."'>".htmlentities(utf8_decode($row->Displayname))."</a></td><td>".date("j.n.Y H:i",$date)."</td><td class ='size'>".getFittingDisplayStlye($row->Size)."</td>";
			if (isset($_SESSION["user_logged_in"]))
				echo "<td class =  'actions' ><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Delete"]."' href ='index.php?module=delete&file=".$row->Hash."'><img  src = './Images/page_delete.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Cut"]."' href ='index.php?module=list&move=true&file=".$row->Hash."'><img  src = './Images/cut_red.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Copy"]."' href ='index.php?module=list&copy=true&file=".$row->Hash."'><img  src = './Images/page_copy.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Rename"]."' href ='index.php?module=rename&file=".$row->Hash."'><img  src = './Images/textfield_rename.png'></a><a class = 'delete' title = '".$GLOBALS["Program_Language"]["Download"]."' href ='index.php?module=download&file=".$row->Hash."'><img  src = './Images/arrow_down.png'></a>";
				echo "</td><td>$Share_Status";
			if ($shared)
				echo "<a href ='".getShareLink($row->Hash)."'><img src = './Images/link_go.png' alt='link' title='".$GLOBALS["Program_Language"]["Share_Link"]."'></a>";
			echo "</td></tr>";
		}
			
	}
	
	if ($rows == 0)
		echo "<h2>Nothing there :(<h2>";
	//Display links to move or copy files and directories	
	if (isset($_GET["move"])   && $_SESSION["currentdir"] == "/" && isset($_GET["file"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&dir=/&file=".$fileToCopyOrToMove."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td>".getFittingDisplayStlye("/")."</td><td class =  'actions' ></td><td></td></tr>";
	else if ( isset($_GET["copy"]) && $_SESSION["currentdir"] == "/" && isset($_GET["file"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&dir=/&file=".$fileToCopyOrToMove."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td>".getFittingDisplayStlye("/")."</td><td class =  'actions' ></td><td></td></tr>";
	else if (isset($_GET["move"]) && $_SESSION["currentdir"] == "/" && isset($_GET["source"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&target=/&source=".$fileToCopyOrToMove."&old_root=".$_GET["old_root"]."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td>".getFittingDisplayStlye("/")."</td><td class =  'actions' ></td><td></td></tr>";
	else if (isset($_GET["copy"]) && $_SESSION["currentdir"] == "/" && isset($_GET["source"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&target=/&source=".$fileToCopyOrToMove."&old_root=".$_GET["old_root"]."'>".$GLOBALS["Program_Language"]["Paste_Home"]."</a></td><td></td><td>".getFittingDisplayStlye("/")."</td><td class =  'actions' ></td><td></td></tr>";
	//Close the table
	echo "</table>";	
	//Display the delete link if we are not in HOME
	if ($_SESSION["currentdir"] != "/" && isset($_GET["share"]) == false)
		echo "<a href ='index.php?module=delete&dir=".$_SESSION['currentdir']."'>Delete</a>";
?>