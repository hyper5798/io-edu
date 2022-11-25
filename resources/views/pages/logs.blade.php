@extends('Layout.default')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">{{__('layout.index_title') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">工程日誌</li>
            </ol>
        </div>
        <div class="col-md-3  text-left">

        </div>

        <div class="col-md-3  text-left">

        </div>
        <div class="col-md-3  text-left">
            <form method="post" action="deleteLogs"  id="deleteLogs">
                <input type="hidden" name="_method" value="delete" />
                <input type="hidden" name="id" v-model="appObj.id" />
                {{csrf_field()}}
                <button type="button" onClick="toDeleteLogs()" class="btn btn-danger" >
                    刪除所有日誌
                </button>
            </form>
        </div>

    </div>
    <div class="row main-content2">
        <div class="col-md-12 mb-2 mt-3 text-left">

            <div >
                <table id ="table1"  class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th width="10%">{{__('layout.item') }}</th>
                        <th width="20%">時間</th>
                        <th width="20%">裝置</th>
                        <th width="50%">紀錄</th>
                    </tr>

                    </thead>

                    <tbody >
                    @foreach($logs as $log)
                        <tr>

                            <td>{{$loop->index+1}}</td>
                            <td>{{$log->recv}}</td>
                            <td>{{$log->client}}</td>
                            <td >{{ $log->log}}</td>


                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
@endsection

@section('footerScripts')
    <script>
        function toDeleteLogs() {
            let yes = confirm('你確定刪除所有日誌嗎？');

            if (yes) {
                document.getElementById('deleteLogs').submit();
            } else {
                alert('你按了取消按鈕');
            }

        }
    </script>



@endsection



