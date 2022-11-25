let table
let debug = true;
let checked = (findAll == 'true' ? true:false);
console.log(checked );

let arr = [];
for(let i = 0; i < keys.length; i++) {
    let item = {
        gaugeChart: '',       // chart 对象实例
        id: 'id' + i,       // 为了标示 id 不同
        name: values[i],
    }
    if(i==0) {
        item.unit = '度';
    } else {
        item.unit = '%';
    }
    arr.push(item);
}
if(debug) {
    console.log('keys:');
    console.log(keys);
    console.log('gauge list:');
    console.log(arr);
    console.log('report list:');
    console.log(reports);
}

function test() {
    let obj = {}
    let total = {}
    for(let i=0;i<reports.length;i++) {
        let item = reports[i];
        if(obj[item.macAddr] === undefined)
            obj[item.macAddr] = {}
        if(obj[item.macAddr][item.key1] === undefined) {
            obj[item.macAddr][item.key1] = 1
        } else {
            obj[item.macAddr][item.key1] = obj[item.macAddr][item.key1] +1;
        }
        if(total[item.key1] === undefined) {
            total[item.key1] = 1
        } else {
            total[item.key1] = total[item.key1] +1;
        }
    }
    let objString = JSON.stringify(obj);
    let totalString = JSON.stringify(total);
    alert('全部: '+totalString+'\n 個別 :' + objString);
}


let app = new Vue({
    el: '#app',
    data: {
        //typeList: types,
        gaugeList: arr,
        reportList: reports,
        isNew: false,
        editPoint: -1,
        delPoint: -1,
        tab:2,
        flag:0,
        checked: checked
    },
    mounted() {
        if(reports !== null && reports.length > 0 ) {
            let report = reports[(reports.length-1)];
            console.log('mounted report : ');
            console.log(report);
            for(let i = 0; i < this.gaugeList.length; i++) {
                let key = keys[i];
                let set  = null;
                let myOption = getGaugeOption(values[i], report[key], set);
                this.gaugeList[i].gaugeChart = echarts.init(document.getElementById(this.gaugeList[i].id));
                this.gaugeList[i].gaugeChart.setOption(myOption, true);
            }
        }

    },
    methods: {
        editCheck: function (index) {
            this.editPoint = index;
        },
        delCheck: function (index, name) {
            console.log(name);
            this.delPoint = index;
            // $('#myModal').modal('show');
        },
        findAll: function() {
            this.checked = !this.checked;
            let newUrl = "/reports?findAll="+ this.checked;
            //alert(newUrl);
            document.location.href = newUrl;
        },
    }
})

let opt= csvOpt;
opt.oLanguage = twLan;

$(document).ready(function() {
    table = $("#table1").dataTable(opt);
    //Defined CSV button
    let tableObj = $('#table1').DataTable();
    $("#export").on("click", function() {
        tableObj.button( '.buttons-csv' ).trigger();
    });

    $(".buttons-csv").detach();
} );

let dom = document.getElementById("container");
let myChart = echarts.init(dom);


let option = getLineOption('mytest', keys, values, reports);

if (option && typeof option === "object") {
    myChart.setOption(option, true);
}



let index = 0;
function switchOption() {

    console.log(index);
    if(index == 0) {
        option = getLineOption();
        index ++;
    } else if(index == 1) {
        option = barOption;
        index ++;
    } else if(index == 2) {
        option = areaOption;
        index ++;
    } else {
        option = gaugeOption;
        index = 0;
    }
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
}

function toDelete() {
    document.getElementById('delReports').submit();
}
