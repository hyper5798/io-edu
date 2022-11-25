let timeoutID = 0;
let setTime = 50;
let target = {};
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
let myProgress = 0;

for(let i = 0; i<missions.length; i++){
    let tmp = missions[i];
    let tmp2 = empty[i];
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
        tmp['start_at'] = getTime(tmp['start_at']);
        tmp['gamePass'] = startBg;
    } else if(tmp['start_at'] !== '' && tmp['end_at'] !== '') {
        tmp['start_at'] = getTime(tmp['start_at']);
        tmp['end_at'] = getTime(tmp['end_at']);
        tmp['gamePass'] = endBg;
    }

    if(i === (sequence-1) ) {
        if(typeof status === 'string')
            status = parseInt(status);
        if(status === 3)
            tmp['gamePass'] = passBg;
        else if(status === 4)
            tmp['gamePass'] = failBg;
        else if(status === 6)
            tmp['gamePass'] = emergencyBg;
    }
    macList.push(tmp.macAddr);
}

console.log('firstMac :'+firstMac);
console.log(target);
console.log(macList);

let app = new Vue({
    el: '#app',
    data: {
        missionList: JSON.parse(JSON.stringify(missions)),
        total_time: pass_time/60,
        sequence: sequence,
        status: status,
        reduce_time: reduce,
        currentMac: actionMac
    },
    methods: {
        back: function () {

            let newUrl = "teamRecords?rank_tab="+from+'&page='+page;
            document.location.href = newUrl;
        },
    }
});

function getTime(obj) {
    let time = new Date(obj);
    let h = time.getHours();
    let m = time.getMinutes();
    let s = time.getSeconds();
    return h+ '時:'+ m + '分:' +s +'秒'
}

function getDiff(start_time, end_tme) {
    let new1 = Date.parse(start_time)
    let new2 = Date.parse(end_tme)
    //最小整數
    let timestamp = Math.ceil((new2-new1)/1000)
    //Math.floor() 最大整數
    //let timestamp = Math.floor((new2-new1)/1000)
    return timestamp
}
