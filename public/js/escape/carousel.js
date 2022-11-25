function changeItem(index) {
    //alert(index);
    let newUrl = "/escape/carousel?app="+apply+'&item='+index;
    //alert(newUrl);
    document.location.href = newUrl;
}

$(document).ready(function() {
    jQuery.fn.carousel.Constructor.TRANSITION_DURATION = 20000  // 2 seconds
});


let app = new Vue({
    el: '#app',
    data: {
        tab:1,
        imageList:images,
        topicList:topics,
        contentList:contents,
        itemList:items,
        myItem:item,
        image:images[0],
        topic: topics[0],
        content: contents[0],
        len:images.length,
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
            this.image = images[this.index];
            this.topic = topics[this.index];
            this.content= contents[this.index];
        },
        next: function () {
            this.index++;
            if(this.index > this.len -1)
                this.index = 0;
            this.image = images[this.index];
            this.topic = topics[this.index];
            this.content= contents[this.index];
        },
        changeItem: function (inx) {
            //alert(inx);
            let newUrl = "carousel?app="+apply+"&item="+inx;
            //alert(newUrl);
            document.location.href = newUrl;
        }
    }
})


$(document).ready(function() {

} );

