<?php

use App\Constant\CacheConstant;
use App\Models\User;
use App\Services\Base\Interfaces\CacheServiceInterface;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;

function getFarmSetting($device_id, $field)
{
    $farm_bot = Setting::where('device_id', $device_id)
        ->where('field', 'farm_bot')
        ->get();
    if(count($farm_bot) == 0) {
        if($field == 'farm_bot') {
            $setting = getFarmBotSetting();
        } else if($field == 'farm_plate') {
            $setting = getFarmPlateSetting();
        } else if($field == 'farm_home') {
            $setting = getFarmHomeSetting();
        } else  if($field == 'farm_script') {
            $setting = getFarmHomeSetting();
        }
    } else {
        $setting = $farm_bot->first()->set;
    }

    return $setting;
}
//檢查是否切換公司，及取得公司ID
function checkCpId($cp_id) {
    //登入用戶
    $user = session('user');
    //若登入用戶為Super Admin才能切換公司
    $target = session('target');

    if($target == null) {
        $target = array();
    }

    $isChangeCp = false;
    if ($cp_id == 0) {
        //未切換公司
        if(array_key_exists('cp_id', $target)) {
            $cp_id = $target['cp_id'];
        } else {
            $cp_id = $user['cp_id'];
        }

    } else {
        //切換公司
        $cp_id = (int)$cp_id;
        $target['cp_id'] = $cp_id;
        $isChangeCp = true;
    }
    //更新session的公司ID
    $target['cp_id'] = $cp_id;
    session(['target' => $target]);
    return ['cp_id'=>$cp_id, 'isChange'=>$isChangeCp];
}
//取得用戶ID，判斷有無數入得用戶ID參數，及是否更換公司
function getUserId(int $user_id = 1, $users ,bool $isChangeCp) {
    //登入用戶
    $user = session('user');
    //若登入用戶為Super Admin才能變更target的cp_id,user_id
    $target = session('target');
    //1:Super admin, 2:CP admin
    if ($user->role_id > 3) {//Super admin and CP admin可以切換用戶
        $user_id = $user['id'];
    } else {

        if($user_id > 0) {

        } else if($user_id == 0 && $isChangeCp) {
            $user_id = $users->first()->id;
        } else {
            if($user_id==0 && array_key_exists('user_id', $target ) ){
                $user_id = $target['user_id'];
            } else {
                $user_id = $user['id'];
            }
        }
    }
    //更新session的用戶ID
    $target['user_id'] = $user_id;
    session(['target' => $target]);
    return $user_id;
}

function getTargetUserId() {
    $target = session('target');
    return $target['user_id'];
}

function getTargetCpId() {
    $target = session('target');
    return $target['cp_id'];
}

//判斷是否切換link (develop:開發版,module:控制模組, room:智慧機電)
function getLink($input) {
    $link = array_key_exists('link', $input) ? $input['link'] : null;
    if($link == null) {
        $link = Session::get('link', function() { return 'develop'; });
    }
    return $link;
}

if (!function_exists('get_cache_key')) {
    /**
     * @param $key
     * @param mixed ...$params
     * @return string
     */
    function get_cache_key(string $key, ...$params): string
    {
        return sprintf($key, ...$params);
    }
}

if (!function_exists('setUserCache')) {
    /**
    * @param $cacheService
    * @param $userId
    * @param $key
    * @param $value
    */
    function setUserCache($cacheService, $userId, $key, $value) {
        $cacheService->put(
            get_cache_key(CacheConstant::USER_LOGIN_DURATION['name'], $userId,$key),
            $value,
            CacheConstant::USER_LOGIN_DURATION['expire']
        );
    }
}

if (!function_exists('getUserCache')) {
    /**
     * @param $cacheService
     * @param $userId
     * @param $key
     * @return String
     */
    function getUserCache($cacheService, $userId, $key) {
        return $cacheService->get(
            get_cache_key(CacheConstant::USER_LOGIN_DURATION['name'], $userId, $key)
        );
    }
}

if (!function_exists('forgotUserCache')) {
    /**
     * @param $cacheService
     * @param $userId
     * @param $key
     * @return String
     */
    function forgotUserCache($cacheService, $key) {
        return $cacheService->pull($key);
    }
}

if (!function_exists('getIdTitleList')) {
    /**
     * @param $cacheService
     * @param $userId
     * @param $key
     */
    function getIdTitleList($items) {
        $arr = [];
        foreach($items as $item) {
            $arr[$item->id] = $item->title;
        }
        return $arr;
    }
}

if (!function_exists('getEmailData')) {
    /**
     * @param $data
     */
    function getEmailData($data) {
        return [
            'company' => '歐利科技',
            'address' => '970 花蓮縣吉安鄉北昌村建昌路32巷6號1樓',
            'from_email' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'subject' => $data['subject'],
            'to_email' => $data['to_email'],
            'to_name' => $data['to_name'],
            'msg' => $data['msg'],
            'url' => $data['url']
        ];
    }
}

if (!function_exists('getNowDiff')) {
    /**
     * @param $oldTime
     */
    function getNowDiff($oldTime) {
        $carbon = carbon::parse ($oldTime);
        return (new Carbon)->diffInSeconds ($carbon, true);
    }
}

/* tokenVerify : 用戶憑證檢查
 * $input user_id: 用戶ID
 * $input user_id: 用戶憑證
 * return boolean
 * */
if (!function_exists('tokenVerify')) {
    /**
     * @param $input
     */
    function tokenVerify($input) {
        if (array_key_exists('token', $input)) {
            $token = $input['token'];
            $userId = $input['user_id'];
            $cacheService = app()->make(CacheServiceInterface::class);
            $cacheToken = getUserCache($cacheService, $userId, 'token');
            if ($token == $cacheToken) {
                return true;
            }
        }
            return false;
    }
}

if (!function_exists('array_equal')) {
    /**
     * @param $a
     * @param $b
     */
    function array_equal($a, $b) {
        return (
            is_array($a)
            && is_array($b)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }
}

