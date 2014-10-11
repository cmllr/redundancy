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
	           			alert("Not implemented yet. :(");
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
	           else if (key =="shareWithLink"){
	           		if (entry.FilePath != null)
	           			StartSharingByLink(entry,"<?php echo $GLOBALS["Language"]->ShowShareLink;?>","<?php echo $GLOBALS["Language"]->LinkToShareText; ?>");          			
	           		else{
	           			alert("Not implemented yet. :(");
	           		}	
	           }  
	            else if (key =="shareToUser"){
	           		if (entry.FilePath != null)
	           			StartSharingByLink(entry,"<?php echo $GLOBALS["Language"]->ShowShareLink;?>","<?php echo $GLOBALS["Language"]->LinkToShareText; ?>");          			
	           		else{
	           			alert("Not implemented yet. :(");
	           		}	
	           }        

	        },
	        items: {	
	        	"open": {name: "<?php echo $GLOBALS["Language"]->OpenEntry; ?>", icon: "fa fa-folder-open-o"}, 
	        	"openNewTab": {name: "<?php echo $GLOBALS["Language"]->OpenEntryNewTab; ?>", icon: "fa fa-folder-open-o"},        
	            "copy": {name: "<?php echo $GLOBALS["Language"]->Copy; ?>", icon: "fa fa-copy"},
	            "move": {name: "<?php echo $GLOBALS["Language"]->Move; ?>", icon: "fa fa-cut"},
	            "delete": {name: "<?php echo $GLOBALS["Language"]->Delete; ?>", icon: "fa fa-recycle"},
	            "rename": {name: "<?php echo $GLOBALS["Language"]->RenameButton; ?>", icon: "fa fa-header"},
	            "rename": {name: "<?php echo $GLOBALS["Language"]->RenameButton; ?>", icon: "fa fa-header"},
	            "shareToUser": {name: "<?php echo $GLOBALS["Language"]->ShareToUserGeneric; ?>", icon: "fa fa-group"},
	            "shareWithLink": {name: "<?php echo $GLOBALS["Language"]->ShareWithLinkGeneric; ?>", icon: "fa fa-link"},
	            "download": {name: "<?php echo $GLOBALS["Language"]->Download; ?>", icon: "fa fa-download"}
	        }
	    });	    
	});
  }
  
  function MoveOrCopyFileDialog(entry,move,targets){  	 	
  	var currentAbsolutePath = currentDir;  				
  	var existingTargetsCount = GetExistingTargetCount(entry);
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
  	AppendEntriesToTargetList(entry,currentAbsolutePath)
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
</script>
</table>