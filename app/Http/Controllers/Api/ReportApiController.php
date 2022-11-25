<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportApiController extends Controller
{
    public function updateReport(Request $request)
    {
        $input = $request->all();
        if(array_key_exists('token', $input )) {
            $token = $input['token'];
            $user = User::where('remember_token', $token )->get();
            if(count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $id = (int)$input['id'];
        $report = Report::find($id);
        if(array_key_exists('data', $input)) {
            $report->data = json_decode($input['data']);
        }
        $report->timestamps = false;
        $report->save();

        return response('更新成功!' , 200);
    }
}
