let target_mac = null;
let target_name = '';
let table;

if(controllers.length>0 && controller_mac != null) {
    for(let i=0;i<controllers.length;i++) {
        let controller = controllers[i];
        if(controller.macAddr === controller_mac) {
            target_mac = controller.macAddr;
        }
    }
}

if(nodes.length>0 && target_mac !==null) {
    for(let i=0;i<nodes.length;i++) {
        let node = nodes[i];
        if(node.node_mac === target_mac) {
            myNode = node;
        }
    }
} else if(nodes.length===0 && target_mac !==null){
    myNode.node_name = target_name;
    myNode.node_mac = target_mac;
}

if(myNode.inputs === null) {
    myNode.inputs = [];
}

if(myNode.outputs === null) {
    myNode.outputs = [];
}

let input1= [

];

let output1= [

];

let pre_input = {key1: '觸發值'};

let empty = {
    id: 0,
    input: '',
    output: '',
    node_mac: controller_mac,
    rule_order: '',
    trigger_value: '',
    operator: 3,
    action:'',
    action_value:'',
    time:'',
    input_type:'',
    output_type:'',
};

let operators = [
    {"id": 1, "value": ">"},
    {"id": 2, "value": ">="},
    {"id": 3, "value": "="},
    {"id": 4, "value": "<="},
    {"id": 5, "value": "<"},
    {"id": 6, "value": "<>"},
];

let type7PortArray = [8,7,6,5,4,3,2,1];
let type7Array = [0,0,0,0,0,0,0,0];


let macList = [];

Vue.component('todo-item', {
    props: ['todo'],
    template: '<li>{{ todo.text }}</li>'
});


