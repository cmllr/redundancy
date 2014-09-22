function StartFolderDownload(entry,dialogTitle,dialogText){
	var arguments = [];
	arguments.push(entry.Id);
	arguments.push(token);
	arguments.push(entry.Id);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'StartZipCreation',args: arguments })
		.done(function( data ) {
			var dir= $.parseJSON(data);	
			window.location.href ='?zipfolder&d='+dir;
		})
		.fail(function(e) {
		  console.log(e);			 
		  ErrorDialog(e.responseText);	
		}
	);
}

function StartSharingByLink(entry,dialogTitle,dialogText){
	//,'ShareToUser',json_encode(array("/PerfTests/",84,$_SESSION['Token'])));
	var arguments = [];
	arguments.push(entry.Id);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetAbsolutePathById',args: arguments })
		.done(function( data ) {	
			ShareByLink($.parseJSON(data),dialogTitle,dialogText);
		})
		.fail(function(e) {
		  console.log(e);			 
		  ErrorDialog(e.responseText);	
		}
	);
}
function ShareByLink(absolutepath,dialogTitle,dialogText){
	var arguments = [];
	arguments.push(absolutepath);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.SharingKernel', method: 'ShareByCode',args: arguments })
		.done(function( data ) {	
			var link = $.parseJSON(data);
			var code = window.location.origin+window.location.pathname+"?share&c="+link;
			var text = dialogText.replace("%s",code);  
			DisplayShareLink(text,dialogTitle);
		})
		.fail(function(e) {
		  console.log(e);			 
		  ErrorDialog(e.responseText);	
		}
	);
}
function StartSharingByLink(entry,dialogTitle,dialogText){
	//,'ShareToUser',json_encode(array("/PerfTests/",84,$_SESSION['Token'])));
	var arguments = [];
	arguments.push(entry.Id);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.FileSystemKernel', method: 'GetAbsolutePathById',args: arguments })
		.done(function( data ) {	
			ShareByLink($.parseJSON(data),dialogTitle,dialogText);
		})
		.fail(function(e) {
		  console.log(e);			 
		  ErrorDialog(e.responseText);	
		}
	);
}
function ShareByLink(absolutepath,dialogTitle,dialogText){
	var arguments = [];
	arguments.push(absolutepath);
	arguments.push(token);
	$.post('./Includes/api.inc.php', { module: 'Kernel.SharingKernel', method: 'ShareByCode',args: arguments })
		.done(function( data ) {	
			var link = $.parseJSON(data);
			var code = window.location.origin+window.location.pathname+"?share&c="+link;
			var text = dialogText.replace("%s",code);  
			DisplayShareLink(text,dialogTitle);
		})
		.fail(function(e) {
		  console.log(e);			 
		  ErrorDialog(e.responseText);	
		}
	);
}


function DisplayShareLink(text,dialogTitle){  	
		$( "<p>"+text+"</p>" ).dialog({ title: dialogTitle,width: 350 , modal: true,draggable:false, buttons: {
		"OK": function() {	      	
			$( this ).dialog( "close" );
			$(this).remove();
		}}});
}  