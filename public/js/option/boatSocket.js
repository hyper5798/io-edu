const socket = io.connect(app_url,{reconnect: true,rejectUnauthorized: false});
// 2
socket.on('connect', function()  {
    //socket.emit('web','Web socket is ready');
    socket.emit('storeClientInfo', { customId:device_mac });
});

socket.on('disconnect', function()  {
    console.log('web disconnect id is:'+socket.id);
    if (socket.connected === false ) {
        //socket.close()
        socket.open();
    }
});

//一般用戶進行同步圍籬狀態
socket.on('sync_status', function(m) {

    if(app.hasOwnProperty('home') === false) {
        //影像監控不做同步圍籬狀態
        return;
    }
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    //console.log(m);
    //mac from web
    if(m.mac !== device_mac) {
        console.log('receive '+ m.mac + ', device: '+device_mac + ' is different device');
        return;
    }
    if(m.hasOwnProperty('home')) {
        console.log('Web->Server->Web : create_home_point');
        app.home.lat = m.data.lat;
        app.home.lng = m.data.lng;
        app.list[0] = JSON.parse(JSON.stringify(app.home));
        let homeLocation = new google.maps.LatLng(app.home.lat, app.home.lng);
        placeMarker(homeLocation);

    } else if(m.hasOwnProperty('block')) {
        console.log('Web->Server->Web : create_block_point');
        app.list = m.block;
        newBlock = getBlock(app.list, newBlock);
    } else if(m.hasOwnProperty('clear_marker')) {
        console.log('Web->Server->Web : clear_marker');
        clearMarkers();
    }  else if(m.hasOwnProperty('clear_block')) {
        console.log('Web->Server->Web : clear_block');
        toClearBlock();
    }  else if(m.hasOwnProperty('clear_report')) {
        console.log('Web->Server->Web : clear_report');
        clearReports();
    }

});

socket.on('news', function(m) {
    console.log(m.hello);
});

socket.on('update_mqtt_ul', function(m) {
    console.log('From server ---------------------------------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    console.log(m);
    if(m.macAddr !== device_mac) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    let target = 0;
    if(m.hasOwnProperty('app_id')) {
        target = statusTarget[m.app_id];
    }

    if(target ===1) {
        m.lat = parseFloat(m.lat);
        m.lng = parseFloat(m.lng);

        if(reportCount>0) {
            let number = reportCount-1;
            infowindow2[number].close(number);
        }
        placeReport(m, true);
    }
    let mChoice = 0;
    if(m.hasOwnProperty('app_id')) {
        mChoice = statusTarget[m.app_id];
    }

    updateStatus(mChoice, m);
});

socket.on('http_report_data', function(m) {
    console.log('From server : http_report_data ------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    //console.log(m);
    //macAddr from report
    if(m.macAddr !== device_mac) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    let mChoice = 0;
    if(m.hasOwnProperty('app_id')) {
        mChoice = statusTarget[m.app_id];
    }
    updateStatus(mChoice, m);

});

socket.on('usv_specified_target', function(m) {
    console.log('From server : usv_specified_target ---------');
    if (typeof m === 'string') {
        m = JSON.parse(m);
    }
    console.log(m);
    if(m.macAddr !== device_mac) {
        console.log('receive '+ m.macAddr + ', device: '+device_mac + ' is different device');
        return;
    }
    app.isRun = false;
    window.setTimeout(function () {
        if(m.key === 3) {
            stop();
            alert('無人船已到達指定目標\n你可以使用遠端控制!');
            //app.toViewControl();
        } else if(m.key === 2) {
            alert('自動巡航完成!');
        } else if(m.key === 95) {
            cancelHomeCheck();
            app.isSetHome = true;
            alert('無人船設定HOME點完成!');
        }
    }, 500);
});

function updateStatus(mChoice, m) {
    app.status[mChoice] = JSON.parse(JSON.stringify(m));

    if(mChoice ===3) {
        $("#usv_direction").rotate(statusObj[mChoice]['key3']);
    }

    if(mChoice ===1) {
        m.lat = parseFloat(m.lat);
        m.lng = parseFloat(m.lng);

        if(reportCount>0) {
            let number = reportCount-1;
            infowindow2[number].close(number);
        }
        placeReport(m, app.isOpenWindow);

        let msg = '';

        if(app.hasOwnProperty('home') === true) {
            if (app.parameter.set.trigger1 !== null) {
                if (parseInt(app.parameter.set.trigger1) >= parseInt(m.key5)) {
                    msg = '警告:左電池電力過低!';
                }
            }
            if (app.parameter.set.trigger2 !== null) {
                if (parseInt(app.parameter.set.trigger1) >= parseInt(m.key6)) {
                    if (msg.length > 0) {
                        msg = msg + ' 右電池電力過低!';
                    } else {
                        msg = '警告:右電池電力過低!';
                    }

                }
            }
        }
        if(msg.length>0) {
            app.alertMessage = msg;
        }
    }
}
