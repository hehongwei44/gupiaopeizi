/** 
* 身份证15位编码规则：dddddd yymmdd xx p  
* dddddd：地区码  
* yymmdd: 出生年月日  
* xx: 顺序类编码，无法确定  
* p: 性别，奇数为男，偶数为女 
* <p /> 
* 身份证18位编码规则：dddddd yyyymmdd xxx y  
* dddddd：地区码  
* yyyymmdd: 出生年月日  
* xxx:顺序类编码，无法确定，奇数为男，偶数为女  
* y: 校验码，该位数值可通过前17位计算获得 
* <p /> 
* 18位号码加权因子为(从右到左) Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2,1 ] 
* 验证位 Y = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ]  
* 校验位计算公式：Y_P = mod( ∑(Ai×Wi),11 )  
* i为身份证号码从右往左数的 2...18 位; Y_P为脚丫校验码所在校验码数组位置 
*  
*/ 
 
var Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];// 加权因子  
var ValideCode = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ];// 身份证验证位值.10代表X  
function IdCardValidate(idCard) {  
    idCard = trim(idCard.replace(/ /g, ""));  
    if (idCard.length == 15) {  
        return isValidityBrithBy15IdCard(idCard);  
    } else if (idCard.length == 18) {  
        var a_idCard = idCard.split("");// 得到身份证数组  
        if(isValidityBrithBy18IdCard(idCard)&&isTrueValidateCodeBy18IdCard(a_idCard)){  
            return true;  
        }else {  
            return false;  
        }  
    } else {  
        return false;  
    }  
} 

/** 
* 判断身份证号码为18位时最后的验证位是否正确 
* @param a_idCard 身份证号码数组 
* @return 
*/ 
function isTrueValidateCodeBy18IdCard(a_idCard) {  
    var sum = 0; // 声明加权求和变量  
    if (a_idCard[17].toLowerCase() == 'x') {  
        a_idCard[17] = 10;// 将最后位为x的验证码替换为10方便后续操作  
    }  
    for ( var i = 0; i < 17; i++) {  
        sum += Wi[i] * a_idCard[i];// 加权求和  
    }  
    valCodePosition = sum % 11;// 得到验证码所位置  
    if (a_idCard[17] == ValideCode[valCodePosition]) {  
        return true;  
    } else {  
        return false;  
    }  
}  
/** 
* 通过身份证判断是男是女 
* @param idCard 15/18位身份证号码  
* @return 'female'-女、'male'-男 
*/ 
function maleOrFemalByIdCard(idCard){  
    idCard = trim(idCard.replace(/ /g, ""));// 对身份证号码做处理。包括字符间有空格。  
    if(idCard.length==15){  
        if(idCard.substring(14,15)%2==0){  
            return 'female';  
        }else{  
            return 'male';  
        }  
    }else if(idCard.length ==18){  
        if(idCard.substring(14,17)%2==0){  
            return 'female';  
        }else{  
            return 'male';  
        }  
    }else{  
        return null;  
    }  
    //  可对传入字符直接当作数组来处理  
    // if(idCard.length==15){  
    // alert(idCard[13]);  
    // if(idCard[13]%2==0){  
    // return 'female';  
    // }else{  
    // return 'male';  
    // }  
    // }else if(idCard.length==18){  
    // alert(idCard[16]);  
    // if(idCard[16]%2==0){  
    // return 'female';  
    // }else{  
    // return 'male';  
    // }  
    // }else{  
    // return null;  
    // }  
}  
/** 
* 验证18位数身份证号码中的生日是否是有效生日 
* @param idCard 18位书身份证字符串 
* @return 
*/ 
function isValidityBrithBy18IdCard(idCard18){  
    var year =  idCard18.substring(6,10);  
    var month = idCard18.substring(10,12);  
    var day = idCard18.substring(12,14);  
    var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));  
    // 这里用getFullYear()获取年份，避免千年虫问题  
    if(temp_date.getFullYear()!=parseFloat(year)  
          ||temp_date.getMonth()!=parseFloat(month)-1  
          ||temp_date.getDate()!=parseFloat(day)){  
            return false;  
    }else{  
        return true;  
    }  
}  
/** 
* 验证15位数身份证号码中的生日是否是有效生日 
* @param idCard15 15位书身份证字符串 
* @return 
*/ 
function isValidityBrithBy15IdCard(idCard15){  
    var year =  idCard15.substring(6,8);  
    var month = idCard15.substring(8,10);  
    var day = idCard15.substring(10,12);  
    var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));  
    // 对于老身份证中的你年龄则不需考虑千年虫问题而使用getYear()方法  
    if(temp_date.getYear()!=parseFloat(year)  
          ||temp_date.getMonth()!=parseFloat(month)-1  
          ||temp_date.getDate()!=parseFloat(day)){  
            return false;  
    }else{  
        return true;  
    }  
}

//去掉字符串头尾空格  
function trim(str) {  
    return str.replace(/(^\s*)|(\s*$)/g, "");  
} 


function setInfo(idCard,sexId,birhId,provinceId,ageId){
    var birthdayValue;
    var val =trim(idCard);
    var province = {11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
    if(15==val.length){ //15位身份证号码
        birthdayValue = val.charAt(6)+val.charAt(7);
        if(parseInt(birthdayValue)<10){
            birthdayValue = '20'+birthdayValue;
        }else{
            birthdayValue = '19'+birthdayValue;
        }
        birthdayValue=birthdayValue+'-'+val.charAt(8)+val.charAt(9)+'-'+val.charAt(10)+val.charAt(11);
        if(parseInt(val.charAt(14)/2)*2!=val.charAt(14)){
            document.getElementById(sexId).value='男';
        }else{
            document.getElementById(sexId).value='女';
        }
        document.getElementById(birhId).value=birthdayValue;
    }
    if(18==val.length){ //18位身份证号码
        birthdayValue=val.charAt(6)+val.charAt(7)+val.charAt(8)+val.charAt(9)+'-'+val.charAt(10)+val.charAt(11)  +'-'+val.charAt(12)+val.charAt(13);
        if(parseInt(val.charAt(16)/2)*2!=val.charAt(16)){
            document.getElementById(sexId).value='男';
        }else{
            document.getElementById(sexId).value='女';
        }
        document.getElementById(birhId).value=birthdayValue;
    }
    document.getElementById(provinceId).value=province[idCard.substr(0,2)];

    var r = birthdayValue.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/);     
    if(r==null){
        return false;
    }   
    var d= new Date(r[1], r[3]-1, r[4]);
    if(d.getFullYear()==r[1]&&(d.getMonth()+1)==r[3]&&d.getDate()==r[4]){   
        var Y = new Date().getFullYear();
        document.getElementById(ageId).value = Y-r[1];
    } 
    
}
// 18位身份证号最后一位校验
function IDCard(Num){
    if (Num.length!=18){
        return false;
    }
    var x=0;
    var y='';
    for(i=18;i>=2;i--){
        x = x + (square(2,(i-1))%11)*parseInt(Num.charAt(19-i-1));
    }        
    x%=11;
    y=12-x;
    if (x==0){
        y='1';
    }        
    if (x==1){
        y='0';
    }        
    if (x==2){
        y='X';
    }        
    return y;
}

// 求得x的y次方
function square(x,y){
    var i=1;
    for (j=1;j<=y;j++){
        i*=x;
    }    
    return i;
}