@extends('Layout.normal')

@section('content')
    <!-- YESIO develop-->
    <!--<div class="container mt-2 mb-2 homeBlock">
        <div class="row">

            <div class="col-sm-6 col-md-4  col-lg-3 " onclick="toDevelop();">
                <div class="roomBlock text-center ">

                    <img class="item_img" src="{{url('/Images/ESP32.png')}}" alt="ESP32圖標"/>

                    <div class="mt-2">
                        <label class="text-info font-weight-bold">個人開發板</label>
                    </div>

                </div>
            </div>
            <div class="col-sm-6 col-md-8  col-lg-9 justify-content-center">
                <div class="itemBlock">
                    <label class="text-info font-weight-bold">運用範圍</label>
                    <ul>
                        <li>個人開發 : 提供三組應用設定，每組應用上報8種數據。<br>所有上報數據，可通過應用管理觀看圖表。</li>
                        <li>無人船系統 : 提供無線遙控(手機或搖桿)，地圖上報位置及無人船狀態。<br>自動到達指定地點，電子圍籬邊際巡航及定距巡航 。</li>
                        <li>農場機器人(開發中)</li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
    </div>-->
    <!--<div class="container mt-2 mb-2 homeBlock">
        <div class="row">
            <div class="col-sm-6 col-md-4  col-lg-3 " onclick="toModule();">
                <div class="roomBlock text-center ">

                    <img class="item_img" src="{{url('/Images/module_controller.png')}}" alt="模組圖標"/>
                    <div class="mt-2">
                        <label class="text-info font-weight-bold">控制器模組</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-8  col-lg-9 justify-content-center">
                <div class="itemBlock">
                    <label class="text-info font-weight-bold">運用範圍</label>
                        <li>個人開發 : 透過簡易圖控完成輸出及輸入設定即可無線(WIFI)操控，無須撰寫任何程式。</li>
                        <li>農場安控</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>-->
    <div class="container mt-2 mb-2 homeBlock">
        <div class="row">
            <!-- YESIO develop-->
            <div class="col-sm-6 col-md-4  col-lg-3" onclick="toRoom();">
                <div class="roomBlock text-center ">
                    <!-- Product image-->
                    <img class="item_img" src="{{url('/Images/farm_iot.png')}}" alt="智慧機電"/>
                    <div class="mt-2">
                        <label class="text-info font-weight-bold">智慧機電</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-8  col-lg-9 justify-content-center">
                <div class="itemBlock">
                    <label class="text-info font-weight-bold">運用範圍</label>
                    <ul>
                        <li>密室逃脫(東區潔能實踐基地)</li>
                        <!--<li>觀光導覽(宜蘭莎貝莉娜合作中)</li>-->
                        <li>港口無人船除汙(花蓮港務公司)</li>
                        <li>農業機器人(花蓮地方型 SBIR)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- ======= About Section ======= -->
    <section id="about" class="about">
        <div class="container mb-2 homeBlock2" data-aos="fade-up">

            <div class="section-title">
                <div class="alert alert-secondary"><h2>歐利科技有限公司</h2></div>

                <p><label class="font-weight-bold">
                        ioFLEX – 彈性佈建你的AIoT應用
                    </label>
                </p>
                <div>統編：24869613</div>
                <div>花蓮縣吉安鄉北昌村建昌路32巷6號1樓</div>
            </div>

            <div class="row content">
                <div class="col-lg-12">
                    <div>
                        歐利科技為一間智慧物聯網AIoT嵌入式系統研發與生產的公司，本團隊掌握了AIoT產業應用的兩大
                        技術核心，資料控制平台技術與終端可程式嵌入系統技術。近年工程實績上，將前述之AIoT整合技
                        術應用於觀光產業、教育產業所需之互動探索技術(教育部東區潔能實踐基地…)、無人化運動載具產
                        業(智慧港口USV無人工作船…)等具有產業指標性之工程實務經驗。
                        <br>
                        <br>
                        本團隊除了致力於AIoT工程產業應用之技術研發生產之外，同時也將部分AIoT技術轉化為教育學習
                        模組，並進一步地，基於Game As Learning競賽即學習/PBL問題、專題導向等學習理論，提供各
                        式專題式、情境式之線上線下(O2O)學習資源，以協助使用者有效提升跨領域之科技實作力，並實
                        踐個人化STEAM學習歷程，戮力同心為AIoT與工程人材培訓盡一份心力。
                    </div>
                </div>
                <div class="col-lg-6 mt-3">
                    <div class="alert alert-success">2021 里程碑</div>
                    <ul>
                        <li><i class="ri-check-double-line"></i> 無人船清理浮油技術概念驗證及測試，研發計畫啟動。</li>
                        <li><i class="ri-check-double-line"></i> GoSUMO 擂台機器人2021版本，正式上市。</li>
                        <li><i class="ri-check-double-line"></i> ioFLEX – Dynamic (ioFLEX2.0)平台技術研發</li>
                        <li><i class="ri-check-double-line"></i> 110教育部專業群科專任教師赴公民營機構研習計畫，暑假兩梯次審核通過。</li>
                    </ul>
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0 mt-3">
                    <div class="alert alert-info">2020 里程碑</div>

                    <ul>
                        <li><i class="ri-check-double-line"></i> 中央型經濟部SBIR中小企業創新研發計畫，榮獲通過。</li>
                        <li><i class="ri-check-double-line"></i> 教育部東區潔能實踐基地，融入本公司ioFLEX物聯網控制系統。</li>
                        <li><i class="ri-check-double-line"></i> ioFLEX互動探索平台技術，研發啟動。</li>
                        <li><i class="ri-check-double-line"></i> 109教育部專業群科專任教師赴公民營機構研習計畫，寒暑假四梯次審核通過。</li>
                        <li><i class="ri-check-double-line"></i> aquaBot漫游者水面機器人，正式上市。</li>
                    </ul>
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0 mt-3">
                    <div class="alert alert-warning">2019 里程碑</div>
                    <ul>
                        <li><i class="ri-check-double-line"></i> 中央型經濟部SBIR中小企業創新研發計畫新秀海選，榮獲通過。</li>
                        <li><i class="ri-check-double-line"></i> 108教育部專業群科專任教師赴公民營機構研習計畫，寒暑假兩梯次審核通過。</li>
                        <li><i class="ri-check-double-line"></i> microPython Embeded System R&D kick-off</li>
                    </ul>
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0 mt-3">
                    <div class="alert alert-danger">2018 里程碑</div>
                    <ul>
                        <li><i class="ri-check-double-line"></i> 經濟部戰國策全國創新創業競賽數位經濟組，全國第一名。</li>
                        <li><i class="ri-check-double-line"></i> 107教育部專業群科專任教師赴公民營機構研習計畫，寒暑假兩梯次審核通過。</li>
                    </ul>
                </div>
            </div>

        </div>
    </section><!-- End About Section -->

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
        <div class="container mb-2 homeBlock2" data-aos="fade-up">

            <div class="section-title">
                <h2>聯絡客服</h2>
                <p>如果有任何的問題請透過以下的聯絡方式，聯絡我們。</p>
            </div>


            <!--<div>
                <iframe style="border:0; width: 100%; height: 270px;" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d12097.433213460943!2d-74.0062269!3d40.7101282!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb89d1fe6bc499443!2sDowntown+Conference+Center!5e0!3m2!1smk!2sbg!4v1539943755621" frameborder="0" allowfullscreen></iframe>
            </div>-->

            <div class="row mt-5">

                <div class="col-lg-4">
                    <div class="info">
                        <div class="address">
                            <i class="icofont-google-map"></i>
                            <h4>地址:</h4>
                            <p>970 花蓮縣吉安鄉北昌村建昌路32巷6號1樓</p>
                        </div>

                        <div class="email">
                            <i class="icofont-envelope"></i>
                            <h4>Email:</h4>
                            <p>service@yesio.net</p>
                        </div>

                        <div class="phone">
                            <i class="icofont-phone"></i>
                            <h4>電話:</h4>
                            <p>03-8575055</p>
                        </div>

                    </div>

                </div>

                <!--<div class="col-lg-8 mt-5 mt-lg-0">

                    <form action="forms/contact.php" method="post" role="form" class="php-email-form">
                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <input type="text" name="name" class="form-control" id="name" placeholder="你的姓名" data-rule="minlen:4" data-msg="請填上姓或名" />
                                <div class="validate"></div>
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="email" class="form-control" name="email" id="email" placeholder="你的 Email" data-rule="email" data-msg="請填上有效的電子信箱" />
                                <div class="validate"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="subject" id="subject" placeholder="主旨" data-rule="minlen:4" data-msg="請填上主旨" />
                            <div class="validate"></div>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="message" rows="5" data-rule="required" data-msg="請寫一些訊息給我們" placeholder="訊息"></textarea>
                            <div class="validate"></div>
                        </div>

                        <div class="text-center"><button type="submit">傳送訊息</button></div>
                    </form>

                </div>-->

            </div>

        </div>
    </section><!-- End Contact Section -->

@endsection

@section('footerScripts')
    <script>
        let cps = {!! $cps !!};
        let arr = {!! json_encode($arr) !!};
    </script>
    <script src="{{asset('js/index.js')}}"></script>
@endsection


