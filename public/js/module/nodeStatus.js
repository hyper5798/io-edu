let selectController = [];
let setAck = '設定完成';
let actionAck = '裝置動作';
let triggerAck = '裝置觸發';

let filter = [];

if(controller_mac && controller_mac.length>0)
    filter.push(controller_mac);
if(selectNode.inputs && selectNode.inputs !== null) {
    filter = filter.concat(selectNode.inputs);
}
if(selectNode.inputs && selectNode.outputs !== null) {
    filter = filter.concat(selectNode.outputs);
}
//console.log(filter);

if(controllers.length>0 && controller_mac != null) {
    for(let i=0;i<controllers.length;i++) {
        let controller = controllers[i];
        if(controller.macAddr === controller_mac) {
            controller.script_time = getTime(controller.script_time);
            selectController.push(JSON.parse(JSON.stringify(controller)));
        }
    }
}

let inObj = {};
for(let i=0;i<nodeInputs.length;i++) {
    let device = nodeInputs[i];
    device.command_time = getTime(device.command_time);
    if(parseInt(device.command)  === 43) {
        device.command = setAck;
    } else if(parseInt(device.command)  === 45 || parseInt(device.command)  === 46) {
        device.command = triggerAck;
    }
    inObj[device.macAddr] = JSON.parse(JSON.stringify(device));
}

let outObj = {};
for(let i=0;i<nodeOutputs.length;i++) {
    let device = nodeOutputs[i];
    device.command_time = getTime(device.command_time);
    if(parseInt(device.command)  === 43) {
        device.command = setAck;
    } if(parseInt(device.command)  === 44) {
        device.command = actionAck;
    }
    outObj[device.macAddr] = JSON.parse(JSON.stringify(device));
}


let macList = [];


let app = new Vue({
    el: '#app',
    data: {
        isEdit: false,
        list1: selectController,
        list2: inObj,//add for inputs
        list4: outObj,//add for outputs
    },
    methods: {
    }
});

function arr_diff (a1, a2) {

    let a = [], diff = [];

    for (let i = 0; i < a1.length; i++) {
        a[a1[i].id] = a1[i];
    }

    for (let i = 0; i < a2.length; i++) {
        if (a[a2[i].id]) {
            //console.log('no change : '+a2[i].id);
            delete a[a2[i].id];
        } else {
            //console.log('remove : '+a2[i].id);
            a[a2[i].id] = a2[i];
        }
    }

    for (let k in a) {
        diff.push(a[k]);
    }
    // console.log('increased : ');
    // console.log(diff);
    return diff;
}

function compare (a1, a2) {

    let a = [], diff = [];

    for (let i = 0; i < a1.length; i++) {
        a[a1[i].id] = a1[i];
    }

    for (let i = 0; i < a2.length; i++) {
        if (a[a2[i].id]) {
            //console.log('不變的 : '+a2[i].id);
            //delete a[a2[i].id];
        } else {
            diff.push(a2[i].id);
        }
    }

    // console.log('變更的 : ');
    // console.log(diff);
    return diff;
}

let msg = document.getElementById("message");
$(document).ready(function() {
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
} );


$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu2) {
        let newUrl = "/module/nodeDevice";
        document.location.href = newUrl;
    } else if(x === menu1) {
        let newUrl = "/module/nodeFlow";
        document.location.href = newUrl;
    }
});

function getTime(obj) {
    if(obj==='') return '';
    let time = new Date(obj);
    let h = time.getHours();
    let m = time.getMinutes();
    let s = time.getSeconds();
    return h+ '時'+ m + '分' +s +'秒'
    //return h+ '時:'+ m + '分';
}

let socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
// 2
socket.on('connect', function()  {
    socket.emit('web','Web socket is ready');
});

socket.on('news', function(m) {
    console.log(m.hello +' to '+ m.customId);
    socket.emit('storeClientInfo',m);
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

socket.on('update_node_status', function(m) {
    console.log('From server ---------------------------------');
    console.log( m);
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    let mac = m.macAddr;
    if(filter.includes(mac) !== true) return;

    //console.log(m);
    if (typeof m.data === 'string')
        m.data = JSON.parse(m.data);
    if (typeof m.key2 === 'string')
        m.key2 = JSON.parse(m.key2);

    let command = null;

    if (m.hasOwnProperty('data'))
        command = m.data.key1;
    else if (m.hasOwnProperty('key2'))
        command = m.key2;
    else if (m.hasOwnProperty('command'))
        command = m.command;

    console.log(m.macAddr + ':' + status);
    if(parseInt(command) === 93) {
        app.list1[0].script_id = m.script_id;
        app.list1[0].script_time = getTime(m.script_time) + '設定';
    }
    let comString = '';
    if(parseInt(command) === 43) {
        comString = setAck;
    } else if(parseInt(command) === 44) {
        comString = actionAck;
    }  else if(parseInt(command) === 45 || parseInt(command) === 46) {
        comString = triggerAck;
    }

    if(app.list2.hasOwnProperty(mac)) {
        //app.list2[mac].command = command;
        app.list2[mac].command = comString;
        app.list2[mac].command_time = getTime(m.command_time);
    } else if(app.list4.hasOwnProperty(mac)) {
        //app.list4[mac].command = command;
        app.list4[mac].command = comString;
        app.list4[mac].command_time = getTime(m.command_time);
    }
    /*window.setTimeout(function () {
        app.message = '';
    }, 5000);*/
});
