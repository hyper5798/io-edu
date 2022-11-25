let table;

let map;
let a = -1;
let max = 4;//區塊定位點最大數量
let dangerBlocks = [];//以設定的危險區塊列表
let reportMax = 3000;//累加最高值
let reportShowMax = 500;//初始顯示最高值
let centerMax = 3;
let dangerBlockMax = 4;
//let obj = {title:'上報',max:1000, lastNum:0};
let reportObj = {max:reportMax, lastNum:0};//上報點
let measureObj = {max:2, lastNum:0};//測量點
//let placeObj = {max:4, lastNum:0};//定位點
//設定危險區塊物件
let blockObj = {placeObj : {title:'定點',max:max, lastNum:0, isShow:true}, newBlock: null};
let arr = [];
let oldBlock = null;
let flightPath = null;
let icon, icon2;
let emptyLatLng = {'lat': 0,'lng': 0};
let emptyCenter = {'name': '','lat': 24.001556,'lng': 121.637998, 'room':18};
let emptyDanger = {"id":0, "name":"","room_id":room_id , "field": "danger","set": []};
let isOpen = false;//true:定位即顯示info window, false:定位不立即顯示
let isBlock = false;//更新定位點是否重新取得區塊
let isDebug = false;//是否重新排列
let isFilter = true;//是否濾掉圍籬外上報點
let isShowFence = false;//是否顯示電子圍籬
let device_mac = device.macAddr;
let offset = 3;//公尺
let myarray = [];
let reduce = 0;
let empty1, empty2, empty3;
let lastNum = 0;
let checkList = [];
let transferName = {};
let emptyData = {
    lat: '',
    lng:'',
    recv: ''
};

let tmpSearchArr = [];
//tmp.push(emptyData);

let emptySetting = {
    app_id:0,
    device_id:device.id,
    field:'center',
    set: []
};


let myCenter = emptyCenter;
let mySetting = (setting == null) ? JSON.parse(JSON.stringify(emptySetting)) : JSON.parse(JSON.stringify(setting));

if(mySetting.set.length>0){
    if(center_index > (mySetting.set.length-1)) {
        center_index = mySetting.set.length-1;
    }
    if(center_index !== null) {
        myCenter = mySetting.set[center_index];
    }
    if(typeof(myCenter.lat) === 'string') {
        myCenter.lat = parseFloat(myCenter.lat);
    }
    if(typeof(myCenter.lng) === 'string') {
        myCenter.lng = parseFloat(myCenter.lng);
    }
}


let empty9 = {
    'prediction': 0
};

//初始化電子圍籬定位點
function initPointList() {
    for(let n=0; n<max; n++) {
        let newOj =  JSON.parse(JSON.stringify(emptyLatLng));
        arr.push(newOj);
    }

    for(let i=0; i<devices.length;i++) {
        let device = devices[i];
        checkList.push(device.macAddr);
        transferName[device.macAddr] = device.device_name;
    }
}

let labelObj, statusTarget;

function initReports() {
    statusTarget = {};
    labelObj = {};
    for(let i=0;i<apps.length;i++) {
        let app = apps[i];
        let keys = Object.keys(app.key_label);
        let obj = {};
        for(let j=0; j<keys.length;j++) {
            let key = keys[j];
            obj[key] = 0;
        }
        //statusTarget: 用app.id取得sequence
        statusTarget[app.id] = app.sequence;
        labelObj[app.sequence] = app.key_label;

        if(statusObj[app.sequence] === null) {
            statusObj[app.sequence] = JSON.parse(JSON.stringify(obj));
        } else {
            if(app.sequence === 1) {
                let tmpObj = statusObj[app.sequence];
                let newArray = [];
                let len = (reportShowMax>tmpObj.length) ? tmpObj.length : reportMax;
                if(Array.isArray(tmpObj)) {
                    if(tmpObj.length>0) {
                        for(let i=0;i<len;i++) {
                            let tmpData = tmpObj[i];
                            if(tmpData.hasOwnProperty('lat')) {
                                tmpData.lat =  parseFloat(parseFloat(tmpData.lat).toFixed(6));
                            }
                            if(tmpData.hasOwnProperty('lng')) {
                                tmpData.lng =  parseFloat(parseFloat(tmpData.lng).toFixed(6));
                            }
                            if(tmpData.data === null) {
                                tmpData.data = baseBg;
                            }
                            tmpData.device_name = transferName[tmpData.macAddr];
                            //tmpData.data = dangerBg;
                            newArray.splice(newArray.length, 1, tmpData);
                        }
                    }

                    tmpSearchArr = JSON.parse(JSON.stringify(newArray));
                } else {
                    if(tmpObj.hasOwnProperty('lat')) {
                        tmpObj.lat =  parseFloat(tmpObj.lat).toFixed(6)
                    }
                    if(tmpObj.hasOwnProperty('lng')) {
                        tmpObj.lng =  parseFloat(tmpObj.lng).toFixed(6)
                    }
                    tmpSearchArr.splice(tmpSearchArr.lenth,1, tmpObj)
                }
            }
        }
    }
}

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};

