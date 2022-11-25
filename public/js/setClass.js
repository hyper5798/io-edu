let table;
console.log(classes);
console.log(classOptions);

let app = new Vue({
    el: '#app',
    data: {
        classList: classes,
        classOptionList: classOptions,
        isNew: false,
        editPoint: -1,
        delPoint: -1,
    },
    methods: {
        editCheck: function (index) {
            this.editPoint = index;
        },
        delCheck: function (index, name) {
            console.log(name);
            this.delPoint = index;
            // $('#myModal').modal('show');
        },
    }
})

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
