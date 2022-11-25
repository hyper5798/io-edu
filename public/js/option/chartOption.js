let lineOption = {
    xAxis: {
        type: 'category',
        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    },
    yAxis: {
        type: 'value'
    },
    series: [{
        data: [820, 932, 901, 934, 1290, 1330, 1320],
        type: 'line',//線性圖
        //areaStyle: {} ,//線性圖轉區塊圖
        smooth: true,//平順線性圖
    }]
};

let areaOption = {
    xAxis: {
        type: 'category',
        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    },
    yAxis: {
        type: 'value'
    },
    series: [{
        data: [820, 932, 901, 934, 1290, 1330, 1320],
        type: 'line',//線性圖
        areaStyle: {} ,//線性圖轉區塊圖
        smooth: true,//平順線性圖
    }]
};

let barOption = {
    xAxis: {
        type: 'category',
        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    },
    yAxis: {
        type: 'value'
    },
    series: [{
        data: [820, 932, 901, 934, 1290, 1330, 1320],
        type: 'bar',//線性圖
        areaStyle: {} ,//線性圖轉區塊圖
        smooth: true,//平順線性圖
    }]
};

let gaugeOption = {
    tooltip: {
        //formatter: '{a} <br/>{b} : {c}'
        formatter: '{b} : {c}'
    },
    toolbox: {
        feature: {
            //restore: {},
            saveAsImage: {}
        }
    },
    series: [
        {
            name: '业务指标',
            type: 'gauge',
            axisLine: {            // 坐标轴线
                lineStyle: {       // 属性lineStyle控制线条样式
                    color: [[0.09, 'lime'], [0.82, '#1e90ff'], [1, '#ff4500']],
                    width: 10,
                    shadowColor: '#fff', //默认透明
                    shadowBlur: 10
                }
            },
            detail: {formatter: '{value}'},
            data: [{value: 50, name: '完成率'}]
        }
    ]
};

//Line option
let appOption = {
    title: {
        text: '溫濕度折線圖'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data: []
    },
    /* dataZoom: [
        {
            show: true,
            realtime: true,
            start: 0,
            end: 100
        }
    ], */
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    toolbox: {
        feature: {
            saveAsImage: {}
        }
    },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: []
    },
    yAxis: {
        type: 'value'
    },
    series: []
};

// 指定图表的配置项和数据
let gaugeOption1 = {

    tooltip: {				// 本系列特定的 tooltip 设定。
        show: true,
        formatter: "{b}：{c}%",
        backgroundColor: "rgba(255,255,50,0.7)",	// 提示框浮层的背景颜色。注意：series.tooltip 仅在 tooltip.trigger 为 'item' 时有效。
        borderColor: "#333",		// 提示框浮层的边框颜色。...
        borderWidth: 0,				// 提示框浮层的边框宽。...
        padding: 5,					// 提示框浮层内边距，单位px，默认各方向内边距为5，接受数组分别设定上右下左边距。...
        textStyle: {				// 提示框浮层的文本样式。...
            // color ,fontStyle ,fontWeight ,fontFamily ,fontSize ,lineHeight ,.......
        },
    },

    series: [
        {
            name: '度',
            type: 'gauge',
            z: 3,
            min: -30,
            max: 70,
            splitNumber: 10,
            radius: '100%',
            axisLine: {            // 坐标轴线
                lineStyle: {       // 属性lineStyle控制线条样式
                    width: 10
                }
            },
            axisTick: {            // 坐标轴小标记
                length: 15,        // 属性length控制线长
                lineStyle: {       // 属性lineStyle控制线条样式
                    color: 'auto'
                }
            },
            splitLine: {           // 分隔线
                length: 20,         // 属性length控制线长
                lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
                    color: 'auto'
                }
            },
            axisLabel: {
                backgroundColor: 'auto',
                borderRadius: 2,
                color: '#eee',
                padding: 3,
                textShadowBlur: 2,
                textShadowOffsetX: 1,
                textShadowOffsetY: 1,
                textShadowColor: '#222'
            },
            title: {
                // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                color: '#ffb12e',
                fontWeight: 'bolder',
                fontSize: 20,
                fontStyle: 'italic'
            },
            detail: {
                // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                formatter: function (value) {
                    /*value = (value + '').split('.');
                    value.length < 2 && (value.push('00'));
                    return ('00' + value[0]).slice(-2)
                        + '.' + (value[1] + '00').slice(0, 2);*/
                    return value + '度';
                },
                fontWeight: 'bolder',
                borderRadius: 3,
                backgroundColor: '#444',
                borderColor: '#aaa',
                shadowBlur: 5,
                shadowColor: '#333',
                shadowOffsetX: 0,
                shadowOffsetY: 3,
                borderWidth: 2,
                textBorderColor: '#000',
                textBorderWidth: 2,
                textShadowBlur: 2,
                textShadowColor: '#fff',
                textShadowOffsetX: 0,
                textShadowOffsetY: 0,
                fontFamily: 'Arial',
                width: 100,
                color: '#eee',
                rich: {}
            },
            data: [{value: 30, name: '溫度'}]
        }
    ]
};


