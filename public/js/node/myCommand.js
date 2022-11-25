console.log(commands);
let timeoutAlertID = null;

let empty = {
    "id" : 0,
    "sequence" : (commands.length+1),
    "type_id" : type_id,
    "device_id" : device_id,
    "cmd_name" : '',
    "command" : ''
};

let length = 8;
let emptyObj = {};

function initKeyArray() {
    let key = null;
    for(let i=0;i<length;i++) {
        key = "key"+(i+1);
        emptyObj[key] = '';
    }
}

function getCmd() {
    let tmpCmd = app.commandObj.ctlKey;
    for(let i=0;i<length;i++) {
        let tmpKey = 'key'+(i+1);
        if(app.keyObj[tmpKey] !=='') {
            let tmpItem = '&'+tmpKey+'='+ parseFloat(app.keyObj[tmpKey]);
            tmpCmd += tmpItem;
        }
    }
    return tmpCmd;
}

let opt={
    dom:'lBrtip',//隱藏搜尋
    "bLengthChange":false,//隱藏變更長度
    "iDisplayLength": 80,//定義長度
    'paging':false,//取消分頁
    "info": false,   //去掉底部文字
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


$(document).ready(function() {
    table = $("#table1").dataTable(opt);
} );

initKeyArray();

let app = new Vue({
    el: '#app',
    data: {
        isShow: true,
        isNew: false,
        isNewCommand: false,
        isError: false,
        commandList: JSON.parse(JSON.stringify(commands)),
        prompt: '訊息:',
        commandObj: JSON.parse(JSON.stringify(empty)),
        check: false,
        choice: '',
        keyObj: JSON.parse(JSON.stringify(emptyObj)),
        cmd: ''
    },
    watch: {
        'keyObj.key1' : function(value) {
            this.cmd = getCmd();
        },
        'keyObj.key2' : function(value) {
            this.cmd = getCmd();
        },
        'keyObj.key3' : function(value) {
            this.cmd = getCmd();
        },
        'keyObj.key4' : function(value) {
            this.cmd = getCmd();
        },
        'keyObj.key5' : function(value) {
            this.cmd = getCmd();
        },
        'keyObj.key6' : function(value) {
            this.cmd = getCmd();
        },
        'keyObj.key7' : function(value) {
            this.cmd = getCmd();
        },
        'keyObj.key8' : function(value) {
            this.cmd = getCmd();
        },
    },
    methods: {
        newCommand () {
            this.isNew = true;
            this.check = true;
            this.commandObj = JSON.parse(JSON.stringify(empty));
        },
        copyUrl () {
            //Remove parameter id
            //let index = parseInt(id, 10);
            //alert(index);
            this.isShow = true;
            this.prompt = "訊息: 已複製好，可貼上。";
            //let Url = document.getElementById('key'+id);
            let Url = document.getElementById('cmdId');
            Url.disabled=false;
            //Url.value = this.commandList[index]['ctlKey'];
            Url.value = this.cmd;
            Url.select(); // 選擇物件
            document.execCommand("Copy"); // 執行瀏覽器複製命令
            //alert("已複製好，可貼上。");
            Url.disabled=true;

            let obj = this;
            if(timeoutAlertID) {
                clearTimeout(timeoutAlertID);
            }
            timeoutAlertID = setTimeout(function(){
                obj.prompt = '訊息: ';
                //app.isShow = false;
            }, 5000);
        },
        toSendControl () {
            //Remove parameter id
            //let index = parseInt(id, 10);
            this.isShow = true;
            this.prompt = '訊息: ';
            //let obj = document.getElementById('key'+id);
            //obj.value = commands[index]['ctlKey'];
            let obj = document.getElementById('cmdId');
            obj.value = this.cmd;
            let appObj = this;
            let value = obj.value;
            //alert("url:"+value);
            $.get(value,
                function(data){
                    //console.log(data);
                    let status = JSON.stringify(data);
                    appObj.prompt = '訊息: '+status;
                });
            if(timeoutAlertID) {
                clearTimeout(timeoutAlertID);
            }
            timeoutAlertID = setTimeout(function(){
                appObj.prompt = '訊息: ';
                //app.isShow = false;
            }, 5000);
        },
        back() {
            this.isNew = false;
        },
        toSubmit() {
            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editCommand').submit();
            }, 500);
        },
        editCommand(index) {
            console.log(typeof index);
            this.isNew = true;
            this.check = false;
            console.log(this.commandList[index]);
            this.commandObj = JSON.parse(JSON.stringify(this.commandList[index]));
        },
        delCheck() {
            $('#myModal').modal('show');
        },
        toDelete() {
            $.LoadingOverlay("show");
            document.getElementById('delMyCommand').submit();
        },
        find() {
            let newUrl = '/node/myCommand?search='+ this.choice;
            document.location.href = newUrl;
        },
        sendCmdCheck(index) {
            console.log(this.commandList[index]);
            this.commandObj = JSON.parse(JSON.stringify(this.commandList[index]));
            this.cmd = this.commandObj.ctlKey;
            $('#myModal2').modal('show');
        }
     }
});
