<?php
namespace App\Repositories;

use App\Models\Setting;
use Yish\Generators\Foundation\Repository\Repository;

class SettingRepository extends Repository
{
    protected $model;

    public function __construct(Setting $model) {
        $this->model = $model;
    }

    public function getFarmSetting($device_id, $field)
    {
        $farm_settings = Setting::where('device_id', $device_id)
            ->where('field', $field)
            ->get(['id','set']);
        $set = null;
        if(count($farm_settings) == 0) {
            if($field == 'farm_bot') {
                $set = $this->getFarmBotSetting();
            } else if($field == 'farm_plate') {
                $set = $this->getFarmPlateSetting();
            } else if($field == 'farm_home') {
                $set = $this->getFarmHomeSetting();
            } else  if($field == 'farm_script') {
                //$set = $this->getFarmScriptSetting();
                $set = array();
            } else if($field == 'farm_commands') {
                $set = $this->getFarmCommands();
            }  else if($field == 'sensor_trigger') {
                $set = $this->getTrigger();
            }
        } else {
            if($field == 'farm_script') {
                /*$set = array();
                foreach ($farm_settings as $setting) {
                    array_push($set,$setting->set);
                }*/
                $set = $farm_settings;
            } else {
                $set = $farm_settings->first()->set;
            }
        }

        return $set;
    }

    public function getUserSetting($user_id, $field)
    {
        $settings = Setting::where('user_id', $user_id)
            ->where('field', $field)
            ->get(['id','set']);
        $setting = null;
        if(count($settings) == 0) {
            return null;
        } else {
            $setting = $settings->first();
            if($setting != null)
                return $setting;
        }

        return null;
    }

    public function getDeviceSetting($device_id, $field, $app_id=null)
    {
        if($app_id != null) {
            $farm_settings = Setting::where('app_id', $app_id)
                ->where('field', $field)
                ->get(['id','set']);
        } else {
            $farm_settings = Setting::where('device_id', $device_id)
                ->where('field', $field)
                ->get(['id','set']);
        }

        $setting = null;
        if(count($farm_settings) == 0) {
            $setting = array();
        } else {
            $setting = $farm_settings->first();
            if($setting != null)
                $setting = $setting->set;
        }

        return $setting;
    }

    function getFarmBotSetting()
    {
        $box = array("number"=>3, "row"=>3, "column"=>1, "interval"=>100);
        $plant = array("number"=>10,"row"=>1, "column"=>10, "interval"=>100);
        $start = array("z"=>0, "y"=>0, "x"=>0);
        return array("field"=>"b", "start"=>$start,"box"=>$box, "plant"=>$plant);
    }

    function getFarmHomeSetting()
    {
        $box = array("row"=>1, "column"=>1);
        $plant = array("row"=>1, "column"=>1);
        return array("box"=>$box, "plant"=>$plant);
    }

    function getFarmPlateSetting()
    {
        $box = array("row"=>1, "column"=>1);
        $plant = array("row"=>1, "column"=>1);
        return array("box"=>$box, "plant"=>$plant);
    }

    function getFarmScriptSetting()
    {
        //fix:???????????? , dynamic:????????????

        $code= array();
        $set = array('name'=>'??????', 'id'=>'test', 'codeList'=>$code);
        $item = array('id'=>0, 'set'=>$set);
        $setting = array($item );

        return $setting;
    }

    public function getFarmScriptEmpty()
    {
        //fix:???????????? , dynamic:????????????
        $code= array();
        $set = array('name'=>'', 'id'=>'', 'codeList'=>$code);
        $setting= array('id'=>0, 'set'=>$set);

        return $setting;
    }

    function getTrigger()
    {
        $set = array('name'=>'', 'field'=>'', 'operator'=>3, 'value'=>0, 'message'=>'','check'=>true);
        $setting = array($set  );

        return $setting;
    }

    public function getFarmCommands()
    {
        $plant= array('name'=>'??????', 'command'=>'plant');
        $crop = array('name'=>'??????', 'command'=>'crop');
        $watering= array('name'=>'??????', 'command'=>'watering');
        $stop_watering= array('name'=>'????????????', 'command'=>'stop_watering');
        $stop = array('name'=>'????????????', 'command'=>'stop');
        $location = array('name'=>'????????????', 'command'=>'location');
        $up= array('name'=>'??????', 'command'=>'up');
        $down= array('name'=>'??????', 'command'=>'down');
        $home= array('name'=>'??????', 'command'=>'home');
        $left= array('name'=>'??????', 'command'=>'left');
        $right= array('name'=>'??????', 'command'=>'right');
        $stretch= array('name'=>'??????', 'command'=>'stretch');
        $pullback= array('name'=>'??????', 'command'=>'pullback');
        $setting= array('plant'=> $plant, 'crop'=> $crop, 'stop'=>$stop,
                         'watering'=> $watering, 'stop_watering'=> $stop_watering,'up'=>$up,
                         'down'=> $down, 'home'=> $home, 'left'=>$left, 'right'=>$right,
                         'stretch'=>$stretch, 'pullback'=>$pullback, 'location'=>$location);

        return $setting;
    }

    function getEmptyAppControlSetting()
    {
        $arr = [];
        for($i=1;$i<=8;$i++) {
            $key = 'set'.$i;
            $arr[$key ] = array("key"=>$key ,"title"=>'', 'value'=> '');
        }

        return $arr;
    }
}
