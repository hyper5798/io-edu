//console.log('app_url :'+app_url );
//console.log('inputs :'+inputs.length, ', outputs :' + outputs.length);
let choiceBg = {backgroundColor:"#a6e4ff"};
let baseBg = {backgroundColor:"#ffffff"};
let cObj = {};
let iList = [];
let oList = [];
let typeNumber = 0;
if(link === 'module') {
    typeNumber = 2;
} else if(link === 'develop') {
    typeNumber = 1;
}


for(let i=0; i < nodes.length; i++) {
    let node = nodes[i];
    if(node.inputs !== null)
        iList = iList.concat(node.inputs);
    if(node.outputs !== null)
        oList = oList.concat(node.outputs);
    cObj[node.node_mac] = {inputs: node.inputs, outputs:node.outputs};
}
let iObj = {};
let oObj = {};
let unInputs = [];
let unOutputs = [];
for(let i=0; i < inputs.length; i++) {
    let input = inputs[i];
    iObj[input.macAddr] = input;
    if(input.macAddr !== null) {
        console.log(iList.includes(input.macAddr));
        if(iList.includes(input.macAddr) !== true) {
            unInputs.push(input);
        }
    }
}

for(let i=0; i < outputs.length; i++) {
    let output = outputs[i];
    oObj[output.macAddr] = output;
    if(output.macAddr !== null) {
        console.log(oList.includes(output.macAddr));
        if(oList.includes(output.macAddr) !== true) {
            unOutputs.push(output);
        }
    }
}

let controllerObj = {};
for(let i=0; i < controllers.length; i++) {
    let controller = controllers[i];
    controllerObj[controller.macAddr] = {data:controller, inputs:[], outputs:[]};
    if(cObj.hasOwnProperty(controller.macAddr)) {
        let tmp = cObj[controller.macAddr];
        if(tmp.inputs !== null) {
            for(let j=0; j < tmp.inputs.length; j++) {
                controllerObj[controller.macAddr].inputs.push(iObj[tmp.inputs[j]]);
            }
        }

        if(tmp.outputs !== null) {
            for(let k=0; k < tmp.outputs.length; k++) {
                controllerObj[controller.macAddr].outputs.push(oObj[tmp.outputs[k]]);
            }
        }
    }
}
let freeObj = {data:{device_name: '尚未加入控制器中'},inputs:unInputs, outputs:unOutputs};

let acts = [];

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};
//types.insert(0,{"type_id":0, "type_name": "不選擇"});
//networks.insert(0,{"id":0, "network_name": "不選擇"});
acts.insert(0,{"id":0, "value": common});
acts.insert(1,{"id":1, "value": defined});

let empty = {
    id: 0,
    device_name: '',
    macAddr: '',
    user_id: user.id,
    network_id: 1,
    make_command: 0,
    status: 2,
    updated_at: '',
    type_id: ''
};
let tree = {
    'core' : {
        'multiple' : false,
        'data' : []
    },
    'plugins' : [ "types" ],
    'types' : {
        'default' : {
            'icon' : 'fa fa-folder fa-fw'
        },
        'f-open' : {
            //'icon' : 'fa fa-folder-open fa-fw'
            'icon' : 'fa fa-folder fa-fw'
        },
        'f-closed' : {
            'icon' : 'fa fa-folder fa-fw'
        }
    }
};

function getTreeData(obj) {
    if(obj.inputs.length === 0 && obj.outputs.length===0) return null;
    let myTree = JSON.parse(JSON.stringify(tree));
    let input = null;
    let output = null;
    if(obj.inputs.length>0) {
        input = {"text": "輸入裝置("+obj.inputs.length+')',"state" : { "disabled" : true }, "children" :[]};
        for(let i=0; i < obj.inputs.length; i++) {
            let device = obj.inputs[i];
            if(device !== undefined && device !== null)
                input.children.push( { "text" : device.device_name+'-'+device.macAddr , "mac" : device.macAddr, "state" : { "selected" : false },"icon" : io_url});
        }
    }
    if(obj.outputs.length>0) {
        output = {"text": "輸出裝置("+obj.outputs.length+')',"state" : { "disabled" : true }, "children" :[]};
        for(let i=0; i < obj.outputs.length; i++) {
            let device = obj.outputs[i];
            if(device !== undefined && device !== null)
              output.children.push( { "text" : device.device_name+'-'+device.macAddr , "mac" : device.macAddr, "state" : { "selected" : false },"icon" : io_url});
        }
    }
    if(input) {
        myTree.core.data.splice(0,0, input);
    }
    if(output) {
        myTree.core.data.splice(myTree.core.data.length,0, output);
    }

    return myTree;
}

