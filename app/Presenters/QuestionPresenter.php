<?php

namespace App\Presenters;

use App\Constant\QuestionConstant;
use App\Models\Question;

class QuestionPresenter
{
    public function field($field_id) {

        switch ($field_id) {
            case 1:
                return QuestionConstant::FIELD_1;
            case 2:
                return QuestionConstant::FIELD_2;
            case 3:
                return QuestionConstant::FIELD_3;
            case 4:
                return QuestionConstant::FIELD_4;
            case 5:
                return QuestionConstant::FIELD_5;
            case 6:
                return QuestionConstant::FIELD_6;
            default:
                return QuestionConstant::FIELD_1;
        }
    }

    public function level($level_id) {
        switch ($level_id) {
            case 1:
                return QuestionConstant::LEVEL_1;
            case 2:
                return QuestionConstant::LEVEL_2;
            case 3:
                return QuestionConstant::LEVEL_3;
            default:
                return QuestionConstant::LEVEL_1;
        }
    }
}
