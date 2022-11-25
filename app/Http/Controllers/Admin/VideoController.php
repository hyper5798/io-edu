<?php

namespace App\Http\Controllers\Admin;

use App\Models\Chapter;
use App\Models\Video;
use App\Repositories\CourseCategoryRepository;
use App\Services\CourseService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends CommonController
{
    private $courseCategoryRepository, $courseService;
    /**
     * LoginController constructor.
     * @param  CourseCategoryRepository  $courseCategoryRepository

     */
    public function __construct(
        CourseCategoryRepository  $courseCategoryRepository,
        CourseService $courseService
    )

    {
        $this->courseCategoryRepository = $courseCategoryRepository;
        $this->courseService = $courseService;
    }
    /**
     * Display a listing of the resource.
     *@param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function index(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $categories =$this->courseCategoryRepository->all();
        if(count($categories)==0) {
            $message = '請先建立課程分類';
            return redirect('admin/categories')->withErrors($message);
        }
        $category_id = (int)$request->input('category_id', $categories[0]->id);

        //$courses = $this->courseService->getBy('category_id', $category_id);
        $courses =  $this->courseService->getByCategoryUserId($category_id, $user['id']);
        if(count($courses)==0) {
            $url = 'admin/courses?category_id='.$category_id;
            $message = '請先建立課程';
            return redirect($url)->withErrors($message);
        }
        $course_id = (int)$request->input('course_id', 0);
        if($course_id == 0) {
            $course_id = $courses->first()->id;
        }
        $course = $this->courseService->find($course_id);

        $video_id = 0;
        if(isset($request['video_id'])) {
            $video_id = (int)$request['video_id'];
        }

        $video_id = 0;
        if(isset($request['video_id'])) {
            $video_id = (int)$request['video_id'];
        }


        $videos = Video::where('course_id', $course_id)->get();
        //dd($videos);
        return view('pages.videos', compact(['user','videos', 'categories','category_id', 'courses', 'course','course_id','video_id']));
    }

    /**
     * Create a video from chapter.
     *@param Request $request
     * @return View
     */
    public function create(Request $request)
    {
        $input = $request->all();

        $category_id = (int)$request->input('category_id', 1);
        $category =$this->courseCategoryRepository->find($category_id);
        $course_id = (int)$request->input('course_id', 0);
        $course = $this->courseService->find($course_id);
        $sort= (int)$request->input('sort', 0);

        return view('pages.video-create', compact(['category_id','category','course', 'course_id','sort']));
    }
    /**
     * Upload video  to storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadVideo(Request $request)
    {
        $user = session('user');
        $file_url = null;
        $path = null;
        $input = $request->all();
        $video_id = (int)$request->input('id' , 0);
        $category_id = (int)$request->input('category_id', 0);
        $course_id = (int)$request->input('course_id', 0);
        $chapter_id = (int)$request->input('chapter_id', 0);
        $duration = (int)$request->input('duration', 0);
        $from= $request->input('from', null);

        if($request->hasFile('file') == false){
            return back()->withErrors('未選擇影片或影片檔案過大!');
        }

        $isChange = false;
        if($request->hasFile('file')){
            $folder_path1 = 'public/video/c'.$category_id;
            if (!Storage::exists($folder_path1)) {
                Storage::makeDirectory($folder_path1);
            }
            $folder_path = 'public/video/c'.$category_id.'/o'.$course_id;
            //$folder_path = 'public/video/c'.$category_id;
            if (!Storage::exists($folder_path)) {
                Storage::makeDirectory($folder_path);
            }
            $file = $request->file('file');
            //$extension = $file->getClientOriginalExtension();
            $file_name = $file->getClientOriginalName();
            //$file_title = str_replace('.'.$extension, "",$file_name);
            $path = $file->storeAs($folder_path, $file_name);
            $file_url = url(Storage::url($path));
            $isChange = true;
        }

        if($video_id == 0) {
            $v = new Video;

        } else {
            $v = Video::find($video_id);
            if($v->storage_path && $isChange) {
                $myPath = $v->storage_path;
                $exists = Storage::disk('local')->exists($myPath);
                if ($exists) {
                    Storage::disk('local')->delete($myPath);
                }
            }
        }
        //$v->user_id = $user['id'];
        $v->video_name = $input['video_name'];
        $v->title = $input['title'];
        if($file_url)
            $v->video_url = $file_url;
        if($path)
            $v->storage_path = $path;
        $v->category_id = $category_id;
        $v->course_id = $course_id;
        $v->duration = $duration;
        //$v->user_id = $user['id'];
        if(array_key_exists('sort',$input))
            $v->sort = (int)$input['sort'];
        $v->save();
        /*if($course_id  != null) {
            $chapter = new Chapter;
            $chapter->course_id = $course_id;
            $chapter->sort = $v->sort;
            $chapter->video_id = $v->id;
            $chapter->title = $file_title;
            $chapter->save();



            $url = '/admin/chapter?course_id='.$course_id;
            $url = $url.'&chapter_id='.$chapter->id;
            $url = $url.'&video_id='.$v->id;
            return redirect($url);
        } else {
            return redirect('/admin/videos?video_id='.$v->id);
        }*/
        $url = null;
        if($from) {//Back to chapter
            $url = '/admin/chapter';
        } else {
            $url = '/admin/videos';
        }
        $url = $url.'?category_id='.$category_id;
        $url = $url.'&course_id='.$course_id;
        return redirect($url);
    }

    /**
     * Update profile user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editVideo(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = (int)$input['id'];

        $course_id = (array_key_exists('course_id', $input)) ? (int)$input['course_id'] : null;
        $chapter_id = (array_key_exists('chapter_id',$input)) ? (int)$input['chapter_id'] : null;

        $video = null;
        if($id > 0) {
            $v1 = Video::find($id);
            if($v1->user_id != $user['id']) {
                $url = '/admin/chapter?course_id='.$course_id;
                $url = $url.'&chapter_id='.$chapter_id;
                $url = $url.'&video_id='.$id;
                return redirect($url)->withErrors('你沒有更改影片權限!');
            }
        }
        if($id>0)
            $video = Video::where('id', $id)->first();
        else {
            $video = new Video;
            //$video->user_id = $user['id'];
        }
        if(array_key_exists('video_name',$input))
            $video->video_name = $input['video_name'];

        if(array_key_exists('title',$input))
            $video->title = $input['title'];

        if(array_key_exists('content',$input))
            $video->content = $input['content'];

        if(array_key_exists('storage_path',$input))
            $video->storage_path = $input['storage_path'];

        if(array_key_exists('video_url',$input))
            $video->video_url = $input['video_url'];

        if(array_key_exists('category_id',$input))
            $video->category_id = (int)$input['category_id'];

        if(array_key_exists('sort',$input))
            $video->sort = (int)$input['sort'];

        $video->save();
        if($course_id  != null) {
            $url = '/admin/chapter?course_id='.$course_id;
            $url = $url.'&chapter_id='.$chapter_id;
            $url = $url.'&video_id='.$video->id;
            return redirect($url);
        } else {
            return redirect('/admin/videos?video_id='.$video->id);
            //return redirect('/admin/videos');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delVideo(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $v = Video::find($id);
        if($v->storage_path) {
            $myPath = $v->storage_path;
            $exists = Storage::disk('local')->exists($myPath);
            if ($exists) {
                Storage::disk('local')->delete($myPath);
            }
        }
        Video::where('id', $id)->delete();
        return back();
    }
}