function checkTreeData(obj, mac) {
    let myTree = JSON.parse(JSON.stringify(tree));
    let input = null;
    let output = null;
    let check = false;
    if(obj.inputs.length>0) {
        input = {"text": "輸入裝置("+obj.inputs.length+")","state" : { "disabled" : true }, "children" :[]};
        for(let i=0; i < obj.inputs.length; i++) {
            let device = obj.inputs[i];

            if(device !== undefined && device !== null) {
                if(device.macAddr.indexOf(mac)> -1 || device.device_name.indexOf(mac) > -1) {
                    check = true;
                    input.children.push( { "text" : device.device_name+'-'+device.macAddr , "mac" : device.macAddr, "state" : { "selected" : true },"icon" : io_url});
                } else {
                    input.children.push( { "text" : device.device_name+'-'+device.macAddr , "mac" : device.macAddr, "state" : { "selected" : false },"icon" : io_url});
                }
            }

        }
    }
    if(obj.outputs.length>0) {
        output = {"text": "輸出裝置("+obj.outputs.length+")","state" : { "disabled" : true }, "children" :[]};
        for(let i=0; i < obj.outputs.length; i++) {
            let device = obj.outputs[i];
            if(device !== undefined && device !== null) {
                if(device.macAddr.indexOf(mac)> -1 || device.device_name.indexOf(mac) > -1) {
                    check = true;
                    output.children.push( { "text" : device.device_name+'-'+device.macAddr , "mac" : device.macAddr, "state" : { "selected" : true },"icon" : io_url});
                } else {
                    output.children.push( { "text" : device.device_name+'-'+device.macAddr , "mac" : device.macAddr, "state" : { "selected" : false },"icon" : io_url});
                }
            }
        }
    }
    if(input) {
        myTree.core.data.splice(0,0, input);
    }
    if(output) {
        myTree.core.data.splice(myTree.core.data.length,0, output);
    }
    return myTree;
}

function createTree(id,tree_data, isDestroy) {
    if(isDestroy) {
        $(id).jstree(true).destroy();
    }

    $(id).jstree(tree_data)
        .on("select_node.jstree", function (e, data) {
            if(data.selected.length) {
                //alert('The selected node is: ' + data.instance.get_node(data.selected[0]).text);
                let m = data.instance.get_node(data.selected[0]);
                let mac = m.original.mac;
                //alert( mac );
                if(iObj.hasOwnProperty(mac)) {
                    app.device = JSON.parse(JSON.stringify(iObj[mac])) ;
                }
                if(oObj.hasOwnProperty(mac)) {
                    app.device = JSON.parse(JSON.stringify(oObj[mac])) ;
                }
                app.isNew = true;
                app.isVerify = true;
            }
        });

    $(id).on('open_node.jstree', function (event, data) {
        data.instance.set_type(data.node,'f-open');
    });
    $(id).on('close_node.jstree', function (event, data) {
        data.instance.set_type(data.node,'f-closed');
    });
}

function updateListBg(list, choice_mac) {

    for(let i=0; i < list.length; i++) {
        let device = list[i];
        if(choice_mac === null || choice_mac === undefined) {
            device['bg'] = baseBg;
        } else if(device.macAddr.indexOf(choice_mac)> -1 || device.device_name.indexOf(choice_mac) > -1) {
            device['bg'] = choiceBg;
        } else {
            device['bg'] = baseBg;
        }
    }
    return list;
}

function updateObjectBg(obj, choice_mac) {
    for(let key in obj) {
        let device = obj[key];
        if(choice_mac === null || choice_mac === undefined) {
            device['bg'] = baseBg
        } else if(device.data.macAddr.indexOf(choice_mac)> -1 || device.data.device_name.indexOf(choice_mac) > -1) {
            device['bg'] = choiceBg
        } else {
            device['bg'] = baseBg
        }
    }
    return obj;
}

devices = updateListBg(devices, null);
controllerObj = updateObjectBg(controllerObj, null);

