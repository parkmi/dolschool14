function NewHttpReq() {
	var httpReq = false;
	try {
		// Firefox, Opera 8.0+, Safari
		httpReq=new XMLHttpRequest();
	}catch (e) {
		// Internet Explorer
		try{
			httpReq=new ActiveXObject("Msxml2.XMLHTTP");
		}catch (e) {
			try{
				httpReq=new ActiveXObject("Microsoft.XMLHTTP");
			}catch (e) {
				return false;
			}
		}
	}
	return httpReq;
}

function DoRequest(httpReq,url,param) {

    // httpReq.open (Method("get","post"), URL(string), Asyncronous(true,false))
    //popupwin(url+"\n"+param);
    httpReq.open("POST", url,false);
    httpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    httpReq.send(param);
    if (httpReq.status == 200) {
        //popupwin(url+"\n"+param+"\n"+httpReq.responseText);
        return httpReq.responseText;
    } else {
        return httpReq.status;
    }
}

function popupwin(content) {
    var op = window.open();
    op.document.open('text/plain');
    op.document.write(content);
    op.document.close();
}