let app = new Vue({
    el: '#app',
    data: {
        isDelete: true,
        isNew: false,
        isShow: false,
        node: JSON.parse(JSON.stringify(myNode)),
        list2: nodeInputs,//add for inputs
        list4: nodeOutputs,//add for outputs
        rule: JSON.parse(JSON.stringify(empty)),
        operatorList: operators,
        index: 1,
        type7List: type7Array,
        type7PortList:type7PortArray,
        selectInput: pre_input,
        trigger:{field:'key1', value: ''},
        ruleList: rules,
        allList: alls,
        itemList: null,//For relation rule item
        idList: null,//For relation rule id
        andList: [],//For checked rule id
    },
    methods: {
        inChange(event) {
            console.log(event.target.value)

            for(let i=0;i<this.list2.length;i++) {
                let device = this.list2[i];
                if(event.target.value === device.macAddr) {
                    this.rule.input_type = device.type_id;
                    break;
                }
            }
            if(this.rule.input_type !== null && this.rule.input_type !== '') {
                if(typeof(this.rule.input_type)==='string') {
                    this.rule.input_type = parseInt(this.rule.input_type);
                }
                //type.fields example: {key1: '輸入值'}
                for(let j=0;j<types.length;j++) {
                    let type = types[j];
                    if(this.rule.input_type === type.type_id) {
                        //this.selectInput 選擇欄位下拉選單
                        this.selectInput = type.fields;
                        break;
                    }
                }
                if(this.rule.input_type === 25) {//8bit
                    //this.selectInput 選擇欄位下拉選單增加API設定
                    this.selectInput['pass'] = 'API設定密碼';
                    this.rule.operator = 3;
                }
                let keys = Object.keys(this.selectInput);
                let values = Object.values(this.selectInput);
                //確認已存的欄位存在下拉選單中
                let target = Object.keys(this.rule.trigger_value)['0'];
                if(keys.includes(target)) {
                    this.trigger.field = Object.keys(this.rule.trigger_value)['0'];
                    this.trigger.value = Object.values(this.rule.trigger_value)['0'];
                } else if(typeof(this.selectInput) === 'object'){
                    this.trigger.field = Object.keys(this.selectInput)['0'];
                    this.trigger.value = '';
                }
            }
        },
        outChange(event) {
            console.log(event.target.value)
            if(event.target.value === 'server') {
                this.rule.output_type = 0;
                return;
            }
            for(let i=0;i<this.list4.length;i++) {
                let device = this.list4[i];
                if(event.target.value === device.macAddr) {
                    this.rule.output_type = device.type_id;
                }
            }

        },
        newCheck:function() {
            this.isNew = true;
            this.rule = JSON.parse(JSON.stringify(empty));
        },
        editCheck:function(inx) {
            this.isNew = true;
            this.rule = rules[inx];

            let sum = this.rule.action_value;
            for(let j=0;j<types.length;j++) {
                let type = types[j];
                if(this.rule.input_type === type.type_id) {
                    this.selectInput = type.fields;
                    break;
                }
            }
            if(this.rule.input_type === 25) {//8bit
                this.selectInput['pass'] = 'API設定密碼';
            }
            if(this.rule.input_type > 20) {
                //console.log(typeof this.rule.trigger_value);
                this.trigger.field = Object.keys(this.rule.trigger_value)['0'];
                this.trigger.value = Object.values(this.rule.trigger_value)['0'];
            }
            if(this.rule.output_type === 7) {
                for(let i= 0 ; i<this.type7List.length; i++) {
                    this.type7List[i] = (sum & (1<<(7-i)))>>(7-i);
                }
            }
        },
        back: function() {
            this.isNew = false;
        },
        toSubmit: function() {
            console.log('input.length:'+this.rule.input.length+',output.length'+this.rule.output.length);
            if(this.rule.input.length === 0 || this.rule.output.length===0) {

                alert('尚未選擇輸入或輸出裝置!')
                return;
            }
            let tmp = {};
            if(this.trigger.field === 'pass') {

                tmp[this.trigger.field] = 0;

            } else {
                tmp[this.trigger.field] = parseInt(this.trigger.value);
            }
            if(this.trigger.value === '' || this.trigger.value === null) {
                alert('尚未設定觸發值!');
                return;
            }
            this.rule.trigger_value = JSON.stringify(tmp);


            if(this.rule.output_type === 7) {
                let sum = 0;
                for(let i= 0 ;i< this.type7List.length; i++) {
                    let v = parseInt(this.type7List[i]);
                    sum = sum + (v << (7-i));
                }
                this.rule.action_value = sum;
            }


            if(this.rule.action_value === '') {
                alert('尚未設定輸出值!')
                return;
            }

            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editNodeRule').submit();
            }, 500);
        },
        delCheck: function (inx) {
            this.isDelete = true;
            this.rule = rules[inx];
            this.index = inx+1;
            $('#myModal').modal('show');
        },
        toDelete: function() {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");

            document.getElementById('delNodeRule').submit();

        },
        log: function(evt) {
            window.console.log(evt);
        },
        selectedClass: function(value) {
            alert(value);
        },
        getAndList: function() {
            let allList = [];
            this.andList = [];
            let check = {};
            let myList = [];
            if(this.node.relation !== null) {
                myList = this.node.relation;
            }

            for(let i=0; i<rules.length; i++) {
                let rule = rules[i];
                //檢查是否已加入itemList中
                if(allList.includes(i) === false) {
                    allList.push(i);
                } else {
                    //已加入略過
                    continue;
                }
                if(check.output === undefined) {
                    check.output = rule.output;
                    check.value = rule.action_value;
                } else if(check.output !== rule.output || check.value !== rule.action_value) {
                    check.output = rule.output;
                    check.value = rule.action_value;
                } else {
                    continue;
                }
                let itemTmp = [(i+1)];
                let idTmp = [rule.id];


                for(let j=i+1; j< rules.length; j++) {
                    let rule2 = rules[j];
                    if(check.output === rule2.output && check.value === rule2.action_value) {
                        itemTmp.push(j+1);
                        idTmp.push(rule2.id);
                        if(allList.includes(j) === false) {
                            allList.push(j);
                        }
                    }
                }
                if(idTmp.length > 1) {
                    let myCheck = false;
                    for(let k=0; k< myList.length; k++) {
                        let myTarget = myList[k][0];
                        if(idTmp.includes(myTarget)) {
                            myCheck = true;
                        }
                    }
                    this.andList.push({check:myCheck, id:idTmp, number:itemTmp});
                }
            }
        },
        showRelation: function () {

            this.getAndList();
            console.log(this.andList);

            this.isDelete = false;
            $('#myModal').modal('show');
        },
        setRelation: function () {
            let flag = false;
            let obj = []
            for(let i=0;i<this.andList.length;i++) {
                let myTemp = this.andList[i];
                if(myTemp.check === true) {
                    flag = true;
                    obj.push(myTemp.id);
                }
            }
            if(flag === true) {
                this.node.relation = JSON.stringify(obj);
            } else {
                this.node.relation = null;
            }
        },
        toEditRelation: function () {
            this.setRelation();
            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editNodeRelation').submit();
            }, 500);
        },
        setController: function() {
            this.getAndList();
            this.setRelation();
            if(this.node.relation !== null && typeof(this.node.relation) === 'string') {
                this.node.relation = JSON.parse(this.node.relation);
            }
            let obj = {rules : rules, relation:this.node.relation, inputs:myNode.inputs,outputs:myNode.outputs};
            let url = app_url+'/escape/command?room_id=1&command=93&macAddr='+myNode.node_mac+'&script='+JSON.stringify(obj);
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+token);
                },
                success: function (result) {
                    console.log(result);
                    if(result.code == 200) {
                        alert('設定完成');
                    } else {
                        alert('設定失敗');
                    }
                }
            });
        },
    }
});

$(document).ready(function() {
    let arr =[];
    for(let i=0;i<myNode.relation.length; i++) {
        arr = arr.concat(myNode.relation[i]);
    }
    orderItemOpt.createdRow = function( row, data, dataIndex ) {
        let value = parseInt(data[9]);
        let index = -1;
        for(let i=0;i<myNode.relation.length; i++) {
            if((myNode.relation[i]).includes(value)) {
                index = i;
            }
        }
        if ( index === 0 ) {
            $(row).addClass('blueRow');
        } else if ( index === 1 ) {
            $(row).addClass('infoRow');
        } else if ( index === 2 ) {
            $(row).addClass('greenRow');
        } else if ( index === 3 ) {
            $(row).addClass('purpleRow');
        } else if ( index === 4 ) {
            $(row).addClass('redRow');
        }
    };
    table = $("#table1").dataTable(orderItemOpt);
});

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu2) {
        let newUrl = "/module/nodeDevice";
        document.location.href = newUrl;
    } else if(x === menu3) {
        let newUrl = "/module/nodeFlow";
        document.location.href = newUrl;
    }
});



