<?php

namespace App\Http\Controllers\Node;

use App\Models\App;
use App\Models\Classes;
use App\Models\Cp as Cp;
use App\Models\Device;
use App\Models\Report;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminController extends Common4Controller
{
    /**
     * Display a listing of the classes and school.
     *
     * @param Request $request
     * @return View
     */
    public function setCp(Request $request)
    {
        $user = session('user');
        //$input = $request->all();
        $cp_id = $user['cp_id'];
        $roles = Role::where('dataset', 1)->get();
        $cps = Cp::where('role_id', 10)->get();
        $classes = Classes::where('cp_id', $cp_id)
            ->where('user_id', $user['id'])
            ->get();

        if($user['role_id'] == 10){
            return view('/nodes/setCp', compact(['cps', 'cp_id','user', 'roles','classes']));
        } else if($user['role_id'] == 8) {
            return view('/escape/setCp', compact(['cps', 'cp_id', 'user', 'roles','classes']));
        }
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editCp(Request $request)
    {
        $input = $request->all();
        unset($input['_token']);
        unset($input['_method']);
        $id = $input['id'];
        unset($input['id']);

        if($id>0)
            $cp = Cp::where('id', $id)->first();
        else {
            $cp = new Cp;

            $cp->cp_name = $request->cp_name;
            if($request->phone != null)
                $cp->phone = $request->phone;
            if($request->role_id != null)
                $cp->role_id = $request->role_id;
            if($request->address != null)
                $cp->address = $request->address;

            $cp->save();
        }
        $user = session('user');
        User::where('id', $user['id'])->update(['cp_id' => $cp->id]);
        $user['cp_id'] = $cp->id;
        session(['user'=>$user]);
        return back();
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editClass(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        unset($input['_token']);
        unset($input['_method']);
        $id = $input['id'];
        unset($input['id']);

        if($id>0)
            $class = Classes::where('id', $id)->first();
        else {
            $class = new Classes;
            $class->user_id = $user['id'];
            $class->class_name = $request->name;
            if($request->class_option != null)
                $class->class_option = $request->class_option;
            if($request->cp_id != null)
                $class->cp_id = $request->cp_id;
            $class->save();
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delClass(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        Classes::where('id', $id)->delete();
        User::where('class_id', $id)->delete();
        return back();
    }

    /**
    /**
     * Display a listing of the account.
     *
     * @param Request $request
     * @return View
     */
    public function accounts(Request $request)
    {
        $user = session('user');
        $id = $user['id'];
        $classes = Classes::where('cp_id', $user['cp_id'])
            ->where('user_id', $user['id'])
            ->get();
        $input = $request->all();

        $class_id = 0;
        if(array_key_exists('class_id', $input))
            $class_id = $input['class_id'];
        else if($classes->count()>0)
            $class_id = $classes[0]->id;
        $edit = 0;
        if(array_key_exists('edit', $input))
            $edit = $input['edit'];
        $users = User::where('class_id', $class_id)->get();
        $data = [
            'name_required' => trans('user.name_required'),
            'email_required' => trans('user.email_required'),
            'edit' => $edit,
        ];
        return view('/nodes/accounts', compact(['classes', 'class_id','user','users', 'data']));
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse |View
     */
    public function editAccount(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        unset($input['_token']);
        unset($input['_method']);
        $id = $input['id'];
        unset($input['id']);
        $email = $input['email'];

        if($id>0)
            $editUser = User::where('id', $id)->first();
        else {
            $checkUser = User::where('email', $email) -> get();
            if($checkUser->count()>0) {
                return redirect('node/accounts?edit=1')->withErrors(trans('user.email_exist'));
            }
            $editUser = new User;
            $editUser->email = $email;
            $editUser->password = bcrypt( $input['password']);
            $editUser->active = 1;
            $editUser->cp_id =  $input['cp_id'];
        }
        $editUser->name =  $input['name'];
        $editUser->role_id = $input['role_id'];
        $editUser->class_id = $input['class_id'];
        if( (int)$input['role_id'] == $user['role_id']) {
            $editUser-> class_id = null;
        }

        $editUser->save();

        return redirect('node/accounts');
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse |View
     */
    public function editBatchAccount(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $accountList = json_decode($input['accountStr']);
        $errorEmail = '';
        foreach ($accountList as $item) {
            $checkProduct = User::where('email', $item->email)->get();
            if($checkProduct->count() > 0) {
                if(strlen($errorEmail) == 0)
                    $errorEmail = $errorEmail.$item->email;
                else
                    $errorEmail = $errorEmail.','.$item->email;
            } else {
                $editUser = new User;
                $editUser->name = $item->name;
                $editUser->email = $item->email;
                $editUser->password = bcrypt( $input['password']);
                $editUser->active = 1;
                $editUser->cp_id =  $input['cp_id'];
                $editUser->role_id = $input['role_id'];
                $editUser->class_id = $input['class_id'];
                $editUser->save();
            }
        }
        if(strlen($errorEmail) == 0)
            return back();
        else
            $errorEmail = trans('user.email_exist_warning').' : '.$errorEmail;
        return back()->withErrors($errorEmail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delAccount(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $devices = Device::where('user_id', $id)->get();
        if($devices->count()>0) {
            foreach($devices as $item) {
                deleteAllByDevice($item);
            }
        }
        User::where('id', $id)->delete();

        return back();
    }
}

function deleteAllByDevice($device) {
    $apps = App::where('macAddr', $device->macAddr)->get();
    foreach ($apps as $app) {
        App::where('id', $app->id)->delete();
        Setting::where('app_id', $app->id)->delete();
    }
    Report::where('macAddr', $device->macAddr)->delete();
    $device->delete();
}


