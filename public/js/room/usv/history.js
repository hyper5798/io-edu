let table;
let map;
let a = -1;
let reportLimit = 500;
let dangerBlocks = [];//以設定的危險區塊列表
let reportObj = {max:reportLimit, lastNum:0};//上報點
let measureObj = {max:2, lastNum:0};//量測點物件
let max = 2;
let arr = [];
let reportCount = 0;
let reports = [];
let newBlock = null;
let flightPath = null;
let icon, icon2;
let lastNum = 0;
let emptyLatLng = {'lat': 0,'lng': 0};
let emptyCenter = {'name': '','lat': 24.001556,'lng': 121.637998, 'room':18};
let isOpen = false;//true:定位即顯示info window, false:定位不立即顯示
let device_mac = device.macAddr;
let offset = 3;//公尺
let emptyData = {
    lat: '',
    lng:'',
    recv: ''
};

let skipArr = [{title:"0", value: 0}];

let tmp = [];
tmp.push(emptyData);

let emptySetting = {
    app_id:0,
    field:'center',
    set: []
};

let myCenter = emptyCenter;

let mySetting = (setting == null) ? JSON.parse(JSON.stringify(emptySetting)) : JSON.parse(JSON.stringify(setting));if(mySetting.set.length>0){
    if(center_index > (mySetting.set.length-1)) {
        center_index = mySetting.set.length-1;
    }
    if(center_index !== null) {
        myCenter = mySetting.set[center_index];
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
}

let labelObj, statusTarget;

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};


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
        /*if(app.isMeasure) {
            //量測的定位點
            pointMarker(event.latLng);
        } else {
            //電子圍籬的定位點
            placeMarker(event.latLng);
        }*/
        //量測的定位點
        pointMarker(event.latLng);
    });
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

    /*google.maps.event.addListener(markerobject, 'drag', function(evt){
        console.log("marker is being dragged");
    });*/
}

function clearReports() {
    work_clearAllMakers(reportObj);
    app.searchList = [];
}

function getFixNumber(num) {
    return parseFloat(num.toFixed(6));
}

function toClearLine() {
    flightPath = work_clearLine(flightPath);
    app.distance = 0;

}

function toClearLineMarker(obj) {
    work_clearAllMakers(obj);
}

function getTime(obj) {
    let date = new Date(obj);
    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let day = date.getDate();


    let h = date.getHours();
    let m = date.getMinutes();
    let s = date.getSeconds();
    return month+'/'+day+' '+h+ '時:'+ m + '分:' +s +'秒'
}

initPointList();

//initReports();

