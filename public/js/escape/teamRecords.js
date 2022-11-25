let table;
let now = new Date();
let year = now.getFullYear()+'';
let month = (now.getFullYear() + '-' + (now.getMonth() + 1));
let yearOption = {
    format: "yyyy",
    autoclose: true,
    // startDate: "today",
    clearBtn: true,
    calendarWeeks: true,
    todayHighlight: true,
    language: 'zh-TW',
    viewMode: "years",
    minViewMode: "years"
};
let monthOption = {
    format: "yyyy-mm",
    autoclose: true,
    // startDate: "today",
    clearBtn: true,
    calendarWeeks: true,
    todayHighlight: true,
    language: 'zh-TW',
    startView: "months",
    minViewMode: "months"
};

let app = new Vue({
    el: '#app',
    data: {
        recordList:records,
        page: page,
        search: search,
        options: [
            { text: '年', value: 1 },
            { text: '季', value: 2 },
            { text: '月', value: 3 }
        ],
        options2: [
            { text: '第一季', value: 1 },
            { text: '第二季', value: 2 },
            { text: '第三季', value: 3 },
            { text: '第四季', value: 4 },
        ],
        isActive: 4,
    },

    methods: {
        next: function () {
            let newpage = page+1;
            let newUrl = getUrl(newpage, this.search);
            document.location.href = newUrl;
        },
        previous: function () {
            let newpage = page-1;
            let newUrl = getUrl(newpage, this.search);
            document.location.href = newUrl;
        },
        first: function () {
            let newUrl = getUrl(1,this.search);
            document.location.href = newUrl;
        }
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
     'iDisplayLength': 100,
     'paging':false,//取消分頁
     "info": false,
     dom: 'Bfrtip',
     //dom:'lBrtip',//隱藏搜尋
     buttons: [
         'copyHtml5',
         {
             extend: 'csvHtml5',
             text: 'CSV',
             title: 'Export',//匯出時檔案名稱
             bom : true
         },
         'print'
     ],
};

$(document).ready(function() {

    table = $("#table1").dataTable(opt);
    /*table.$('tr').click(function() {
        let row=table.fnGetData(this);
        //alert(row[0]);
        let index = (row[0] - ((page-1)*limit) -1);
        let record = app.recordList[index];

        console.log(record);
        let newUrl = "/escape/roomRecord?team_record_id="+ record.id+'&from=5&page='+page;
        console.log(newUrl);
        document.location.href = newUrl;
    })*/
} );

function toQuery(){
    let newUrl = getUrl(1, app.search);
    //alert(newUrl);
    document.location.href = newUrl;
}

function getUrl(page, name) {
    let newUrl = 'teamRecords?page='+page +'&search='+ name;
    return newUrl;
}
