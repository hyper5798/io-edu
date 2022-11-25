let twLan = {
    "sProcessing":"處理中...",
    "sLengthMenu":"顯示 _MENU_ 項結果",
    "sZeroRecords":"沒有匹配結果",
    "sInfo":"顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
    "sInfoEmpty":"顯示第 0 至 0 項結果，共 0 項",
    "sInfoFiltered":"(從 _MAX_ 項結果過濾)",
    "sSearch":"搜索:",
    "oPaginate":{"sFirst":"首頁 ",
        "sPrevious":"上一頁 ",
        "sNext":" 下一頁",
        "sLast":" 尾頁"}
};

let serachOpt={
    "oLanguage":twLan,
    "iDisplayLength": 10,//定義長度
    "order": [[ 1, 'desc' ]],//設定排序
};

let csvOpt={
    //dom:'lBrtip',//隱藏搜尋
    dom: 'Blrtip',
    buttons: [
        //'copyHtml5',
        //'excelHtml5',
        {
            extend: 'csvHtml5',
            text: 'CSV',
            title: 'Export',
            bom : true
        }
    ],
    "bLengthChange" : true,//隱藏變更長度
    "iDisplayLength": 50,//定義長度
    "order": [[ 1, 'desc' ]],//設定排序
    "oLanguage":twLan,
};

let defaultOpt={
    dom: 'Bfrtip',
    "bLengthChange":false,//隱藏變更長度
    "iDisplayLength": 100,//定義長度
    'paging':true,
    "info": true,
    "oLanguage":twLan,
};



let orderDateOpt={
    dom:'lBrtip',//隱藏搜尋
    "bLengthChange":false,//隱藏變更長度
    "iDisplayLength": 10,//定義長度
    'paging':true,//取消分頁
    "info": true,   //去掉底部文字
    "order": [[ 1, 'desc' ]],//設定排序
    "oLanguage":twLan,
};

let orderItemOpt={
    dom:'lBrtip',//隱藏搜尋
    "bLengthChange":false,//隱藏變更長度
    "iDisplayLength": 10,//定義長度
    'paging':true,//取消分頁
    "info": true,   //去掉底部文字
    "order": [[ 0, 'asc' ]],//設定排序
    "oLanguage":twLan,
};

let onlySearchOpt={
    dom:'Bfrtip',//隱藏搜尋
    "bLengthChange":false,//隱藏變更長度
    "iDisplayLength": 10,//定義長度
    'paging':false,//取消分頁
    "info": false,   //去掉底部文字
    "order": [[ 1, 'desc' ]],//設定排序
    "oLanguage":twLan,
};
