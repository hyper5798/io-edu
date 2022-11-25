let operateFormat = 0;//0: table row點選後編輯, 1: table row 放編輯刪除按鍵

let editFlag = 0;
let tmpTarget = null;
let notifyMessage = '';


let app = new Vue({
    el: '#app',
    data: {
        operateTag: operateFormat,
        target: null,//Switch room, mission and script
        setList: JSON.parse(JSON.stringify(sets)),
        setting: '',
        selected: 0,
        isEdit: editFlag,
        notifyMessage: notifyMessage,
        setString: '',
        user: user,
        settingIndex: 0,
        notify_max: notify_max
    },
    methods: {
        newSetting: function () {
            this.setting = '';
            this.settingIndex = this.setList.length;
            this.isEdit = 1;
        },
        editSettingByInx: function (index) {
            this.isEdit = 2;
            this.settingIndex = index;
            this.setting = JSON.parse(JSON.stringify(this.setList[index]));
        },
        deleteSettingByInx: function (index) {
            this.setting = JSON.parse(JSON.stringify(this.setList[index]));
            this.settingIndex = index;
            $('#myModal').modal('show');
        },
        back() {
            this.isEdit = 0;
        },
        saveSetting: function () {
            let obj = document.getElementById('email');
            let check = validateEmail(this.setting);
            //return alert(check);

            if(check === null)
            {
                obj.focus();
                return alert("無效的電子信箱!");
            }
            $.LoadingOverlay("show");
            this.setList.splice(this.settingIndex, 1, this.setting);
            if(this.setList.length>0) {
                this.setString = JSON.stringify(this.setList);
            } else {
                this.setString = '[]';
            }
            window.setTimeout(function () {
                document.getElementById('editEmail').submit();
            }, 500);
        },
        deleteSetting: function () {
            $('#myModal').modal('show');
        },
        toDelete: function () {
            if(this.setList.length>0) {
                this.setList.splice(this.settingIndex, 1);
            }
            if(this.setList.length>0) {
                this.setString = JSON.stringify(this.setList);
            } else {
                this.setString = '[]';
            }

            $('#myModal').modal('hide');
            $.LoadingOverlay("show");
            window.setTimeout(function () {
                document.getElementById('editEmail').submit();
            }, 500);

        },

    }

});

function validateEmail(email) {
    return String(email)
        .toLowerCase()
        .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
}

let timeElement = null;
function showMessage(str) {
    app.notifyMessage = str;
    clearTimeout(timeElement);
    timeElement = setTimeout(function () {
        app.notifyMessage = '';
    }, 10000);
}

let opt= orderItemOpt;
opt.oLanguage = twLan;
opt.columnDefs = [
    {
        "targets": [ 1 ],////隱藏 1: index
        "visible": false,
        "searchable": false
    }
];

$(document).ready(function() {
    table = $("#table1").DataTable(opt);
    $('#table1 tbody').on('click', 'tr', function () {
        let data = table.row( this ).data();
        let myIndex = parseInt(data[1]);
        app.settingIndex = myIndex;
        app.setting = JSON.parse(JSON.stringify(app.setList[myIndex]));
        app.isEdit = 2;
    } );

} );

