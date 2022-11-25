//let farm_size_set = setting.farm_size_set;
let direction = 1; //1:右下，2:左上(同網頁), 3:左下, 4:右上
let player = null;
let isAutoTarget = false;
let targetList = null;
let targetIndex = 0;
let p = location.protocol;
let baseBg = {backgroundColor:"#d9d9d9"};
let whiteBg = {backgroundColor:"#FFFFFF"};
let highlightBg = {backgroundColor:"#eed764"};
console.log(p);

//是否顯示方向按鍵
let isDirectionCmd = true;
//是否支援重載
let isReload = false;
//是否外環設定顏色(存於資料庫與否)
let isIncludeBorder = false;
let isWebRTC = true;
//已種植是否顯示綠環
let isShowPlantBorder = true;
let firstPlant = null;

let defaultBlock = {"backgroundColor":gray};
let greenBlock = {"backgroundColor":green};
if(isIncludeBorder) {
    defaultBlock['border-color'] = black;
    greenBlock['border-color'] = black;
}
let redBorder = {"border-color": red};
let greenBorder = {"border-color": green};

let bg ={"none": defaultBlock,
    "plant": greenBlock
};


let rows = [];//For checkbox
for(let i=0;i<farm_size_set.box.number;i++) {
    rows[i] = false;
}
let empty = {
    title: '',
    tag: '',
    box: {x:1, y:1},
    plant: {x:1, y:1},
    kind: 1,
    maturity: 0,
    color: gray,
    colorBlock: bg.none,
    sort: 0,
    code: ''
}

let emptyTrigger = {
    name: '',
    field: '',
    operator: 3,
    value: 0,
    message: ''
};

/*let kinds = {
    "none": "不選",
    "spinach": "菠菜",
    "water spinach": "空心菜",
    "potato": "馬鈴薯"
}*/
let kindOptions = [
    {"name": "菠菜", "maturity": 30, "url": "https://www.laoziliao.net/doc/1639455973714066"},
    {"name": "空心菜", "maturity": 35, "url": "https://kmweb.coa.gov.tw/knowledge_view.php?id=8282"},
    {"name": "青江菜", "maturity": 30 , "url": "https://kmweb.coa.gov.tw/knowledge_view.php?id=2374"},
];



let kinds = [
    {"key": 0, "name": "X", "color": "#898987", "colorBlock": defaultBlock, "maturity": 0},
    {"key": 1, "name": "菠菜", "color": "#94b815","colorBlock": defaultBlock, "maturity": 30},
    {"key": 2, "name": "空心菜", "color": "#94b815","colorBlock": defaultBlock, "maturity": 35},
    {"key": 3, "name": "青江菜", "color": "#94b815","colorBlock": defaultBlock, "maturity": 30},
];

let directs = [
    {"name": "往上", "command": "up", "key": 81},
    {"name": "往下", "command": "down", "key": 82},
    {"name": "初始", "command": "initial", "key": 80},
    {"name": "停止移動", "command": "stop", "key": 20},
    {"name": "往左", "command": "left", "key": 83},
    {"name": "往右", "command": "right", "key": 84},
];


/*let commands = [
    //{"name": "種植", "command": "plant", "key": 86},
    {"name": "上架", "command": "plant", "key": 86,"id": null},
    {"name": "採收", "command": "crop", "key": 87,"id": null},
    {"name": "澆水", "command": "watering", "key": 88,"code": null},
    //{"name": "施肥", "command": "muck", "key": 89,"code": null},

];*/

let emptyCodeStruct = {
    "g-code": null,
    "speed": null,
    "x": null,
    "y": null,
    "z": null
}

let sets = {
    "watering":{"name": "澆水", "command": "watering","key": 88, "duration": 10, "time": ''},
    "muck":  {"name": "施肥", "command": "muck", "key": 89, "duration": 10, "time": ''},
};


if(plant_kinds !== undefined && plant_kinds !== null) {
    kinds = JSON.parse(JSON.stringify(plant_kinds));
}

/*if(plant_sets !== undefined && plant_sets !== null) {
    sets = JSON.parse(JSON.stringify(plant_sets));
}*/

function addDays(timeStr, days) {
    var date = new Date(timeStr);
    date.setDate(date.getDate() + days);
    return date;
}

let obj = null;
let farmObjectKeys = Object.keys(farmObject);
if(farmObjectKeys.length>0) {
    obj = verifyPlants( farmObject, farm_size_set );
} else {
    obj = getFarmData( farm_size_set);
}
let objStr = JSON.stringify(obj);

function verifyPlants( myObj, setting ) {
    let myKeys = Object.keys(myObj);
    let now = new Date().getTime();
    for(let i=0;i<myKeys.length;i++) {
        let tmp = myObj[myKeys[i]];
        tmp.code = getCode('G0',setting.speed, 0,(tmp.box.x-1)*setting.box.distance, (tmp.plant.y-1)*setting.plant.distance);
        let changObj = checkPlant(tmp)
        if(i===0) {
            firstPlant = JSON.parse(JSON.stringify(changObj));
        }
        myObj[myKeys[i]] = JSON.parse(JSON.stringify(changObj));``

        //console.log(tmp);
    }
    return myObj;
}

function checkPlant(myTarget) {

    let myPlant = JSON.parse(JSON.stringify(myTarget));
    if(farm_size_set.plant.number < 8) {
        let i = (10-farm_size_set.plant.number)*25;
        let j = (10-farm_size_set.plant.number)*4;
        myPlant.colorBlock['width'] = i+"px";
        myPlant.colorBlock['height'] = i+"px";
        myPlant.colorBlock['font-size'] = j+"px";
    }

    myPlant.colorBlock['border-color'] = black;
    myPlant.checked = false;
    myPlant.countdown = myPlant.maturity;
    if(myPlant.plant_time=== '') {
        myPlant.plant_time = null;
    }
    if(myPlant.crop_time === '') {
        myPlant.crop_time = null;
    }

    if(myPlant.plant_time !== null  && myPlant.crop_time === null && myPlant.plant_time !== null && myPlant.maturity>0 ) {

        let check = getDifference(myPlant.plant_time);
        //顯示已種植綠環
        if(isShowPlantBorder) {
            myPlant.colorBlock['border-color'] = green;
        }

        //console.log(tmp.start+'=>'+check);
        if(check > myPlant.maturity) {
            myPlant.colorBlock['border-color'] = red;
            myPlant.checked = true;
            myPlant.countdown = check - myPlant.maturity -1;
        } else {
            myPlant.countdown = myPlant.maturity - check + 1;
        }
        //console.log(myKeys[i]+'=>'+myPlant.countdown);
    } else if (myPlant.plant_time !== null  && myPlant.crop_time !== null ) {
        myPlant.colorBlock['border-color'] = dark_gray;
        myPlant.checked = false;
        myPlant.countdown = myPlant.maturity;
    }
    return myPlant;
}

