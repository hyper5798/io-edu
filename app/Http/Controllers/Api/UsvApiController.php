<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsvApiController extends Controller
{
    private $userService;

    public function __construct(
       UserService $userService

    )
    {
        $this->userService = $userService;
    }
    public function saveLocation(Request $request)
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
        $location = new Location;
        $location->macAddr = $input['macAddr'];
        $location->lat = $input['lat'];
        $location->lng = $input['lng'];
        if(array_key_exists('fileName', $input)) {
            $location->image_url = 'public/UAV/'.$input['fileName'];
        }
        $location->save();
        $locations = Location::where('macAddr', $input['macAddr'])
            ->orderBy('recv', 'asc')
            ->get();
        foreach ($locations as $location) {
            $location->image_url = Storage::url($location->image_url);
        }
        $result = array('api'=>'saveLocation', 'locations'=> $locations);
        return response($result , 200);
    }

    public function removeLocation(Request $request)
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
        $loc = Location::find($input['id']);
        if($loc->image_url) {
            //$file = 'CE8iBq2YfWLSsk4hV8teR3EZR9RqI8CyhHMPcA8R.jpeg';
            //$storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            $myPath = $loc->image_url;
            $exists = Storage::disk('local')->exists($myPath);
            if($exists) {
                Storage::disk('local')->delete($myPath);
            }
        }
        Location::where('id', $input['id'])->delete();
        $locations = Location::where('macAddr', $input['macAddr'])
            ->orderBy('recv', 'asc')
            ->get();
        foreach ($locations as $location) {
            $location->image_url = Storage::url($location->image_url);
        }
        $result = array('api'=>'removeLocation', 'locations'=> $locations);
        return response($result , 200);
    }

    public function saveReportSetting(Request $request)
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

        $app_id = $app_id = (int) $input['app_id'];
        $setting = null;

        if (array_key_exists('cp_id',$input ) ){
            $cp_id = (int)$input['cp_id'];
            $setting = Setting::where('app_id', $app_id)
                ->where('cp_id', $cp_id)
                ->where('field', 'report')
                ->first();
            if($setting == null) {
                $setting = new Setting;
                $setting->cp_id = $cp_id;
            }
        } else {
            $user_id = (int)$input['user_id'];
            $setting = Setting::where('app_id', $app_id)
                ->where('user_id', $user_id)
                ->where('field', 'report')
                ->first();
            if($setting == null) {
                $setting = new Setting;
                $setting->user_id = $user_id;
            }
        }
        $setting->app_id = $app_id;
        $setting->field = 'report';
        $setting->set = json_decode($input['setStr']);
        $setting->save();

        return response('完成客制設定' , 200);
    }

    public function removeReportSetting(Request $request)
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

        $app_id = $app_id = (int) $input['app_id'];
        $setting = null;

        if (array_key_exists('cp_id',$input ) ){
            $cp_id = (int)$input['cp_id'];
            $setting = Setting::where('app_id', $app_id)
                ->where('cp_id', $cp_id)
                ->where('field', 'report')
                ->delete();

        } else {
            $user_id = (int)$input['user_id'];
            $setting = Setting::where('app_id', $app_id)
                ->where('user_id', $user_id)
                ->where('field', 'report')
                ->delete();

        }


        return response('刪除客制設定' , 200);
    }

    public function searchReport(Request $request)
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

        $mac = $input['macAddr'];
        $start = $input['start'];
        $end = $input['end'];
        $skip = 0;
        $limit = 1000;
        if(array_key_exists('skip', $input)) {
            $skip = (int)$input['skip'];
        }
        if(array_key_exists('limit', $input)) {
            $limit = (int)$input['limit'];
        }

        $count = Report::where('macAddr', $mac)
            ->whereBetween('recv', [$start, $end])
            ->orderBy('recv', 'desc')
            ->count();

        $reports = Report::where('macAddr', $mac)
            ->whereBetween('recv', [$start, $end])
            ->skip($skip)
            ->take($limit)
            ->orderBy('recv', 'desc')
            ->get();

        $result = array("check"=>"searchReport" ,"count"=>$count, "reports"=> $reports);

        return response($result , 200);
    }

    public function removeReport(Request $request)
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

        $mac = $input['macAddr'];
        $start = $input['start'];
        $end = $input['end'];
        $skip = 0;
        $limit = 1000;
        if(array_key_exists('skip', $input)) {
            $skip = (int)$input['skip'];
        }
        if(array_key_exists('limit', $input)) {
            $limit = (int)$input['limit'];
        }

        $reports = Report::where('macAddr', $mac)
            ->whereBetween('recv', [$start, $end])
            ->delete();
        $result = array("check"=>"removeReport" ,"message"=>"刪除完成");
        return response('刪除完成' , 200);
    }

    public function uploadImage(Request $request)
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
        $success = 0;
        $new_name = time().'.png';

        if(array_key_exists('file_name', $input )) {
            $new_name = $input['file_name'];
        }
        $option = 'UAV';
        if(array_key_exists('option', $input )) {
            $option = $input['option'];
        }

        $path = public_path().'/photo/';
        if(isset($_POST['img'])){

            $base64 = $_POST['img'];
            $img = str_replace('data:image/jpeg;base64,', '', $base64);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            //$dir = storage_path('app\\public\\UAV');
            $dir = storage_path('app/public/'.$option);
            if(!Storage::exists($dir)) {
                Storage::makeDirectory($dir, 0775, true); //creates directory
            }
            //$file = storage_path('app\\public\\UAV\\'.$new_name);
            $file = storage_path('app/public/'.$option.'/'.$new_name);
            $success = file_put_contents($file, $data);
        }
        $result = array("file"=>$file, "length"=>$success);

        /*if(isset($_POST['img'])){
            $new_name = time().'.'.'png';
            $base64 = $_POST['img'];
            $data = explode(',', $base64);
            $file = base64_decode($data[1]);
            $output_file = storage_path('app\\public\\UAV\\'.$new_name);;
            $file->move($output_file, $new_name);
            $file = fopen($output_file, "wb");
            fwrite($file, $base64);
            fclose($file);
            $st_id = Auth::user()->id;
            $keys = array('sign');
            $values = array($new_name);
            $data = array_combine($keys, $values);

            DB::table("students")->where('st_id',$st_id)->update($data);

        }*/
        return response($result , 200);
    }

    public function checkEmail(Request $request)
    {
        $email= $request->input('email');
        $user = $this->userService->getUserByEmail($email);
        if($user)
            return response($user->active , 200);
        return response(null , 200);
    }
}
