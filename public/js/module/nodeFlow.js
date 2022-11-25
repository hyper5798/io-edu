let isChangeFlow = false;
let isRemove = false;//避免doublick時同時執行刪除&編輯node動作
let isLoad = true;
let offsetX = 0;
let offsetY = 0;
let target_mac = null;
let target_name = '';
let numberOfElements = 0;
const canvasId = "#canvas";
let timeDescription = '0時0分~23時59分';
//Defined command limit
let cmdLength = 5;
let serverDescrption = '1~'+cmdLength;
let pre_input = {key1: '觸發值'};
let operators = [
    {"id": 1, "value": ">"},
    {"id": 2, "value": ">="},
    {"id": 3, "value": "="},
    {"id": 4, "value": "<="},
    {"id": 5, "value": "<"},
    {"id": 6, "value": "<>"},
];
let emptyContent = {name:'',mac:'', value:''}
let emptyNotify = {
    //notify: '',
    topic: '',
    content: JSON.parse(JSON.stringify(emptyContent)),
    friends: [''],
    //node_id: '',
    //node_mac: ''
};
let emptyInput = {
    data: {mac:'',type:0, field:'key1', operator:3, value:'', name:'',url:'',symbol:'='}
};
let emptyOutput = {
    data: {mac:'',type:0, value:'', time:'', name:'',url:'', topic:''}
};
let emptyScript = {
    id: 0,
    script_name: '',
    node_id: node_id,
    node_mac: controller_mac,
    relation: null,
    flow: null,
    notify: null,
};
//8 port Relay
let type7PortArray = [8,7,6,5,4,3,2,1];
let type7Array = [0,0,0,0,0,0,0,0];
let selectScript = null;
let commands ={};
let nodeData = {};
//備份及還原relation
let backupData = {};
let notify = {};
let typeObj = {};
let triggerDevices = JSON.parse(JSON.stringify(nodeInputs));
let reportDevices = JSON.parse(JSON.stringify(nodeInputs));
let sampleTime = {hour:7,minute:0};

function init() {
    //triggerDevices.splice(triggerDevices.length,0, {device_name:'定時', macAddr: 'time'});
    //增加修改type_id > 200 的輸出入邏輯合一控制器的mac改為self
    nodeInputs.forEach(function(nodeInput){
        if(nodeInput.type_id >199) {
            nodeInput.macAddr = 'self';
        }
    });
    nodeOutputs.forEach(function(nodeOutput){
        if(nodeOutput.type_id >199) {
            nodeOutput.macAddr = 'self';
        }
    });
    nodeInputs.splice(nodeInputs.length,0, {device_name:'命令控制', macAddr: 'server'});
    nodeInputs.splice(nodeInputs.length,0, {device_name:'定時控制', macAddr: 'time'});
    //nodeOutputs.splice(2,0, {device_name:'平台', macAddr: 'server'});
    nodeOutputs.splice(nodeOutputs.length,0, {device_name:'電子郵件', macAddr: 'mail'});
    nodeOutputs.splice(nodeOutputs.length,0, {device_name:'Line', macAddr: 'line'});
    nodeOutputs.splice(nodeOutputs.length,0, {device_name:'Line和郵件', macAddr: 'both'});
    for(let m=1; m<=cmdLength ; m++) {
        commands[('命令'+m)] = m;
    }
    for(let j=0;j<types.length;j++) {
        let type = types[j];
        typeObj[type.type_id] = type;
    }

    for(let k=0;k<subscripts.length;k++) {
        let subscript = subscripts[k];
        subscript['check'] = false;
    }
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
    //Jason add for filter socket from server
    let filterList = [target_mac];

    if(myNode.inputs === null) {
        myNode.inputs = [];
    }

    if(myNode.outputs === null) {
        myNode.outputs = [];
    }

    filterList = filterList.concat(myNode.inputs);
    filterList = filterList.concat(myNode.outputs);
    console.log(filterList);

    if(scripts.length>0) {
        //selectScript = JSON.parse(JSON.stringify(scripts[0]));
        for(let i=0;i<scripts.length;i++) {
            let target = scripts[i];
            console.log(parseInt(target.id));
            if(parseInt(target.id) === script_id) {
                selectScript = JSON.parse(JSON.stringify(target));
            }
        }
    } else {
        selectScript = JSON.parse(JSON.stringify(emptyScript));
    }
    //Backup script
    backupData = JSON.parse(JSON.stringify(selectScript));
    //Jason for test
    selectScript.flow = JSON.stringify(selectScript.flow);
}

