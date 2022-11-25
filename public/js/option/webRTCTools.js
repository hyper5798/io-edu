// video tag
//const remoteVideo = document.querySelector('video#remoteVideo')
let remoteVideo = null;

let peerConn
//const room = 'room1'
let room = null;
let socket

function setElementId(id) {
    remoteVideo = document.getElementById(id);
}

function setRoomByMac(mac) {
    room = mac;
}

//
// connect socket.io
//
function connectIO() {
    // socket
    if(location.protocol === 'http:') {
        socket = io('ws://ioflex.yesio.net:3002')
    } else {
        socket = io('wss://ice.ioflex.yesio.net')
    }


    socket.on('ice_candidate', async (data) => {
        console.log('get ice_candidate')
        const candidate = new RTCIceCandidate({
            sdpMLineIndex: data.label,
            candidate: data.candidate,
        })
        await peerConn.addIceCandidate(candidate)
    })

    socket.on('answer', async (desc) => {
        console.log('get answer')
        // set remote description
        await peerConn.setRemoteDescription(desc)
    })

    socket.emit('join', room)
}

//
// init peer connection
//
function initPeerConnection() {
    const configuration = {
        iceServers: [
            //{
            //  urls: 'turn:ice.ioflex.yesio.net:3478',
            //  username: 'user',
            //  credential: '1234567890',
            //},
            {
                urls: 'stun:stun.l.google.com:19302',
            },
        ],
    }
    peerConn = new RTCPeerConnection(configuration)

    peerConn.addTransceiver('video', { direction: 'recvonly' })
    peerConn.addTransceiver('audio', { direction: 'recvonly' })

    // 找尋到 ICE 候選位置後，送去 Server 與另一位配對
    peerConn.onicecandidate = (e) => {
        if (e.candidate) {
            console.log('發送 ICE')
            // 發送 ICE
            socket.emit('ice_candidate', room, {
                label: e.candidate.sdpMLineIndex,
                id: e.candidate.sdpMid,
                candidate: e.candidate.candidate,
            })
        }
    }

    // 監聽 ICE 連接狀態
    peerConn.oniceconnectionstatechange = (e) => {
        if (e.target.iceConnectionState === 'disconnected') {
            remoteVideo.srcObject = null
            console.log('peerConn.oniceconnectionstatechange disconnected')
        }
    }

    // 監聽是否有流傳入，如果有的話就顯示影像
    peerConn.onaddstream = ({ stream }) => {
        // display remote side video
        remoteVideo.srcObject = stream
    }
}

//
// process signal
// @param {Boolean} isOffer 是 offer 還是 answer
//
async function sendSDP(isOffer) {
    try {
        if (!peerConn) {
            initPeerConnection()
        }

        // 創建SDP信令
        const localSDP = await peerConn.createOffer()

        // 設定本地SDP信令
        await peerConn.setLocalDescription(localSDP)

        // 寄出SDP信令
        let e = isOffer ? 'offer' : 'answer'
        socket.emit(e, room, peerConn.localDescription)
    } catch (err) {
        throw err
    }
}

//
// init
//
async function init(elementId, macAddr) {
    setElementId(elementId);
    setRoomByMac(macAddr)
    initPeerConnection()
    connectIO()
    sendSDP(true)
}

//startBtn.onclick = init
//window.onload = init()

