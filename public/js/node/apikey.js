let empty = [];
let myVar = null;
let empty1 = [{'key': 'result', 'name': '數量', 'value': 2}];
let empty2 = [
    {'key': 'result', 'name': '筆量', 'value': 2},
    {'key': 'page', 'name': '頁數', 'value': 1},
];
let mkeys = Object.keys(labels);
let findKeys = ['limit', 'offset'];
console.log('labels.length :' + labels.length);
console.log(labels);
console.log('setting length :' + mkeys.length);
console.log(mkeys);
for(let i=0;i<mkeys.length;i++) {
    let item = {};
    let key = mkeys[i];
    item.key = key;
    item.name = labels[key];
    item.value = 0;
    empty.splice(i, 0, item);
}

console.log( empty);

let app = new Vue({
    el: '#app',
    data: {
        isNew: false,
        isShow: false,
        isIntro: false,
        editPoint: -1,
        delPoint: -1,
        labelList: empty,
        findList: empty2,
        prompt: '訊息:',
        tab: tab,
        myIntro: user.myIntro,
        mykey: api_key
    },
    computed: {
        // 计算属性的 getter
        write_url: function () {
            return this.getUrl();
        },
        read_url: function () {
            return this.getUrl2();
        },
    },
    mounted() {
        //alert(this.deviceList.length);
        if(this.myIntro === 4) {
            window.setTimeout(( () =>  startIntro() ), 100);
        }
        $('#timeselector input').on("change", function() {
            app.changeTab(parseInt(this.id));
        });
    },
    methods: {
        changeTab(value) {
            this.tab = value;
        },
        back: function () {
            this.isNew = false;
            this.editPoint = -1;
        },
        toSubmit: function () {
            $.LoadingOverlay("show");
            let obj = document.getElementById('editForm');
            //alert(obj.action);
            //alert(obj.method);
            obj.submit();
        },
        copyUrl:function (id) {
            let url = this.getUrl();
            let obj = document.getElementById("write_url");
            obj.disabled=false;
            obj.value = url;
            obj.select(); // 選擇物件
            document.execCommand("Copy"); // 執行瀏覽器複製命令
            this.isShow = true;
            obj.disabled=true;
            this.prompt = "訊息: 已複製好，可貼上。";
        },
        copyKey:function (id) {
            this.prompt = '';
            let url = this.mykey;
            let obj = document.getElementById("api_key");
            obj.disabled=false;
            obj.value = url;
            obj.select(); // 選擇物件
            document.execCommand("Copy"); // 執行瀏覽器複製命令
            this.isShow = true;
            obj.disabled=true;
            this.prompt = "訊息: 已複製好，可貼上。";
        },
        toSendControl:function (id) {
            let url = this.getUrl();
            let appObj = this;
            toAjax(appObj, url);
        },
        copyUrl2:function (id) {
            this.prompt = '';
            let url = this.getUrl2();
            let obj = document.getElementById("read_url");
            obj.disabled=false;
            obj.value = url;
            obj.select(); // 選擇物件
            document.execCommand("Copy"); // 執行瀏覽器複製命令
            this.isShow = true;
            obj.disabled=true;
            this.prompt = "訊息: 已複製好，可貼上。";
        },
        toSendControl2:function (id) {
            let url = this.getUrl2();
            let appObj =this;
            //alert(url);
            toAjax(appObj, url);
        },
        getUrl:function (id) {
            let url = app_url + '/reports/write?api_key=' + api_key;
            for(let i = 0; i<this.labelList.length; i++) {
                let item = this.labelList[i];
                url = url + '&' + item.key + '=' + item.value;
            }
            return url;
        },
        getUrl2:function () {
            let url = app_url + '/reports/read?api_key=' + api_key;
            for(let i = 0; i<this.findList.length; i++) {

                let item = this.findList[i];
                if(i==0 && item.value > 500) {
                    item.value = 500;
                    alert('筆量最大設定500筆');
                }
                if(i==1 && item.value > 5) {
                    item.value = 5;
                    alert('頁數最大設定5頁');
                }
                url = url + '&' + item.key + '=' + item.value;
            }
            return url;
        },
        clean: function() {
            this.isShow = false;
            this.prompt= '訊息:';
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delForm').submit();
}

function toAjax(obj, url) {
    $.get( url,
        function(data){
            let status = JSON.stringify(data);
            obj.isShow = true;
            obj.prompt = '訊息: '+status;

            setTimeout(function(){
                if(preStep === 3) {
                    intro.nextStep();
                }
            }, 100);

        });
}