function addNode({ id, posX, posY, scenario, data }) {
    if (typeof id == "undefined") {
        numberOfElements++;
        id = "taskcontainer" + numberOfElements;
    }

    if (scenario === "task") {
        if(data.hasOwnProperty('operator')) {
            if(data.operator === 1) {
                data.symbol = '>';
            } else if(data.operator === 2) {
                data.symbol = '>=';
            } else if(data.operator === 3) {
                data.symbol = '=';
            } else if(data.operator === 4) {
                data.symbol = '<=';
            } else if(data.operator === 5) {
                data.symbol = '<';
            } else if(data.operator === 6) {
                data.symbol = '<>';
            }
        }
        if(data.mac === 'time') {
            data.field_name = '時間';
        } else {
            let tmp = typeObj[data.type];
            if(tmp && (tmp.category === 1 ||tmp.category === 4 ) ) {
                if(tmp.fields !== null) {
                    let fields = tmp.fields;
                    data.field_name = fields[data.field];
                } else {
                    data.field_name =  data.field;
                }
            }
        }
    } else {
        //只有不是重載時才加入mail設定
        if(isLoad===false && (data.mac === "mail" || data.mac === "line" || data.mac === "both")) {
            //在此將mail notify設定備份
            notify[id] = app.currentNotify;
            app.currentOutput.data.value = id;
        }
    }

    const taskNode = `
        <div class="window task node" id="${id}" data-nodetype="task" style="left: ${posX}px; top: ${posY}px;">
          <div class="ml-1 input-container">
             <img class="device-image" src="${data.url}">  
             ${data.name}
            <div class="button-remove">x</div>
          </div>
          <div class="details-container">
             <div>
                觸發${data.field_name} ${data.symbol}${data.value}
             </div>
             <!--<div>
                <img src="${data.url}" width="30" height="30">
                ${data.mac}
             </div>-->
          </div>
        </div>
      `;

    const taskNode2 = `
        <div class="window task node" id="${id}" data-nodetype="task" style="left: ${posX}px; top: ${posY}px;">
          <div class="ml-1 input-container">
             <img class="device-image" src="${data.url}">  
             ${data.name}
            <div class="button-remove">x</div>
          </div>
          <div class="details-container">
             <div>
                <!--命令 :${data.field_name}-->
                命令${data.value}
             </div>
          </div>
        </div>
      `;

    const decisionNode = `
        <div class="window task node" id="${id}" data-nodetype="decision" style="left: ${posX}px; top: ${posY}px;">
          <div class="ml-2 output-container">
                 <img class="device-image" src="${data.url}">  
                 ${data.name}
            <div class="button-remove">x</div>
          </div>
          <div class="details-container">
             <div>
                動作值:${data.value} &nbsp; 時間:${data.time}
             </div>
          </div>
        </div>
      `;

    const decisionNode2 = `
        <div class="window task node" id="${id}" data-nodetype="decision" style="left: ${posX}px; top: ${posY}px;">
          <div class="ml-2 output-container">
                 <img class="device-image" src="${data.url}">  
                 ${data.name}
            <div class="button-remove">x</div>
          </div>
          <div class="details-container">
             <div>
                主旨:${data.topic}
             </div>
          </div>
        </div>
      `;

    const decisionNode3 = `
        <div class="window task node" id="${id}" data-nodetype="decision" style="left: ${posX}px; top: ${posY}px;">
          <div class="ml-2 output-container">
                 <img class="device-image" src="${data.url}">  
                 ${data.name}
            <div class="button-remove">x</div>
          </div>
          <div class="details-container">
             <div>
                訊息:${data.topic}
             </div>
          </div>
        </div>
      `;
    const decisionNode4 = `
        <div class="window task node" id="${id}" data-nodetype="decision" style="left: ${posX}px; top: ${posY}px;">
          <div class="ml-2 output-container">
                 <img class="device-image" src="${data.url}">  
                 ${data.name}
            <div class="button-remove">x</div>
          </div>
          <div class="details-container">
             <div>
                上報裝置:${data.topic}
             </div>
          </div>
        </div>
      `;
    if (scenario === "task" && data.mac!== "server") {
        $(taskNode).appendTo(canvasId);
    } else if (scenario === "task" && data.mac === "server" ) {
        $(taskNode2).appendTo(canvasId);
    }else if (scenario === "decision" && data.mac === "server" ) {
        $(decisionNode4).appendTo(canvasId);
    }else if (scenario === "decision" && data.mac === "mail" ) {
        $(decisionNode2).appendTo(canvasId);
    } else if (scenario === "decision" && (data.mac === "line" || data.mac === "both")) {
        $(decisionNode3).appendTo(canvasId);
    }else if (scenario === "decision" && data.mac !== "mail" && data.mac !== "line") {
        $(decisionNode).appendTo(canvasId);
    }

    addEndpoint({ id, scenario });

    jsPlumb.draggable(id);
    nodeData[id] = data;
    //console.log(nodeData);
}

function addEndpoint({ id, scenario }) {
    if (scenario == "task") {


        jsPlumb.addEndpoint(
            id,
            {
                uuid: id + "rm-out",
                isSource: true,
                anchor: "Right"
            }
        );
    }

    if (scenario == "decision") {
        jsPlumb.addEndpoint(
            id,
            {
                uuid: id + "lm-in",
                isTarget: true,
                anchor: [0, 0.5]
            }
        );
    }
}

function saveFlowchart() {
    const nodes = [];
    let relation = {};

    $(".node").each((idx, elem) => {
        const $elem = $(elem);
        let id = $elem.attr("id");
        nodes.push({
            blockId: id,
            nodetype: $elem.attr("data-nodetype"),
            positionX: parseInt($elem.css("left"), 10),
            positionY: parseInt($elem.css("top"), 10)
        });
        //更動輸出: 由 mail 改成其他裝置時移除 mail notify
        if(notify.hasOwnProperty(id)) {
            if(nodeData[id]['mac'] !== 'mail' && nodeData[id]['mac'] !== 'line' && nodeData[id]['mac'] !== 'both') {
                delete notify[id];
            }
        }
        relation[id] = nodeData[id];
    });

    const connections = [];

    $.each(jsPlumb.getConnections(), (idx, connection) => {

        connections.push({
            uuids: connection.getUuids()
        });
    });

    if(connections.length === 0) {
        alert('尚未建立連線');
        return false;
    }  else if(connections.length > 10) {
        alert('連線數不可以超過10');
        return false;
    }

    const flowChart = {};
    flowChart.nodes = nodes;
    flowChart.connections = connections;
    flowChart.numberOfElements = numberOfElements;
    if(flowChart.nodes.length === 0) {
        flowChart.numberOfElements = 0;
    }
    const flowChartJson = JSON.stringify(flowChart);

    $("#jsonOutput").val(flowChartJson);
    app.script.flow = flowChartJson;
    app.script.relation = JSON.stringify(relation);
    if(Object.keys(notify).length>0) {
        app.script.notify = JSON.stringify(notify);
    } else {
        app.script.notify = '';
    }

    return true;
}

