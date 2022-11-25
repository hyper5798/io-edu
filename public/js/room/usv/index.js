let table;
let measureObj = {max:2, lastNum:0};//量測點物件
let targetObj = {max:50, lastNum:0};//標的點物件
let map;
let a = -1;
let count = 0;
let max = 4;
let arr = [];
let check = [];
let check2 = [];
let markers = [];
let reportCount = 0;
let reports = [];
let infowindow = [];
let infowindow2 = [];
let newBlock = null;
let oldBlock = null;
let flightPath = null;
let icon, icon2, icon3;
let emptyHome = {'name': '','lat': 0,'lng': 0};
let emptyLatLng = {'lat': 0,'lng': 0};
let emptyCenter = {'name': '','lat': 24.001556,'lng': 121.637998, 'room':18};
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
//Jason add for check set home ack is finished on 2021.8.4
let checkHomeAck = null;
let tmpSearchArr = [];

function setHomeCheck() {
    app.isCheckHome = true;
    checkHomeAck = window.setTimeout(function () {
        if(app.isCheckHome === true) {
            app.isCheckHome = false;
            app.isSetHome = false;
            alert('設定無人船HOME點失敗!');
        }
    }, 5000);
}

function cancelHomeCheck() {
    app.isCheckHome = false;
    window.clearTimeout(checkHomeAck);
}

let emptySetting = {
    app_id:0,
    field:'center',
    set: []
};

let emptyHomeSetting = {
    app_id:0,
    field:'home',
    set: []
};

let myCenter = emptyCenter;
let myHome = emptyHome;
let mySetting = (setting == null) ? JSON.parse(JSON.stringify(emptySetting)) : JSON.parse(JSON.stringify(setting));
let myHomeSetting = (home_setting == null) ? JSON.parse(JSON.stringify(emptyHomeSetting)) : JSON.parse(JSON.stringify(home_setting));
if(mySetting.set.length>0){
    if(center_index > (mySetting.set.length-1)) {
        center_index = mySetting.set.length-1;
    }
    if(center_index !== null) {
        myCenter = mySetting.set[center_index];
    }
}

if(myHomeSetting.set.length>0){
    myHome = myHomeSetting.set[0];
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
}

let labelObj, status_9, statusTarget;

function initReports() {
    statusTarget = {};
    labelObj = {};
    for(let i=0;i<apps.length;i++) {
        let app = apps[i];
        //置換客制化上報
        let check = 'sequence'+app.sequence;
        let keys = [];
        if(app.sequence<=5) {
            keys = Object.keys(app.key_label);
        } else {

            if(reportSetting!==null &&  reportSetting.hasOwnProperty(app.id)) {
                if(reportSetting[app.id] !== null) {
                    keys = reportSetting[app.id]['set'];
                    let newObj = {};
                    for(let j=0; j<keys.length;j++) {
                        if(app.key_label.hasOwnProperty(keys[j])) {
                            newObj[keys[j]] = app.key_label[keys[j]];
                        }
                    }
                    app.key_label = JSON.parse(JSON.stringify(newObj));
                } else {
                    app.key_label = [];
                }

            } else {
                continue;
            }
        }



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
            let tmp = statusObj[app.sequence];
            if(tmp.hasOwnProperty('lat')) {
                tmp.lat =  parseFloat(tmp.lat).toFixed(6)
            }
            if(tmp.hasOwnProperty('lng')) {
                tmp.lng =  parseFloat(tmp.lng).toFixed(6)
            }
        }
        if(app.sequence === 3) {
            $("#usv_direction").rotate(statusObj[app.sequence]['key3']);
        }
    }

    status_9 = JSON.parse(JSON.stringify(empty9));
}

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 1, item );
};

$(document).ready(function() {

    $('#timeselector input').on("change", function() {
        app.changeTab(parseInt(this.id));
    });

    let myModalEl = document.getElementById('myModal')
    myModalEl.addEventListener('hide.bs.modal', function (event) {
        // do something...
        console.log(event.target);
    })

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
    icon3= {
        url: star_url, // url
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
            //電子圍籬的定位點
            placeMarker(event.latLng);
        }

    });
}


function showTargetMaker(list) {
    targetObj = {title:'標的',max:20, lastNum:0};
    for(let i=0;i<list.length;i++) {
        let loc = list[i];
        targetMaker(loc);
    }
}

