let table;

let empty = {
    id: 0,
    category_id: category_id,
    title: '',
    content: '',
    sort: '',
    video_id: '',
};


let app = new Vue({
    el: '#app',
    data: {
        chapterList:chapters,
        chapter: JSON.parse(JSON.stringify(empty))
    },
    methods: {
        newCheck: function () {
            this.isNeedUpload = true;
            this.isNew = true;
            this.video = JSON.parse(JSON.stringify(empty));
            console.log(this.video)
        },
        editCheck: function (index) {
            this.isNeedUpload = false;
            this.editPoint = index;
            this.isNew = true;
            this.video = this.videoList[index];
            document.getElementById("movie").src = this.videoList[index]['video_url'];

            console.log('Select index:' + index)
            console.log(this.video)
        },
        delCheck: function (index) {
            this.chapter = this.chapterList[index];
            $('#myModal').modal('show');
            console.log('this.video :' );
            console.log(this.video );
        },
    }
});

function create() {
    let newUrl = "/admin/chapter/create?category_id="+category_id+"&course_id="+course_id;
    document.location.href = newUrl;
}


function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delChapter').submit();
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

function create() {
    let newUrl = "/admin/chapter/create?category_id="+category_id+"&course_id="+course_id;
    document.location.href = newUrl;
}