function checkFlowchart() {
    const nodes = [];
    let relation = {};

    $(".node").each((idx, elem) => {
        const $elem = $(elem);
        let id = $elem.attr("id");
        nodes.push({
            blockId: id,
            nodetype: $elem.attr("data-nodetype"),
            positionX: parseInt($elem.css("left"), 10),
            positionY: parseInt($elem.css("top"), 10)
        });
        //更動輸出: 由 mail 改成其他裝置時移除 mail notify
        if(notify.hasOwnProperty(id)) {
            if(nodeData[id]['mac'] !== 'mail' && nodeData[id]['mac'] !== 'line' && nodeData[id]['mac'] !== 'both') {
                delete notify[id];
            }
        }
        relation[id] = nodeData[id];
    });

    const connections = [];

    $.each(jsPlumb.getConnections(), (idx, connection) => {

        connections.push({
            uuids: connection.getUuids()
        });
    });

    const flowChart = {};
    flowChart.nodes = nodes;
    flowChart.connections = connections;
    flowChart.numberOfElements = numberOfElements;
    if(flowChart.nodes.length === 0) {
        flowChart.numberOfElements = 0;
    }
    if(flowChart.nodes.length !== backupData.flow.nodes.length || flowChart.connections.length !== backupData.flow.connections.length) {
        return false;
    }

    let keys = Object.keys(relation);
    for(let i=0;i<keys.length;i++) {
        let data = relation[keys[i]];
        let data1 = backupData.relation[keys[i]];
        if(data.type !== data1.type || data.value !== data1.value || data.time !== data1.time) {
            return false;
        }
    }
    const flowChartJson = JSON.stringify(flowChart);

    $("#jsonOutput").val(flowChartJson);
    app.script.flow = flowChartJson;
    app.script.relation = JSON.stringify(relation);
    if(Object.keys(notify).length>0) {
        app.script.notify = JSON.stringify(notify);
    } else {
        app.script.notify = '';
    }

    return true;
}

function loadFlowchart() {
    isLoad = true;
    const flowChartJson = $("#jsonOutput").val();
    const flowChart = JSON.parse(flowChartJson);
    const nodes = flowChart.nodes;

    $.each(nodes, (index, elem) => {
        const id = elem.blockId;
        const posX = elem.positionX;
        const posY = elem.positionY;

        if (elem.nodetype == "task") {
            let data = emptyInput.data;
            if(nodeData.hasOwnProperty(id)) {
                data = nodeData[id];
            }

            addNode({ id, posX, posY, scenario: "task",data: data });
            repositionElement({ id, posX, posY });
        }

        if (elem.nodetype == "decision") {
            let data = emptyOutput.data;
            if(nodeData.hasOwnProperty(id)) {
                data = nodeData[id];
            }
            addNode({ id, posX, posY, scenario: "decision",data: data });
            repositionElement({ id, posX, posY });
        }
    });

    const connections = flowChart.connections;

    $.each(connections, function (index, elem) {
        jsPlumb.connect({
            uuids: elem.uuids
        })
    });

    numberOfElements = flowChart.numberOfElements;
    isLoad = false;
}

function repositionElement({ id, posX, posY }) {
    $(`#${id}`).css("left", posX);
    $(`#${id}`).css("top", posY);
    jsPlumb.repaint(id);
}

function getScript() {
    const flowChartJson = $("#jsonOutput").val();
    const flowChart = JSON.parse(flowChartJson);
    const nodes = flowChart.nodes;
    let cObj = {};
    const connections = flowChart.connections;

    $.each(connections, function (index, elem) {
        let list = elem.uuids;
        let outId = list[1].replace('lm-in','');
        let inId = list[0].replace('rm-out', '');
        //用輸出id判斷是否關聯
        if(cObj[outId] === undefined) cObj[outId] = {};
        if(cObj[outId].input === undefined) {
            cObj[outId].input = [];
        }
        if(cObj[outId].output === undefined) {
            cObj[outId].output = [];
        }
        if( cObj[outId].input.includes(inId) !== true)
            cObj[outId].input.push(inId);
        if( cObj[outId].output.includes(outId) !== true)
            cObj[outId].output.push(outId);
    });
    let rule = [];
    console.log(cObj);
    let keys = Object.keys(cObj);
    for(let i =0; i<keys.length;i++) {
        let obj = cObj[[keys[i]]];
        let newObj = {input:[],output:[]};
        for(let j =0; j<obj.input.length;j++) {
            newObj.input.push(nodeData[obj.input[j]]);
        }
        for(let k =0; k<obj.output.length;k++) {
            newObj.output.push(nodeData[obj.output[k]]);
        }
        rule.push(newObj);
    }

    //console.log(rule);
    return rule;
}

function updateCheckbox() {
    let checkObj = {};
    for(let i= 0 ; i<app.currentNotify.friends.length; i++) {
        let tmp = app.currentNotify.friends[i];
        checkObj[tmp] = true;
    }
    for(let i= 0 ; i<app.lineList.length; i++) {
        let tmp = app.lineList[i];
        if(checkObj.hasOwnProperty(tmp.line_group)) {
            tmp.check = true;
        }
    }
}

