let max = 8;
let checkObj = {};
if(user.role_id < 5 || user.role_id === 14 || user.role_id === 15) {
    max = 10;
}

let mkey = [];
let mParse = [];
for(let j=0;j<max;j++) {
    mkey.push('');
}
/* for controlSetting to array
let controlSettings = [];

Object.keys(data.control_setting).forEach((key) => {
    controlSettings.push(data.control_setting[key])
});*/

function getFieldList(keyList, parseList) {
    let list = [];
    for(let i = 0 ; i<max; i++) {
        let item = {};
        item.key = keyList[i];
        item.parse = parseList[i];
        if(item.key!== undefined && item.key.length > 0) {
            item.check = true;
        } else {
            item.check = false;
        }
        if(i===8 && item.key==='') {
            item.key = '緯度'
        } else if(i===9 && item.key==='') {
            item.key = '經度'
        }
        //item.check = false;
        list.insert(i, item);
    }
    return list;
}

function getkeyList(labeObj) {
    console.log('getkeyList :');
    console.log(labeObj);
    let arr = [];
    //let mKeys = Object.keys(labeObj);
    for(let i = 0 ; i<max; i++) {
        let key = 'key'+ (i+1);
        if(i === 8) {
            key = 'lat';
        } else if(i===9) {
            key = 'lng';
        }

        let value = null;

        if(labeObj[key] !== undefined)
            value = labeObj[key];
        else
            value = '';
        arr.insert(i, value);
    }
    return arr;
}

function getParseList(parseObj) {
    let arr = [];
    console.log('getParseList :');
    console.log(parseObj);
    if(parseObj) {
        //let mKeys = Object.keys(labeObj);
        for(let i = 0 ; i<max; i++) {
            let key = 'key'+ (i+1);
            if(parseObj[key] !== undefined)
                arr.insert(i, parseObj[key]);
            else
                arr.insert(i, '');
        }
    }

    return arr;
}

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};

let fields = getFieldList(mkey, mParse);
let targetMac = '';
if(target)
    targetMac = target.macAddr;

myapp.fieldList = JSON.parse(JSON.stringify(fields));

let operators = [
    {"id": 1, "value": ">"},
    {"id": 2, "value": ">="},
    {"id": 3, "value": "="},
    {"id": 4, "value": "<="},
    {"id": 5, "value": "<"},
    {"id": 6, "value": "<>"},
];

