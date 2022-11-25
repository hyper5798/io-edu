let emptyObj = {
    "a": '',
    'b': '',
    'c': '',
    'd': '',
    'e': ''
}

let fieldObj = {};
let levelObj = {};
fields.forEach(function(field){
    fieldObj[field.id] = field.title;
});
levels.forEach(function(level){
    levelObj[level.id] = level.title;
});


function getRandomInt(max) {
    return Math.floor(Math.random() * max);
}



/* app : Vue DOM
* data isFinish  : 是否完成測試
* data isNext  : 是否顯示正確與否圖片&標準答案&測按下一題按鍵
* data isRight  : 答案正確與否
* data isSend  : 是否傳送API
* data count   : 題數
* data question: 考題物件
* data sortList: 經過 random 排序的 a~e 列表
* data options : 考題選擇JSON (key : a~e, value: 考題選項內容) 搭配 sortList做考題選項顯示
* data testObj : 答案選擇的物件(a~e)
* */
let app = new Vue({
    el: '#app',
    data: {
        isFinish: false,
        isNext: false,
        isRight: false,
        isSend  : false,
        count   : question_data.count,
        question: question_data.question,
        sortList: JSON.parse(JSON.stringify(question_data.sorts)),
        options : JSON.parse(JSON.stringify(question_data.options)),
        testObj : JSON.parse(JSON.stringify(emptyObj)),
        field_title: fieldObj[question_data['field_id']],
        level_title: levelObj[question_data['level_id']],
        number: question_data.number,
        questionData: question_data,
        startAt: getFormatDateString(question_data.start_at,2),
        score: question_data.score,
        answerString: ''
    },
    methods: {
        toSubmit() {
            //alert('toSubmit');

            setTimeout(function () {
                document.getElementById('testVerify').submit();
            }, 500);
        },
        toVerify() {
            let url = api_url+'/api/test-verify';
            let optionList = getOptionList(this.testObj);
            if(optionList.length===0) return alert('尚未選擇答案!');
            let msg= {
                user_id    : user_id ,
                token      : token,
                record_id  : question_data.record_id,
                question_id: this.question.id,
                field_id   : this.question.field_id,
                level_id   : this.question.level_id,
                start_at   : this.questionData.start_at,
                answer     : JSON.stringify(optionList),
                XDEBUG_SESSION_START:'PHPSTORM'
            };
            sendToApi(url,msg);
        },
        showNext() {
            app.isNext = false;
            if(question_data.question != null) {
                resetQuestion(question_data);
            } else {
                app.isFinish = true;
                window.setTimeout(function () {
                    let newUrl = "/learn/test-analyze";
                    document.location.href = newUrl;
                }, 5000);
            }
        },
        stopTest() {

        }
    }
});

function getAnswerString(answerList) {
    var str = '';

    for(let i=0;i<app.sortList.length;i++) {
        let key = app.sortList[i];
        if(answerList.indexOf(key) !== -1) {
            if(str === '') {
                str = str + (i+1);
            } else {
                str = str + ', ' +(i+1);
            }
        }
    }
    return str;
}

function getOptionList(tmpObj) {
    var tmp = [];
    for(let i=0;i<app.sortList.length;i++) {
        let key = app.sortList[i];
        if(tmpObj[key]) {
            tmp.push(key);
        }
    }
    return tmp;
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
            //app.message = result;
            //alert(JSON.stringify(result));
            if(result.hasOwnProperty('items')) {
                question_data = result['items'];
                app.answerString = getAnswerString(question_data.cache_answer);
                app.score = question_data.score;
                app.isNext = true;
                app.isRight = result['items']['check'];
            }
            //showMessage(app,result);
            app.isSend = false;
        },
        error:function(err){
            //app.alertMessage = err;
            alert(err.responseText);
            app.isSend = false;
        },
    });
    setTimeout(function(){
        app.isSend = false;
    }, 5000);
}

function resetQuestion(items) {
    app.questionData = JSON.parse(JSON.stringify(items));
    app.questionData.start_at = new Date().toISOString();
    app.count = items.count;
    app.question = items.question;
    app.sortList = JSON.parse(JSON.stringify(items.sorts));
    app.options = JSON.parse(JSON.stringify(items.options));
    app.testObj = JSON.parse(JSON.stringify(emptyObj));
    app.field_title = fieldObj[items.question['field_id']];
    app.level_title = levelObj[items.question['level_id']];
    app.startAt= getFormatDateString(new Date().toISOString(),2)
}