let app = new Vue({
    el: '#app',
    data: {
        deviceList: devices,
        controllerList: controllerObj,
        typeList:types,
        actList: acts,
        isNew: false,
        isVerify: false,
        isNotify: false,
        editPoint: -1,
        delPoint: -1,
        device: JSON.parse(JSON.stringify(empty)),
        choice: '',
        change_mac: '',
        isType: typeNumber,
    },
    mounted() {
        for(let i=0; i < nodes.length; i++) {
            let cMac = nodes[i]['node_mac'];
            console.log('controller mac:'+cMac);
            let id = '#'+cMac;

            let my_tree = getTreeData(controllerObj[cMac]);
            if(my_tree !==null)
                createTree(id, my_tree, false);
        }


        let free = '#free';
        let free_tree_data = getTreeData(freeObj);
        if(free_tree_data!==null)
            createTree(free, free_tree_data, false);

    },
    methods: {
        toControlModule(mac) {
            //alert(mac);
            let newUrl = "/module/nodeFlow?mac="+mac;
            document.location.href = newUrl;
        },
        toApp(mac) {
            //alert(mac);
            let newUrl = "/node/apps?mac="+mac;
            document.location.href = newUrl;
        },
        editDevice(inx) {
            this.device = JSON.parse(JSON.stringify(this.deviceList[inx]) );
            this.isNew = true;
            this.isVerify = true;
        },
        editController(mac) {
            this.device = JSON.parse(JSON.stringify(controllerObj[mac].data)) ;
            this.isNew = true;
            this.isVerify = true;
        },
        find() {
            for(let i=0; i < nodes.length; i++) {
                let cMac = nodes[i]['node_mac'];
                console.log('controller mac:'+cMac);
                let id = '#'+cMac;

                let my_tree = checkTreeData(controllerObj[cMac], this.choice);
                if(my_tree !== null && my_tree.core.data.length>0) {
                    createTree(id, my_tree, true);
                }
            }
            let free = '#free';

            let free_tree = checkTreeData(freeObj, this.choice);
            if(free_tree !== null && free_tree.core.data.length>0) {
                createTree(free, free_tree, true);
            }
            this.deviceList = updateListBg(this.deviceList, this.choice);
            this.controllerList = updateObjectBg(this.controllerList, this.choice);
        },
        newCheck: function () {
            this.isNew = true;
            this.isVerify = false;
            this.device = JSON.parse(JSON.stringify(empty));
            //console.log(this.cp)
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.isNew = true;
            this.isVerify = true;
            this.device= this.deviceList[index];

            console.log('Select index:' + index)
            console.log(this.device)
        },
        delCheck: function () {
            $('#myModal').modal('show');
        },
        back: function () {
            this.isNew = false;
            this.editPoint = -1;
            this.device = JSON.parse(JSON.stringify(empty));
            //console.log(this.userList);
        },
        toSubmit: function () {
            $.LoadingOverlay("show");
            document.getElementById('editDevice').submit();
        },
        toVerify: function () {
            //$.LoadingOverlay("show");
            let test = this;
            /*$.post(app_url+"/devices/verify",{mac:this.device.macAddr},function(result){
                console.log(result);
                test.isVerify = true;
            });*/
            $.ajax({
                url: app_url+'/devices/verify',
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+token);
                },
                data: {mac:this.device.macAddr},
                success: function (result) {
                    if(result.code == 400) {//未填MAC
                        alert(unfilledMac);
                    } else if(result.code == 404) {//不再產品名單中
                        alert(test.device.macAddr + notFoundMac);
                    } else if(result.code == 405) {//已被綁定
                        console.log(result);
                        let dateStr = getDateString(result.data.created_at);
                        alert(beBoundMsg1+result.data.email+beBoundMsg2+ dateStr);
                    } else if(result.code == 200){//可以完成綁定程序
                        app.device.type_id = result.data.type_id;
                        let yes = confirm(verifyOkMsg1+test.device.macAddr+verifyOkMsg2);

                        if (yes) {
                            test.isVerify = true;
                        } else {
                            test.isNew = false;
                        }

                    }
                }
            });
        },
    }
});

function getDateString(str) {
    console.log(str);
    let date = new Date(str);
    let year = date.getFullYear() + '';
    let month = date.getMonth() + 1;
    let day = date.getDate();
    console.log('day :'+day);

    if(month<10)
        month = '0' + month;
    if(day<10)
        day = '0' + day;

    let data = year +''+ month +''+ day;
    return data;
}

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delDevice').submit();
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
