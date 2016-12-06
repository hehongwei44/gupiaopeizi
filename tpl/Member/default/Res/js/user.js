String.prototype.replaceAll = function(stringToFind, stringToReplace) {
	var temp = this;
	var index = temp.indexOf(stringToFind);
	while (index != -1) {
		temp = temp.replace(stringToFind, stringToReplace);
		index = temp.indexOf(stringToFind);
	}
	return temp;
}

/*
 * 增加JavaScript原生String的trim方法。 author: zhengdd date: 2010-3-30
 */
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");
}

/*
 * 增加JavaScript原生String的isBlank方法。 author: zhengdd date: 2010-3-30
 */
String.prototype.isBlank = function() {
	if (this == null) {
		return true;
	}
	var temp = this.trim();
	if (temp == null || temp == "") {
		return true;
	}
	return false;
}

/*
 * 增加JavaScript原生String的isNotBlank方法。 author: zhengdd date: 2010-3-30
 */
String.prototype.isNotBlank = function() {
	if (this == null) {
		return false;
	}
	var temp = this.trim();
	if (temp != null && temp != "") {
		return true;
	}
	return false;
}

function goUrl(url) {
	window.location.href = url;
}

function goBack() {
	window.history.go(-1);
}

// 除法函数，用来得到精确的除法结果
// 说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。
// 调用：accDiv(arg1,arg2)
// 返回值：arg1除以arg2的精确结果
function accDiv(arg1, arg2) {
	var t1 = 0, t2 = 0, r1, r2;
	try {
		t1 = arg1.toString().split(".")[1].length
	} catch (e) {
	}
	try {
		t2 = arg2.toString().split(".")[1].length
	} catch (e) {
	}
	with (Math) {
		r1 = Number(arg1.toString().replace(".", ""))
		r2 = Number(arg2.toString().replace(".", ""))
		return (r1 / r2) * pow(10, t2 - t1);
	}
}

// 乘法函数，用来得到精确的乘法结果
// 说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
// 调用：accMul(arg1,arg2)
// 返回值：arg1乘以arg2的精确结果
function accMul(arg1, arg2) {
	var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
	try {
		m += s1.split(".")[1].length
	} catch (e) {
	}
	try {
		m += s2.split(".")[1].length
	} catch (e) {
	}
	return Number(s1.replace(".", "")) * Number(s2.replace(".", ""))
			/ Math.pow(10, m)
}

// 加法函数，用来得到精确的加法结果
// 说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
// 调用：accAdd(arg1,arg2)
// 返回值：arg1加上arg2的精确结果
function accAdd(arg1, arg2) {
	var r1, r2, m;
	try {
		r1 = arg1.toString().split(".")[1].length
	} catch (e) {
		r1 = 0
	}
	try {
		r2 = arg2.toString().split(".")[1].length
	} catch (e) {
		r2 = 0
	}
	m = Math.pow(10, Math.max(r1, r2));

	return accDiv((accMul(arg1, m) + accMul(arg2, m)), m);
}

// 减法函数
function accSub(arg1, arg2) {
	return accAdd(arg1, -arg2);
}

function myRound(number,fractionDigits){   
    with(Math){   
    	return round(number*pow(10,fractionDigits))/pow(10,fractionDigits);   
    }   
}   

function myRoundCelling(number, saveBit){
	var tempStr = number.toString();
	var pointIndex = tempStr.indexOf(".");
	if(pointIndex == -1){
		return number;
	}
	var shouldBit = pointIndex + 1 + saveBit;
	if(shouldBit >= tempStr.length){
		return number;
	}
	with(Math){
		return accAdd(tempStr.substring(0,shouldBit),accDiv(1,pow(10,saveBit)));
	}
}

function checkMobile(mobile){
	var result = mobile.match(/^1[3|4|5|7|8]\d{9}$/);
    if(result==null)
	    return false;   
    return true;   
}

function checkMail(str){   
   var result=str.match(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/);   
   if(result==null)
	   return false;   
   return true;   
}

function isMoney(str){
	var reg = /^\d{1,12}(\.\d{1,2})?$/;
	return reg.test(str);
}

/**
 * 金额格式化（精确到小数点后两位）
 * @param mnt
 * @returns
 */
function formatMoney(mnt) {
	var amount = (mnt == Math.floor(mnt)) ? mnt + '.00' : ((mnt * 10 == Math
			.floor(mnt * 10)) ? mnt + '0' : mnt);
	
	return commafy(amount);
}

/**
 * 返回一个以元为单位的格式化后的金额（不带小数点，如果有小数点，则丢掉小数点以及后面的内容）
 * @param mnt
 * @returns
 */
function formatYuan(mnt){
	if(mnt == null || mnt == ""){
		return 0;
	}
	return commafy(Math.floor(mnt));
}

function formatMyMoney(mnt){
	return commafy(mnt);
}

function commafy(n) { 
	if(n<1000){
		return n;
	}
	var n = ""+n;
	var re=/\d{1,3}(?=(\d{3})+$)/g; 
	var n1=n.replace(/^(\d+)((\.\d+)?)$/,function(s,s1,s2){
		return s1.replace(re,"$&,")+s2;
	}); 
　        return n1; 
}