function verifyCheckbox() {
    let count = 0;
    for(let i= 0 ; i<app.lineList.length; i++) {
        let tmp = app.lineList[i];
        if(tmp.check) {
            count++;
        }
    }
    if(count>0) return true;
    else return false;
}

function verifyMail() {
    for(let i= 0 ; i<app.currentNotify.friends.length; i++) {
        let tmp = app.currentNotify.friends[i];
        if(tmp.length === 0) {
            alert('尚未輸入收件者郵件!');
            return false;
        }
        if(tmp.includes('@') === false) {
            alert('收件者郵件格式錯誤!');
            return false;
        }
    }
    return true;
}

function toAjax(obj, url) {
    $.get( url,
        function(data){
            let status = JSON.stringify(data);
            obj.prompt = '訊息: '+status;

            setTimeout(function(){
                app.prompt = '';
            }, 5000);

        });
}

function Auth() {
    //alert(URL);
    //window.location.href = 'https://notify-bot.line.me/zh_TW/';
    window.open('https://notify-bot.line.me/zh_TW/', '_blank');
}

function restoreData() {
    //test
    app.script = JSON.parse(JSON.stringify(backupData));

    if(backupData.relation !== null && backupData.flow !== '') {
        nodeData = JSON.parse(JSON.stringify(backupData.relation));
    } else {
        nodeData = {};
    }
    if(backupData.flow !== null && backupData.flow !== '') {
        $("#jsonOutput").val(JSON.stringify(backupData.flow));
    }
    if(backupData.notify !== null && backupData.notify !== '') {
        notify = JSON.parse(JSON.stringify(backupData.notify));
    } else {
        notify = {};
    }
}
// ------------------------------------------------------------
init();

