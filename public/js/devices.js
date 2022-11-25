let table, table2;
//console.log(devices);
//console.log(types);
let userRoomId = {};

let acts = []

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};
types.insert(0,{"type_id":0, "type_name": "不選擇"});
networks.insert(0,{"id":0, "network_name": "不選擇"});
acts.insert(0,{"id":1, "value": "綁定"});
acts.insert(1,{"id":3, "value": "啟用"});

let categoryArr = [
    {"name": "控制型裝置", "value":0},
    {"name": "輸入型裝置", "value":1},
    {"name": "輸出型裝置", "value":2},
    {"name": "輸入及上報型裝置", "value":3},
    {"name": "ALL IN ONE模組控制器", "value":4},
];

let empty = {
    id: 0,
    device_name: '',
    macAddr: '',
    user_id:0,
    type_id: type_id,
    network_id: 1,
    isPublic: 0,
    setting_id: false,
    status: 3,
    product_id: 0,
    updated_at: '',
};

let app = new Vue({
    el: '#app',
    data: {
        typeList: types,
        deviceList: devices,
        networkList: networks,
        actList: acts,
        addList: [
            {"id":0, "value": "不加入"},
            {"id":1, "value": "加入"},
        ],
        tab: 1,
        editPoint: -1,
        delPoint: -1,
        device: JSON.parse(JSON.stringify(empty)),
        categoryList: categoryArr,
        category: category,
        productList: products,
        isSendScript: false,
        alertMessage: '',
        userList: users,
        supportList: supports,
        targetProduct: '',
        isRoom: false,
        roomList: userRooms[user_id],
        selectRoom_id: 0,
    },
    watch:{
        isRoom: function(value) {
           //alert(value);
           if(value === true) {
               if(!userRoomId.hasOwnProperty(this.device.user_id)) {
                   let url = api_url+'/api/search-room';
                   let data = {route: 'search-room', user_id:this.device.user_id , mac:this.device.macAddr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
                   sendToApi(url,data);
               } else {
                   this.selectRoom_id = userRoomId[this.device.user_id];
               }

           }

        }
    },
    methods: {
        changeCategory(){
            //alert(this.category);
            let newUrl = "/node/devices?category="+this.category;
            document.location.href = newUrl;
        },
        newCheck: function () {
            this.tab = 2;
            this.device = JSON.parse(JSON.stringify(empty));
            //console.log(this.cp)
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.tab = 2;
            this.device= JSON.parse(JSON.stringify(this.deviceList[index]));
            //有場域
            if(this.device.setting_id === 1) {
                this.isRoom = true;
            }
            if(this.device.user_id>0) {
                if(userRooms.hasOwnProperty(this.device.user_id)) {
                    this.roomList = userRooms[this.device.user_id];
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
            this.tab = 1;
            this.editPoint = -1;
            this.device = JSON.parse(JSON.stringify(empty));
            this.selectRoom_id = 0;
            this.isRoom = false;
            //console.log(this.userList);
        },
        toSubmit: function () {
            $.LoadingOverlay("show");
            if(this.device.device_name.length === 0) {
                this.device.device_name = this.device.macAddr;
            }
            setTimeout(function () {
                document.getElementById('editForm').submit();
            }, 500);

        },
        print() {
            window.print();
        },
        setScript(index) {

            this.device = JSON.parse(JSON.stringify(this.deviceList[index]));
            if(this.device !== null) {
                toSendScript(this.device.macAddr)
                $.LoadingOverlay("show");
            }
        },
        searchProduct: function () {
            //alert(this.targetProduct);
            let url = api_url+'/api/search-device';

            let data = {route: 'search-device',mac:this.targetProduct , token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
        },
        changeProduct(event) {
            //alert(event.target.value);
            for(let i=0;i<this.productList.length;i++) {
                let product = this.productList[i];
                if(product.macAddr === event.target.value) {
                    this.device.product_id = product.id;
                }
            }
        }

    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delDevice').submit();
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
    table = $("#table1").dataTable(opt);

    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
    for(let i=0;i<devices.length;i++) {
        let id = 'qrcode' + i;
        let code = getCodeObj (id );
        makeCode(code, devices[i]['macAddr']);
    }

} );

function getCodeObj (id) {
    return new QRCode(document.getElementById(id), {
        width : 230,
        height : 230
    });
}

function makeCode (obj, text) {
    //alert(text);
    obj.makeCode(text);
}

function toSendScript(mac) {
    let url = app_url+'/missions/scriptByMac/'+mac;
    app.isSendCmd = true;
    app.alertMessage = '';
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+ token);
        },
        success: function (result) {
            //$.LoadingOverlay("hide");
            window.setTimeout(function () {
                if(result.code == 200){//可以完成設定程序

                    app.alertMessage = '完成設定腳本';
                    app.isSendCmd = false;
                }
            }, 500);
            $.LoadingOverlay("hide");
        },
        error:function(err){
            //$.LoadingOverlay("hide");
            app.cmdMessage = err;
            app.isSendCmd = false;
            $.LoadingOverlay("hide");
        },
    });
    window.setTimeout(function () {
        app.alertMessage ='';
        app.isSendCmd = false;
    }, 5000);
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
            console.log(typeof result);
            if(result.route === 'search-device') {
                if(typeof(result.value) === 'object') {
                    let newUrl = '/node/devices?type_id='+result.value.type_id+'&category='+result.value.category+'&mac='+result.value.macAddr;
                    document.location.href = newUrl;
                } else {
                    alert('無法找到裝置');
                }
            } else if(result.route === 'search-room') {
                if(typeof(result.rooms) === 'object') {
                    app.roomList = result.rooms;
                    app.selectRoom_id = result.room_id;
                    userRooms[app.device.user_id] = JSON.parse(JSON.stringify(app.roomList));
                    userRoomId[app.device.user_id] = app.selectRoom_id;
                }

            }

        },
        error:function(err){
            //app.alertMessage = err;
            alert(err.responseText);
        },
    });
    setTimeout(function(){
        app.isSend = false;
    }, 5000);
}
