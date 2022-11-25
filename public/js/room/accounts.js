let table,table2;

let wb;//讀取完成的資料
let rABS = false; //是否將檔案讀取為二進位制字串
//let nObj = {id:0, name:'不加入'};
//groups.push(nObj);

let mObj = {id:0, cp_name:'不加入'};
cps.push(mObj);

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
    group_id: group_id,
    role_id: 9,
    active: 1,
    password: '12345678',
    updated_at: '',
    group_name: '',
};

function removeErrMsg() {
    let msg = document.getElementById("message2");
    if(msg!=null) {
        msg.remove();
    }
}

function getGroup() {
    let obj = null;
    for(let i=0;i<groups.length;i++) {
        let item =groups[i];
        if(item.id === group_id) {
            obj =item;
        }
    }
    return obj;
}

function init() {
    for(let i=0;i<adds.length;i++) {
        adds[i]['check'] = false;
    }
    for(let i=0;i<users.length;i++) {
        users[i]['check'] = false;
    }
}

function addAll() {
    for(let i=0;i<app.addList.length;i++) {
        app.addList[i]['check'] = true;
    }
}

function addCancelAll() {
    for(let i=0;i<app.addList.length;i++) {
        app.addList[i]['check'] = false;
    }
}

function userAll() {
    for(let i=0;i<app.userList.length;i++) {
        app.userList[i]['check'] = true;
    }
}

function userCancelAll() {
    for(let i=0;i<app.userList.length;i++) {
        app.userList[i]['check'] = false;
    }
}

function getCheckListStr(uList) {
    let arr = [];
    for(let i=0;i<uList.length;i++) {
        if(uList[i]['check']) {
            arr.splice(arr.length, 0, uList[i]['id']);
        }
    }
    return JSON.stringify(arr);
}

init();

let app = new Vue({
    el: '#app',
    data: {
        addList: adds,
        userList: users,
        groupList: groups,
        roleList: roles,
        userRoleList: user_roles,
        user: JSON.parse(JSON.stringify(empty)),
        isNew: data.edit,
        isError: false,
        messages: [],
        delPoint: -1,
        group: getGroup(),
        accountStr: '',
        isGroup: false,
        member: null,
    },
    methods: {
        onChange: function (event) {
            //alert(event.target.value);
            if(event.target.value === 0) {
                //this.user.class_id = 0;
                //this.user.class_name = '';
            } else {
                //this.user.class_id = this.class_id;
                //this.user.class_name = this.tempName;
            }
        },
        newCheck: function () {
            this.isNew = 1;
            this.user = JSON.parse(JSON.stringify(empty));
            //this.user.class_name = this.tempName;
        },
        editUserCheck: function (index) {
            console.log(index);
            this.isNew = 1;
            this.user = this.userList[index];
            console.log(this.user);
            //this.user.class_name = this.tempName;
        },
        editAddCheck: function (index) {
            console.log(index);
            this.isNew = 1;
            this.user = this.addList[index];
            console.log(this.user);
            //this.user.class_name = this.tempName;
        },
        delMemberCheck: function (index, name) {
            console.log(name);
            this.delPoint = index;
            console.log('this.delPoint :' + this.delPoint);
            this.user = this.addList[index];
            $('#myModal').modal('show');
        },
        delUserCheck: function (index, name) {
            console.log(name);
            this.delPoint = index;
            console.log('this.delPoint :' + this.delPoint);
            this.user = this.userList[index];
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
            if(this.user.email === '' && this.user.phone === '' ) {
                this.messages.push('電子信箱跟電話不能同時為空!');
            } else if (this.user.email !== '' && emailCheck (this.user.email) === false) {
                this.messages.push(data.email_format_required);
            }

            if(this.messages.length>0) {
                this.isError = true;
                return false;
            } else {
                return true;
            }
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
        },
        changeAdd(event) {
            if(event.currentTarget.checked) {
                addAll();
            } else {
                addCancelAll();
            }
        },
        changeUser(event) {
            if(event.currentTarget.checked) {
                userAll();
            } else {
                userCancelAll();
            }
        },
        addToGroup() {
            this.member = getCheckListStr(this.userList);
            //alert( this.member);
            $.LoadingOverlay("show");
            window.setTimeout(function () {
                document.getElementById('editGroupUser').submit();
            }, 100);
        },
        checkGroup() {
            $('#myModal2').modal('show');
        },
        removeFromGroup() {
            this.member = getCheckListStr(this.addList);
            //alert( this.member);
            $.LoadingOverlay("show");
            window.setTimeout(function () {
                document.getElementById('delGroupUser').submit();
            }, 100);
        }
    }
});

function emailCheck (emailStr) {
    let regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!regex.test(emailStr)) {
        return false;
    }else{
        return true;
    }
}

function toDelete() {
    $('#myModal').modal('hide')
    document.getElementById('delAccount').submit()
    $.LoadingOverlay("show");
}


$(document).ready(function() {
    table = $("#table1").dataTable(onlySearchOpt);
    table2 = $("#table12").dataTable(onlySearchOpt);
});

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu1) {
        let newUrl = "/room/setCp?cp_id="+cp_id;
        document.location.href = newUrl;
    } else if(x === menu2) {
        let newUrl = "/room/setGroup?cp_id="+cp_id;
        document.location.href = newUrl;
    }
});