let app = new Vue({
    el: '#app',
    data: {
        dataTab: 1,
        isDebug: false,
        isNew: false,
        isIntro: false,
        editPoint: -1,
        delPoint: -1,
        myTitle: 'My Apps',
        appObj: myapp,
        tempObj: {},
        sendLabel: '',
        sendParse: '',
        myIntro: user.myIntro,
        reportList: getReportChecks(myapp.fieldList, data.report_settings),
        triggerList: JSON.parse(JSON.stringify(data.triggers)),
        operatorList:operators,
        trigger: JSON.parse(JSON.stringify(data.emptyTrigger[0])),
        isCpCustom: true,
        checkList: getChecks(myapp.fieldList, data.triggers),
        targetIndex:0,
        controlSetting:data.control_setting, //for object
        //controlSetting:controlSettings, // for array
        controlSettingMax: data.control_setting_max,
        myKey: data.api_key,
        prompt: '',
        read_url: app_url + '/setting/control/read?api_key=' + data.api_key
    },
    mounted() {
        this.editCheck();
    },
    computed: {
        // 计算属性的 getter
        write_url: function () {
            return this.getUrl();
        }
    },
    methods: {
        editCheck: function () {
            $('html,body').animate({scrollTop:110}, 333);

            this.tempObj = JSON.parse(JSON.stringify(this.appObj));
            let parseArr = null;
            if(typeof this.tempObj.key_label == 'string') {
                this.tempObj.key_label = JSON.parse(this.tempObj.key_label);
            }

            let keyArr = getkeyList(this.tempObj.key_label);
            if(this.tempObj.key_parse == null) {
                parseArr = JSON.parse(JSON.stringify(mParse));
            } else  {
                if(typeof this.tempObj.key_parse == 'string')
                    this.tempObj.key_parse = JSON.parse(this.tempObj.key_parse);
                parseArr = getkeyList(this.tempObj.key_parse);
            }
            let fieldArr = getFieldList(keyArr, parseArr);
            this.tempObj.fieldList = JSON.parse(JSON.stringify(fieldArr));
            this.appObj = JSON.parse(JSON.stringify(this.tempObj));

        },
        toSubmit: function () {
            //$.LoadingOverlay("show");
            if(this.checkValue()) {
                setTimeout(function () {
                    $.LoadingOverlay("show");
                    document.getElementById('updateChannel').submit();
                }, 500);
            }
        },
        checkValue: function () {
            let labelObj = {};
            let parseObj = {};
            let keyCount = 0;
            let parseCount = 0;
            let checkCount = 0;
            if(this.appObj.name.length === 0) {
                alert(name_required);
                return false;
            }
            this.appObj.fieldList.forEach( (item, index) => {
                if(item.check) {
                    checkCount++;
                    if(item.key !== undefined && item.key !== '')
                        keyCount++;

                    if(item.parse !== undefined && item.parse !== '')
                        parseCount++;
                }

                let target = 'key'+(index+1);
                if(index === 8) {
                    target = 'lat';
                } else if(index === 9){
                    target = 'lng';
                }
                if(item.check ) {
                    labelObj[target] = item.key;
                    parseObj[target ] = item.parse;

                } else {//Out all field
                    labelObj[target] = '';
                    parseObj[target] = '';
                }
            });
            if(keyCount === 0 || checkCount !== keyCount) {
                alert(field_required);
                return false;
            }

            this.sendLabel = JSON.stringify(labelObj);
            if(parseCount === 0) {
                this.sendParse = null;
            } else {
                this.sendParse = JSON.stringify(parseObj);
            }

            /*console.log('checkValue ---------');
            console.log(this.sendLabel);
            console.log(this.sendParse);*/
            return true;
        },
        toSave() {
            let arr = [];

            for(let i=0;i<this.reportList.length;i++) {
                let tmp = this.reportList[i];
                if(tmp.check == true) {
                    let str = 'key' + (i+1);
                    arr.splice(arr.length,1, str);
                }
            }
            if(arr.length==0) return alert('未選擇')
            let setStr = JSON.stringify(arr );
            let url = api_url+'/api/save-report-setting';
            let data = {app_id:this.appObj.id, setStr: setStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};

            sendToApi(url,data);
        },
        check (e, inx) {
            //alert(inx+' : '+e.target.checked)
            this.targetIndex = inx;
            let key = 'key' + (inx+1);
            this.triggerList = changeTriggerSetting(this.triggerList, inx, e.target.checked);
        },
        toSetTrigger(inx) {
            //alert(inx);
            this.targetIndex = inx;
            console.log('this.appObj.fieldList :' +this.appObj.fieldList[inx]);
            let obj = this.appObj.fieldList[inx];
            let key = 'key'+(inx+1);
            if(checkObj.hasOwnProperty(key)) {
                this.trigger = checkObj[key];
            } else {
                this.trigger = JSON.parse(JSON.stringify(data.emptyTrigger[0]));
                this.trigger.name = obj.key;
                this.trigger.field = key;
            }

            $('#myModal').modal('show');
        },
        cancelSetting() {
            this.trigger = JSON.parse(JSON.stringify(data.emptyTrigger[0]));
        },
        saveSetting() {
            this.triggerList = updateTriggerSetting(this.triggerList, this.trigger);
            this.checkList= getChecks(this.checkList,this.triggerList);
            checkObj[this.trigger.field] = JSON.parse(JSON.stringify(this.trigger));
            let setStr = JSON.stringify(this.triggerList);
            let url = api_url+'/api/edit-setting';
            let data = {app_id:app.appObj.id, field: 'sensor_trigger', setStr: setStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};

            sendToApi(url,data);
        },
        toSaveAllTrigger() {
            let setStr = JSON.stringify(this.triggerList);
            let url = api_url+'/api/edit-setting';
            let data = {app_id:app.appObj.id, field: 'sensor_trigger', setStr: setStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};

            sendToApi(url,data);
        },
        delSetting() {
            this.triggerList = delTriggerSetting(this.triggerList, this.targetIndex);
            this.checkList[this.targetIndex]['key'] = '新增';
            this.checkList[this.targetIndex]['check'] = false;
            let setStr = JSON.stringify(this.triggerList);
            let url = api_url+'/api/edit-setting';
            let data = {app_id:app.appObj.id, field: 'sensor_trigger', setStr: setStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};

            sendToApi(url,data);
        },
        toSaveControlSetting() {
            let keys = Object.keys(this.controlSetting);
            let check = false;
            for(let i=0; i<keys.length;i++) {
                let item = this.controlSetting[keys[i]];
                if(item.title !== '' && item.value !== '') {
                    check = true;
                }
            }
            if(check===false) return alert('尚未設定任何雙向通道');
            let setStr = JSON.stringify(this.controlSetting);
            let url = api_url+'/api/edit-setting';
            let message = {app_id:app.appObj.id, field: data.control_setting_key, setStr: setStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};

            sendToApi(url,message);
        },
        getUrl:function () {
            let url = app_url + '/setting/control/write?api_key=' + data.api_key;

            Object.keys(this.controlSetting).forEach((key) => {
                let item = this.controlSetting[key];
                if(item.value !== '') {
                    if(item.title === '') {
                        alert(item.key+'尚未設別名! 新增別名設定值後及，請按[更新雙向通道設定]');
                    }
                    url = url + '&' + item.key + '=' + item.value;
                }
            });

            /*for(let i = 0; i<this.controlSettingMax; i++) {
                let item = this.controlSetting[i];
                url = url + '&' + item.key + '=' + item.value;
            }*/
            return url;
        },
        copyUrl:function (id) {
            let url = this.getUrl();
            let obj = document.getElementById(id);
            obj.disabled=false;
            //obj.value = url;
            obj.select(); // 選擇物件
            document.execCommand("Copy"); // 執行瀏覽器複製命令
            this.isShow = true;
            obj.disabled=true;
            this.prompt = "訊息: 已複製好，可貼上。";
        },
        copyKey:function (id) {
            this.prompt = '';
            let url = this.myKey;
            let obj = document.getElementById(id);
            obj.disabled=false;
            obj.value = url;
            obj.select(); // 選擇物件
            document.execCommand("Copy"); // 執行瀏覽器複製命令
            this.isShow = true;
            obj.disabled=true;
            this.prompt = "訊息: 已複製好，可貼上。";
        },
        toSenApi:function (id) {
            let obj = document.getElementById(id);
            obj.disabled=false;
            let url = obj.value;
            let appObj = this;
            obj.disabled=true;
            toAjax(appObj, url);
        },
    }
});

