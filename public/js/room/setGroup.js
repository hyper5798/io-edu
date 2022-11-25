let table;
console.log(groups);
let opt= csvOpt;
let editFlag = false;
let empty = {
    id: 0,
    name: '',
    cp_id: cp_id,
    room_id: '',
    mission_id: ''
};
let myGroup = JSON.parse(JSON.stringify(empty));
//opt.oLanguage = twLan;
let roomFlag = false;
let missionFlag = true;
function init() {
    if(groups && groups.length === 0) {
        editFlag = true;
    } else {
        for(let i=0;i<groups.length;i++) {
            if(groups[i]['id'] === group_id) {
                myGroup = groups[i];
            }
        }
        editFlag = false;
        if(myGroup.mission_id == null) {
            roomFlag = true;
            missionFlag = false;
        }
    }
}

init();

let app = new Vue({
    el: '#app',
    data: {
        groupList: groups,
        roomList: rooms,
        missionList: missions,
        isEdit: editFlag,
        isRoom: roomFlag,
        isMission: missionFlag,
        isCancel: false,
        editPoint: -1,
        delPoint: -1,
        group: myGroup,
        selected: 0,
    },
    watch: {
        isRoom: function (value) {
            this.isMission = !value;
        },
        isMission: function (value) {
            this.isRoom = !value;
        }
    },
    methods: {
        onChange: function (event) {
            this.cp = this.cpList[event.target.value];
        },

        newCheck: function () {
            this.isEdit = true;
            this.isMission = true;
            this.isCancel = true;
            this.group = JSON.parse(JSON.stringify(empty));
            //console.log(this.cp)
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.isEdit = true;
            this.group = this.groupList[index];
        },
        cancel: function () {
            this.isLinkRoom = false;
            this.isEdit = false;
            this.isCancel = false;
            this.group = this.groupList[0];
            if(this.group.room_id)
                this.isRoom = true;
            else
                this.isRoom = false;

        },
        toSubmit: function () {
            if(this.group.name.length === 0) {
                alert('名稱不能為空')
                return;
            }
            if(this.group.role_id === '' && this.group.mission_id === '' ) {
                alert('尚未選擇群組管理(場域或任務)');
                return;
            }
            if(this.isRoom) {
                this.group.mission_id = '';
            } else {
                this.group.room_id = '';
            }
            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editGroup').submit();
            }, 200);
        },
        editGroup: function (index) {
            //alert(index);
            this.editPoint = index;
            this.isEdit = true;
            this.group = this.groupList[index];
        },
        delGroup: function (index) {
            $('#myModal').modal('show');
        },
        toDelete: function () {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");
            document.getElementById('delGroup').submit();
        }
    }
});

$(document).ready(function() {
    table = $("#table1").dataTable(opt);
} );

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu1) {
        let newUrl = "/room/setCp";
        document.location.href = newUrl;
    } else if(x === menu3) {
        let newUrl = "/room/accounts?cp_id="+cp_id;
        document.location.href = newUrl;
    }
});

