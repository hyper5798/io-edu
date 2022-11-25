let chapter = chapters[data.chapter_index];
let url = chapterVideos[data.chapter_id];
let courseRating = {"url":getUrl(data.avg_scores), "avg": data.avg_scores};
let replyUrl = api_url+'/api/course-comment-reply';
let commentUrl = api_url+'/api/course-comment';
let emptyReplyData =  {"replyComment": false, "replyShow": false, "comment": ""}

function getCommentReplyData(comments) {
    let tmpObj = [];
    for(let i=0;i<comments.length;i++) {
        let comment = comments[i];
        //let obj = {'replyComment': false, 'replyShow': false};
        tmpObj.push(JSON.parse(JSON.stringify(emptyReplyData)));

    }
    return tmpObj;
}

let commentReplyObj = getCommentReplyData(comments);

let emptyScore = {
    "user_id": user_id,
    "course_id" : data.course_id,
    "rating": 3,
    "comment" : ''
};

let emptyComment = {
    "parent_id": null,
    "user_id": user_id,
    "course_id" : data.course_id,
    "specify": null,
    "comment" : '',
    "status": 0,
};


let app = new Vue({
    el: '#app',
    data: {
        tab: 1,
        isScore: data.isScore,
        isComment: data.isComment,
        isSmall:true,
        isShow: false,
        courseCheck: data.courseCheck,
        chapterList: chapters,
        chapterVideoList: chapterVideos,
        chapter: chapter,
        videoUrl: url,
        score: JSON.parse(JSON.stringify(emptyScore)),
        ratingString: '不錯',
        data: data,
        courseRating: courseRating,
        scoreList: getScoresUrl(scores),
        //scores_count: course.scores_count,
        comments_count: course.comments_count,
        data:data,
        comment: '',
        reply: '',
        commentList: comments,
        commentChildren: commentChildren, //children comments
        commentReplyObj: commentReplyObj, //for show/hide reply and children comments
        specify_id: null,
    },
    methods: {
        changeChapter(id,inx) {
            this.chapter = this. chapterList[inx];
            //檢查播放權
            if(!this.chapter.check) return;
            this.isShow = true;
            this.videoUrl = this.chapterVideoList[id];
            //alert(this.chapterVideoList[id]);

            let video = document.getElementById("videoDemo");
            video.src = this.chapterVideoList[id];
            video.play();
        },
        toRating() {

            $(".my-rating-9").starRating({
                initialRating: 3,
                disableAfterRate: true,
                useFullStars: true,
                ratedColors: ['#ffa700', '#ffa700', '#ffa700', '#ffa700', '#ffa700'],
                onHover: function(currentIndex, currentRating, $el){
                    let str = getLiveRating(currentIndex);
                    app.ratingString= str;
                },
                onLeave: function(currentIndex, currentRating, $el){
                    app.ratingString= currentIndex;
                },
                callback: function(currentRating, $el){
                    app.ratingString= currentIndex;
                }
            });
            $('#myModal').modal('show');
        },
        toSendRating() {
            $('#myModal').modal('hide');
            let url = api_url+'/api/course-score';
            let score = JSON.stringify(this.score);
            let data = {attribute:score, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
        },
        toSendComment() {

            let myComment = JSON.parse(JSON.stringify(emptyComment));
            if(this.comment.length==='') return;
            myComment.comment = this.comment;
            let commentStr = JSON.stringify(myComment);
            this.comment = '';
            let data = {attribute:commentStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(commentUrl,data);
        },
        toSendReply(inx, parent_id, comment) {
            this.commentReplyObj[inx]['replyComment'] = false;
            let temp = JSON.parse(JSON.stringify(emptyComment));
            if(comment.length==='') return;
            temp.parent_id = parent_id;
            temp.comment = comment;
            //PS: 父留言留言者ID與回覆留言者相同時，表示再次提問，所以狀態惟須回覆
            if(parent_id !== user_id) {
                temp.status = 1;
            }
            //如果留言者為課程老師，課程老師將留言狀態改為不須回復(已回復)
            if(user_id === course.user_id) {
                temp.status = 1;
                temp.targetId = user_id;
            }
            let commentStr = JSON.stringify(temp);
            let data = {attribute:commentStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(replyUrl,data);
            this.commentReplyObj[inx]['comment'] = '';
        },
        toShowChildReply(comment_id, inx, name) {
            let target = this.commentChildren[comment_id][inx];
            target.reply_show = !target.reply_show
            if(target.reply_show) {
                target.reply = '@'+name+ ' ';
            }
        },
        toSendChildReply(comment_id, inx, reply, user_id) {
            let target = this.commentChildren[comment_id][inx];
            target.reply_show = false;
            let childComment = JSON.parse(JSON.stringify(emptyComment));
            if(reply.length==='') return;
            childComment.parent_id = comment_id;
            childComment.comment = reply;
            childComment.specify = user_id;
            //PS: 子留言一率以不須回覆處理
            //if(comment_id !== user_id) {
                childComment.status = 1;
            //}
            let commentStr = JSON.stringify(childComment);
            let data = {attribute:commentStr, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(replyUrl,data);
        },
        switchParentReply(inx) {//parent reply show or not
            let tmp = this.commentReplyObj[inx];
            tmp.replyComment = !tmp.replyComment;
            if( tmp.replyComment == false) {
                tmp.replyShow= false;
            }
        },
        switchChildReply(inx) {
            let tmp = this.commentReplyObj[inx];
            tmp.replyShow = !tmp.replyShow;
            tmp.replyComment = tmp.replyShow;
        }
    }
});

function getScoresUrl(scoreArr) {
    let newArr = JSON.parse(JSON.stringify(scoreArr));
    for(let i=0;i<newArr.length;i++) {
        let score = newArr[i];
        score.url =  getUrl(score.rating);
    }
    return newArr
}

function getUrl(avg) {
    let url = star1;
    if(avg<=1) {
        url = star1;
    } else if(avg<=2) {
        url = star2;
    } else if(avg<=3) {
       url = star3;
    } else if(avg<=4) {
        url = star4;
    } else if(avg<=5) {
       url = star5;
    }
    return url
}

function sendToApi(url,data) {

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        /*beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
        },*/
        success: function (result) {
            console.log(result);
            if(result.source==='course-score') {
                app.isScore = false;
                result.data.url =  getUrl(result.data.rating);
                app.scoreList.unshift(result.data);
                toChangeCourseRating(app.scoreList);

            } else if(result.source==='course-comment') {
                let data = result.data;
                data.children = [];
                data.children_count = 0;
                app.comments_count++;
                app.commentChildren[data.id] = [];
                app.commentList.unshift(data);
                let obj = JSON.parse(JSON.stringify(emptyReplyData));
                //console.log(data.id);
                //console.log(typeof(obj));
                app.commentReplyObj.unshift(obj);
                console.log(app.commentReplyObj);

            } else if(result.source==='course-comment-reply') {
                let data = result.data;
                data.reply_show = false;
                app.comments_count++;
                if(app.commentChildren.hasOwnProperty(data.parent_id)) {
                    app.commentChildren[data.parent_id].unshift(data);
                }
                console.log( app.commentChildren[data.parent_id]);
            }

        },
        error:function(err){
            console.log(err);
        },
    });
}

function toChangeCourseRating(list) {
    let count = 0;
    for(let i=0;i<list.length;i++) {
        let tmp = list[i];
        count = count + tmp.rating;
    }
    app.courseRating.avg =  count/list.length;
    app.courseRating.url = getUrl(app.courseRating.avg );
}

function getDate(dateStr) {
    let newDate = new Date(dateStr);
    return newDate.getFullYear()+'/'+(newDate.getMonth()+1)+'/'+newDate.getDate();
}

$(document).ready(function() {

    var tabs = $('ul.tabs');

    tabs.each(function(i) {

        //Get all tabs
        var tab = $(this).find('> li > a');
        tab.click(function(e) {

            //Get Location of tab's content
            var contentLocation = $(this).attr('href');
            if(contentLocation == '#content') {
                app.tab = 1;
            } else  if(contentLocation == '#rating') {
                app.tab = 2;
            }  else  if(contentLocation == '#discussion') {
                app.tab = 3;
            }

            //Let go if not a hashed one
            if(contentLocation.charAt(0)=="#") {

                e.preventDefault();

                //Make Tab Active
                tab.removeClass('active');
                $(this).addClass('active');

                //Show Tab Content & add active class
                $(contentLocation).show().addClass('active').siblings().hide().removeClass('active');

            }
        });
    });
});
