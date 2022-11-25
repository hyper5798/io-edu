<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Constant;

class QuestionConstant
{
    //快速驗證指定(只取兩個考題快速驗證)
    public const FAST_ANSWER_TEST = false;
    //考題content驗證HTML功能指定(指定領域:程式力,等級:初級的第1題修改content做測試)
    public const CONTENT_HTML_TEST = false;
    //網頁測試是否顯示答案
    public const WEB_ANSWER_TEST = true;
    //領域
    public const FIELD_1 = '全領域';
    public const FIELD_2 = '程式力';
    public const FIELD_3 = '電子力';
    public const FIELD_4 = '構造力';
    public const FIELD_5 = '網路力';
    public const FIELD_6 = '邏輯力';
    //等級
    public const LEVEL_1 = '初級';
    public const LEVEL_2 = '中級';
    public const LEVEL_3 = '高級';
    //
    public const FIELD_ALL_ID = 1;
    public const FIELD_DEFAULT_ID = 2;
    public const LEVEL_DEFAULT_ID = 1;
    //radar level
    public const RADAR_LEVEL = 10;
}
