let dangerBg = {backgroundColor:"#f17996"};
let baseBg = {backgroundColor:"#d9d9d9"};
let highlightBg = {backgroundColor:"#eed764"};

function cross(o, a, b)
{
    //console.log("cross()");
    let v_ax = (parseFloat(a["lng"]) - parseFloat(o["lng"]));
    let v_ay = (parseFloat(a["lat"]) - parseFloat(o["lat"]));
    let v_bx = (parseFloat(b["lng"]) - parseFloat(o["lng"]));
    let v_by = (parseFloat(b["lat"]) - parseFloat(o["lat"]));
    return (v_ax * v_by)-(v_ay * v_bx);
}


function convex_hull(list)
{
    let in_point = [];
    let len = 0;
    for (let n=0; n<list.length; n++)
    {
        if(list[n]["lng"] !== '' && list[n]["lat"] !== '') {
            let newData = JSON.parse(JSON.stringify(list[n]));
            in_point.splice(len,0, newData);
            len++;
        }
    }
    let result = [];
    let tmp_p;
    let num = 0;
    let n = in_point.length;
    // (1) sort
    if (n < 3)
    {
        return result;
    }
    for (let i=0; i<n-1; i++)
    {
        for (let j=i+1; j<n; j++)
        {
            if (parseFloat(in_point[j]["lng"]) == parseFloat(in_point[i]["lng"]))
            {
                if (parseFloat(in_point[j]["lat"]) < parseFloat(in_point[i]["lat"]))
                {
                    //console.log("\nconvex_hull() 1-1:");
                    tmp_p = in_point[i];
                    in_point[i] = in_point[j];
                    in_point[j] = tmp_p;
                    //print(in_point);
                }
            }
            else if (parseFloat(in_point[j]["lng"]) < parseFloat(in_point[i]["lng"]))
            {
                //console.log("\nconvex_hull() 1-2:");
                tmp_p = in_point[i];
                in_point[i] = in_point[j];
                in_point[j] = tmp_p;
                //print(in_point);
            }
        }
    }
    // (2) convex hull
    // find lower hull
    for (let i=0; i<n; ++i)
    {
        num = result.length;
        while (num>=2 && cross(result[num-2], result[num-1], in_point[i])<=0)
        {
            result.pop();
            num = result.length;
        }
        result.push(in_point[i]);
    }
    // find upper hull
    num = result.length;
    for (let i=n-2, t=num+1; i>=0; --i)
    {
        num = result.length;
        while (num>=t && cross(result[num-2], result[num-1], in_point[i])<=0)
        {
            result.pop();
            num = result.length;
        }
        result.push(in_point[i]);
    }
    //result.pop();
    //console.log("\nconvex_hull() 2:");
    //print(result);
    return result;
}

function work_clearLine(path) {
    path.setMap(null);
    path = null;
    return null;
}

function  work_getLine(map, list, color) {
    let start = new google.maps.LatLng(list[0].lat,list[0].lng);
    let end = new google.maps.LatLng(list[1].lat,list[1].lng);
    let myTrip=[start,end];
    if(color === undefined) {
        color = "#0000FF";
    }
    let path = new google.maps.Polyline({
        path:myTrip,
        strokeColor:color,
        strokeOpacity:0.8,
        strokeWeight:2
    });
    path.setMap(map);
    return path;
}

function getDistance2(p1,p2) {
    if(p1.hasOwnProperty('lat')) {
        let latLngA = new google.maps.LatLng(p1.lat, p1.lng);
        let latLngB = new google.maps.LatLng(p2.lat, p2.lng);
        return google.maps.geometry.spherical.computeDistanceBetween (latLngA, latLngB);
    } else {
        return google.maps.geometry.spherical.computeDistanceBetween(p1,p2);
    }
}

let rad = function(x) {
    return x * Math.PI / 180;
};

