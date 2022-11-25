let table;
const max = 8;//Commands limit
const min = 1;
const workLen = 16; //8byte = 16
const cmdPrifix = 'fc00';
const initData = '0201000000000000';
let debug = true;
if(debug) {
    console.log(commands);
}

let obj1 = [
    {'value': '02', 'title': 'Pin 02 I/O'},
    {'value': '04', 'title': 'Pin 04 I/O'},
    {'value': '05', 'title': 'Pin 05 I/O'},
    {'value': '0c', 'title': 'Pin 12 I/O'},
    {'value': '0d', 'title': 'Pin 13 I/O'},
    {'value': '0e', 'title': 'Pin 14 I/O'},
    {'value': '0f', 'title': 'Pin 15 I/O'},
    {'value': '10', 'title': 'Pin 16 I/O'},
    {'value': '11', 'title': 'Pin 17 I/O'},
    {'value': '12', 'title': 'Pin 18 I/O'},
    {'value': '13', 'title': 'Pin 19 I/O'},
    {'value': '15', 'title': 'Pin 21 I/O'},
    {'value': '16', 'title': 'Pin 22 I/O'},
    {'value': '17', 'title': 'Pin 23 I/O'},
    {'value': '19', 'title': 'Pin 25 I/O'},
    {'value': '1a', 'title': 'Pin 26 I/O'},
    {'value': '1b', 'title': 'Pin 27 I/O'},
    {'value': '20', 'title': 'Pin 32 I/O'},
    {'value': '21', 'title': 'Pin 33 I/O'},
    {'value': '22', 'title': 'Pin 34 IN only'},
    {'value': '23', 'title': 'Pin 35 IN only'},
    {'value': '24', 'title': 'Pin 36 IN only'},
    {'value': '27', 'title': 'Pin 39 IN only'}];
let obj2 = [
    {'value': '01', 'title': 'IN'},
    {'value': '02', 'title': 'OUT'},
    {'value': '03', 'title': 'OPEN_DRAIN'},
    {'value': '04', 'title': 'PWM'},
    {'value': '05', 'title': 'ADC'},
    {'value': '06', 'title': 'DAC'},
    {'value': '07', 'title': 'NeoPixel'}];
let obj3 = [
    {'value': '00', 'title': 'None'},
    {'value': '01', 'title': 'PULL_DOWN'},
    {'value': '02', 'title': 'PULL_UP'}]
let obj3_out = [
    {'value': '00', 'title': 'Low'},
    {'value': '01', 'title': 'High'}];
let obj3_atten = [
    {'value': '00', 'title': 'ATTN_0DB'},
    {'value': '01', 'title': 'ATTN_2_5DB'},
    {'value': '02', 'title': 'ATTN_6DB'},
    {'value': '03', 'title': 'ATTN_11DB'}];
let obj4_width = [
    {'value': '00', 'title': 'WIDTH_9BIT'},
    {'value': '01', 'title': 'WIDTH_10BIT'},
    {'value': '02', 'title': 'WIDTH_11BIT'},
    {'value': '03', 'title': 'WIDTH_12BIT'}];

let empty = {
    id: 0,
    type_id: type_id,
    device_id: device_id,
    cmd_name: '',
    command: 'fc000102030405060708',
    updated_at: ''
};
let emptyOption = ['第1組命令'];

