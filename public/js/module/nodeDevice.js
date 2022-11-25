
let input_data = {from:'', to: ''};
let output_data = {from:'', to: '', input:'', judge: '1', command: '', value: '', time: ''};

let myNode = {
    id: 0,
    node_name: '',
    node_mac: '',
    inputs: [],
    outputs: [],
};

let target_mac = null;
let target_name = '';

if(controllers.length>0 && controller_mac != null) {
    for(let i=0;i<controllers.length;i++) {
        let controller = controllers[i];
        if(controller.macAddr === controller_mac) {
            target_mac = controller.macAddr;
            target_name = controller.device_name;
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
}


myNode.node_name = target_name;
myNode.node_mac = target_mac;


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


let macList = [];

Vue.component('todo-item', {
    template: '<li>这是个待办项</li>'
});

function clearError() {
    let msg = document.getElementById("message");
    msg.remove();
}


let app = new Vue({
    el: '#app',
    data: {
        isEdit: false,
        node: JSON.parse(JSON.stringify(myNode)),
        list1: inputs,
        list2: nodeInputs,//add for inputs
        list3: outputs,
        list4: nodeOutputs,//add for outputs
        backup1: JSON.parse(JSON.stringify(inputs)),
        backup2: JSON.parse(JSON.stringify(nodeInputs)),
        backup3: JSON.parse(JSON.stringify(outputs)),
        backup4: JSON.parse(JSON.stringify(nodeOutputs)),

    },
    watch:{
        list2: function(value) {
            if(value.length===5) {//Keep data
                this.backup1 = JSON.parse(JSON.stringify(this.list1));
                this.backup2 = JSON.parse(JSON.stringify(this.list2));
            }
            if(value.length>5) {
                /*let test = arr_diff(this.li  st1, this.backup);
                console.log(test);*/
                this.list1 = JSON.parse(JSON.stringify(this.backup1));
                this.list2 = JSON.parse(JSON.stringify(this.backup2));
                alert("裝置限制5組");
            }
        },
        list4: function(value) {
            if(value.length===5) {//Keep data
                this.backup3 = JSON.parse(JSON.stringify(this.list3));
                this.backup4 = JSON.parse(JSON.stringify(this.list4));
            }
            if(value.length>5) {
                /*let test = arr_diff(this.li  st1, this.backup);
                console.log(test);*/
                this.list3 = JSON.parse(JSON.stringify(this.backup3));
                this.list4 = JSON.parse(JSON.stringify(this.backup4));
                alert("裝置限制5組");
            }
        }
    },
    methods: {


        back: function() {
            this.isEdit = false;
        },
        toSubmit: function() {
            let inputList = [];
            for(let i=0;i<this.list2.length;i++) {
                let device = this.list2[i];
                //inputList.push(device.macAddr);
                if(device.macAddr === this.node.node_mac) {
                    inputList.push('self');
                } else {
                    inputList.push(device.macAddr);
                }
            }
            this.node.inputs = (inputList.length > 0) ? JSON.stringify(inputList) : '';
            let outputList = [];
            for(let i=0;i<this.list4.length;i++) {
                let device = this.list4[i];
                //outputList.push(device.macAddr);
                if(device.macAddr === this.node.node_mac) {
                    outputList.push('self');
                } else {
                    outputList.push(device.macAddr);
                }
            }
            this.node.outputs = (outputList.length > 0) ? JSON.stringify(outputList) : '';
            if(this.node.inputs === '' && this.node.outputs === '') {
                alert('未選擇輸入輸出裝置!');
                return;
            }
            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editNodeDevice').submit();
            }, 500);
        },
        toDelete: function() {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");

            document.getElementById('delTeam').submit();

        },
        log: function(evt) {
            window.console.log(evt);
        },
        selectedClass: function(value) {
            alert(value);
        },
    }
});

function arr_diff (a1, a2) {

    let a = [], diff = [];

    for (let i = 0; i < a1.length; i++) {
        a[a1[i].id] = a1[i];
    }

    for (let i = 0; i < a2.length; i++) {
        if (a[a2[i].id]) {
            //console.log('no change : '+a2[i].id);
            delete a[a2[i].id];
        } else {
            //console.log('remove : '+a2[i].id);
            a[a2[i].id] = a2[i];
        }
    }

    for (let k in a) {
        diff.push(a[k]);
    }
    // console.log('increased : ');
    // console.log(diff);
    return diff;
}

function compare (a1, a2) {

    let a = [], diff = [];

    for (let i = 0; i < a1.length; i++) {
        a[a1[i].id] = a1[i];
    }

    for (let i = 0; i < a2.length; i++) {
        if (a[a2[i].id]) {
            //console.log('不變的 : '+a2[i].id);
            //delete a[a2[i].id];
        } else {
            diff.push(a2[i].id);
        }
    }

    // console.log('變更的 : ');
    // console.log(diff);
    return diff;
}


$(document).ready(function() {
    let msg = document.getElementById("message");
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
} );


$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu2) {
        let newUrl = "/module/nodeDevice";
        document.location.href = newUrl;
    } else if(x === menu1) {
        let newUrl = "/module/nodeFlow";
        document.location.href = newUrl;
    }  else if(x === menu3) {
        let newUrl = "/module/nodeStatus";
        document.location.href = newUrl;
    }
});

