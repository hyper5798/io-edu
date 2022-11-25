console.log(commands)

let opt={
    dom:'lBrtip',//隱藏搜尋
    "bLengthChange":false,//隱藏變更長度
    "iDisplayLength": 80,//定義長度
    'paging':false,//取消分頁
    "info": false,   //去掉底部文字
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

let app = new Vue({
    el: '#app',
    data: {
        isShow: false,
        commandList: JSON.parse(JSON.stringify(commands)),
        prompt: ''
    },
    watch:{
        commandList: function(value) {
            this.commandList= JSON.parse(JSON.stringify(commands));
        }
    },
    methods: {
        copyUrl:function (id) {
            let index = parseInt(id, 10);
            //alert(index);
            this.isShow = true;
            this.prompt = "訊息: 已複製好，可貼上。";
            let Url = document.getElementById(id);
            Url.value = commands[index]['ctlKey'];
            Url.select(); // 選擇物件
            document.execCommand("Copy"); // 執行瀏覽器複製命令
            //alert("已複製好，可貼上。");

            let obj = this;
            setTimeout(function(){
                obj.prompt = '訊息: ';
                //app.isShow = false;
            }, 5000);
        },
        toSendControl:function (id) {
            let index = parseInt(id, 10);
            this.isShow = true;
            let obj = document.getElementById(id);
            obj.value = commands[index]['ctlKey'];
            let appObj = this;
            let value = obj.value;
            //alert("url:"+value);
            $.get(value,
                function(data){
                    //console.log(data);
                    let status = JSON.stringify(data);
                    appObj.prompt = '訊息: '+status;
                });

            setTimeout(function(){
                appObj.prompt = '訊息: ';
                //app.isShow = false;
            }, 5000);
        }
    }
});