let getDistance = function(p1, p2) {
    let R = 6378137; // Earth???s mean radius in meter
    let dLat;
    let dLong;
    let a;
    if(p1.hasOwnProperty('lat')) {
        dLat = rad(p2.lat - p1.lat);
        dLong = rad(p2.lng - p1.lng);
        a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(rad(p1.lat)) * Math.cos(rad(p2.lat)) *
            Math.sin(dLong / 2) * Math.sin(dLong / 2);
    } else {
        dLat = rad(p2.lat() - p1.lat());
        dLong = rad(p2.lng() - p1.lng());
        a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(rad(p1.lat())) * Math.cos(rad(p2.lat())) *
            Math.sin(dLong / 2) * Math.sin(dLong / 2);
    }


    let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    let d = R * c;
    return d; // returns the distance in meter
};

function isInBlock(location,testArea) {
    if(google.maps.geometry.poly.containsLocation(location, testArea)){
        //alert("??????????????????");
        return true;
    }else{
        //alert("?????????????????????");
        return false;
    }
}

/* ???????????????:??????????????????
*  parameter
*  map:google map ??????
*  obj: markerList???????????????checkList??????????????????????????????infoList?????????????????????
*       max:??????????????????, icon: ???????????????
*  location: ????????????????????????????????????????????? lat()???????????????????????????????????????????????????????????????
*  list: ???????????????(??????????????? lat & lng) ???????????????????????????????????????
*  isOpenFlag: ??????????????????????????????
*  max: ?????????????????????
*  draggable: ????????????????????????
*  order: asc???????????????desc????????????
*  return : ??????????????????
*  ??????
*  markerCoords(obj.markerList[num], num);
* */
function makeMarker(map, obj, location, list, isOpenFlag, draggable, order) {
    if(draggable === undefined)
        draggable = true;
    if(order === undefined)
        order = 'asc';
    let loc = null;
    if(typeof location.lat === 'function') {
        loc = location;
    } else {
        let tmp = {};
        tmp.lat = parseFloat(location.lat);
        tmp.lng = parseFloat(location.lng);
        loc = new google.maps.LatLng(tmp.lat, tmp.lng);
    }

    if(obj === undefined || obj === null) {
        obj = {};
    }
    if(obj.markerList === undefined || obj.markerList === null)
        obj.markerList = [];
    if(obj.infoList === undefined || obj.infoList === null)
        obj.infoList = [];
    if(obj.checkList === undefined || obj.checkList === null)
        obj.checkList = [];
    let num = obj.markerList.length;
    if(obj.max !== undefined) {
        if(num===obj.max) return;
    }

    let contentTxt='';
    if(obj.title !== undefined && obj.title.length>0) {
        contentTxt = obj.title+(num+1)+'<br>';
    }
    if(location.device_name !== undefined && location.device_name.length>0) {
        contentTxt = contentTxt+'??????: '+location.device_name+'<br>';
    } else if(location.macAddr !== undefined && location.macAddr.length>0) {
        contentTxt = contentTxt+'?????????: '+location.macAddr+'<br>';
    }
    contentTxt = contentTxt +
    '??????: ' + getFixNumber(loc.lat() )+
    '<br>??????: ' + getFixNumber(loc.lng())
    if(location.hasOwnProperty('recv')) {
        if(!location.recv.includes('???')) {
            location.recv = getDayTime(location.recv);
        }
        contentTxt = contentTxt + '<br>??????: ' + location.recv;
    }
    let makerOption ={
        position: loc,
        map: map,
        draggable: draggable
    };
    if(obj.icon !== undefined && obj.icon != null ) {
        makerOption.icon= obj.icon;
    }

    if(order === 'asc') {//????????????
        num = obj.markerList.length;//num: asc????????????????????????
        obj.checkList.splice(num,1,1);
        obj.markerList[num] = new google.maps.Marker(makerOption);

        obj.infoList[num] = new google.maps.InfoWindow({
            content:contentTxt
        });
    } else {//????????????
        num = 0;
        obj.checkList.unshift(1);
        obj.markerList.unshift(new google.maps.Marker(makerOption));
        obj.infoList.unshift(new google.maps.InfoWindow({
            content:contentTxt
        }));
    }

    if(isOpenFlag) {
        obj.infoList[num].open(map, obj.markerList[num]);
    } else {
        obj.checkList[num] = obj.checkList[num] * -1;
    }

    obj.markerList[num].addListener('click',function(){
        obj.checkList[num] = obj.checkList[num] * -1;
        if(obj.checkList[num] > 0){
            obj.infoList[num].open(map, obj.markerList[num]);
        }else{
            obj.infoList[num].close();
        }
    });

    if(list !==undefined && list !== null) {
        list[(num)]['lat'] = getFixNumber(loc.lat());
        list[(num)]['lng'] = getFixNumber(loc.lng());
        if(location.hasOwnProperty('recv')) {
            if(!list[(num)]['recv'].includes('???')) {
                list[(num)]['recv'] = getDayTime(list[(num)]['recv'])
            }
        }
    }

    return num;
}

