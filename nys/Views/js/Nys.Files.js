/**
	needed jquery extension...
*/
$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}
//https://stackoverflow.com/questions/881510/jquery-sorting-json-by-properties
function sortJSON(data, key, way) {
    return data.sort(function(a, b) {
        var x = a[key]; var y = b[key];
        if (way == 1 ) { return ((x < y) ? -1 : ((x > y) ? 1 : 0)); }
        if (way == -1) { return ((x > y) ? -1 : ((x < y) ? 1 : 0)); }
    });
}
function DisplaySpinner(){  
  	$(".entry").hide();	
  	var opts = {
	    lines: 13, // The number of lines to draw
		length: 0, // The length of each line
		width: 9, // The line thickness
		radius: 27, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 5, // The rotation offset
		direction: 1, // 1: clockwise, -1: counterclockwise
		color: 'black', // #rgb or #rrggbb or array of colors
		speed: 1, // Rounds per second
		trail: 95, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
	   top: '65%', // Top position relative to parent
	   left: '50%' // Left position relative to parent
	};
	var target = document.getElementById('spinner');
	var spinner = new Spinner(opts).spin(target);
  }
  function HideSpinner(){
  	var target = document.getElementById('spinner');
	
	$( document ).ready( function(){
		$("#spinner").html("");
		$(".entry").fadeIn();
	});
  }

  /****************************************************Files*****************************************************/
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
    var inner;
    var files = {};
    if (mime.indexOf("inode") != -1)
      return "<i class=\"fa fa-folder-open-o fa-2x\"></i>";
    //todo implement a better algorithm
    //add the filetypes
    files["image"] = "fa-image";
    files["zip"] = "fa-compress";
    files["audio"] = "fa-audio-circle";
    files["video"] = "fa-video-camera";
    files["plain"] = "fa-header";
    //office filetypes
    files["msword"] = "fa-keyboard-o";
    files["document"] = "fa-keyboard-o";
    files["presentation"] = "fa-line-chart";
    files["pdf"] = "fa-font";
    files["spreadsheetml"] = "fa-table";
    files["src"] ="fa-code";
    files["ms-dos"] ="fa-windows";
    files["sql"] = "fa-database";
    files["html"] = "fa-html5";
    var length = Object.keys(files).length;
    for (var key in files) {
      var value = files[key];
      if (mime.indexOf(key) != -1)
      {
        inner = value;
        break;
      }
    }
    var content = "<span class=\"fa-stack\"><i class=\"fa fa-file-o fa-stack-2x\"></i><i class=\"fa "+inner+" fa-stack-1x\"></i> </span>";
    return content;
  }
  function DisplayContent(data){   	
  		for (var i = 0; i < data.length;i++){  	  			
  			var content = '';  						
  			content = "<tr class='entry' id='"+data[i].Hash+"'><td>";
  			//if (data[i].Thumbnail != true)
  				content += ReturnIcon(data[i].MimeType)+"</td>";
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
   function AppendEntriesToTargetList(entry,currentAbsolutePath){
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
  function DisplayMoveOrCopy(entry,move){  	
  	if (targets != null){
  		MoveOrCopyFileDialog(entry,move,targets);	
   	}
  }
  function CopyEntry(entry,target){
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
  function GetExistingTargetCount(entry){
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
  					existingTargetsCount++;
  				}  							
  			}		       			
  		}	  				
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
  }