let gaugeOption2 = {
    tooltip: {				// 本系列特定的 tooltip 设定。
        show: true,
        formatter: "{b}：{c}",
        backgroundColor: "rgba(255,255,50,0.7)",	// 提示框浮层的背景颜色。注意：series.tooltip 仅在 tooltip.trigger 为 'item' 时有效。
        borderColor: "#333",		// 提示框浮层的边框颜色。...
        borderWidth: 0,				// 提示框浮层的边框宽。...
        padding: 5,					// 提示框浮层内边距，单位px，默认各方向内边距为5，接受数组分别设定上右下左边距。...
        textStyle: {				// 提示框浮层的文本样式。...
            // color ,fontStyle ,fontWeight ,fontFamily ,fontSize ,lineHeight ,.......
        },
    },

    series: [
        {
            name: '濕度',
            type: 'gauge',
            z: 3,
            min: 0,
            max: 100,
            splitNumber: 10,
            radius: '100%',
            axisLine: {            // 坐标轴线
                lineStyle: {       // 属性lineStyle控制线条样式
                    color: [[0.09, 'lime'], [0.82, '#1e90ff'], [1, '#ff4500']],
                    width: 3,
                    shadowColor: '#fff', //默认透明
                    shadowBlur: 10
                }
            },
            axisLabel: {            // 坐标轴小标记
                fontWeight: 'bolder',
                color: '#fff',
                shadowColor: '#fff', //默认透明
                shadowBlur: 10
            },
            axisTick: {            // 坐标轴小标记
                length: 15,        // 属性length控制线长
                lineStyle: {       // 属性lineStyle控制线条样式
                    color: 'auto',
                    shadowColor: '#fff', //默认透明
                    shadowBlur: 10
                }
            },
            splitLine: {           // 分隔线
                length: 25,         // 属性length控制线长
                lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
                    width: 3,
                    color: '#fff',
                    shadowColor: '#fff', //默认透明
                    shadowBlur: 10
                }
            },
            pointer: {           // 分隔线
                shadowColor: '#fff', //默认透明
                shadowBlur: 5
            },
            title: {
                textStyle: {       // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                    fontWeight: 'bolder',
                    fontSize: 20,
                    fontStyle: 'italic',
                    color: '#fff',
                    shadowColor: '#fff', //默认透明
                    shadowBlur: 10
                }
            },
            detail: {
                backgroundColor: 'rgba(30,144,255,0.8)',
                borderWidth: 1,
                borderColor: '#fff',
                shadowColor: '#fff', //默认透明
                shadowBlur: 5,
                offsetCenter: [0, '50%'],       // x, y，单位px
                textStyle: {       // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                    fontWeight: 'bolder',
                    color: '#fff'
                },
                formatter: function (value) {
                    /*value = (value + '').split('.');
                    value.length < 2 && (value.push('00'));
                    return ('00' + value[0]).slice(-2)
                        + '.' + (value[1] + '00').slice(0, 2);*/
                    return value + '%';
                },
            },
            data: [{value: 60, name: '濕度'}]
        }
    ]

};

