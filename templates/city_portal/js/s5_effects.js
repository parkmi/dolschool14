function bookmarksite(title,url){
if (window.sidebar) // firefox
 window.sidebar.addPanel(title, url, "");
else if(window.opera && window.print){ // opera
 var elem = document.createElement('a');
 elem.setAttribute('href',url);
 elem.setAttribute('title',title);
 elem.setAttribute('rel','sidebar');
 elem.click();
}
else if(document.all)// ie
 window.external.AddFavorite(url, title);
}


function hide_login() {
	opacity_NA = 1;
	}

function hide_login2() {
	opacity_NA = 0;
	document.getElementById('s5_box_outer').style.display = 'none';
	}


function opacity(id, opacStart, opacEnd, millisec) {
	//speed for each frame
	var speed = Math.round(millisec / 100);
	var timer = 0;
	//determine the direction for the blending, if start and end are the same nothing happens
	if(opacStart > opacEnd) {
		for(i = opacStart; i >= opacEnd; i--) {
			setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
			timer++;
		}
	} else if(opacStart < opacEnd) {
		for(i = opacStart; i <= opacEnd; i++)
			{
			setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
			timer++;
		}
	}
}

//change the opacity for different browsers
function changeOpac(opacity, id) {
	var object = document.getElementById(id).style; 
	object.opacity = (opacity / 100);
	object.MozOpacity = (opacity / 100);
	object.KhtmlOpacity = (opacity / 100);
	object.filter = "alpha(opacity=" + opacity + ")";
}

var opacity_NA = 0;

function shiftOpacity(id) {
	//if an element is invisible, make it visible, else make it ivisible
	if(opacity_NA == 0) {
		document.getElementById('s5_box_outer').style.display = 'block';
		window.setTimeout('hide_login()',100);
	} 
	
	if(opacity_NA == 1) {
		window.setTimeout('hide_login2()',100); 
		opacity_NA = 0;
	}
}





function blendimage(divid, imageid, imagefile, millisec) {
	var speed = Math.round(millisec / 100);
	var timer = 0;
	
	//set the current image as background
	document.getElementById(divid).style.backgroundImage = "url(" + document.getElementById(imageid).src + ")";
	
	//make image transparent
	changeOpac(0, imageid);
	
	//make new image
	document.getElementById(imageid).src = imagefile;

	//fade in image
	for(i = 0; i <= 100; i++) {
		setTimeout("changeOpac(" + i + ",'" + imageid + "')",(timer * speed));
		timer++;
	}
}

function currentOpac(id, opacEnd, millisec) {
	//standard opacity is 100
	var currentOpac = 100;
	
	//if the element has an opacity set, get it
	if(document.getElementById(id).style.opacity < 100) {
		currentOpac = document.getElementById(id).style.opacity * 100;
	}

	//call for the function that changes the opacity
	opacity(id, currentOpac, opacEnd, millisec)
}



