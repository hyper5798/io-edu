let table;
let test = true;//false: lat,lng to gauge and charge, true: lat,lng only for map
let rangeMax = 3;
let keys = Object.keys(labelObj);
let values = Object.values(labelObj);
let opt= csvOpt;
opt.oLanguage = twLan;
let myChart = null;
let map;
let icon;
let check = [];
let points = [];
let infoWindow = [];
let pointCount = 0;
let isReportUpdate = false;
let showMap = false;

let debug = true;
let settingObject = null;
if(settings.length > 0) {
    settingObject = {};
    settings.forEach(function(item){
        settingObject[item.field] = JSON.parse(JSON.stringify(item));
    });
}

let dateOption = {
    format: "yyyy-mm-dd",
    autoclose: true,
    // startDate: "today",
    clearBtn: true,
    calendarWeeks: true,
    todayHighlight: true,
    language: 'zh-TW',
    startView: "days",
    minViewMode: "days"
};

let range = [
    [0.2, '#41FF0D'],
    [0.8, '#1e90ff'],
    [1  , '#ff4500']
];

let setJson = {
    choice: 0,
    min: '',
    max: '',
    unit: '',
    range: JSON.parse(JSON.stringify(range))
};

let empty = {
    id: 0,
    device_id: data.device.id ,
    field: '',
    recv: '',
    set: JSON.parse(JSON.stringify(setJson ))
};

let gChoices = [
    {id:0 , value: '儀表圖'},
    {id:1 , value: '折線圖'},
    {id:2 , value: '柱狀圖'}
];


let arr = [];
let percentOptions = [];

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};

function init() {
    if(test) {
        let gaugeObj = JSON.parse(JSON.stringify(labelObj));
        delete gaugeObj['lat'];
        delete gaugeObj['lng'];
        keys = Object.keys(gaugeObj);
        values = Object.values(gaugeObj);
    }

    if(labelObj.hasOwnProperty('lat')) {
        showMap = true;
    }

    for(let i = 0; i < keys.length; i++) {
        let item = {
            gaugeChart: '',       // chart 对象实例
            id: 'id' + i,       // 为了标示 id 不同
            name: values[i],
            recv: ''
        };
        if(i==0) {
            item.unit = '度';
        } else {
            item.unit = '%';
        }
        arr.push(item);
    }
    //console.log(arr);
    percentOptions.insert(0,{"value":0.1, "name": "10%"});
    percentOptions.insert(1,{"value":0.2, "name": "20%"});
    percentOptions.insert(2,{"value":0.3, "name": "30%"});
    percentOptions.insert(3,{"value":0.4, "name": "40%"});
    percentOptions.insert(4,{"value":0.5, "name": "50%"});
    percentOptions.insert(5,{"value":0.6, "name": "60%"});
    percentOptions.insert(6,{"value":0.7, "name": "70%"});
    percentOptions.insert(7,{"value":0.8, "name": "80%"});
    percentOptions.insert(8,{"value":0.9, "name": "90%"});
    percentOptions.insert(9,{"value":1, "name": "100%"});
}

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 18,
        center: {
            lat: 24.001556,
            lng: 121.637998
        },
        panControl:true,
        draggableCursor: 'default'
        //mapTypeId:google.maps.MapTypeId.HYBRID
    });
    for(let i=0;i<app.reportList.length; i++) {
        let report = app.reportList[i];
        if(i === (reports.length-1)) {
            placeReport(report, true);
        } else {
            placeReport(report, false);
        }
    }
}

