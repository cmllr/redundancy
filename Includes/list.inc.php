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
	include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";	
	include $_SESSION["Program_Dir"]."Includes/broadcrumbs.inc.php";	
	
	$user = mysql_real_escape_string($_SESSION["user_id"]);	
	$result = mysql_query("Select * from Files  where UserID = '$user' and Directory ='".mysql_real_escape_string($dir)."'") or die("Error: 014 ".mysql_error());
	$rows = mysql_affected_rows();	
	if (mysql_affected_rows() > 0){
		//Only start the table if we have files in there.
		//Display an information if the program should process copy or movement
		if (isset($_GET["move"]) || isset($_GET["copy"]))
			echo "<h2 id = 'copyhint' >Click on the directory name to copy or move the file</h2>";
		echo "<table id = 'filetable'><tr><th></th><th>Name</th><th>Uploaded</th><th>Size</th><th>Actions</th><th>Share status</th></tr>";
	}
	//Display the resultss
	while ($row = mysql_fetch_object($result)) {
		$date = strtotime($row->Uploaded);
		$Share_Status = "";	
		if ($row->Displayname != $row->Filename){
			//Check if file is shared
			$shared = isShared($row->Hash);
			//Display an equivalent link to the share result
			if ($shared)
				$Share_Status = "<a class = 'shared' href = 'index.php?module=share&file=".$row->Hash."&delete=true'>Shared</a>";
			else
				$Share_Status = "<a href = 'index.php?module=share&file=".$row->Hash."&new=true'>Share</a>";
			//Default image path, only change if the file is a image and the config allows it
			$imagepath = './Images/page.png';			
			if (isImage($_SESSION["Program_Dir"]."Storage/".$row->Filename) && $_SESSION["config"]["Program_Display_Icons_if_needed"] == 1)
			{
				$imagepath = "index.php?module=image&thumb=true&file=".$row->Hash;
			}
		}	
		//Create move links if needed
		//Include database file
		include $_SESSION["Program_Dir"]."Includes/DataBase.inc.php";
		
		if (isset($_GET["file"]))
			$fileToCopyOrToMove = mysql_real_escape_string($_GET["file"]);
		if (isset($_GET["source"]))
			$fileToCopyOrToMove = mysql_real_escape_string($_GET["source"]);
		if (isset($_GET["move"]) && isset($_GET["file"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=move&dir=".$row->Displayname."&file=".mysql_real_escape_string($_GET["file"])."'>".$row->Displayname."</a>";
		else if (isset($_GET["copy"]) && isset($_GET["file"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=copy&dir=".$row->Displayname."&file=".mysql_real_escape_string($_GET["file"])."'>".$row->Displayname."</a>";
		else if (isset($_GET["move"]) && isset($_GET["source"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=move&source=".$_GET["source"]."&target=".$row->Displayname."&old_root=".$_GET["old_root"]."'>".$row->Displayname."</a>";
		else if (isset($_GET["copy"]) && isset($_GET["source"]))
			$dirlink = "<a class = 'filelink' href = 'index.php?module=copy&source=".$_GET["source"]."&target=".$row->Displayname."&old_root=".$_GET["old_root"]."'>".$row->Displayname."</a>";
		else
			$dirlink = "<a class = 'filelink' href = 'index.php?module=list&dir=".$row->Displayname."'>".getDisplayName($row->Displayname,$row->Filename)."</a>";
	
		
		//This is needed to allow alternating colors
		if ($i%2 == 0 )
			$suffix = 0;
		else
			$suffix = 1;		
		$i++;
		//Display the file or directory links itself
		if ($row->Displayname == $row->Filename)
			echo "<tr class = 'filetype$suffix'><td><img  src='./Images/folder.png'></td><td>$dirlink</td><td>".date("j.n.Y H:i",$date)."</td><td class ='size'>".getFittingDisplayStlye(getDirectorySize($row->Displayname))."</td><td class =  'actions' ><a class = 'delete' href ='index.php?module=delete&dir=".$row->Filename."'><img  src = './Images/folder_delete.png'></a><a class = 'delete' href ='index.php?module=list&move=true&source=".$row->Filename."&old_root=".$row->Directory."'><img  src = './Images/cut_red.png'></a><a class = 'delete' href = 'index.php?module=list&copy=true&source=".$row->Filename."&old_root=".$row->Directory."'><img src= './Images/page_copy.png'></a></td><td></td></tr>";
		else
			echo "<tr class = 'filetype$suffix'><td><img src='$imagepath'></td><td><a class = 'filelink' href = 'index.php?module=file&file=".$row->Hash."'>".$row->Displayname."</a></td><td>".date("j.n.Y H:i",$date)."</td><td class ='size'>".getFittingDisplayStlye($row->Size)."</td><td class =  'actions' ><a class = 'delete'href ='index.php?module=delete&file=".$row->Hash."'><img  src = './Images/page_delete.png'></a><a class = 'delete' href ='index.php?module=list&move=true&file=".$row->Hash."'><img  src = './Images/cut_red.png'></a><a class = 'delete' href ='index.php?module=list&copy=true&file=".$row->Hash."'><img  src = './Images/page_copy.png'></a></td><td>$Share_Status</td></tr>";
	}
	
	if ($rows == 0)
		echo "<h2>Nothing there :(<h2>";
	//Display links to move or copy files and directories	
	if (isset($_GET["move"])   && $_SESSION["currentdir"] == "/" && isset($_GET["file"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&dir=/&file=".$fileToCopyOrToMove."'>Paste into Home</a></td><td></td><td>".getFittingDisplayStlye("/")."</td><td class =  'actions' ></td><td></td></tr>";
	else if ( isset($_GET["copy"]) && $_SESSION["currentdir"] == "/" && isset($_GET["file"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&dir=/&file=".$fileToCopyOrToMove."'>Paste into Home</a></td><td></td><td>".getFittingDisplayStlye("/")."</td><td class =  'actions' ></td><td></td></tr>";
	else if (isset($_GET["move"]) && $_SESSION["currentdir"] == "/" && isset($_GET["source"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&target=/&source=".$fileToCopyOrToMove."&old_root=".$_GET["old_root"]."'>Paste into Home</a></td><td></td><td>".getFittingDisplayStlye("/")."</td><td class =  'actions' ></td><td></td></tr>";
	else if (isset($_GET["copy"]) && $_SESSION["currentdir"] == "/" && isset($_GET["source"]))
		echo "<tr class = 'filetype$suffix'><td><img src='./Images/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&target=/&source=".$fileToCopyOrToMove."&old_root=".$_GET["old_root"]."'>Paste into Home</a></td><td></td><td>".getFittingDisplayStlye("/")."</td><td class =  'actions' ></td><td></td></tr>";
	//Close the table
	echo "</table>";
	//Display the delete link if we are not in HOME
	if ($_SESSION["currentdir"] != "/")
		echo "<a href ='index.php?module=delete&dir=".$_SESSION['currentdir']."'>Delete</a>";
?>