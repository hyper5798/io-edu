let replyUrl = api_url+'/api/course-comment-reply';
let removeAllUrl = api_url+'/api/remove-all-comment';
let disableCommentUrl = api_url+'/api/disable-comment';
let table = null;

let emptyComment = {
    "parent_id": null,
    "user_id": user_id,
    "course_id" : 0,
    "specify": null,
    "comment" : '',
    "status": 0,
};

let selectComment = null;
if(comments.length>0) {
    selectComment = comments[0];
}


let app = new Vue({
    el: '#app',
    data: {
        commentList: comments,
        selectIndex: 0,
        selectComment:JSON.parse(JSON.stringify(selectComment)),
        commentUserName: '',
        reply: JSON.parse(JSON.stringify(emptyComment)),
    },
    methods: {
        toShowReply(inx, name) {
            this.selectIndex = inx;
            this.selectComment = JSON.parse(JSON.stringify(this.commentList[inx])) ;
            this.commentUserName =  name;
            this.reply = JSON.parse(JSON.stringify(emptyComment));
            this.reply.comment = '@' + name + ' ';
            this.reply.specify = this.selectComment.user_id;
            this.reply.course_id = this.selectComment.course_id;
            this.reply.status = 1;
            if(this.selectComment.parent_id !== null)
                this.reply.parent_id = this.selectComment.parent_id;
            else
                this.reply.parent_id = this.selectComment.id;
            $('#myModal').modal('show');
        },
        toSendReply() {
            let commentStr = JSON.stringify(this.reply);
            let data = {attribute:commentStr, targetId:this.selectComment.id, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(replyUrl,data);
            $('#myModal').modal('hide');
        },
        toShowOther(inx, name) {
            this.commentUserName =  name;
            this.selectIndex = inx;
            this.selectComment = JSON.parse(JSON.stringify(this.commentList[inx])) ;
            $('#myModal2').modal('show');
        },
        removeAll() {

            let tempList = JSON.parse(JSON.stringify(this.commentList));
            let arr = [];
            for(let i=tempList.length-1; i>=0 ;i--) {
                let temp = tempList[i];
                if(temp.user_id === this.selectComment.user_id) {
                    arr.push(temp.id);
                    this.commentList.splice(i,1);
                }
            }
            if(this.commentList.length===0) {
                this.selectComment = JSON.parse(JSON.stringify(emptyComment));
            }
            let arrStr = JSON.stringify(arr);
            let data = {arrStr:arrStr,  token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(removeAllUrl,data);
            $('#myModal2').modal('hide');

        },
        disableComment() {
            let data = { userId:this.selectComment.user_id, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(disableCommentUrl,data);
            $('#myModal2').modal('hide');

        },
        stopReply() {
            this.commentList.splice(app.selectIndex, 1);
            let data = {attribute:null, targetId:this.selectComment.id, token:token, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(replyUrl,data);
            $('#myModal2').modal('hide');
        }
    }
});


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
            if( result && result.source === 'course-comment-reply') {
                let data = result.data;
                app.commentList.splice(app.selectIndex, 1);
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

    table = $("#table1").dataTable(defaultOpt);

});
