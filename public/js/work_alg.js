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

function getLine(map, list, color) {
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

let getDistance = function(p1, p2) {
    let R = 6378137; // Earth’s mean radius in meter
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
        //alert("在指定區域內");
        return true;
    }else{
        //alert("不在指定區域內");
        return false;
    }
}
