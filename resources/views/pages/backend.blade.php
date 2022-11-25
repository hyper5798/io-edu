@extends('Layout.default')
@inject('QuestionPresenter', 'App\Presenters\QuestionPresenter')

@section('content')

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">{{__('layout.index_title') }}</a></li>
    <!--<li class="breadcrumb-item active" aria-current="page">{{__('layout.index_title') }}</li>-->
    </ol>

    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;" onclick="javascript:location.href='/admin/cps'">
                <div class="card-header"><h3>{{__('layout.cps') }}</h3></div>
                <div class="card-body text-right">
                    <h3>{{$data['cp_count']}}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;" onclick="javascript:location.href='/admin/users'">
                <div class="card-header"><h3>{{__('layout.accounts') }}</h3></div>
                <div class="card-body text-right">
                    <h3>{{$data['user_count']}}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;" onclick="javascript:location.href='/admin/roles'">
                <div class="card-header"><h3>{{__('layout.roles') }}</h3></div>
                <div class="card-body text-right">
                    <h3>{{$data['role_count']}}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;" onclick="javascript:location.href='/admin/classes'">
                <div class="card-header"><h3>{{__('layout.classes') }}</h3></div>
                <div class="card-body text-right">
                    <h3>{{$data['class_count']}}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;" onclick="javascript:location.href='/admin/teams'">
                <div class="card-header"><h3>{{__('layout.teams') }}</h3></div>
                <div class="card-body text-right">
                    <h3>{{$data['team_count']}}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;" onclick="javascript:location.href='/node/types'">
                <div class="card-header"><h3>{{__('layout.types') }}</h3></div>
                <div class="card-body text-right">
                    <h3>{{$data['type_count']}}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;" onclick="javascript:location.href='/node/products'">
                <div class="card-header"><h3>{{__('product.products') }}</h3></div>
                <div class="card-body text-right">
                    <h3>{{$data['product_count']}}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;" onclick="javascript:location.href='/node/devices'">
                <div class="card-header"><h3>{{__('layout.devices') }}</h3></div>
                <div class="card-body text-right">
                    <h3>{{$data['device_count']}}</h3>
                </div>
            </div>

        </div>

    </div>
    <div class="card mb-1" onclick="javascript:location.href='/learn/question'">
        <div class="card-header"><i class="fas fa-book mr-1"></i>評量考題</div>
        <div class="card-body">
            <table style="border:3px #cccccc solid; font-size: 20px;" border="2" class="text-center">
                <tr class="bg-dark text-white">
                    <th width="10%">領域</th>
                    <th width="10%">初級</th>
                    <th width="10%">中級</th>
                    <th width="10%">高級</th>
                </tr>
            @foreach($fields as $field)
                    <tr class="bg-secondary text-white">
                        <th class="bg-dark text-white">{{$QuestionPresenter->field($field->id)}} </th>

                        @foreach($field->groups as $group)
                            <th >
                                 {{$group->total}}
                            </th>
                        @endforeach

                    </tr>
            @endforeach
        </div>
        </table>
    </div>
    <!-- /.row -->
    <div class="row mt-2">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-book mr-1"></i>{{__('index.system_info')}}</div>
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">{{__('index.os_label')}}</label>
                            <label class="col-sm-8 col-form-label">{{$data['os_value']}}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">{{__('index.env_label')}}</label>
                            <label class="col-sm-8 col-form-label">{{$data['env_value']}}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">{{__('index.ver_label')}}</label>
                            <label class="col-sm-8 col-form-label">{{$data['ver_value']}}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">{{__('index.upload_limit_label')}}</label>
                            <label class="col-sm-8 col-form-label">{{$data['upload_limit_value']}}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">{{__('index.zone_time_label')}}</label>
                            <label class="col-sm-8 col-form-label">{{$data['zone_time_value']}}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">{{__('index.server_domain_label')}}</label>
                            <label class="col-sm-8 col-form-label">{{$data['server_domain_value']}}</label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script>
        let reports = {!! $reports !!};
        let type = {!! $type !!};
        let app_url = '{{ env('APP_URL') }}';
    </script>
@endsection

@section('footerScripts')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/echarts@4.1.0/dist/echarts.js"></script>-->
    <script src="{{asset('vender/echarts/echarts.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/echarts/theme/roma.js')}}" crossorigin="anonymous"></script>
    <script src="http://127.0.0.1:8000/js/option/chartOption.js"></script>
    <script src="{{asset('js/admin/backend.js')}}"></script>
@endsection
