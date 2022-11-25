<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Chapter;
use App\Models\Video;
use App\Repositories\ChapterRepository;
use App\Repositories\CourseCategoryRepository;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChapterController extends CommonController
{
    private $courseCategoryRepository, $courseService , $chapterRepository;
    /**
     * LoginController constructor.
     * @param  CourseCategoryRepository  $courseCategoryRepository
     * @param  CourseService $courseService
     * @param  ChapterRepository $chapterRepository
     */
    public function __construct(
        CourseCategoryRepository  $courseCategoryRepository,
        CourseService $courseService,
        ChapterRepository $chapterRepository
    )

    {
        $this->courseCategoryRepository = $courseCategoryRepository;
        $this->courseService = $courseService;
        $this->chapterRepository = $chapterRepository;
    }
    /**
     * Display a listing of the resource.
     *@param Request $request
     *@return View
     */
    public function index(Request $request)
    {
        $user = session('user');
        $input = $request->all();

        $chapter_id = 0;
        $video_id = 0;
        //$sort = (int)$request->input('sort', 0);
        $course_id = (int)$request->input('course_id', 0);
        $category_id = (int)$request->input('category_id', 0);
        $categories = $this->courseCategoryRepository->all();
        //$courses = $this->courseService->getBy('category_id', $category_id);
        $courses =  $this->courseService->getByCategoryUserId($category_id, $user['id']);
        $course = $this->courseService->findBy('id', $course_id);
        $video_id = (int)$request->input('video_id', 0);
        $chapters = $this->chapterRepository->getBy('course_id', $course_id);

        $videos = Video::where('course_id', $course_id)
            ->get();

        $sort = count($chapters)+1;
        $video_id = (int)$request->input('video_id', 0);

                //dd($videos);
        return view('pages.chapter', compact(['user','categories','chapters','courses', 'course','category_id','course_id', 'videos', 'sort', 'chapter_id', 'video_id']));
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
        $course_id = (int)$request->input('course_id', 0);
        $course = $this->courseCategoryRepository->find($course_id);
        $videos = Video::where('course_id', $course_id)
            ->get();
        $count = $this->chapterRepository->getBy('course_id', $course_id)
            ->count();
        $sort = $count+1;
        $course = Course::find($course_id);
        return view('pages.chapter-create',compact(['videos', 'category_id', 'course_id', 'course', 'sort', 'user']));
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
        $this->chapterRepository->create($input);
        $category_id = $request->input('category_id', 0);
        $course_id = $request->input('course_id', 0);
        $url = '/admin/chapter?category_id='.$category_id;
        $url = $url.'&course_id='.$course_id;
        return redirect($url);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Chapter $chapter)
    {
        //dd($answerCheck);
        $user = session('user');
        $course_id = $chapter->course_id;
        $course = $this->courseService->find($course_id);
        $categories = $this->courseCategoryRepository->all();
        $videos = Video::where('course_id', $course_id)
            ->get();
        $course = Course::find($course_id);
        $category_id = $course->category_id;

        return view('pages.chapter-edit',compact(['videos', 'category_id', 'course_id', 'course', 'chapter', 'user']));
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
        $this->chapterRepository->update($id, $input);
        $category_id = $input['category_id'];
        $course_id = $input['category_id'];
        $url = '/admin/chapter/?category_id='.$category_id.'&course_id='.$course_id;
        return redirect($url);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function editChapter(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = (int)$input['id'];
        $chapter = null;
        if($id>0)
            $chapter =  Chapter::find($id);
        else {
            $chapter = new Chapter;
            $chapter->course_id = (int)$input['course_id'];
            $chapter->sort = $input['sort'];
        }
        $chapter->title = $input['title'];
        if(isset($input['video_id'])) {
            $chapter->video_id = (int)$input['video_id'];
        }

        $chapter->save();

        if(isset($input['video_id'])) {
            $video = Video::find($input['video_id']);
            $video->chapter_id = $chapter->id;
            $video->save();
        }
        $url = '/admin/chapter?course_id='.$chapter->course_id;
        $url = $url.'&sort='.$chapter->sort;
        return redirect($url);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function delChapter(Request $request)
    {
        $id = (int)$request->input('id');
        $category_id = (int)$request->input('category_id', 0);
        $course_id = (int)$request->input('course_id', 0);
        $this->chapterRepository->destroy($id);
        $url = 'admin/chapter?category_id='.$category_id.'&course_id='.$course_id;
        return redirect($url);
    }
}
