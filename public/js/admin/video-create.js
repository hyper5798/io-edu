let table;


let empty = {
    id: 0,
    video_name: '',
    title: '',
    content: '',
    storage_path: '',
    video_url: '',
    category_id: category_id,
    course_id: course_id,
    sort: sort,
    duration: 0
};

let app = new Vue({
    el: '#app',
    data: {
        duration: 0,
        video:empty,
        isNeedUpload:true
    },
    methods: {
        back: function () {
            this.isNew = false;
            this.editPoint = -1;
            this.video = JSON.parse(JSON.stringify(empty));
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
            app.video.duration = Math.round(vid.duration);

            let el = document.getElementById('uploadVideo');
            if(el) {
                //需先等元件產生再初始化
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
