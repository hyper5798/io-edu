let obj = {};

function init() {
    for(let i=0;i<cps.length;i++) {
        let cp = cps[i];
        for(let j=0;j<rooms.length;j++) {
            let room = rooms[j];
            if(obj[cp.id] === undefined) {
                obj[cp.id] = [];
            }
            if(room.cp_id = cp.id) {
                obj[cp.id].splice(obj[cp.id].length, 0, room);
            }
        }
    }
}

function toRoom() {
    let newUrl = "/login?link=room";
    document.location.href = newUrl;
}

function toDevelop() {
    let newUrl = "/login?link=develop";
    document.location.href = newUrl;
}

function toModule() {
    let newUrl = "/login?link=module";
    document.location.href = newUrl;
}


let app = new Vue({
    el: '#app',
    data: {
        cpList:cps,
    },
    methods: {

    }
});


$(document).ready(function() {

} );

