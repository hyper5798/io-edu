let timeoutID = 0;
let setTime = 50;
let target = {};
//Game point background
let startBg = {backgroundColor:"#ffff00"};
let endBg = {backgroundColor:"#94b815"};
let passBg = {backgroundColor:"#94b815"};
let emergencyBg = {backgroundColor:"#ff1a1a"};
let failBg = {backgroundColor:"#ff7d35"};
let baseBg = {backgroundColor:"#ffffff"};
let progress = {width: "100%"};
let macList =[];
let firstMac = '';
let actionMac = '';
let empty = JSON.parse(JSON.stringify(missions));
let isAction = false;
let pass_time = room.pass_time;
let count = 0;
let countdown = pass_time;
//Progress bar
let myProgress = 0;
let loop = Math.ceil(pass_time / 100);
let isReset = false;
let isAllCheck = false;
let missionCount = 0;
let saveStartTime = null;

//Date.parse for date string to timestamp
function getDiff(start_time, end_tme) {
    let new1 = Date.parse(start_time)
    let new2 = Date.parse(end_tme)
    //最小整數
    let timestamp = Math.ceil((new2-new1)/1000)
    //Math.floor() 最大整數
    //let timestamp = Math.floor((new2-new1)/1000)
    return timestamp
}

let mMap = {}
for(let i = 0; i<missions.length; i++){

    let tmp = missions[i];
    let tmp2 = empty[i];
    mMap[tmp.macAddr] = i;
    if(i===0) {
        firstMac = tmp.macAddr;
    }

    if(i===sequence) {
        actionMac = tmp.macAddr;
    }
    tmp2['start_at'] = '';
    tmp2['end_at'] = '';
    tmp2['gamePass'] = baseBg;
    if(tmp['start_at'] === '' && tmp['end_at'] === '') {
        tmp['gamePass'] = baseBg;
    } else if(tmp['start_at'] !== '' && tmp['end_at'] === '') {
        saveStartTime = tmp['start_at'];
        tmp['start_at'] = getTime(tmp['start_at']);
        tmp['gamePass'] = startBg;

    } else if(tmp['start_at'] !== '' && tmp['end_at'] !== '') {
        tmp['start_at'] = getTime(tmp['start_at']);
        tmp['end_at'] = getTime(tmp['end_at']);
        tmp['gamePass'] = endBg;
        missionCount ++;
    }

    /*if(i === (sequence-1) ) {
        if(typeof status === 'string')
            status = parseInt(status);
        if(status === 3)
            tmp['gamePass'] = passBg;
        else if(status === 4)
            tmp['gamePass'] = failBg;
        else if(status === 6)
            tmp['gamePass'] = emergencyBg;
    }*/
    macList.push(tmp.macAddr);
}

if(missionCount == missions.length) {
    isAllCheck = true;
}

if(status === 1 || status === 2) {
    isAction = true;
} else {
    isAction = false;
}

if(isAction) {
    let now = new Date().toISOString()
    let diff = getDiff(start_time, now);
    count = diff%loop;
    countdown = pass_time - diff;
    if(countdown>0)  {
        myProgress = Math.round(diff*100/pass_time);
        console.log('myProgress:'+myProgress);
    } else {
        isAction = false;
        myProgress = 100;
        countdown = 0;
    }

}

console.log('firstMac :'+firstMac);
console.log(target);
console.log(macList);

let app = new Vue({
    el: '#app',
    data: {
        missionList: JSON.parse(JSON.stringify(missions)),
        total_time: pass_time/60,
        check_time:countdown, //Countdown time
        progress: myProgress, //Progress bar
        isStart:false,
        sequence: sequence,
        status: status,
        currentMac: actionMac,
        isActive: 2,
    },
    computed: {
        left_time: function() {
            let m = Math.floor(this.check_time/60);
            let s = this.check_time%60;
            return (m + '分: '+ s + ' 秒');
        },
        progress_style: function() {
            return {width: this.progress+ '%'};
        },
    },
    watch:{
        check_time: function(value) {
            if(value === 0) {
                window.clearInterval(timeoutID);
                this.isStart = false;
                this.status = 4;
                for(let k in app.missionList) {
                    let mission = app.missionList[k];
                    //console.log('current k:'+k);
                    //console.log(app.missionList[k]);
                    if(mission.macAddr === this.currentMac) {
                        mission.gamePass = failBg;
                    }
                }
            }
        },
        isStart: function(value) {

            if(value === true) {
                console.log('@@@@@ isStart is true');
                timeoutID = startCount();
            }
        },
    },
    methods: {

    }
});

window.onload=function(){
    if(isAction) {
        app.isStart = true;
    }
}

const socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
// 2
socket.on('connect', function()  {
    socket.emit('web','Web socket is ready');
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

socket.on('update_command_status', function(m) {
    console.log('From server ---------------------------------');
    //console.log(typeof m);
    if(typeof m === 'string') {
        m = JSON.parse(m);
    }
    //console.log(m);
    if(m.data && typeof m.data === 'string')
        m.data = JSON.parse(m.data);

    let time = getTime(m.recv);
    let status = m.data.key1;
    if(typeof status === 'string') status = parseInt(status);

    if(status === 22) {
        if(isReset == false) {
            isReset = true;
            console.log('Reset to default');
            window.setTimeout(function () {
                location.reload();
            }, 500);
        }
        return;
    }

    if(status >= 11) return;//Command no action in real-time room

    if(firstMac === m.macAddr && status === 1 ) {
        location.reload();
        return;
    }

    app.currentMac = m.macAddr;
    app.status = status;

    if(isAllCheck) {
        for(let k in app.missionList) {
            let tmp = app.missionList[k];
            tmp['start_at'] = '';
            tmp['end_at'] = '';
            tmp['gamePass'] = baseBg;
        }
        isAllCheck = false;
    }

    for(let k in app.missionList) {
        let mission = app.missionList[k];
        //console.log('current k:'+k);
        //console.log(app.missionList[k]);

        if(mission.macAddr === app.currentMac) {
            if(status === 1) {
                saveStartTime = m.recv;
                mission.start_at = time;
                mission.gamePass = startBg;
            }
            if(status === 2) {
                mission.end_at = time;
                mission.gamePass = endBg;
                mission.time = getDiff(saveStartTime, m.recv);
            }

            if(status === 3 || status === 4 || status === 6) {
                mission.end_at = time;
                window.clearInterval(timeoutID);
                app.isStart = false;
            }

            if(status === 3)
                mission.gamePass = passBg;

            if(status === 4)
                mission.gamePass = failBg;

            if(status === 6)
                mission.gamePass = emergencyBg;

            break;
        }
    }
});

function getTime(obj) {
    let time = new Date(obj);
    let h = time.getHours();
    let m = time.getMinutes();
    let s = time.getSeconds();
    return h+ '時:'+ m + '分:' +s +'秒'
}



function startCount() {
    return window.setInterval(function() {
        app.check_time = app.check_time -1;
        count++;
        if(count === loop) {//Loop default 60
            app.progress = 100 - Math.round(app.check_time*100/(app.total_time*60));
            count = 0;
        }
    }, 1000);
}

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu2) {
        let newUrl = "/escape/accounts";
        document.location.href = newUrl;
    }
});
