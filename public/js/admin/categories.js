let table;
let edit = false;

let empty = {
    'id': 0,
    'title': '',
    'tag': 'c'+ (categories.length+1)
};


let app = new Vue({
    el: '#app',
    data: {
        categoryList: categories,
        isNew: edit,
        editPoint: -1,
        delPoint: -1,
        category: JSON.parse(JSON.stringify(empty)),
    },
    methods: {
        newCheck: function () {
            this.isNew = true;
            this.category = JSON.parse(JSON.stringify(empty));
            this.roleIdList
            console.log(this.category)
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.isNew = true;
            this.category = this.categoryList[index];
            //console.log('Select index:' + index)
            //console.log(this.course)
        },
        delCheck: function (index) {
            this.delPoint = index;
            //console.log('this.delPoint :' + this.delPoint);
            this.category = this.categoryList[index];
            $('#myModal').modal('show');
            console.log(this.category);
        },
        back: function () {
            this.isNew = false;
            this.editPoint = -1;
            //this.course = JSON.parse(JSON.stringify(empty));
        },
        toSubmit: function () {
            if(this.category.title.length===0) {
                return alert('尚未輸入分類名稱!');
            }
            /*if(this.category.code.length===0) {
                return alert('尚未輸入分類代號!');
            }*/

            window.setTimeout(function () {
                $.LoadingOverlay("show");
                document.getElementById('editCategory').submit();
            }, 500);

        }
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delCategory').submit();
}

let opt={
    "oLanguage":{"sProcessing":"處理中...",
        "sLengthMenu":"顯示 _MENU_ 項結果",
        "sZeroRecords":"沒有匹配結果",
        "sInfo":"顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
        "sInfoEmpty":"顯示第 0 至 0 項結果，共 0 項",
        "sInfoFiltered":"(從 _MAX_ 項結果過濾)",
        "sSearch":"搜索:",
        "oPaginate":{"sFirst":"首頁",
            "sPrevious":"上頁",
            "sNext":"下頁",
            "sLast":"尾頁"}
    },

};

$(document).ready(function() {
    table = $("#table1").dataTable(opt);
} );
