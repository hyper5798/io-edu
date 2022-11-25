<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CourseCategoryRepository;
use App\Repositories\CourseOptionRepository;
use App\Repositories\CourseUserRepository;
use App\Repositories\UserRepository;
use App\Services\CourseService;
use Illuminate\Http\Request;
use App\Models\Cp;
use App\Models\Role;
use App\Models\User;
//use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends CommonController
{
    private $courseCategoryRepository, $courseService, $courseOptionRepository, $userRepository;
    /**
     * LoginController constructor.
     * @param  CourseCategoryRepository  $courseCategoryRepository
     * @param  CourseService $courseService
     * * @param  CourseOptionRepository $courseOptionRepository
     * * @param  UserRepository $userRepository
     */
    public function __construct(
        CourseCategoryRepository  $courseCategoryRepository,
        CourseService $courseService,
        CourseOptionRepository $courseOptionRepository,
        UserRepository $userRepository
    )

    {
        $this->courseCategoryRepository = $courseCategoryRepository;
        $this->courseService = $courseService;
        $this->courseOptionRepository = $courseOptionRepository;
        $this->userRepository = $userRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function index()
    {
        $user = session('user')->toArray();

        if($user['role_id'] > 2){
            $message = trans('auth.permission_denied',  ['name' => 'Admin']);
            return redirect('/login?link=develop')->with('message', $message );
        }
        //$users = User::all();
        $users = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->join('cps', 'users.cp_id', '=', 'cps.id')
            ->where('users.id', '!=' , 1)
            ->select('users.id', 'users.name', 'users.email', 'users.cp_id', 'users.role_id', 'users.active','users.updated_at', 'cps.cp_name', 'roles.role_name')
            ->get();
        $roles = Role::get(['role_id', 'role_name']);
        $cps = Cp::get(['id', 'cp_name']);
        //$test = Redis::get('name');
        return view('pages.users', compact(['user','users', 'roles', 'cps']));
    }

    public function update(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        unset($input['_token']);
        unset($input['_method']);
        $id = (int)$input['id'];
        $mUser = null;
        if($id>0) {
            $mUser = User::find($id);
            $mUser-> active = $input['active'];

        } else {
            $mUser = new User;
            $mUser-> email = $input['email'];
            $mUser->active = 1;
            $mUser->password = $input['password'];
        }
        if($user->role_id == 1) {
            //Super admin can change company
            $mUser->cp_id = $input['cp_id'];
            $mUser->role_id = $input['role_id'];
        } else if($user->role_id == 2){
            //Local admin only change to slf company
            $mUser->cp_id = $user->cp_id;
            if((int)$input['role_id'] < 2) {
                $mUser->role_id = 2;
            } else {
                $mUser->role_id = $input['role_id'];
            }
        } else if($user->role_id == 8){
            //Local admin only change to slf company
            $mUser->cp_id = $user->cp_id;
            if((int)$input['role_id'] < 8) {
                $mUser->role_id = 8;
            } else {
                $mUser->role_id = $input['role_id'];
            }
        }
        $mUser->name = $input['name'];
        $mUser->save();
        return back();
    }

    public function destroy(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        unset($input['id']);
        $affectedRows = User::where('id', $id)->delete();
        return redirect('\/admin/users');
    }

    public function userCourses(Request $request)
    {
        $input = $request->all();

        $target_id = (int) $request->input('user_id', 0);
        $user = $this->userRepository->find($target_id);
        $categories = $this->courseCategoryRepository->all();
        $userCourse = $this->courseOptionRepository->findBy('user_id', $target_id);
        $category_selects = array();
        if($userCourse)
            $category_selects = $userCourse->category_selects;
        $categoryCourses = array();
        $categoryChecks = array();

        foreach ($categories as $category) {
            $category_id = $category->id;
            $arr = array();
            $count2 = 0;
            foreach ($category->courses as $course) {
                if($category_selects) {
                    if(array_key_exists($category_id, $category_selects)) {
                        $selects = $category_selects[$category_id];
                        $count2 = count($selects);
                        if (in_array($course->id, $selects)) {
                            $course->check = true;
                            continue;
                        }
                    }
                }
                $course->check = false;
            }
            $count1 = count($category->courses);

            if(  $count1 == $count2 ) {
                $categoryChecks[$category->id] = true;
            } else {
                $categoryChecks[$category->id] = false;
            }

            $categoryCourses[$category->id] = $category->courses;

        }

        return view('pages.userCourses', compact(['user','target_id', 'categories', 'categoryCourses' , 'categoryChecks']));
    }

    public function updateUserCourses(Request $request)
    {
        $input = $request->all();

        $target_id = (int) $request->input('target_id', 0);
        $optionString = $request->input('optionString', null);
        $arr = ['user_id'=>  $target_id, 'category_selects' => json_decode($optionString)];
        $userCourse = $this->courseOptionRepository->findBy('user_id', $target_id);
        if($userCourse) {
            $this->courseOptionRepository->update($userCourse->id, $arr);
        } else {
            $this->courseOptionRepository->create($arr);
        }

        return back();
    }
}
