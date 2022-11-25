let empty = {
    id: 0,
    user_id: '',
    cellphone: '',
    telephone: '',
    birthday: '',
    address: '',
    image_url: ''
};

if(profile === null) {
    profile = JSON.parse(JSON.stringify(empty));
}
let url = 'https://bootdey.com/img/Content/avatar/avatar7.png';
if(user.image_url) {
    url = user.image_url;
}

let app = new Vue({
    el: '#app',
    data: {
        image_url: url,
        user:user,
        profile: JSON.parse(JSON.stringify(profile)),
        editName:false,
        editCellPhone: false,
        editTelephone:false,
        editBirthday:false,
        editAddress: false
    },
    methods: {
        toChangePass: function () {
            //let newUrl = "{{url('/pass?page=escape')}}";
            //alert(newUrl);
            document.location.href = newUrl;
        },
        toSubmit: function () {
            if(this.user.name.length === 0) {
                alert(name_required)
                return;
            }
            $.LoadingOverlay("show");
            document.getElementById('editProfile').submit();
        }
    }
})


$(document).ready(function() {
    $("#imgInp").change(function(){
        //當檔案改變後，做一些事
        readURL(this);   // this代表<input id="imgInp">
    });
} );

function readURL(input){
    if(input.files && input.files[0]){
        let reader = new FileReader();
        reader.onload = function (e) {
            //$("#preview_progressbarTW_img").attr('src', e.target.result);
            app.image_url = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function custChange(event) {
    // `this` refers to the DOM element
}

