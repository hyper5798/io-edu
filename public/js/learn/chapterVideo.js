let chapter = chapters[chapter_index];
let url = chapterVideos[chapter_id];

let app = new Vue({
    el: '#app',
    data: {
        isSmall:true,
        chapterList: chapters,
        chapterVideoList: chapterVideos,
        chapter: chapter,
        videoUrl: url,
    },
    methods: {
        changeChapter(id,inx) {

            this.chapter = this. chapterList[inx];
            //檢查播放權
            if(!this.chapter.check) return;

            let newUrl = '/learn/chapterVideo?course_id='+course_id+'&chapter_id='+id+'&chapter_index='+inx;
            document.location.href=newUrl;
        }
    }
});
