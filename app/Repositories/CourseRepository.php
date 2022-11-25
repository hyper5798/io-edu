<?php

namespace App\Repositories;

use App\Models\Course;
use Yish\Generators\Foundation\Repository\Repository;

class CourseRepository extends Repository
{
    protected $model;

    public function __construct(Course $model) {
        $this->model = $model;
    }

    public function getByCategoryId($category_id) {
        return  $this->model->isShow()->ofCategory($category_id)->get();
    }

    public function getByCategoryUserId($category_id, $user_id) {
        return  $this->model->ofCategory($category_id)->ofUser($user_id)->get();
    }

    public function getByCourseIdWithCount($course_id) {
        return  $this->model->withCount(['scores', 'comments'])->find($course_id);
    }
}
