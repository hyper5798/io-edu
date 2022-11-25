<?php

namespace App\Http\Controllers\Escape;

use App\Http\Controllers\Controller;
use App\Models\Cp;
use App\Models\Device;
use App\Models\GroupUser;
use App\Models\Mission;
use App\Models\Record;
use App\Models\Report;
use App\Models\Room;
use App\Models\Game;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Repositories\MemberRepository;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
//use Illuminate\Http\RedirectResponse;
use App\Models\TeamRecord;
use Illuminate\View\View;
use Redis;

class EscapeController extends Controller
{
    private $memberRepository;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return RedirectResponse | View
     */
    public function admin(Request $request)
    {
        //透過team_id 找到 team 所有隊員
        //$users= Team::find(1)->users()->where('room_id',2)->get();
        $user = session('user');
        /* Get team data in admin -----------------------------------------*/
        //1.Get room id

        $redis = app('redis.connection');
        $cp_id = $request->input('cp_id');
        if($cp_id == null) {
            $cp_id = $user->cp_id;
        } else {
            $cp_id = (int)$cp_id;
        }

        $cps = null;
        if($user->role_id < 3) {//Super Admin
            $cps = Cp::all();
        } else {
            $cps = Cp::where('id', $cp_id)->get();
        }

        $room_id = $request->input('room_id');
        $rooms = null;
        if ($user->role_id > 7) {//Group user
            //Get rooms of group user
            $rooms = $this->memberRepository->getGroupRoomByUserId($user['id']);
        } else {//Admin user
            //Get rooms of the company
            $rooms = Room::where('work', 'demo')->get();
        }

        if(count($rooms) == 0){
            return redirect('/');
        }

        if($room_id == null) {
            $room_id = $rooms->first()->id;
        }

        $key = 'room'.$room_id;
        $all = $redis->hgetall($key);
        $mission_status = null;
        $mac_status = null;
        $mode = 30;

        if(array_key_exists('mission_status', $all)) {
            $mission_status = json_decode($all['mission_status']);
        }
        if(array_key_exists('mac_status', $all)) {
            $mac_status = json_decode($all['mac_status']);
        }
        if(array_key_exists('mode', $all)) {
            $mode = (int)$all['mode'];
        }

        //2.Get user Collection
        $room  = Room::find($room_id);
        $param = 0;

        $missions = Mission::where('room_id', $room->id)
            ->select('mission_name', 'sequence', 'macAddr')
            ->orderBy('sequence', 'asc')
            ->get();
        //Room missions
        foreach ($missions as $mission) {
            $mac = $mission->macAddr;
            $name = $mission->mission_name;

            if ( $mission_status != null) {
                if(array_key_exists($name, $mission_status)) {
                    $mission->mission_status =  $mission_status->$name;
                }
            }
            if ( $mac_status != null) {
                if(array_key_exists($mac, $mac_status)) {
                    $mission->mac_status =  $mac_status->$mac;
                }
            }

            if(isset($mission->mac_status) == false) {
                $report = Report::where('macAddr', $mission->macAddr)
                    ->whereIn('key1',[1,2])
                    ->latest('recv')->first();
                if($report)
                    $mission->status = $report->key1;
                else
                    $mission->status = '';
            }

            //Check device is online or not
            $report = Report::where('macAddr', $mac)
                ->where('key1', 0)
                ->latest('recv')->first();
            if ($report)
                $mission->recv = $report->recv;
            else
                $mission->recv = '';
        }
        //Security devices
        $devices = Device::where('type_id', 98) //type_id for device type
        ->where('setting_id', $room->id) //setting_id for room classification
        ->select('id', 'device_name', 'macAddr')
            ->get();
        foreach ( $devices as $device) {

            $report = Report::where('macAddr', $device->macAddr)
                ->latest('recv')->first();
            if($report != null) {
                $device->recv = $report->recv;
                $device->status = $report->key1;
            } else {
                $device->recv = '';
                $device->status = null;
            }

        }
        $uri = $request->path();
        $data = [
            'mode' => $mode,
            'connection' => trans('escape.connection'),
            'disconnection' => trans('escape.disconnection'),
            'open_door' => trans('escape.open_door'),
            'close_door' => trans('escape.close_door'),
            'security_event' => trans('escape.security_event'),
            'security' => trans('escape.security'),
            'mission_start' => trans('escape.mission_start'),
            'mission_end' => trans('escape.mission_end'),
            'change_password' => trans('escape.change_password'),
            'emergency_button' => trans('escape.emergency_button'),
            'timeout_failure' => trans('escape.timeout_failure'),
            'game_mode' => trans('escape.game_mode'),
            'demo_mode' => trans('escape.demo_mode'),
            'security_mode' => trans('escape.security_mode'),
            'sensing' => trans('escape.sensing'),
            'security_reset' => trans('escape.security_reset'),
            'reset' => trans('escape.reset'),
            'token' => $user->remember_token
        ];
        return view('escape.admin', compact(['uri', 'user', 'rooms', 'room', 'missions','devices','data', 'room_id', 'cps', 'cp_id']));
    }