function getDifference(dateStr) {
    const date1 = new Date();
    const date2 = new Date(dateStr);
    const diffTime = Math.abs(date2 - date1);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    //console.log(diffTime + " milliseconds");
    //console.log(diffDays + " diffDays");
    return diffDays;
}

let table =null;
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

let labelObj, statusTarget;

function initReports() {
    statusTarget = {};
    labelObj = {};
    for(let i=0;i<apps.length;i++) {
        let app = apps[i];
        let keys = Object.keys(app.key_label);
        let obj = {};
        for(let j=0; j<keys.length;j++) {
            let key = keys[j];
            obj[key] = 0;
        }
        //statusTarget: 用app.id取得sequence
        statusTarget[app.id] = app.sequence;
        labelObj[app.sequence] = app.key_label;

        if(statusObj[app.sequence] === null) {
            statusObj[app.sequence] = JSON.parse(JSON.stringify(obj));
        }
    }
}

//取得可種植列表
function getPlantList( myObj, command) {
    let tmpArr = [];
    let myKeys = Object.keys(myObj);
    let now = new Date().getTime();
    for(let i=0;i<myKeys.length;i++) {
        let tmp = myObj[myKeys[i]];
        if(tmp.hasOwnProperty('plant_time')) {
            if(tmp.plant_time === null && tmp.kind > 0) {
                let data  = getActionData(tmp, command);
                tmpArr.splice(tmpArr.length, 1, data);
            }
        }
    }
    return tmpArr;
}

//取得採收列表
function getCropList( myObj, command ) {
    let tmpArr = [];
    let myKeys = Object.keys(myObj);

    for(let i=0;i<myKeys.length;i++) {
        let tmp = myObj[myKeys[i]];
        if(tmp.checked === true) {
            let data  = getActionData(tmp, command);
            tmpArr.splice(tmpArr.length, 1, data);
        }

    }
    return tmpArr;
}

//取得澆水/施肥列表
function getAllList( myObj, command ) {
    let tmpArr = [];
    let myKeys = Object.keys(myObj);

    for(let i=0;i<myKeys.length;i++) {
        let tmp = myObj[myKeys[i]];
        let data  = getActionData(tmp, command);
        tmpArr.splice(tmpArr.length, 1, data);
    }
    return tmpArr;
}

let myFarmScript = JSON.parse(JSON.stringify(farm_script_empty));

if(farm_script_set.length>0) {
    myFarmScript = JSON.parse(JSON.stringify(farm_script_set[0]));
}


initReports();


