let app = new Vue({
    el: '#app',
    data: {
        tab:1,
        itemList:items,
        sort:sort,
        video:chapterList[0].video_url,
        title: chapterList[0].title,
        content: chapterList[0].content,
        len:chapterList.length,
        index:0
    },
    methods: {
        test: function () {
            if(this.tab == 1) {
                this.tab = 2;
            } else {
                this.tab = 1;
            }
        },
        previous: function () {
            this.index--;
            if(this.index < 0)
                this.index = this.len -1;
            this.video = chapterList[this.index]['video_url'];
            document.getElementById("movie").src = chapterList[this.index]['video_url'];
            this.title = chapterList[this.index]['title'];
            this.content= contents[this.index]['content'];
        },
        next: function () {
            this.index++;
            if(this.index > this.len -1)
                this.index = 0;
            this.video = chapterList[this.index]['video_url'];
            document.getElementById("movie").src = chapterList[this.index]['video_url'];
            this.title = chapterList[this.index]['title'];
            this.content= contents[this.index]['content'];
        },
        changeItem: function (inx) {
            //alert(inx);
            let newUrl = "tutorial?category_id="+category_id+"&sort="+(inx+1);
            newUrl = newUrl + '&link='+link;
            //alert(newUrl);
            document.location.href = newUrl;
        }
    }
})


$(document).ready(function() {

} );

