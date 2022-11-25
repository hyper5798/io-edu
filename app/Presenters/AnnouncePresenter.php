<?php

namespace App\Presenters;

use App\Constant\UserConstant;

class AnnouncePresenter
{
    public function tag($tagIndex) {

        return UserConstant::ANNOUNCE_TAG[$tagIndex-1];
    }
}
