let mac = device.macAddr;
//測試
//mac = "room1"
mac = option+':'+device.macAddr;

window.onload = init("remoteVideo", mac);

function test() {
    //let canvas = document.getElementById('canvas');
    let canvas = $("#canvas")[0];
    let video = document.getElementById('remoteVideo');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, video.videoWidth, video.videoHeight); // for drawing the video element on the canvas
    //canvas.hidden = true;

    // PNG data url
    let image_data_url = canvas.toDataURL("image/png");

    // JPEG data url
    //let image_data_url = canvas.toDataURL('image/jpeg');

    //let image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");  // here is the most important part because if you dont replace you will get a DOM 18 exception.
    //window.location.href=image; // it will save locally

    /*let file = null;
    let blob = document.querySelector("#canvas").toBlob(function(blob) {
        file = new File([blob], 'test.png', { type: 'image/png' });
    }, 'image/png');*/

    //var link = document.getElementById('link');
    //link.setAttribute('download', 'MintyPaper.png');
    //link.setAttribute('href', canvas.toDataURL("image/png").replace("image/png", "image/octet-stream"));
    //link.click();

    let url = api_url+'/api/upload-image';
    let data = {img: image_data_url, option:option, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
    sendToApi(url,data);
}

function uploadImage(file_name) {
    //let canvas = document.getElementById('canvas');
    let canvas = $("#canvas")[0];
    let video = document.getElementById('remoteVideo');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, video.videoWidth, video.videoHeight); // for drawing the video element on the canvas
    canvas.hidden = true;

    // PNG data url
    //let image_data_url = canvas.toDataURL("image/png");
    // JPEG data url
    let image_data_url = canvas.toDataURL('image/jpeg');


    let url = api_url+'/api/upload-image';
    let data = {img: image_data_url, option:option, token:token, file_name:file_name, XDEBUG_SESSION_START:'PHPSTORM'};
    sendToApi(url,data);
}

function sendToApi(url,data) {

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        /*beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
        },*/
        success: function (result) {
           console.log(result);

        },
        error:function(err){
            console.log(err);
        },
    });
}


