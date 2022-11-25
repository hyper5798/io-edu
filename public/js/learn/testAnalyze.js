let table;
let fieldObj = {};
let levelObj = {};
fields.forEach(function(field){
    fieldObj[field.id] = field.title;
});
levels.forEach(function(level){
    levelObj[level.id] = level.title;
});

var dom = document.getElementById('container');
var myChart = echarts.init(dom, null, {
    renderer: 'canvas',
    useDirtyRect: false
});

var option;

option = {
    title: {
        text: 'Basic Radar Chart'
    },
    legend: {
        data: ['Allocated Budget', 'Actual Spending']
    },
    radar: {
        // shape: 'circle',
        indicator: [
            { name: 'Sales', max: 6500 },
            { name: 'Administration', max: 16000 },
            { name: 'Information Technology', max: 30000 },
            { name: 'Customer Support', max: 38000 },
            { name: 'Development', max: 52000 },
            { name: 'Marketing', max: 25000 }
        ]
    },
    series: [
        {
            name: 'Budget vs spending',
            type: 'radar',
            data: [
                {
                    value: [4200, 3000, 20000, 35000, 50000, 18000],
                    name: 'Allocated Budget'
                },
                {
                    value: [5000, 14000, 28000, 26000, 42000, 21000],
                    name: 'Actual Spending'
                }
            ]
        }
    ]
};

if (option && typeof option === 'object') {
    myChart.setOption(option);
}

window.addEventListener('resize', myChart.resize);

let appDom = new Vue({
    el: '#app',
    data: {
        recordList: records,
    },
    methods: {

        editCheck: function (index) {
        },

    }
});


$(document).ready(function() {
    //table = $("#table1").dataTable(defaultOpt);
} );




