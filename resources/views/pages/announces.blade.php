@extends('Layout.default')
@inject('AnnouncePresenter','App\Presenters\AnnouncePresenter')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>
                <!--<li class="breadcrumb-item">{{__('layout.management') }}</li> -->
                <li class="breadcrumb-item active" aria-current="page">聲明宣告</li>
            </ol>
        </div>
        <div class="col-md-6 text-center">
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-success text-right" onclick="create()">{{__('layout.add')}}</button>
        </div>
    </div>

    <div  class="main-content">

        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th >{{__('layout.item')}}</th>
                <th >標題</th>
                <th >標記</th>
                <th width="30%">{{__('layout.update_at')}}</th>
                <th > </th>
            </tr>

            </thead>

            <tbody>
            @if($announces)
                @foreach ($announces as $announce)
                    <tr>
                        <td width="10%"> {{$loop->index +1}} </td>
                        <td width="35%"> {{$announce->title}} </td>
                        <td width="15%"> {{$AnnouncePresenter->tag($announce->tag)}} </td>
                        <td width="25%"> {{$announce->updated_at}} </td>
                        <td>
                            <a href="{{ route('admin.announce.edit', [$announce->id]) }}" class="btn btn-primary btn-sm">{{__('layout.edit')}}</a>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>


@endsection

@section('footerScripts')
    <script>
        let announces = {!! $announces !!};

        function create() {
            let newUrl = "/admin/announce/create";
            document.location.href = newUrl;
        }
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>

@endsection