let app = new Vue({
    el: '#app',
    data: {
        isDemo: false,
        tab: 1,
        isRun: false,
        isMeasure: false,
        list:  arr,
        isTimeSelect: true,
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
        setString: JSON.stringify(mySetting.set),
        centerIndex: center_index,
        homeIndex: 0,
        isSetHome: false,
        alertMessage: '',
        isSendCmd: false,
        cmdMessage: '',
        searchList: tmp,
        timeList: [1,2,4,12,24],
        timeOption: 24,
        skipList: skipArr,
        skip: 0,
        limit: reportLimit,
        notifyMessage: '最新上報排第一筆'
    },
    mounted() {
        window.setTimeout(function () {
            initialize();
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
            window.setTimeout(function () {
                app.search();
            }, 500);

        }, 1000);

    },
    methods: {
        changeTab(value) {
            this.tab = value;
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
            }
            toClearLineMarker(measureObj);
            this.measureList =[JSON.parse(JSON.stringify(emptyLatLng)), JSON.parse(JSON.stringify(emptyLatLng))];
        },
        measureTool() {
            this.isMeasure = true;
        },
        toHisTory() {
            showOverlay();
            let newUrl = "/room/history/"+device.id;
            document.location.href = newUrl;
        },
        search() {


            clearReports();
            //sendCmd(url);

            let url = api_url+'/api/search-report';
            let data = getData();
            data.skip = this.skip;
            data.limit = this.limit;
            sendToApi(url,data);
        },
        deleteCheck() {
            $('#myModal').modal('show');
        },
        removeReport() {
            $('#myModal').modal('hide');
            clearReports();

            let url = api_url+'/api/remove-report';
            let data = getData();
            sendToApi(url,data);
        },
        changeSkip(event){
            clearReports();
            //alert(event.target.value);
            this.skip = event.target.value;
            let url = api_url+'/api/search-report';
            let data = getData();
            data.skip = this.skip;
            data.limit = this.limit;
            sendToApi(url,data);
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

function getData() {
    let end = document.getElementById('end').value;
    let start = null;

    if(app.isTimeSelect === false) {
        let start = document.getElementById('start').value;
        if(start.length === 0) {
            return alert('尚未設定起始時間!');
        }
    } else {
        start = addHours(app.timeOption, end);
    }

    if(end.length === 0) {
        return alert('尚未設定結束時間!');
    }
    let startTime = new Date(start);
    let endTime = new Date(end);
    let time = (endTime.getTime() -startTime.getTime())/1000;
    if(startTime > endTime) {
        return alert('結束時間不可小於起始時間!');
    }
    if(time>86400) {
        return alert('搜尋範圍不可超過一天!');
    }

    let startStr = formatDate(start);
    let endStr = formatDate(end);

    return {macAddr:device.macAddr, start: startStr, end: endStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};

}

function addHours(hour, dateStr) {
    let newDate = new Date(dateStr);
    newDate.setHours(newDate.getHours()-hour);
    let year = newDate.getFullYear();
    let month = newDate.getMonth() + 1;
    let day = newDate.getDate();
    let h = newDate.getHours();
    let m = newDate.getMinutes();
    let s = newDate.getSeconds();
    return year+'/'+month+'/'+day+' '+h+ ':'+ m + ':' +s;
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
            if(result.check === 'searchReport') {
                if(result.count >0) {
                    app.cmdMessage ='搜尋完成';
                    if(result.count >1000) {
                        app.cmdMessage = '紀錄超過'+app.limit+'選擇下拉選單查看';
                        let len = Math.round(result.count/app.limit);
                        let arr = [];
                        for(let i=0;i<len;i++) {
                            let tmp = {value:i*app.limit};
                            if(i==len-1) {
                                tmp.title = (i*app.limit)+ '^';
                            } else {
                                tmp.title =  i*app.limit+'~'+((i+1)*app.limit);
                            }
                            arr[i] = JSON.parse(JSON.stringify(tmp));
                        }
                        app.skipList = JSON.parse(JSON.stringify(arr));
                    }
                    app.isSendCmd = false;
                    listMark(result.reports);
                } else {
                    app.cmdMessage ='搜尋完成，時間範圍內無上報紀錄!';
                }

            } else if(result.check === 'removeReport') {
                app.skipList = JSON.parse(JSON.stringify(skipArr));
                app.searchList= tmp;
                app.cmdMessage ='完成刪除時間範圍內上報紀錄!';
            }

        },
        error:function(err){
            //app.alertMessage = err;
            showMessage(app,err);
            app.isSend = false;
        },
    });
    window.setTimeout(function () {
        app.cmdMessage ='';
        app.isSendCmd = false;
    }, 5000);
}

function listMark(list) {
    app.searchList = list;
    work_listMark(map, reportObj, app.searchList, false, 'asc');
}

$(document).ready(function() {

    let start = document.getElementById('start');
    let end = document.getElementById('end');
    let now = new Date();
    end.value = formatDate(now);
    start.value = formatDate(addDays(now, -1));


    let yearOption = {
        format: "yyyy-mm-dd hh:ii:00",
        autoclose: true,
        // startDate: "today",
        clearBtn: true,
        calendarWeeks: true,
        todayHighlight: true,
        language: 'zh-TW',
    };

    $('.input-daterange input').each(function() {
        $(this).datetimepicker(yearOption);
    });

    $('#timeselector input').on("change", function() {
        app.changeTab(parseInt(this.id));
    });
} );
