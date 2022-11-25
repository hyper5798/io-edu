<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cp as Cp;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Type;
use Illuminate\View\View;

class RoleController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $role_id = $request['role_id'];
        $roles =  Role::all();
        if($roles->count() > 0 && $role_id ==null ){
            $role_id = $roles[0]['role_id'] ;
        }
        $role_id = (int)$role_id;

        return view('pages.roles', compact(['roles', 'role_id']));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function update(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        if($id>0)
            $role = Role::find($id);
        else {
            $role = new Role;
        }
        $role->role_name = $request->role_name;
        $role->role_id = $request->role_id;
        $role->dataset = $request->dataset;
        $role->save();
        return back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function destroy(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        Role::where('id', $id)->delete();
        return back();
    }
}