let app = new Vue({
    el: '#app',
    data: {
        settingTab:1,
        isDirectionCmd:isDirectionCmd,
        isChangePlantSetting: false,//判斷是否修改設定
        tab: 1,
        message: '手動指定操做目標，移動滑鼠動點擊目標植栽選取',
        alertMessage: '',
        codeMessage: '',
        isNewScript: false,
        isCodeEditHelp:false,
        isShowPosition: false,
        isShowCodeStyle: false,
        isAutoTarget:isAutoTarget,
        isWebRTC:isWebRTC,
        isChangeKind: false,//true: set plant enable, false: show plant data
        isShowPlantBorder: isShowPlantBorder,
        isSend: false,
        //kindKey: 'none',
        modalIndex: 1,//1: set plant, 2: edit kind
        kindIndex:0,
        kindOptionList: kindOptions,
        kindObject: JSON.parse(JSON.stringify(kinds)),
        kindObjectBackup:  JSON.parse(JSON.stringify(kinds)),
        device: device,
        farmSize: JSON.parse(JSON.stringify(farm_size_set)),
        farmHome: JSON.parse(JSON.stringify(farm_home_set)),
        farmPlate: JSON.parse(JSON.stringify(farm_plate_set)),
        farmScriptList: JSON.parse(JSON.stringify(farm_script_set)),
        farmScript: myFarmScript,//JSON.parse(JSON.stringify(farm_script_set[0])),
        farmScriptIndex: 0,
        codeStr: myFarmScript.set.codeList[0],
        codeStruct: JSON.parse(JSON.stringify(emptyCodeStruct)),
        boxObject: JSON.parse(JSON.stringify(farm_size_set.box)),
        plantObject: JSON.parse(JSON.stringify(farm_size_set.plant)),
        farmSizeStr: JSON.stringify(farm_size_set),
        //plantList: getFarmData(farm_size_set)
        farmObject: obj,
        farmObjectStr: objStr,
        plant: empty,
        kind: JSON.parse(JSON.stringify(kinds[0])),
        selectedKind:JSON.parse(JSON.stringify(kinds[0])),
        selectIndex: 1,
        checkList:JSON.parse(JSON.stringify(rows)),
        label: labelObj,
        status: statusObj,
        appList: apps,
        directList: JSON.parse(JSON.stringify(directs)),
        commandObject:farm_commands_set,
        backupObject: null,
        backupKey: null,
        element: getVideoObject(device.macAddr, 1),
        set: sets['watering'],
        duration_min: 5,
        duration_max: 60,
        isEditCodeStruct:false,
        structIndex:-1,
        codeStyleList: [
            {"name": "選擇自動加入命令格式", "value": ""},
            {"name": "G code", "value": "G0"},
            {"name": '速度', "value": "F1000"},
            {"name": 'X軸', "value": "X0"},
            {"name": 'Y軸', "value": "Y0"},
            {"name": 'Z軸', "value": "Z0"},
        ],
        positionList: [
            {"name": "選擇自動加入位置", "value": ""},
            {"name": "動態位置", "value": "DYNAMIC"},
        ],
        selectedCodeStyle: '',
        editText: getCodeTxt(myFarmScript.set.codeList),
        triggerList: trigger_set,
        trigger: JSON.parse(JSON.stringify(emptyTrigger)),
        isEditTrigger: 0,
        operatorList: [
            {"id": 1, "value": ">"},
            {"id": 2, "value": ">="},
            {"id": 3, "value": "="},
            {"id": 4, "value": "<="},
            {"id": 5, "value": "<"},
            {"id": 6, "value": "<>"},
        ],
        triggerIndex: 0

    },
    mounted() {


    },
    watch:{
        "farmSize.box.number": function (value) {
           this.farmSize.box.number = parseInt(value);
           this.boxObject.number = parseInt(value);
           this.farmSizeStr = JSON.stringify(this.farmSize);
        },
        "farmSize.box.row": function (value) {
            this.farmSize.box.row = parseInt(value);
            this.boxObject.row = parseInt(value);
            this.farmSize.box.number = this.boxObject.row*this.boxObject.column;
            this.boxObject.number = this.farmSize.box.number;
            this.farmSizeStr = JSON.stringify(this.farmSize);
            this.farmObject = getFarmData(this.farmSize);
            this.farmObjectStr = JSON.stringify(this.farmObject);
        },
        "farmSize.box.column": function (value) {
            this.farmSize.box.column = parseInt(value);
            this.boxObject.column = parseInt(value);
            this.farmSize.box.number = this.boxObject.row*this.boxObject.column;
            this.boxObject.number = this.farmSize.box.number;
            this.farmSizeStr = JSON.stringify(this.farmSize);
            this.farmObject = getFarmData(this.farmSize);
            this.farmObjectStr = JSON.stringify(this.farmObject);
        },
        "farmSize.plant.number": function (value) {
            this.farmSize.plant.number = parseInt(value);
            this.plantObject.number = parseInt(value);
            this.farmSizeStr = JSON.stringify(this.farmSize);
        },
        "farmSize.plant.row": function (value) {
            this.farmSize.plant.row = parseInt(value);
            this.plantObject.row = parseInt(value);
            this.farmSize.plant.number = this.plantObject.row*this.plantObject.column;
            this.plantObject.number = this.farmSize.plant.number;
            this.farmSizeStr = JSON.stringify(this.farmSize);
            this.farmObject = getFarmData(this.farmSize);
            this.farmObjectStr = JSON.stringify(this.farmObject);
        },
        "farmSize.plant.column": function (value) {
            this.farmSize.plant.column = parseInt(value);
            this.plantObject.column = parseInt(value);
            this.farmSize.plant.number = this.plantObject.row*this.plantObject.column;
            this.plantObject.number = this.farmSize.plant.number;
            this.farmSizeStr = JSON.stringify(this.farmSize);
            this.farmObject = getFarmData( this.farmSize);
            this.farmObjectStr = JSON.stringify(this.farmObject);
        },
        "farmSize.box.interval": function (value) {
            this.farmSize.box.interval = Number(value);
        },
        "farmSize.plant.interval": function (value) {
            this.farmSize.plant.interval = Number(value);
        },
        "farmSize.start.z" : function (value) {
            this.farmSize.start.z = Number(value);
        },
        "farmSize.start.y" : function (value) {
            this.farmSize.start.y = Number(value);
        },
        "set.duration": function (value) {
            if(value > this.duration_max) {
                this.set.duration = this.duration_max;
            } else if(value < this.duration_min) {
                this.set.duration = this.duration_min;
            }
        },

        "farmSize.field": function (value) {
            this.farmObject = getFarmData(this.farmSize);
            this.farmObjectStr = JSON.stringify(this.farmObject);
            this.plant = JSON.parse(JSON.stringify(empty));
        },
        codeStr: function (value) {
            this.codeStruct = getCodeStruct(this.codeStr);
        }
    },
    methods: {
        initial() {
            this.plant = JSON.parse(JSON.stringify(empty));
            this.backupObject = null;
            this.backupKey = null;
        },
        onChangeOption(event) {
            //console.log(event.target.value);
            let name = event.target.value;
            for(let i=0;i<this.kindOptionList.length;i++) {
                let option = this.kindOptionList[i];
                if(option.name === name) {
                    this.selectedKind.maturity = option.maturity;
                    break;
                }
            }
        },
        changeKind(event){
            let value = Number(event.target.value);
            if(value > 0) {
                this.isChangeKind = true;
            } else {
                this.isChangeKind = false;
            }

            this.kindIndex = value;
            this.kind = JSON.parse(JSON.stringify(this.kindObject[this.kindIndex]));
            //this.kindKey = this.kindObject[this.kindIndex]['key'];
        },
        /*disableKind() {
            this.isChangeKind = false;
            this.kindIndex = 0;
            this.kind = JSON.parse(JSON.stringify(this.kindObject[this.kindIndex]));
        },*/
        showKindList() {
            this.kindIndex = 0;
            this.kind = JSON.parse(JSON.stringify(this.kindObject[this.kindIndex]));
            this.modalIndex = 2;
            $('#myModal').modal('show');
        },
        cancelEditKindList() {
            this.kindObject = JSON.parse(JSON.stringify(this.kindObjectBackup));
        },
        cancelEditKind(){
            this.kind = JSON.parse(JSON.stringify(this.kindBackup));
            $('#myModal2').modal('hide');
            this.showKindList();
        },

        setKind(index){
            $('#myModal').modal('hide');
            if(index === undefined || index === null) {
                this.selectedKind = {"key": this.kindObject.length, "name": "", "color": gray, "colorBlock": defaultBlock};
                this.selectIndex = this.kindObject.length;
            } else {
                this.selectedKind = JSON.parse(JSON.stringify(this.kindObject[index]));
                this.selectIndex = index;
            }


            this.kindBackup = JSON.parse(JSON.stringify(this.selectedKind));
            $('#myModal2').modal('show');
        },
        delKind(index){
            this.kindObject.splice(index, 1);
            for(let i=0;i<this.kindObject.length;i++ ) {
                let target = this.kindObject[i];
                target.key = i;
            }
        },
        changeTab(value) {
            //切換前
            if(this.tab === 4) {
                for(let i=0;i<this.checkList.length;i++) {
                    this.checkList[i] = false;
                }
                if(this.isChangePlantSetting === true) {
                    this.isSend = true;
                    this.toSaveAllPlants();
                }

                this.isEditCodeStruct=false;
                this.settingTab=1;
            } else if(this.tab === 3 || this.tab === 1) {//IP CAM & operation
                restoreTarget();
            }
            //切換後
            if(value === 3) {
                //Play fly player
                if(this.isWebRTC === false) {
                    window.setTimeout(function () {

                        player = flv_load_mds(player, app.element.id, app.element.url, app.element.number);
                        console.log(typeof player);
                    }, 500);
                }

            }
            this.message = '';
            this.tab = value;
            this.initial();
            //alert(this.tab);
        },
        setPlant(value) {
            //alert(value);


            //Set plant parameter
            if(this.tab === 4) {
                this.isChangePlantSetting = true;
                this.plant = JSON.parse(JSON.stringify(this.farmObject[value]));
                if(this.kind.key > 0) {
                    //this.plant.kind = this.kindKey;
                    this.plant.kind = this.kindIndex;
                    this.plant.color = this.kind.color;
                    this.plant.colorBlock = this.kind.colorBlock;
                    this.plant.maturity = this.kind.maturity;
                    this.plant.title = this.kind.name;
                } else {
                    this.plant.kind = 0;
                    this.plant.color = this.kindObject[0].color;
                    this.plant.colorBlock = this.kindObject[0].colorBlock;
                    this.plant.maturity = 0;
                    this.plant.title = this.plant.tag;
                }
                this.plant.plant_time = null;
                this.plant.crop_time = null;
                this.plant.watering = null;
                this.plant.muck = null;
                this.plant = checkPlant(this.plant);
                this.farmObject[value] = JSON.parse(JSON.stringify(this.plant));
            }

            if(this.tab === 1) {//顯示植栽資料進行設定
                /*this.plant = JSON.parse(JSON.stringify(this.farmObject[value]));
                this.modalIndex = 1;
                $('#myModal').modal('show');*/
                //傳送命令中避免

                if(this.isAutoTarget===false) {
                    if(this.isSend) return alert('動作中');
                    //指定單一植栽操作時

                    switchTarget(value);
                    showMessage(app,'指定動作目標:'+ this.backupObject.tag, 10000);
                }

            } else if(this.tab===3){//設定移動到植栽位置
                if(this.isSend) return alert('農業機器人動作中...');
                switchTarget(value);
                //this.message = '移動鏡頭到'+ this.backupObject.tag;
                showMessage(app,'移動鏡頭到'+ this.backupObject.tag);
                /*let location = {box:this.backupObject.box, plant: this.backupObject.plant, plant_key: this.backupObject.plant_key};
                //80: initial command ,移動到指定位置
                this.isSend = true;
                socket.emit('farm', {mac:device.macAddr, "farm_direction":80, "data":location});*/
                //let item = this.commandObject['location'];
                this.sendCmd('location');
            }
        },
        showPlant(value) {
            //alert(value);
            if(this.tab ===4) {
                this.plant = this.farmObject[value];
                //console.log(this.plant);
            }
            if(this.tab ===1 && this.isAutoTarget===true) {
                //避免傳送命令時造成問題
                if(app.isSend === false) {
                    this.plant = JSON.parse(JSON.stringify(this.farmObject[value]));
                }

                //console.log(this.plant);
            }

        },
        setRowPlant(x, y, all) {
            //console.log(event.target.checked, x,y);
            this.isChangePlantSetting = true;
            let inx = (y-1)*this.boxObject.number+(x-1);
            let check = this.checkList[inx];
            let keys = Object.keys(this.farmObject);
            if(this.kindIndex === 0) {
                this.checkList[inx] = !check;
                return alert('未選擇菜種');
            }
            let number = this.plantObject.number;
            let column = this.boxObject.column;
            let start = (x-1)*column*number + (y-1)*number;
            let end = start+number;
            //alert(start+'~'+end);
            //kindObject : 蔬菜類型list

            for(let i=start; i<end;i++) {
                let key = keys[i];
                let target = this.farmObject[key];
                //target.kind = this.kindKey;
                if(check) {
                    target.kind = this.kindIndex;
                    target.color = this.kind.color;
                    target.colorBlock = this.kind.colorBlock;
                    target.maturity = this.kind.maturity;
                    target.title = this.kind.name;
                } else {
                    target.kind = 0;
                    target.color = this.kindObject[0].color;
                    target.colorBlock = this.kindObject[0].colorBlock;
                    target.maturity = 0;
                    target.title = target.tag;
                }
                target.plant_time = null;
                target.crop_time = null;
                target.watering_time = null;
                target.muck_time = null;
                if(!all) {
                    this.farmObject[key] = JSON.parse(JSON.stringify(checkPlant(target))) ;
                }
            }

        },
        setAllPlant(event) {
            let check = event.target.checked;
            if(this.kindIndex === 0) {
                event.target.checked = !check;
                return alert('未選擇菜種');
            }
            for(let i=0;i<this.checkList.length;i++) {
                this.checkList[i] = check;
            }
            for(let i=1;i<=this.boxObject.row;i++) {
                for(let j=1;j<=this.boxObject.column;j++) {
                    this.setRowPlant(i,j);
                }
            }
            this.farmObject = verifyPlants(this.farmObject);
        },
        toSavePlant() {
            let url = api_url+'/api/update-plants';
            let myObj = {};
            this.plant = checkPlant(this.plant);
            this.backupObject.colorBlock['border-color'] = this.plant.colorBlock['border-color'];
            this.backupObject.countdown = this.plant.countdown;
            this.backupObject.plant_time = this.plant.plant_time;
            this.backupObject.checked = this.plant.checked;
            myObj[this.plant.plant_key] = this.plant;
            myObj[this.plant.plant_key]['colorBlock']['backgroundColor'] = this.backupObject['colorBlock']['backgroundColor'];
            let str = JSON.stringify(myObj);
            let data = {device_id:this.device.id, farmStr: str , token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
            $('#myModal').modal('hide');
            this.farmObject[this.plant.plant_key] = JSON.parse(JSON.stringify(this.plant));
            /*window.setTimeout(function () {
                window.location.reload();
            }, 3000);*/

        },
        toSaveAllPlants() {
            //alert('toSaveAllPlants');
            this.isChangePlantSetting = false;
            let url = api_url+'/api/update-plants';
            this.farmObjectStr = JSON.stringify(this.farmObject);
            let data = {device_id:this.device.id, farmStr: this.farmObjectStr , token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);

        },
        toSaveKind() {
            //alert('toSaveKind');
            this.kindObject[this.selectIndex] = JSON.parse(JSON.stringify(this.selectedKind));
            $('#myModal2').modal('hide');
            this.showKindList();
        },
        toSaveAllKinds() {
            //alert('toSaveAllKind');
            $('#myModal').modal('hide');
            let url = api_url+'/api/update-kinds';
            let kindStr = JSON.stringify(this.kindObject);
            let data = {device_id:this.device.id, kindStr: kindStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
        },
        selectAll() {
            let keys = Object.keys(this.farmObject);
            if(this.kindIndex === 0) {
                return alert('請先選擇菜種');
            }
            for(let i=0; i<keys.length;i++) {
                let target = this.farmObject[keys[i]];
                //target.kind = this.kindKey;
                target.kind = this.kindIndex;
                target.color = this.kind.color;
                target.colorBlock = this.kind.colorBlock;
                //target.title = this.kindObject[this.kindKey];
                target.title = this.kindObject[this.kindIndex]['name'];
            }
        },
        disableAll() {
            let keys = Object.keys(this.farmObject);
            for(let i=0; i<keys.length;i++) {
                let target = this.farmObject[keys[i]];
                target.kind = 'none';
                target.color = "#898987";
                target.colorBlock = bg.none;
                target.title = target.tag;
            }
        },
        sendCmd(key) {
            //alert(command);
            if(this.isSend) return alert('機器人動作中...');

            let cmd = getCommand(this.commandObject, key);
            //判斷是否有腳本
            let script = getScript(this.farmScriptList, cmd.command);
            if(script === null) {
                return alert(cmd.name+'腳本尚未設定，請先到命令腳本設定，然後到手動命令綁定腳本');
            }
            //socket 下命令
            this.isSend = true;
            showMessage(app,'執行動作:'+cmd.name);
            let data = this.getTargetList(cmd.command);
            socket.emit('farm', {macAddr:device.macAddr, "bot_action":80, "data":data[0]});

            this.isSend = true;

            window.setTimeout(function () {
                app.isSend = false;
            }, 3000);
        },
        sendPlantCmd(key) {

            if(key === 'stop') {
                showCodeMessage('停止自動操作');

                this.isSend = false;

                socket.emit('farm', {mac:device.macAddr, "farm_action":80, "data":{"command":"stop"}});
                return;
            }

            //判斷是否有腳本
            let item = getCommand(this.commandObject, key);
            let script = getScript(this.farmScriptList, item.command);
            if(script === null) {
                return alert(item.name+'腳本尚未設定，請先到命令腳本設定，然後到手動命令綁定腳本');
            }

            if(this.isAutoTarget===false && this.plant.tag.length===0) {
                return alert('手動操作尚未指定操作目標!');
            }
            showCodeMessage('發送'+item.name+'命令中...');
            //showLoadingOverlay();
            if(item.command=== 'stop') {//停止動作
                socket.emit('farm', {mac:device.macAddr, "farm_action":20});
                return;
            }
            targetList = this.getTargetList(item.command);
            if(targetList.length>0) {
                //
                targetIndex = 0;
                let key = targetList[targetIndex].plant_key;
                this.plant = this.farmObject[key];
                this.isSend = true;
                //變更圓圈背景色
                switchTarget(key);

                socket.emit('farm', {mac:device.macAddr, "farm_action":80, "data":targetList});
                showMessage(app,item.name+'植栽數量:'+targetList.length);
            } else {
                //alert('沒有可'+item.name+'的植栽!');
                //this.message = '沒有可'+item.name+'的植栽!';
                showMessage(app,'沒有可'+item.name+'的植栽!');
           }
        },
        showVideoData() {
            this.modalIndex = 3;
            $('#myModal').modal('show');
        },
        copyUrl() {
            let content = document.getElementById('rtmp_url');
            content.disabled = false;

            content.select();
            document.execCommand('copy');
            content.disabled = true;
            alert("Copied!");
        },
        toSaveSet() {
            $('#myModal2').modal('hide');
            if(this.set.time !== '') {
                showMessage(app,this.set.name+"儲存設定時間到裝置中...");
            } else {
                showMessage(app,this.set.name+"儲存設定");
            }

            sets[this.set.command] = this.set;
            let str = JSON.stringify(sets);
            socket.emit('farm', {mac:device.macAddr, "plant_sets":this.set.key, "set":str, command:this.set.command});
        },
        changeTargetAuto() {
            if(this.isAutoTarget===true) {
                restoreTarget();
                this.plant = JSON.parse(JSON.stringify(empty));
                this.message = '自動指定操做目標，系統自動選取可進行操作目標';
            } else {
                this.message = '手動指定操做目標，移動滑鼠動點擊目標植栽選取';
            }
        },
        saveSetting(elementId) {
            if(elementId=='editFarmBotSetting') {
                //間隔至少為半徑兩倍
                /*let len = this.farmSize.radius*2;
                if(this.farmSize.box.interval<len) {
                    this.farmSize.box.interval = len;
                }
                if(this.farmSize.plant.interval<len) {
                    this.farmSize.plant.interval = len;
                }*/
                this.farmSizeStr = JSON.stringify(this.farmSize);
                let check = Object.keys(this.farmObject)
                if(check.length !== farmObjectKeys.length) {
                    this.toSaveAllPlants();
                }
            } else if(elementId==='editFarmPlateSetting') {
                this.farmSizeStr = JSON.stringify(this.farmPlate);
            } else if(elementId==='editFarmHomeSetting') {
                this.farmSizeStr = JSON.stringify(this.farmHome);
            }  else if(elementId==='editFarmScriptSetting') {
                //
                if(this.isCodeEditHelp === false) {
                    this.farmScript.set.codeList = getCodeList(this.editText);
                }

                this.farmSizeStr = JSON.stringify(this.farmScript.set);
            } else if(elementId==='editFarmCommandsSetting') {
                this.farmSizeStr = JSON.stringify(this.commandObject);
            }  else if(elementId==='editFarmTriggerSetting') {
                this.farmSizeStr = JSON.stringify(this.triggerList);
            }

            window.setTimeout(function () {
                document.getElementById(elementId).submit();
            }, 500);
        },
        newScript() {
            this.isNewScript = true;

            this.farmScript = JSON.parse(JSON.stringify(farm_script_empty));
            this.farmScript.set.id = 'action'+ new Date().getTime();
            this.editText = '';
        },
        cancelNewScript() {
            this.isNewScript = false;
            this.farmScript = JSON.parse(JSON.stringify(this.farmScriptList[this.farmScriptIndex]));
            this.editText = getCodeTxt(this.farmScript.set.codeList);
        },
        onChangeScript(event) {
            //alert(event.target.value);
            this.farmScriptIndex = Number(event.target.value);
            this.farmScript = JSON.parse(JSON.stringify(this.farmScriptList[this.farmScriptIndex]));
            this.editText = getCodeTxt(this.farmScript.set.codeList);
        },
        editCodeStruct(index) {
            this.structIndex = index;
            this.codeStr = this.farmScript.set.codeList[index];
            this.isEditCodeStruct = true;

            this.codeStruct = getCodeStruct(this.codeStr);
        },
        toSaveCode() {
            alert('toSaveCode');
        },
        newStruct() {
            this.structIndex = -1;
            this.isEditCodeStruct = true;
            this.codeStr = '';
        },
        cancelStruct() {
            let select = document.getElementById("codeStyleOption");
            if(select !== null) {
                select.setAttribute("size", 0);
            }

            this.isEditCodeStruct = false;
        },
        delStruct() {
            this.isEditCodeStruct = false;
            /*let yes = confirm('你確定刪除'+this.codeStr+'?');

            if (yes) {
                this.farmScript.set.codeList.splice(this.structIndex, 1);
            }*/
            this.farmScript.set.codeList.splice(this.structIndex, 1);
            this.structIndex = -1;
        },
        saveStruct() {
            if(this.codeStr.length===0) {
                this.structIndex = -1;
                this.isEditCodeStruct = false;
                return alert('未設定G code，不進行任何變更')
            }
            if(this.structIndex == -1) {//
                let select = document.getElementById("codeStyleOption");
                if(select !== null) {
                    select.setAttribute("size", 0);
                }
                this.farmScript.set.codeList.splice(this.farmScript.set.codeList.length,1,this.codeStr );
            } else {
                let obj = getUpperCodeAndMark(this.codeStr)
                this.farmScript.set.codeList[this.structIndex]= obj.code+obj.mark;
                this.structIndex = -1;
            }

            this.isEditCodeStruct = false;
            //('尚未儲存，需按下儲存腳本設定才完成!', 5000);
        },
        onChangeStyle(event) {
            let value = event.target.value;
            this.isShowCodeStyle = false;
            //選中之後新增一個事件來把它的size屬性變為0，這樣就可以關閉展開項
            document.getElementById("codeStyleOption").setAttribute("size", 0);
            this.selectedCodeStyle = '';
            //if(value.includes('G') && this.codeStr.includes('G')) return;
            if(value.includes('F') && this.codeStr.includes('F')) return;
            if(value.includes('X') && this.codeStr.includes('X')) return;
            if(value.includes('Y') && this.codeStr.includes('Y')) return;
            if(value.includes('Z') && this.codeStr.includes('Z')) return;
            this.codeStr = this.codeStr+value;

        },
        onChangePosition(event) {
            let value = event.target.value;
            this.isShowPosition = false;
            //選中之後新增一個事件來把它的size屬性變為0，這樣就可以關閉展開項
            document.getElementById("positionOption").setAttribute("size", 0);
            this.selectedCodeStyle = '';
            let obj = getUpperCodeAndMark(this.codeStr);
            this.codeStr = obj.code+value+obj.mark;

        },
        onChangeEditHelp() {
            if(this.isCodeEditHelp === false) {
                //將list轉文字
                this.editText = getCodeTxt(this.farmScript.set.codeList);
            } else {
                //輔助模式
                this.farmScript.set.codeList = getCodeList(this.editText);
            }
        },
        checkDeleteFarmScript(elementId) {
            let yes = confirm('你確定刪除'+this.farmScript.set.name+'?');

            if (yes) {
                window.setTimeout(function () {
                    document.getElementById(elementId).submit();
                }, 500);
            }
        },
        getTargetList(command) {
            let list = [];
            if(command === 'plant') {
                if(this.isAutoTarget===false && this.plant.plant_time !== null) {
                    //因isAutoTarget true會找到可上架植栽，不須確認
                    return alert(this.plant.tag+'已上架!');
                }
                if(this.isAutoTarget === true) {
                    list = getPlantList(this.farmObject, command);
                } else {
                    list = [getActionData(this.plant, command)];
                }

            } else if(command === 'crop') {
                if(this.isAutoTarget===false && this.plant.crop_time !== null) {
                    return alert(this.plant.tag+'已採收!');
                }
                if(this.isAutoTarget === true) {
                    list = getCropList(this.farmObject, command);
                } else {
                    list = [getActionData(this.plant, command)];
                }
            /*} else if(command === 'watering' || command === 'muck') {
                if(this.isAutoTarget === true) {
                    list = getAllList(this.farmObject, command);
                } else {
                    list = [getActionData(this.plant, command)];
                }*/
            } else {

                    list = [getActionData(this.plant, command)];

            }
            return JSON.parse(JSON.stringify(list));
        },
        addTrigger() {
            this.isEditTrigger = 1;
        },
        delTrigger() {
            let yes = confirm('你確定刪除觸發設定嗎？');

            if (yes) {
                this.triggerList.splice(this.triggerIndex, 1);
                this.isEditTrigger = 0;
            }
        },
        checkTrigger() {
            if(this.isEditTrigger==2) {
                this.triggerList[this.triggerIndex] = JSON.parse(JSON.stringify(this.trigger));
            } else {
                this.triggerList.push(JSON.parse(JSON.stringify(this.trigger)));
            }

            this.isEditTrigger = 0;
        },
        changeTriggerObject(sequence, event) {
            let key = event.target.value;
            let name = this.label[sequence][key];
            this.trigger.name = name;
            this.trigger.field = key;
        },
        editTrigger(index) {
            this.triggerIndex = index;
            this.isEditTrigger = 2;
            this.trigger = this.triggerList[index];
        }
    }
});

function getCommandById(cmdId) {
    let keys = Object.keys(app.commandObject);
    for(let i=0;i<keys.length;i++) {
        let cmd = app.commandObject[keys[i]];
        if(cmd.command === cmdId) {
            return cmd;
        }
    }
    return null;
}

function getCommand(commandObj, key) {
    return mCommand = commandObj[key];
}

function getScript(scriptList, cmdId) {
    let mScript = null;
    for(let i=0;i<scriptList.length;i++) {
        let tmp = scriptList[i];
        if(tmp.set.id === cmdId) {
            mScript = JSON.parse(JSON.stringify(tmp));
        }
    }
    return mScript;
}

function getPlantGCode(plant) {
    let boxInterval = app.farmSize.box.interval;
    let boxNumber = app.farmSize.box.number;
    let boxRow = plant.box.x;
    let plantInterval = app.farmSize.plant.interval;
    let planNumber = app.farmSize.plant.number;
    let plantColumn = plant.plant.y;
    //let radius = app.farmSize.radius;
    let startZ = app.farmSize.start.z;
    let startY = app.farmSize.start.y;
    let location = 0;
    //全部以左上角第一個植栽為初始位置
    if(direction === 1) {
        //右下開始位置
        location = " X0 Y"+(startY+((boxNumber-boxRow)*boxInterval))+' Z'+(startZ+((planNumber-plantColumn)*plantInterval));
    } else if(direction === 2) {
        //左上開始位置
        location = " X0 Y"+(startY+((boxRow-1)*boxInterval))+' Z'+(startZ+((plantColumn-1)*plantInterval));
    } else if(direction === 3) {
        //左下開始位置
        location = " X0 Y"+(startY+((boxNumber-boxRow)*boxInterval))+' Z'+(startZ+((plantColumn-1)*plantInterval));
    }  else if(direction === 4) {
        //右上開始位置
        ocation = " X0 Y"+(startY+((boxRow-1)*boxInterval))+' Z'+(startZ+((planNumber-plantColumn)*plantInterval));
    }
    return location;
}

//plant:植栽資料, cmd:命令 example => plant 上架
function getActionData(_plant, cmd) {

    let list = [];
    let script = getScript(app.farmScriptList, cmd);
    let codeList = script.set.codeList;

    for(let i=0; i<codeList.length; i++) {
        let code = codeList[i];
        let obj = getUpperCodeAndMark(code);
        if(obj.code !=='') {
            if(obj.code.includes(' DYNAMIC')) {
                let struct = getCodeStruct(obj.code);
                let plantGCode = getPlantGCode(_plant);

                if(struct.f !== undefined) {
                    code = struct.g_code+' F'+struct.f+plantGCode;
                } else {
                    code = struct.g_code+plantGCode;
                }
            } else {
                code = obj.code;
            }
            list.splice(list.length, 1, code);
        }

    }
    let msg = {"command":cmd, "codeList": list}
    if(_plant.plant_key) {
        msg.plant_key = _plant.plant_key;
    }
    return msg;
}

function getCodeList(editText) {
    let arr = editText.split('\n');
    if(arr[arr.length-1] === '') {
        arr.splice(arr.length-1, 1);
    }

    //避免備註被修改
    for(let i=0;i<arr.length;i++) {
        let str = arr[i];
        let obj= getUpperCodeAndMark(str);
        arr[i] = obj.code + obj.mark;
    }
    return arr;
}

function getUpperCodeAndMark(codeStr) {
    let inx = codeStr.indexOf(';');
    let mark = '';
    if(inx === 0) {
        return {"code":'', "mark": codeStr};
    } else if(inx > 0){
        mark = codeStr.substring(inx, codeStr.length);
        codeStr = codeStr.substring(0, inx);
    }
    codeStr = codeStr.toUpperCase();
    return {"code":codeStr, "mark": mark};
}

function getCodeTxt(list) {
    let txt = '';
    for(let i=0;i<list.length;i++) {
        txt = txt+list[i]+'\n';
    }
    return txt;
}

function showCodeMessage(msg, time) {
    if(timeoutAlertID !== null)
        clearTimeout(timeoutAlertID);

    app.codeMessage = msg;

    if(time === undefined || time === null)
        time = 3000;

    timeoutAlertID = window.setTimeout(function () {
        app.codeMessage = '';
    }, time);
}

function toHighlight(obj) {
    //console.log(obj);
    //obj.style.background='#b3b3b3';
    obj.style.backgroundColor = highlightBg.backgroundColor;

}

function restore(obj) {
    //console.log(obj);
    //obj.style.background='#d9d9d9';
    obj.style.background = whiteBg.backgroundColor;
}

function getCodeStruct(codeStr) {
    let cStruct = JSON.parse(JSON.stringify(emptyCodeStruct));
    let obj = getUpperCodeAndMark(codeStr);
    let str = obj.code;

    if(str.charAt(0) === ';') {
        return;
    }

    /*if(str.length === 0 || (str.charAt(str.length-1) === ' ' && !str.includes('Z') ) ){
        app.isShowCodeStyle = true;
        setTimeout(function(){
            let select=document.getElementById("codeStyleOption");
            if(select!==null) {
                select.focus()//聚焦
                //聚焦之後下框是不會自動展開的，檢視資料沒有屬性，也沒有支援的方法，可以用sizes屬性來代替下拉框的展開動作
                select.setAttribute("size",select.options.length); // 設定
            }
            let codeStrEl=document.getElementById("codeStr");
            if(codeStrEl !==null) {
                codeStrEl.focus()//聚焦
            }
        }, 500);

    } else*/ if(str.charAt(str.length-1) === ' ' && str.includes('Z') ){
        app.isShowPosition = true;
        setTimeout(function(){
            let select=document.getElementById("positionOption");
            if(select !== null) {
                select.focus()//聚焦
                //聚焦之後下框是不會自動展開的，檢視資料沒有屬性，也沒有支援的方法，可以用sizes屬性來代替下拉框的展開動作
                select.setAttribute("size",select.options.length); // 設定
            }

            let codeStrEl=document.getElementById("codeStr");
            if(codeStrEl !==null) {
                codeStrEl.focus()//聚焦
            }
        }, 500);

    } else{
        let select = document.getElementById("codeStyleOption");
        if(select !== null) {
            select.setAttribute("size", 0);
        }
        app.isShowPosition = false;
        app.isShowCodeStyle = false;
    }

    //str = 'G0 F1000 X0 Y0 Z0';
    if(str.length === 0) {
        return null;
    }

    let arr = str.split(' ');

    for(let i=0;i<arr.length;i++) {
        let tmp = arr[i];
        tmp = tmp.toUpperCase();
        if(tmp.includes('DYN')) {
            cStruct.position = tmp;
        } else if( i<2 && ( tmp.includes('G') || tmp.includes('Y') || tmp.includes('M') ) ) {
            if(cStruct.g_code === undefined) {
                cStruct.g_code = tmp;
            } else {
                cStruct.g_code = cStruct.g_code+' '+tmp;
            }
            tmpStr = cStruct.g_code;
        } else if(tmp.includes('F') || tmp.includes('F')) {

            cStruct.f = Number(tmp.replace('F', ''));

        } else if(tmp.includes('X')) {

            cStruct.x = Number(tmp.replace('X', ''));

        } else if(tmp.includes('Y')) {

            cStruct.y = Number(tmp.replace('Y', ''));

        } else if(tmp.includes('Z')) {

            cStruct.z = Number(tmp.replace('Z', ''));

        }
    }


    return cStruct;
}

/*function getCodeString(struct) {
    let str = '';
    if(struct.g_code !== null)  {
        str = struct.g_code;
    }
    if(struct.f !== null)  {
        str = str+' F'+struct.f;
    }

    if(struct.x !== null)  {
        str = str+' X'+struct.x;
    }

    if(struct.y !== null)  {
        str = str+' Y'+struct.y;
    }

    if(struct.z !== null)  {
        str = str+' Y'+struct.f;
    }
    return str;
}*/

function restoreTarget() {
    if(app.backupKey !== null) {
        //console.log('restoreTarget app.backupKey:'+app.backupKey);
        app.farmObject[app.backupKey] = JSON.parse(JSON.stringify(app.backupObject));
        //console.log('app.backupKey: null');

        app.backupKey = null;
    }
}

function switchTarget(plantKey) {
    restoreTarget();
    //Backup target

    app.backupKey = plantKey;
    app.backupObject = JSON.parse(JSON.stringify(app.farmObject[plantKey]));
    console.log('switchTarget 灰背:'+plantKey ) ;
    app.farmObject[plantKey]['colorBlock']['backgroundColor'] = gray;
    app.plant = JSON.parse(JSON.stringify(app.farmObject[plantKey]));
}

function getColorStyle(color) {
    if(isIncludeBorder) {
        return {backgroundColor:color, "border-color": black};
    } else {
        return {backgroundColor:color};
    }
}

function getFarmData(setting) {
    //let newArr = [];
    let newObject = {};
    let index = 0;
    rows = [];
    let arrIndex = 0;

    for(let x1=1; x1<= setting.box.row; x1++) {
        for(let y1=1; y1<= setting.box.column; y1++) {
            rows[arrIndex] = false;
            for(let x=1; x<= setting.plant.row; x++) {
                for(let y=1; y<= setting.plant.column; y++) {
                    let target = JSON.parse(JSON.stringify(empty));
                    let plant_key = setting.field+x1+y1+x+y;

                    target.title = setting.field+((x1-1)*setting.box.column +y1)+'-'+ ((x-1)*setting.plant.column +y);
                    target.tag = setting.field+((x1-1)*setting.box.column +y1)+'-'+ ((x-1)*setting.plant.column +y);
                    target.box.x = x1;
                    target.box.y = y1;
                    target.plant.x = x;
                    target.plant.y = y;
                    target.sort = index;
                    target.kind = 0;
                    target.checked = false;
                    target.code = getCode('G0',setting.speed, 0,(x1-1)*setting.box.distance, (y-1)*setting.plant.distance);
                    //newArr.splice(newArr.length, 1,  target);
                    newObject[plant_key] = target;
                    index++;
                }
            }
            arrIndex++;
        }
    }
    //return newArr;
    return newObject;
}

function getCode(code, speed, x, y, z) {
    return (code+' F'+speed+' X'+x+' Y'+y+' Z'+z);
}

function sendCmd(url) {
    app.isSendCmd = true;
    app.cmdMessage = '';
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+ token);
        },
        success: function (result) {
            //$.LoadingOverlay("hide");

            if(result.code == 200){//可以完成設定程序
                //app.cmdMessage =mechanism.cmd_name+'完成命令';
                showMessage(app,'完成命令');
                app.isSendCmd = false;
            }


        },
        error:function(err){
            //$.LoadingOverlay("hide");
            //app.cmdMessage = err;
            showMessage(app,err);
            app.isSendCmd = false;
        },
    });
    window.setTimeout(function () {
        app.isSendCmd = false;
    }, 5000);
}


