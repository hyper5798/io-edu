<?php

namespace App\Http\Controllers\Admin;

use App\Models\Log;
use Illuminate\View\View;

class LogController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $user = session('user');
        $logs = Log::all();
        return view('pages.logs', compact(['user','logs']));
    }

    /**
    * Display a listing of the resource.
    *
    * @return View
    */
    public function deleteLogs()
    {
        $user = session('user');
        $logs = Log::truncate();
        return back();
    }


}
