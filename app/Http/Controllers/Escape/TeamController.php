<?php

namespace App\Http\Controllers\Escape;

use App\Models\Classes;
use App\Models\Record;
use App\Models\TeamRecord;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  Request  $request
     * @return View
     */
    public function setTeam(Request $request)
    {
        $user = session('user');
        $cp_id = $user->cp_id;
        $team_id = $request->input('team_id');
        $class_id = $request->input('class_id');

        $teams = Team::where('cp_id', $cp_id)->get();

        $classes = Classes::where('cp_id',$cp_id)->get();

        if($classes->count() > 0 && $class_id == null) {
            $class_id = $classes->first()->id;
        } else if($classes->count() == 0 && $class_id == null) {
            $class_id = 0;
        } else {
            $class_id = (int)$class_id;
        }
        $users = null;
        $arr = ['id','macAddr','key1','key2'];
        /*if($class_id === 0) {//The members belong no join to classes
            $users = User::where('cp_id',$user['cp_id'])
                ->join('answers as answers', 'responses.answer_id', '=', 'answers.id', 'left outer')
                ->whereNull('class_id')
                ->get();
        } else {//The members belong to classes
            $users = User::where('class_id',$class_id)->get();
        }*/




        if($team_id == null && $teams->count()>0) {
            $team_id = $teams->first()->id;
        } else if($team_id == null && $teams->count() == 0){
            $team_id = 0;
        } else {
            $team_id = (int) $team_id;
        }

        //該校所有成員
        /*$allMembers = DB::table('users')
            ->where('cp_id', $cp_id)
            ->join('team_users', 'users.id', '=', 'team_users.user_id')
            ->select('users.id','users.name','team_users.team_id')
            ->get();*/

        //該團隊已加入隊員
        $members = DB::table('team_users')
            ->where('team_id', $team_id)
            ->join('users','team_users.user_id','=', 'users.id')
            ->select('users.id','users.name','team_users.team_id')
            ->get();
        //該公司所有已加入團隊人員
        $adds = DB::table('team_users')
            ->where('team_users.cp_id', $cp_id)
            ->join('users','team_users.user_id','=', 'users.id')
            ->select('users.id','users.name','team_users.team_id')
            ->get();
        $arr = array();
        foreach ($adds as $member) {
            array_push($arr, $member->id);
        }

        $users = null;
        // 班級所有未加入成員
        if($class_id != 0) {
            $users = DB::table('users')
                ->where('cp_id', $cp_id)
                ->where('class_id', $class_id)
                ->whereNotIn('id', $arr)
                ->select('users.id', 'users.name')
                ->get();
        } else {
            $users = DB::table('users')
                ->where('cp_id', $cp_id)
                ->whereNull('class_id')
                ->whereNotIn('id', $arr)
                ->select('users.id', 'users.name')
                ->get();
        }


        return view('escape.editTeam', compact(['user','teams', 'team_id','classes','class_id', 'users', 'members']));
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editTeam(Request $request)
    {
        $user = session('user');
        $input = $request->all();

        //Check repeat team name
        $name = $input['name'];
        $result = Team::where('name', $name)->first();
        if($result != null) {
            return back()->withErrors([trans('team.team_exist')]);
        }


        $id = (int)$input['id'];
        //$teams = Team::all();
        $team = null;
        if($id>0)
            $team = Team::where('id', $id)->first();
        else {
            $team = new Team;
            $team->cp_id = $user['cp_id'];
        }
        $team->name = $input['name'];
        $team->save();
        if($id>0)
            return back();
        if($id==0)
            return redirect('escape/teams');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delTeam(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        TeamUser::where('team_id', $id)->delete();

        Team::where('id', $id)->delete();
        Record::where('team_id',$id)->delete();
        TeamRecord::where('team_id',$id)->delete();
        return redirect('escape/teams');
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editTeamUsers(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $team_id = $input['team_id'];
        if($team_id) $team_id = (int)$team_id;

        $remove_members = json_decode($input['remove_members']);
        $add_members = json_decode($input['add_members']);
        if(count($add_members) > 0) {
            foreach($add_members as $id) {
                $teamUser = new TeamUser();
                $teamUser->user_id = $id;
                $teamUser->team_id = $team_id;
                $teamUser->cp_id = $user['cp_id'];
                $teamUser->save();
            }
        }
        if(count($remove_members) > 0) {
            foreach ($remove_members as $id) {
                TeamUser::where('user_id', $id)->delete();
            }
        }


        return back();
    }
}
