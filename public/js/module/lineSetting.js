function switchTab(inx) {
    //alert(inx);
    app.isShow = inx;
    window.scrollTo({ top: 0, behavior: 'smooth' });
    let id = '#myTab a[href="#'+inx+'"]';
    $(id).tab('show')
}

function toAdd() {
    app.error = '';
    //alert(inx);
    if(app.subscript.token.length === 0) {
        alert('尚未輸入權杖');
        return;
    }
    $.LoadingOverlay("show");
    let url = app_url+'/nodes/line_notify?user_id='+user_id;

    $.ajax({
        url: url,
        type: 'POST',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+token);
        },
        data: {"line_token":app.subscript.token},
        success: function (result) {
            console.log(result);

            window.setTimeout(function () {
                if(result.code === 405) {
                    app.error ='傳送失敗: 你的系統憑證無效請重新登入!';
                    $.LoadingOverlay("hide");
                } else if(result.code===401) {
                    app.error ='傳送失敗: 你的Line 權杖無效請重新取得!';
                    $.LoadingOverlay("hide");
                } else {
                    location.reload();
                }
            }, 1000);
        }
    });
}

function toRemove(target) {
    app.error = '';
    $.LoadingOverlay("show");
    let url = app_url+'/nodes/remove_line_notify';

    $.ajax({
        url: url,
        type: 'POST',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+token);
        },
        data: {"id":target.id, "line_token":target.token},
        success: function (result) {
            console.log(result);

            window.setTimeout(function () {
                if(result.code !== 200) {
                    app.information ='傳送失敗:'+result.message;
                    $.LoadingOverlay("hide");
                } else {
                    location.reload();
                }
            }, 1000);

        }
    });
}

function toSend(target, message) {
    app.error = '';
    if(app.message.length === 0) {
        alert('左側訊息輸入欄，尚未輸入訊息!');
        return;
    }
    $.LoadingOverlay("show");
    let url = app_url+'/nodes/send_line_notify';

    $.ajax({
        url: url,
        type: 'POST',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+token);
        },
        data: {"message":message, "line_token":target.token},
        success: function (result) {
            console.log(result);
            $.LoadingOverlay("hide");
            window.setTimeout(function () {
                if(result.code !== 200) {
                    app.error ='傳送失敗:'+result.message;
                    $.LoadingOverlay("hide");
                } else {
                    app.error ='傳送成功';
                }
            }, 1000);
        }
    });
}

let empty = {id:0, token: ''};

let app = new Vue({
    el: '#app',
    data: {
        isShow: 0,
        subscript:JSON.parse(JSON.stringify(empty)),
        message: '',
        error: '',
        list: subscripts
    },
    methods: {
        delLineNotify(index) {
            toRemove(this.list[index])
        },
        sendLineNotify(index) {
            toSend(this.list[index], this.message)
        }
    }
});



let msg = document.getElementById("message");
$(document).ready(function() {
    if(msg!=null) {
        window.setTimeout(( () => msg.remove() ), 8000);
    }
} );


$('.nav-tabs a').on('shown.bs.tab', function(event){
    let x = $(event.target).text();         // active tab
    //let y = $(event.relatedTarget).text();  // previous tab
    if(x === menu0) {
        app.isShow = 0;
    } else if(x === menu1) {
        app.isShow = 1;
    } else if(x === menu2) {
        app.isShow = 2;
    } else if(x === menu3) {
        app.isShow = 3;
    } else if(x === menu4) {
        if(newUrl !== null) {
            document.location.href = newUrl;
        } else {
            history.back();
            document.execCommand('Refresh');
        }

    }
});