$(document).ready(function() {
    table = $("#table1").dataTable(opt);
    $('#timeselector input').on("change", function() {
        app.changeTab(parseInt(this.id));
    });

    // Basic instantiation:
    $('#demo-input').colorpicker();

    // Example using an event, to change the color of the #demo div background:
    $('#demo-input').on('colorpickerChange', function(event) {
        //alert(event.color.toString());
        if(event.color.toString() === red) {
            return alert('此顏色不可使用，請重選!');
        }
        app.selectedKind.colorBlock = getColorStyle(event.color.toString());
        app.selectedKind.color = event.color.toString();
    });
    //initialize();
} );

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
            showMessage(app,result);
            app.isSend = false;
        },
        error:function(err){
            //app.alertMessage = err;
            alert(err.responseText);
            app.isSend = false;
        },
    });
    setTimeout(function(){
        app.isSend = false;
    }, 5000);
}

const socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
// 2
socket.on('connect', function()  {
    //socket.emit('web','Web socket is ready');
    socket.emit('storeClientInfo', { customId:device.macAddr });
});

socket.on('disconnect', function()  {
    console.log('web disconnect id is:'+socket.id);
    if (socket.connected === false ) {
        //socket.close()
        socket.open();
    }
});

socket.on('news', function(m) {
    console.log(m.hello);
});

