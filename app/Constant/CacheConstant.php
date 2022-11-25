<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Constant;

class CacheConstant
{
    // controller 層調用的前缀
    public const PREFIX_ID = 'id_';

    public const QUESTION_UPLOAD_PATH = 'id_';

    public const USER_LOGIN_DURATION = [
        'name' => self::PREFIX_ID . '%d:%s',
        'email' => '%s:%s',
        'expire' => 3600 * 24 * 1,
    ];

}
