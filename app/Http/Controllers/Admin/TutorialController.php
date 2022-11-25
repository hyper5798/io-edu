<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Chapter;
use App\Models\Video;
use Illuminate\View\View;
use Illuminate\Http\Request;

class TutorialController extends CommonController
{
    /**
     * Display a listing of the resource.
     *@param Request $request
     * @return view
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $sort = 1;
        $category_id = 1;
        $link = 'develop';
        if (array_key_exists('link', $input))
            $link = $input['link'];
        if (array_key_exists('sort', $input))
            $sort = (int)$input['sort'];

        /*if (array_key_exists('category_id', $input))
            $category_id = (int)$input['category_id'];*/

        if($link == 'develop' ) {
            $category_id = 1;
        } else if($link == 'module' ) {
            $category_id = 2;
        } else {
            $category_id = 3;
        }

        $user = session('user');
        $items = Course::where('category_id', $category_id)
            ->orderBy('id', 'asc')
            ->get();

        $arr = array();
        foreach ($items as $item) {
            array_push($arr, $item->id);
        }

        $chapterList = Chapter::where('sort', $sort)
            ->whereIn('course_id', $arr)
            ->get();


        foreach ($chapterList as $chapter) {
            //dd($chapter->video_id);
            $tmp =  Video::where('id',$chapter->video_id)->first();
            if($tmp != null) {
                $chapter->video_url = $tmp->video_url;
            }
        }
        if($link == 'develop') {
            return view('/nodes/tutorial', compact(['user', 'category_id', 'items','sort','chapterList', 'link']));
        } else {
            return view('/module/tutorial', compact(['user', 'category_id', 'items','sort','chapterList', 'link']));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
