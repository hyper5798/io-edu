<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Constant;

class AppConstant
{
    // APP數量限制
    public const APP_MAX = 2;
    // APP雙向設定數量限制
    public const CONTROL_SETTING_MAX = 3;
    //控制設定命名
    public const CONTROL_SETTING_TITLE = '雙向通道';
    public const DATA_TITLE = '數據通道';
    public const CONTROL_SETTING_KEY = "app_control_channel";
    //Route path
    public const APP_REPORTS_PATH = '/node/apps/reports';
    public const APP_CHANNEL_PATH = '/node/apps/channel';
    public const APP_API_KEY_PATH = '/node/apps/APIkey';
}
