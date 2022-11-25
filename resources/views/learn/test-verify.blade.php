@extends('Layout.diy')
@inject('QuestionPresenter', 'App\Presenters\QuestionPresenter')

@section('css')

    <style>
        #test { height: 500px; width: 100%; }
    </style>
@endsection

@section('content')
    @php
        $user = session('user');
    @endphp
    <div class="row breadcrumb">
        <div class="col-md-9">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">自我評量結果</li>
            </ol>
        </div>

        <div class="col-md-3 text-right">

        </div>
    </div>

    <!-- Create question-->
    <div class="row justify-content-center main-content">
        <div class="col-lg-12 mt-2">
            <div class="card shadow-lg border-0 rounded-lg">

                <div class="card-body">
                    @foreach ($errors->all() as $error)
                        @if($error)
                            <h1>恭喜</h1>
                        @else
                            <h1>下次加油</h1>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>

    </div>

@endsection

@section('footerScripts')

    <script>
        $(document).ready(function() {
            window.setTimeout(function () {
                var newUrl = '{!! url('/learn/self-test') !!}';
                //alert(newUrl);
                document.location.href = newUrl;
            }, 3000);
        } );

    </script>

@endsection