let app = new Vue({
    el: '#app',
    data: {
        commandObjList: commands,//From server
        commandObj: JSON.parse(JSON.stringify(empty)),
        isNew: false,
        isNewCommand: false,
        isError: false,
        editPoint: -1,
        delPoint: -1,
        byteObj: getByteData(initData),//Show command select function
        commandList: [],
        typeId: type_id,
        newAttribute: '',
        commandObjString:'',
        cmdIndex: 0,
        pin1: obj1,
        pin2: obj2,
        pin3: obj3,
        pin3_out: obj3_out,
        pin3_atten: obj3_atten,
        pin4_width: obj4_width,
        finalCmd: [],
        cmdOptions : JSON.parse(JSON.stringify(emptyOption)),
        checkValue: 0,
        computedValue: 0
    },
    watch:{
        checkValue: function(value) {
            let x= parseInt(value);
            let check = x.toString(16);
            if(check.length%2 == 1) {
                this.computedValue = '0' + check;
            } else {
                this.computedValue = check;
            }
        }
    },
    computed: {
        // 计算属性的 getter
        currentCmd: function () {
            let str = '';
            for(let i=1; i<9; i++) {
                let tmp = 'p'+i;
                str = str + this.byteObj[tmp];
            }
            return str;
        },
    },
    methods: {
        setData:function (data){
            if(debug)
                console.log('setData : '+data);
            this.cmdIndex = 0;
            this.commandList = getCmdArray(data);
            if(this.isNewCommand === true) {
                this.byteObj = getByteData(initData);

            } else if(this.commandList.length > 0) {
                this.byteObj = getByteData(this.commandList[0]);
            }
            this.cmdOptions = getOptions(this.commandList);
            if(debug) {
                console.log('this.commandList : ');
                console.log(this.commandList);
                console.log('this.cmdOptions : ');
                console.log(this.cmdOptions);
            }
        },
        newCommand: function () {
            this.isNew = true;
            this.isNewCommand = true;
            this.commandObj = JSON.parse(JSON.stringify(empty));
            this.setData(this.commandObj.command);

        },
        editCommand: function (index) {
            this.editPoint = index;
            this.isNew = true;
            this.isNewCommand = false;
            this.commandObj = this.commandObjList[index];
            this.setData(this.commandObj.command);
        },
        delCommand: function (index) {
            console.log(index);
            this.delPoint = index;
            this.commandObj = this.commandObjList[index];
            $('#myModal').modal('show');
        },
        backCommandObjList: function () {
            this.isError = false;
            this.isNew = false;
            this.editPoint = -1;
        },
        toSubmit: function () {
            $.LoadingOverlay("show");
            //console.log(document.getElementById('editForm'));
            document.getElementById('editForm').submit();
        },
        changeItem: function () {
            this.commandObjString = JSON.stringify(this.commandObj);
        },
        addCommand: function () {
            if(this.cmdOptions.length == max)
                return;
            this.cmdIndex++;
            let obj = getOption(this.cmdIndex, initData);
            this.cmdOptions.splice( this.cmdIndex, 0, obj );
            this.byteObj = getByteData(initData);
            if(this.cmdIndex > max)
                this.cmdIndex = max-1;
        },
        deleteCommand: function () {
            if(this.cmdOptions.length == min)
                return;
            this.cmdOptions.splice( this.cmdIndex, 1 );
            this.cmdIndex--;
            if(this.cmdIndex < 0)
                this.cmdIndex = 0;
        },
        saveCommand: function () {
            this.cmdOptions[this.cmdIndex]['value'] = this.currentCmd;
            this.commandObj.command = cmdPrifix;
            for(let i=0; i < this.cmdOptions.length; i++) {
                this.commandObj.command = this.commandObj.command + this.cmdOptions[i]['value'];
            }
            this.commandObjString = JSON.stringify(this.commandObj);
            if(debug){
                console.log('this.commandObj.command :');
                console.log(this.commandObj.command);
                console.log('this.commandObjString :');
                console.log(this.commandObjString);
            }
        },
        onChange(event) {
            let data = this.cmdOptions[this.cmdIndex]['value'];
            this.byteObj = getByteData(data);
        }
    }
});

function getOptionTitle(inx) {
    return '第' + (inx+1) + '組命令';
}

function getOption(inx, cmdStr) {
    let mOption = {};
    mOption.title = getOptionTitle(inx);
    mOption.value = cmdStr;
    return mOption;
}

function getOptions(arr) {
    let options = [];
    for(let i=0; i<arr.length; i++) {
        let obj = getOption(i, arr[i]);
        options.push(obj);
    }
    return options;
}

//Split command into array bytes
function getByteData(cmd) {
    let tmpObj = {};
    tmpObj.p1 = cmd.substring(0,2);
    tmpObj.p2 = cmd.substring(2,4);
    tmpObj.p3 = cmd.substring(4,6);
    tmpObj.p4 = cmd.substring(6,8);
    tmpObj.p5 = cmd.substring(8,10);
    tmpObj.p6 = cmd.substring(10,12);
    tmpObj.p7 = cmd.substring(12,14);
    tmpObj.p8 = cmd.substring(14,16);
    return tmpObj;
}

//Split string into array command
function getCmdArray(cmdStr) {
    let flag = true;
    let diff = 0;
    let arr = [];
    do{
        if(cmdStr.length >= (4 + workLen + diff) ) {
            let str = cmdStr.substring( (4 + diff), (4 + workLen + diff));
            arr.push(str);
            diff = diff + workLen;
        } else {
            flag = false;
        }
    }
    while (flag)

    return arr;
}

//Combine all of commands in array to a string
function getCmdArrayString(cmdArr) {
    let str = cmdPrifix;
    for(let i=0;i < cmdArr.length; i++) {
        str = str + cmdArr[i];
    }
    return str;
}

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delForm').submit();
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
