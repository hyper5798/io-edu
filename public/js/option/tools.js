let gray = "#a3a3a1";
let green = "#9ef81f";
let black = "#000000";
let blue = "#14a3dc";
let brown = "#e3b030";
let red = "#DC143C";
let dark_gray = "#5e5e5b";
let timeoutID = null;
let timeoutAlertID = null;

function showMessage(vueApp, msg, time) {
    if(timeoutID !== null)
        clearTimeout(timeoutID);

    vueApp.message = msg;

    if(time === undefined || time === null)
        time = 3000;

    timeoutID = window.setTimeout(function () {
        vueApp.message = '';
    }, time);
}

function showOverlay() {
    $.LoadingOverlay("show");
    window.setTimeout(function () {
        $.LoadingOverlay("hide");
    }, 1000);
}

function showAlertMessage(msg, time) {
    if(timeoutAlertID !== null)
        clearTimeout(timeoutAlertID);

    app.alertMessage = msg;

    if(time === undefined || time === null)
        time = 3000;

    timeoutAlertID = window.setTimeout(function () {
        app.alertMessage = '';
    }, time);
}

function getLocalTimeString(str) {
    //console.log(str);
    let date = new Date(str);
    return date.toLocaleString();
}

//中文化日期格式
function getTimeString(str) {
    //console.log(str);
    let newStr =  getFormatDateString(str, 1)
    return newStr;
}

function getDateString(str) {
    //console.log(str);
    let newStr =  getFormatDateString(str, 2)
    return newStr;
}

function getDateTimeString(str) {
    //console.log(str);
    let newStr =  getFormatDateString(str, 3)
    return newStr;
}

/* getFormatDateString : 格式化日期
* @param str : 日期字串
* @param tag : 切換標籤 1:顯示月日 2:時間 3:月日&時間
* */
function getFormatDateString(str, tag) {
    //console.log(str);
    let date = new Date(str);
    let year,month,day,h,m,s, dateString;
    if(tag ===1 || tag === 3) {
        year = date.getFullYear() + '';
        month = date.getMonth() + 1;
        day = date.getDate();
        if(month<10)
            month = '0' + month;
        if(day<10)
            day = '0' + day;
    }

    if(tag ===2 || tag === 3) {
        h = date.getHours();
        m = date.getMinutes();
        s = date.getSeconds();
    }

    if(tag === 1) {
        dateString = /*year +'年'+*/ month +'月'+ day+'日';
    } else if(tag === 2) {
        dateString = h+ '時'+ m + '分' +s +'秒';
    } else {
        dateString = /*year +'年'+*/ month +'月'+ day+'日 ';
        dateString = dateString +  h+ '時'+ m + '分' +s +'秒';
    }
    return dateString;
}

//查詢日期格式
function formatDate(date) {
    console.log(typeof date);
    if(typeof date === 'string') {
        date = new Date(date);
    }

    let year,month,day,h,m,s, dateString;

    year = date.getFullYear();
    month = date.getMonth() + 1;
    day = date.getDate();
    if(month<10)
        month = '0' + month;
    if(day<10)
        day = '0' + day;

    h = date.getHours();
    m = date.getMinutes();
    s = date.getSeconds();

    dateString = year +'-'+ month +'-'+ day+' ';

    dateString = dateString+ h+ ':'+ m + ':' +s ;

    return dateString;
}

function addDays(date, days) {
    console.log(typeof date);
    if(typeof date === 'string') {
        date = new Date(date);
    }
    date.setDate(date.getDate() + days);
    return date;
}

function showLoadingOverlay() {
    $.LoadingOverlay("show");
    window.setTimeout(function () {
        $.LoadingOverlay("hide");
    }, 1000);
}