function toAjax(obj, url) {
    $.get( url,
        function(data){

            if(data.code === 404) {
                return alert('請先按下更新雙向通道設定，再進行寫入或讀取測試!');
            }
            let status = JSON.stringify(data);
            obj.isShow = true;
            obj.prompt = status;
        });
}

function updateTriggerSetting(list, trigger) {
    if(!checkObj.hasOwnProperty(trigger.field)) {
        list.push(trigger);
    } else {
        for(let i=0;i<list.length;i++) {
            let tmp = list[i];
            if(tmp.field == trigger.field) {
                list[i] = trigger;
            }
        }
    }

    return list;
}

function changeTriggerSetting(list, inx, checked) {
    let key = 'key'+(inx+1);
    for(let i=0;i<list.length;i++) {
        let tmp = list[i];
        if(tmp.field === key) {
            tmp.check = checked;
        }
    }


    return list;
}

function delTriggerSetting(list, inx) {
    let key = 'key'+(inx+1);
    if(checkObj.hasOwnProperty(key)) {
        delete checkObj[key];
    }
    let newList = [];
    for(let i=0;i<list.length;i++) {
        let tmp = list[i];
        if(tmp.field !== key) {
            newList.splice(newList.length, 1, tmp);
        }
    }

    return newList;
}

function getReportChecks(list , reportList) {
    for(let i=0;i<list.length;i++) {
        let tmp =  list[i];
        let key = 'key'+(i+1);
        if(reportList.includes(key)) {
            tmp.check = true;
        } else {
            tmp.check = false;
        }
    }
    return list
}

function getChecks(list , triggerList) {
    let checks = JSON.parse(JSON.stringify(list));
    checkObj = {};
    if(triggerList.length>0) {
        for(let j=0;j<triggerList.length;j++) {
            let tmp = triggerList[j];
            checkObj[tmp.field] = tmp;
        }
    }
    for(let i=0;i<checks.length;i++) {
        let key = 'key'+(i+1);
        if( checkObj.hasOwnProperty(key)) {
            let tmp =  checkObj[key];
            checks[i]['key'] = '編輯 : '+tmp.field+ getOperatorStr(tmp.operator) + ' ' + tmp.value;
            checks[i]['check'] = tmp.check;
        } else if(checks[i]['key'] == '' ) {
            checks[i]['key'] = '新增';
            checks[i]['check'] = false;
        }
    }
    return checks;
}

function getOperatorStr(op) {
    for(let i=0;i<operators.length;i++) {
        let tmp = operators[i];
        if(tmp.id === op) {
            return tmp.value;
        }
    }
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

let msg = document.getElementById("message");
$(document).ready(function() {
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
} );

function sendToApi(url,data) {
    app.isSend = true;
    $('#myModal').modal('hide');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        /*beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
        },*/
        success: function (result) {
            //console.log(result);
            alert('更新成功');
        },
        error:function(err){
            //app.alertMessage = err;
            //showMessage(app,err);
            alert(err.responseText);
            app.isSend = false;
        },
    });
    setTimeout(function(){
        app.isSend = false;
    }, 5000);
}