function placeReport(location, isOpenWindow) {
    let num = pointCount;
    check.splice(num,1,1);
    let loc = new google.maps.LatLng(location.lat, location.lng);
    points[num] = new google.maps.Marker({
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

    infoWindow[num] = new google.maps.InfoWindow({
        content: '緯度: ' + getFixNumber(location.lat )+
            '<br>經度: ' + getFixNumber(location.lng)+
            '<br>時間: ' + getTime(location.recv)
    });

    if(isOpenWindow) {
        infoWindow[num].open(map, points[num]);
    }

    points[num].addListener('click',function(){
        check[num] = check[num] * -1;
        if(check[num] < 0){
            infoWindow[num].open(map, points[num]);
        }else{
            infoWindow[num].close();
        }
    });

    pointCount++;
}

function getFixNumber(num) {
    return parseFloat(num.toFixed(6));
}

//console.log(reports);

init();

let app = new Vue({
    el: '#app',
    data: {
        gaugeList: arr,
        gaugeChoiceList: gChoices,
        settingList: settings,
        reportList: reports,
        isNew: false,
        isRefresh: true,
        showRefreshMsg: false,
        editPoint: -1,
        delPoint: -1,
        tab:1,
        flag:0,
        percentList:percentOptions,
        setting: JSON.parse(JSON.stringify(empty)),
        data: data,
        chartOption:0,//0:both 1,gauge, 2:line chart
        start: start,
        end: end,
        isShowMap:showMap
    },
    mounted() {
        this.initChart();
        this.tab = data.tab;
    },
    methods: {
        initChart() {
            if(this.reportList.length > 0) {
                let dom = document.getElementById("container");
                if(dom) {
                    myChart = echarts.init(dom);
                    let title = data.device.device_name;
                    if(keys.length>0) {
                        let option = getLineOption(title, keys, values, this.reportList);
                        if (option && typeof option === "object") {
                            myChart.setOption(option, true);
                        }
                    }
                }
                let report = this.reportList[(this.reportList.length-1)];
                //console.log(report);
                for(let i = 0; i < keys.length; i++) {
                    let key = keys[i];
                    let set  = null;
                    if(settingObject != null && settingObject[key]!== undefined) {
                        set = settingObject[key].set;
                    }
                    this.gaugeList[i].recv = report.recv;
                    let myOption = getGaugeOption(values[i], report[key], set);
                    this.gaugeList[i].gaugeChart = echarts.init(document.getElementById(this.gaugeList[i].id));
                    this.gaugeList[i].gaugeChart.setOption(myOption, true);
                }
            }
        },
        set: function (index) {
            this.isNew = true;
            this.isRefresh = false;
            let tmpObj = {};
            //let tmpSetting = null;
            let key = keys[index];
            let inx = 0;

            if(settingObject!=null && settingObject[key] !== undefined) {
                this.setting = settingObject[key];
            } else {
                this.setting = JSON.parse(JSON.stringify(empty));
                this.setting.field = key;
            }
            this.setting.set.range.forEach(function(item){
                let myId = 'range'+inx;
                inx++;
                setTimeout(function () {
                    document.getElementById(myId).jscolor.fromString(item[1]);
                }, 500);
            });
        },
        back: function () {
            this.isNew = false;
            myRefreshFunction();
            $('html,body').animate({scrollTop:0}, 333);
        },
        toSubmit: function () {

            //$.LoadingOverlay("show");
            if(this.checkValue()) {
                //Jason for avoid label & parse data not ready
                //alert('toSubmit');
                setTimeout(function () {
                    $.LoadingOverlay("show");
                    document.getElementById('editSetting').submit();
                }, 500);
            } else {
                alert(field_required);
            }
        },
        checkValue: function () {
            /*console.log('this.setting.set.min.length :'+this.setting.set.min.length);
            console.log('this.setting.set.max.length :'+this.setting.set.max.length);
            console.log('tthis.setting.set.unit.length:'+this.setting.set.unit.length);
            */
            if(this.setting.set.min.length === 0 || this.setting.set.max.length === 0 || this.setting.set.unit.length === 0) {

                return false
            }
            this.setting.set = JSON.stringify(this.setting.set);
            return true;
        },
        prePage: function () {
            let newPage = this.data.page - 1;
            let newUrl = "/module/nodeReports?device_id="+this.data.device.id+'&page=' + newPage;
            newUrl = newUrl + '&start='+this.start;
            newUrl = newUrl + '&end='+this.end;
            //alert(newUrl);
            document.location.href = newUrl;
        },
        nextPage: function () {
            let newPage = this.data.page + 1;
            let newUrl = "/module/nodeReports?device_id="+this.data.device.id+'&page=' + newPage+'&tab='+this.tab;
            newUrl = newUrl + '&start='+this.start;
            newUrl = newUrl + '&end='+this.end;
            //alert(newUrl);
            document.location.href = newUrl;
        },
        switchTab: function (num) {
            //alert(num);
            this.chartOption = 0;
            this.tab = num;
            if(isReportUpdate) {
                let newUrl = "/module/nodeReports?device_id="+this.data.device.id+'&page=' + this.data.page +'&tab='+this.tab;
                newUrl = newUrl + '&start='+this.start;
                newUrl = newUrl + '&end='+this.end;
                //alert(newUrl);
                document.location.href = newUrl;
            }
        },
        reload: function () {
            let newUrl = "/module/nodeReports?device_id="+this.data.device.id+'&tab='+this.tab;
            newUrl = newUrl + '&start='+this.start;
            //newUrl = newUrl + '&page=' + this.data.page
            //alert(newUrl);
            document.location.href = newUrl;
        },
        switchRefresh: function () {
            this.isRefresh = !this.isRefresh;
        },
        search() {
            this.start = document.getElementById("start").value;
            this.end = document.getElementById("end").value;
            //alert(this.category);
            let newUrl = '/module/nodeReports?device_id='+this.data.device.id;
            newUrl = newUrl + '&start='+this.start;
            newUrl = newUrl + '&end='+this.end;
            document.location.href = newUrl;

        },
        /*addRange: function () {
            let index = this.tmpSet.range.length;
            if(rangeMax < index +1 ) {
                alert('Range max is 3');
                return;
            }
            let newRange = {};

            newRange = JSON.parse(JSON.stringify(range[index]));

            let mId = 'range' + index;
            newRange.id = mId;
            this.tmpSet.range.insert(index, newRange);

            setTimeout(function () {
                document.getElementById(newRange.id ).innerHTML += '<input data-jscolor="">'
                jscolor.install();
            }, 100);
        },
        delRange: function (index) {
            console.log(this.tmpSet.range[index]);
            if (index > -1) {
                this.tmpSet.range.splice(index,1);
            }
        }*/
        delDataCheck() {
            $('#myModal2').modal('show');
        },
        onChangeChoice(event) {
            alert(event.target.value);
        }
    }
});

$(document).ready(function() {
    if(labelObj.hasOwnProperty('lat')) {
        icon = {
            url: point_url, // url
            scaledSize: new google.maps.Size(4, 4), // scaled size
            origin: new google.maps.Point(0,0), // origin
            anchor: new google.maps.Point(2,2) // anchor
        };
        if(app.reportList.length>0) {
            setTimeout(function() {
                initMap();
            }, 1000);
        }
    }

    table = $("#table1").dataTable(opt);
    //Defined CSV button
    let tableObj = $('#table1').DataTable();
    $("#export").on("click", function() {
        tableObj.button( '.buttons-csv' ).trigger();
    });
    $(".buttons-csv").detach();

    $('.datepicker').datepicker(dateOption);
} );

function toDeleteReports() {
    $('#myModal2').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delReports').submit();
}

let index = 0;
function switchOption() {

    //console.log(index);
    if(index == 0) {
        option = getLineOption();
        index ++;
    } else if(index == 1) {
        option = barOption;
        index ++;
    } else if(index == 2) {
        option = areaOption;
        index ++;
    } else {
        option = gaugeOption;
        index = 0;
    }
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
}

const socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
// 2
socket.on('connect', function()  {
    //socket.emit('web','Web socket is ready');
    socket.emit('storeClientInfo', { customId:data.device.macAddr });
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

socket.on('mqtt_report_data', function(m) {
    console.log('From server ---------------------------------');
    //console.log(typeof m);
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    console.log(m);
    if(m.macAddr === data.device.macAddr) {
        //資料表直接reload
        //if(app.tab === 2 && app.isRefresh === true) {
        if(app.tab === 2) {
            let newUrl = "/module/nodeReports?device_id="+this.data.device.id+'&page=' + this.data.page +'&tab='+this.tab;
            newUrl = newUrl + '&start='+this.start;
            newUrl = newUrl + '&end='+this.end;
            //alert(newUrl);
            document.location.href = newUrl;
            return;
        }

        $.LoadingOverlay("show");
        isReportUpdate = true;
        app.reportList.push(m);
        //剛加入尚未初始化Gauge
        if(app.reportList.length === 1) {
            //需先等元件產生再初始化
            app.$nextTick(() => {
                app.initChart();
                if(labelObj.hasOwnProperty('lat')) {
                    initMap();
                }
                $.LoadingOverlay("hide");
                return;
            });
            return;
        }

        if(m.hasOwnProperty('lat')) {
            if(pointCount>0) {
                let number = pointCount-1;
                infoWindow[number].close(number);
            }

            placeReport(m, true);
        }

        if(m.hasOwnProperty('key1')) {
            //Update gauge
            refreshGaugeData(app.gaugeList , keys, m)

            //Update line chart
            refreshLineData(myChart, keys, m);
        }
        $.LoadingOverlay("hide");
    }

    //table.reload();
    /*if(m.macAddr === data.device.macAddr && app.isRefresh === true) {
        $.LoadingOverlay("show");
        app.reload();
    }*/

});


function getTime(obj) {
    let time = new Date(obj);
    let h = time.getHours();
    let m = time.getMinutes();
    let s = time.getSeconds();
    return h+ '時:'+ m + '分:' +s +'秒'
}
