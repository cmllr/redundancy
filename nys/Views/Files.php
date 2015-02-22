<div id="spinner"></div>
<ol class="breadcrumb" id="bc">	
</ol>
<table id='list' class="table table-striped table-hover">
	<tr>
		<th></th>
		<th id="namecolumn"><?php echo $GLOBALS["Language"]->Files_Name;?></th>
		<th class='hidden-xs' id="uploadcolumn"><?php echo $GLOBALS["Language"]->Files_Uploaded;?></th>
		<th class='hidden-xs' id="sizecolumn"><?php echo $GLOBALS["Language"]->Files_Size;?></th>
	</tr>
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
var language = "<?php echo $_SESSION['Language']; ?>";
nys.Init();  	   
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
	           		nys.DisplayMoveOrCopy(entry,false);
	           }
	           else if (key == "move"){
	           		nys.DisplayMoveOrCopy(entry,true);
	           }else if (key == "download"){
	           		if (entry.FilePath != null)
	           			window.open('?download&f='+entry.Hash,'_blank');
	           		else
	           			alert("Not implemented yet. :(");
	           }else if (key == "open"){
	           		if (entry.FilePath != null){
	           			if (entry.DisplayName.indexOf(".zip") == -1)
	           				window.location.href ='?detail&f='+entry.Hash;      		
	           			else{
	           				Extract(entry);
	           			}	
	           		}
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
	           			nys.StartSharingByLink(entry,"<?php echo $GLOBALS["Language"]->ShowShareLink;?>","<?php echo $GLOBALS["Language"]->LinkToShareText; ?>");          			
	           		else{
	           			alert("Not implemented yet. :(");
	           		}	
	           }  
	            else if (key =="shareToUser"){
	           		if (entry.FilePath != null)
	           			nys.StartSharingByLink(entry,"<?php echo $GLOBALS["Language"]->ShowShareLink;?>","<?php echo $GLOBALS["Language"]->LinkToShareText; ?>");          			
	           		else{
	           			alert("Not implemented yet. :(");
	           		}	
	           }        

	        },
	        items: {	
	        	"open": {name: (entry.DisplayName.indexOf(".zip") == -1) ?"<?php echo $GLOBALS["Language"]->OpenEntry; ?>"  :"<?php echo $GLOBALS["Language"]->Unzip; ?>", icon: (entry.DisplayName.indexOf(".zip") == -1) ? "fa fa-folder-open-o" : "fa fa-file-zip-o"}, 
	        	"openNewTab": {name: "<?php echo $GLOBALS["Language"]->OpenEntryNewTab; ?>", icon: "fa fa-folder-open-o"},        
	            "copy": {name: "<?php echo $GLOBALS["Language"]->Copy; ?>", icon: "fa fa-copy"},
	            "move": {name: "<?php echo $GLOBALS["Language"]->Move; ?>", icon: "fa fa-cut"},
	            "delete": {name: "<?php echo $GLOBALS["Language"]->Delete; ?>", icon: "fa fa-recycle"},
	            "rename": {name: "<?php echo $GLOBALS["Language"]->RenameButton; ?>", icon: "fa fa-header"},
	            "rename": {name: "<?php echo $GLOBALS["Language"]->RenameButton; ?>", icon: "fa fa-header"},
	            //This function is disabled until the function gets implemented. "shareToUser": {name: "<?php echo $GLOBALS["Language"]->ShareToUserGeneric; ?>", icon: "fa fa-group"},
	            "shareWithLink": {name: "<?php echo $GLOBALS["Language"]->ShareWithLinkGeneric; ?>", icon: "fa fa-link"},
	            "download": {name: "<?php echo $GLOBALS["Language"]->Download; ?>", icon: "fa fa-download"}
	        }
	    });	    
	});
  }
  function Extract(entry){
  		// UnzipInPlace($hash,$token,$path)
  		var arguments = [];
        arguments.push(entry.Hash);
        arguments.push(token);
        arguments.push(currentDir);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'UnzipInPlace',
            args: arguments
        })
        .done(function(data) {           
            nys.Init();
        })
        .fail(function(e) {
            console.log(e);
            nys.ErrorDialog(e.responseText);
        });
  }
  function MoveOrCopyFileDialog(entry,move,targets){  	 	
  	var currentAbsolutePath = currentDir;  				
  	var existingTargetsCount = nys.GetExistingTargetCount(entry);
  	if (existingTargetsCount == 0){
  		nys.ErrorDialog("24");	
  		return ;
  	}
  	var dialogTitle = (move) ? "<?php echo $GLOBALS["Language"]->MoveEntryTitle; ?>".replace("%s",entry.DisplayName) : "<?php echo $GLOBALS["Language"]->CopyEntryTitle; ?>".replace("%s",entry.DisplayName);  				
  	var text = (move) ? "<?php echo $GLOBALS["Language"]->MoveEntryText; ?>".replace("%s",entry.DisplayName) :  "<?php echo $GLOBALS["Language"]->CopyEntryText; ?>".replace("%s",entry.DisplayName);  	  	
  	$( "<p class='dialogcontent'>"+text+"</p>" ).dialog({ title: dialogTitle,width: 'auto' , modal: true,draggable:false,buttons: 
  	[		
  		{
  			text: "<?php echo $GLOBALS["Language"]->StartButton; ?>",
  			click : function() {	
	  			var target = $(".target").val();
	  			if (target != ""){
	  				//console.log(target);
	  				if (move)
	  					nys.MoveEntry(entry,target);
	  				else
	  					nys.CopyEntry(entry,target);
	  				nys.DisplaySpinner();
	  				$( this ).dialog( "close" );
	  				$('.dialogcontent').each(function() {
					    $(this).remove();
					});
	  			}		        
	  		},
	  		'class': "btn btn-primary"
  		},
  		{
  			text: "<?php echo $GLOBALS["Language"]->Abort; ?>",
	  		click : function() {
	  			$( this ).dialog( "close" );
		  		$('.dialogcontent').each(function() {
				    $(this).remove();
				});
	  		} 
  		}  		
  	]
    });
  	$(".target").empty();
  	var currentAbsolutePath = currentDir;  				
  	if (entry.FilePath == null)
  		currentAbsolutePath = currentDir+entry.DisplayName+"/";  			
  	nys.AppendEntriesToTargetList(entry,currentAbsolutePath)
  }  
 
  function DeleteFolderDialog(entry){  
	var dialogTitle = "<?php echo $GLOBALS["Language"]->DeleteFolderTitle; ?>".replace("%s",entry.DisplayName);  				
	var text = "<?php echo $GLOBALS["Language"]->DeleteFolderText; ?>".replace("%s",entry.DisplayName);  	
		$( "<p class='dialogcontent'>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 ,draggable:false, buttons: 
		[
			{
				text:"<?php echo $GLOBALS["Language"]->DeleteFolderDeleteButton; ?>",
				'class':"btn btn-primary",
				click: function() {
					nys.StartDeleteFolder(entry);
					$( this ).dialog( "close" );
					$('.dialogcontent').each(function() {
					    $(this).remove();
					});					
				}
			},
			{
				text:"<?php echo $GLOBALS["Language"]->Abort; ?>",
				click:function() {
				  $( this ).dialog( "close" );
				  $('.dialogcontent').each(function() {
					    $(this).remove();
				  });
				}
			}
		]
	}); 
  }  
  
  function DeleteFileDialog(entry){  	
	var dialogTitle = "<?php echo $GLOBALS["Language"]->DeleteFileTitle; ?>".replace("%s",entry.DisplayName);  				
	var text = "<?php echo $GLOBALS["Language"]->DeleteFileText; ?>".replace("%s",entry.DisplayName);  	
	$( "<p class='dialogcontent'>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 , modal: true,draggable:false, buttons: 
		[
			{
				text:"<?php echo $GLOBALS["Language"]->DeleteFileDeleteButton; ?>",
				'class':'btn btn-danger',
				click: function() {		        
					nys.StartDeleteFile(entry);		        	
					$( this ).dialog( "close" );
					$('.dialogcontent').each(function() {
					    $(this).remove();
				    });
				}
			},
			{
				text: "<?php echo $GLOBALS["Language"]->Abort; ?>",
				click: function() {
					$( this ).dialog( "close" );
					$('.dialogcontent').each(function() {
					    $(this).remove();
				    });
				}
			}
		]
	});  	
  }  
  function NewDirDialog(){
  	var dialogTitle = "<?php echo $GLOBALS["Language"]->New_Directory; ?>" + " " + currentDir;  				
	var text = "<?php echo $GLOBALS["Language"]->NewDirEntryText; ?>";  
	$("<p class='dialogcontent'>"+text+"</p>").dialog({ title: dialogTitle,width: 350 , modal: true,draggable:false,buttons: 
		[
			{
				text:"<?php echo $GLOBALS["Language"]->New_Directory_Short; ?>",
				click: function() {
					var newName = $("#newname").val();	
					nys.CreateDirectory(newName);
					$( this ).dialog( "close" );
					$('.dialogcontent').each(function() {
				  	  $(this).remove();
			 		});	
				},
				'class':'btn btn-primary'
			},
			{
				text:	"<?php echo $GLOBALS["Language"]->Abort; ?>",
				click: function() {
					$( this ).dialog( "close" );
					$('.dialogcontent').each(function() {
					    $(this).remove();
				    });
				}
			}
		]		
	});  	
  }
  function RenameEntryDialog(entry){  	
	var dialogTitle = "<?php echo $GLOBALS["Language"]->RenameEntryTitle; ?>".replace("%s",entry.DisplayName);  				
	var text = "<?php echo $GLOBALS["Language"]->RenameEntryText; ?>".replace("%s",entry.DisplayName);  
	$("<p class='dialogcontent'>"+text+"</p>").dialog({ title: dialogTitle,width: 350 , modal: true,draggable:false,buttons: 
		[
			{
				text:"<?php echo $GLOBALS["Language"]->RenameButton; ?>",
				click: function() {
					var newName = $("#newname").val();	
					if (newName != ""){					
						nys.RenameEntry(entry.Id,newName);								        		  			        
						$( this ).dialog( "close" );						
						$('.dialogcontent').each(function() {
					  	  $(this).remove();
				 		});		
					}    	
				},
				'class':'btn btn-primary'
			},
			{
				text:	"<?php echo $GLOBALS["Language"]->Abort; ?>",
				click: function() {
					$( this ).dialog( "close" );
					$('.dialogcontent').each(function() {
					    $(this).remove();
				    });
				}
			}
		],
		onClose:function(){
			$(this).dialog("destroy");
			$('.dialogcontent').each(function() {
			  $(this).remove();
			});	
		}
	});  	
  } 
   //Upload drop
	$('#list').on(
	    'dragover',
	    function(e) {
	        e.preventDefault();
	        e.stopPropagation();	        
	    }
	)
	$('#list').on(
	    'drop',
	    function(e) {	              
	        if (nys.Target != -1 && nys.Source != -1){
	        	$("#statusMove").fadeIn();     
	        	e.preventDefault();
	       		e.stopPropagation();	
	        	console.log("will try to move");
	        	var source = nys.Source;
	        	console.log(source);
	        	var target = $("#HrefOf"+nys.Target).attr("href");
	        	var targetRe = /d=(.*)/;
	        	if (targetRe.exec(target) == null){
	        		nys.ErrorDialog("21");
	        		nys.Target = -1;
	             	nys.Source = -1;	
	        		$("#statusMove").fadeOut();   
	        		return;
	        	}
	        	target = decodeURIComponent(targetRe.exec(target)[1]);
	        	console.log(target);	        
		        $.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel',method: 'MoveEntryById',args: [source,target,token]  })
	            .done(function(data) {	 
	              nys.ScrollPosition = $(window).scrollTop(); 
	              nys.Target = -1;
	              nys.Source = -1;	
	              $("#statusMove").fadeOut();             
	              nys.Init();
	            })
	            .fail(function(data){
	            	nys.ErrorDialog(data.responseText);
	            	$("#statusMove").fadeOut();  
	            });
	        }

	   }
	)
	$('#list').on(
	    'dragstart',
	    function(e) {
	       // e.preventDefault();
	        //e.stopPropagation();	       
	        var re = /\d+/;
	        var source = re.exec(e.target.id);

	        if (source !== null)
				nys.Source =source[0];
			else
				nys.Source = -1;   
	    }
	)
	$('#list').on(
	    'dragenter',
	    function(e) {  	    	
	    	if (e.originalEvent.dataTransfer.types.indexOf("Files") !== -1){
	    		DisplayUploadDialog();
	    	}
	    	else{	    		
	    		var re = /\d+/;
				var target = re.exec(e.target.id);
				if (target !== null)
					nys.Target = target[0];	 
				else
					nys.Target = -1;   		
	    	}	    	
	        e.preventDefault();
	        e.stopPropagation();
	    }
	)
	
	function DisplayUploadDialog(){		
		$("#uploadbox").attr("style","");
    	$("#uploadbox").dialog({title:"<?php echo $GLOBALS['Language']->Upload_Title.'</span> '. (isset($_SESSION['currentFolder']) ? $_SESSION['currentFolder'] : "/" );?>",
    		buttons: 
			[					
				{
					text:	"OK",
					click: function() {
						$( this ).dialog( "close" );
						$('.dialogcontent').each(function() {
						    $(this).remove();
					    });					   
					    $(this).dialog("close");
					},
					'class' :"btn btn-primary"
				}
			],
    		onClose:function(){	
    			$( this ).dialog( "close" );
				$('.dialogcontent').each(function() {
				    $(this).remove();
			    });	    			
    			$(this).dialog("close");
			}});
	}
</script>
</table>

