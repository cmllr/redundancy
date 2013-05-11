var links = "<a href = 'index.php?module=delete&file=Name'>test</a>";
function getLinks(Name)
{			
	var toadd = links.replace("Name",Name);		
	if (document.getElementById(Name).innerHTML.indexOf("module=delete") == -1){
		var a = document.createElement('a');			
		var downloadButton = document.createElement('IMG');
		downloadButton.setAttribute('src',"./Images/box_download.png");		
		downloadButton.setAttribute('width',16);
		downloadButton.setAttribute('height',16);
		downloadButton.setAttribute('padding',2);
		downloadButton.style.position = 'absolute';		  
		a.appendChild(downloadButton);	
		a.href = "index.php?module=donwload&file=Name".replace("Name",Name);		
		document.getElementById(Name).appendChild(a);
		
		
		
		
	
	}
}
function removeLinks(Name)
{
	var toadd = links.replace("Name",Name);		
	var myDiv = document.getElementById(Name);
	myDiv.removeChild(myDiv.childNodes[1]);
}
function displayorhide()
{
	
	if (document.getElementById("sidebar").style.visibility == 'visible')
		document.getElementById("sidebar").style.visibility = 'hidden';
	else
		document.getElementById("sidebar").style.visibility = 'visible';
}
function displayorhideWarning()
{
	
	if (document.getElementById("warning").style.visibility == 'visible')
		document.getElementById("warning").style.visibility = 'hidden';
	else
		document.getElementById("warning").style.visibility = 'visible';
}