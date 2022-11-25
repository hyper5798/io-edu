<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CourseCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends CommonController
{
    private $courseCategoryRepository;
    /**
     * LoginController constructor.
     * @param  CourseCategoryRepository  $courseCategoryRepository

     */
    public function __construct(
        CourseCategoryRepository  $courseCategoryRepository
    )

    {
        $this->courseCategoryRepository = $courseCategoryRepository;
    }
    /**
     * Display a listing of the resource.
     *@param Request $request
     *@return View
     */
    public function index(Request $request)
    {
        $user = session('user');
        $categories = $this->courseCategoryRepository->all();

        // dd(gettype($categories[0]['roles']));
        return view('pages.categories', compact(['user','categories']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function editCategory(Request $request)
    {
        $user = session('user');
        $input = $request->all();

        $id = (int)$input['id'];

        $category = null;


        if($id == 0) {
            $category = $this->courseCategoryRepository->create($input);
            $id = $category->id;
        }
        $tag = 'c'.$id;
        $input['tag'] = 'c'.$id;
        $this->courseCategoryRepository->update($id,  $input);
        $directory= 'public/video/c'.$id;
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function delCategory(Request $request)
    {
        $user = session('user');
        $id = (int)$request->input('id', 0);
        $category =$this->courseCategoryRepository->destroy($id );

        return back();
    }
}
