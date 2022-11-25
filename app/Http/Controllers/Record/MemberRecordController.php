<?php

namespace App\Http\Controllers\Record;

//use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use App\Models\Record;
use Illuminate\View\View;

class MemberRecordController extends Common2Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $data = [
            'title' => '架設中',
            'change_password' => '變更密碼',
            'log' => '日誌',
            'logout' => '登出',

        ];
        $records = Record::orderBy('team_id', 'asc')->get();
        $time = 0;
        $ss = 0;
        $id = 0;
        $test = [];
        $length = count($records);
        foreach ($records as $record) {
            if($id != $record->team_id) {
                if($time != 0) {
                    $test[$id] = ['time'=> $time, 'score' => $ss];
                    $time = 0;
                    $ss = 0;
                }
                $id = $record->team_id;
            }
            $time = $time + $record->time;
            $ss = $ss + $record->score;
            $length = $length -1;
            if($length == 0) {
                $test[$id] = ['time'=> $time, 'score' => $ss];
            }
        }
        dd($test);
        return view('pages.work', $data);
    }


}
