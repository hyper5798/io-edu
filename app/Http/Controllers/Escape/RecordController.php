<?php

namespace App\Http\Controllers\Escape;

use App\Http\Controllers\Controller;
use App\Models\Cp;
use App\Models\Game;
use App\Models\Mission;
use App\Models\Record;
use App\Models\Room;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
//use Illuminate\Http\RedirectResponse;
use App\Models\TeamRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function rank(Request $request)
    {
        $rooms = Room::all();
        $room_id = $request->input('room_id');
        $cp_id = $request->input('cp_id');
        $rank_tab = $request->input('rank_tab');
        $page = $request->input('page');
        $type = $request->input('type');
        $year = $request->input('year');
        $range = $request->input('range');
        $cps = Cp::all();
        if($cp_id === null) {
            $cp_id = $cps->first()->id;;
        }
        if($room_id === null) {
            $room_id = $rooms->first()->id;;
        }
        if($rank_tab === null) {
            $rank_tab = 1;;
        } else {
            $rank_tab = (int)$rank_tab;
        }
        if($page === null) {
            $page = 1;
        } else {
            $page = (int)$page;
        }
        if($type === null) {
            $type = 3;
        } else {
            $type = (int)$type;
        }
        if ($year === null) {
            $year = date('Y');
        }
        if($range === null) {
            $range = (int)date('m');;
        } else {
            $range = (int)$range;
        }

        $from = null;
        $to = null;
        if($type == 1) {
            $fromStr = $year.'-01-01';
            $toStr = ($year+1).'-01-01';
        }
        if($type == 2) {
            if($range == 1) {
                $fromStr = $year.'-01-01';
                $toStr = $year.'-04-01';
            } else if($range == 2) {
                $fromStr = $year.'-04-01';
                $toStr = $year.'-07-01';
            } else if($range == 3) {
                $fromStr = $year.'-07-01';
                $toStr = $year.'-10-01';
            } else if($range == 4) {
                $fromStr = $year.'-10-01';
                $toStr = ($year+1).'-01-01';
            }

        }
        if($type == 3) {
            $end = $range +1;
            $fromStr = $year.'-'.$range.'-01';
            if($end>12) {
                $toStr = ($year+1).'-01-01';
            } else {
                $toStr = $year . '-' . $end . '-01';
            }
        }
        $from = date($fromStr);
        $to = date($toStr);

        $limit = 100;
        $skip = ($page-1)*$limit;

        $user = session('user');
        $arr = ['team_records.id','name','total','start','end','status', 'sequence','reduce','cps.cp_name'];
        $records = null;
        if($rank_tab === 1) {
            $records = DB::table('team_records')
                ->join('teams', 'team_records.team_id', '=', 'teams.id')
                ->join('cps', 'team_records.cp_id', '=', 'cps.id')
                ->whereBetween('start', [$from, $to])
                ->where('team_records.status', 3)
                ->skip($skip)
                ->take($limit)
                ->orderBy('total', 'DESC')
                ->get($arr);

            //dd($fromStr,$toStr, $test);
        } else if($rank_tab === 2) {
            $records = DB::table('team_records')
                ->join('teams', 'team_records.team_id', '=', 'teams.id')
                ->join('cps', 'team_records.cp_id', '=', 'cps.id')
                ->whereBetween('start', [$from, $to])
                ->where('team_records.cp_id', $user->cp_id)
                ->where('status', 3)
                ->skip($skip)
                ->take($limit)
                ->orderBy('total', 'DESC')
                ->get($arr);
        }  else if($rank_tab === 3) {
            $records = DB::table('team_records')
                ->join('teams', 'team_records.team_id', '=', 'teams.id')
                ->join('cps', 'team_records.cp_id', '=', 'cps.id')
                ->whereBetween('start', [$from, $to])
                ->where('status', 6)
                ->skip($skip)
                ->take($limit)
                ->orderBy('total', 'DESC')
                ->get($arr);
        }   else if($rank_tab === 4) {
            $records = DB::table('team_records')
                ->join('teams', 'team_records.team_id', '=', 'teams.id')
                ->join('cps', 'team_records.cp_id', '=', 'cps.id')
                ->whereBetween('start', [$from, $to])
                ->where('status', 4)
                ->skip($skip)
                ->take($limit)
                ->orderBy('total', 'DESC')
                ->get($arr);
        }
        $search = [
            'type' => $type,
            'year' => $year,
            'range' => $range,
        ];


        $uri = $request->path();
        return view('escape.rank', compact(['cps','cp_id','rooms','room_id','records','user','uri', 'page','limit', 'rank_tab','search']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delRecord(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        TeamRecord::where('id', $id)->delete();
        Record::where('team_record_id', $id)->delete();
        return back();
    }

    public function teamRecords(Request $request)
    {
        $rooms = Room::all();
        $room_id = $request->input('room_id');
        $page = $request->input('page');
        $search = $request->input('search');

        if($room_id === null) {
            $room_id = $rooms->first()->id;;
        }

        if($page === null) {
            $page = 1;
        } else {
            $page = (int)$page;
        }
        $limit = 100;
        $skip = ($page-1)*$limit;
        $ids = array();
        if($search != null && $search != '') {
            $teams = Team::Where('name', 'like', '%' . $search . '%')->get();
            //dd($teams->count());
            for ( $i=0 ; $i< $teams->count() ; $i++ ) {
                $ids[$i] = $teams[$i]->id;
            }
        }

        $arr = ['team_records.id','name','total','start','end','status', 'sequence','reduce','cps.cp_name'];

        $records = DB::table('team_records')
            ->join('teams', 'team_records.team_id', '=', 'teams.id')
            ->join('cps', 'team_records.cp_id', '=', 'cps.id')
            ->whereIn('team_id', $ids)
            ->skip($skip)
            ->take($limit)
            ->orderBy('end', 'DESC')
            ->get($arr);

        $limit = 100;
        $skip = ($page-1)*$limit;

        $user = session('user');

        $uri = $request->path();
        return view('escape.teamRecords', compact(['rooms','room_id','records','user','uri', 'page','limit', 'search']));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function roomRecord(Request $request)
    {
        $user = session('user');
        /* Get team data in admin -----------------------------------------*/
        //1.Get room id

        $from = $request->input('from');
        $page = $request->input('page');
        $team_record_id = $request->input('team_record_id');
        if($team_record_id != null)
            $team_record_id = (int)$team_record_id;
        $team_record = TeamRecord::find($team_record_id);
        $from = (int)$from;
        $page = (int)$page;
        if($team_record != null)
            $room_id = $team_record->room_id;

        $start_time = $team_record->start;
        $status = $team_record->status;
        $sequence= $team_record->sequence;
        $reduce= $team_record->reduce;
        if($status == null) $status = 0;
        if($sequence == null) $sequence = 0;
        if($reduce == null) $reduce = 0;

        //2.Get rank of team record in local and cp
        $team = null;
        if($team_record != null) {
            $team_id = (int)($team_record->team_id);
            $team = Team::find($team_id);
            $team['total'] = $this->getMyTime($team_record->total);
        }
        $team_users = TeamUser::where('team_id', $team_id)->get()->toArray();
        $users = User::query()
            ->select(['id', 'name'])
            ->whereIn('id', array_column($team_users, 'user_id'))
            ->get();


        if($from < 3) { //3 emergency stop, 4:timeout failure
            $type = $request->input('type');
            $year = $request->input('year');
            $range = $request->input('range');
            if($type === null) {
                $type = 3;
            } else {
                $type = (int)$type;
            }
            if ($year === null) {
                $year = date('Y');
            }
            if($range === null) {
                $range = (int)date('m');;
            } else {
                $range = (int)$range;
            }

            $from = null;
            $to = null;
            if($type == 1) {
                $fromStr = $year.'-01-01';
                $toStr = ($year+1).'-01-01';
            }
            if($type == 2) {
                if($range == 1) {
                    $fromStr = $year.'-01-01';
                    $toStr = $year.'-04-01';
                } else if($range == 2) {
                    $fromStr = $year.'-04-01';
                    $toStr = $year.'-07-01';
                } else if($range == 3) {
                    $fromStr = $year.'-07-01';
                    $toStr = $year.'-10-01';
                } else if($range == 4) {
                    $fromStr = $year.'-10-01';
                    $toStr = ($year+1).'-01-01';
                }

            }
            if($type == 3) {
                $end = $range +1;
                $fromStr = $year.'-'.$range.'-01';
                if($end>12) {
                    $toStr = ($year+1).'-01-01';
                } else {
                    $toStr = $year . '-' . $end . '-01';
                }
            }
            $from = date($fromStr);
            $to = date($toStr);
            //3.Get local record
            $localRecords = TeamRecord::where('room_id',$room_id)
                ->whereBetween('start', [$from, $to])
                ->where('status',3)
                ->orderBy('total', 'DESC')
                ->get();
            $index = 1;
            foreach ($localRecords as $record) {
                if($record->id == $team_record_id) {
                    //Add time & score to team attributes

                    break;
                }
                $index++;
            }
            $team['local_rank'] = $index;


            //5.Get cp record
            $cpRecords = null;

            $cpRecords = TeamRecord::where('room_id',$room_id)
                ->whereBetween('start', [$from, $to])
                ->where('cp_id',$team->cp_id)
                ->where('status',3)
                ->orderBy('total', 'DESC')
                ->get();

            $index = 1;
            foreach ($cpRecords as $record) {
                if($record->id == $team_record_id) {
                    break;
                }
                $index++;
            }
            $team['cp_rank'] = $index;
            //dd($index, $localRecords, $cpRecords, $team);
        } else {
            $type = 0;
            $year = 0;
            $range = 0;
            $team['local_rank'] = '';
            $team['cp_rank'] = '';
        }
        //Get mission record of team
        $records = Record::where('team_record_id', $team_record_id) ->get();
        $mArr = array();
        $tArr = array();
        foreach ($records as $record){
            $mArr[$record->mission_id] = $record;
            $tArr[$record->mission_id] = $this->getMyTime($record->time);
        }

        //Get room & mission
        //$rooms = Room::where('cp_id', $user->cp_id)->get();
        $rooms =  Room::all();
        $room = array();
        if(count($rooms) > 0){
            $room = $rooms[0];
        }
        $games = Game::where('room_id', $room->id)->get(['id', 'game_name']);
        $param = 0;
        $missions = Mission::where('room_id', $room->id)
            ->where(function ($query) use ($param) {
                $query->where('sequence', '>', $param);
            })->select('id','mission_name', 'sequence', 'macAddr')
            ->orderBy('sequence', 'asc')
            ->get();

        foreach ($missions as $mission) {
            if($sequence >= $mission->sequence) {
                $mid = $mission->id;
                $tmp = null;
                $start = null;
                $end = null;

                if(array_key_exists($mid , $tArr))
                    $mission->time = $tArr[$mid];
                else
                    $mission->time = 0;
                if(array_key_exists($mid , $mArr)) {
                    $tmp = $mArr[$mid];
                    $start = $tmp->start_at;
                    $end = $tmp->end_at;
                }


                if($start)
                    $mission->start_at = $start;
                else
                    $mission->start_at = '';
                if($end)
                    $mission->end_at = $end;
                else
                    $mission->end_at = '';
            } else {
                $mission->start_at = '';
                $mission->end_at = '';
            }
        }

        $uri = $request->path();
        return view('escape.roomRecord', compact(['uri','team', 'user', 'rooms', 'room','missions', 'start_time','status','sequence','reduce', 'from','page','type','year','range','users']));
    }

    public function getMyTime($value) {
        $m = intval(floatval($value)/ 60 );
        $s = $value % 60;
        return $m . '分:' . $s .'秒';
    }
}

