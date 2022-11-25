let arr = [];

for(let i=0;i<securityNodes.length;i++) {
    arr.push(securityNodes[i]['id']);
}
let arr2 = [];
for(let i=0;i<devices.length;i++) {
    arr2.push(devices[i]['id']);
}
$('.nav-tabs li:eq(2) a').tab('show')

let app = new Vue({
    el: '#app',
    data: {
        tab: 3,//Switch mission table and sequence
        target: data.target,//Switch room, mission and script
        room: room,
        isEditScript: false,
        list1: securityNodes,
        list2: devices,
        security_devices: JSON.stringify(arr),
        available_devices: JSON.stringify(arr2)
    },
    methods: {
        toSubmit: function() {
            let mArr = [];
            for(let i=0;i<this.list1.length;i++) {
                mArr.push(this.list1[i]['id']);
            }
            let mArr2 = [];
            for(let i=0;i<this.list2.length;i++) {
                mArr2.push(this.list2[i]['id']);
            }
            this.security_devices =  JSON.stringify(mArr);
            this.available_devices =  JSON.stringify(mArr2);
            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editSecurity').submit();
            }, 1000);
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delForm').submit();
}



$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu1) {
        let newUrl = "/escape/setMission?target=1&room_id="+room_id;
        //alert(newUrl);
        document.location.href = newUrl;
    } else if(x === menu2) {
        let newUrl = "/escape/setMission?target=2&room_id="+room_id;
        //alert(newUrl);
        document.location.href = newUrl;
    } else if(x === menu3) {
        let newUrl = "/escape/setSecurity?target=3&room_id="+room_id;
        //alert(newUrl);
        document.location.href = newUrl;
    }
});

