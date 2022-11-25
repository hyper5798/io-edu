<?php

namespace App\Http\Controllers\Admin;

// use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use App\Models\Classes;
use App\Models\ClassOption;
use Illuminate\View\View;

class ClassController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $classes = Classes::all();
        $classOptions = ClassOption::all();
        return view('pages.classes', compact(['classes', 'classOptions']));
    }
}
