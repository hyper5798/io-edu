
//取得全部領域總和
let max = getAllFieldCountByLevel(level_id);

let app = new Vue({
    el: '#app',
    data: {
        fieldId: field_id,
        levelId: level_id,
        total:  max,
        fieldList: JSON.parse(JSON.stringify(fields)),
        field: JSON.parse(JSON.stringify(fields[0])),
        group: {level_id: 1, total:0},
        dropList: getDropList(max),
        number:10
    },
    methods: {
        toSubmit() {
            //alert('toSubmit');
            if(this.total === 0) {
                return alert('您的選擇沒有任何考題，請更改選擇。');
            }
            if(this.dropList.length === 0) {
                this.number = this.total;
            }
            setTimeout(function () {
                document.getElementById('testRecord').submit();
            }, 500);
        }


    }
});

function changeField(inx) {
    //alert(inx);
    app.field = app.fieldList[inx];
    app.fieldId = app.field.id;
    if(app.fieldId === 1) {
        app.total = getAllFieldCountByLevel(app.levelId);
    } else {
        app.total = getTotal(app.field.groups, app.levelId);
    }

    app.dropList = getDropList(app.total);
}

function  changeLevel(id) {
    app.levelId = parseInt(id);
    if(app.fieldId === 1) {
        app.total = getAllFieldCountByLevel(app.levelId);
    } else {
        app.total = getTotal(app.field.groups, app.levelId);
    }
    app.dropList = getDropList(app.total);
}

function getAllFieldCountByLevel(levelId) {
    var total = 0
    for(let i=1; i<fields.length;i++) {
        let groups = fields[i].groups;
        total = total + getTotal(groups, levelId);
    }
    return total;
}

function  getTotal(groupList, levelId) {
    var total = 0;
    for(let i=0; i<groupList.length;i++) {
        let group = groupList[i];
        if(group.level_id === levelId) {
            total = group.total;
        }
    }
    return total;
}

function getDropList(number) {
    var len = number/10;
    let arr = [];
    for(i=1; i<=len; i++) {
        arr.push(i*10);
    }
    return arr;
}
