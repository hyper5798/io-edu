<?php

namespace App\Http\Controllers\Admin;

use App\Constant\UserConstant;
use App\Models\Announce;
use App\Repositories\AnnounceRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AnnounceController extends CommonController
{
    private $announceRepository;

    public function __construct(
        AnnounceRepository $announceRepository
    )
    {
        $this->announceRepository = $announceRepository;

    }
    /**
     * Display a listing of the resource.
     *
     * @return view
     */
    public function index(Request $request)
    {
        $tags = UserConstant::ANNOUNCE_TAG;
        $announces = $this->announceRepository->all();
        return view('pages.announces',compact(['announces', 'tags']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|view
     */
    public function create(Request $request)
    {
        $tags = UserConstant::ANNOUNCE_TAG;
        return view('pages.announce-create',compact(['tags']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $this->announceRepository->create($input);
        return redirect ('/admin/announce');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Announce $announce)
    {
        //dd($answerCheck);
        $tags = UserConstant::ANNOUNCE_TAG;
        return view('pages.announce-edit',compact(['announce', 'tags']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $this->announceRepository->update($id, $input);
        return back ();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $this->announceRepository->destroy($id);
        return redirect(route('admin.announce.index'));
    }
}
