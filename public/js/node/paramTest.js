let table;
let map;
let a = -1;
let max = 4;
let arr = [];
let check2 = [];
let reportCount = 0;
let reports = [];
let infowindow2 = [];
let playerList = [];
let p = 0;

let flightPath = null;
let icon;
let emptyLatLng = {'lat': 0,'lng': 0};
let emptyCenter = {'name': '','lat': 24.001556,'lng': 121.637998, 'room':18};
let isOpen = false;//true:定位即顯示info window, false:定位不立即顯示
let device_mac = '';
let empty1, empty2, empty3;
let emptyObj = {};
let checkResetAck = null;

function setResetCheck() {
    app.isReset = true;
    checkResetAck = window.setTimeout(function () {
        if(app.isReset  === true) {
            app.isCheckHome = false;
            alert('設定無人船重置失敗!');
        }
    }, 5000);
}

function cancelResetCheck() {
    app.isReset = false;
    window.clearTimeout(checkResetAck);
}

function initKeyArray() {
    let key = null;
    for(let i=0;i<8;i++) {
        key = "key"+(i+1);
        emptyObj[key] = '';
    }
}

let btnEmptySetting = {
    app_id:0,
    field:'btn',
    set: []
};

let myBtnSetting = JSON.parse(JSON.stringify(btnEmptySetting));
if(btn_setting !== null) {
    myBtnSetting = JSON.parse(JSON.stringify(btn_setting));
}

let empty9 = {
    'prediction': 0
};

function getCurrentMac() {
    for(let n=0; n<devices.length; n++) {
        let device =  devices[n];
        if( device.id === device_id) {
            device_mac = device.macAddr;
        }
    }
}

//初始化電子圍籬定位點
function initPointList() {
    for(let n=0; n<max; n++) {
        let newOj =  JSON.parse(JSON.stringify(emptyLatLng));
        arr.push(newOj);
    }
}

let status_1,status_2, status_3, status_9, statusTarget;
let label_1, label_2, label_3, labelTarget;

function initReports() {
    statusTarget = {};
    labelTarget = {};
    for(let i=0;i<apps.length;i++) {
        let app = apps[i];
        let label = app.key_label;
        let keys = Object.keys(app.key_label);
        let obj = {};
        for(let j=0; j<keys.length;j++) {
            let key = keys[j];
            obj[key] = 0;
        }
        statusTarget[app.id] = app.sequence;
        labelTarget[app.sequence] = app.key_label;

        app.status = statusObj.hasOwnProperty(app.sequence) ? statusObj[app.sequence] : JSON.parse(JSON.stringify(obj));
    }
}

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};

$(document).ready(function() {


    $('#timeselector input').on("change", function() {
        app.changeTab(parseInt(this.id));
    });
} );

function initialize() {
    icon = {
        url: point_url, // url
        scaledSize: new google.maps.Size(4, 4), // scaled size
        origin: new google.maps.Point(0,0), // origin
        anchor: new google.maps.Point(2,2) // anchor
    };
    if(center == null) {
        center = JSON.parse(JSON.stringify(emptyCenter));
    }

    delete center.name;
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: center.room,
        center: center,
        panControl:true,
        draggableCursor: 'default'
        //mapTypeId:google.maps.MapTypeId.HYBRID
    });
}

function placeReport(location, isOpenWindow) {
    let num = reportCount;
    check2.splice(num,1,1);
    let loc = new google.maps.LatLng(location.lat, location.lng);
    reports[num] = new google.maps.Marker({
        position: loc,
        map: map,
        icon: icon
    });
    if(typeof(location.lat) === 'string') {
        location.lat = parseFloat(location.lat);
    }
    if(typeof(location.lng) === 'string') {
        location.lng = parseFloat(location.lng);
    }
    let content = '緯度: ' + getFixNumber(location.lat )+
        '<br>經度: ' + getFixNumber(location.lng)
    if(location.hasOwnProperty('recv')) {
        content = content+'<br>時間: ' + getTime(location.recv);
    }

    infowindow2[num] = new google.maps.InfoWindow({
        content: content
    });

    if(isOpenWindow) {
        infowindow2[num].open(map, reports[num]);
    }

    reports[num].addListener('click',function(){
        check2[num] = check2[num] * -1;
        if(check2[num] < 0){
            infowindow2[num].open(map, reports[num]);
        }else{
            infowindow2[num].close();
        }
    });

    reportCount++;
}