function getLineOption(title, fields,titles, list) {
    let mData = {time:[]};
    let serials = [];
    let tmp = JSON.parse(JSON.stringify(appOption));
    tmp.title.text = title;
    tmp.legend.data = titles;
    //Get serial
    titles.forEach(function(key) {
        mData[key] = []
        let serial = {
            name: key,
            type: 'line',
            data: []
        };
        serials.push(serial);
    });

    tmp.series = serials;

    //Get serial data
    if(list && list.length>0) {
        list.forEach(function(report) {
            mData['time'].push(report['recv']);
            for(let i=0; i < fields.length; ++i) {
                let key =  fields[i];
                ;
                if(mData[key] == undefined) {
                    mData[key] = [];
                }
                mData[key].push(report[key])
            }
        });
    } else {
        mData['time'].push(new Date().toLocaleString());
        for(let i=0; i < fields.length; ++i) {
            let key =  fields[i];
            ;
            if(mData[key] == undefined) {
                mData[key] = [];
            }
            mData[key].push(0)
        }
    }
    tmp.xAxis.data = mData.time;

    for(let j=0; j < fields.length; ++j) {
        let key = fields[j];
        tmp.series[j].data = mData[key];
    }

    return tmp;
}

function refreshLineData(myChart, fields, data){
    //刷新数据
    let origin_option = myChart.getOption();
    for(let j=0; j < fields.length; ++j) {
        let key = fields[j];
        origin_option.series[j].data.push(data[key]);
    }
    let date = data['recv'];
    date = new Date(date);
    date = date.toLocaleString();
    origin_option.xAxis[0].data.push(date);

    myChart.setOption(origin_option, true);
}


function getGaugeOption(name, value, set) {

    let myOption = getGaugeSetting(gaugeOption, name, value, set);

    return myOption;
}

function getGaugeOption1(name, value, set) {

    let myOption = getGaugeSetting(gaugeOption1, name, value, set);

    return myOption;
}

function getGaugeOption2(name, value, set) {

    let myOption = getGaugeSetting(gaugeOption2, name, value,set);

    return myOption;
}

function getGaugeSetting(target, name, value, set) {
    //console.log('set -->');
    //console.log(set);
    let mOption = JSON.parse(JSON.stringify(target));
    let data = mOption.series[0];
    let min,max,unit;
    if(set !== null) {
        min = set.min;
        max = set.max;
        unit = set.unit;
        data.axisLine.lineStyle.color = set.range;
    } else if(name === 'temperature' || name === '溫度') {
        min = -20;
        max = 50;
        unit = '°C';
    } else if(name === 'humidity' || name === '濕度') {
        min = 0;
        max = 100;
        unit = '%';
    } else {
        min = 0;
        max = 100;
        unit = '';
    }

    data.name = name;//values[i];
    data.detail.formatter = '{value}' + unit;
    data.data[0].value = value;//report[keys[i]];
    data.data[0].name = name;//values[i];
    let mUnit = '{value}' + unit;
    if(min !== undefined)
        data.min = min;
    if(max !== undefined)
        data.max = max;
    if(unit !== undefined)
        data.detail.formatter = mUnit;
    //console.log('Gauge option:');
    //console.log(mUnit);
    return mOption;
}

function refreshGaugeData(list, fields, data){
    //刷新数据

    for(let j=0; j < fields.length; ++j) {
        let key = fields[j];
        let myChart = list[j].gaugeChart;
        let option = myChart.getOption();
        option.series[0].data[0].value = data[key];
        myChart.setOption(option, true);
    }
}



