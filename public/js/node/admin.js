let table;
let max = 8;
let type = 2;//type:1 輸出所有欄位 type:2 輸出選擇欄位
let toParse = false;

if(user.role_id < 9) {
    toParse = true;
}

let mkey = [];
let mParse = [];
for(let j=0;j<max;j++) {
    mkey.push('');
}

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
        if(labeObj[key] !== undefined)
            arr.insert(i, labeObj[key]);
        else
            arr.insert(i, '');
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

let myapp = {
    'id'         : 0,
    'name'       : '',
    'macAddr'    : targetMac,
    'key_label'  : {},
    'key_parse'  : {},
    'fieldList'  : JSON.parse(JSON.stringify(fields))
}

let app = new Vue({
    el: '#app',
    data: {
        isDebug: true,
        isNew: false,
        isParse: toParse,
        editPoint: -1,
        delPoint: -1,
        myTitle: 'My Apps',
        appObj: myapp,
        appList: apps,
        tempObj: {},
        sendLabel: '',
        sendParse: '',
    },
    methods: {
        newCheck: function () {
            $('html,body').animate({scrollTop: 110}, 333);
            this.isNew = true;
            this.appObj = JSON.parse(JSON.stringify(myapp));
        },
        back: function () {
            this.isNew = false;
            this.editPoint = -1;
            $('html,body').animate({scrollTop:0}, 333);
        },
        toSubmit: function () {
            //$.LoadingOverlay("show");
            if(this.checkValue()) {
                //Jason for avoid label & parse data not ready
                setTimeout(function () {
                    $.LoadingOverlay("show");
                    document.getElementById('editForm').submit();
                }, 500);
            } else {
                alert('欄位未進行設定!!!');
            }
        },
        checkValue: function () {
            let labelObj = {};
            let parseObj = {};
            let keyCount = 0;
            let parseCount = 0;
            this.appObj.fieldList.forEach( (item, index) => {

                if(item.key !== '')
                    keyCount++;

                if(item.parse !== '')
                    parseCount++;

                let target = 'key'+(index+1);
                if(item.check ) {
                    labelObj[target] = item.key;
                    parseObj[target ] = item.parse;

                } else if(type === 1) {//Out all field
                    labelObj[target] = '';
                    parseObj[target] = '';
                }
            });
            if(keyCount === 0) return false;
            this.sendLabel = JSON.stringify(labelObj);
            if(parseCount === 0) {
                this.sendParse = null;
            } else {
                this.sendParse = JSON.stringify(parseObj);
            }

            console.log('checkValue ---------');
            console.log(this.sendLabel);
            console.log(this.sendParse);
            return true;
        },
    }
});

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
    table = $("#table1").dataTable(opt);
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
    table.$('td').click(function() {
        let colIndex = $(this).parent().find("td").index($(this)[0]);
        let index = $(this).parent().parent().find("tr").index($(this).parent()[0]);
        if(colIndex == 4) return;
        let app = apps[index];
        let newUrl = "/node/apps/reports?app_id="+app.id;
        //alert(newUrl);
        document.location.href=newUrl ;
    });
} );
