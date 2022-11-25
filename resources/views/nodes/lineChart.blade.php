
<!DOCTYPE html>
<html lang="zh-CN" style="height: 100%">
<head>
    <meta charset="utf-8">
</head>
<body style="height: 100%; margin: 0">
<div>
    <span class="text-danger">{{$title}}</span>
</div>
<div id="container" style="height: 300px; width: 95%"></div>
<script src="{{asset('js/option/chartOption.js')}}"></script>
<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5.3.3/dist/echarts.min.js"></script>
<script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>

<script type="text/javascript">

    let dom = document.getElementById('container');
    let max = 500;
    //var zoom = JSON.parse(document.getElementById("zoom").value);
    let app_url = '{{ env('APP_URL') }}';
    let fields = {!! json_encode($dataKeys) !!};
    let titles = {!! json_encode($labels) !!};
    let reports = {!! json_encode($reports) !!};
    let app_id = {!! $app_id !!};
    let macAddr = "{!! $macAddr !!}";
    let isFirst = true;
    let title = "";
    let myChart = echarts.init(dom, null, {
        renderer: 'canvas',
        useDirtyRect: false
    });
    let app = {};

    let option = getLineOption(title, fields, titles, reports);
    //console.log('line option 1 :');

    //console.log('line option 2 :');
    //onsole.log(option);


    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }

    window.addEventListener('resize', myChart.resize);

    setTimeout(function () {
        myChart.resize;
    }, 3000);


    function changeTheme(check) {
        myChart.dispose();
        let Theme = (check==2) ? 'dark' : 'light';
        //基于准备好的dom，初始化echarts实例
        myChart = echarts.init(document.getElementById('container'), Theme);
        //使用刚指定的配置项和数据显示图表
        myChart.setOption(option);
    }

    function refreshLineData(data){
        //刷新單筆数据
        let origin_option = myChart.getOption();
        for(let j=0; j < fields.length; ++j) {
            let key = fields[j];
            origin_option.series[j].data.push(data[key]);
            if(isFirst || origin_option.series[j].data.length>max) {
                origin_option.series[j].data.shift();
            }
        }
        let date = data['recv'];
        date = new Date(date);
        date = date.toLocaleString();
        origin_option.xAxis[0].data.push(date);
        if(isFirst) {
            origin_option.xAxis[0].data.shift();
            isFirst = false;
        }
        if(origin_option.xAxis[0].data.length>max) {
            origin_option.xAxis[0].data.shift();
        }
        myChart.setOption(origin_option, true);
    }

    function loadLineDatas(datas){
        //多筆数据
        let origin_option = myChart.getOption();
        var mData = {"time":[]};
        var list = JSON.parse(JSON.stringify(datas));
        for(let i=0; i < list.length; ++i) {
            let report = list[i];
            let time = report.date;
            mData['time'].push(time);
            for(let i=0; i < fields.length; ++i) {
                let key =  fields[i];
                ;
                if(mData[key] == undefined) {
                    mData[key] = [];
                }
                mData[key].push(report.information[key])
            }
        }
        origin_option.xAxis[0].data = mData.time;

        for(let j=0; j < fields.length; ++j) {
            let key = fields[j];
            origin_option.series[j].data = mData[key];
        }

        myChart.setOption(origin_option, true);
    }

    function clearData(){
        //清除数据
        let myOption = myChart.getOption();
        for(let j=0; j < fields.length; ++j) {
            myOption.series[j].data = [0];
        }

        date = new Date().toLocaleString();
        myOption.xAxis[0].data= [date];
        myChart.setOption(myOption, true);
    }

    function receiveMessage(e) {
        //alert(e.origin + ' : ' + Array.isArray(e.data));
        if(Array.isArray(e.data)) {
            loadLineDatas(e.data);
        } else {
            refreshLineData(e.data)
        }
    }

    const socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
    // 2
    socket.on('connect', function()  {
        //socket.emit('web','Web socket is ready');
        socket.emit('storeClientInfo', { customId: macAddr });
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
        console.log('From server ---------------------------------');
        //console.log(typeof m);
        if (typeof m === 'string') {
            m = JSON.parse(m);
        }
        console.log(m);

        if(m.macAddr === macAddr && m.app_id === app_id) {
            refreshLineData(m);
        }
    });


    function getTime(obj) {
        let time = new Date(obj);
        let h = time.getHours();
        let m = time.getMinutes();
        let s = time.getSeconds();
        return h+ '時:'+ m + '分:' +s +'秒'
    }
</script>
</body>
</html>
