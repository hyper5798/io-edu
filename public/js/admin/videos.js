let table;
console.log(videos);
let edit = false;

let empty = {
    id: 0,
    video_name: '',
    title: '',
    content: '',
    storage_path: '',
    video_url: '',
    category_id: category_id,
    course_id: course_id,
    sort: 1,
    duration: 0
};

let target = null;

if(video_id) {
    if(videos && videos.length>0) {
        for(let i=0;i<videos.length; i++) {
            if(video_id == videos[i]['id']) {
                target = videos[i];
            }
        }
    }
} else {
    target = JSON.parse(JSON.stringify(empty));
}


let needUpload = false;
if(video_id === 0) {
    edit = false;
} else {
    edit = true;
}

let app = new Vue({
    el: '#app',
    data: {
        videoList: videos,
        isNew: edit,
        isNeedUpload: false,
        editPoint: -1,
        delPoint: -1,
        video: target,
        cId: category_id,
        test: '',
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
            this.delPoint = index;
            //console.log('this.delPoint :' + this.delPoint);
            this.video = this.videoList[index];
            $('#myModal').modal('show');
            console.log('this.video :' );
            console.log(this.video );
        },
        back: function () {
            this.isNew = false;
            this.editPoint = -1;
            this.video = JSON.parse(JSON.stringify(empty));
        },
        updateVideo() {
            this.isNeedUpload = true;
            this.video.video_url = '';
        },

        toSubmit: function () {
            $.LoadingOverlay("show");
            document.getElementById('editVideo').submit();
        },
        toUpload: function () {
            if(this.video.video_name === '') {
                return alert('尚未選擇檔案');
            }
            var vid = document.getElementById("movie");
            if(vid===null)
                vid = document.getElementById("upload");
            //alert(vid.duration);
            this.video.duration = Math.round(vid.duration);

            let el = document.getElementById('uploadVideo');
            if(el) {
                app.$nextTick(() => {
                    el.submit();
                    $.LoadingOverlay("show");
                });
            }
        },
        onChange: function(event) {
            //alert(event.target.value);
            let newUrl = "/admin/videos?category_id="+event.target.value;
            document.location.href = newUrl;
        }
    }
});

function toDelete() {
    $('#myModal').modal('hide');
    $.LoadingOverlay("show");
    document.getElementById('delVideo').submit();
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

function fileChange() {
    let fileInput = document.getElementById("uploadVideoFile");
    console.log('Trying to upload the video file: %O', fileInput);

    if ('files' in fileInput) {
        if (fileInput.files.length === 0) {
            alert("Select a file to upload");
        } else {
            let name = fileInput.files[0].name;
            //app.video.video_name = name.replace('.mp4', '');
            let arr = name.split('.');
            app.video.title = arr[0];
            app.video.video_name = name;
            let $source = $('#videoSource');
            $source[0].src = URL.createObjectURL(fileInput.files[0]);
            $source.parent()[0].load();
            $("#videoSourceWrapper").show();
        }
    } else {
        console.log('No found "files" property');
    }
}

function UploadVideo(file) {
    let loaded = 0;
    let chunkSize = 500000;
    let total = file.size;
    let reader = new FileReader();
    let slice = file.slice(0, chunkSize);

    // Reading a chunk to invoke the 'onload' event
    reader.readAsBinaryString(slice);
    console.log('Started uploading file "' + file.name + '"');

    reader.onload = function (e) {
        //Send the sliced chunk to the REST API
        $.ajax({
            url: "http://api/url/etc",
            type: "POST",
            data: slice,
            processData: false,
            contentType: false,
            error: (function (errorData) {
                console.log(errorData);
                alert("Video Upload Failed");
            })
        }).done(function (e){
            loaded += chunkSize;
            let percentLoaded = Math.min((loaded / total) * 100, 100);
            console.log('Uploaded ' + Math.floor(percentLoaded) + '% of file "' + file.name + '"');

            //Read the next chunk and call 'onload' event again
            if (loaded <= total) {
                slice = file.slice(loaded, loaded + chunkSize);
                reader.readAsBinaryString(slice);
            } else {
                loaded = total;
                console.log('File "' + file.name + '" uploaded successfully!');
            }
        })
    }
}

function readURL(input){
    if(input.files && input.files[0]){
        let reader = new FileReader();
        reader.onload = function (e) {
            //$("#preview_progressbarTW_img").attr('src', e.target.result);
            app.video.video_url = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
