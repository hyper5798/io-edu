<?php

namespace App\Presenters;

class DatePresenter
{
    public function getDate($dateString) {
        $time = strtotime($dateString);
        return date('Y-m-d',$time);
    }

    public function getTime($dateString) {
        $time = strtotime($dateString);
        return date('H:i:s', $time);
    }

    public function getDateTime($dateString) {
        $time = strtotime($dateString);
        return date('Y-m-d H:i:s', $time);
    }
}
