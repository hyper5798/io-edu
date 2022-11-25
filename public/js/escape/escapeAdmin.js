
let target = {};
const redBg = {backgroundColor:"#ff1a1a"};
const wraingBg = {backgroundColor:"#ff7611"};
const startBg = {backgroundColor:"#cbcb00"};
const endBg = {backgroundColor:"#90b315"};
const securityBg = {backgroundColor:"#ff7ba9"};
const normalBg = {backgroundColor:"#139d10"};
const openBg = {backgroundColor:"#1727ff"};
const closeBg = {backgroundColor:"#000000"};
const demoBg = {backgroundColor:"#0d7c98"};
let currentMac = null;

function getDiff(start_time, end_tme) {
    let new1 = Date.parse(start_time)
    let new2 = Date.parse(end_tme)
    //最小整數
    let timestamp = Math.ceil((new2-new1)/1000)
    //Math.floor() 最大整數
    //let timestamp = Math.floor((new2-new1)/1000)
    return timestamp
}
let now = new Date().toISOString();
let sMap = {};
//Security device status
for(let i = 0; i<devices.length; i++){
    let tmp = devices[i];
    sMap[tmp.macAddr] = i;

    if(tmp.recv != '') {
        let diff = getDiff(tmp.recv, now);
        if(diff>3600) {
            tmp['bg'] = wraingBg;
            tmp['status'] = data.disconnection;
        } else {
            if(data.mode === 32) {
                tmp['bg'] = securityBg;
                tmp['missionStatus'] = data.sensing;
            } else {
                tmp['bg'] = normalBg;
                tmp['status'] = data.connection;
            }
        }
    } else {
        tmp['bg'] = wraingBg;
        tmp['status'] = data.disconnection;
    }
}

let mMap = {};
let firstMac = '';

//Room missions status
for(let i = 0; i<missions.length; i++){
    let tmp = missions[i];
    mMap[tmp.macAddr] = i;
    if(tmp.sequence === 0) {
        firstMac = tmp.macAddr;
    }

    if(tmp.mission_status) {
        if(tmp.mission_status.statt !== '' && tmp.mission_status.end === '') {
            //tmp['gamePass'] = startBg;
            tmp['missionBg'] = startBg;
            tmp['missionStatus'] = data.mission_start;
        } else if(tmp.mission_status.end !== '') {
            //tmp['gamePass'] = endBg;
            tmp['missionBg'] = endBg;
            tmp['missionStatus'] = data.mission_end;
        }
    } else {
        tmp['missionBg'] = normalBg;
        tmp['missionStatus'] = data.connection;
        tmp['mission_status'] = {team:'無'};
    }

    if(tmp.mac_status) {
        if(tmp.mac_status.start !== '' && tmp.mac_status.end === '') {
            //tmp['gamePass'] = startBg;
            tmp['macBg'] = startBg;
            tmp['macStatus'] = '取物開始';
        } else if(tmp.mac_status.end !== '') {
            //tmp['gamePass'] = endBg;
            tmp['macBg'] = endBg;
            tmp['macStatus'] = '取物結束';
        }
    }

    if(tmp.mac_status === undefined) {
        let diff = getDiff(tmp.recv, now);

        if(diff>1800) {
            tmp['macBg'] = wraingBg;
            tmp['macStatus'] = data.disconnection;
        } else {
            tmp['macBg'] = normalBg;
            tmp['macStatus'] = data.connection;
        }
        tmp['mac_status'] = {team:'無'};
    }

}
//console.log(target);
console.log(mMap);

let app = new Vue({
    el: '#app',
    data: {
        missionList: JSON.parse(JSON.stringify(missions)),
        securityList: JSON.parse(JSON.stringify(devices)),
        mode: data.mode,
        event: data.securityStatus,
        setEndIndex: -1,
        isActive: 1,
    },
    methods: {
        setGame: function() {
            this.mode = 30;
            setMode(this.mode);
        },
        setDemo: function() {
            this.mode = 31;
            setMode(this.mode);
        },
        reset: function() {
            setMode(22);
        },
        openDoor: function() {
            setMode(11);
        },
        /*replay: function() {
            this.status = 1;
            setMode(27);
        },
        standby: function() {
            this.status = 0;
            setMode(28);
        },
        test: function(inx) {
            let myMission = this.missionList[0];
            if(myMission.sequence === 0) return;
            let mUrl = app_url+'/escape/gameTest?room_id=1&sequence='+inx;
            $.ajax({
                url: mUrl,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
                },
                success: function (result) {
                    if(result.code == 200){//可以完成設定程序
                        alert('關卡: '+inx+' 開始測試');
                    } else {
                        alert(result.message);
                    }
                },
                error:function(err){console.log(err)},
            });
        },*/
        setCheck: function(inx){
            this.setEndIndex = inx;
            $('#myModal').modal('show');
        },
        setGameCommand: function() {
            let mac = this.missionList[this.setEndIndex]['macAddr'];
            let mUrl = app_url+'/escape/command?room_id=1&command=24&macAddr='+mac;
            if(this.setEndIndex === 0) {
                mUrl = app_url+'/escape/command?room_id=1&command=21&macAddr='+mac;
            }

            $.ajax({
                url: mUrl,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
                },
                success: function (result) {
                    $('#myModal').modal('hide');
                    window.setTimeout(function () {
                        if(result.code == 200){//可以完成設定程序
                            alert('完成手動設定');
                            app.setEndIndex = -1;
                        }
                    }, 500);

                },
                error:function(err){
                    $('#myModal').modal('hide');
                    alert(err);
                },
            });
        },
    }
});

