let table;
let max = 8;
let debug = false;
let toParse = true;

let categorys = [
    {"id": 0, "value": "控制型控制器"},
    {"id": 1, "value": "輸入型控制器"},
    {"id": 2, "value": "輸出型控制器"},
    {"id": 3, "value": "輸入型上報控制器"},
    {"id": 4, "value": "All-IN-ONE模組控制器"}
];

/*if(user.role_id < 9) {
    toParse = true;
}*/
if(debug)
    console.log(types);
let mField = [];
let mParser = [];

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};

for(let j=0;j<max;j++) {
    mField.push('');
    mParser.push(['','', '']);
}

function getFieldList(fieldObj) {
    if(debug) {
        console.log('getFieldList  fieldObj:');
        console.log(fieldObj);
    }

    let arr = [];
    for(let i = 0 ; i<max; i++) {
        let key = 'key'+ (i+1);
        if(fieldObj[key] !== undefined) {
            arr.insert(i, fieldObj[key]);
            //alert(arr);
        } else {
            arr.insert(i, '');
        }
    }
    //alert(arr);
    return arr;
}

function getParserList(parserObj) {
    let arr = [];
    if(debug) {
        console.log('getParseList parserObj:');
        console.log(parserObj);
    }

    if(parserObj) {
        for(let i = 0 ; i<max; i++) {
            let key = 'key'+ (i+1);
            if(parserObj[key] !== undefined)
                arr.insert(i, parserObj[key]);
            else
                arr.insert(i, [null,null,'']);
        }
    }

    return arr;
}

function getList(keyList, parseList) {
    if(debug) {
        console.log('getList keyList:');
        console.log(keyList);
        console.log('getList parseList:');
        console.log(parseList);
    }

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

let fields = getList(mField, mParser);

let empty = {
    id: 0,
    type_id: '',
    type_name: '',
    description: '',
    image_url: url,
    fields: {},
    rules: {},
    category: 0,
    updated_at: '',
};

let app = new Vue({
    el: '#app',
    data: {
        typeList: types,
        isNew: false,
        isNewRule: false,
        isError: false,
        isParse: toParse,
        editPoint: -1,
        delPoint: -1,
        type: JSON.parse(JSON.stringify(empty)),
        keys: [],
        newAttribute: '',
        typeString:'',
        fieldList  : JSON.parse(JSON.stringify(fields)),
        categoryList: categorys,
        setting: setting
    },
    computed: {
        // 计算属性的 getter
        rulesMax: function () {
            return (this.keys.length - 1);
        },
    },
    methods: {
        newTYpe: function () {
            this.isNew = true;
            this.type = JSON.parse(JSON.stringify(empty));
            this.fieldList = JSON.parse(JSON.stringify(fields))
            console.log(this.type)
        },
        editType: function (index) {
            this.editPoint = index;
            this.isNew = true;
            this.type = this.typeList[index];
            if(debug) {
                console.log('select type:');
                console.log(this.type);
                console.log('this.type.fields type:');
                console.log(typeof this.type.fields);
                console.log(this.type.fields);
                console.log('this.type.rules type:');
                console.log(typeof this.type.rules);
                console.log(this.type.rules);
            }
            let fieldArr = [];
            let parseArr = [];

            if(this.type.fields == null) {
                fieldArr = JSON.parse(JSON.stringify(mField));
            } else  {
                if(typeof this.type.fields  == 'string')
                    this.type.fields  = JSON.parse(this.type.fields );
                //console.log('this.type.fields parse:');
                //console.log(this.type.fields);
                fieldArr = getFieldList(this.type.fields );
            }

            if(this.type.rules == null) {
                parseArr = JSON.parse(JSON.stringify(mParser));
            } else  {
                if(typeof this.type.rules  == 'string')
                    this.type.rules  = JSON.parse(this.type.rules );
                parseArr = getParserList(this.type.rules );
            }
            if(debug) {
                console.log('fieldArr:');
                console.log(fieldArr);
                console.log('parseArr:');
                console.log(parseArr);
            }
            let tmpArr = getList(fieldArr, parseArr);
            this.fieldList = JSON.parse(JSON.stringify(tmpArr));
            if(debug) {
                console.log('this.type.fieldList:');
                console.log(this.fieldList);
            }
            if(this.type.image_url == null) {
                this.type.image_url = url;
            }
        },
        delType: function (index) {
            console.log(index);
            this.delPoint = index;
            this.type= this.typeList[index];
            $('#myModal').modal('show');
        },
        back: function () {
            this.isError = false;
            this.isNew = false;
            this.editPoint = -1;
        },
        toSubmit: function () {
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
            let fieldObj = {};
            let parserObj = {};
            let fieldCount = 0;
            let parserCount = 0;
            let sendParse = null;
            let sendLabel = null;

            this.fieldList.forEach( (item, index) => {

                if(item.key !== '')
                    fieldCount++;

                if(item.parse[2] !== '')
                    parserCount++;

                let target = 'key'+(index+1);
                if(item.check ) {
                    fieldObj[target] = item.key;
                    parserObj[target ] = item.parse;
                }
            });
            if(fieldCount > 0) {
                sendLabel = JSON.stringify(fieldObj);
            }


            if(parserCount > 0) {

                sendParse = JSON.stringify(parserObj);
            }
            this.type.fields = sendLabel;
            this.type.rules = sendParse;
            this.typeString = JSON.stringify(this.type);

            console.log('checkValue ---------');
            console.log(sendLabel);
            console.log(sendParse);
            console.log(this.typeString);
            return true;
        },
        chanheItem: function () {
             this.typeString = JSON.stringify(this.type);
        },
        toUpload: function() {
            $.LoadingOverlay("show");
            let obj = document.getElementById('uploadTypeImage');
            obj.submit();
        },
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delForm').submit();
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
} );

function changeImage(event) {
    console.log(event.currentTarget);
    readURL(event.currentTarget);
}

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            //$('#preview_progressbarTW_img').attr('src', e.target.result);
            app.type.image_url = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
}