$(document).ready(function() {

    $('#timeselector input').on("change", function() {
        app.changeTab(parseInt(this.id));
    });

    //initialize();
} );

function initialize() {
    icon = {
        url: point_url, // url
        scaledSize: new google.maps.Size(4, 4), // scaled size
        origin: new google.maps.Point(0,0), // origin
        anchor: new google.maps.Point(2,2) // anchor
    };
    icon2= {
        url: diamond_url, // url
        scaledSize: new google.maps.Size(16, 20), // scaled size
        origin: new google.maps.Point(0,0), // origin
        anchor: new google.maps.Point(8,20) // anchor
    };
    let center = JSON.parse(JSON.stringify(myCenter));
    delete center.name;
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: myCenter.room,
        center: myCenter,
        panControl:true,
        draggableCursor: 'default'
        //mapTypeId:google.maps.MapTypeId.HYBRID
    });

    google.maps.event.addListener(map, 'click', function(event) {
        if(app.isMeasure) {
            //量測的定位點
            pointMarker(event.latLng);
        } else {
            //危險區塊的定位點
            if(app.dangerList.length < app.dangerBlockMax) {

                if(app.isDangerSetting === false) {
                    //addDanger()函式會清除定位點列表，所以須先執行再執行placeMarker
                    //執行placeMarker會顯示定點及更新除定位點列表
                    app.addDanger();
                }
                placeMarker(event.latLng);
            } else {
                let msg = '危險區塊已到達上限'+app.dangerBlockMax+'個';
                showMessage(app, msg);
            }

        }

    });
    //let dangerLocations = [];
}

function placeMarker(location) {
    let placeObj = blockObj.placeObj;
    let num = makeMarker(map, placeObj, location, app.list, false, true, 'asc');
    markerCoords(placeObj.markerList[num], num);//移動監聽
}

//量測點
function pointMarker(location) {
    let num = makeMarker(map, measureObj, location, app.measureList, false, true, 'asc');
    markerCoords2(measureObj.markerList[num], num);

    if(num > 0) {
        app.betweenLine();
        app.betweenDistance();
    }
}

//監聽定位點移動
function markerCoords(markerObject, num){
    google.maps.event.addListener(markerObject, 'dragend', function(evt){
        console.log('lat:'+evt.latLng.lat());
        console.log('lng:'+evt.latLng.lng());
        app.list[(num)]['lat'] = getFixNumber(evt.latLng.lat());
        app.list[(num)]['lng'] = getFixNumber(evt.latLng.lng());

        if(app.isMeasure === false && blockObj.newBlock !== null) {
            blockObj.newBlock = getBlock(app.list, blockObj, app.distanceList, 1);
            //socket.emit('map', {mac:device_mac, "block":app.list});
        }
    });

    /*google.maps.event.addListener(markerobject, 'drag', function(evt){
        console.log("marker is being dragged");
    });*/
}

function markerCoords2(markerObject, num){
    google.maps.event.addListener(markerObject, 'dragend', function(evt){
        //console.log('lat:'+evt.latLng.lat());
        //console.log('lng:'+evt.latLng.lng());
        app.measureList[num].lat = getFixNumber(evt.latLng.lat());
        app.measureList[num].lng = getFixNumber(evt.latLng.lng());
        if(flightPath !== null) {
            toClearLine();
            app.betweenLine();
            app.betweenDistance();
        }
    });
}

function clearMarkers() {
    //app.backupList = JSON.parse(JSON.stringify(app.list));
    work_clearAllMakers(blockObj.placeObj);
    app.list = JSON.parse(JSON.stringify(arr));
}

function clearReports() {
    work_clearAllMakers(reportObj);
    app.searchList = [];
}

function clearOldBlock() {
    app.backupList = JSON.parse(JSON.stringify(app.list));
    oldBlock.setMap(null);
}

function toClearLine() {
    flightPath = work_clearLine(flightPath);
    app.distance = 0;
}

