<div id="spinner"></div>
<ol class="breadcrumb" id="bc">	
</ol>
<table id='list' class="table table-striped table-hover">
	<tr><th></th><th id="namecolumn">Name</th><th class='hidden-xs' id="uploadcolumn">Hochgeladen am</th><th class='hidden-xs' id="sizecolumn">Größe</th></tr>
<script>
var SortBy = null;
var SortOrder = 1;
$("#namecolumn").click(function(){
	if (SortBy == "name" && SortOrder != -1)
		SortOrder = -1;
	else
		SortOrder = 1;
	SortBy = "name";
	Init();
});
$("#uploadcolumn").click(function(){
	if (SortBy == "upload" && SortOrder != -1)
		SortOrder = -1;
	else
		SortOrder = 1;
	SortBy = "upload";
	Init();
});
$("#sizecolumn").click(function(){
	if (SortBy == "size" && SortOrder != -1)
		SortOrder = -1;
	else
		SortOrder = 1;
	SortBy = "size";
	Init();
});

var currentDir = "<?php echo (isset($_SESSION['currentFolder'])) ? $_SESSION['currentFolder'] : "/" ; ?>";
if ($.urlParam("d") != null)
	currentDir = decodeURI($.urlParam("d"));
var token = "<?php echo $_SESSION['Token']; ?>";
var targets = null;
Init();
  function Init(){	  		
  		DisplaySpinner();
  		var arguments = [];
		arguments.push(token);	
		$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetFolderList',args: arguments })
			.done(function( data ) {	
				targets = $.parseJSON(data);	
				$(".entry").remove();		  		
		  		$("#bc").empty();
		  		$.contextMenu( 'destroy' );
				var arguments = [];
				arguments.push(currentDir);
				arguments.push(token);
				$.post( './Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetContent',args: arguments })
					.done(function( data ) {						
						var files = $.parseJSON(data);
						if (SortBy == "name"){
							files = sortJSON(files, "DisplayName", SortOrder);
						}
						if (SortBy == "upload"){
							files = sortJSON(files, "CreateDateTime", SortOrder);
						}
						if (SortBy == "size"){
							files = sortJSON(files, "SizeInBytes", SortOrder);
						}
						DisplayContent(files);
						DisplayBreadcrumbs(currentDir);
						HideSpinner();
					})
					.fail(function(e) {
						$("#list").remove();
						$(".panel-body").append("<div class='alert alert-danger'>R_ERR_"+e.responseText+"</div>");
						DisplayBreadcrumbs("/");
						HideSpinner();
				});		
			})
			.fail(function(e) {
			  console.log(e);			 
			  ErrorDialog(e.responseText);	
		});	
  		
  }
  function DisplayBreadcrumbs(absolutePath){
  		var parts = absolutePath.split("/");
  		var completePath = "/";
  		$("#bc").append("<li><a href='?files&d=/'>Home</a></li>");
  		for (var i = 0; i < parts.length;i++){
  			if (parts[i] != "/" && parts[i] != "" && parts[i] != "/"){
  				completePath = completePath + parts[i] + "/";
  				$("#bc").append("<li><a href='?files&d="+completePath+"'>"+parts[i]+"</a></li>");
  			}  				
  		}
  }
  function ReturnIcon(mime){
  	if (mime.indexOf("image") != -1)
  		return "fa-file-image-o";
  	else if (mime.indexOf("directory") != -1)
		return "fa-folder-open-o";
	else if (mime.indexOf("pdf") != -1)
		return "fa-file-pdf-o";
	else if (mime.indexOf("audio") != -1)
		return "fa-file-audio-o";
	else if (mime.indexOf("word") != -1)
		return "fa-file-word-o";
	else if (mime.indexOf("java") != -1)
		return "fa-coffee";
	else if (mime.indexOf("shell") != -1)
		return "fa-terminal";
  	else
  		return "fa-file-o";
  }
  function DisplayContent(data){   	
  		for (var i = 0; i < data.length;i++){  	  			
  			var content = '';  						
  			content = "<tr class='entry' id='"+data[i].Hash+"'><td>";
  			//if (data[i].Thumbnail != true)
  				content += "<i class='fa "+ReturnIcon(data[i].MimeType)+"'></i></td>";
  			//else
  			//	content += "<img src ='"+"./Thumbs/"+data[i].FilePath+"thumb"+"'></td>";
  			content += "<td><a class='filelink' id='HrefOf"+data[i].Id+"' href=''>"+data[i].DisplayName+"</a></td><td class='hidden-xs'>"+data[i].CreateDateTime+"</td><td class='size hidden-xs' id='SizeOf"+data[i].Id+"'>"+data[i].SizeWithUnit+"</td>";
  			content +="</tr>";
  			$('#list').append(content);		  		
  			if (data[i].FilePath != null){
  				$("#HrefOf"+data[i].Id).attr("href","?detail&f="+data[i].Hash);			  	
				$("#DownloadOf"+data[i].Id).attr("href","?download&f="+data[i].Hash); 
  			}  				
  			else{
  				DisplayLinksForFolder(data[i].Id);
  				GetSizeWithUnit(data[i].SizeInBytes,data[i].Id);
  			}
  			AddContextMenu(data[i]);	
  		}   			
  }
  /**
		logic Methods.
  **/
  function DeleteFileDialog(entry){  	
	var dialogTitle = "<?php echo $GLOBALS["Language"]->DeleteFileTitle; ?>".replace("%s",entry.DisplayName);  				
	var text = "<?php echo $GLOBALS["Language"]->DeleteFileText; ?>".replace("%s",entry.DisplayName);  	
	$( "<p>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 , modal: true,draggable:false, buttons: {
	"<?php echo $GLOBALS["Language"]->DeleteFileDeleteButton; ?>": function() {		        
		StartDeleteFile(entry);		        	
			$( this ).dialog( "close" );
		},
		"<?php echo $GLOBALS["Language"]->Abort; ?>": function() {
			$( this ).dialog( "close" );
		}
	}});  	
  }  
  function RenameEntryDialog(entry){  		
	var dialogTitle = "<?php echo $GLOBALS["Language"]->RenameEntryTitle; ?>".replace("%s",entry.DisplayName);  				
	var text = "<?php echo $GLOBALS["Language"]->RenameEntryText; ?>".replace("%s",entry.DisplayName);  	
	$( "<p id='dialogcontent'>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 , modal: true,draggable:false,buttons: {
		"<?php echo $GLOBALS["Language"]->RenameButton; ?>": function() {
			var newName = $("#newname").val();		        	
			if (newName != ""){
				console.log(newName);
				RenameEntry(entry.Id,newName);			        		  			        
				$( this ).dialog( "close" );
				$("#dialogcontent").remove();		      
			}	        	
		},
		"<?php echo $GLOBALS["Language"]->Abort; ?>": function() {
			$( this ).dialog( "close" );
		}
	}});  	
  }  
  function DisplayMoveOrCopy(entry,move){  	
  	if (targets != null){
  		MoveOrCopyFileDialog(entry,move,targets);	
   	}
  }
  function AddContextMenu(entry){
  	$(function(){
	    $.contextMenu({
	        selector: '#'+entry.Hash, 
	        callback: function(key, options) {		         
	           if (key == "rename"){
	           		RenameEntryDialog(entry);
	           }
	           else if (key == "delete"){	           		
	           		if (entry.FilePath == null)
	           			DeleteFolderDialog(entry);
	           		else
	           			DeleteFileDialog(entry);
	           }
	           else if (key == "copy"){
	           		DisplayMoveOrCopy(entry,false);
	           }
	           else if (key == "move"){
	           		DisplayMoveOrCopy(entry,true);
	           }else if (key == "download"){
	           		if (entry.FilePath != null)
	           			window.open('?download&f='+entry.Hash,'_blank');
	           		else
	           			alert("NYI");
	           }else if (key == "open"){
	           		if (entry.FilePath != null)
	           			window.location.href ='?detail&f='+entry.Hash;      			
	           		else{
	           			var directory = currentDir + entry.DisplayName +"/";	   	           					
	           			window.location.href ='?files&d='+directory;
	           		}
	           }else if (key == "openNewTab"){
	           		if (entry.FilePath != null)
	           			window.open('?detail&f='+entry.Hash,'_blank');	           			
	           		else{
	           			var directory = currentDir + entry.DisplayName +"/";           			
	           			window.open('?files&d='+directory,'_blank');	
	           		}	           		
	           }           

	        },
	        items: {	
	        	"open": {name: "<?php echo $GLOBALS["Language"]->OpenEntry; ?>", icon: "fa fa-folder-open-o"}, 
	        	"openNewTab": {name: "<?php echo $GLOBALS["Language"]->OpenEntryNewTab; ?>", icon: "fa fa-folder-open-o"},        
	            "copy": {name: "<?php echo $GLOBALS["Language"]->Copy; ?>", icon: "fa fa-copy"},
	            "move": {name: "<?php echo $GLOBALS["Language"]->Move; ?>", icon: "fa fa-cut"},
	            "delete": {name: "<?php echo $GLOBALS["Language"]->Delete; ?>", icon: "fa fa-recycle"},
	            "rename": {name: "<?php echo $GLOBALS["Language"]->RenameButton; ?>", icon: "fa fa-header",id:"test"},
	            "sep1": "---------",
	            "download": {name: "<?php echo $GLOBALS["Language"]->Download; ?>", icon: "fa fa-download"}
	        }
	    });	    
	});
  }
  function MoveOrCopyFileDialog(entry,move,targets){  	 	
	var currentAbsolutePath = currentDir;  				
	var existingTargetsCount = 0;
	if (entry.FilePath == null){
		currentAbsolutePath = currentDir+entry.DisplayName+"/";


	for (var i = 0; i < targets.length;i++){  	  					
		if (currentAbsolutePath != targets[i] )
			{
				if (entry.ParentID == "-1"){
					if (targets[i] != "/"){
						if (targets[i].indexOf(currentAbsolutePath) !== 0)
							existingTargetsCount++;
					}
						
				}
				else if (targets[i] != currentDir)
				{
					console.log(targets[i]);
					existingTargetsCount++;
				}  							
			}		       			
   	}
   	console.log(existingTargetsCount);		  				
	}
	else{
		var currentAbsolutePath = currentDir+entry.DisplayName;
		for (var i = 0; i < targets.length;i++){  	  	
			if (currentAbsolutePath != targets[i] )
			{
				if (targets[i].indexOf(currentAbsolutePath) !== 0){
					if (entry.ParentID == "-1"){
						if (targets[i] != "/"){	  							
							existingTargetsCount++;
						}	  								
					}
					else if (targets[i] != currentDir)
					{	  						
						existingTargetsCount++;
					}  
				}  	
				else{
					if (entry.FilePath != null && targets[i] != "/" && currentAbsolutePath != targets[i]){  			 							
						existingTargetsCount++;
					}  							
				}											
			}
	}
	}
	if (existingTargetsCount == 0){
		ErrorDialog("24");	
		return ;
	}
	var dialogTitle = (move) ? "<?php echo $GLOBALS["Language"]->MoveEntryTitle; ?>".replace("%s",entry.DisplayName) : "<?php echo $GLOBALS["Language"]->CopyEntryTitle; ?>".replace("%s",entry.DisplayName);  				
	var text = (move) ? "<?php echo $GLOBALS["Language"]->MoveEntryText; ?>".replace("%s",entry.DisplayName) :  "<?php echo $GLOBALS["Language"]->CopyEntryText; ?>".replace("%s",entry.DisplayName);  	  	
	$( "<p>"+text+"</p>" ).dialog({ title: dialogTitle,width: 'auto' , modal: true,draggable:false,buttons: {		
"<?php echo $GLOBALS["Language"]->StartButton; ?>" : function() {	
	var target = $(".target").val();
	if (target != ""){
		if (move)
			MoveEntry(entry,target);
		else
			CopyEntry(entry,target);
		DisplaySpinner();
		$( this ).dialog( "close" );
	}		        
},
"<?php echo $GLOBALS["Language"]->Abort; ?>": function() {
  $( this ).dialog( "close" );
}
}});
	$(".target").empty();
	var currentAbsolutePath = currentDir;  				
	if (entry.FilePath == null)
		currentAbsolutePath = currentDir+entry.DisplayName+"/";  			
	for (var i = 0; i < targets.length;i++){  					
		if (currentAbsolutePath != targets[i] )
		{
			if (targets[i].indexOf(currentAbsolutePath) !== 0){
				if (entry.ParentID == "-1"){
					if (targets[i] != "/"){
						$(".target").append("<option>"+targets[i]+"</option>");	  							
					}	  								
				}
				else if (targets[i] != currentDir)
				{
					$(".target").append("<option>"+targets[i]+"</option>");	  							
				}  
			}  	
			else{
				if (entry.FilePath != null && targets[i] != "/" && currentAbsolutePath != targets[i]){  								
					$(".target").append("<option>"+targets[i]+"</option>");  								
				}  							
			}											
		}
	}
  } 
  //
  function CopyEntry(entry,target){
  	//MoveEntry MoveEntryById
  	var arguments = [];
	arguments.push(entry.Id);
	arguments.push(target);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'CopyEntryById',args: arguments })
		.done(function( data ) {	
			var string = $.parseJSON(data);		
			Init();			
		})
		.fail(function(e) {
		  console.log(e);			 
		  ErrorDialog(e.responseText);	
		});
  }
  function MoveEntry(entry,target){
  	//MoveEntry MoveEntryById
  	var arguments = [];
	arguments.push(entry.Id);
	arguments.push(target);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'MoveEntryById',args: arguments })
		.done(function( data ) {	
			var string = $.parseJSON(data);	
			Init();
		})
		.fail(function(e) {
		  console.log(e);			 
		  ErrorDialog(e.responseText);	
		});
  }
  function RenameEntry(id,newname){
  	var arguments = [];
	arguments.push(id);
	arguments.push(newname);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'RenameEntry',args: arguments })
		.done(function( data ) {	
			var res = $.parseJSON(data);					
			if (res == false)
				ErrorDialog("12");	
			Init();
		})
		.fail(function(e) {
		  console.log(e);			 
		  ErrorDialog(e.responseText);	
		});		
  }
  function StartDeleteFile(entry){  	
  	var arguments = [];
	arguments.push(entry.Id);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetAbsolutePathById',args: arguments })
		.done(function( data ) {	
			var string = $.parseJSON(data);
			Delete(string,false);
		})
		.fail(function(e) {
		  ErrorDialog(e.responseText);	
	});		
  }
  function ErrorDialog(message){
  	
	var dialogTitle = "R2_ERR_"+message;			
	var text ="R2_ERR_"+message;		
	$( "<p>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 ,buttons: {
		"OK": function() {			        
			$( this ).dialog( "close" );
		}
	}});  	  	
  }  
  function DeleteFolderDialog(entry){  
	var dialogTitle = "<?php echo $GLOBALS["Language"]->DeleteFolderTitle; ?>".replace("%s",entry.DisplayName);  				
	var text = "<?php echo $GLOBALS["Language"]->DeleteFolderText; ?>".replace("%s",entry.DisplayName);  	
		$( "<p>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 ,draggable:false, buttons: {
	"<?php echo $GLOBALS["Language"]->DeleteFolderDeleteButton; ?>": function() {
		StartDeleteFolder(entry);
		$( this ).dialog( "close" );
	},
	"<?php echo $GLOBALS["Language"]->Abort; ?>": function() {
	  $( this ).dialog( "close" );
	}
	}}); 
  }  
  function StartDeleteFolder(entry){
  	var arguments = [];
	arguments.push(entry.Id);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetAbsolutePathById',args: arguments })
		.done(function( data ) {	
			var string = $.parseJSON(data);
			Delete(string,true);
		})
		.fail(function(e) {
		  console.log(e);
	});		
  }
  function Delete(path,folder){
  	DisplaySpinner();
  	var arguments = [];
	arguments.push(path);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: (folder) ? 'DeleteDirectory' : 'DeleteFile',args: arguments })
		.done(function( data ) {	
			var string = $.parseJSON(data);						
			Init();
		})
		.fail(function(e) {
		  console.log(e);
	});		
  }  
  /**
		Display Methods.
  **/  
  function DisplayLinksForFolder(id){
  	var arguments = [];
	arguments.push(id);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetAbsolutePathById',args: arguments })
		.done(function( data ) {	  
		 	$("#HrefOf"+id).attr("href","?files&d="+encodeURI($.parseJSON(data)));
		 	$("#DownloadOf"+id).attr("href","?download&d="+$.parseJSON(data));
		}	
	);  
  }
  function GetSizeWithUnit(value,id){
  	var arguments = [];
	arguments.push(value);	
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetCorrectedUnit',args: arguments })
		.done(function( data ) {	  
		 	$("#SizeOf"+id).text($.parseJSON(data));
		}	
	);  
  }  
  
</script>
</table>