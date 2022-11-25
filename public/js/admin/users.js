let table
let acts = []
let userEdit = document.getElementById('userEdit')

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};
//cps.insert(0,{"id":0, "cp_name": "不選擇"})
//roles.insert(0,{"id":0, "role_name": "不選擇"})
acts.insert(0,{"id":0, "value": "禁用"})
acts.insert(1,{"id":1, "value": "啟用"})

let empty = {
    id: 0,
    name: '',
    email: '',
    cp_id: '',
    role_id: '',
    active: 1,
    password: '12345678',
    updated_at: ''
};

let app = new Vue({
    el: '#app',
    data: {
        userList: users,
        roleList: roles,
        cpList: cps,
        actList: acts,
        user: JSON.parse(JSON.stringify(empty)),
        isNew: false,
        delPoint: -1,
    },
    methods: {
        newCheck: function () {
            this.isNew = true;
            this.user = JSON.parse(JSON.stringify(empty));
        },
        editCheck: function (index) {
            console.log(name)
            this.isNew = true;
            this.user = this.userList[index];
        },
        delCheck: function (index, name) {
            console.log(name);
            this.delPoint = index;
            console.log('this.delPoint :' + this.delPoint);
            this.user = this.userList[index];
            console.log('this.user :' );
            console.log(this.user );
            $('#myModal').modal('show');
        },
        back: function () {
            this.isNew = false;;
        },
        toSubmit: function () {
            $.LoadingOverlay("show");
            document.getElementById('editForm').submit();
        },
        toChoiceCourse(index) {
            this.user = this.userList[index];
            let newUrl = "/admin/userCourses?user_id="+this.user.id;
            document.location.href = newUrl;
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide')
    document.getElementById('delForm').submit()
    $.LoadingOverlay("show");
}


$(document).ready(function() {
    table = $("#table1").dataTable(orderDateOpt)
})