//target object
//標的點
function targetMaker(location) {

    makeMarker(map, targetObj, location, null, false, false);
}


//定位點
function placeMarker(location) {
    if(count>=max) return;
    let num = count;
    check.splice(num,1,1);
    markers[num] = new google.maps.Marker({
        position: location,
        map: map,
        draggable: true
    });
    infowindow[num] = new google.maps.InfoWindow({
        content: '定位點:'+(num+1)
        /*'緯度: ' + getFixNumber(location.lat() )+
        '<br>經度: ' + getFixNumber(location.lng())*/
    });
    if(isOpen) {
        infowindow[num].open(map, markers[num]);
    } else {
        check[num] = check[num] * -1;
    }

    markers[num].addListener('click',function(){
        check[num] = check[num] * -1;
        if(check[num] > 0){
            infowindow[num].open(map, markers[num]);
        }else{
            infowindow[num].close();
        }
    });
    //Drag point then change location and block
    markerCoords(markers[num], num);

    app.list[(num)]['lat'] = getFixNumber(location.lat());
    app.list[(num)]['lng'] = getFixNumber(location.lng());

    count++;
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

//上報點
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

//監聽定位點移動
function markerCoords(markerObject, num){
    google.maps.event.addListener(markerObject, 'dragend', function(evt){
        console.log('lat:'+evt.latLng.lat());
        console.log('lng:'+evt.latLng.lng());
        app.list[(num)]['lat'] = getFixNumber(evt.latLng.lat());
        app.list[(num)]['lng'] = getFixNumber(evt.latLng.lng());

        if(isBlock === true) {
            newBlock = getBlock(app.list, newBlock);
            socket.emit('map', {mac:device_mac, "block":app.list});
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
    })
}

function clearMarkers() {
    app.backupList = JSON.parse(JSON.stringify(app.list));
    setMapOnAll(null);
    /*app.test.lat = 0;
    app.test.lng = 0;
    app.test2.lat = 0;
    app.test2.lng = 0;*/
}

function clearReports() {

    for (let i = 0; i < reports.length; i++) {
        reports[i].setMap(null);
    }
    check2 = [];
    reports = [];
    infowindow2 = [];
    reportCount = 0;
    if(isAdmin) {
        socket.emit('map', {"mac":device_mac, "clear_report":true});
    }
}

function toClearBlock() {
    newBlock.setMap(null);
    newBlock = null;
}

function clearOldBlock() {
    app.backupList = JSON.parse(JSON.stringify(app.list));
    oldBlock.setMap(null);
}

function setMapOnAll(map) {
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
        app.list.splice(i,1,  {'lat': 0,'lng': 0});
    }
    check = [];
    markers = [];
    infowindow = [];
    count = 0;
}

function getFixNumber(num) {
    return parseFloat(num.toFixed(6));
}

function getBlock(list, myPolygon, type) {

    let tmpList = [];
    if(isDebug === true) {
        tmpList = JSON.parse(JSON.stringify(list))
    } else {
        tmpList = convex_hull(list);
    }

    clearMarkers();

    if(type === undefined || type === null) {
        type = 1;
    }

    let myTrip = [];
    let tripNum = 0;
    let tmp;
    app.distanceList = [];

    for(let i=0;i<tmpList.length;i++) {
        if(i<(tmpList.length-1)) {
            //console.log('p'+(i+1)+'-p'+(i+2)+':'+getDistance2(tmpList[i], tmpList[i+1]));
            let length = getDistance2(tmpList[i], tmpList[i+1]);
            length = length.toFixed(2) + ' 公尺';
            app.distanceList.splice(i,0, length);
            //app.distanceList.splice(i,0, getDistance2(tmpList[i], tmpList[i+1]));
        }
        let point = tmpList[i];
        //Bypass last(origin) maker
        if(isDebug === true && type !==2) {
            let loc = new google.maps.LatLng(point.lat, point.lng);
            placeMarker(loc);
        } else if(i< (tmpList.length-1) && type !==2) {
            let loc = new google.maps.LatLng(point.lat, point.lng);
            placeMarker(loc);
        }


        //console.log('point:'+(i+1)+', lat:'+point.lat+', lng:'+point.lng);
        if(point.lat !== null && point.lng !== null) {
            tripNum++;
            let item = new google.maps.LatLng(point.lat,point.lng);
            myTrip.splice( i, 0, item );
            if(i===0) {
                tmp = item;
            }
        }
    }
    if(isDebug === true) {
        myTrip.splice( tripNum, 0, tmp );
    }
    if(tripNum < 3) {
        alert('定位點過少，無法劃定區塊!');
        return;
    }
    if(myPolygon !== null) {
        toClearBlock();
        myPolygon = null;
    }
    let color1 = "#838385";
    let color2 = "#ffe994";
    if(type === 2 ) {
        color1 = "#838385";
        color2 = "#ff4343";
    }

    myPolygon = new google.maps.Polygon({
        paths: myTrip,
        strokeColor: color1,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: color2,
        fillOpacity: 0.35,
    });
    myPolygon.setMap(map);
    list = tmpList.splice((tmpList.length-1),1);
    isBlock = true;
    return myPolygon;
}

function getFenceList(fenceList) {
    let direction = -1;//1: up, -1: down
    let diff = 100000;
    offset = 0.1;
    let interval = 10; //0.1*10 = 1m , 南北巡航線間隔1公尺
    if(typeof(app.parameter.set.interval) === 'string') {
        interval = 10* parseInt(app.parameter.set.interval);
    } else {
        interval = 10* app.parameter.set.interval;
    }

    let value = offset/diff;
    let report_count = 0;
    let p1 = fenceList[0];
    let left=p1.lng;
    let right=p1.lng;
    let up=p1.lat;
    let down=p1.lat;
    let rightConner = null;//最右邊的點
    for(let m=0; m<fenceList.length;m++) {
        let loc = fenceList[m];
        if(left > loc.lng) {
            left = loc.lng;
        }
        if(right < loc.lng) {
            right = loc.lng;
            rightConner = JSON.parse(JSON.stringify(loc));
        }
        if(down > loc.lat) {
            down = loc.lat;
        }
        if(up < loc.lat) {
            up = loc.lat;
        }
    }
    let tmp = {'lat': up,'lng': left};
    /*console.log('left:'+left);
    console.log('right:'+right);
    console.log('up:'+up);
    console.log('down:'+down);
    console.log('rightConner lat:'+rightConner.lat + ', lng:'+rightConner.lng);*/
    let len1 = ((right*diff) - (left*diff))/offset;
    let len2 = ((up*diff) - (down*diff))/offset;
    //console.log('len1:'+len1);
    //console.log('len2:'+len2);

    len1 = Math.ceil( len1 );
    len2 = Math.ceil( len2 );
    //console.log('len1:'+len1);
    //console.log('len2:'+len2);
    let tmpArr = [];
    let testArr = [];
    let myLocation = {};
    //lat : S~N, lng: W~E
    for(let i=0;i<len1;i=i+interval) {//由左到右
        for(let j=0;j<len2;j++) {//由上到下
            if(j> 0) {
                let newLat = ((tmp.lat)+(value*direction));
                newLat = parseFloat(((newLat)+(value*direction)).toFixed(6));
                tmp.lat = newLat;
            }
            let loc = new google.maps.LatLng(tmp.lat, tmp.lng);
            if(isInBlock(loc, newBlock)) {
                tmpArr.splice(tmpArr.length, 0, {'lat': tmp.lat,'lng': tmp.lng});
            } else {
                console.log('Outside lat:'+tmp.lat+', lng:'+tmp.lng);
            }
        }
        //console.log(myarray.length);

        if(tmpArr.length > 1) {
            //有起始點,沒有結束點
            //因已根據方向切換資料就不必再做方向切換
            myLocation.start = tmpArr[0];
            myLocation.end = tmpArr[tmpArr.length-1];
        } else if(tmpArr.length === 1) {
            myLocation.start = tmpArr[0];
            myLocation.end = tmpArr[0];
        } else {//最左的點(第一點)但是不在範圍內
            myLocation.start = fenceList[0];
            myLocation.end = fenceList[0];
        }
        testArr.push(JSON.parse(JSON.stringify(myLocation)));

        tmpArr = [];
        let newLng = (tmp.lng+(value*interval));
        newLng = parseFloat(((newLng)+(value*direction)).toFixed(6));
        tmp.lng = newLng;
        direction = direction*-1;
    }
    /*補上loss的右三角
    1.加上最右邊的點
    2.加上倒數第二點
     */
    let last1 = testArr[testArr.length-1];//最後1點
    let test1 = {"start":last1.end, "end":rightConner};//最後1點到最右點
    let test2 = {"start":rightConner, "end":last1.start};//最右點到倒數第二點
    //
    testArr.push(test1);
    testArr.push(test2);

    /*let last = testArr[testArr.length-1];
    testArr.push({'start': last, 'end': rightConer});*/

    return testArr;
}

function getDemoList(demoList) {
    let direction = -1;//1: up, -1: down
    let diff = 100000;
    offset = 3;
    let value = offset/diff;
    let report_count = 0;
    let p1 = demoList[0];
    let left=p1.lng;
    let right=p1.lng;
    let up=p1.lat;
    let down=p1.lat;
    for(let m=1; m<demoList.length;m++) {
        let loc = demoList[m];
        if(left > loc.lng) {
            left = loc.lng;
        }
        if(right < loc.lng) {
            right = loc.lng;
        }
        if(down > loc.lat) {
            down = loc.lat;
        }
        if(up < loc.lat) {
            up = loc.lat;
        }
    }


    let start = {'lat': up,'lng': left};
    let tmp = {'lat': up,'lng': left};
    /*console.log('left:'+left);
    console.log('right:'+right);
    console.log('up:'+up);
    console.log('down:'+down);*/
    let len1 = ((right*diff) - (left*diff))/offset;
    let len2 = ((up*diff) - (down*diff))/offset;
    /*console.log('len1:'+len1);
    console.log('len2:'+len2);*/


    //console.log(Math.floor( len1 )) ;
    //console.log(Math.ceil( len1 ));
    //console.log(Math.round( len1 ));
    len1 = Math.ceil( len1 );
    len2 = Math.ceil( len2 );
    /*console.log('len1:'+len1);
    console.log('len2:'+len2);*/
    myarray = [];
    for(let i=0;i<len1;i++) {
        for(let j=0;j<len2;j++) {
            if(j> 0) {
                tmp.lat = ((tmp.lat)+(value*direction));
            }
            let loc = new google.maps.LatLng(tmp.lat, tmp.lng);
            if(isInBlock(loc,newBlock) || isFilter === false) {
                myarray.splice(report_count, 0, {'lat': tmp.lat,'lng': tmp.lng});
                //placeReport({'lat': tmp.lat,'lng': tmp.lng});
                report_count++;
            }
        }
        //break;report_count
        tmp.lng = tmp.lng+(value);
        direction = direction*-1;
    }

    //setTimeout( placeReport( myarray[i]), 3000);
    reduce = myarray.length;
    myVar = setInterval(myTimer, 10);
}

function myTimer() {
    if(reduce > 0) {
        app.test = myarray[myarray.length-reduce];
        placeReport(app.test, false);
        reduce--;
    } else {
        app.isRun = false;
        myStopFunction();
    }
}

function myStopFunction() {
    clearInterval(myVar);
}

function toClearLine() {
    work_clearLine(flightPath);
    app.distance = 0;
}

function toClearLineMarker(obj) {
    work_clearAllMakers(obj);
}

function stop() {
    app.isRun = false;
    let arr = [];
    if(app.isDemo === false) {
        socket.emit('map', {mac:device_mac, data:arr});
    } else {
        myStopFunction();
    }
}

function toHome() {
    if(app.isSetHome===false) {
        alert('請先設定Home點!');
        app.changeTab(1);
        return;
    }
    app.isRun = true;
    let arr = [];
    if(typeof(app.home.lat) === 'string') {
        app.home.lat = parseFloat(app.home.lat);
    }
    if(typeof(app.home.lng) === 'string') {
        app.home.lng = parseFloat(app.home.lng);
    }
    arr.push({"start": app.home});
    socket.emit('map', {mac:device_mac, data:arr});
}

function getTime(obj) {
    let time = new Date(obj);
    let h = time.getHours();
    let m = time.getMinutes();
    let s = time.getSeconds();
    return h+ '時:'+ m + '分:' +s +'秒'
}

initPointList();

initReports();

let app = new Vue({
    el: '#app',
    data: {
        isShow:0,
        isDemo: false,
        isOpenWindow: true,
        tab: 1,
        isCheckHome:false,
        isBlock: true,
        isRun: false,
        isMeasure: false,
        list:  arr,
        centerTab: 1,
        backupList: null,
        measureList: [JSON.parse(JSON.stringify(emptyLatLng)), JSON.parse(JSON.stringify(emptyLatLng))],
        test: JSON.parse(JSON.stringify(emptyLatLng)),
        test2: JSON.parse(JSON.stringify(emptyLatLng)),
        distance: null,
        isShowDistance: false,
        distanceList: [],
        home:JSON.parse(JSON.stringify(myHome)),
        center: JSON.parse(JSON.stringify(myCenter)),
        setting: mySetting,
        status: statusObj,
        label: labelObj,
        status9:status_9,
        setString: JSON.stringify(mySetting.set),
        centerIndex: center_index,
        home_setting: myHomeSetting,
        homeIndex: 0,
        isSetHome: false,
        parameter: parameter,
        alertMessage: '',
        isSendCmd: false,
        cmdMessage: '',
        searchList: JSON.parse(JSON.stringify(locations)),
        location: {'lat': '','lng': ''},
        appList:apps,
        deviceMac:device_mac,
        targetOption: 1,
        isSupportUAV: (device.support==1) ? true : false,
        image_url: null
    },
    mounted() {
        window.setTimeout(function () {
            initialize();
            targetObj.icon = {
                url: star_url, // url
                scaledSize: new google.maps.Size(16, 20), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(8,20) // anchor
            };
            measureObj.icon = {
                url:diamond_url, // url
                scaledSize: new google.maps.Size(16, 20), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(8,20) // anchor
            };

            showTargetMaker(locations);
        }, 1000);

    },
    methods: {
        toParamTest() {
            let newUrl = "/node/paramTest?mac="+device_mac;
            document.location.href = newUrl;
        },
        clearBlockMarkers() {
            clearMarkers();
            if(isAdmin) {//Admin web sync with normal user
                socket.emit('map', {"mac":device_mac, "clear_marker":true});
            }
        },
        changeTab(value) {
            this.tab = value;
            if(value === 1) {
                this.isBlock = true;
            } else {
                this.isBlock = false;
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
                newBlock = getBlock(this.list, newBlock);
                socket.emit('map', {mac:device_mac, "block":this.list});
            } else {
                alert('請用滑鼠在地圖上加入4個定點，才能成圍籬區塊!')
            }
        },
        createOldBlock() {
            oldBlock = getBlock(this.backupList, oldBlock, 2);
        },
        clearNewBlock() {
            toClearBlock();
            clearMarkers();
            app.distanceList = [];
            isBlock = false;
            if(isAdmin) {
                socket.emit('map', {"mac":device_mac, "clear_block":true});
            }
        },
        betweenDistance() {
            this.distance= getDistance2(this.measureList[0], this.measureList[1]);
            this.distance = this.distance.toFixed(2)+'公尺';
        },
        demo() {
            if(newBlock !== null) {
                this.isRun = true;
                let arr = getDemoList(this.list);
            } else {
                app.changeTab(1);
                alert('請先劃定區塊！');
            }
        },
        fence() {
            if(newBlock !== null) {
                this.isRun = true;
                let arr = getFenceList(this.list);
                socket.emit('map', {mac:device_mac, data:arr});
            } else {
                app.changeTab(1);
                alert('請先劃定區塊！');
            }
        },
        betweenLine() {
            flightPath = work_getLine(map, this.measureList);
        },
        clearLine() {
            if(flightPath !== null) {
                toClearLine();
            }
            toClearLineMarker(measureObj);
            this.measureList =[JSON.parse(JSON.stringify(emptyLatLng)), JSON.parse(JSON.stringify(emptyLatLng))];
        },
        setHomeToNode() {
            clearMarkers();
            //let homeLocation = new google.maps.LatLng(this.home.lat, this.home.lng);
            let homeLocation = new google.maps.LatLng(this.list[0]['lat'], this.list[0]['lng']);
            placeMarker(homeLocation);
            if(isAdmin) {
                socket.emit('map', {"mac":device_mac, "home":true ,"data":this.home});
            }
            setHomeCheck();
        },
        measureTool() {
            this.isMeasure = true;
            this.isBlock=false;
        },
        addCenter() {
            this.centerTab = 1;
            this.center = JSON.parse(JSON.stringify(emptyCenter));
            if (this.list[0]['lat'] !== 0) {
                this.center.lat = this.list[0]['lat'];
                this.center.lng = this.list[0]['lng'];
                $('#myModal').modal('show');
                $('html, body').scrollTop(200);
            } else {
                alert('請在地圖上先加入定位點!');
            }
        },
        setCenter() {
            this.centerTab = 2;
            this.center = this.setting.set[this.centerIndex];
            if(this.list[0]['lat'] !== 0) {
                this.center.lat = this.list[0]['lat'];
                this.center.lng = this.list[0]['lng'];
            }

            $('#myModal').modal('show');
            $('html, body').scrollTop(200);
        },
        delCenter() {
            this.centerTab = 3;
            this.center = this.setting.set[this.centerIndex];

            $('#myModal').modal('show');
            $('html, body').scrollTop(200);
        },
        addHome() {
            this.centerTab = 4;
            this.home = JSON.parse(JSON.stringify(emptyHome));
            if (this.list[0]['lat'] !== 0) {
                this.home.lat = this.list[0]['lat'];
                this.home.lng = this.list[0]['lng'];
                $('#myModal').modal('show');
                $('html, body').scrollTop(200);
            } else {
                alert('請先在地圖上加入定位點!');
            }
        },
        setHome() {

            this.centerTab = 5;
            this.home = this.home_setting.set[this.homeIndex];
            if(this.list[0]['lat'] !== 0) {
                this.home.lat = this.list[0]['lat'];
                this.home.lng = this.list[0]['lng'];
            }

            $('#myModal').modal('show');
            $('html, body').scrollTop(200);
        },
        delHome() {
            this.centerTab = 6;
            this.home = this.home_setting.set[this.homeIndex];

            $('#myModal').modal('show');
            $('html, body').scrollTop(200);
        },
        toDelSetting() {
            this.setting.set.splice(this.centerIndex,1);
            this.setting.set = JSON.stringify(this.setting.set);
            window.setTimeout(function () {
                showLoadingOverlay();
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
                showLoadingOverlay();
                document.getElementById('editSetting').submit();
            }, 500);
        },
        changeSetting(event) {
            //alert(event.target.value);
            showLoadingOverlay();
            let newUrl = "/room/usv?device_id="+device.id;
            newUrl = newUrl+'&center_index='+event.target.value;
            document.location.href = newUrl;
        },
        toViewControl() {
            showLoadingOverlay();
            let newUrl = "/room/viewControl?device_id="+device.id;
            document.location.href = newUrl;
        },
        toHistory() {
            showLoadingOverlay();
            let newUrl = "/room/history/"+device.id;
            document.location.href = newUrl;
        },
        toAddHomeSetting() {
            if(this.home.name.length === 0) {
                alert('尚未設定Home點名稱!');
                return;
            }
            if(typeof(this.home.lat)=== 'string') {
                this.home.lat = parseFloat(this.home.lat);
                this.home.lng = parseFloat(this.home.lng);
            }

            if(this.centerTab===4) {
                this.home_setting.set.splice(this.home_setting.set.length,1, this.home);
                //this.homeIndex++;
            } else if(this.centerTab===5) {
                this.home_setting.set[this.homeIndex] = this.home;
            }

            this.home_setting.set = JSON.stringify(this.home_setting.set);
            window.setTimeout(function () {
                showLoadingOverlay();
                document.getElementById('editHomeSetting').submit();
            }, 500);
        },
        toDelHomeSetting() {
            this.home_setting.set.splice(this.homeIndex,1);
            this.home_setting.set = JSON.stringify(this.home_setting.set);
            window.setTimeout(function () {
                showLoadingOverlay();
                document.getElementById('editHomeSetting').submit();
            }, 500);
        },
        changeHomeSetting(event) {
            this.homeIndex = parseInt(event.target.value);
            this.home = this.home_setting.set[this.homeIndex];
        },
        setParameter() {
            this.parameter.set = JSON.stringify(this.parameter.set);
            window.setTimeout(function () {
                showLoadingOverlay();
                document.getElementById('editParamSetting').submit();
            }, 500);
        },
        fenceOutline() {
            if(newBlock !== null) {
                this.isRun = true;
                let arr = [];
                for(let i = 0; i<this.list.length;i++) {
                    if(i<this.list.length-1) {
                        let tmp = {start:this.list[i], end:this.list[i+1]};
                        arr.splice( arr.length, 1, tmp );
                    } else {
                        let tmp = {start:this.list[i],end:this.list[0]};
                        arr.splice( arr.length, 1, tmp );
                    }
                }
                let tmp = {start:this.list[0]};
                arr.splice( arr.length, 1, tmp );
                socket.emit('map', {mac:device_mac, data:arr});
            } else {
                app.changeTab(1);
                alert('請先劃定區塊！');
            }
        },
        toPropeller(value) {
            let url = '';
            if(value === 0) {
                url = cmd + '&key1=0';
            }
            if(value === 1) {
                url = cmd + '&key1=1';
            }
            sendCmd(url);
        },
        toCatcher(value) {
            let url = '';
            if(value === 0) {
                url = cmd + '&key2=0';
            }
            if(value == 1) {
                url = cmd + '&key2=1';
            }
            sendCmd(url);
        },
        saveLocation() {

            if(this.location.lat.length === 0 || this.location.lng.length === 0) {
                return alert('標的緯度或經度不能為空!');
            }

            let data = {macAddr:device.macAddr, lat:this.location.lat, lng:this.location.lng, token:token, XDEBUG_SESSION_START:'PHPSTORM'};

            if(this.isSupportUAV) {
                let fileName = new Date().getTime()+'.jpeg';
                screenshot(fileName);
                data.fileName = fileName;
            }

            let url = api_url+'/api/save-location';

            sendToApi(url,data);
        },
        removeLocation(id) {

            let url = api_url+'/api/remove-location';
            let data = {id:id, macAddr:device.macAddr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
        },
        showInfo(index) {
            //alert(index);
            let obj = this.searchList[index];
            this.image_url = obj.image_url;
            let mLastNum = targetObj.lastNum;
            targetObj.infoList[mLastNum].close(mLastNum);
            targetObj.infoList[index].open(map, targetObj.markerList[index]);
            targetObj.lastNum = index;
        },
        changeTargetOption(option) {
            this.targetOption = option;
            if(option === 2) {
                if(this.searchList.length>0) {
                    this.showInfo(0);
                }
            }
        }
    }
});

function screenshot(fileName) {
    //let fileName = new Date().getTime()+'.png';

    $("#iframe1")[0].contentWindow.uploadImage(fileName);
}

function showLoadingOverlay() {
    $.LoadingOverlay("show");
    window.setTimeout(function () {
        $.LoadingOverlay("hide");
    }, 1000);
}

function sendCmd(url) {
    app.isSendCmd = true;
    app.cmdMessage = '';
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
                    app.cmdMessage =mechanism.cmd_name+'完成命令';
                    app.isSendCmd = false;
                }
            }, 500);

        },
        error:function(err){
            //$.LoadingOverlay("hide");
            app.cmdMessage = err;
            app.isSendCmd = false;
        },
    });
    window.setTimeout(function () {
        app.cmdMessage ='';
        app.isSendCmd = false;
    }, 5000);
}