function clearReports() {

    for (let i = 0; i < reports.length; i++) {
        reports[i].setMap(null);
    }
    check2 = [];
    reports = [];
    infowindow2 = [];
    reportCount = 0;
}

function getFixNumber(num) {
    return parseFloat(num.toFixed(6));
}

function getTime(obj) {
    let time = new Date(obj);
    let h = time.getHours();
    let m = time.getMinutes();
    let s = time.getSeconds();
    return h+ '時:'+ m + '分:' +s +'秒'
}

function getBtnList() {
    let arr = [];
    let len = 9;
    for(let i=0;i<len;i++) {
        let tmp={'id':0, 'name': '控制'+(i+1), 'url': ''};
        arr.splice(i,1, tmp);
    }
    return arr;
}
let btnArray = getBtnList();

if(myBtnSetting.set !== null && myBtnSetting.set.length>0) {
    btnArray = JSON.parse(JSON.stringify(myBtnSetting.set));
}

function getParam() {
    let tmpCmd = '';
    for(let i=0;i<8;i++) {
        let tmpKey = 'key'+(i+1);
        if(app.keyObj[tmpKey] !=='') {
            let tmpItem = '&'+tmpKey+'='+ parseFloat(app.keyObj[tmpKey]);
            tmpCmd += tmpItem;
        }
    }
    return tmpCmd;
}

initKeyArray();
initPointList();
getCurrentMac();
initReports();

let app = new Vue({
    el: '#app',
    data: {
        tab: 1,
        list:  arr,
        centerTab: 1,
        backupList: null,
        center: JSON.parse(JSON.stringify(emptyCenter)),
        btnList: JSON.parse(JSON.stringify(btnArray)),
        myBtn: JSON.parse(JSON.stringify(btnArray[0])),
        btnIndex: 0,
        commandList: commands,
        btnSetting : myBtnSetting,
        setting: myBtnSetting,
        cmdMessage: '',
        isSendCmd: false,
        isReset: false,
        appList: apps,
        keyObj: JSON.parse(JSON.stringify(emptyObj)),
        param: '',
        newCommand: ''
    },
    mounted() {
        window.setTimeout(function () {
            initialize();
        }, 1000);

    },
    watch: {
        'keyObj.key1' : function(value) {
            this.param = getParam();
        },
        'keyObj.key2' : function(value) {
            this.param = getParam();
        },
        'keyObj.key3' : function(value) {
            this.param = getParam();
        },
        'keyObj.key4' : function(value) {
            this.param = getParam();
        },
        'keyObj.key5' : function(value) {
            this.param = getParam();
        },
        'keyObj.key6' : function(value) {
            this.param = getParam();
        },
        'keyObj.key7' : function(value) {
            this.param = getParam();
        },
        'keyObj.key8' : function(value) {
            this.param = getParam();
        },
    },
    methods: {
        changeTab(value) {
            this.tab = value;
        },
        addBtn() {
            let tmp={'id':0, 'name': '控制'+(this.btnList.length+1), 'url': ''};
            this.btnList.splice(this.btnList.length, 1, tmp);
            this.btnIndex = this.btnList.length-1;
            this.myBtn = this.btnList[this.btnIndex]
        },
        delBtn() {
            this.btnList.splice(this.btnIndex, 1);
            if(this.btnIndex > (this.btnList.length-1)) {
                this.btnIndex = this.btnList.length-1;
            }
            this.myBtn = JSON.parse(JSON.stringify(this.btnList[this.btnIndex])) ;
        },
        toBtnSetting() {
            this.btnSetting.set = JSON.stringify(this.btnList);
            this.setting = JSON.parse(JSON.stringify(this.btnSetting));
            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editSetting').submit();
            }, 500);
        },
        toCmd(inx) {
            //alert(typeof inx);

            this.cmdMessage = '';
            this.isSendCmd = true;
            //$.LoadingOverlay("show");
            let cmd = this.btnList[inx];
            app.newCommand = cmd.url+this.param;

            $.ajax({
                url: app.newCommand,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
                },
                success: function (result) {
                    //$.LoadingOverlay("hide");
                    window.setTimeout(function () {
                        if(result.code == 200){//可以完成設定程序
                            app.cmdMessage =cmd.name+'完成命令';
                            app.isSendCmd = false;
                        }
                    }, 500);


                    window.setTimeout(function () {
                        app.cmdMessage = '';
                    }, 5000);

                },
                error:function(err){
                    //$.LoadingOverlay("hide");
                    app.cmdMessage = err;
                    app.isSendCmd = false;
                },
            });
        },
        cancelBtnSetting() {
            this.btnIndex = 0;
            this.btnList = JSON.parse(JSON.stringify(btnArray));
            this.myBtn = this.btnList[this.btnIndex];
        },
        editBtn() {
            this.setting = JSON.parse(JSON.stringify(this.btnSetting));
            $('#myModal').modal('show');
        },
        onChangeBtn(event) {
            //alert(event.target.value);
            this.btnIndex = parseInt(event.target.value);
            this.myBtn = this.btnList[this.btnIndex];
        },
        onChangeCommand(event) {
            let cmd_id = 0;
            if(typeof(event.target.value) === 'string') {
                cmd_id = parseInt(event.target.value)
            }
            for (let i = 0; i < this.commandList.length; i++) {
                let cmd = this.commandList[i];
                if(cmd.id === cmd_id) {
                    this.myBtn.name = cmd.cmd_name;
                    this.myBtn.id = cmd.id;
                    this.myBtn.url = app_url+'/send_control?command='+cmd.command;
                }
            }
            this.btnList[this.btnIndex] = JSON.parse(JSON.stringify(this.myBtn));
        },
        clearParam() {
            this.keyObj= JSON.parse(JSON.stringify(emptyObj));
        },
        toReset() {
            socket.emit('map', {"mac":device_mac, "reset":true});
            setResetCheck();
        }
    }
});

const socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
// 2
socket.on('connect', function()  {
    //socket.emit('web','Web socket is ready');
    socket.emit('storeClientInfo', { customId:device_mac });
});

socket.on('disconnect', function()  {
    console.log('web disconnect id is:'+socket.id);
    if (socket.connected === false ) {
        //socket.close()
        socket.open();
    }
});

socket.on('news', function(m) {
    console.log(m.hello);
});

socket.on('http_report_data', function(m) {
    console.log('From server ---------------------------------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    console.log(m);
    if(m.macAddr !== device_mac) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    let target = 0;
    if(m.hasOwnProperty('app_id')) {
        target = statusTarget[m.app_id];
    }

    if(target ===1) {
        m.lat = parseFloat(m.lat);
        m.lng = parseFloat(m.lng);

        if(reportCount>0) {
            let number = reportCount-1;
            infowindow2[number].close(number);
        }
        placeReport(m, true);
    }

    for (let i = 0; i < app.appList.length; i++) {
        let mApp = app.appList[i];
        if(mApp.id === m.app_id) {
            mApp.status = m;
        }
    }
});

socket.on('usv_update_mqtt_ul', function(m) {
    console.log('From server ---------------------------------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    console.log(m);
    if(m.macAddr !== device_mac) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    let target = 0;
    if(m.hasOwnProperty('app_id')) {
        target = statusTarget[m.app_id];
    }

    if(target ===1) {
        m.lat = parseFloat(m.lat);
        m.lng = parseFloat(m.lng);

        if(reportCount>0) {
            let number = reportCount-1;
            infowindow2[number].close(number);
        }
        placeReport(m, true);
    }

    for (let i = 0; i < app.appList.length; i++) {
        let mApp = app.appList[i];
        if(mApp.id === m.app_id) {
            mApp.status = m;
        }
    }
});

socket.on('usv_specified_target', function(m) {
    console.log('From server : usv_specified_target ---------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    console.log(m);
    if(m.macAddr !== device_mac) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }

    window.setTimeout(function () {
        if(m.key === 22) {
            cancelResetCheck();
            window.setTimeout(function () {
                alert('無人船已重置!');
            }, 500);
        }
    }, 500);
});