/* ???????????????:??????????????????
*  parameter
*  map:google map ??????
*  obj: markerList???????????????checkList??????????????????????????????infoList?????????????????????
*       max:??????????????????, icon: ???????????????
*  ?????? : let obj = {title:'??????',max:1000, lastNum:0};
*  ?????? icon??????????????????????????????
*  obj.icon = {
                url: point_url, // url
                scaledSize: new google.maps.Size(4, 4), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(2,2) // anchor
            };
*  report: ????????????????????????????????????????????? lat()???????????????????????????????????????????????????????????????
*  list: ???????????????(??????????????? lat & lng) ???????????????????????????????????????
*  isOpenFlag: ??????????????????????????????
*  max: ?????????????????????
*  draggable: ????????????????????????
*  order: asc???????????????desc????????????
*  return : ??????????????????
*  ??????
*  markerCoords(obj.markerList[num], num);
* */
function addLocationMarker(map, obj, location, list, isOpenFlag, draggable, order) {

    let num = makeMarker(map, obj, location, list, isOpenFlag, draggable, order)
    if(order === 'asc') {

        if(obj.infoList.length>0) {
            //?????????????????????
            //?????????lastNum??????
            obj.infoList[obj.lastNum].close(obj.lastNum);
            //???????????????????????????
            obj.lastNum++;
        }
    } else {
        //lastNum?????? listMark????????????1???????????????0
        //???showInfo?????????,lastNum???index
        //??????????????????lastNum +1???????????????0
        if(obj.infoList.length>1) {
            //?????????????????????
            //obj.lastNum = 1;//???2
            obj.lastNum ++;//?????????????????????1
            obj.infoList[obj.lastNum].close(obj.lastNum);
            obj.lastNum = 0; // ???????????????0 =>?????????????????????
        }
    }
    return num;
}

function work_listMark(map, obj, list, draggable, order) {
    if(list.length === 0) {
        return;
    }
    //????????????????????????
    if(order === 'asc') {
        for (let i = 0; i < list.length; i++) {
            let data = list[i];
            makeMarker(map, obj, data, null, false, draggable, order);
        }
        obj.lastNum = list.length-1;
    } else {
        for (let i = list.length-1; i >= 0; i--) {
            let data = list[i];
            makeMarker(map, obj, data, null, false, draggable, order);
        }
        obj.lastNum = 0;
    }
    //???????????????????????????
    obj.infoList[obj.lastNum].open(map, obj.markerList[obj.lastNum]);

    //???????????????????????????
    //lastNum = reportCount-1;
    //app.searchList = list;
}

function removeLastMaker(obj, order) {
    let num = 0;
    if(order === 'asc') {
        num = 0
    } else {
        num = obj.checkList.length - 1;
    }

    obj.checkList.splice(num,1,);
    obj.markerList.splice(num,1,);
    obj.infoList.splice(num,1,);
}

