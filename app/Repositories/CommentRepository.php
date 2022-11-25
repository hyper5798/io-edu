<?php

namespace App\Repositories;

use App\Models\Comment;
use Yish\Generators\Foundation\Repository\Repository;

class CommentRepository extends Repository
{
    protected $model;

    public function __construct(Comment $model) {
        $this->model = $model;
    }

    public function getParentCommentsByCourseId($course_id) {
        return $this->model->ofCourse($course_id)->parentComments(null)->withCount(['children'])->orderBy('created_at','desc')->get();
    }

    public function getChildCommentsByCourseId($course_id, $parent_id) {
        return $this->model->ofCourse($course_id)->parentComments($parent_id)->orderBy('created_at','desc')->get();
    }
    //For super admin
    public function getParentCommentsByStatus($parent_id, $status) {
        return $this->model->parentComments(null)->ofStatus($status)->get();
    }

    //For super admin
    public function getCommentsByCourseStatus($arr=null, $status) {
        if($arr==null) {
            return $this->model->ofStatus($status)->get();
        } else {
            return $this->model->whereIn('course_id', $arr)->ofStatus($status)->get();
        }
    }
}
