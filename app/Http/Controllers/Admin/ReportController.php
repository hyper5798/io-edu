<?php

namespace App\Http\Controllers\Admin;

use App\Constant\DeviceConstant;
use App\Models\App;
use App\Models\Device;
use App\Models\Report;
use App\Models\Type;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ReportController extends CommonController
{
    private $deviceService;

    public function __construct(
        DeviceService $deviceService
    )
    {
        $this->deviceService = $deviceService;

    }
    /**
     * Display a listing of the resource.
     *
     *  @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $user = session('user');
        //Jason mark
        $types =  Type::where('type_id', DeviceConstant::DEVELOP_TYPE)->get();
        $target = null;
        $reports = [];
        $dataKeys = [];
        $labels = [];
        $devices = [];
        $device = null;
        $type_id = $request['type_id'];
        $device_id = $request['device_id'];
        $findAll = $request['findAll'];
        $isAll = $findAll === 'true' ? true:false;
        if($findAll == null) {
            $findAll = false;
        }
        if($type_id==null && $types->count() > 0) {
            $target = $types->first();
            $type_id =  $target->type_id;
        } else {//if($type_id != null && $types->count() > 0) {
            $target = Type::where('type_id', $type_id) -> first();
        } /*else {//no types
            return redirect('node/types')->withErrors('沒有裝置類型設定上報欄位,因此無法查看上報資料頁');
        }*/
        if($target != null && $target->fields != null) {
            $dataObj = $target['fields'];
            list($dataKeys, $labels) = Arr::divide($dataObj);
        }
        $this->deviceService->getBy('type_id', DeviceConstant::DEVELOP_TYPE);
        /*$devices = Device::where('type_id', DeviceConstant::DEVELOP_TYPE)
            ->get();*/

        if($device_id == null && $devices->count()>0) {
            $device =  $devices->first();
            $device_id = $device -> id;
        } else {
            $device = Device::find($device_id);
        }
        $arr = ['recv','macAddr','key1','key2'];
        $array = Arr::collapse([$arr, $dataKeys]);
        $reports = [];
        if($isAll) {
            $reports = Report::where('type_id', $type_id)
                ->orderBy('recv', 'DESC')
                ->get($array);
        } else {
            $reports = Report::where('type_id', $type_id)
                ->where('macAddr', $device->macAddr)
                ->orderBy('recv', 'DESC')
                ->get($array);
        }

        //Combine array and filter repeat
        //$array = Arr::collapse([[1, 2, 4], [4, 5, 6], [7, 8, 9]]);
        //$array = array_unique($array);
        return view('pages.commonReports', compact(['user','types','type_id','devices','device','device_id','reports', 'dataKeys', 'labels', 'findAll']));
    }

    public function destroy(Request $request)
    {
        $devices = Device::where('type_id', 99)
            ->where('setting_id', 1)->get();
        foreach ($devices as $device) {
            Report::where('macAddr', $device->macAddr)
                ->delete();
        }
        return back();
    }
}
