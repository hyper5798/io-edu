<?php

namespace App\Http\Controllers\Admin;

use App\Constant\CourseConstant;
use App\Constant\UserConstant;
use App\Models\Course;
use App\Repositories\ChapterRepository;
use App\Repositories\CourseCategoryRepository;
use App\Repositories\CourseOptionRepository;
use App\Services\CommentService;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends CommonController
{
    private $courseCategoryRepository, $courseService, $courseOptionRepository,$chapterRepository,$commentService;
    /**
     * LoginController constructor.
     * @param  CourseCategoryRepository  $courseCategoryRepository
     * @param  CourseService $courseService
     * @param  CourseOptionRepository $courseOptionRepository
     * @param  ChapterRepository $chapterRepository
     * @param  CommentService $commentService
     */
    public function __construct(
        CourseCategoryRepository  $courseCategoryRepository,
        CourseService $courseService,
        CourseOptionRepository $courseOptionRepository,
        ChapterRepository $chapterRepository,
        CommentService $commentService
    )

    {
        $this->courseCategoryRepository = $courseCategoryRepository;
        $this->courseService = $courseService;
        $this->courseOptionRepository = $courseOptionRepository;
        $this->chapterRepository = $chapterRepository;
        $this->commentService = $commentService;
    }
    /**
     * Display a listing of the resource.
    *@param Request $request
    * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function index(Request $request)
    {
        $user = session('user');
        $categories = $this->courseCategoryRepository->all();
        if(count($categories)==0) {
            $message = '請先建立課程類型';
            return redirect('admin/categories')->withErrors($message);
        }
        $free_chapter = CourseConstant::FREE_CHAPTER;
        $category_id = (int)$request->input('category_id', $categories[0]->id);
        //$courses = Course::where('category_id', $category_id)->get();
        //$courses = $this->courseService->getBy('category_id', $category_id);
        $courses =  $this->courseService->getByCategoryUserId($category_id, $user['id']);
        //dd($videos);
        return view('pages.courses', compact(['user','courses', 'category_id', 'categories', 'free_chapter']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|view
     */
    public function create(Request $request)
    {
        $user = session('user');
        $category_id = (int)$request->input('category_id', 0);
        $categories = $this->courseCategoryRepository->all();
        $free_chapter = CourseConstant::FREE_CHAPTER;
        return view('pages.course-create',compact(['user','categories', 'category_id', 'free_chapter']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $this->courseService->create($input);
        return redirect ('/admin/courses');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Course $course)
    {
        //dd($answerCheck);
        $user = session('user');
        $categories = $this->courseCategoryRepository->all();
        return view('pages.course-edit',compact(['user','course', 'categories']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $this->courseService->update($id, $input);
        //return back ();
        $category_id = $input['category_id'];
        $url = 'admin/courses?category_id='.$category_id;
        return redirect ($url);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @return View
     */
    public function editCourse(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = (int)$input['id'];
        $course = null;
        if($id>0)
            $course =  Course::find($id);
        else {
            $course = new Course;
            $course->user_id = $user['id'];
            $course->category_id = $input['category_id'];
            $course->is_show = (int)$input['is_show'];
            $course->description = $input['description'];
        }
        $course->title = $input['title'];
        $course->save();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
    *  @return View
     */
    public function delCourse(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = (int)$input['id'];
        Course::find($id)->delete();
        return back();
    }

    /**
     * Display a listing of the resource.
     *@param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function allCourses(Request $request)
    {
        $user = session('user');
        $categories = $this->courseCategoryRepository->getWithCourseCount();
        if(count($categories)==0) {
            $message = '請先建立課程分類';
            return redirect('admin/categories')->withErrors($message);
        }
        $category_id = (int)$request->input('category_id', $categories->first()->id);
        $category = $this->courseCategoryRepository->find($category_id);
        $courses = $this->courseService->getByCategoryId($category_id);
        return view('learn.allCourses', compact(['user', 'categories', 'category','courses']));
    }

    /**
     * Display all of videos of the course.
     *@param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function courseVideo(Request $request)
    {
        //說明:原頁面做課程介紹及單元影像播放，但切換時video無法載入影片，本頁面目前只做課程介紹
        //播放影片改由chapterVideo處理
        $user = session('user');
        $course_id = (int)$request->input('course_id', 0);
        //$course = $this->courseService->find($course_id);
        $course = $this->courseService->getByCourseIdWithCount($course_id);
        $chapterVideos = array();
        $scores = $this->courseService->getScoresByCourseId($course_id);
        $avg_scores = round($scores->avg('rating'));
        $comments = $this->commentService->getParentCommentsByCourseId($course_id);
        $commentChildren = $this->commentService->getCommentChildren($comments);

        //用戶權限檢查  $courseCheck=true => 有權限
        $courseCheck = $this->courseService->getCourseCheck($user['id'], $user->role_id ,$course);

        //可否評論
        $isScore = $this->courseService->getIsScore($user['id'], $course_id, $scores, $courseCheck);
        $isComment = $courseCheck;

        if($user->active == UserConstant::DISABLE_STATUS) {
            $isScore = false;
            $isComment = false;
        }

        $chapters = $course->chapters;
        if(count($chapters) == 0) {
            $message = $course->title.': 尚未建立課程單元!';
            $url= 'learn/allCourses';
            return redirect($url)->withErrors($message);
        }
        foreach ($chapters as $chapter) {
            if($chapter->video) {
                $chapterVideos[$chapter->id] = $chapter->video->video_url;
            } else {
                $chapterVideos[$chapter->id] = null;
            }

        }
        $chapters = $this->courseService->getChaptersCheck($chapters, $courseCheck,  $course->freeChapterMax);
        $chapter_id = (int)$request->input('chapter_id', $chapters->first()->id);
        $chapter_index = (int)$request->input('chapter_index', 0);
        $data =  [
            'avg_scores'=> $avg_scores,
            'course_id' => $course_id,
            'chapter_id' => $chapter_id,
            'chapter_index' => $chapter_index,
            'isScore' => $isScore,
            'isComment' => $isComment,
            'user' => $user,
            'courseCheck' => $courseCheck
        ];
         //dd($comments);
        return view('learn.courseVideo', compact(['user', 'course', 'chapterVideos', 'chapters', 'scores', 'comments', 'commentChildren', 'data']));
    }

    /**
     * Display all of videos of the course.
     *@param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function commentReply(Request $request)
    {
        $user = session('user');

        $courses = $this->courseService->getBy('user_id', $user['id']);
        $comments = $this->commentService->getNewParentComments( $user['id'], $user->role_id, $courses);
        //dd($comments);
        return view('learn.commentReply', compact(['user', 'comments']));
    }

    /**
     * Display a course of the resource.
     *@param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    /*public function chapterVideo(Request $request)
    {
        $user = session('user');
        $userCourse = $this->courseOptionRepository->findBy('user_id', $user['id']);

        $course_id = (int)$request->input('course_id', 0);
        $course = $this->courseService->find($course_id);
        $chapterVideos = array();
        $courseCheck = false;
        //用戶權限檢查  $courseCheck=true => 有權限
        if($userCourse) {
            $selects = $userCourse->category_selects;
            if(array_key_exists($course->category_id, $selects)) {
                $courseIdList = $selects[$course->category_id];
                if (in_array($course_id, $courseIdList)) {
                    $courseCheck = true;
                }
            }
            //dd($selects );
        }
        $chapters = $course->chapters;
        $chapter_id = (int)$request->input('chapter_id', $chapters->first()->id);
        $chapter_index = (int)$request->input('chapter_index', 0);
        $chapter = $chapters[$chapter_index];
        $arr = array('count'=>$chapter->count+1);
        $this->chapterRepository->update($chapter_id, $arr);
        foreach ($chapters as $chapter) {
            //dd($chapter->video);

            if($courseCheck == true) {
                $chapter->check = true;
            } else {
                //無權限時的免費單元
                if($course->freeChapterMax >= $chapter->sort) {
                    $chapter->check = true;
                } else {
                    $chapter->check = false;
                }
            }
            $chapterVideos[$chapter->id] = $chapter->video->video_url;
        }
        return view('learn.chapterVideo', compact(['user', 'course', 'chapterVideos', 'chapters', 'course_id','chapter_id', 'chapter_index', 'chapter']));
    }*/
}
