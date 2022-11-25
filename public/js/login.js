if (window.location.hash && window.location.hash == '#_=_') {
    if (window.history && history.pushState) {
        window.history.pushState("", document.title, window.location.pathname);
    } else {
        // Prevent scrolling by storing the page's current scroll offset
        let scroll = {
            top: document.body.scrollTop,
            left: document.body.scrollLeft
        };
        window.location.hash = '';
        // Restore the scroll offset, should be flicker free
        document.body.scrollTop = scroll.top;
        document.body.scrollLeft = scroll.left;
    }
}

function disableMsg() {
    let msg = document.getElementById("message");
    //console.log(msg)
    if(msg !== null)
        document.getElementById("message").remove();
}

function showQRCode() {
    $('#myModal').modal('show');
}

function toCheckMail($email) {
    let url = api_url+'/api/check-email';
    let data = {email:$email , XDEBUG_SESSION_START:'PHPSTORM'};
    sendToApi(url,data);
}

function toResendMail() {
    let email = document.getElementById('email').value;
    let newUrl = "/resend-mail?email="+email;
    document.location.href = newUrl;
}

let show = false;
if(errors.length>0) {
    let message = errors[0];
    while(typeof(message) === 'object') {
        message = message[0];
    }
    //alert(message);
    if(message.indexOf('重送認證信')>0) {
        show = true;
    }
}

let app = new Vue({
    el: '#app',
    data: {
        isLogin: false,
        isShowResend: show,
        message: ''
    },
    methods: {
        checkEmail() {
            let email = document.getElementById('email').value;

        },
        cancelResendMail() {
            this.isShowResend=false;
            this.message = '';
        }
    }
});

function checkMail() {
    setTimeout(function () {
        if(app.isShowResend === false && app.isLogin === false) {
            let email = document.getElementById('email').value;
            if(email.length>0 && email.indexOf('@')>0) {
                toCheckMail(email);
            }
        }
    }, 500);

}

function sendToApi(url,data) {
    app.isSend = true;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        /*beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
        },*/
        success: function (result) {
            if(result) {
                result = parseInt(result);
                if(result === 0) {
                    app.isShowResend = true;
                    app.message = '電子信箱尚未驗證，請重送認證信或到你註冊信箱啟用帳號。';
                } else {
                    app.isShowResend = false;
                }
            }
        },
        error:function(err){
            //app.alertMessage = err;
            alert(err.responseText);
        },
    });

}
