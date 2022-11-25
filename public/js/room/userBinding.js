let table;
let maxRoomLength = 3;
//console.log(devices);
//console.log(types);

let acts = [];
let isFlowCheck = 2;
let keys = Object.keys(deviceUsers);

if(keys.length===0) {
    isFlowCheck = 1;
} else {
    for(let i=0;i<keys.length;i++) {
        if(deviceUsers[keys[i]].length == 1) {
            isFlowCheck = 2;room
        }
    }
}

let users = deviceUsers[keys[0]];

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};
networks.insert(0,{"id":0, "network_name": "不選擇"});
acts.insert(0,{"id":1, "value": "綁定"});
acts.insert(1,{"id":3, "value": "啟用"});

let emptyRoom = {
    id: 0,
    room_name: '',
};

let myRoom = room == null ? emptyRoom : room;


let empty = {
    id: 0,
    device_name: '',
    macAddr: '',
    type_id: 102,
    network_id: 1,
    status: 3,
    updated_at: '',
};

let emptyUser = {
    id: 0,
    name: '',
    email: '',
    role_id: 11,
    active: 1,
    password: '12345678',
    updated_at: '',
};

let app = new Vue({
    el: '#app',
    data: {
        maxRoomLength:maxRoomLength,
        deviceList: devices,
        isNew: false,
        isSend: false,
        editPoint: -1,
        delPoint: -1,
        device: JSON.parse(JSON.stringify(empty)),
        room: myRoom,
        isVerify: false,
        isFlow:  isFlowCheck, //1: 綁定控制器, 2:分享帳戶, 3:分享用戶列表, 4:分享用戶設定
        userList: users,
        user: JSON.parse(JSON.stringify(emptyUser)),
        roomList: JSON.parse(JSON.stringify(rooms)),
        userId:user_id
    },
    methods: {
        setRoom() {
            if(this.room.room_name.length===0) {
                return alert('尚未輸入場域名稱!');
            }
            $.LoadingOverlay("show");
            document.getElementById('editUserRoom').submit();
        },
        newCheck: function () {
            this.isFlow = 1;
            this.isVerify = false;
            this.device = JSON.parse(JSON.stringify(empty));
            //console.log(this.cp)
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.isFlow = 1;
            this.isVerify = true;
            this.device= this.deviceList[index];
            let room_id = deviceRooms[this.device.macAddr];
            for(let i=0;i<this.roomList.length;i++) {
                let tmp = this.roomList[i];
                if(tmp.id === room_id) {
                    this.room = JSON.parse(JSON.stringify(this.roomList[i]));
                    break;
                }
            }
            //console.log('Select index:' + index)
            //console.log(this.device)
        },
        delCheck: function (index) {
            this.delPoint = index;
            //console.log('this.delPoint :' + this.delPoint);
            this.device = this.deviceList[index];
            //console.log('this.cp :' );
            //console.log(this.cp );
            $('#myModal').modal('show');
        },
        back: function () {
            this.isFlow = 2;
            this.editPoint = -1;
            this.device = JSON.parse(JSON.stringify(empty));
            this.room = JSON.parse(JSON.stringify(this.roomList[0]));
            //console.log(this.userList);
        },
        toSubmit: function () {
            if(this.room.room_name.length === 0) {
                alert('尚未輸入場地名稱!');            }
            if(this.device.device_name.length === 0) {
                //return alert('尚未輸入控制器別名!');
                let yes = confirm('尚未輸入控制器別名，以專屬註冊碼做控制器別名？');

                if (yes) {
                    this.device.device_name = this.device.macAddr;
                } else {
                    return;
                }
            }
            $.LoadingOverlay("show");
            setTimeout(function () {
                document.getElementById('editUserDevice').submit();
            }, 500);
        },
        toVerify: function () {
            //$.LoadingOverlay("show");
            let test = this;
            /*$.post(app_url+"/devices/verify",{mac:this.device.macAddr},function(result){
                console.log(result);
                test.isVerify = true;
            });*/
            $.ajax({
                url: app_url+'/devices/verify',
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+token);
                },
                data: {mac:this.device.macAddr},
                success: function (result) {
                    if(result.code == 400) {//未填註冊碼
                        alert('未填專屬註冊碼');
                    } else if(result.code == 403) {//不再產品名單中
                        alert('憑證過期或不正確，請重新登入!');
                    } else if(result.code == 404) {//不再產品名單中
                        alert(test.device.macAddr + '不再產品名單中');
                    } else if(result.code == 405) {//已被綁定
                        console.log(result);
                        let dateStr = getDateString(result.data.created_at);
                        alert('此註冊碼已經由[帳號'+result.data.email+']完成註冊,註冊日期: '+ dateStr);
                    } else if(result.code == 200){//可以完成綁定程序
                        app.device.type_id = result.data.type_id;
                        let yes = confirm('此註冊碼('+test.device.macAddr+')允許註冊,是否繼續完成註冊?');

                        if (yes) {
                            test.isVerify = true;
                        } /*else {
                            test.isFlow = 1;
                        }*/
                    }
                }
            });
        },
        bindingUser(index) {
            this.isFlow = 3;
            //alert(index);
            this.editPoint = index;
            this.isFlow = 3;
            this.device= this.deviceList[index];
            this.userList = deviceUsers[this.device.macAddr];
        },
        addNewUser() {
            this.isFlow = 4;
            this.user = JSON.parse(JSON.stringify(emptyUser));
        },
        editUser(index) {
            this.isFlow = 4;
            this.user = JSON.parse(JSON.stringify(this.userList[index]));
        },
        deleteUser(index) {
            this.isFlow = 3;
            this.user = JSON.parse(JSON.stringify(this.userList[index]));
            $('#myModal').modal('show');
        },
        backUserList() {
            this.isFlow = 3;
        },
        toBindingUser() {
            if(this.user.name.length === 0) {
                return alert('尚未輸入用戶的暱名');
            }
            if(this.user.name.length === 0) {
                return alert('尚未輸入用戶的信箱');
            }
            $.LoadingOverlay("show");
            document.getElementById('bindingUser').submit();
        },
        toDeleteUser() {
            $.LoadingOverlay("show");
            document.getElementById('delBindUser').submit();
        },
        onChangeRoom(event){
            //alert(event.target.value);
            for(let i=0;i<this.roomList.length;i++) {
                let tmp = this.roomList[i];
                if(tmp.id === parseInt(event.target.value)) {
                    this.room = JSON.parse(JSON.stringify(this.roomList[i]));
                    break;
                }
            }
        },

        addRoomCheck() {
            this.room = JSON.parse(JSON.stringify(emptyRoom));
        },
        delRoomCheck() {
            if(this.room.id === 0) {
                alert('尚未選擇場地!');
            }
            $('#myModal2').modal('show');
        },
        toDeleteRoom() {
            $('#myModal2').modal('hide');
            let roomObjectStr = JSON.stringify(this.room);
            let url = api_url+'/api/remove-room';
            let data = {device_id:this.device.id, room_id: this.room.id , token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delUserDevice').submit();
}

 let opt={
  "oLanguage":{"sProcessing":"處理中...",
        "sLengthMenu":"顯示 _MENU_ 項結果",
        "sZeroRecords":"沒有匹配結果",
        "sInfo":"顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
        "sInfoEmpty":"顯示第 0 至 0 項結果，共 0 項",
        "sInfoFiltered":"(從 _MAX_ 項結果過濾)",
        "sSearch":"搜索:",
        "oPaginate":{"sFirst":"首頁",
            "sPrevious":"上頁",
            "sNext":"下頁",
            "sLast":"尾頁"}
    },

};

let msg = document.getElementById("message");
$(document).ready(function() {
    //table = $("#table1").dataTable(opt);
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
} );

function getDateString(str) {
    console.log(str);
    let date = new Date(str);
    let year = date.getFullYear() + '';
    let month = date.getMonth() + 1;
    let day = date.getDate();
    console.log('day :'+day);

    if(month<10)
        month = '0' + month;
    if(day<10)
        day = '0' + day;

    let data = year +''+ month +''+ day;
    return data;
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
            //app.message = result;
            location.reload();
        },
        error:function(err){
            alert(err);

        },
    });
    setTimeout(function(){
        app.isSend = false;
    }, 5000);
}
