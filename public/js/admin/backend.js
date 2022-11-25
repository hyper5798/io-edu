let chart1 = echarts.init(document.getElementById("chart1"));
let chart2 = echarts.init(document.getElementById("chart2"), 'roma');
let chart4 = echarts.init(document.getElementById("chart4"), 'roma');
let url = location.href;
let id = 'yesio' + new Date().getTime();

let fields = type.fields;
let keys = Object.keys(fields);
let values = Object.values(fields);

let option = getGaugeOption1(values[0], reports[reports.length-1][keys[0]]);
let option2 = getGaugeOption2(values[1], reports[reports.length-1][keys[1]]);
let option4 = getLineOption(keys,values, reports);
chart1.setOption(option, true);
chart2.setOption(option2, true);

chart4.setOption(option4);


let macAddr = "fcf5c4536490";
url = url.replace('http', '');
console.log(url);
if(location.protocol=="https:"){
    //url='wss://'+location.hostname+':1880/test';
    url='wss://'+location.hostname+':8080';
} else {
    //url='ws://'+location.hostname+':1880/test';
    url='ws://'+location.hostname+':8080';
}
console.log(url);
let  ws=null;

function wsConn() {
    ws = new WebSocket(url);
    ws.onmessage = function(m) {
        console.log('< from-node-red:',m.data);
        var msg =JSON.parse(m.data);

        //console.log("from-node-red : id:"+msg.id);
        //console.log("v : "+ msg.v);
        let id = msg.id;
        if(id !== macAddr){
            console.log("控制 macAddr:"+ macAddr+ "跟 command : "+id + " 不同");
            return;
        }
        let obj = msg.v;
        let data = obj.data;
        showGauge(data);
        console.log('showLine:');
        console.log(obj);
        showLine(obj);
    }
    ws.onopen = function() {

        let obj = {"id":macAddr,"v":{"cmd":"connection"}};
        let getRequest = JSON.stringify(obj);
        console.log("getRequest type : "+ typeof(getRequest)+" : "+getRequest);
        console.log("ws.onopen : "+ getRequest);
        ws.send(getRequest);      // Request ui status from NR
        console.log(getRequest);

    }

    ws.onclose   = function()  {
        console.log('Node-RED connection closed: '+new Date().toUTCString());
        ws = null;
    }
    ws.onerror  = function(){
        console.log("connection error");
    }
}


function open_fan() {
    let obj = {"id":macAddr,"v":{"cmd":"open"}};
    let getRequest = JSON.stringify(obj);
    ws.send(getRequest )
}

function close_fan() {
    let obj = {"id":macAddr,"v":{"cmd":"close"}};
    let getRequest = JSON.stringify(obj);
    ws.send(getRequest );
}

//console.log(option.series[0]['data'][0]['value']);

function getReportData (report) {
    let reportData = report['data'];
    if(typeof(reportData)=='string')
        reportData = JSON.parse(reportData);
    return reportData;
}

function getLineData(report) {
    let obj = getReportData(report);
    return {
        //name: report['recv'],
        value: [
            report['recv'],
            obj['temperature']
        ]
    };
}






function showGauge(obj){
    option.series[0]['data'][0]['value'] = obj.temperature;
    option2.series[0]['data'][0]['value'] = obj.humidity;
    console.log(option.series[0]['data'][0]);
    chart1.setOption(option, true);
    chart2.setOption(option2, true);
}

function showLine(obj) {
    option4.xAxis.data.push(obj.recv);
    let myData = obj.data
    for(let j=0; j < keys.length; ++j) {
        let key = keys[j];
        option4.series[j].data.push(myData[key]);
    }
    chart4.setOption(option4, true);
}

let open_src = '/Images/fan_open.png';
let close_src = '/Images/fan_close.png';

window.onload=function(){
    let div2=document.getElementById("div2");
    let div1=document.getElementById("div1");
    let div3=document.getElementById("div3");
    let img = document.getElementById("img_fan");
    console.log(img);
    wsConn(); // connect to Node-RED server
    if(div1.className=="close1") {
        div3.innerHTML = '風扇關';
        img.src = close_src;
    } else {
        div3.innerHTML = '風扇開';
        img.src = open_src;
    }
    div2.onclick=function(){
        div1.className=(div1.className=="close1")?"open1":"close1";
        div2.className=(div2.className=="close2")?"open2":"close2";
        if(div1.className=="close1") {
            div3.innerHTML = '風扇關';
            img.src = close_src;
            close_fan();
        } else {
            div3.innerHTML = '風扇開';
            img.src = open_src;
            open_fan();
        }
    }
}
