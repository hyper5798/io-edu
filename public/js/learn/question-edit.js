let table;
let answerCheck = {
    "a" : answer.includes('a'),
    "b" : answer.includes('b'),
    "c" : answer.includes('c'),
    "d" : answer.includes('d'),
    "e" : answer.includes('e'),
};


/*let app = new Vue({
    el: '#app',
    data: {
        isNew: false,
        editPoint: -1,
        delPoint: -1,
        check: JSON.parse(JSON.stringify(answerCheck))
    },
    methods: {

        toSubmit: function () {
            $.LoadingOverlay("show");
            document.getElementById('editForm').submit();
        },
    }
});*/

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
