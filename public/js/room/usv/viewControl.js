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
let device_mac = device.macAddr;
let empty1, empty2, empty3;
let emptyVideo = {'id': '', 'name':'', 'url': ''};
let videoDatas = [
    //{'id': 1, 'name':'', 'url': 'http://185.201.9.186:8000/fcf5c455ae68/1.flv'},
    //{'id': 'd2', 'name':'', 'url': 'http://185.201.9.186:8000/live/d2.flv'},
    //{'id': 'd3', 'name':'', 'url': 'http://185.201.9.186:8000/live/d3.flv'},
    //{'id': 'd4', 'name':'','url': 'http://185.201.9.186:8000/live/d4.flv'},
];

let btnEmptySetting = {
    app_id:0,
    field:'btn',
    set: []
};

let myBtnSetting = JSON.parse(JSON.stringify(btnEmptySetting));
if(btn_setting !== null) {
    myBtnSetting = JSON.parse(JSON.stringify(btn_setting));
}
let myVideoSetting = JSON.parse(JSON.stringify(btnEmptySetting));
myVideoSetting.field = 'video';
if(video_setting !== null) {
    myVideoSetting = JSON.parse(JSON.stringify(video_setting));
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

    load();
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
let videoArray = [];

if(myBtnSetting.set !== null && myBtnSetting.set.length>0) {
    btnArray = JSON.parse(JSON.stringify(myBtnSetting.set));
}

if(myVideoSetting.set !== null && myVideoSetting.set.length>0) {
    videoArray = JSON.parse(JSON.stringify(myVideoSetting.set));
}

initPointList();
initReports();

let app = new Vue({
    el: '#app',
    data: {
        isWebRTC:true,
        tab: 1,
        list:  arr,
        centerTab: 1,
        backupList: null,
        center: JSON.parse(JSON.stringify(emptyCenter)),
        status: statusObj,
        label: labelObj,
        videoList: JSON.parse(JSON.stringify(videoArray)),
        btnList: JSON.parse(JSON.stringify(btnArray)),
        myBtn: JSON.parse(JSON.stringify(btnArray[0])),
        btnIndex: 0,
        commandList: commands,
        btnSetting : myBtnSetting,
        videoSetting : myVideoSetting,
        setting: myBtnSetting,
        cmdMessage: '',
        isSendCmd: false,
        myVideo: JSON.parse(JSON.stringify(emptyVideo)),
        isNewVideo: true,
        videoIndex: 0,
        key1: 0,
        key2: 0,
    },
    mounted() {
        window.setTimeout(function () {
            initialize();
        }, 1000);

    },
    watch:{

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
            let url = cmd.url;
            if(this.key1 !== '0') {
                url = url + '&key1='+this.key1;
            }
            if(this.key2 !== '0') {
                url = url + '&key2='+this.key2;
            }
            $.ajax({
                url: url,
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
        addVideo() {
            //this.setting for form post
            //this.videoSetting for video setting
            this.setting = JSON.parse(JSON.stringify(this.videoSetting));
            this.isNewVideo = true;
            let url = media_url+device_mac+'/'+(this.videoList.length+1)+'.flv';
            let rtmp = rtmp_url+device_mac+'/'+(this.videoList.length+1);
            this.myVideo = {'id': (this.videoList.length+1), 'name':'影像'+(this.videoList.length+1), 'url': url, 'rtmp':rtmp};
            this.videoList.splice(this.videoList.length, 1, this.myVideo);
            this.videoIndex = this.videoList.length-1;
            $('#myModal2').modal('show');
        },
        editVideo(inx) {
            //alert(inx);
            this.setting.set = JSON.parse(JSON.stringify(this.videoList));
            this.isNewVideo = false;
            this.videoIndex = inx;
            this.myVideo = this.videoList[inx];
            $('#myModal2').modal('show');
        },
        delVideo() {
            this.videoList.splice(this.videoIndex, 1);
            if(this.videoIndex > (this.videoList.length-1)) {
                this.videoIndex = this.videoList.length-1;
            }
            this.myVideo = JSON.parse(JSON.stringify(this.videoList[this.videoIndex])) ;
        },
        onChangeVideo(event) {
            //alert(event.target.value);
            this.videoIndex = parseInt(event.target.value);
            this.myVideo = this.videoList[this.btnIndex];
        },
        cancelVideoSetting() {
            this.videoIndex = 0;
            this.videoList = JSON.parse(JSON.stringify(myVideoSetting.set));
            if(this.videoList.length>0) {
                this.myVideo = this.videoList[this.videoIndex];
            } else {
                this.myVideo = {'id': 1, 'name':'', 'url': '', 'rtmp':''};
            }
        },
        toVideoSetting(){
            this.videoSetting.set = JSON.stringify(this.videoList);
            this.setting = JSON.parse(JSON.stringify(this.videoSetting));
            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editSetting').submit();
            }, 500);
        },
    }
});

function load() {
    console.log('isSupported: ' + flvjs.isSupported());
    p = 0;
    let mediaSourceURL;
    for(let i=0;i<app.videoList.length;i++) {
        let v = app.videoList[i];
        let url = v.url;

        let mediaDataSource = {
            type: 'flv',
            isLive: true,
        };

        mediaDataSource['url'] = document.getElementById(v.id).value;
        console.log('MediaDataSource', mediaDataSource);
        window.setTimeout(( () =>  flv_load_mds(v.id, mediaDataSource) ), i*50);
    }
}

function flv_load_mds(id, mediaDataSource) {
    let mId = id+'test';
    let element = document.getElementsByName(mId)[0];

    if (typeof playerList[p] !== "undefined") {
        if (playerList[p] != null) {
            playerList[p].unload();
            playerList[p].detachMediaElement();
            playerList[p].destroy();
            playerList[p] = null;
        }
    }
    playerList[p] = flvjs.createPlayer(mediaDataSource, {
        enableWorker: false,
        //lazyLoadMaxDuration: 0,
        //lazyLoadRecoverDuration: 0,
        //deferLoadAfterSourceOpen: false,
        enableStashBuffer: false,
        stashInitialSize: 128,
        //seekType: 'range',
        isLive: true,
    });
    playerList[p].attachMediaElement(element);
    playerList[p].load();
    playerList[p].play();
    p++;
}
