let empty = {
    id: 0,
    room_name: '',
    pass_time: 3000,
    cp_id: cp_id,
    user_id: '',
    mac: '',
    work: 'usv',
    type: 'develop',
    isSale: false
};

let room_works = [{'key': 'demo', 'value': '展示'},
                  {'key': 'usv', 'value': '無人船'}];
let room_types = [{'key': 'develop', 'value': '開發版'},
                  {'key': 'module', 'value': '控制模組'}];

let macList = [];
//For all of mission's devices
if(missions.length>0) {
    for(let i=0;i<missions.length;i++) {
        if(missions[i]['sequence'] > 0) {
            macList.push(missions[i]['macAddr']);
        }
    }
}

//For switch room
let room = empty;
if(rooms.length > 0) {
    for(let i=0;i<rooms.length;i++) {
        if(rooms[i]['id'] === room_id) {
            room = rooms[i];
        }
    }
    room.mac = data.mac;
    room.device_id = data.device_id;
}

let emptyMission = {
    id: 0,
    mission_name: '',
    sequence: missions.length+1,
    room_id: room_id,
    user_id: user.id,
    device_id: '',
    macAddr: ''
};

//For missions of room
let tmp = emptyMission;

if(missions.length>0) {
    tmp = JSON.parse(JSON.stringify(missions[0]));
}

let app = new Vue({
    el: '#app',
    data: {
        tab: 1,//Switch mission table and sequence
        target: 1,//Switch room, mission and script
        roomList: JSON.parse(JSON.stringify(rooms)),
        missionList: JSON.parse(JSON.stringify(missions)),
        room: room,
        selected: 0,
        enabled: true,
        list: JSON.parse(JSON.stringify(missions)),
        mission: tmp,
        dragging: false,
        sequence: '',
        workList: JSON.parse(JSON.stringify(room_works)),
        typeList: JSON.parse(JSON.stringify(room_types)),
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
        newRoom: function () {
            this.isEdit = true;
            this.room = JSON.parse(JSON.stringify(empty));
        },
        setRoom: function () {
            this.isEdit = true;
            $.LoadingOverlay("show");
            document.getElementById('editRoom').submit();
        },
        deleteRoom: function () {
            this.target = 1;
            $('#myModal').modal('show');
        },
        checkMove: function(e) {
            window.console.log("Future index: " + e.draggedContext.futureIndex);
        },
        resetSequence: function() {
            if(this.sequence.length === 0) {
                alert(messages.sequence_not_change);
                return;
            }
            $.LoadingOverlay("show");
            document.getElementById('editSequence').submit();
        },
        back: function() {
            this.isEditScript = false;
        },
        toDelete: function() {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");
            document.getElementById('delRoom').submit();
        }
    }
});

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu2) {
        let newUrl = "/room/setMission?target=2&room_id="+room_id+'&cp_id='+cp_id;
        //alert(newUrl);
        document.location.href = newUrl;
    } else if(x === menu3) {
        let newUrl = "/room/setSecurity?target=3&room_id="+room_id+'&cp_id='+cp_id;
        //alert(newUrl);
        document.location.href = newUrl;
    }
});

