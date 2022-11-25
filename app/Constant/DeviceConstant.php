<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Constant;

class DeviceConstant
{
    //開發版類型
    public const DEVELOP_TYPE = 101;
    //模組類型　
    public const MODULE_TYPE = 99;
    // 多合一類型
    public const ALL_TYPE = 200;
    //無人船類型
    public const USV_TYPE = 102;
    //農業機器人類型
    public const FARM_BOT_TYPE = 103;
    //推進器類型
    public const PROPELLER_TYPE = 104;
    //啟用狀態
    public const ACTIVE_STATUS = 3;
    //不啟用狀態
    public const INACTIVE_STATUS = 0;
    // 不支援
    public const NONE_SUPPORT = 0;
    //支援無人機
    public const UAV_SUPPORT = 1;
    //支援模組
    public const MODULE_SUPPORT = 2;
    //未設定
    public const SET_NONE = 0;
    //設定場域
    public const SET_ROOM = 1;
    //WIFI
    public const WIFI = 1;

}
