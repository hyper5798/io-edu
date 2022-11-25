<?php

namespace App\Presenters;

class VideoPresenter
{
    public function convert($duration) {
        $c = null;
        $a= (int)floor($duration/60);
        $b= $duration%60;
        if($a>=60) {
            $c = (int)floor($a/60);
            $a = $a%60;
        }
        if($c) {
            return $c.':'.$a.':'.$b;
        } else {
            return $a.':'.$b;
        }
    }
}
