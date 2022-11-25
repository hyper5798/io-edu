let table;

let empty = {
    id: 0,
    title: '',
    content: '',
    dataset: '',
    updated_at: ''
};

let app = new Vue({
    el: '#app',
    data: {
        questionList: questions,
        isNew: false,
        editPoint: -1,
        delPoint: -1,
        question: JSON.parse(JSON.stringify(empty))
    },
    methods: {
        newCheck: function () {
            /*this.isNew = true;
            this.question = JSON.parse(JSON.stringify(empty));
            console.log(this.question)*/
            let newUrl = "/learn/question/create?field_id="+field_id+'&level_id='+level_id;
            document.location.href = newUrl;
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.isNew = true;
            this.question = this.questionList[index];

            console.log('Select index:' + index)
            console.log(this.question)
        },
        delCheck: function (index) {
            this.delPoint = index;
            //console.log('this.delPoint :' + this.delPoint);
            this.question = this.questionList[index];
            $('#myModal').modal('show');
            console.log('this.question :' );
            console.log(this.question);
        },
        back: function () {
            this.isNew = false;
            this.editPoint = -1;
            this.question = JSON.parse(JSON.stringify(empty));
            //console.log(this.userList);
        },
        toSubmit: function () {
            $.LoadingOverlay("show");
            document.getElementById('editForm').submit();
        },
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delForm').submit();
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
