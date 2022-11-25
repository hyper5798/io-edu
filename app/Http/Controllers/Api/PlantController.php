<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function updateKinds(Request $request)
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
        $setting = Setting::where('device_id', $input['device_id'])
            ->where('field', 'plant_kinds')->get();
        if(count($setting) == 0) {
            $setting = new Setting;
            $setting->device_id = $input['device_id'];
            $setting->field = 'plant_kinds';
        } else {
            $setting =  $setting->first();
        }
        $setting->set = json_decode($input['kindStr']);
        $setting->save();
        return response('更新成功!', 200);
    }

    public function updatePlants(Request $request)
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

        $farmObj = json_decode($input['farmStr']);
        $device_id =$input['device_id'];

        //For update single plant
        if(count((array)$farmObj) == 1) {
            foreach ($farmObj as $key => $value) {
                $plant = plant::where('device_id', $input['device_id'])
                    ->where('plant_key', $key)
                    ->first();
                $key = $plant->plant_key;
                $value = $farmObj->$key;
                $plant->title = $value->title;
                $plant->tag = $value->tag;
                $plant->color = $value->color;
                $plant->colorBlock = $value->colorBlock;
                $plant->kind = $value->kind;
                if(property_exists($value, 'maturity')) {
                    $plant->maturity = $value->maturity;
                }
                if(property_exists($value, 'plant_time') && $value->plant_time != '') {
                    $plant->plant_time = $value->plant_time;
                } else {
                    $plant->plant_time= null;
                }
                if(property_exists($value, 'crop_time' && $value->crop_time != '')) {
                    $plant->crop_time = $value->crop_time;
                } else {
                    $plant->crop_time = null;
                }
                $plant->save();
            }
            return response('更新成功!', 200);
        }

        $keys = array_keys(get_object_vars($farmObj));
        //檢查是否更改後plant數量不正確修正
        //缺少時移除
        $plants = plant::where('device_id', $device_id)
            ->whereNotIn('plant_key', $keys)
            ->delete();

        //For update all of plants
        foreach ($farmObj as $key => $value) {
            $plant = plant::where('device_id', $input['device_id'])
                ->where('plant_key', $key)
                ->first();
            if($plant == null) {
                $plant = new Plant;
            }

            $plant->title = $value->title;
            $plant->tag = $value->tag;
            $plant->device_id = (int)$input['device_id'];
            $plant->color = $value->color;
            $plant->colorBlock = $value->colorBlock;
            $plant->box = $value->box;
            $plant->plant = $value->plant;
            $plant->kind = $value->kind;
            $plant->sort = $value->sort;
            $plant->plant_key = $key;
            if(property_exists($value, 'maturity')) {
                $plant->maturity = $value->maturity;
            }

            $plant->save();
        }

        return response('更新成功!', 200);
    }
}