function sendToApi(url,data) {
    app.isSend = true;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        /*beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
        },*/
        success: function (result) {
            //console.log(result);
            if(result.api === 'saveLocation' || result.api === 'removeLocation') {
                work_clearAllMakers(targetObj);
                app.searchList = result.locations;
                if(app.searchList.length>0) {
                    showTargetMaker(app.searchList);
                    app.showInfo(0);
                } else {
                    app.image_url = null;
                }
            }
            app.isSend = false;
        },
        error:function(err){
            //app.alertMessage = err;
            showMessage(app,err);
            app.isSend = false;
        },
    });
    setTimeout(function(){
        app.isSend = false;
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

//一般用戶進行同步圍籬狀態
socket.on('sync_status', function(m) {

    if(app.hasOwnProperty('home') === false) {
        //影像監控不做同步圍籬狀態
        return;
    }
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    //console.log(m);
    //mac from web
    if(m.mac !== device_mac) {
        console.log('receive '+ m.mac + ', device: '+device_mac + ' is different device');
        return;
    }
    if(m.hasOwnProperty('home')) {
        console.log('Web->Server->Web : create_home_point');
        app.home.lat = m.data.lat;
        app.home.lng = m.data.lng;
        app.list[0] = JSON.parse(JSON.stringify(app.home));
        let homeLocation = new google.maps.LatLng(app.home.lat, app.home.lng);
        placeMarker(homeLocation);

    } else if(m.hasOwnProperty('block')) {
        console.log('Web->Server->Web : create_block_point');
        app.list = m.block;
        newBlock = getBlock(app.list, newBlock);
    } else if(m.hasOwnProperty('clear_marker')) {
        console.log('Web->Server->Web : clear_marker');
        clearMarkers();
    }  else if(m.hasOwnProperty('clear_block')) {
        console.log('Web->Server->Web : clear_block');
        toClearBlock();
    }  else if(m.hasOwnProperty('clear_report')) {
        console.log('Web->Server->Web : clear_report');
        clearReports();
    }

});

socket.on('news', function(m) {
    console.log(m.hello);
});

socket.on('usv_update_mqtt_ul', function(m) {
    console.log('From server usv_update_mqtt_ul -----------------');
    m = checkData(m);

    if(m == null) return;

    let target = 0;
    if(m.hasOwnProperty('app_id')) {
        target = statusTarget[m.app_id];
    }

    if(target ===1 || target === 10  ) {
        m.lat = parseFloat(m.lat);
        m.lng = parseFloat(m.lng);
    }

    let mChoice = 0;
    if(m.hasOwnProperty('app_id')) {
        mChoice = statusTarget[m.app_id];
    }

    updateStatus(mChoice, m);
});

socket.on('update_mqtt_ul', function(m) {
    console.log('From server usv_update_mqtt_ul -----------------');
    m = checkData(m);

    if(m == null) return;

    let target = 0;
    if(m.hasOwnProperty('app_id')) {
        target = statusTarget[m.app_id];
    }

    if(target ===1 || target === 10  ) {
        m.lat = parseFloat(m.lat);
        m.lng = parseFloat(m.lng);
    }
    let mChoice = 0;
    if(m.hasOwnProperty('app_id')) {
        mChoice = statusTarget[m.app_id];
    }

    updateStatus(mChoice, m);
});

socket.on('http_report_data', function(m) {
    console.log('From server : http_report_data ------');
    m = checkData(m);

    if(m == null) return;

    let mChoice = 0;
    if(m.hasOwnProperty('app_id')) {
        mChoice = statusTarget[m.app_id];
    }
    updateStatus(mChoice, m);

});

socket.on('usv_specified_target', function(m) {
    console.log('From server : usv_specified_target ------');
    m = checkData(m);

    if(m == null) return;

    app.isRun = false;
    window.setTimeout(function () {
        if(m.key === 3) {
            stop();
            alert('無人船已到達指定目標\n你可以使用遠端控制!');
            //app.toViewControl();
        } else if(m.key === 2) {
            alert('自動巡航完成!');
        } else if(m.key === 95) {
            cancelHomeCheck();
            app.isSetHome = true;
            alert('無人船設定HOME點完成!');
        }
    }, 500);
});

function checkData(m) {
    console.log('From server : usv_specified_target ---------');
    let data = null;
    if (typeof m === 'string') {
        data = JSON.parse(m);
    } else {
        data = m;
    }

    console.log(data);
    if(data.macAddr !== device_mac) {
        console.log('receive '+ data.macAddr + ', device: '+device_mac + ' is different device');
        return null;
    }
    return data;
}

function updateStatus(mChoice, m) {
    app.status[mChoice] = JSON.parse(JSON.stringify(m));

    if(mChoice ===3) {
        $("#usv_direction").rotate(statusObj[mChoice]['key3']);
    }

    if(mChoice ===10) {
        app.location.lat = m.lat;
        app.location.lng = m.lng;
        //app.cmdMessage = '更新定位座標';
        window.setTimeout(function () {
            app.cmdMessage = '';
        }, 3000);
    }

    if(mChoice ===1) {

        if(reportCount>0) {
            let number = reportCount-1;
            infowindow2[number].close(number);
        }
        placeReport(m, app.isOpenWindow);

        let msg = '';

        if(app.hasOwnProperty('home') === true) {
            if (app.parameter.set.trigger1 !== null) {
                if (parseInt(app.parameter.set.trigger1) >= parseInt(m.key5)) {
                    msg = '警告:左電池電力過低!';
                }
            }
            if (app.parameter.set.trigger2 !== null) {
                if (parseInt(app.parameter.set.trigger1) >= parseInt(m.key6)) {
                    if (msg.length > 0) {
                        msg = msg + ' 右電池電力過低!';
                    } else {
                        msg = '警告:右電池電力過低!';
                    }

                }
            }
        }
        if(msg.length>0) {
            app.alertMessage = msg;
        }
    }
}

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu0) {
        app.isShow = 0;
    } else if(x === menu1) {
        app.isShow = 1;
    }
});
