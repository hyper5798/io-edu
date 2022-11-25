<?php

namespace App\Http\Controllers\Learn;

//use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Common3Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $data = [
            'title' => '架設中',
            'change_password' => '變更密碼',
            'log' => '日誌',
            'logout' => '登出',

        ];
        return view('pages.work', $data);
    }
}
