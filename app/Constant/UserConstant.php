<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Constant;

class UserConstant
{
    //超級管理員
    public const SUPER_ADMIN = 1;
    //講師　
    public const COMPANY_ADMIN = 7;
    //場域管理員
    public const ROOM_ADMIN = 8;
    //場域用戶
    public const ROOM_USER = 9;
    //進階用戶
    public const ADVANCE_USER = 10;
    //一般用戶
    public const NORMAL_USER = 11;
    //啟用狀態
    public const ACTIVE_STATUS = 1;
    //不啟用狀態
    public const INACTIVE_STATUS = 0;
    //禁止狀態
    public const DISABLE_STATUS = 2;
    //狀態類型
    public const STATUS_TYPE = array("禁用","啟用", "禁止留言");
    //User send mail count
    public const MAIL_COUNT = "mail_count";
    //重寄認證信最多次數
    public const RESEND_MAIL_CHECK = 3;
    //重寄認證信最多次數
    public const RESEND_MAIL_MAX = 10;
    //通知信箱最大數
    public const NOTIFY_EMAIL_MAX = 5;
    //聲明宣告類型
    public const ANNOUNCE_TAG = array("免責聲明","系統通知");
    public const ANNOUNCE_DISCLAIMER = 1;
    public const ANNOUNCE_SYSTEM_NOTIFY = 2;
}
