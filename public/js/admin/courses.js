let table;
let edit = false;

let empty = {
    'id': 0,
    'category_id': category_id,
    'title': '',
    'content': '',
    'freeChapterMax': free_chapter,
    'is_show': 1
};


let app = new Vue({
    el: '#app',
    data: {
        courseList: courses,
        isNew: edit,
        editPoint: -1,
        delPoint: -1,
        course: JSON.parse(JSON.stringify(empty)),
        test: '',
        categoryList: categories
    },
    methods: {
        newCheck: function () {
            this.isNew = true;
            this.course = JSON.parse(JSON.stringify(empty));
            console.log(this.course)
        },
        editCheck: function (index) {
            this.editPoint = index;
            this.isNew = true;
            this.course = this.courseList[index];

            console.log('Select index:' + index)
            console.log(this.course)
        },
        delCheck: function (index) {
            this.delPoint = index;
            //console.log('this.delPoint :' + this.delPoint);
            this.course = this.courseList[index];
            $('#myModal').modal('show');
            console.log(this.course );
        },
        back: function () {
            this.isNew = false;
            this.editPoint = -1;
            //this.course = JSON.parse(JSON.stringify(empty));
        },
        toSubmit: function () {
            if(this.course.category_id.length===0) {
                return alert('尚未輸入群組!');
            }
            if(this.course.title===0) {
                return alert('尚未輸入課程名稱!');
            }
            $.LoadingOverlay("show");
            document.getElementById('editCourse').submit();
        },
        toUpload: function (index) {
            $.LoadingOverlay("show");
        },
        toChapter: function (index) {
            this.course = JSON.parse(JSON.stringify(this.courseList[index]));
            let newUrl = "/admin/chapter?course_id="+this.course.id;
            newUrl = newUrl + '&sort='+(index+1);
            newUrl = newUrl + '&category_id='+category_id;
            document.location.href=newUrl;
            //$.LoadingOverlay("show");
        },
        onChange: function (event) {
            //alert(event.target.value);
            let newUrl = "/admin/courses?category_id="+event.target.value;
            document.location.href=newUrl;
            $.LoadingOverlay("show");
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delCourse').submit();
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

});
