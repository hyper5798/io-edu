<?php

namespace App\Services;

use App\Constant\CourseConstant;
use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;
use Yish\Generators\Foundation\Service\Service;

class CommentService extends Service
{
    protected $repository,$userRpository;

    public function __construct(CommentRepository $repository, UserRepository $userRepository) {
        $this->repository = $repository;
        $this->userRpository = $userRepository;
    }

    public function getNewParentComments($user_id, $role_id, $courses) {
        $comments = null;
        if($role_id<3) { //Super Admin
            //parent_id: null , course_id array: null, status: 0
            $comments = $this->repository->getCommentsByCourseStatus( null, CourseConstant::NEW_STATUS);
        } else if($role_id<8) {
            $idList = array_column($courses->toArray(), 'id');
            //parent_id: null , course_id array: $idList, status: 0
            $comments = $this->repository->getCommentsByCourseStatus($idList, CourseConstant::NEW_STATUS);
        }
        foreach($comments as $comment) {
            $comment->user_name = $comment->user->name;
            $comment->course_title = $comment->course->title;
            //$comment->date = $comment->created_at->toDateString();
        }

        return $comments;
    }

    public function getParentCommentsByCourseId($course_id) {
        $comments = $this->repository->getParentCommentsByCourseId($course_id);
        foreach ($comments as $comment) {
            $comment->user_name = $comment->user->name;
        }
        return $comments;
    }

    public function getCommentChildren($comments) {
        $arr = array();
        foreach($comments as $comment) {
            $children1 = $comment->children;
            $children = $this->repository->getChildCommentsByCourseId($comment->course_id, $comment->id);
            foreach($children as $child) {
                $child->user_name = $child->user->name;
                $child->reply_show = false;
                $child->reply = '';
            }
            $arr[$comment->id] = $children;
        }
        return $arr;
    }


}
