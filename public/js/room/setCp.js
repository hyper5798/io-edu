let table;
console.log(cps);
let opt= defaultOpt;
//opt.oLanguage = twLan;


let empty = {
    id: 0,
    parent_id: null,
    role_id: 9,
    cp_name: '',
    cp_id: cp_id,
    phone: '',
    address: '',
    updated_at: ''
};

let toEdit = false;
let mycp = JSON.parse(JSON.stringify(empty));
/*if(role_id === 8 && cps.length >0) {
    toEdit = true;
    mycp = cps[0];
} else if(role_id === 8 && cps.length === 0){
    toEdit = true;
}*/

let app = new Vue({
    el: '#app',
    data: {
        cpList: cps,
        isEdit: toEdit,
        editPoint: -1,
        delPoint: -1,
        cp: mycp,
        selected: 0,
        isParent: false,
        parentList: JSON.parse(JSON.stringify(parent_cps)),
        parent_id: parent_id
    },
    watch:{
        isParent: function(value) {
            alert(value);
        }
    },
    methods: {

        toSubmit: function () {
            if(this.cp.cp_name.length === 0) {
                alert('名稱不能為空')
                return;
            }
            $.LoadingOverlay("show");
            document.getElementById('editCp').submit();
        },
        newCheck: function () {
            this.isEdit = true;
            this.isParent = false;
            this.cp = JSON.parse(JSON.stringify(empty));
            //console.log(this.cp)
        },
        back: function () {
            this.isEdit = false;
        },
        editCp: function (index) {
            //alert(index);
            this.editPoint = index;
            this.isEdit = true;
            this.cp = this.cpList[index];
            if(this.cp.parent_id != null) {
                this.isParent = true;
            } else {
                this.isParent = false;
            }
        },
        delCp: function (index) {
            //alert(index);
            this.delPoint = index;
            this.cp = this.cpList[index];
            $('#myModal').modal('show');
        },
        toDelete: function () {
            $('#myModal').modal('hide');
            $.LoadingOverlay("show");
            document.getElementById('delCp').submit();
        }
    }
});

let msg = document.getElementById("message");
$(document).ready(function() {
    table = $("#table1").dataTable(opt);
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
} );

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu2) {
        let newUrl = "/room/setGroup?cp_id="+cp_id;
        document.location.href = newUrl;
    } else if(x === menu3) {
        let newUrl = "/room/accounts?cp_id="+cp_id;
        document.location.href = newUrl;
    }
});

