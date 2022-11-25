<?php

namespace App\Repositories;

use App\Models\Score;
use Yish\Generators\Foundation\Repository\Repository;

class ScoreRepository extends Repository
{
    protected $model;

    public function __construct(Score $model) {
        $this->model = $model;
    }

    public function getByCourseCourseId($user_id, $course_id) {
        return  $this->model->ofUser($user_id)->ofCourse($course_id)->get();
    }
}
