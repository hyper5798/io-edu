let obj = {};

function toDevelop() {
    let newUrl = "/login?link=develop";
    document.location.href = newUrl;
}

function toModule() {
    let newUrl = "/login?link=module";
    document.location.href = newUrl;
}

function toRoom(id, len) {

    if(len === 0) {
        return alert('尚未設定任務!')
    }
    $.LoadingOverlay("show");
    window.setTimeout(function () {
        $.LoadingOverlay("hide");
    }, 1000);
    let type = null;
    for(let i=0;i<rooms.length;i++) {
        if(rooms[i]['id'] === id) {
            let room = rooms[i];
            if(room.work == 'demo') {
                let newUrl = '/escape/personal';
                document.location.href = newUrl;
                return;
            }
            if(room.type === 'develop') {
                type = 'develop';
            } else {
                type = 'module';
            }
        }
    }

    let newUrl = '/room/'+type+'/'+id;
    document.location.href = newUrl;
}


let app = new Vue({
    el: '#app',
    data: {
        roomList:rooms,
    },
    methods: {

    }
});


$(document).ready(function() {
    $.LoadingOverlay("hide");
} );

