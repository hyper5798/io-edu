let table;

let wb;//讀取完成的資料
let rABS = false; //是否將檔案讀取為二進位制字串

let categoryArr = [
    {"name": "控制型裝置", "value":0},
    {"name": "輸入型裝置", "value":1},
    {"name": "輸出型裝置", "value":2},
    {"name": "輸入及上報型裝置", "value":3},
    {"name": "ALL_IN_ONE模組控制器", "value":4}
];

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
        app.macStr = str.toLowerCase();
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



let empty = {
    id: 0,
    type_id: typeId,
    macAddr: '',
    description: '',
    updated_at: '',

};

let app = new Vue({
    el: '#app',
    data: {
        typeList: types,
        productList: products,
        isNew: isNew,
        editPoint: -1,
        delPoint: -1,
        macStr: '',
        product: JSON.parse(JSON.stringify(empty)),
        categoryList: categoryArr,
        category: category,
        targetProduct: '',
        type_id:typeId
    },
    methods: {
        changeCategory(){
            //alert(this.category);
            let newUrl = "/node/products?category="+this.category+'&isNew='+this.isNew;
            document.location.href = newUrl;
        },
        newCheck: function () {
            this.isNew = 1;
            this.product = JSON.parse(JSON.stringify(empty));
            //console.log(this.cp)
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.isNew = 1;
            this.product= this.productList[index];

            console.log('Select index:' + index)
            console.log(this.product)
        },
        delCheck: function (index) {
            this.delPoint = index;
            //console.log('this.delPoint :' + this.delPoint);
            this.product = this.productList[index];
            //console.log('this.cp :' );
            //console.log(this.cp );
            $('#myModal').modal('show');
        },
        back: function () {
            this.isNew = 0;
            this.editPoint = -1;
            this.product = JSON.parse(JSON.stringify(empty));
            //console.log(this.userList);
        },
        toSubmit: function () {
            if(this.product.macAddr.length<1) {
                if(this.macStr.length < 1 ) {
                    return alert('尚未輸入產品註冊碼!');
                }
            }
            this.product.macAddr = this.product.macAddr.toLowerCase();
            $.LoadingOverlay("show");
            document.getElementById('editForm').submit();
        },
        importCheck: function () {
            this.isNew = 2;
        },
        toImportSubmit: function () {
            if(this.macStr.length < 1 ) {
                return alert('尚未選擇Excel檔案!');
            }
            if(!this.macStr.includes('mac') ) {
                return alert('檔案格式錯誤!');
            }
            $.LoadingOverlay("show");
            document.getElementById('import').submit();
        },
        searchProduct: function () {
            //alert(this.targetProduct);
            let url = api_url+'/api/search-product';

            let data = {mac:this.targetProduct , token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
        },
        changeType() {
            let newUrl = '/node/products?category='+category+'&type_id='+this.type_id +'&isNew='+this.isNew;
            document.location.href = newUrl;
        }
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

function showBarChart(containerId , list) {
    let option = {
        title: {
            text: '產品柱狀圖'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        xAxis: {
            type: 'category',
            data: []
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                data: [],
                type: 'bar'
            }
        ]
    };

    if( list.length > 0) {
        let dom = document.getElementById(containerId);
        if(dom) {
            myChart = echarts.init(dom);
            for(let i=0; i< list.length; i++) {
                let data = list[i];
                option.xAxis.data.push(  getDate(data.created_at));
                option.series[0].data.push( data.total);
            }
            myChart.setOption(option, true);
        }
    }
}

let msg = document.getElementById("message");
$(document).ready(function() {

    table = $("#table1").dataTable(opt);
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
    showBarChart('bar_container' , product_group);
} );

function sendToApi(url,data) {
    app.isSend = true;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        /*beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
        },*/
        success: function (result) {
            //app.message = result;
            console.log(typeof result);

            if(typeof(result) === 'object') {
                let newUrl = '/node/products?type_id='+result.type_id+'&category='+result.category+'&mac='+result.macAddr;
                document.location.href = newUrl;
            } else {
                alert('無法找到產品');
            }


        },
        error:function(err){
            //app.alertMessage = err;
            alert(err.responseText);
        },
    });
    setTimeout(function(){
        app.isSend = false;
    }, 5000);
}



function getDate(dateStr) {
    let newDate = new Date(dateStr);
    return newDate.getFullYear()+'/'+(newDate.getMonth()+1)+'/'+newDate.getDate();
}