function setMode(mode) {
    let msg = {token:data.token, mode: mode, room_id:room.id};
    socket.emit('web',msg);
}

const socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
// 2
socket.on('connect', function()  {
    socket.emit('web','Web socket is ready');
});

socket.on('news', function(m) {
    console.log(m.hello);
});

socket.on('token_expire', function(m) {
    if(typeof m === 'string') {
        m = JSON.parse(m);
    }
    let token = m.token;
    if(token && token === data.token) {
        alert('登入時取得tokn時效(一天)已過期,登出後重新登入');
        document.location.href = logout_url;
    }
});

socket.on('update_command_status', function(m) {
    console.log('From server ---------------------------------');
    console.log( m);
    if(typeof m === 'string') {
        m = JSON.parse(m);
    }
    //console.log(m);
    if(typeof m.data === 'string')
        m.data = JSON.parse(m.data);
    if(typeof m.key1 === 'string')
        m.key1 = JSON.parse(m.key1);

    let time = getTime(m.recv);
    let status = null;

    if(m.data)
        status = m.data.key1;
    else
        status = m.key1;

    if(status === 5) {
        alert(m)
    }
    let mac = m.macAddr;
    console.log('mac:'+mac+'-> status :'+status );
    if(typeof status === 'string')
        status = parseInt(status);
    if(sMap.hasOwnProperty(mac)) {
        let inx = sMap[mac];
        let tmp = app.securityList[inx];
        if(status === 30 || status === 20) {
            tmp['bg'] = normalBg;
            tmp['status'] = data.connection;
        }
        return;
    }


    //餘者由以下判斷處理
    if(mMap.hasOwnProperty(mac)){
        //Mission node notify
        let inx = mMap[mac];
        let mission = app.missionList[inx];
        if(status === 1) {//Command from node
            //mission['gamePass'] = startBg;
            currentMac = mac;
            mission['missionBg'] = startBg;
            mission['missionStatus'] = data.mission_start;
            mission['mission_status'] = {team: m.id};
            mission['macBg'] = securityBg;
            mission['macStatus'] = '設定密碼';
            startNotify(inx);
        } else if(status === 2) {//Command from node
            //mission['gamePass'] = endBg;
            currentMac = mac;
            mission['missionBg'] = endBg;
            mission['missionStatus'] = data.mission_end;
            mission['mission_status'] = {team: m.id};
            /*} else if(status === null) {//Command from node
                 mission['bg'] = securityBg;
                 mission['status'] = data.change_password;*/
        } else if(status === 22) {//System reset
            mission['missionBg'] = normalBg;
            mission['missionStatus'] = data.reset;
        } else if(status === 23) {//Command from node
            mission['macBg'] = startBg;
            mission['macStatus'] = '取物開始';
            mission['mac_status'] = {team: m.id};
        } else if(status === 24) {//Command from node
            mission['macBg'] = endBg;
            mission['macStatus'] = '取物結束';
            mission['mac_tatus'] = {team: m.id};
        } else if(status === 26) {//Command from node
            mission['missionBg'] = redBg;
            mission['missionStatus'] = data.emergency_button;
        } else if(status === 25) {//Command from node
            mission['missionBg'] = wraingBg;
            mission['status'] = data.timeout_failure;
        } else if(status === 30) {//Command from node
            mission['missionBg'] = normalBg;
            mission['missionStatus'] = data.game_mode;
        } else if(status === 31) {//Command from node
            mission['missionBg'] = demoBg;
            mission['missionStatus'] = data.demo_mode;
        }
        console.log(mission);
        console.log('test');
    }
});

function getTime(obj) {
    let time = new Date(obj);
    let h = time.getHours();
    let m = time.getMinutes();
    let s = time.getSeconds();
    return h+ '時:'+ m + '分:' +s +'秒'
}

function startNotify(inx) {
    let mission = app.missionList[inx];
    mission['bg'] = securityBg;
    mission['status'] = data.change_password;
    /*window.setTimeout(function () {
        mission['bg'] = startBg;
        mission['status'] = data.mission_start;
    }, 1000);*/
}