socket.on('http_report_data', function(m) {
    console.log('From server : http_report_data ------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    //console.log(m);
    //macAddr from report
    if(m.macAddr !== device.macAddr) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    let mChoice = 0;
    if(m.hasOwnProperty('app_id')) {
        mChoice = statusTarget[m.app_id];
    }
    updateStatus(mChoice, m);

});

socket.on('update_mqtt_ul', function(m) {
    console.log('From server : http_report_data ------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    //console.log(m);
    //macAddr from report
    if(m.macAddr !== device.macAddr) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    let mChoice = 0;
    if(m.hasOwnProperty('app_id')) {
        mChoice = statusTarget[m.app_id];
    }
    updateStatus(mChoice, m);

});

socket.on('farm_update_target', function(m) {
    console.log('From server : farm_update_target ------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    console.log(app.backupObject.plant_key+'綠還');
    //macAddr from report
    if(m.macAddr !== device.macAddr) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    let mChoice = m.command+"_time";

    app.backupObject[mChoice] = getLocalTimeString(m.time);
    if(m.command === 'plant') {
        app.backupObject.colorBlock['border-color'] = green;
    } else if(m.command === 'crop') {
        app.backupObject.countdown = target.maturity;
        app.backupObject.colorBlock['border-color'] = dark_gray;
    } else if(ifm.command === 'watering') {
        app.backupObject.colorBlock['border-color'] = blue;
    } else if(m.command === 'muck') {
        app.backupObject.colorBlock['border-color'] = brown;
    }
    let mPlant = null;
    if(targetList.length>0) {
        targetList.shift();
    }
    if(targetList.length>0) {
        mPlant = targetList[0];
    }

    if(mPlant !== null) {
        switchTarget(mPlant.plant_key);
    }

    //app.isSend = false;

    //app.message = '更新植栽狀態';
    showMessage(app,'更新'+m.plant_key+'植栽狀態',2000)
});

socket.on('farm_specified_target', function(m) {
    console.log('From server : farm_specified_target ---------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    console.log(m);
    if(m.macAddr !== device.macAddr) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    app.isSend = false;
    //window.setTimeout(function () {
        if(m.key === 2) {

            if(m.action=== 80) {
                //app.message = '移動到指定位置已執行！';
                let cmd = getCommandById(m.command);
                if(cmd) {
                    showMessage(app,cmd.name+'命令已執行完畢！');
                } else {
                    showMessage(app,'命令已執行完畢！');
                }

                let mChoice = m.command;

                if(mChoice === 'plant' || mChoice === 'plant') {
                    //更新備份動作的時間
                    let date = new Date(m.time);
                    app.backupObject[mChoice+'_time'] = date.toLocaleString();
                    if(mChoice === 'plant') {
                        app.backupObject.colorBlock['border-color'] = green;
                    } else if(mChoice === 'crop') {
                        app.backupObject.countdown = target.maturity;
                        app.backupObject.colorBlock['border-color'] = dark_gray;
                    }/* else if(m.command === 'watering') {
                    app.backupObject.colorBlock['border-color'] = blue;
                    } else if(m.command === 'muck') {
                        app.backupObject.colorBlock['border-color'] = brown;
                    }*/
                    //還原資料
                    if(app.backupObject) {
                        app.plant = JSON.parse(JSON.stringify(app.backupObject));
                        restoreTarget();
                    }
                }

            } else if(m.command === 20) {
                //app.message = '停止命令已執行！';
                restoreTarget();
                showMessage(app,'停止命令已執行！');
            }

        }  else if(m.key === 4) {
            let str = '';
            if(m.command === app.set.key) {
                str = app.set.name;
            }
            str=  str+ '時間已設定！';
            showMessage(app,str);
        }
});

function updateStatus(mChoice, m) {
    app.status[mChoice] = JSON.parse(JSON.stringify(m));
}


