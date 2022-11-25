<?php

namespace App\Http\Controllers\Admin;

//use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use App\Models\Cp as Cp;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends CommonController
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $user = session('user')->toArray();
        if($request['role_id'] != null)
            $role_id = $request['role_id'];
        else
            $role_id = $user['role_id'];
        if($user['role_id'] !=1){
            $message = trans('auth.permission_denied',  ['name' => 'Super Admin']);
            return redirect('/login')->with('message', $message );
        }
        $roles = Role::where('dataset', 1)->get();
        $cps = null;
        if($role_id === 1) {
            $cps = Cp::all();
        } else {
            $cps = Cp:: where('role_id', $role_id)->get();
        }

        return view('pages.cps', compact(['cps', 'roles', 'role_id']));
    }

    public function update(Request $request)
    {
        $input = $request->all();
        unset($input['_token']);
        unset($input['_method']);
        $id = $input['id'];
        unset($input['id']);

        if($id>0)
            Cp::where('id', $id)->update($input);
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
        return redirect('/admin/cps');
    }

    public function destroy(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        unset($input['id']);
        Cp::where('id', $id)->delete();
        return redirect('/admin/cps');
    }
}
