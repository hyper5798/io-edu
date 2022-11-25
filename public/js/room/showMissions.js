let obj = {};


function toMission(id) {
    let newUrl = '/room/'+room.work+'?mission_id='+id;
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

} );

