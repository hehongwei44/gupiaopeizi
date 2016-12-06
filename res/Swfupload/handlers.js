/**
 * 
 * @version        $Id: handlers.js 1 22:28 2010年7月20日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */

//---事件句并------------------------------
function fileQueueError(file, errorCode, message){
    try {
        var imageName = "error.gif";
		var errorName = "";
		if (errorCode === SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED) {
			errorName = "你添加的文件超过了限制！";
		}

		if (errorName !== "") {
			alert(errorName);
			return;
		}
		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			imageName = "zerobyte.gif";
			break;
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			imageName = "toobig.gif";
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
		case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
			alert('上传数量超过最大限制');
			break;
		default:
			alert(errorCode);
			break;
		}
		//addImage("images/" + imageName, 0);
	} catch (ex) {
		this.debug(ex);
	}

}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesQueued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadProgress(file, bytesLoaded) {
	try {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);
		var progress = new FileProgress(file,  this.customSettings.upload_target);
		progress.setProgress(percent);
		if (percent === 100) {
			progress.setStatus("创建缩略图...");
			progress.toggleCancel(false, this);
		} else {
			progress.setStatus("上传中...");
			progress.toggleCancel(true, this);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function get_thumb_img(str){
	var path = str.split("/");
	var sc = parseInt(path.length);
	path[(sc-1)] = "thumb_"+path[(sc-1)];
	return path.join("/");
}

function uploadSuccess(file, serverData) {
	try {
		var rgx = /\d+\:[^:]+/;
		var progress = new FileProgress(file,  this.customSettings.upload_target);
		if (rgx.test(serverData)) {
			var resarr = serverData.split(":");
			var imgid = resarr[0];
			var imgsrc = resarr[1];
			if(typeof IS_AD !="undefined" && IS_AD == true) var imgurlx = resarr[2];;
			addImage(imgsrc, imgid, imgurlx);
			progress.setStatus("获取缩略图...");
			progress.toggleCancel(false);
		} else {
			addImage("images/error.gif", 0);
			progress.setStatus("有错误！");
			progress.toggleCancel(false);
			alert(serverData);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadComplete(file) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus("所有图片上传完成...");
			progress.toggleCancel(false);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	var imageName =  "error.gif";
	var progress;
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Cancelled");
				progress.toggleCancel(false);
			}
			catch (ex1) {
				this.debug(ex1);
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Stopped");
				progress.toggleCancel(true);
			}
			catch (ex2) {
				this.debug(ex2);
			}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			imageName = "uploadlimit.gif";
			break;
		default:
			alert(message);
			break;
		}

		addImage("images/" + imageName, 0);

	} catch (ex3) {
		this.debug(ex3);
	}

}

var albImg = 0;
function addImage(src, pid,imgurl){
	var newImgDiv = document.createElement("div");
	var delstr = '';
	var iptwidth = 190;
	albImg++;
	if(pid != 0) {
		albImg = 'ok' + pid;
		delstr = '<a href="javascript:;" onclick="javascript:delPic('+pid+')">[删除]</a><a href="javascript:;" onclick="javascript:leftPic('+pid+')">[前移]</a><a href="javascript:;" onclick="javascript:rightPic('+pid+')">[后移]</a>';
	} else {
		albImg = 'err' + albImg;
	}
	newImgDiv.className = 'albCt';
	newImgDiv.id = 'albCt'+albImg;
	document.getElementById("thumbnails").appendChild(newImgDiv);

	if(typeof swf_justimg != 'undefined' && swf_justimg == true){
		newImgDiv.innerHTML = '<img src="'+get_thumb_img(src)+'"/>';
		newImgDiv.innerHTML += '<input type="hidden" name="swfimglist[]" value="'+src+'" />';
	}else{
		newImgDiv.innerHTML = '<img src="'+get_thumb_img(src)+'" width="120" height="120" />'+delstr;
		
		if(typeof arctype != 'undefined' && arctype ==  'article' )
		{ 
			iptwidth = 100;
			if(pid != 0) {
				newImgDiv.innerHTML = '<img src="'+src+'" width="120" onClick="addtoEdit('+pid+')"/>'+delstr;
			}
		}
		newImgDiv.innerHTML += '<div style="margin-top:10px">注释：<input type="text" name="picinfo[]" value="" style="width:'+iptwidth+'px;" /><input type="hidden" name="swfimglist[]" value="'+src+'" /></div>';
		if(typeof IS_AD !="undefined" && IS_AD == true){
		newImgDiv.innerHTML += '<div style="margin-top:10px">地址：<input type="text" name="urlinfo[]" value="" style="width:'+iptwidth+'px;" /></div>';
		}
	}
}


/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {
	this.fileProgressID = "divFileProgress";

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(targetID).appendChild(this.fileProgressWrapper);

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};