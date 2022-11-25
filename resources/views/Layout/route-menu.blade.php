<div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                <!-- <div class="sb-sidenav-menu-heading">Core</div> -->
                <a class="nav-link" href="{{url('/backend')}}"
                ><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    儀表板
                </a>

                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                        {{__('layout.management')}}
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{url('/admin/users')}}">{{__('layout.accounts')}}</a>
                            <a class="nav-link" href="{{url('/admin/roles')}}">{{__('layout.roles')}}</a>
                        <!-- <a class="nav-link" href="{{url('/admin/cps')}}">{{__('layout.cps')}}</a> -->
                            <a class="nav-link" href="{{url('/admin/classes')}}">{{__('layout.classes')}}</a>
                            <a class="nav-link" href="{{url('/admin/teams')}}">{{__('layout.teams')}}</a>

                        </nav>
                    </div>
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                        <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                        {{__('layout.devices')}}
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{url('/node/types')}}">{{__('layout.types')}}</a>
                            <a class="nav-link" href="{{url('/node/products')}}">{{__('product.products_manager')}}</a>
                            <a class="nav-link" href="{{url('/node/devices')}}">{{__('layout.devices_manager')}}</a>
                        </nav>
                    </div>


                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#courses" aria-expanded="false" aria-controls="collapsePages">
                        <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                        教學與評量
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="courses" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{url('/admin/categories')}}">教學分類</a>
                            <!--<a class="nav-link" href="{{url('/admin/courses')}}">編輯課程</a>
                            <a class="nav-link" href="{{url('/admin/videos')}}">上傳影片</a>-->
                            <a class="nav-link" href="{{url('/learn/question')}}">評量考題</a>
                        </nav>
                    </div>

                <a class="nav-link" href="{{url('/admin/announce')}}"
                ><div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
                    聲明宣告
                </a>
                <a class="nav-link" href="{{url('/reports')}}"
                    ><div class="sb-nav-link-icon"><i class="fas fa-upload"></i></div>
                        {{__('layout.reports')}}
                    </a>
                <a class="nav-link" href="{{url('/logs')}}"
                ><div class="sb-nav-link-icon"><i class="fas fa-edit"></i></div>
                    工程日誌
                </a>

                <a class="nav-link" href="{{url('/node/myDevices?link=develop')}}"
                ><div class="sb-nav-link-icon"><i class="fas fa-podcast"></i></div>
                    {{__('device.develop_title')}}
                </a>
                <!--<a class="nav-link" href="{{url('/escape/admin')}}"
                ><div class="sb-nav-link-icon"><i class="fas fa-running"></i></div>
                    {{__('escape.escape_room')}}
                </a>-->
                <!--<a class="nav-link" href="{{url('/node/myDevices?link=module')}}"
                ><div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                    {{__('device.module_title')}}
                </a>-->

                <!--<a class="nav-link" href="{{url('/learn/question')}}"
                ><div class="sb-nav-link-icon"><i class="fas fa-edit"></i></div>
                    評量考題
                </a>-->
                <!-- <div class="sb-sidenav-menu-heading">Interface</div> -->



                    <!--
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGates" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    {{__('layout.records')}}
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseGates" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{url('/record/teams')}}">{{__('layout.team_records')}}</a>
                        <a class="nav-link" href="{{url('/record/members')}}">{{__('layout.person_records')}}</a>
                    </nav>
                </div>
                <a class="nav-link" href="{{url('/learn/news')}}"
                ><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    {{__('layout.learning_maps')}}
                </a>-->
                <!--<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseNews" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    {{__('layout.person_records')}}
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseNews" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{url('/learn/news')}}">新知管理</a>
                    </nav>
                </div> -->
                <!--<div class="sb-sidenav-menu-heading">Addons</div>
                <a class="nav-link" href="charts.html"
                ><div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                    Charts</a
                ><a class="nav-link" href="tables.html"
                ><div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                    Tables</a
                > -->
            </div>
            </div>
        </nav>
</div>
