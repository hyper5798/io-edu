<!--
	此示例下载自 https://echarts.apache.org/examples/zh/editor.html?c=radar
-->
<!DOCTYPE html>
<html lang="zh-CN" style="height: 100%">
<head>
    <meta charset="utf-8">
</head>
<body style="height: 100%; margin: 10px;s">
<div id="container" style="height: 90%"></div>

<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5.3.3/dist/echarts.min.js"></script>

<script type="text/javascript">
    var values = {!! json_encode($values)  !!};
    var fieldAverages = {!! json_encode($fieldAverages)  !!};
    var indicators = Object.values(fieldAverages);
    var maxList = [];
    for(let i=0;i<indicators.length;i++) {
        let item = indicators[i];
        item.max = 10;
        maxList.push(item.max);
    }
    //alert(JSON.stringify(indicators));
    var dom = document.getElementById('container');
    var myChart = echarts.init(dom, null, {
        renderer: 'canvas',
        useDirtyRect: false
    });
    var app = {};

    var option;

    option = {
        title: {
            text: '雷達圖'
        },
        legend: {
            data: ['Allocated Budget']
        },
        radar: {
            indicator: indicators,
            axisName: {
                color: '#fff',
                backgroundColor: '#939090',
                borderRadius: 3,
                padding: [3, 5]
            }
        },
        series: [
            {
                name: 'Budget',
                type: 'radar',
                data: [
                    {
                        value: maxList,
                        name: 'Data C',
                        symbol: 'rect',
                        symbolSize: 12,
                        lineStyle: {
                            type: 'dashed'
                        },
                        label: {
                            show: true,
                            formatter: function (params) {
                                return params.value;
                            }
                        }
                    },
                    {
                        value: values,
                        name: 'Data D',
                        label: {
                            show: true,
                            formatter: function (params) {
                                return params.value;
                            }
                        },
                        areaStyle: {
                            color: new echarts.graphic.RadialGradient(0.1, 0.6, 1, [
                                {
                                    color: 'rgb(213,226,220)',
                                    offset: 0
                                },
                                {
                                    color: 'rgb(43,136,238)',
                                    offset: 1
                                }
                            ])
                        }
                    }
                ]
            }
        ]
    };

    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }

    window.addEventListener('resize', myChart.resize);
</script>
</body>
</html>
