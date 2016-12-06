function $m(theVar){
	return document.getElementById(theVar)
}
function remove(theVar){
	var theParent = theVar.parentNode;
	theParent.removeChild(theVar);
}
function addEvent(obj, evType, fn){
	if(obj.addEventListener)
	    obj.addEventListener(evType, fn, true)
	if(obj.attachEvent)
	    obj.attachEvent("on"+evType, fn)
}
function removeEvent(obj, type, fn){
	if(obj.detachEvent){
		obj.detachEvent('on'+type, fn);
	}else{
		obj.removeEventListener(type, fn, false);
	}
}

// browser detection
function isWebKit(){
	return RegExp(" AppleWebKit/").test(navigator.userAgent);
}

// send data
function ajaxUpload(form){
	var detectWebKit = isWebKit();
	var get_url = 'upload.php';// php file
	var div_id = 'upload_area';// div id where to show uploaded image
	var show_loading = '<img src="img/loading.gif" />';// loading image
	var html_error = '<img src="img/error.png" />';// error image

	// create iframe
	var sendForm = document.createElement("iframe");
	sendForm.setAttribute("id","uploadform-temp");
	sendForm.setAttribute("name","uploadform-temp");
	sendForm.setAttribute("width","0");
	sendForm.setAttribute("height","0");
	sendForm.setAttribute("border","0");
	sendForm.setAttribute("style","width: 0; height: 0; border: none;");

	// add to document
	form.parentNode.appendChild(sendForm);
	window.frames['uploadform-temp'].name="uploadform-temp";

	// add event
	var doUpload = function(){
		removeEvent($m('uploadform-temp'),"load", doUpload);
		var cross = "javascript: ";
		cross += "window.parent.$m('"+div_id+"').innerHTML = document.body.innerHTML; void(0);";
		$m(div_id).innerHTML = html_error;
		$m('uploadform-temp').src = cross;
		if(detectWebKit){
        	remove($m('uploadform-temp'));
        }else{
        	setTimeout(function(){ remove($m('uploadform-temp'))}, 250);
        }
    }
	addEvent($m('uploadform-temp'),"load", doUpload);

	// form proprietes
	form.setAttribute("target","uploadform-temp");
	form.setAttribute("action",get_url);
	form.setAttribute("method","post");
	form.setAttribute("enctype","multipart/form-data");
	form.setAttribute("encoding","multipart/form-data");

	// loading
	if(show_loading.length > 0){
		$m(div_id).innerHTML = show_loading;
	}
	// submit
	form.submit();
	formDisable();
  return false;
}

// disable form
  function formDisable() {
    var limit = document.forms[0].elements.length;
    for (i=0;i<limit;i++) {
      document.forms[0].elements[i].disabled = true;
    }
	
  }

// enable form
  function formEnable() {
    var limit = document.forms[0].elements.length;
    for (i=0;i<limit;i++) {
      document.forms[0].elements[i].disabled = false;
    }
  }

// when from is submited
// disable form and after 5sec
//reload captcha, reset form, then enable it
function formDone() {
	formDisable();
setTimeout ( "formEnable()", 5000 );
}