function getFixNumber(num) {
    return parseFloat(num.toFixed(6));
}

function getBlock(list, blockObject, distanceList, type) {
    for(let i=0;i<list.length;i++) {
        let point = list[i];
        if(point.lat===0 || point.lng===0) {
            return;
        }
    }
    //顯示區塊會重新整理定位點
    toClearBlock(blockObj);
    let result = createPolygon(map, list, type);
    app.list = result.list;
    app.distanceList = result.distanceList;
    work_clearAllMakers(blockObj.placeObj);
    if(blockObj.placeObj.isShow) {
        for(let i=0;i<app.list.length;i++) {
            let point = app.list[i];
            let loc = new google.maps.LatLng(point.lat, point.lng);
            placeMarker(loc);
        }
    }
    return result.block;
}

function toClearLineMarker(obj) {
    work_clearAllMakers(obj);
}

initPointList();
initReports();

let app = new Vue({
    el: '#app',
    data: {
        isDemo: false,
        dangerBlockMax:dangerBlockMax,
        tab: 1,
        isBlock: true,
        isRun: false,
        isMeasure: true,
        isOpenWindow:true,
        list:  JSON.parse(JSON.stringify(arr)),
        centerTab: 1,
        backupList: null,
        measureList: [JSON.parse(JSON.stringify(emptyLatLng)), JSON.parse(JSON.stringify(emptyLatLng))],
        distance: null,
        isShowDistance: false,
        distanceList: [],
        center: JSON.parse(JSON.stringify(myCenter)),
        setting: mySetting,
        status: statusObj,
        label: labelObj,
        appList: apps,
        setString: JSON.stringify(mySetting.set),
        centerIndex: center_index,
        alertMessage: '',
        isSendCmd: false,
        message: '',
        searchList: JSON.parse(JSON.stringify(tmpSearchArr)),
        danger: emptyDanger,
        dangerList: JSON.parse(JSON.stringify(dangers)),
        isDangerSetting:false,
        dangerIndex:0,
        checkList: checkList
    },
    mounted() {
        window.setTimeout(function () {
            initialize();
            //listMark(tmpSearchArr);
            reportObj.icon = {
                url: point_url, // url
                scaledSize: new google.maps.Size(4, 4), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(2,2) // anchor
            };
            measureObj.icon = {
                url:diamond_url, // url
                scaledSize: new google.maps.Size(16, 20), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(8,20) // anchor
            };
            for(let i=0; i<dangers.length;i++) {
                let danger = dangers[i];
                //dangerLocations.push(danger.set);
                let result = createPolygon(map, danger.set, 2);
                dangerBlocks[i] = result.block;
            }
            if(app.searchList.length>0) {
                work_listMark(map, reportObj, app.searchList, false, 'desc');
            }

        }, 1000);

    },
    methods: {
        clearBlockMarkers() {
            clearMarkers();
        },
        changeTab(value) {
            if(value === 2) {//歷史紀錄
                this.toHistory();
                return;
            }
            this.tab = value;
            this.cancel();
            if(value === 1 || value === 3) {
                this.isMeasure = true;
                this.setting = mySetting,
                this.setString= JSON.stringify(mySetting.set);
            } else if(value === 4){
                this.isMeasure = false;
                if(this.dangerList.length>0) {
                    this.setting = this.dangerList[0];
                    this.list = JSON.parse(JSON.stringify(this.setting.set ));
                }
            }


        },
        change(value){
            if(value === 2) {
                if(this.distanceList.length === 0) {
                    app.tab = 1;
                    alert('劃定區塊後才能取得區塊定位點間距!');
                    return;
                }
                this.isShowDistance = true;
            } else {
                this.isShowDistance = false;
            }
        },
        createBlock() {
            let isCheck = true;
            for(let i=0;i<this.list.length;i++) {
                let tmp = this.list[i];
                if(tmp.lat === 0) {
                    isCheck = false;
                }
            }
            if(isCheck) {
                blockObj.newBlock = getBlock(app.list, blockObj, app.distanceList, 1);
                /*socket.emit('map', {mac:device_mac, "block":this.list});*/
            } else {
                alert('請用滑鼠在地圖上加入4個定點，才能成圍籬區塊!')
            }
        },
        clearNewBlock() {
            toClearBlock(blockObj);
            clearMarkers();
            app.distanceList = [];
        },
        betweenDistance() {
            this.distance= getDistance2(this.measureList[0], this.measureList[1]);
            this.distance = this.distance.toFixed(2)+'公尺';
        },
        betweenLine() {
            flightPath = work_getLine(map, this.measureList);
        },
        clearLine() {
            if(flightPath !== null) {
                toClearLine();
                app.distance = 0;
            }
            toClearLineMarker(measureObj);
            this.measureList =[JSON.parse(JSON.stringify(emptyLatLng)), JSON.parse(JSON.stringify(emptyLatLng))];
        },

        measureTool() {
            this.isMeasure = true;
            this.isBlock=false;
        },
        changeDangers(event) {
            let value = event.target.value;
            this.setting = this.dangerList[value];
            this.list = JSON.parse(JSON.stringify(this.setting.set ));

            if(this.isDangerSetting === true) {
                blockObj.newBlock = getBlock(app.list, blockObj, app.distanceList, 1);
            }
        },
        addDanger() {
            this.isDangerSetting = true;
            this.setting = JSON.parse(JSON.stringify(emptyDanger));
            this.list = JSON.parse(JSON.stringify(arr));
            this.distanceList = [];
        },
        setDanger() {
            this.isDangerSetting = true;
            blockObj.newBlock = getBlock(app.list, blockObj, app.distanceList, 1);
        },
        delDanger() {
            $('#myModal2').modal('show');
        },
        toDelDanger() {
            showOverlay();
            document.getElementById('delDanger').submit();
        },
        saveDanger() {
            if(blockObj.newBlock === null) {
                return alert('請先劃區塊!');
            }
            if(this.setting.name.length === 0) {
                return alert('請填入區域名稱!');
            }
            this.setString = JSON.stringify(this.list);
            window.setTimeout(function () {
                showOverlay();
                document.getElementById('editDangerSetting').submit();
            }, 500);

        },
        cancel() {
            this.isDangerSetting = false;

            if(this.dangerList.length>0) {
                this.setting = this.dangerList[this.dangerIndex ];
                this.list = JSON.parse(JSON.stringify(this.setting.set ));
            }

            if(blockObj.newBlock !== null) {
                toClearBlock(blockObj);//in work_alg.js
                this.distanceList = [];
            }
            work_clearAllMakers(blockObj.placeObj);//in work_alg.js
        },
        addCenter() {
            this.centerTab = 1;
            this.center = JSON.parse(JSON.stringify(emptyCenter));
            //改圍籬list
            /*if (this.list[0]['lat'] !== 0) {
                this.center.lat = this.list[0]['lat'];
                this.center.lng = this.list[0]['lng'];
                $('#myModal').modal('show');
            } else {
                alert('請在地圖上先加入定位點!');
            }*/
            if (this.measureList[0]['lat'] !== 0) {
                this.center.lat = this.measureList[0]['lat'];
                this.center.lng = this.measureList[0]['lng'];
                $('#myModal').modal('show');
            } else {
                alert('請在地圖上先加入做為中心的定位點!');
            }
        },
        setCenter() {
            this.centerTab = 2;
            this.center = this.setting.set[this.centerIndex];
            /*if(this.list[0]['lat'] !== 0) {
                this.center.lat = this.list[0]['lat'];
                this.center.lng = this.list[0]['lng'];
            }*/
            if(this.measureList[0]['lat'] !== 0) {
                this.center.lat = this.measureList[0]['lat'];
                this.center.lng = this.measureList[0]['lng'];
            }

            $('#myModal').modal('show')
        },
        delCenter() {
            this.centerTab = 3;
            this.center = this.setting.set[this.centerIndex];

            $('#myModal').modal('show')
        },
        toDelSetting() {
            this.setting.set.splice(this.centerIndex,1);
            this.setting.set = JSON.stringify(this.setting.set);
            window.setTimeout(function () {
                showOverlay();
                document.getElementById('editSetting').submit();
            }, 500);
        },
        toAddSetting() {
            if(typeof(this.center.lat)=== 'string') {
                this.center.lat = parseFloat(this.center.lat);
            }
            if(typeof(this.center.lng)=== 'string') {
                this.center.lng = parseFloat(this.center.lng);
            }
            if(typeof(this.center.room)=== 'string') {
                this.center.room = parseInt(this.center.room);
            }

            if(this.centerTab===1) {
                this.setting.set.splice(this.setting.set.length,1, this.center);
                this.centerIndex++;
            } else if(this.centerTab===2) {
                this.setting.set[this.centerIndex] = this.center;
            }

            this.setting.set = JSON.stringify(this.setting.set);
            window.setTimeout(function () {
                showOverlay();
                document.getElementById('editSetting').submit();
            }, 500);
        },
        changeSetting(event) {
            //alert(event.target.value);
            $.LoadingOverlay("show");
            let newUrl = "/room/usv?device_id="+device.id;
            newUrl = newUrl+'&center_index='+event.target.value;
            document.location.href = newUrl;
        },
        toViewControl() {

            let newUrl = "/room/viewControl?device_id="+device.id;
            document.location.href = newUrl;
        },
        toHistory() {

            showOverlay();

            let newUrl = "/room/history/"+device.id;
            document.location.href = newUrl;
        },
        showInfo(index) {
            reportObj.infoList[reportObj.lastNum].close(reportObj.lastNum);
            reportObj.infoList[index].open(map, reportObj.markerList[index]);
            reportObj.lastNum = index;
        },
        highlight(index) {
            let myItem = this.searchList[index];
            myItem.backup = JSON.parse(JSON.stringify(myItem.data));
            myItem.data= JSON.parse(JSON.stringify(highlightBg));
            this.showInfo(index);
        },
        restoreColor(index, color) {
            let myItem = this.searchList[index];
            myItem.data = JSON.parse(JSON.stringify(myItem.backup));
        }
    }
});

