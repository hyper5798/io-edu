let table;
console.log(cps);
let opt= csvOpt;
//opt.oLanguage = twLan;

let empty = {
    id: 0,
    role_id: 10,
    cp_name: '',
    cp_id: cp_id,
    phone: '',
    address: '',
    updated_at: ''
};

let empty2 = {
    id: 0,
    cp_id: cp_id,
    name: '',
    class_option: 1,
    updated_at: ''
};

let app = new Vue({
    el: '#app',
    data: {
        cpList: cps,
        classList: classes,
        isEdit: false,
        editPoint: -1,
        delPoint: -1,
        cp: JSON.parse(JSON.stringify(empty)),
        selected: '',
        myClass: JSON.parse(JSON.stringify(empty2)),
    },
    mounted() {

        if(cp_id==1) {
            this.cp = JSON.parse(JSON.stringify(empty));
        } else {
            this.cpList.forEach(function(item){
                if(item.id == cp_id) {
                    this.cp = item;
                }
            });
        }
    },
    methods: {
        onChange: function (event) {
            this.cp = this.cpList[event.target.value];
        },
        setSchool: function () {
            $.LoadingOverlay("show");
            document.getElementById('editCp').submit();
        },
        newCheck: function () {
            this.isEdit = true;
            this.cp = JSON.parse(JSON.stringify(empty2));
            //console.log(this.cp)
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.isEdit = true;
            this.cp = this.classList[index];

            console.log('Select index:' + index)
            console.log(this.cp)
        },
        back: function () {
            this.isEdit = false;
        },
        toSubmit: function () {
            $.LoadingOverlay("show");
            document.getElementById('editClass').submit();
        },
        editClass: function (index) {
            //alert(index);
            this.editPoint = index;
            this.isEdit = true;
            this.myClass = this.classList[index];
        },
        delClass: function (index) {
            //alert(index);
            this.delPoint = index;
            this.myClass = this.classList[index];
            $('#myModal').modal('show');
        },
        toDelete: function () {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");
            document.getElementById('delClass').submit();
        }
    }
});

$(document).ready(function() {
    table = $("#table1").dataTable(opt);
} );

