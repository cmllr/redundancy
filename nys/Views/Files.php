<ol class="breadcrumb" id="bc">	
</ol>
<table id='list' class="table table-striped table-hover">
	<tr><th></th><th>Name</th><th class='hidden-xs'>Hochgeladen am</th><th class='hidden-xs'>Größe</th><th class='visible-xs hidden-mg visible-lg'>Aktionen</th></tr>
<script>
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
var currentDir = "<?php echo $_SESSION['currentFolder']; ?>";
if ($.urlParam("d") != null)
	currentDir = unescape($.urlParam("d"));
var token = "<?php echo $_SESSION['Token']; ?>";
Init();
  function Init(){	
  		$(".entry").remove();
  		$("#bc").empty();
		var arguments = [];
		arguments.push(currentDir);
		arguments.push(token);
		$.post( './Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetContent',args: arguments })
			.done(function( data ) {
				DisplayContent($.parseJSON(data));
				DisplayBreadcrumbs(currentDir);
			})
			.fail(function(e) {
				$("#list").remove();
				$(".panel-body").append("<div class='alert alert-danger'>R_ERR_"+e.responseText+"</div>");
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
  function DisplayContent(data){  	  
  		for (var i = 0; i < data.length;i++){  			
  			var content = '';
  			content = "<tr class='entry'><td></td><td><a class='filelink' id='HrefOf"+data[i].Id+"' href=''>"+data[i].DisplayName+"</a></td><td class='hidden-xs'>"+data[i].CreateDateTime+"</td><td class='size hidden-xs' id='SizeOf"+data[i].Id+"'></td>";
  			content +="<td class='visible-xs hidden-mg visible-lg'><a class='fileActions' id='DeleteOf"+data[i].Id+"' href='#'><span class='elusive icon-remove-sign glyphIcon'></span></a>";
  			content +="<a id='MoveOf"+data[i].Id+"' class='fileActions' href=''><span class='elusive icon-tag glyphIcon'></span></a>";
  			content += "<a id='CopyOf"+data[i].Id+"'   class='fileActions' href=''><span class='elusive icon-tags glyphIcon'></span></a>";
  			content += "<a id='RenameOf"+data[i].Id+"' class='fileActions' href='#'><span class='elusive icon-edit glyphIcon'></span></a>";
  			content += "<a  id='DownloadOf"+data[i].Id+"' class='fileActions' href=''><span class='elusive icon-download-alt glyphIcon'></span></a></td></tr>";
  			$('#list').append(content);		
  			GetSizeWithUnit(data[i].SizeInBytes,data[i].Id);
  			
  			if (data[i].FilePath != null){
  				DisplayLinksForFile(data[i]);
  				DeleteFileDialog(data[i]);	
  			}  				
  			else{
  				DisplayLinksForFolder(data[i].Id);
  				DeleteFolderDialog(data[i]);
  			}
  			RenameEntryDialog(data[i]);
  			
  		}  		
  }
  /**
		logic Methods.
  **/
  function DeleteFileDialog(entry){
  		$("#DeleteOf"+entry.Id).click(function(e){
  				e.preventDefault();
  				var dialogTitle = "<?php echo $GLOBALS["Language"]->DeleteFileTitle; ?>".replace("%s",entry.DisplayName);  				
  				var text = "<?php echo $GLOBALS["Language"]->DeleteFileText; ?>".replace("%s",entry.DisplayName);  	
  				$( "<p>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 , modal: true,buttons: {
		        "<?php echo $GLOBALS["Language"]->DeleteFileDeleteButton; ?>": function() {
		        	StartDeleteFile(entry);
		        	$( this ).dialog( "close" );
		        },
		        "<?php echo $GLOBALS["Language"]->Abort; ?>": function() {
		          $( this ).dialog( "close" );
		        }
		      }});
  		});
  }  
  function RenameEntryDialog(entry){
  		$("#RenameOf"+entry.Id).click(function(e){
  				e.preventDefault();
  				var dialogTitle = "<?php echo $GLOBALS["Language"]->RenameEntryTitle; ?>".replace("%s",entry.DisplayName);  				
  				var text = "<?php echo $GLOBALS["Language"]->RenameEntryText; ?>".replace("%s",entry.DisplayName);  	

  				$( "<p>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 , modal: true,buttons: {
		        "<?php echo $GLOBALS["Language"]->RenameButton; ?>": function() {
		        	var newName = $("#newname").val();		        	
		        	if (newName != ""){
		        		RenameEntry(entry.Id,newName);		        
		        		$( this ).dialog( "close" );
		        	}	        	
		        },
		        "<?php echo $GLOBALS["Language"]->Abort; ?>": function() {
		          $( this ).dialog( "close" );
		        }
		      }});
  		});  	
  }  
  function RenameEntry(id,newname){
  	var arguments = [];
	arguments.push(id);
	arguments.push(newname);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'RenameEntry',args: arguments })
		.done(function( data ) {	
			var string = $.parseJSON(data);	
			console.log(string);	
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
		  console.log(e);
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
  		$("#DeleteOf"+entry.Id).click(function(e){
  				e.preventDefault();
  				var dialogTitle = "<?php echo $GLOBALS["Language"]->DeleteFolderTitle; ?>".replace("%s",entry.DisplayName);  				
  				var text = "<?php echo $GLOBALS["Language"]->DeleteFolderText; ?>".replace("%s",entry.DisplayName);  	
  				$( "<p>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 ,buttons: {
		        "<?php echo $GLOBALS["Language"]->DeleteFolderDeleteButton; ?>": function() {
		        	StartDeleteFolder(entry);
		        	$( this ).dialog( "close" );
		        },
		        "<?php echo $GLOBALS["Language"]->Abort; ?>": function() {
		          $( this ).dialog( "close" );
		        }
		      }});
  		});
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
  function DisplayLinksForFile(entry){
  	$("#HrefOf"+entry.Id).attr("href","?file&f="+entry.Hash);
  	//$("#DeleteOf"+entry.Id).attr("href","?delete&f="+entry.Hash); 
  	$("#MoveOf"+entry.Id).attr("href","?move&f="+entry.Hash); 
	$("#CopyOf"+entry.Id).attr("href","?copy&f="+entry.Hash); 
	//$("#RenameOf"+entry.Id).attr("href","?rename&f="+entry.Hash); 
	$("#DownloadOf"+entry.Id).attr("href","?download&f="+entry.Hash); 
  }
  function DisplayLinksForFolder(id){
  	var arguments = [];
	arguments.push(id);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetAbsolutePathById',args: arguments })
		.done(function( data ) {	  
		 	$("#HrefOf"+id).attr("href","?files&d="+$.parseJSON(data));
		 	$("#DeleteOf"+id).attr("href","?delete&d="+$.parseJSON(data));
		 	$("#MoveOf"+id).attr("href","?move&d="+$.parseJSON(data));
		 	$("#CopyOf"+id).attr("href","?copy&d="+$.parseJSON(data));
		 	//$("#RenameOf"+id).attr("href","?rename&d="+$.parseJSON(data));
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