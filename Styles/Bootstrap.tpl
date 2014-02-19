Template_Header = "
	  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <!-- Bootstrap -->
        <link href=\"Lib/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">  
		<link rel=\"stylesheet\" href=\"Lib/bootstrap/css/elusive-webfont.css\">
		<link rel=\"stylesheet\" href=\"Lib/bootstrap/css/custom.css\">
        ";
Template_Wrapper = "<div class = "row">";
Template_Container_NO_JS = "";
Template_Container_JS = "<div class = \"container\">";
Table_Definition = "<table class = 'table table-striped table-hover'>";
Table_Definition_Additional = "##def<th></th><th>##name</th>";
Uploaded = true;
Uploaded_template_header = "<th class = 'hidden-xs' >##uploaded</th>";
Copy_Hint = "<div class=\"alert alert-info\">##Paste_Description <a href = 'index.php?module=list'>##Abort</a></div>";
Size = true;
Size_template_header = "<th class ='hidden-xs'>##size</th>";
Actions = true;
Actions_template_header = "<th class = 'visible-xs hidden-mg visible-lg'>##actions</th>";
Status = false;
Status_template_header = "<th class = 'hidden-xs'>##status</th>";
Actions_template_file = "<td class =  'visible-xs hidden-mg visible-lg' ><a class = 'fileActions' title = '##delete' href ='index.php?module=delete&file=##hash'><span class=\"elusive icon-remove-sign glyphIcon\"></span></a><a class = 'fileActions' title = '##cut' href ='index.php?module=list&move=true&file=##hash'><span class=\"elusive icon-tag glyphIcon\"></span></a><a class = 'fileActions' title = '##copy' href ='index.php?module=list&copy=true&file=##hash'><span class=\"elusive icon-tags glyphIcon\"></span></a><a class = 'fileActions' title = '##rename' href ='index.php?module=rename&file=##hash'><span class=\"elusive icon-edit glyphIcon\"></span></a><a class = 'fileActions' title = '##download' href ='index.php?module=download&file=##hash'><span class=\"elusive icon-download-alt glyphIcon\"></span></a>";
Actions_template_folder = "<td class =  'visible-xs hidden-mg visible-lg' ><a class = 'fileActions' title = '##delete' href ='index.php?module=delete&dir=##filename'><span class=\"elusive icon-remove-sign glyphIcon\"></span></a><a class = 'fileActions' title = '##cut' href ='index.php?module=list&move=true&source=##filename&old_root=##directory'><span class=\"elusive icon-tag glyphIcon\"></span></a><a class = 'fileActions' title = '##copy' href = 'index.php?module=list&copy=true&source=##filename&old_root=##directory'><span class=\"elusive icon-tags glyphIcon\"></span></a><a class = 'fileActions' title = '##rename' href ='index.php?module=rename&source=##displayname&old_root=##currentdir'><span class=\"elusive icon-edit glyphIcon\"></span></a><a class = 'fileActions' title = '##download' href ='index.php?module=zip&dir=##displayname'><span class=\"elusive icon-download-alt glyphIcon\"></span></a>";
Status_template_shared = "<a class = 'hidden-xs' href = 'index.php?module=share&file=##hash&delete=true'><span class=\"elusive icon-ok-sign glyphIcon\"></span></a>";
Status_template_share = "<a class = 'hidden-xs' href = 'index.php?module=share&file=##hash&new=true'><span class=\"elusive icon-remove-sign glyphIcon\"></span></a>";
Table_Item_Definition = "<tr>";
Table_Item_template = "<td><img  src='##imagepath'></td><td   id = '##hash' >##dirlink</td><td  class ='hidden-xs' >##uploaded</td><td class ='size hidden-xs'>##size</td>";
Table_Item_Search_template = "<td class =  'actions' ><a class = 'fileActions' title = '##open' href ='index.php?##modulelink'><span class=\"elusive icon-folder-open glyphIcon\"></span></a></td>";
Table_File_Definition = "<tr>";
Table_File_template = "<td><img src='##imagepath'></td><td  id = '##hash' ><a class = 'filelink' title = '##directory##displayname' href = 'index.php?module=file&file=##hash'>##croppeddisplayname</a></td><td class ='hidden-xs' >##uploaded</td><td class ='size hidden-xs'>##size</td>";
Share_Link = "<a class = 'hidden-xs' href ='##link'><span class=\"elusive icon-share-alt glyphIcon\"></a>";
Move_file= "<tr ><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&dir=##currentdir&file=##fileToCopyOrToMove'>##Paste_Home</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
Move_folder="<tr><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=move&target=##currentdir&source=##fileToCopyOrToMove&old_root=##old_root'>##Paste_Home</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
Copy_file= "<tr ><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&dir=##currentdir&file=##fileToCopyOrToMove'>##Paste_Home</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
Copy_folder="<tr ><td><img src='./Images/mimetypes/folder.png'></td><td><a class = 'filelink' href = 'index.php?module=copy&target=##currentdir&source=##fileToCopyOrToMove&old_root=##old_root'>##Paste_Home</a></td><td></td><td></td><td class =  'actions' ></td><td></td></tr>";
Delete_folder = "<div class="btn-group " id="fileActionBtnGroup"><a type=\"a\" href = 'index.php?module=delete&dir=##currentdir'class=\"btn btn-default\"><span class=\"elusive icon-trash glyphIcon\"></span><span class='hidden-xs'>##Delete_Folder</span></a>";