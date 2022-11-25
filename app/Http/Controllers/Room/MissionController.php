<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Cp;
use App\Models\Device;
use App\Models\DeviceMission;
use App\Models\Mission;
use App\Models\Network;
use App\Models\Node;
use App\Models\Profile;
use App\Models\Room;
use App\Models\Script;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;

class MissionController extends Controller
{
    public function setRoom(Request $request)
    {
        $user = session('user');
        //Jason add for record current url on 2022/5/20
        $target = session('target');
        $target['url'] = url()->full();
        session(['target' => $target]);
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
        $rooms = Room::where('cp_id',$cp_id)->get();
        $room = null;
        //dd($user->room_id, $user->role_id);
        if(count($rooms) > 0 && $room_id == null ) {
            $room_id = $rooms[0]->id;
        } else {


            if($room_id != null) {
                $room_id = (int)$room_id;
                $room = Room::where('id', $room_id)->first();
            }

            if($room==null && count($rooms) > 0){
                $room = $rooms[0];
                $room_id = $room->id;
            }
        }

        $target = $request->input('target');

        if($target == null) {
            $target = session('option');
            if($target == 3)
                $target = 1;
        }

        if($target == null) {
            $target = 1;
        } else {
            $target = (int)$target;
        }

        $param = 0;
        //大門任務
        $emergency = null;
        if($room_id != null) {
            $emergency = Mission::where('room_id', $room_id)
                ->where('sequence', 0)->first();
        }

        $messages = array(
            'mission_name_required' => trans('escape.mission_name_required'),
            'device_required' => trans('escape.device_required'),
            'sequence_not_change' => trans('escape.sequence_not_change'),
        );

        if($emergency != null) {
            $data = [
                'target' => $target,
                'mac' => $emergency->macAddr,
                'device_id' => $emergency->device_id,
            ];
        } else {
            $data = [
                'target' => $target,
                'mac' => '',
                'device_id' => '',
            ];
        }
        if($room_id == null) {
            $room_id = 0;
        }
        $missions = Mission::where('room_id', $room_id)
            ->where(function ($query) use ($param) {
                $query->where('sequence', '>', $param);
            })->select('id','mission_name', 'sequence', 'macAddr','device_id')
            ->orderBy('sequence', 'asc')
            ->get();

        $misArr = $missions->toArray();
        $macArr = array_column($misArr, 'macAddr');

        $scripts = Script::where('room_id', $room_id)
            ->orderBy('mission_id', 'asc')
            ->get();
        //dd($scripts);
        $devices = Device::whereIn('macAddr', $macArr)
            ->get();
        //Remove mission_id from edit script
        $user->mission_id = null;;
        session(['user'=>$user]);

        return view('room.setRoom', compact(['user', 'rooms', 'room_id','missions', 'devices', 'data', 'messages', 'cps', 'cp_id']));
    }
    /**
     * Display a listing of the resource.
     * @param  Request  $request
     * @return View
     */
    public function setMission(Request $request)
    {
        $user = session('user');
        //Jason add for record current url on 2022/5/20
        $target = session('target');
        $target['url'] = url()->full();
        session(['target' => $target]);
        $mission_id = null;
        if(isset($user['mission_id'])) {
            $mission_id = $user['mission_id'];
        }
        $cp_id = $request->input('cp_id');
        if($cp_id == null) {
            $cp_id = $user->cp_id;
        } else {
            $cp_id = (int)$cp_id;
        }
        $rooms = Room::where('cp_id',$cp_id)->get();
        $room = null;
        $room_id = $request->input('room_id');
        if($room_id != null) {
            $room = Room::where('id', $room_id)->first();
        }

        if($room==null && count($rooms) > 0){
            $room = $rooms[0];
            $room_id = $room->id;
        }
        $target = $request->input('target');

        if($target == null) {
            $target = session('option');
            if($target == 3)
                $target = 1;
        }

        if($target == null) {
            $target = 1;
        } else {
            $target = (int)$target;
        }

        $param = 0;
        $emergency = null;
        if($room_id != null) {
            $emergency = Mission::where('room_id', $room->id)
                ->where('sequence', 0)->first();
        }
        $messages = array(
            'mission_name_required' => trans('escape.mission_name_required'),
            'device_required' => trans('escape.device_required'),
            'script_name_required' => trans('escape.script_name_required'),
            'script_content_required' => trans('escape.script_content_required'),
            'pass_key_required' => trans('escape.pass_key_required'),
            'pass_value_required' => trans('escape.pass_value_required'),
            'sequence_not_change' => trans('escape.sequence_not_change'),
        );

        $data = [
            'target' => $target,
            'room_id' => $room_id,
            'room_name' => $room->room_name,
        ];

        $missions = Mission::where('room_id', $room_id)
            ->select('id','mission_name', 'sequence', 'macAddr','device_id', 'pass_time')
            ->orderBy('sequence', 'asc')
            ->get();
        if($mission_id == null && $missions->count()>0) {
            $mission_id = $missions[0]->id;
        } else if($mission_id == null && $missions->count()==0) {
            $mission_id = 0;
        }
        $arr = array();
        foreach ($missions as $item) {
            array_push($arr, $item->macAddr);
        }

        $scripts = Script::where('room_id', $room_id)
            ->orderBy('mission_id', 'asc')
            ->get();
        //dd($scripts);
        //帳戶下所有裝置
        $devices = Device::where('cp_id', $cp_id)
            ->whereBetween('type_id', [99,150])
            ->get();
        //未加入任務裝置
        $available = Device::where('cp_id', $cp_id)
            ->whereBetween('type_id', [99,255])
            ->whereNotIn('macAddr', $arr)
            ->get();

        return view('room.setMission', compact(['user', 'room_id','missions', 'devices', 'data', 'scripts', 'messages','available','mission_id', 'cp_id']));
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editRoom(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $target = $input['target'];
        if($target == null) {
            session(['option' => 1]);
        } else {
            session(['option' => (int)$target]);
        }
        $cp_id = $request->input('cp_id');
        if($cp_id == null) {
            $cp_id = $user->cp_id;
        } else {
            $cp_id = (int)$cp_id;
        }
        unset($input['_token']);
        unset($input['_method']);
        $id = (int)$input['id'];
        unset($input['id']);
        $user = session('user');
        $rooms = Room::all();
        if($id>0)
            $room = Room::where('id', $id)->first();
        else {
            $room = new Room;
            $room->cp_id = $cp_id;
            $room->user_id = $user['id'];
        }
        if(array_key_exists('room_name', $input))
            $room->room_name = $input['room_name'];
        if(array_key_exists('pass_time', $input))
            $room->pass_time = (int)$input['pass_time'];

        if(array_key_exists('room_work', $input))
            $room->work = $input['room_work'];

        if(array_key_exists('room_type', $input))
            $room->type = $input['room_type'];
        if(array_key_exists('isSale', $input) && $input['isSale'] == 'on') {
            $room->isSale = 1;
        } else {
            $room->isSale = 0;
        }

        $room->save();
        if($input['mac'] != null) {
            $emergency = Mission::where('room_id', $room->id)
                ->where('sequence', 0)->first();
            if($emergency == null) {
                $emergency = new Mission;
                $emergency-> mission_name = 'emergency'.$room->id;
                $emergency->room_id = $room->id;
                $emergency->sequence = 0;
                $emergency->user_id = $user['id'];
            }

            $emergency->macAddr = $input['mac'];
            $emergency->device_id = $input['device_id'];
            $emergency->save();
        }
        return back();
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editSequence(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        unset($input['_token']);
        unset($input['_method']);
        $sequence = json_decode($input['sequence']);
        $target = $input['target'];
        if($target == null) {
            session(['option' => 1]);
        } else {
            session(['option' => (int)$target]);
        }
        $s = 1;
        foreach($sequence as $item) {
            $m = Mission::findOrFail($item);
            $m->sequence = $s;
            $m->save();
            $s++;
        }
        return back();
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editMission(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $target = $input['target'];
        if($target == null) {
            session(['option' => 1]);
        } else {
            session(['option' => (int)$target]);
        }
        unset($input['_token']);
        unset($input['_method']);
        $id = (int)$input['id'];
        unset($input['id']);
        $user = session('user');
        $groupMission = null;
        $mission = null;
        if($id>0) {
            $mission = Mission::where('id', $id)->first();
            $deviceMission = DeviceMission::where('mission_id', $id)->first();
        } else {
            $mission = new Mission;
            $deviceMission = new DeviceMission;
            $mission->user_id = $input['user_id'];
            $mission->room_id = $input['room_id'];
            $mission->sequence = $input['sequence'];
        }
        if($input['mission_name'] != null)
            $mission->mission_name = $input['mission_name'];
        if($input['macAddr'] != null)
            $mission->macAddr = $input['macAddr'];
        if($input['device_id'] != null) {
            $mission->device_id = (int)$input['device_id'];
            $deviceMission->device_id = (int)$input['device_id'];
        }


        $mission->save();

        if($mission->id >0) {
            $deviceMission->mission_id = $mission->id;
            $deviceMission->save();
        }

        return back();
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editScript(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $target = $input['target'];
        if($target == null) {
            session(['option' => 1]);
        } else {
            session(['option' => (int)$target]);
        }
        $user->mission_id = (int)$input['mission_id'];;
        session(['user'=>$user]);

        $id = (int)$input['id'];


        $script = null;
        if($id>0)
            $script = Script::where('id', $id)->first();
        else {
            $script = new Script;
            $script->mission_id = $input['mission_id'];
            $script->room_id = $input['room_id'];
        }

        if(array_key_exists('script_name',$input))
            $script->script_name = $input['script_name'];

        if(array_key_exists('content',$input))
            $script->content = $input['content'];

        if(array_key_exists('prompt1',$input))
            $script->prompt1 = $input['prompt1'];

        if(array_key_exists('prompt2',$input))
            $script->prompt2 = $input['prompt2'];

        if(array_key_exists('prompt3',$input))
            $script->prompt3 = $input['prompt3'];

        if(array_key_exists('pass',$input))
            $script->pass = $input['pass'];

        if(array_key_exists('next_pass',$input))
            $script->next_pass = $input['next_pass'];
        else
            $script->next_pass = null;

        if(array_key_exists('next_sequence',$input))
            $script->next_sequence = (int)$input['next_sequence'];
        else
            $script->next_sequence = null;

        if(array_key_exists('note',$input))
            $script->note = $input['note'];


         $script->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delRoom(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $target = $input['target'];
        if($target == null) {
            session(['option' => 1]);
        } else {
            session(['option' => (int)$target]);
        }
        Room::where('id', $id)->delete();
        $missions = Mission::where('room_id', $id)->get();
        foreach ($missions as $mission) {
            $scripts = Script::where('mission_id', $mission['id'])->get();
            foreach ($scripts as $script) {
                $script->delete();
            }
            $mission->delete();
        }
        //Script::where('room_id', $id)->delete();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delMission(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $target = $input['target'];
        if($target == null) {
            session(['option' => 2]);
        } else {
            session(['option' => (int)$target]);
        }
        Mission::where('id', $id)->delete();
        DeviceMission::where('mission_id', $id)->delete();
        //Script::where('room_id', $id)->delete();
        return back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delScript(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $target = $input['target'];
        if($target == null) {
            session(['option' => 3]);
        } else {
            session(['option' => (int)$target]);
        }
        Script::where('id', $id)->delete();
        return back();
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function setSecurity(Request $request)
    {
        $user = session('user');
        $rooms = Room::all();
        $my_room = null;
        $input = $request->all();
        $target = $request->input('target');

        if($target == null) {
            $target = session('option');
        }

        if($target == null) {
            $target = 1;
        } else {
            $target = (int)$target;
        }
        $room_id = $request->input('room_id');
        if($room_id != null) {
            $room_id = (int)$room_id;
            $my_room = Room::where('id', $room_id)->first();
        }

        if($my_room==null && count($rooms) > 0){
            $my_room = $rooms[0];
            $room_id = $my_room->id;
        }
        $data = [
            'target' => $target
        ];
        $devices = Device::where('type_id', 98)
            ->whereNull('setting_id')
            ->get();
        $securityNodes = Device::where('type_id', 98)
            ->where('setting_id', $room_id)
            ->get();

        return view('room.securityNode', compact(['user','rooms', 'my_room', 'room_id', 'devices', 'data', 'securityNodes']));
    }

    /**
     * Update room with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editSecurity(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $target = $input['target'];
        $room_id = null;
        if(isset($input['room_id'])) {
            $room_id = (int)$input['room_id'];
        }

        if($room_id == null) {
            $room_id = ROOM::all()->first()->id;
        }


        $security_devices = json_decode($input['security_devices']);
        $available_devices = json_decode($input['available_devices']);
        if(count($security_devices) > 0) {
            foreach($security_devices as $id) {
                $device = Device::find($id);
                $device->setting_id = $room_id;
                $device->save();
            }
        }
        if(count($available_devices) > 0) {
            foreach ($available_devices as $id) {
                $device = Device::find($id);
                $device->setting_id = null;
                $device->save();
            }
        }


        return back();
    }

    /**
     * Upload  image of user  in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function uploadScriptImage(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? (int)$input['id'] : 0 ;
        $script_name = isset($input['script_name'])? $input['script_name'] : null;
        $room_id = isset($input['room_id'])? (int)$input['room_id'] : null;
        $mission_id = isset($input['mission_id'])? (int)$input['mission_id'] : null;
        $content = isset($input['content'])? $input['content'] : '';

        $file_url = null;
        $user = session('user');
        if($request->hasFile('script_img')){

            $file = $request->file('script_img');
            $file_name = $file->getClientOriginalName();
            $save = $this->resizeImage($file, $file_name);
            if(!$save) {
                return back();
            }
            $path = 'public/photo/'.$file_name;
            $file_url = url(Storage::url($path));

        } else {
            return back();
        }

        $script = ($id>0) ? Script::where('id', $id)->first() : $script = new Script;
        $script->mission_id = $mission_id;
        $script->room_id = $room_id;
        $script->content = $content;
        $script->image_url = $file_url;
        $script->script_name = $script_name;
        $script->save();

        return back();
    }

    /**
     * Resizes a image using the InterventionImage package.
     *
     * @param object $file
     * @param string $fileNameToStore
     * @author Niklas Fandrich
     * @return bool
     */
    public function resizeImage($file, $fileNameToStore) {
        // Resize image
        $resize = Image::make($file)->resize(512, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg');

        // Create hash value
        $hash = md5($resize->__toString());

        // Prepare qualified image name
        $image = $hash."jpg";

        // Put image to storage
        $save = Storage::put("public/photo/{$fileNameToStore}", $resize->__toString());

        if($save) {
            return true;
        }
        return false;
    }
}

