let empty = {
    id: 0,
    name: '',
};
let team = null;
for(let i=0;i<teams.length;i++) {
    let tmp = teams[i];
    if(team_id === tmp.id) {
        team = JSON.parse(JSON.stringify(tmp));
    }
}

if(team === null) {
    team = JSON.parse(JSON.stringify(empty));
}
let arr = [];
for(let i=0;i<members.length;i++) {
    arr.push(members[i]['id']);
}
let arr2 = [];
for(let i=0;i<users.length;i++) {
    arr2.push(users[i]['id']);
}

let macList = [];
let app = new Vue({
    el: '#app',
    data: {
        isEdit: false,
        teamList: JSON.parse(JSON.stringify(teams)),
        team: team,
        teamTab: 1,
        list1: JSON.parse(JSON.stringify(members)),
        list2: JSON.parse(JSON.stringify(users)),
        backup1: JSON.parse(JSON.stringify(members)),
        backup2: JSON.parse(JSON.stringify(users)),
        add_members: null,
        remove_members: null
        //userList: JSON.parse(JSON.stringify(teamUsers)),
    },
    watch:{
        list1: function(value) {

            if(value.length===5) {//Keep data
                this.backup1 = JSON.parse(JSON.stringify(this.list1));
                this.backup2 = JSON.parse(JSON.stringify(this.list2));
            }
            if(value.length>5) {
                /*let test = arr_diff(this.li  st1, this.backup);
                console.log(test);*/
                this.list1 = JSON.parse(JSON.stringify(this.backup1));
                this.list2 = JSON.parse(JSON.stringify(this.backup2));
                alert(members_limit);
            }
        }
    },
    methods: {
        changeTeam: function (index) {
            alert(this.team.id);
            /*let newUrl = "/node/admin?device_id="+myId+'&myIntro='+inx;
            //alert(newUrl);
            document.location.href = newUrl;*/
        },
        newTeam: function () {
            this.isEdit = true;
            this.team = JSON.parse(JSON.stringify(empty));
        },
        setTeam: function () {
            this.isEdit = true;
            if(this.team.name.length === 0) {
                alert(name_required);
                return;
            }
            $.LoadingOverlay("show");
            document.getElementById('editTeam').submit();
        },
        deleteTeam: function () {
            $('#myModal').modal('show');
        },
        back: function() {
            this.isEdit = false;
        },
        toSubmit: function() {
            if(this.team.id === 0) {
                alert(no_team_selected);
                return;
            }
            this.add_members = compare(this.list2, users);
            this.remove_members = compare(this.list1,members);
            this.add_members = JSON.stringify(this.add_members);
            this.remove_members = JSON.stringify(this.remove_members);

            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editTeamUsers').submit();
            }, 500);
        },
        toDelete: function() {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");

            document.getElementById('delTeam').submit();

        },
        log: function(evt) {
            window.console.log(evt);
        },
        selectedClass: function(value) {
            alert(value);
        },
    }
});

function arr_diff (a1, a2) {

    let a = [], diff = [];

    for (let i = 0; i < a1.length; i++) {
        a[a1[i].id] = a1[i];
    }

    for (let i = 0; i < a2.length; i++) {
        if (a[a2[i].id]) {
            //console.log('no change : '+a2[i].id);
            delete a[a2[i].id];
        } else {
            //console.log('remove : '+a2[i].id);
            a[a2[i].id] = a2[i];
        }
    }

    for (let k in a) {
        diff.push(a[k]);
    }
    // console.log('increased : ');
    // console.log(diff);
    return diff;
}

function compare (a1, a2) {

    let a = [], diff = [];

    for (let i = 0; i < a1.length; i++) {
        a[a1[i].id] = a1[i];
    }

    for (let i = 0; i < a2.length; i++) {
        if (a[a2[i].id]) {
            //console.log('不變的 : '+a2[i].id);
            //delete a[a2[i].id];
        } else {
            diff.push(a2[i].id);
        }
    }

    // console.log('變更的 : ');
    // console.log(diff);
    return diff;
}

let msg = document.getElementById("message");
$(document).ready(function() {

    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
} );

