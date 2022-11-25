/* flvTools.js : flv 播放器輔助工具
*  author: Jason Huang
*  播放器Element:  <video :name="element.id" style="width: 100%; height: 500px;" controls autoplay muted>
*  Example:
*  app.element = getVideoObject(device.macAddr, 1)
*  let player = flv_load_mds(player, app.element.id, app.element.url, app.element.number);
* */

let media_url = 'https://media.yesio.net/';
let rtmp_url = 'rtmp://media.yesio.net:1935/';

let emptyVideo = {'id': '', 'name':'', 'url': ''};

let mediaDataSource = {
    type: 'flv',
    isLive: true,
    url: ''
};

function getVideoObject(mac, number) {
    let url = media_url+mac+'/'+number + '.flv';
    let rtmp = rtmp_url+mac+'/'+number;
    let myVideo = {'id': ('video'+number),
        'number': number,
        'name':'影像'+number,
        'url': url,
        'rtmp':rtmp
    };
    return myVideo;
}


function getVideoList(mac, length) {
    let videoList = [];
    for(let x=1; x<=length;x++) {
        videoList.splice(x,1, getVideoObject(mac, number));
    }

    return videoList;
}

//elementId: 撥放器的id 必須設定在name
function flv_load_videos(videoList, elementId) {
    let playerList = [];

    for(let x=0; x<videoList.length;x++) {
        let video = videoList[x];
        playerList[x] = flv_load_mds(playerList[x], elementId, video.url, x)
    }
    return playerList;
}

//若只有一個撥放器，number可不填
function flv_load_mds(player, eId, url, number) {

    if(number === undefined)
        number = 0;
    let source = JSON.parse(JSON.stringify(mediaDataSource));
    source.url = url;

    let element = document.getElementsByName(eId)[number-1];

    if (player !== null) {
        if (player != null) {
            player.unload();
            player.detachMediaElement();
            player.destroy();
            player = null;
        }
    }
    player = flvjs.createPlayer(source, {
        enableWorker: false,
        //lazyLoadMaxDuration: 0,
        //lazyLoadRecoverDuration: 0,
        //deferLoadAfterSourceOpen: false,
        enableStashBuffer: false,
        stashInitialSize: 128,
        //seekType: 'range',
        isLive: true,
    });
    player.attachMediaElement(element);
    player.load();
    player.play();
    return player;
}
