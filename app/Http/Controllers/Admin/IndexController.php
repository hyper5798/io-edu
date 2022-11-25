<?php

namespace App\Http\Controllers\Admin;

use App\Models\Classes;
use App\Models\Cp;
use App\Models\Device;
use App\Models\Field;
use App\Models\Product;
use App\Models\Report;
use App\Models\Role;
use App\Models\Team;
use App\Models\Type;
use App\Models\User;
use App\Services\QuestionService;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cookie;


class IndexController extends CommonController
{
    private $questionService;

    public function __construct(
        QuestionService $questionService
    )
    {
        $this->questionService = $questionService;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function index()
    {
        //Home
        /*$cps = Cp::where('id', '>', 1)->get();
        $arr = array();
        for($i=0;$i<count($cps);$i++) {
            $arr[$cps[$i]->id] = $rooms = Room::where('cp_id', $cps[$i]->id)->get();
        }

        return view('pages.index', compact(['cps', 'arr']));*/
        $path = '/login?link=develop';
        //$path = '/';
        return redirect($path);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function backend()
    {
        //Home
        $user = session('user');
        $profile = User::find($user['id'])->profile;
        if($profile && $profile->image_url) {
            $user->image_url = Storage::url($profile->image_url);
            session(['user' => $user]);
        }
        if(gettype($user) != 'object') {
            return redirect('/login');
        }
        if($user->role_id == 10 || $user->role_id == 7 ) {
            return redirect('/escape/admin');
        } else if($user->role_id == 9 || $user->role_id == 8 ) {
            return redirect('/escape/personal');
            /*} else if($user->role_id == 10 || $user->role_id == 11 ) {
                return redirect('/node/myDevices');*/
        }
        $server_software = $_SERVER['SERVER_SOFTWARE'];
        $fields = $this->questionService->getFieldsWithoutAll();
        $fields =  $this->questionService->fieldWithLevelGroup($fields);
        //$fields = $this->questionService

        $data = [
            'cp_count' => Cp::all()->count(),
            'user_count' => User::all()->count(),
            'role_count' => Role::all()->count(),
            'class_count' => Classes::all()->count(),
            'team_count' => Team::all()->count(),
            'product_count' => Product::all()->count(),
            'type_count' => Type::all()->count(),
            'device_count' => Device::all()->count(),
            'os_value' => PHP_OS,
            'env_value' => $server_software,
            'ver_value' => 'v-0.1',
            'upload_limit_value' => get_cfg_var('upload_max_filesize') ? get_cfg_var('upload_max_filesize') : '不允許上傳檔案',
            'zone_time_value' => date('Y年m月d日 H時i分s秒'),
            'server_domain_value' => $_SERVER['SERVER_NAME'],
        ];
        $mutable = Carbon::now();
        $modifiedMutable = $mutable->add(-1, 'day');
        $type = Type::where('type_id',99)->first();
        $reports = Report::where('type_id',99)
            ->where('recv','>=',$modifiedMutable)
            ->orderBy('recv', 'asc')
            ->get();
        return view('pages.backend', compact(['data', 'type' ,'reports', 'fields']));
    }

    /**
     * Change password of user.
     *
     * @param Request $request
     * @return RedirectResponse | View
     */
    public function pass(Request $request)
    {
        //Change
        $input = $request->all();
        $user = session('user');
        if (count($input)>2) {
            $code = (int)$request->input('code', 1);
            $rules = [
                'new_pass' => 'required|between:6,20|confirmed',
                'new_pass_confirmation' => 'required',
            ];

            $msg = [
                'new_pass.required' => trans('passwords.new_pass_required'),
                'new_pass_confirmation.required' => trans('passwords.confirmation_required'),
                'new_pass.between' => trans('passwords.pass_between'),
                'new_pass.confirmed' => trans('passwords.pass_confirmed_different'),
            ];
            /*if($code) {
                $rules['old_pass'] = 'required';
                $msg['old_pass.required'] = trans('passwords.old_pass_required');
            }*/
            $validator = Validator::make($input, $rules, $msg);

            if(count($validator->errors()->all()) > 0){
                session(['error'=> $validator->errors()]);
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $hashedPassword = $user['password'];
            /*if ($code && !Hash::check($input['old_pass'], $hashedPassword))
            {
                return back()
                    ->withErrors([trans('passwords.old_code_error')])
                    ->withInput();
            }*/
            $this->updatePass($user['id'], $input['new_pass'] );
            Cookie::queue('token', null);
            return redirect('login')->withErrors([trans('passwords.pass_updated_ok')]);
        } else {
            $code = (array_key_exists('code',$input) && $input['code'] == 'false') ? 0 : 1;

            if($input['page'] == 'admin') {
                return view('pages.password',compact(['user']));
            } else if($input['page'] == 'escape') {
                return view('escape.password',compact(['user']));
            } else if($input['page'] == 'node') {
                return view('nodes.password',compact(['user']));
            }
        }
    }



    /**
     * Update user password method.
     *
     * @param int $id
     * @param string $form_password
     * @return bool
     */
    private function updatePass($id, $form_password)
    {
        try {
            //$encode = Crypt::encrypt($form_password);
            $encode = Hash::make($form_password);
        } catch (EncryptException $err) {
            echo $err;
            return false;
        }
        DB::table('users')
            ->where('id', $id)
            ->update(['password' => $encode]);
        return true;
    }
}