let app = new Vue({
    el: '#app',
    data: {
        isChangePass: isChangePass,
        isShowFlowJson: false,//true:顯示node json
        isChoice: 0,
        isShowNew: false,
        isNew: false,
        node: JSON.parse(JSON.stringify(myNode)),
        list2: nodeInputs,//add for inputs
        triggerList: triggerDevices,//add for inputs
        reportList: reportDevices,//add for inputs
        list4: nodeOutputs,//add for outputs
        operatorList: operators,
        index: 1,
        type7List: type7Array,
        type7PortList:type7PortArray,
        selectInput: pre_input,
        trigger:{field:'key1', value: ''},
        allList: alls,
        itemList: null,//For relation rule item
        idList: null,//For relation rule id
        andList: [],//For checked rule id
        test: '12345678',
        currentInput:JSON.parse(JSON.stringify(emptyInput)),//For drop input
        currentOutput:JSON.parse(JSON.stringify(emptyOutput)),//For drop output
        scripts: scripts,
        script: selectScript,
        backupInput: JSON.parse(JSON.stringify(emptyInput)),
        backupOutput: JSON.parse(JSON.stringify(emptyOutput)),
        time:JSON.parse(JSON.stringify(sampleTime)),
        message: '',
        error: '',
        isSendAck: false,
        currentNotify: JSON.parse(JSON.stringify(emptyNotify)),
        lineList: subscripts,
        example: '200',
        info: '觸發值 200',
        content: JSON.parse(JSON.stringify(emptyContent)),
        commandList: commands,
        api_url:app_url+'/node/command?api_key='+api_key+'&command=1',
        prompt: ''
    },
    mounted() {
        if(scripts.length>0) {
            this.isShowNew = true;
        }
        jsPlumb.ready(() => {

            jsPlumb.draggable(".window");
            jsPlumb.importDefaults({
                MaxConnections: 3,
                Endpoint: ["Dot", { radius: 6 }],
                EndpointStyle: { fill: "#8aa2d8" },
                EndpointHoverStyle: { fill: "#224492" },
                PaintStyle: { stroke: '#e85050', strokeWidth: 3},
                HoverPaintStyle: { stroke: "#9e1b1b", strokeWidth: 3 },
                Connector: [
                    //"Flowchart",
                    "Straight",
                    {
                        gap: 5,
                        midpoint: 0,
                        stub: 10,
                        cornerRadius: 2,
                    }
                ],

                ConnectionOverlays: [
                    ["Arrow", {
                        location: 1,
                        id: "arrow",
                        length: 15,
                        width: 14,
                        foldback: 0.6
                    }]
                ]
            });

            jsPlumb.setContainer('canvas');

            jsPlumb.bind("beforeDrop", ({ sourceId, targetId }, originalEvent) => {
                if (sourceId === targetId) {
                    return false;
                } else {
                    let source = nodeData.hasOwnProperty(sourceId) ? nodeData[sourceId] : null;
                    let target = nodeData.hasOwnProperty(targetId) ? nodeData[targetId] : null;
                    if(source.mac === 'server') {
                        if(target.mac === 'line' || target.mac === 'mail' || target.mac === 'both' ) {
                            alert('控制命令不能連接通知(Line 或 電子信件!');
                            return false;
                        } else if(target.mac === 'server') {
                            alert('控制命令不能連接平台(上報資料)');
                            return false;
                        }
                    }
                }

                const connections = [];

                $.each(jsPlumb.getConnections(), (idx, connection) => {

                    connections.push({
                        uuids: connection.getUuids()
                    });
                });

                if(connections.length > 10) {
                    alert('連線數限制不可以超過10');
                    return false;
                }

                return true;
            });

            jsPlumb.bind("click", function(connection) {

                let yes = confirm('你確定刪除連線嗎？');

                if (yes) {
                    jsPlumb.deleteConnection(connection);
                }
            });

            $("#resetButton").on("click", () => {
                numberOfElements = 0;
                jsPlumb.empty("canvas");
            });

            $(canvasId).on("click", ".button-remove", function () {
                isRemove = true;
                //alert(numberOfElements);
                let yes = confirm('你確定刪除設定嗎？');

                if (yes) {
                    const targetNode = $(this)[0];
                    const id = targetNode.parentNode.parentNode.id;
                    //alert(id);
                    //刪除node一併刪除mail notify
                    if(notify.hasOwnProperty(id)) {
                        delete notify[id];
                    }
                    const parentnode = $(this)[0].parentNode.parentNode;
                    jsPlumb.deleteConnectionsForElement(parentnode);
                    jsPlumb.removeAllEndpoints(parentnode);
                    $(parentnode).remove();
                }
            });

            //
            $(canvasId).on("dblclick", ".task", function () {
                if(isRemove) {
                    isRemove = false;
                    return;
                }

                const targetNode = $(this)[0];
                //Record node data
                const $elem = $(targetNode);
                const id = targetNode.id;
                const posX = parseInt($elem.css("left"), 10);
                const posY = parseInt($elem.css("top"), 10);
                //For input device
                if($elem.attr("data-nodetype") === 'task') {
                    app.currentInput.data = nodeData[targetNode.id];
                    app.isChoice = 2;
                    app.currentInput.id = id;
                    app.currentInput.posX = posX;
                    app.currentInput.posY = posY;
                    app.currentInput.scenario = 'task';
                    if(app.currentInput.data.mac === 'time') {
                        app.description = timeDescription;
                        let arr = app.currentInput.data.value.split(':');
                        if(arr.length === 2) {
                            app.time.hour = parseInt(arr[0]);
                            app.time.minute = parseInt(arr[1]);
                        }
                    } else if(app.currentInput.data.mac === 'server') {
                        app.description = serverDescrption;
                        app.api_url = app_url+'/node/command?api_key='+api_key+'&command='+app.currentInput.data.value;

                    } else {
                        let tmp = typeObj[app.currentInput.data.type];

                        if(tmp) {
                            app.selectInput = tmp.fields;
                            app.description = tmp.description;
                        } else {
                            app.selectInput = [];
                            app.description = '';
                        }
                    }
                    //Backup input origin data
                    app.backupInput = JSON.parse(JSON.stringify(app.currentInput));
                } else if($elem.attr("data-nodetype") === 'decision') {
                    //For output device
                    app.currentOutput.data = nodeData[targetNode.id];
                    app.isChoice = 3;
                    app.currentOutput.id = id;
                    app.currentOutput.posX = posX;
                    app.currentOutput.posY = posY;
                    app.currentOutput.scenario = 'decision';
                    let tmp = typeObj[app.currentOutput.data.type];
                    if(tmp) {
                        app.description = tmp.description;
                    } else {
                        app.description = '';
                    }
                    if(app.currentOutput.data.type === 7) {
                        let sum = app.currentOutput.data.value;
                        for(let i= 0 ; i<app.type7List.length; i++) {
                            app.type7List[i] = (sum & (1<<(7-i)))>>(7-i);
                        }
                    }
                    //雙擊點選時將notify回給app.currentNotify
                    if(app.currentOutput.data.mac === 'mail' || app.currentOutput.data.mac === 'line' || app.currentOutput.data.mac === 'both') {
                        //在此將mail notify設定還原
                        if(notify.hasOwnProperty(id)) {
                            app.currentNotify = notify[id];
                        }
                        //在此將line friends checkbox設定還原
                        if(app.currentOutput.data.mac === 'line' || app.currentOutput.data.mac === 'both') {
                            updateCheckbox();
                        }
                    }

                    //Backup output origin data
                    app.backupOutput = JSON.parse(JSON.stringify(app.currentOutput));
                }
                $('#myModal').modal('show');

            });

            $(".ele-draggable").on("dragstart", (ev, ff) => {
                const { originalEvent, target } = ev;
                offsetX = ev.offsetX;
                offsetY = ev.offsetY;
                originalEvent.dataTransfer.setData("text", target.id); // e.g. button-add-task
            });

            $(canvasId).on("dragover", ev => {
                ev.preventDefault();
            });

            $(canvasId).on("drop", ev => {
                const { originalEvent } = ev;
                const posX = ev.pageX - offsetX; // 需要减去鼠标的偏移值
                const posY = ev.pageY - offsetY;

                if (!originalEvent.dataTransfer) return; // 连线时会触发 drop 事件，值为 undfined

                const data = originalEvent.dataTransfer.getData("text");

                if (data == "button-add-task") {
                    //addNode({ posX, posY, scenario: "task" });
                    app.currentInput = JSON.parse(JSON.stringify(emptyInput));
                    app.isChoice = 2;
                    app.currentInput.posX = posX;
                    app.currentInput.posY = posY;
                    app.currentInput.scenario = 'task';
                    app.description = '';
                    $('#myModal').modal('show');
                }

                if (data == "button-add-decision") {
                    //addNode({ posX, posY, scenario: "decision" });
                    app.currentOutput = JSON.parse(JSON.stringify(emptyOutput));
                    app.isChoice = 3;
                    app.currentOutput.posX = posX;
                    app.currentOutput.posY = posY;
                    app.currentOutput.scenario = 'decision';
                    app.description = '';
                    $('#myModal').modal('show');
                }
            });

            $("#loadButton").click(() => {
                jsPlumb.empty("canvas");
                restoreData();
                loadFlowchart();
            });

            restoreData();

            window.setTimeout(function () {
                loadFlowchart();
            }, 500);
        });
    },
    watch:{
        "time.hour": function (value) {
            //console.log('hour:'+value);
            if(value > 23) this.time.hour = 23;
            if(value < 0) this.time.hour = 0;
        },
        "time.minute": function (value) {
            //console.log('minute:'+value);
            if(value > 59) this.time.minute = 59;
            if(value < 0) this.time.minute = 0;
        },
        'script.flow': function (value) {
            console.log('script.flow:');
            console.log(value);
            isChangeFlow = true;
        },
        'currentNotify.content.value': function (value) {
            this.info = '';
            this.info = this.currentNotify.content.value+' '+this.currentNotify.content.name;
            this.info = this.info + '觸發值 '+this.example;
        }
    },
    methods: {
        toSendCommand(id) {
            let url = this.getUrl();
            toAjax(this, url);
        },
        commandChange(event) {
            //alert(event.target.value);
            let value = event.target.value;
            if(value === '')
                return;
            else
                value = parseInt(value);
            let keys = Object.keys(this.commandList);
            /*for(let n=0;n<keys.length;n++) {
                let mykey = keys[n];
                let myvalue = this.commandList[mykey];
                if(value === myvalue) {
                    this.currentInput.data.field_name = mykey;
                }
            }*/
            this.currentInput.data.value = event.target.value;
            this.api_url = app_url+'/node/command?api_key='+api_key+'&command='+this.currentInput.data.value;
        },
        getUrl() {
            this.api_url = app_url+'/node/command?api_key='+api_key+'&command='+this.currentInput.data.value;
            return this.api_url;
        },
        copyUrl(id) {
            let url = this.getUrl();
            let obj = document.getElementById("api_url");
            obj.value = url;
            obj.select(); // 選擇物件
            document.execCommand("Copy"); // 執行瀏覽器複製命令
            this.prompt = "訊息: 已複製好，可貼上。";
        },
        clean() {
            this.prompt = '';
        },
        //For output mail/line to set topic &  content
        reportChange(event) {
            for(let i=0;i<this.reportList.length;i++) {
                let device = this.reportList[i];
                if(event.target.value === device.macAddr) {
                    this.currentOutput.data.topic = device.device_name;
                    break;
                }
            }
        },
        targetChange(event) {
            console.log(event.target.value);
            this.currentNotify.content.mac = event.target.value;
            if(this.currentNotify.content.mac === 'time') {
                this.example = "10:00";
                this.currentNotify.content.name = '定時';
            } else {
                this.example = "200";
                for(let i=0;i<this.list2.length;i++) {
                    let device = this.list2[i];
                    if(event.target.value === device.macAddr) {
                        this.currentNotify.content.name = device.device_name;
                        break;
                    }
                }
            }
            this.info = '';
            this.info = this.currentNotify.content.value +' '+this.currentNotify.content.name;
            this.info = this.info + '觸發值 '+this.example;
            this.currentNotify.topic = this.currentNotify.content.name + '觸發';
        },
        addNewScript() {
            if(this.scripts.length == 5) {
                alert('控制器腳本已達5個');
                return;
            }
            this.isShowNew = false;
            this.script = JSON.parse(JSON.stringify(emptyScript));
        },
        cancleNew() {
            this.isShowNew = true;
            this.script = JSON.parse(JSON.stringify(selectScript));
        },
        inChange(event) {
            console.log(event.target.value);
            //console.log('backupInput.data.value : '+this.backupInput.data.value);
            //Is the selected device the same as the original device?
            if(event.target.value === 'server') {
                if(this.backupInput.data.mac === 'server') {
                    this.currentInput = JSON.parse(JSON.stringify(this.backupInput));
                } else {
                    this.currentInput.data.type = 0;
                    this.currentInput.data.name = "平台命令控制";
                    this.currentInput.data.url = computer_url;
                    this.currentInput.data.value = 1;
                    this.currentInput.data.field = "server";
                    this.description = serverDescrption;
                }
                return;
            }
            if(event.target.value === 'time') {
                //Is the selected time the same as the original setting?
                if(this.backupInput.data.mac === 'time') {
                    this.currentInput = JSON.parse(JSON.stringify(this.backupInput));
                } else {
                    this.currentInput.data.type = 0;
                    this.currentInput.data.name = "定時控制";
                    this.currentInput.data.url = clock_url;
                    this.currentInput.data.value = "07:00";
                    this.currentInput.data.field = "time";
                    this.description = timeDescription;
                    this.time = JSON.parse(JSON.stringify(sampleTime));
                }
                return;
            }
            //Is the selected device the same as the original device?
            if(event.target.value === this.backupInput.data.mac) {
                this.currentInput = JSON.parse(JSON.stringify(this.backupInput));
            } else {
                for(let i=0;i<this.list2.length;i++) {
                    let device = this.list2[i];
                    if(event.target.value === device.macAddr) {
                        this.currentInput.data.type = device.type_id;
                        this.currentInput.data.name = device.device_name;
                        this.currentInput.data.url = device.image_url;
                        this.currentInput.data.value = '';
                        break;
                    }
                }
            }

            if(this.currentInput.data.type !== null && this.currentInput.data.type !== '') {
                if(typeof(this.currentInput.data.type)==='string') {
                    this.currentInput.data.type = parseInt(this.currentInput.data.type);
                }
                //type.fields example: {key1: '輸入值'}
                /*for(let j=0;j<types.length;j++) {
                    let type = types[j];
                    if(this.currentInput.data.type === type.type_id) {
                        //this.selectInput 選擇欄位下拉選單
                        this.selectInput = type.fields;
                        break;
                    }
                }*/
                let tmp = typeObj[this.currentInput.data.type];

                if(tmp) {
                    this.description = tmp.description;
                    this.selectInput = tmp.fields;
                } else
                    this.description = '';

                if(this.currentInput.data.type === 25 || this.currentInput.data.type === 26) {//8bit
                    //this.selectInput 選擇欄位下拉選單增加API設定
                    //this.selectInput['pass'] = 'API設定密碼';
                    this.currentInput.data.operator = 3;
                    if(isChangePass === 0) {
                        if(this.currentInput.data.hasOwnProperty('change')) {
                            delete this.currentInput.data.change;
                        }
                    } else {
                        if(!this.currentInput.data.hasOwnProperty('change')) {
                            this.currentInput.data.change=0;
                        }
                    }

                }
            }
        },
        outChange(event) {
            console.log(event.target.value)
            if(event.target.value === 'server' || event.target.value === 'mail' || event.target.value === 'line' || event.target.value === 'both') {
                this.currentOutput.data.type = 0;
                if(event.target.value === 'server') {
                    this.currentOutput.data.url = computer_url;
                } else if(event.target.value === 'mail') {
                    this.currentOutput.data.url = gmail_url;
                    //在此將mail notify設定還原或新增
                    if(this.currentOutput.id && notify.hasOwnProperty(this.currentOutput.id)){
                        this.currentNotify = JSON.parse(JSON.stringify(notify[this.currentOutput.id]));
                        //判斷是否為不是mail loop
                        if(this.currentNotify.friends && this.currentNotify.friends.length>0) {
                            //console.log(this.currentNotify.friends[0].includes('@') );
                            //不是mail loop 清除
                            if(this.currentNotify.friends[0].includes('@') === false) {
                                this.currentNotify.friends = [''];
                            }
                        } else {
                            this.currentNotify.friends = [''];
                        }
                    } else {
                        this.currentNotify = JSON.parse(JSON.stringify(emptyNotify));
                    }
                } else if(event.target.value === 'line' || event.target.value === 'line') {
                    this.currentOutput.data.url = line_url;
                    //在此將mail notify設定還原或新增
                    if(this.currentOutput.id && notify.hasOwnProperty(this.currentOutput.id)){
                        this.currentNotify = JSON.parse(JSON.stringify(notify[this.currentOutput.id]));
                        //判斷是否為不是mail loop
                        if(this.currentNotify.friends && this.currentNotify.friends.length>0) {
                            //console.log(this.currentNotify.friends[0].includes('@') );
                            //不是mail loop 清除
                            if(this.currentNotify.friends[0].includes('@') === true) {
                                this.currentNotify.friends = [];
                            }
                        } else {
                            this.currentNotify.friends = [];
                        }
                    } else {
                        this.currentNotify = JSON.parse(JSON.stringify(emptyNotify));
                        //Line and mail default friends is different
                        //Line: [], mail: ['']
                        this.currentNotify.friends = [];
                    }
                    updateCheckbox();
                }
                //this.currentNotify.content = JSON.parse(JSON.stringify(this.currentNotify.content));
                return;
            }
            if(event.target.value === this.backupOutput.data.mac) {
                //回復原始值
                this.currentOutput = JSON.parse(JSON.stringify(this.backupOutput));
            } else {
                for(let i=0;i<this.list4.length;i++) {
                    let device = this.list4[i];
                    if(event.target.value === device.macAddr) {
                        this.currentOutput.data.type = device.type_id;
                        this.currentOutput.data.name = device.device_name;
                        this.currentOutput.data.url = device.image_url;
                    }
                }
            }

            let tmp = typeObj[this.currentOutput.data.type];
            if(tmp)
                this.description = tmp.description;
            else
                this.description = '';

            if(this.currentOutput.data.type === 7) {
                let sum = this.currentOutput.data.value;
                for(let i= 0 ; i<this.type7List.length; i++) {
                    this.type7List[i] = (sum & (1<<(7-i)))>>(7-i);
                }
            }
        },
        back: function() {
            this.isNew = false;
        },
        toSetNode: function() {
            if(this.isChoice === 2) {
                const targetNode = document.getElementById(this.currentInput.id);
                if(targetNode !== null) {
                    $(targetNode).remove();
                }
                if(this.currentInput.data.hasOwnProperty('change')) {
                    this.currentInput.data.change = this.currentInput.data.change ? 1: 0;
                }
                if(this.currentInput.data.mac==='time') {
                    let hour = (this.time.hour < 10) ? '0'+this.time.hour : this.time.hour;
                    let minute = (this.time.minute < 10) ? '0'+this.time.minute : this.time.minute;
                    this.currentInput.data.value = hour+':'+minute;
                }
                if(this.currentInput.data.mac==='server') {
                    this.currentInput.data.name = '平台命令控制';
                    this.currentInput.data.operator = 3;
                }
                addNode(this.currentInput);
                repositionElement(this.currentInput);
            } else if(this.isChoice === 3) {
                if(this.currentOutput.data.type === 0)  {
                    if(this.currentNotify.content.mac.length===0){
                        alert('尚未選擇觸發裝置!');
                        return;
                    }
                    if(this.currentOutput.data.mac==='mail') {
                        if(this.currentNotify.topic.length===0) {
                            alert('尚未輸入郵件主旨!');
                            return;
                        }
                        if(verifyMail() === false) {
                            return;
                        }
                        this.currentOutput.data.name = '電子郵件';
                        //this.currentOutput.data.value = this.currentNotify.topic;
                        this.currentOutput.data.topic = this.currentNotify.topic;
                    }
                    if(this.currentOutput.data.mac==='line' || this.currentOutput.data.mac==='both') {
                        if(verifyCheckbox() === false) {
                            alert('尚未加入Line notify通知群組!');
                            return;
                        }
                        if(this.currentOutput.data.mac==='line') {
                            this.currentOutput.data.name = 'Line';
                        } else {
                            this.currentOutput.data.name = 'Line & Email';
                        }

                        //this.currentOutput.data.value = this.currentNotify.topic;
                        this.currentOutput.data.topic = this.currentNotify.content.name+'觸發';
                    }
                    if(this.currentOutput.data.mac==='server') {
                        this.currentOutput.data.name = '平台資料上報';
                    }
                }

                const targetNode = document.getElementById(this.currentOutput.id);
                if(targetNode !== null) {
                    $(targetNode).remove();
                }
                if(this.currentOutput.data.type === 7) {
                    let sum = 0;
                    for(let i= 0 ;i< this.type7List.length; i++) {
                        let v = parseInt(this.type7List[i]);
                        sum = sum + (v << (7-i));
                    }
                    this.currentOutput.data.value = sum;
                }

                addNode(this.currentOutput);
                repositionElement(this.currentOutput);
            }
            this.isChoice = 0;//Avoid restore backup data
            $('#myModal').modal('hide');
        },
        saveFlow: function() {
            if(this.script.script_name.length === 0) {
                alert('尚未設定腳本名稱');
                return;
            }

            if(saveFlowchart() === false) return;

            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editNodeFlow').submit();
            }, 500);

        },
        setController: function() {

            if(this.script.id === 0) {
                alert('尚未儲存腳本!');
                return;
            }
            if(checkFlowchart() === false) {
                alert('已更動腳本,請先儲存腳本!');
                return;
            }

            $.LoadingOverlay("show");
            let rules = getScript();

            let obj = {id:this.script.id, rules : rules, inputs:myNode.inputs,outputs:myNode.outputs};
            let url = app_url+'/nodes/command?room_id=1&command=93&macAddr='+myNode.node_mac+'&script='+JSON.stringify(obj);

            this.isSendAck = false;

            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+token);
                },
                success: function (result) {
                    console.log(result);

                    window.setTimeout(function () {
                        $.LoadingOverlay("hide");
                        if(result.code !== 200) {
                            app.error ='傳送失敗';
                        }
                    }, 1000);

                }
            });
            window.setTimeout(function () {
                if(app.isSendAck !== true) {
                    app.error = '控制器設定腳本失敗';
                }
            }, 5000);
        },
        delScript() {
            this.isChoice = 0;
            $('#myModal').modal('show');
        },
        toDelete() {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");
            document.getElementById('delScript').submit();
        },
        //For add mail loop to friends
        addFriend() {
            if(this.currentNotify.friends.length>=10)
                return;
            this.currentNotify.friends.push('');
        },
        //For remove mail loop from friends
        delFriend(inx) {
            //alert(inx);
            this.currentNotify.friends.splice(inx,1);
        },
        //For line notify of friends
        editFriends() {
            this.currentNotify.friends = [];
            for(let i= 0 ;i< this.lineList.length; i++) {
                let tmp = this.lineList[i];
                if(tmp.check) {
                    this.currentNotify.friends.push(tmp.line_group);
                }
            }
        }
    }
});