function sendToApi(url,data) {

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        /*beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
        },*/
        success: function (result) {
            //console.log(result);
            showMessage(app,result);
        },
        error:function(err){
            //app.alertMessage = err;
            showMessage(app,err);
        },
    });
    setTimeout(function(){
        app.isSend = false;
    }, 5000);
}

function sendCmd(url) {
    app.isSendCmd = true;
    app.message = '';
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+ token);
        },
        success: function (result) {
            //$.LoadingOverlay("hide");
            window.setTimeout(function () {
                if(result.code == 200){//可以完成設定程序
                    let msg =mechanism.cmd_name+'完成命令';
                    showMessage(app, msg, 3000);
                    app.isSendCmd = false;
                }
            }, 500);

        },
        error:function(err){
            //$.LoadingOverlay("hide");
            let msg = err;
            showMessage(app, msg, 3000);
            app.isSendCmd = false;
        },
    });
    window.setTimeout(function () {
        app.isSendCmd = false;
    }, 5000);
}

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

socket.on('usv_update_mqtt_ul', function(m) {
    console.log('From server usv_update_mqtt_ul -----------------');
    checkReport(m);
});

socket.on('http_report_data', function(m) {
    console.log('From server : http_report_data ------');
    checkReport(m);
});

socket.on('update_mqtt_ul', function(m) {
    console.log('From server : update_mqtt_ul ------');
    checkReport(m);
});

