@extends('Layout.normal')
@section('content')
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center loginBlock">
                    <div class="col-lg-5">
                        <div class="text-center mt-5 mb-3"></div>
                        <div class="card shadow-lg border-0 rounded-lg mt-2">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light">
                                    帳戶註冊通知
                                </h3>
                            </div>
                            <div class="card-body">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger" id="message">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if($verify)
                                <div class="text-center mb-2">
                                    <h3>{{$verify->name}}</h3>
                                </div>
                                <div class="text-center mb-2">

                                    驗證電子信件已送出至{{$verify->email}}
                                </div>

                                <div class="text-center text-info mb-4">
                                    <i class="fa fa-anchor"></i> 按一下電子信件中的安全連結以啟用您的帳號
                                </div>

                                @endif

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script type="text/javascript">
        //Fix facebook return hash
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

    </script>
@endsection
