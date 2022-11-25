let empty = {
    id: 0,
    room_name: '',
    pass_time: 0,
    cp_id: '',
    user_id: '',
    mac: ''
};

let emptyMission = {
    id: 0,
    mission_name: '',
    sequence: missions.length+1,
    room_id: data.room_id,
    user_id: user.id,
    device_id: '',
    macAddr: ''
};

let emptyScript = {
    id: 0,
    script_name: '',
    mission_id: 0,
    room_id: data.room_id,
    content: '',
    prompt1: '',
    prompt2: '',
    prompt3: '',
    pass: '',
    next_pass: '',
    next_sequence: '',
    note: '',
    image_url: url
};

let macObj = {};

//For all of mission's devices
let inx = 0;
let seq = 0;
if(missions.length>0) {
    for(let i=0;i<missions.length;i++) {
        let tmp = missions[i];
        macObj[tmp.macAddr] = tmp.sequence;
        if(tmp.id === mission_id) {
            inx = i;
            seq = tmp.sequence;
        }
    }
}

//For devices without bind mission -> available
let myDevices2 = JSON.parse(JSON.stringify(available));

//All optional devices
for(let i=0;i<devices.length;i++) {
    let device = JSON.parse(JSON.stringify(devices[i]));
    if(macObj[device.macAddr] !== null && macObj[device.macAddr] === seq) {
        myDevices2.push(device);
    }
}

let emptyDevices = JSON.parse(JSON.stringify(myDevices2));



let missionScript = {};
//For group script for mission
for(let i=0;i<scripts.length;i++) {
    let script = scripts[i];
    if(missionScript[script.mission_id] === undefined) {
        missionScript[script.mission_id] = [];
    }
    missionScript[script.mission_id].push(script);
}

//For missions of room
let tmp = emptyMission;
let tmpScripts = [];
if(missions.length>0) {
    tmp = JSON.parse(JSON.stringify(missions[inx]));
    if(missionScript.hasOwnProperty(tmp.id)) {
        tmpScripts = JSON.parse(JSON.stringify(missionScript[tmp.id]));
    }
}




/*if(user.image_url) {
    url = user.image_url;
}*/

let app = new Vue({
    el: '#app',
    data: {
        image_url: url,
        target: data.target,//Switch room, mission and script
        missionList: JSON.parse(JSON.stringify(missions)),
        deviceList: JSON.parse(JSON.stringify(myDevices2)),
        selected: inx,
        isEditScript: false,
        enabled: true,
        list: JSON.parse(JSON.stringify(missions)),
        mission: tmp,
        scriptList: tmpScripts,
        script: emptyScript,
        isDelScript: false,
        dragging: false,
        sequence: ''
    },
    watch:{
        list: function(value) {
            this.sequence = '';
            let temp = [];
            for(let i=0;i<this.list.length;i++) {
                let item = this.list[i];
                item.sequence = i+1;
                /*let m = {};
                m[item.id] = item.sequence;
                tmp.push(m);*/
                temp.push(item.id);
            }
            this.sequence = JSON.stringify(temp);
        }
    },
    methods: {
        onChangeDevice: function (event) {
            //alert(event.target.value);
            if(this.mission.macAddr === undefined)
                this.mission.macAddr = '';
            for(let i=0;i<this.deviceList.length;i++) {
                let device = this.deviceList[i];
                if(device.macAddr === event.target.value) {
                    this.mission.device_id = device.id;
                    this.mission.macAddr = device.macAddr;
                    break;
                }
            }
        },
        checkMove: function(e) {
            window.console.log("Future index: " + e.draggedContext.futureIndex);
        },
        onChangeMission: function (event) {
            //alert(event.target.value);
            this.isEditScript = false;
            this.mission = JSON.parse(JSON.stringify(missions[event.target.value]));
            let myDevices2 = JSON.parse(JSON.stringify(available));;
            for(let i=0;i<devices.length;i++) {
                let device = JSON.parse(JSON.stringify(devices[i]));
                if(macObj[device.macAddr] !== null && macObj[device.macAddr] === this.mission.sequence) {
                    myDevices2.push(device);
                }
            }
            this.deviceList = JSON.parse(JSON.stringify(myDevices2))
            if(missionScript[this.mission.id])
            this.scriptList = JSON.parse(JSON.stringify(missionScript[this.mission.id]))
        },
        newMission: function() {
            this.mission = JSON.parse(JSON.stringify(emptyMission));
            this.deviceList = JSON.parse(JSON.stringify(available));
        },
        setMission: function () {
            if(this.mission.mission_name === '') {
                //任務名稱不能為空
                return alert(messages.mission_name_required);
            }

            if(this.mission.macAddr === '') {
                //裝置不能為空
                return alert(messages.device_required);
            }

            $.LoadingOverlay("show");
            document.getElementById('editMission').submit();
        },
        deleteMission: function () {
            $('#myModal').modal('show');
        },
        resetSequence: function() {
            if(this.sequence.length === 0) {
                alert(messages.sequence_not_change);
                return;
            }
            $.LoadingOverlay("show");
            document.getElementById('editSequence').submit();
        },
        newScript: function(index){
            //alert(index);
            this.isEditScript = true;
            this.script = JSON.parse(JSON.stringify(emptyScript));
            this.script.room_id = data.room_id;
            this.script.mission_id = this.mission.id;
            if(this.script.next_pass)
                this.script.next_sequence = this.mission.sequence + 1
        },
        editScript: function(index){
            //alert(index);
            this.isEditScript = true;
            this.script = this.scriptList[index];

            this.script.image_url = this.script.image_url ? this.script.image_url :url;
        },
        delScript: function(index){
            //alert(index);
            this.isDelScript = true;
            this.script = this.scriptList[index];
            $('#myModal').modal('show');
        },
        back: function() {
            this.isEditScript = false;
        },
        toSubmit: function() {
            if(this.script.script_name === '') {
                //劇本名稱不能為空
                return alert(messages.script_name_required);
            }
            if(this.script.content === '') {
                //任務內容不能為空
                return alert(messages.mission_content_required);
            }
            if(this.script.pass === '') {
                //通關密語不能為空
                return alert(messages.pass_value_required);
            }

            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editScript').submit();
            }, 100);
        },
        toDelete: function() {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");
            if(this.target === 2) {
                if(this.isDelScript === false)
                    document.getElementById('delMission').submit();
                else
                    document.getElementById('delScript').submit();
            }
        },
        toUpload: function() {
            if(this.script.script_name.length === 0) {
                alert('上傳圖片需先指定劇本名稱');
                return;
            }
            $.LoadingOverlay("show");
            document.getElementById('uploadScriptImage').submit();
        },
    }
});

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu1) {
        let newUrl = "/room/setRoom?target=1&room_id="+data.room_id+'&cp_id='+cp_id;
        //alert(newUrl);
        document.location.href = newUrl;
    } else if(x === menu3) {
        let newUrl = "/room/setSecurity?target=3&room_id="+data.room_id+'&cp_id='+cp_id;
        //alert(newUrl);
        document.location.href = newUrl;
    }
});

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            //$('#preview_progressbarTW_img').attr('src', e.target.result);
            app.script.image_url = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
}

/*$("#customer").on("change", function () {
    readURL(this);
});*/

function custChange(event) {
    // event.currentTarget = select element
    console.log(event.currentTarget);
    readURL(event.currentTarget);
}