function checkReport(data) {

    if (typeof data === 'string') {
        data = JSON.parse(data);
    }
    let index = checkList.indexOf(data.macAddr)
    if(index === -1) {
        console.log('receive '+ data.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    let mChoice = 0;
    if(data.hasOwnProperty('app_id')) {
        mChoice = statusTarget[data.app_id];
    }
    data.recv = formatDate(data.recv);
    updateStatus(mChoice, data);
}

function updateStatus(mChoice, m) {

    if(mChoice ===1) {
        //超過最大數自動刪除第一筆
        removeMarkerCheck(reportObj, app.searchList, order);
        let isDanger = false;
        let location = new google.maps.LatLng(m.lat,m.lng);

        for(let i=0; i<dangerBlocks.length;i++) {
            let dangerBlock = dangerBlocks[i];
            if(isInBlock(location,dangerBlock)) {
                isDanger = true;
                break;
            }
        }
        if(isDanger) {
            let msg = m.macAddr + '進入危險區域';
            showMessage(app, msg, 3000);
            let url = api_url+'/api/update-report';
            m.data = JSON.parse(JSON.stringify(dangerBg));
            let data = {id:m.id, data:JSON.stringify(m.data), token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
        } else {
            m.data = JSON.parse(JSON.stringify(baseBg));
        }

        if(order === 'asc') {
            app.status[mChoice].splice(app.searchList.length, 1 , JSON.parse(JSON.stringify(m)));
            app.searchList.splice(app.searchList.length, 1 , JSON.parse(JSON.stringify(m)));
        } else {
            app.status[mChoice].unshift(m);
            app.searchList.unshift(m);
        }

        addLocationMarker(map, reportObj, m, app.searchList, true, false, order)

    } else {
        //for other app sequence
        app.status[mChoice] = JSON.parse(JSON.stringify(m));
    }
}


