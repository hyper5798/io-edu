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

let empty = {
    cp_name:"",
    end:"",
    id:0,
    name:"",
    reduce:0,
    sequence:0,
    start:"",
    status:0,
    total:0,
}

let app = new Vue({
    el: '#app',
    data: {
        record: empty,
        recordList:records,
        page: page,
        search: search,
        rank_tab: rank_tab,
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
        isActive: 3,
    },

    methods: {
        next: function () {
            let newpage = page+1;
            //let newUrl = "teamRecords?rank_tab="+rank_tab+'&page='+newpage;
            let newUrl = getUrl(rank_tab, newpage, this.search);
            document.location.href = newUrl;
        },
        previous: function () {
            let newpage = page-1;
            //let newUrl = "teamRecords?rank_tab="+rank_tab+'&page='+newpage;
            let newUrl = getUrl(rank_tab, newpage, this.search);
            document.location.href = newUrl;
        },
        first: function () {

            //let newUrl = "teamRecords?rank_tab="+rank_tab+'&page=1';
            let newUrl = getUrl(rank_tab,1,this.search);
            document.location.href = newUrl;
        },
        changeType: function(type) {
            if(type === 1) {
                $('.input-daterange input').each(function() {
                    $(this).datepicker('destroy');
                });
                $('.input-daterange input').each(function() {
                    $(this).datepicker(yearOption);
                });
                document.getElementById("report").value = year;
                app.search.year = year;
            } else if(type === 2) {
                $('.input-daterange input').each(function() {
                    $(this).datepicker('destroy');
                });
                $('.input-daterange input').each(function() {
                    $(this).datepicker(yearOption);
                });
                document.getElementById("report").value = year;
                app.search.year = year;
                app.search.range = 1;
            } else if(type === 3) {
                $('.input-daterange input').each(function() {
                    $(this).datepicker('destroy');
                });
                $('.input-daterange input').each(function() {
                    $(this).datepicker(monthOption);
                });
                document.getElementById("report").value = month;
                app.search.year = month;
            } else {
                document.getElementById("report").value = '';
            }
        },
        changeRange: function(range) {
            this.search.range = range;
        },
        delCheck: function(index) {
            //alert(index);
            this.record = this.recordList[index];
            $('#myModal').modal('show');
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide')
    document.getElementById('delRecord').submit()
    $.LoadingOverlay("show");
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
    table.$('tr').click(function(e) {
        let target = $(e.target);
        if (target.is('i') || target.is('button') || target.is('a') || target.hasClass('select-checkbox')) {
            //alert(target);
            return;
        }


        let row=table.fnGetData(this);
        //alert(row[0]);
        let index = (row[0] - ((page-1)*limit) -1);
        let record = app.recordList[index];


        console.log(record);
        let newUrl = "/escape/roomRecord?team_record_id="+ record.id+'&from='+rank_tab+'&page='+page;
        let report = $('#report').val();
        if(app.search.type === 3) {//for year
            let arr = report.split('-');
            app.search.year = arr[0];
            app.search.range = arr[1];
        } else {
            app.search.year = report;
        }
        newUrl = newUrl + '&type='+ app.search.type;
        newUrl = newUrl + '&year='+ app.search.year;
        newUrl = newUrl + '&range='+ app.search.range;
        console.log(newUrl);
        document.location.href = newUrl;
    })


    if(search.type ===1 || search.type === 2) {
        $('.input-daterange input').each(function() {
            $(this).datepicker(yearOption);
        });
        document.getElementById("report").value = search.year;
    } else {

        $('.input-daterange input').each(function() {
            $(this).datepicker(monthOption);
        });
        document.getElementById("report").value = search.year+'-'+search.range;
    }
} );

$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    let newUrl = null;
    if(x === menu1) {
        newUrl = getUrl(1, 1, app.search);
    } else if(x === menu2) {
        newUrl = getUrl(2, 1, app.search);
    } else if(x === menu3) {
        newUrl = getUrl(3, 1, app.search);
    } else if(x === menu4) {
        newUrl = getUrl(4, 1, app.search);
    } else {
        return;
    }
    document.location.href = newUrl;
});

function toQuery(){
    let report = $('#report').val();
    if(app.search.type === 3) {//for year
        let arr = report.split('-');
        app.search.year = arr[0];
        app.search.range = arr[1];
    } else {
        app.search.year = report;
    }
    let newUrl = getUrl(rank_tab,1,app.search);
    //alert(newUrl);
    document.location.href = newUrl;
}

function getUrl(tab,page,obj) {
    let newUrl = "rank?rank_tab="+tab+'&page='+page;
    newUrl = newUrl + '&type='+ obj.type;
    newUrl = newUrl + '&year='+ obj.year;
    newUrl = newUrl + '&range='+ obj.range;
    newUrl = newUrl + '&room_id='+ room_id;
    return newUrl;
}