function removeMarkerCheck(obj, list , order) {
    if(obj.markerList === undefined) {
        return;
    }

    let num = obj.markerList.length
    let index = 0;
    if(num >= obj.max) {
        if(order === 'desc') {
            index = num-1;
        }
        obj.markerList[index].setMap(null);
        obj.markerList.splice(index,1);
        obj.checkList.splice(index,1,);
        obj.infoList.splice(index,1,);
        list.splice(index,1,);
    }
}

/* function: ??????????????????????????? 3/16 12???10???20???
*  obj : ???????????????????????????
* */
function getDayTime(obj) {
    let date = obj;
    if(typeof obj === 'string') {
        date = new Date(obj);
    }

    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let day = date.getDate();


    let h = date.getHours();
    let m = date.getMinutes();
    let s = date.getSeconds();
    return month+'/'+day+' '+h+ '???'+ m + '???' +s +'???'
}

function work_clearAllMakers(obj) {
    if(obj !== null && obj.hasOwnProperty('markerList')) {
        for(let i=0;i<obj.markerList.length;i++) {
            obj.markerList[i].setMap(null);
        }
        obj.markerList = [];
    }
    if(obj.hasOwnProperty('checkList')) {
        obj.checkList = [];
    }
    if(obj.hasOwnProperty('infoList')) {
        obj.infoList = [];
    }
    obj.count = 0;
    return obj;
}

function getFixNumber(num) {
    return parseFloat(num.toFixed(6));
}

function toHighlight(obj) {
    //console.log(obj);
    obj.style.background='#b3b3b3';
}

function restore(obj) {
    //console.log(obj);
    obj.style.background='#d9d9d9';
}

function createPolygon(map, list, type) {
    //???????????????
    let tmpList = convex_hull(list);

    if(type === undefined  || type === null) {
        type = 1;
    }

    let distanceList = [];


    let myTrip = [];
    let tripNum = 0;
    let tmp;


    for(let i=0;i<tmpList.length;i++) {
        if(i<(tmpList.length-1)) {
            //console.log('p'+(i+1)+'-p'+(i+2)+':'+getDistance2(tmpList[i], tmpList[i+1]));
            let length = getDistance2(tmpList[i], tmpList[i+1]);
            length = length.toFixed(2) + ' ??????';
            distanceList.splice(i,0, length);
            //app.distanceList.splice(i,0, getDistance2(tmpList[i], tmpList[i+1]));
        }
        let point = tmpList[i];

        //console.log('point:'+(i+1)+', lat:'+point.lat+', lng:'+point.lng);
        if(point.lat !== null && point.lng !== null) {
            tripNum++;
            let item = new google.maps.LatLng(point.lat,point.lng);
            myTrip.splice( i, 0, item );
            if(i===0) {
                tmp = item;
            }
        }
    }

    if(tripNum < 3) {
        alert('????????????????????????????????????!');
        return;
    }
    /*if(myPolygon !== null) {
        toClearBlock(myPolygon);
        myPolygon = null;
    }*/
    let color1 = "#838385";
    let color2 = "#ffe994";
    if(type === 2 ) {
        color1 = "#838385";
        color2 = "#ff4343";
    }

    let myPolygon = new google.maps.Polygon({
        paths: myTrip,
        strokeColor: color1,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: color2,
        fillOpacity: 0.35,
    });
    myPolygon.setMap(map);
    tmpList.splice((tmpList.length-1),1);
    return {block:myPolygon, list:JSON.parse(JSON.stringify(tmpList)), distanceList:JSON.parse(JSON.stringify(distanceList))};
}

function toClearBlock( obj) {
    if( obj.newBlock !== null) {
        obj.newBlock.setMap(null);
        obj.newBlock= null;
    }
}