    public function personal(Request $request)
    {
        $link = request()->cookie('link');
        /*if($link) {
            dd($link);
        }*/
        $redis = app('redis.connection');
        //$redis->hset('library', 'predis', '12345'); // 存储 key 为 library， 值为 predis 的记录；
        //$test = $redis->hget('library', 'predis');

        $user = session('user');

        /* Get team data in admin -----------------------------------------*/


        //1.Check user role
        $teamUser = null;
        $team = null;
        $rooms = null;

        //1.Get room id
        $room_id = $request->input('room_id');
        if ($user->role_id > 7) {//Group user
            //Get rooms of group user
            $rooms = $this->memberRepository->getGroupRoomByUserId($user['id']);
        } else {//Admin user
            //Get rooms of the company
            $rooms = Room::where('work', 'demo')->get();
        }

        if(count($rooms) == 0){
            return redirect('/escape/setRoom?cp_id='.$cp_id);
        }

        if($room_id == null)
            $room_id = $rooms->first()->id;
        else
            $room_id = (int)$room_id;
        $roomkey = 'room'.$room_id;
        $start_time = $redis->hget($roomkey, 'start');
        $status = $redis->hget($roomkey, 'status');
        $sequence = $redis->hget($roomkey, 'sequence');
        $reduce = $redis->hget($roomkey, 'reduce');
        $team_id = 0;

        $team_user = TeamUser::where('user_id', $user['id'])->first();
        //$team_id = $redis->hget($roomkey, 'team_id');
        if($team_user != null)  {
            $team_id = $team_user->team_id;
        }


        if($status == null) $status = 0;
        if($sequence == null) $sequence = 0;
        if($reduce == null) $reduce = 0;
        if($team_id != null) {
            $team_id = (int)$team_id;
            if($team_id>0)
                $team = Team::where('id',$team_id)->first();
        }
        //------------------ team obj ------------------------------
        $teamObj = $redis->hgetall('team'.$team_id);
        //$Game : pass record include start, end, diff
        $game = null;
        $records = null;
        if($teamObj != null) {
            if (array_key_exists('game', $teamObj)) {
                $game =  json_decode($teamObj['game']);
            }
        } else {
            $team_record = TeamRecord::where('team_id',$team_id)
                ->orderBy('id', 'desc')->first();
            if($team_record != null) {
                $records = Record::where('team_record_id',$team_record->id)->get();
            }
        }

        /* Add page control for personal -----------------------------------------*/

        $room = Room::find($room_id);
        $param = 0;
        $missions = Mission::where('room_id', $room->id)
            ->where(function ($query) use ($param) {
                $query->where('sequence', '>', $param);
            })->select('id','mission_name', 'sequence', 'macAddr', 'pass_time')
            ->orderBy('sequence', 'asc')
            ->get();

        foreach ($missions as $mission) {
            if($game != null) {
                if(property_exists($game, $mission->macAddr)){
                    $mac = $mission->macAddr;
                    $obj = $game->$mac;
                    if(property_exists($obj, 'start')){
                        $mission->start_at = $obj->start;
                    } else {
                        $mission->start_at = '';
                    }
                    if(property_exists($obj, 'end')){
                        $mission->end_at = $obj->end;
                    } else {
                        $mission->end_at = '';
                    }
                    if(property_exists($obj, 'diff')){
                        $mission->time = $obj->diff;
                    } else {
                        $mission->time = '';
                    }
                } else {
                    $mission->start_at = '';
                    $mission->end_at = '';
                    $mission->time = '';
                }
            } else if($records!=null && $records->count() > 0) {
                $isChange = false;
                foreach ($records as $record) {
                    if($mission->id == $record->mission_id) {
                        $mission->start_at = $record->start_at;
                        $mission->end_at = $record->end_at;
                        $mission->time =  $record->time;
                        $isChange = true;
                        break;
                    }
                }
                if($isChange == false) {
                    $mission->start_at = '';
                    $mission->end_at = '';
                    $mission->time = '';
                }
            } else {
                $mission->start_at = '';
                $mission->end_at = '';
                $mission->time = '';
            }
        }

        $uri = $request->path();
        return view('escape.personal', compact(['uri','team', 'user', 'rooms', 'room','missions', 'start_time','status','sequence','team_id', 'room_id']));
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editTeamName(Request $request)
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
        $team = Team::where('id', $id)->first();
        $team->name = $input['name'];
        $team->save();

        return back();
    }

    public function temp(Request $request)
    {
        $user = session('user');
        return view('escape.temp', compact(['user']));
    }

    public function getMyTime($value) {
        $m = intval(floatval($value)/ 60 );
        $s = $value % 60;
        return $m . '分:' . $s .'秒';
    }
}