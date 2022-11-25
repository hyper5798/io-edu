let table;

let wb;//讀取完成的資料
let rABS = false; //是否將檔案讀取為二進位制字串

function importf(obj) {//匯入

    if(!obj.files) {
        return;
    }
    let f = obj.files[0];
    let reader = new FileReader();
    reader.onload = function(e) {
        let data = e.target.result;
        if(rABS) {
            wb = XLSX.read(btoa(fixdata(data)), {//手動轉化
                type: 'base64'
            });
        } else {
            wb = XLSX.read(data, {
                type: 'binary'
            });
        }
        //wb.SheetNames[0]是獲取Sheets中第一個Sheet的名字
        //wb.Sheets[Sheet名]獲取第一個Sheet的資料
        let str = JSON.stringify( XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]) );
        //document.getElementById("demo").innerHTML= str;
        app.accountStr = str;
    };
    if(rABS) {
        reader.readAsArrayBuffer(f);
    } else {
        reader.readAsBinaryString(f);
    }
}

function fixdata(data) { //檔案流轉BinaryString
    let o = "",
        l = 0,
        w = 10240;
    for(; l < data.byteLength / w; ++l) o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w, l * w + w)));
    o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w)));
    return o;
}

Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};

let empty = {
    id: 0,
    name: '',
    email: '',
    cp_id: cp_id,
    class_id: class_id,
    role_id: (role_id+1),
    active: 1,
    password: '12345678',
    updated_at: '',
    class_name: '',
};

function removeErrMsg() {
    let msg = document.getElementById("message2");
    if(msg!=null) {
        msg.remove();
    }
}

function getClassName() {
    let newname = '';
    for(let i=0;i<classes.length;i++) {
        let item = classes[i];
        if(item.id === class_id) {
            newname = item.class_name;
        }
    }
    return newname;
}

let app = new Vue({
    el: '#app',
    data: {
        userList: users,
        classList: classes,
        user: JSON.parse(JSON.stringify(empty)),
        isNew: data.edit,
        isError: false,
        messages: [],
        delPoint: -1,
        tempName: getClassName(),
        accountStr: ''
    },
    methods: {
        newCheck: function () {
            this.isNew = 1;
            this.user = JSON.parse(JSON.stringify(empty));
            this.user.class_name = this.tempName;
        },
        editCheck: function (index) {
            console.log(name);
            this.isNew = 1;
            this.user = this.userList[index];
            this.user.class_name = this.tempName;
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
            this.isNew = 0;
            this.isError= false;
            removeErrMsg();
        },
        toSubmit: function () {
            if(this.checkValue()) {
                $.LoadingOverlay("show");
                document.getElementById('editAccount').submit();
            }
        },
        checkValue: function () {
            this.messages = [];
            if(this.user.name === '') {
                this.messages.push(data.name_required);
            }
            if(this.user.email === '') {
                this.messages.push(data.email_required);
            }
            if(this.messages.length>0) {
                this.isError = true;
                return false;
            } else {
                return true;
            }
        },
        toAdmin: function() {
            this.user.role_id = role_id;
            this.user.class_id = 0;
            this.user.class_name = '';
        },
        toUser: function() {
            this.user.role_id = role_id+1;
            this.user.class_id = class_id;
            this.user.class_name = this.tempName;
        },
        onFocusEvent: function () {
            this.isError = false;
            removeErrMsg();
        },
        importCheck: function () {
            this.isNew = 2;
            this.user = JSON.parse(JSON.stringify(empty));
        },
        toImportSubmit: function () {
            $.LoadingOverlay("show");
            document.getElementById('editBatchAccount').submit();
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide')
    document.getElementById('delAccount').submit()
    $.LoadingOverlay("show");
}


$(document).ready(function() {
    table = $("#table1").dataTable(serachOpt);

})
