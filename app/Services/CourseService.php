<?php

namespace App\Services;

use App\Constant\UserConstant;
use App\Models\Course;

use App\Presenters\DatePresenter;
use App\Repositories\CourseOptionRepository;
use App\Repositories\CourseRepository;
use App\Repositories\ScoreRepository;
use App\Repositories\UserRepository;
use Yish\Generators\Foundation\Service\Service;

class CourseService extends Service
{
    protected $repository, $userRepository, $courseOptionRepository,$scoreRepository;

    public function __construct(
        CourseRepository $repository,
        UserRepository $userRepository,
        CourseOptionRepository $courseOptionRepository,
        ScoreRepository $scoreRepository
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->courseOptionRepository = $courseOptionRepository;
        $this->scoreRepository = $scoreRepository;
    }

    public function getCourseCheck($user_id, $role_id ,$course) {

        if($role_id <  UserConstant::COMPANY_ADMIN) {
            return true;
        }

        if($course->user_id == $user_id) {
            return true;
        }

        $courseCheck = false;
        $userCourse = $this->courseOptionRepository->findBy('user_id', $user_id);



        if($userCourse) {
            $selects = $userCourse->category_selects;
            if(array_key_exists($course->category_id, $selects)) {
                $courseIdList = $selects[$course->category_id];
                if (in_array($course->id, $courseIdList)) {
                    $courseCheck = true;
                }
            }
            //dd($selects );
        }
        return $courseCheck;
    }

    public function getChaptersCheck($chapters,  $courseCheck, $free) {
        foreach ($chapters as $chapter) {
            if($courseCheck == true) {
                $chapter->check = true;
            } else {
                //無權限時的免費單元
                if($free >= $chapter->sort) {
                    $chapter->check = true;
                } else {
                    $chapter->check = false;
                }
            }
            if($chapter->video) {
                $chapterVideos[$chapter->id] = $chapter->video->video_url;
            } else {
                $chapterVideos[$chapter->id] = null;
            }

        }
        return $chapters;
    }

    public function getScoresByCourseId($course_id) {
        $list = $this->scoreRepository->getBy('course_id', $course_id);
        foreach ($list as $score) {
            $score->user_name = $score->user->name;
            $score->date = $score->created_at->toDateString();
        }
        return $list;
    }

    public function getIsScore($user_id, $course_id, $courseCheck) {
        if($courseCheck == false) return false;
        $count = $this->scoreRepository->getByCourseCourseId($user_id, $course_id)->count();
        if($count>0)
            return false;
        else
            return true;
    }

    public function getByCategoryId($category_id) {
        return $this->repository->getByCategoryId($category_id);
    }

    public function getByCategoryUserId($category_id, $user_id) {
        $user = $this->userRepository->find($user_id);
        if($user->role_id<3) {
            return $this->repository->getBy('category_id', $category_id);
        } else {
            return $this->repository->getByCategoryUserId($category_id, $user_id);
        }
    }

    public function create($input) {
        $this->repository->create($input);
    }

    public function update($id, $input) {
        $this->repository->update($id,$input);
    }

    public function getDate($dateString) {
        $time = strtotime($dateString);
        return date('Y-m-d',$time);
    }

    public function getByCourseIdWithCount($course_id) {
        return $this->repository->getByCourseIdWithCount($course_id);
    }
}
