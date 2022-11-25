@extends('Layout.diy')

@section('content')
    <div class="breadcrumb mt-1">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                @if($url != null)
                    <a href="{{$url}}">返回</a>
                @else
                    <a href="javascript:history.back()" onclick="self.location=document.referrer;">返回</a>
                @endif

            </li>
            <li class="breadcrumb-item active" aria-current="page">Line Notify設定 (觸發通知將通知列表中群組)</li>
        </ol>
    </div>
    <!-- Tab -->
    <div class="row mt-2">
        <div class="col-11">
            <ul class="nav nav-tabs" id="myTab">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#0">設定流程</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#1">[教學] 建立群組</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#2">[教學] 取得權杖</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#3">[教學] 群組加入Line Notify</a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#4">返回</a>
                </li>-->
            </ul>

        </div>
        <div class="col-1">
            <!--<button title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=4&item=1") }}'"><i class="fas fa-question"></i></button>-->
        </div>

    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger mt-2" id="message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div v-cloak class="row justify-content-center main-content">
        <div v-show="isShow==0" class="col-md-12">
            <div class="card shadow-lg rounded-lg mt-3">
                <div  class="card-header">
                    <span class="text-info statusText">設定流程</span>
                </div>
                <div class="card-body">

                    <p>1.
                        <button type="button" class="btn btn-outline-secondary" onclick="switchTab(1)">
                            [教學] 建立群組
                        </button>
                        - 加入群組的成員可以接收通知，已建立群組可略過此流程。
                    </p>
                    <p>2.
                        <button type="button" class="btn btn-outline-secondary" onclick="switchTab(2)">
                            [教學] 取得權杖
                        </button>
                        - 建立發送Line Notify的權杖，將取得的權杖貼到權杖輸入欄，按下儲存。
                    </p>
                    <p>3.
                        <button type="button" class="btn btn-outline-secondary" onclick="switchTab(3)">
                            [教學] 群組加入Line Notify
                        </button>
                        - 將Line Notify加入建立的群組中，群組成員才會收到通知。
                    </p>
                    <div v-cloak v-if="error.length>0" class="col-12 alert alert-info" role="alert">
                        @{{ error }}
                        <button type="button" class="close" @click="error=''">
                            <span >&times;</span>
                        </button>
                    </div>
                    <div class = "row">
                        <div class="emptyBlock col-6">
                            <label class="sr-only" for="inlineFormInputGroup">權杖</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">權杖</div>
                                </div>
                                <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="權杖輸入欄" v-model="subscript.token">
                            </div>
                            <div class="float-right">
                                <button type="button" class="btn btn-primary mb-2" onClick="toAdd()">儲存</button>
                            </div>

                            <div v-if="list.length>0" class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">訊息</div>
                                </div>
                                <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="請輸入訊息" v-model="message">
                            </div>


                        </div>
                        <div class="emptyBlock col-6">
                            <h4>已建立Line Notify的群組列表</h4>
                            <table v-if="list.length>0" id ="table1"  class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th >{{__('layout.item')}}</th>
                                    <th >通知群組</th>
                                    <!--<th width="30%">權杖</th>-->
                                    <th ></th>
                                </tr>

                                </thead>

                                <tbody >

                                    <tr v-for="(item, index) in list">
                                        <td> @{{index +1}} </td>
                                        <td> @{{item.line_group}} </td>
                                        <!--<td width="30%"> @{{item.token}} </td>-->

                                        <td>
                                            <button type="button" name="del" class="btn btn-danger btn-sm" @click="delLineNotify(index)">
                                                {{__('layout.delete')}}
                                            </button>
                                            <button type="button" name="del" class="btn btn-success btn-sm" @click="sendLineNotify(index)">
                                                傳送測試
                                            </button>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--建立群組 -->
        <div v-show="isShow==1" class="col-md-12">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <span class="text-info statusText">1.建立群組</span>
                </div>
                <div class="card-body">
                    <div class = "row">
                        <div class="col-sm-12 col-md-6">
                            <p>1. 打開 LINE 的主頁 ，點選右上角「加入好友」。</p>
                            <img src="{{url('/Images/line/11.png')}}" width="40%">
                            <p class="mt-3">3. 選擇好友加入群組，然後按「下一步」。</p>
                            <img src="{{url('/Images/line/13.png')}}" width="40%">
                            <p class="mt-3">5. 點選「建立」。</p>
                            <img src="{{url('/Images/line/15.png')}}" width="40%">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p>2. 選擇「建立群組」。</p>
                            <img src="{{url('/Images/line/12.png')}}" width="40%">
                            <p class="mt-3">4. 輸入自訂「群組名稱」。</p>
                            <img src="{{url('/Images/line/14.png')}}" width="40%">，
                            <p class="mt-3">6. 群組已經建立完成，顯示群組。</p>
                            <img src="{{url('/Images/line/16.png')}}" width="40%">
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--申請 LINE Notify 權杖 -->
        <div v-show="isShow==2" class="col-md-12">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <span class="text-info statusText">2.申請 LINE Notify 權杖</span>
                </div>
                <div class="card-body">
                    <p>1. 打開 LINE Notify 的網站 ( <a href="https://notify-bot.line.me/zh_TW/" rel="noreferrer noopener" target="_blank">https://notify-bot.line.me/zh_TW/</a> )，點選右上角登入。</p>
                    <img src="{{url('/Images/line/21.png')}}" width="80%">
                    <p class="mt-2">2. 輸入自己的LINE帳號跟密碼。</p>
                    <img src="{{url('/Images/line/22.png')}}" width="50%">
                    <p class="mt-2">3. 登入後滑鼠移至上方個人帳號，選擇「個人頁面」。</p>
                    <img src="{{url('/Images/line/23.png')}}" width="50%">
                    <p class="mt-2">4. 在個人頁面按下「發行權杖」。</p>
                    <img src="{{url('/Images/line/24.png')}}" width="70%">
                    <p class="mt-2">5. 點選「發行權杖」，指定權杖名稱 ( 傳送通知訊息時所顯示的名稱 )，以及選擇是要一對一接收，或是讓群組也可以接收通知。</p>
                    <img src="{{url('/Images/line/25.png')}}" >
                    <img src="{{url('/Images/line/25-1.png')}}" >
                    <p class="mt-2">6 點選「發行」，會出現一段權杖代碼，這段代碼「
                        <strong>只會出現一次</strong>」，複製這段代碼，將其貼上<p class="text-primary">Line Notify設定的權杖輸入欄中</p>，按下「儲存」，系統就可以讓你接收通知。
                    </p>
                    <img src="{{url('/Images/line/26.png')}}" >
                    <p class="mt-2">7. 顯示連動服務「從ioFlex通知傳送至iot 監聽」。</p>
                    <img src="{{url('/Images/line/27.png')}}" width="70%">
                </div>
            </div>

        </div>
        <!--群組加入Line Notify-->
        <div v-show="isShow==3" class="col-md-12">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <span class="text-info statusText">3. 群組加入Line Notify</span>
                </div>
                <div class="card-body">
                    <div class = "row">
                        <div class="col-sm-12 col-md-6">
                            <p>1. 打開 LINE 的主頁 ，點選群組。</p>
                            <img src="{{url('/Images/line/31.png')}}" width="40%">
                            <p class="mt-3">3. 選擇的群組按「成員名單」。</p>
                            <img src="{{url('/Images/line/33.png')}}" width="40%">
                            <p class="mt-3">5. 在「利用名稱搜尋欄」輸入line。</p>
                            <img src="{{url('/Images/line/35.png')}}" width="40%">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p>2. 選擇要加入Line Notify 的「群組」。</p>
                            <img src="{{url('/Images/line/32.png')}}" width="40%">
                            <p class="mt-3">4. 按下邀請「好友」</p>
                            <img src="{{url('/Images/line/34.png')}}" width="40%">，
                            <p class="mt-3">6. 勾選「Line Notify」，然後按下「邀請」。</p>
                            <img src="{{url('/Images/line/36.png')}}" width="40%">
                            <img src="{{url('/Images/line/37.png')}}" width="40%">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection

@section('footerScripts')
    <script>
        let menu0 = "設定流程";
        let menu1 = "[教學] 建立群組";
        let menu2 = "[教學] 取得權杖";
        let menu3 = "[教學] 群組加入Line Notify";
        let menu4 = "返回";
        let app_url = '{{ env('APP_URL') }}';
        let token = "{!! $token !!}";
        let subscripts = {!! $subscripts !!};
        let user_id = {!! $user->id !!};
        @if($url == null)
        let newUrl = null;
        @else
        let newUrl = '{!! $url !!}';
        @endif
    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/module/lineSetting.js')}}"></script>

@endsection