$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu2) {
        let newUrl = "/module/nodeDevice";
        document.location.href = newUrl;
    } else if(x === menu1) {
        let newUrl = "/module/nodeFlow";
        document.location.href = newUrl;
    } else if(x === menu3) {
        let newUrl = "/module/nodeStatus";
        document.location.href = newUrl;
    }
});

$('#myModal').on('hide.bs.modal', function(e){
    //If cancel the device selection, restore the original data
    if(app.isChoice === 2) {
        //避免新加入的裝置還沒有定義ID造成問題
        if(app.backupInput.id) {
            nodeData[app.backupInput.id] = app.backupInput.data;
        }
    } else if(app.isChoice === 3){
        //避免新加入的裝置還沒有定義ID造成問題
        if(app.backupOutput.id) {
            nodeData[app.backupOutput.id] = app.backupOutput.data;
        }
    }
});

const socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
// 2
socket.on('connect', function()  {
    socket.emit('web','Web socket is ready');
});

socket.on('news', function(m) {
    console.log(m.hello +' to '+ m.customId);
    socket.emit('storeClientInfo',m);
});

socket.on('token_expire', function(m) {
    if(typeof m === 'string') {
        m = JSON.parse(m);
    }
    let token = m.token;
    if(token && token === data.token) {
        alert('登入時取得tokn時效(一天)已過期,登出後重新登入');
        document.location.href = logout_url;
    }
});

socket.on('update_node_status', function(m) {
    console.log('From server ---------------------------------');
    console.log( m);
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    //console.log(m);
    let mac = m.macAddr;
    if(mac !== controller_mac) return;

    if (typeof m.data === 'string')
        m.data = JSON.parse(m.data);
    if (typeof m.key2 === 'string')
        m.key2 = JSON.parse(m.key2);

    let status = null;

    if (m.data)
        status = m.data.key1;
    else
        status = m.key2;
    console.log(m.macAddr + ':' + status);
    if(status === 93) {
        app.isSendAck = true;
        app.message = '控制器設定完成';
    }
    /*window.setTimeout(function () {
        app.message = '';
    }, 5000);*/
});


