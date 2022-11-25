let table;
let max = 8;
if(user.role_id < 5 || user.role_id === 14 || user.role_id === 15) {
    max = 10;
}
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

let len = (apps !==null && apps.length >0) ? (apps.length+1) : 1;

let myapp = {
    'id'         : 0,
    'name'       : '',
    'macAddr'    : targetMac,
    'key_label'  : {},
    'key_parse'  : {},
    'sequence'   : len,
    'fieldList'  : JSON.parse(JSON.stringify(fields))
}

let app = new Vue({
    el: '#app',
    data: {
        isDebug: false,
        isNew: false,
        isParse: toParse,
        isIntro: false,
        editPoint: -1,
        delPoint: -1,
        myTitle: 'My Apps',
        appObj: myapp,
        appList: apps,
        tempObj: {},
        sendLabel: '',
        sendParse: '',
        myIntro: user.myIntro
    },
    mounted() {
        if(app_id !=0) {
            this.isNew = true;
            let index = -1;
            for(let i=0; i<this.appList.length;i++) {
                let item = this.appList[i];
                if(item.id === app_id ) {
                    index = i;
                }
            }
            if(index != -1) {
                this.editCheck(index);
            }
        }
    },
    methods: {
        newCheck: function () {
            $('html,body').animate({scrollTop:110}, 333);
            this.isNew = true;
            this.appObj = JSON.parse(JSON.stringify(myapp));
        },
        editCheck: function (index) {
            $('html,body').animate({scrollTop:110}, 333);
            this.editPoint = index;
            this.isNew = true;
            this.tempObj = this.appList[index];
            if(this.isDebug) {
                console.log('select app:');
                console.log(this.tempObj);
                console.log('key label type:');
                console.log(typeof this.tempObj.key_label);
                console.log(this.tempObj.key_label);
                console.log('key parse type:');
                console.log(typeof this.tempObj.key_parse);
                console.log(this.tempObj.key_parse);
            }
            let keyArr = null;
            let parseArr = null;
            if(typeof this.tempObj.key_label == 'string')
                this.tempObj.key_label = JSON.parse(this.tempObj.key_label);
            keyArr = getkeyList(this.tempObj.key_label);

            if(this.tempObj.key_parse == null) {
                parseArr = JSON.parse(JSON.stringify(mParse));
            } else  {
                if(typeof this.tempObj.key_parse == 'string')
                    this.tempObj.key_parse = JSON.parse(this.tempObj.key_parse);
                parseArr = getkeyList(this.tempObj.key_parse);
            }
            if(this.isDebug) {
                console.log('keyArr:');
                console.log(keyArr);
                console.log('parseArr:');
                console.log(parseArr);
            }

            let fieldArr = getFieldList(keyArr, parseArr);
            this.tempObj.fieldList = JSON.parse(JSON.stringify(fieldArr));

            this.appObj = JSON.parse(JSON.stringify(this.tempObj));

            console.log('Select index:' + index);
            console.log(keyArr);
            console.log(parseArr);
            console.log(this.appObj)
        },
        delAppCheck: function (index) {
            this.delPoint = index;
            this.appObj = this.appList[index];
            $('#myModal').modal('show');
        },
        delDataCheck: function (index) {
            this.delPoint = index;
            this.appObj = this.appList[index];
            $('#myModal2').modal('show');
        },
        back: function () {
            if(app_id !=0) {
                let newUrl = "/node/apps/reports?app_id="+app_id;
                document.location.href=newUrl;
                return;
            }
            this.isNew = false;
            this.editPoint = -1;
            $('html,body').animate({scrollTop:0}, 333);
        },
        toSubmit: function () {
            //$.LoadingOverlay("show");
            if(this.checkValue()) {
                setTimeout(function () {
                    $.LoadingOverlay("show");
                    document.getElementById('editForm').submit();
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

                } else if(type === 1) {//Out all field
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

            console.log('checkValue ---------');
            console.log(this.sendLabel);
            console.log(this.sendParse);
            return true;
        },
        updateApiKey() {

            let url = app_url + '/node/updateApiKey?api_key=' + app.appList[0].api_key;
            //console.log(url);
            toAjax(url);
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delForm').submit();
}

function toDeleteReports() {
    $('#myModal2').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delReports').submit();
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
    table = $("#table1").dataTable(opt);
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
    /*table.$('td').click(function() {
        let colIndex = $(this).parent().find("td").index($(this)[0]);
        let index = $(this).parent().parent().find("tr").index($(this).parent()[0]);
        if(colIndex == 3) return;
        let app = apps[index];
        let newUrl = "/node/apps/reports?app_id="+app.id;
        //alert(newUrl);
        document.location.href=newUrl ;
    });*/
} );

function toAjax(url) {
    //alert(url);
    $.ajax({
        url: url,
        type: 'GET',
        /*beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
        },*/
        success: function (result) {
            console.log(result);
            alert('ok');

        },
        error:function(err){
            alert('fall');
        },
    });
}