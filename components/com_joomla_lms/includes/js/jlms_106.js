function getObj(name) {
	if (document.getElementById) { return document.getElementById(name); }
	else if (document.all) { return document.all[name]; }
	else if (document.layers) { return document.layers[name]; }
	else return false;
}
function JLMS_preloadImages() {
	var d=document;
	if (d.images) {
	if (!d.MM_p) d.MM_p=new Array();
	var i,j=d.MM_p.length,a=JLMS_preloadImages.arguments;
	for(i=0; i<a.length; i++) if (a[i].indexOf("#")!=0) { d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];
}}}
function jlms_WStatus(ws_txt) {
	window.status='';//ws_txt;
}
function jlms_writetxt(eid,mes) {
	var jsm = getObj(eid);
	if (jsm) { jsm.innerHTML = mes; return true; }else{ return false; }
}
function KeepAL_AReq(hr_k) {
	if (hr_k.readyState == 4) {
		if ((hr_k.status == 200)) {
			//hr_k.responseText;
			timer_KeepAL = setTimeout("KeepAL_MReq('" + timer_KeepAL_gl_url + "')", 120000);
		}
	}
}
function trim( str ) {
   return str.replace(/^\s+|\s+$/g,"");
}
function KeepAL_MReq(url) {
	var hr_k = false;
	if (window.ActiveXObject) {
		try { hr_k = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try { hr_k = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	} else if (window.XMLHttpRequest) {
		hr_k = new XMLHttpRequest();
		if (hr_k.overrideMimeType) {
			hr_k.overrideMimeType('text/xml');
		}
	}
	if (!hr_k) { return false; }
	hr_k.onreadystatechange = function() { KeepAL_AReq(hr_k); };
	hr_k.open('GET', url, true);
	hr_k.send(null);
}
var timer_KeepAL = false;
var timer_KeepAL_gl_url = '';
function KeepAL_MReq_run(url) {
	if (!timer_KeepAL) {
		timer_KeepAL_gl_url = url;
		timer_KeepAL = setTimeout("KeepAL_MReq('" + url + "')", 120000);// 2 min
	}
}
/*
//temporary commented by DEN;
// setStyles function below makes the message problematic to read on some of the templates
// e.g. 'position:absolute' moved the message below the screen
window.addEvent('domready', function(){
	var redirect_message_el = $$('.message')[0];
	if($defined(redirect_message_el)){
		var old_coord = redirect_message_el.getCoordinates();
		redirect_message_el.setStyles({'position': 'absolute', 'left': 0, 'top': old_coord.top, 'width': old_coord.width, 'height': old_coord.height});
		
		if( typeof(Fx.Styles) != 'undefined' ) {
			var fx = new Fx.Styles(redirect_message_el, {
				duration: 1000,
				transition: Fx.Transitions.linear,
				onComplete: function(){
					redirect_message_el.setStyles({'display': 'none'});
				}
			});
		} else {			
			var fx = new Fx.Tween(redirect_message_el, {
				duration: 1000,
				transition: Fx.Transitions.linear,
				onComplete: function(){
					redirect_message_el.setStyles({'display': 'none'});
				}
			});
		}
		var fx_run = function(){
			fx.start({'opacity': 0});
		}
		fx_run.delay(5000);
	}
});
*/
