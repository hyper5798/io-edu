<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Admin\CommonController;
use App\Models\Classes;
use App\Models\Cp;
use App\Models\Group;
use App\Models\GroupMission;
use App\Models\GroupRoom;
use App\Models\GroupUser;
use App\Models\Mission;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ManageController extends CommonController
{
    /**
     * Display a listing of the classes and school.
     *
     * @param Request $request
     * @return View
     */
    public function setCp(Request $request)
    {
        $user = session('user');
        //Jason add for record current url on 2022/5/20
        $target = session('target');
        $target['url'] = url()->full();
        session(['target' => $target]);

        if($user->role_id > 3) {
            return redirect('/room/setGroup?cp_id='.$user->cp_id);
        }
        $input = $request->all();
        $cp_id = (array_key_exists('cp_id', $input)) ? (int)$input['cp_id'] : $user['cp_id'];

        $cps = null;
        $parent_cps = null;
        $parent_id = 0;
        $arr = ['cps.id','cps.parent_id','cps.cp_name','cps.updated_at','users.name','cps.user_id'];
        if($user->role_id > 3) {
            //母公司管理人可新增子公司
            $parent_cps = Cp::parents()
                ->where('cp_id', $user->cp_id)
                ->get();
        } else {
            $parent_cps = Cp::parents()->get();
        }
        $parent_id = $parent_cps->first()->id;

        $cps = DB::table('cps')
            ->join('users','cps.user_id','=', 'users.id')
            ->get($arr);

        return view('/room/setCp', compact(['cps', 'cp_id', 'user', 'parent_cps', 'parent_id']));
    }

    /**
     * Display a listing of the classes and school.
     *
     * @param Request $request
     * @return View
     */
    public function setGroup(Request $request)
    {
        $user = session('user');
        //Jason add for record current url on 2022/5/20
        $target = session('target');
        $target['url'] = url()->full();
        session(['target' => $target]);
        $input = $request->all();
        $cp_id = null;
        $group_id = (array_key_exists('group_id', $input)) ? (int)$input['group_id'] : null;
        if(array_key_exists('cp_id', $input) != null) {
            $cp_id = (int)$input['cp_id'];
        } else if($group_id != null) {
            $group = Group::find($group_id);
            if($group != null) {
                $cp_id = $group->cp_id;
            } else {
                $cp_id = $user['id'];
            }
        }
        $room_id = (array_key_exists('room_id', $input)) ? (int)$input['room_id'] : null;

        $roles = Role::where('dataset', 1)->get();
        //公司下拉選單
        $cps = Cp::all();
        //群組下拉選單
        $groups = Group::where("cp_id", $cp_id)->get();
        if($group_id === null && $groups->count() >0) {
            $group_id = $groups->first()->id;
        } else if($group_id == null && $groups->count() == 0) {
            $group_id = 0;
        }
        //當群組被刪除時，重新指定group_id
        $group = Group::find($group_id);
        if($group == null && count($groups)>0) {
            $group_id = $groups->first()->id;
            $group = Group::find($group_id);
        }
        //場地下拉選單
        $rooms = Room::where('cp_id',$cp_id)->get();
        if($group != null) {
            $room_id =  $group->room_id;
        } else if($room_id == null && count($rooms)>0) {
            $room_id =  $rooms->first()->id;
        }
        $missions = Mission::where('room_id', $room_id)->get();

        return view('/room/setGroup', compact(['cp_id','group_id','room_id' ,'cps','groups','rooms','missions','user']));
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editCp(Request $request)
    {
        $input = $request->all();
        $id = (int)$input['id'];

        if ($id > 0) {
            $cp = Cp::where('id', $id)->first();
            $cp->cp_name = $input['cp_name'];
        } else {
            $cp = new Cp;
            $result = Cp::where('cp_name', $input['cp_name'])->first();
            if($result != null) {
                return back()->withErrors([trans('layout.school_exist')]);
            }
            $user = session('user');
            $cp->user_id = $user['id'];
            $cp->role_id = 9;

        }
        $cp->cp_name = $input['cp_name'];
        if(array_key_exists('parent_id', $input)) {
            $cp->parent_id = (int)$input['parent_id'];
        } else {
            $cp->parent_id = null;
        }
        if(array_key_exists('phone', $input))
            $cp->phone = $input['phone'];
        if(array_key_exists('role_id', $input))
            $cp->role_id = (int)$input['role_id'];
        if(array_key_exists('address', $input))
            $cp->address = $input['address'];
        $cp->save();
        //$cp->id> 0 : create cp success
        /*if($cp->id>0) {
            $edirUser = User::find($user['id']);
            $edirUser->cp_id = $cp->id;
            $edirUser->save();
            $user->cp_id = $cp->id;
            session(['user' => $user]);
        }*/

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delCp(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = (int)$input['id'];
        Cp::where('id', $id)->delete();
        Classes::where('cp_id', $id)->delete();
        if($id == $user->cp_id) {
            $editUser = User::find($user['id']);
            $editUser->cp_id = null;
            $editUser->save();
            $user->cp_id = null;
            session(['user'=> $user]);
        }
        User::where('cp_id', $id)
            ->where('id','!=',$user['id'])
            ->delete();
        return back();
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editGroup(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = $input['id'];
        $groupMission = null;
        $groupRoom = null;

        if($id>0)
            $group = Group::find($id);
        else {
            $group = new Group;
        }
        $group->name = $input['name'];
        if(array_key_exists('cp_id', $input) && $input['cp_id'] !=null)
            $group->cp_id = (int)$input['cp_id'];
        else
            $group->cp_id = $user['cp_id'];

        if(array_key_exists('mission_id', $input) && $input['mission_id'] !=null) {
            $group->mission_id = (int)$input['mission_id'];
            //Jason add for group_mission
            if($id>0) {
                $groupMission = GroupMission::where('group_id', $id)->first();
            }

            if($id==0 || $groupMission == null)  {
                $groupMission = new GroupMission;
            }
            $groupMission->group_id = $id;
            $groupMission->mission_id = (int)$input['mission_id'];
            $groupMission->save();
        } else {
            $group->mission_id = null;
            $groupMission = DB::table('group_mission')
                ->where('group_id',$id)->delete();
        }
        if(array_key_exists('room_id', $input) && $input['room_id'] !=null) {
            $group->room_id = (int)$input['room_id'];
            //Jason add for group_room
            if($id>0) {
                $groupRoom = GroupRoom::where('group_id', $id)->first();
            }

            if($id==0 || $groupRoom == null)  {
                $groupRoom = new GroupRoom;
            }
            $groupRoom->group_id = $id;
            $groupRoom->room_id = (int)$input['room_id'];
            $groupRoom->save();
        } else {
            //Jason modify for room_id is null on 2021.07.09
            $mission = Mission::find($group->mission_id);
            $group->room_id = $mission->room_id;
            //Jason add for delete group_room
            $groupRoom = DB::table('group_room')
            ->where('group_id',$id)->delete();
        }


        $group->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delGroup(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        Group::where('id', $id)->delete();
        GroupUser::where('group_id', $id)->delete();
        //Jason add on 2021.09.14
        DB::table('group_mission')
            ->where('group_id',$id)->delete();
        DB::table('group_room')
            ->where('group_id',$id)->delete();
        return back();
    }

    /**
    /**
     * Display a listing of the account.
     *
     * @param Request $request
     * @return View
     */
    public function accounts(Request $request)
    {
        $user = session('user');
        //Jason add for record current url on 2022/5/20
        $target = session('target');
        $target['url'] = url()->full();
        session(['target' => $target]);
        $input = $request->all();
        if(array_key_exists('cp_id', $input)) {
            $cp_id = $input['cp_id'];
        } else {
            $cp_id = null;
        }
        //user id
        if($cp_id != null && $cp_id != 'null') {
            $cp_id = (int)$cp_id;
        } else {
            $cp_id = $user->cp_id;
        }

        //公司下拉選單，選擇只會在super admin才會顯示
        $cps = Cp::where('role_id', '<', 10)->get(['id', 'cp_name']);
        //群組下拉選單，
        $groups = Group::where('cp_id',$cp_id)->get();
        //dataset: 3 企業用戶權限 super:7 admin:8 user:9 (for group limit)
        $roles = Role::where('dataset', 3)
            ->where('role_id','>=',$user['role_id'])
            ->orderBy('id', 'DESC')
            ->get();

        $user_roles = Role::whereIn('dataset', [1,2,4])
            ->where('role_id','>=',$user['role_id'])
            ->orderBy('id', 'DESC')
            ->get();

        //dd($user_roles);

        $group_id = 0;
        if(array_key_exists('group_id', $input))
            $group_id = (int)($input['group_id']);
        else if($groups->count()>0)
            $group_id = $groups[0]->id;

        $group = Group::find($group_id);
        $group_user = GroupUser::where('group_id', $group_id)->get();

        //加入群組成員id
        /*$arr = array();
        foreach ($group_user as $member) {
            array_push($arr, $member->user_id);
        }*/
        $arr = array_column($group_user->toArray(), 'user_id');

        $all_group_user = GroupUser::where('cp_id',$cp_id) //全部群組成員
        ->get();

        $edit = 0;
        if(array_key_exists('edit', $input))
            $edit = $input['edit'];
        $users = null;

        $arr1 = ['users.id', 'users.name', 'users.cp_id','group_user.group_role_id','users.email'];
        $arr2 = ['users.id', 'users.name', 'users.cp_id','users.role_id','users.email','users.phone','roles.role_name'];

        /*$add = $users = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->where('users.cp_id', '>=' , $cp_id)
            ->whereIn('id', $arr)
            ->get($arr);*/
        //已加入目前群組成員

        $adds = DB::table('group_user')
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->where('group_user.group_id', $group_id )
            ->whereIn('users.id', $arr)
            ->get( $arr1);
        //已加入其他群組成員
        $arr = array_column($all_group_user->toArray(), 'user_id');
        //未加入群組成員
        $users = null;
        if(env('IS_GROUP')==true) {
            $users = $users = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.role_id')
                ->where('users.cp_id', $cp_id)
                ->whereNotIn('users.id', $arr)
                ->get($arr2);
        } else {
            $users = $users = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.role_id')
                ->where('users.cp_id', $cp_id)
                ->get($arr2);
        }



        $data = [
            'name_required' => trans('user.name_required'),
            'email_required' => trans('user.email_required'),
            'email_format_required' => trans('user.email_format_required'),
            'edit' => $edit,
        ];
        return view('/room/accounts', compact(['cps','groups', 'group_id','group','group_user','user','adds','users', 'data', 'roles', 'cp_id', 'user_roles']));
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse |View
     */
    public function editAccount(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = $input['id'];
        $email = $input['email'];
        $phone = $input['phone'];
        $checkUser = null;

        if($id>0)
            $editUser = User::where('id', $id)->first();
        else {
            if($email != null) {
                $checkUser = User::where('email', $email) -> get();
            } else {
                $checkUser = User::where('phone', $phone) -> get();
            }

            if($checkUser->count()>0) {
                return redirect('room/accounts?edit=1')->withErrors(trans('user.email_exist'));
            }
            $editUser = new User;
            if($email != null) {
                $editUser->email = $email;
            } else {
                $editUser->phone = $phone;
            }

            $editUser->password = bcrypt( $input['password']);
            $editUser->active = 1;
        }
        $editUser->name =  $input['name'];
        $editUser->role_id = (int)$input['role_id'];


        if($input['cp_id'] !== '0' && $input['cp_id'] !== null) {
            $editUser->cp_id = (int)$input['cp_id'];
        } else {
            $editUser->cp_id = null;
        }
        $editUser->save();

        if(array_key_exists('group_role_id', $input)) {
            $group_role_id = (int)$input['group_role_id'];
            $gUser = GroupUser::where('user_id', $editUser->id)->first();
            $gUser->group_role_id = $group_role_id;
            $gUser->save();
        }

        if($user['id'] == $editUser->id) {
            $user-> cp_id = $editUser->cp_id;
            $user['name'] = $editUser->name;
            $user-> role_id = $editUser->role_id;
            session(['user' => $user]);
        }

        return back();
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse |View
     */
    public function editGroupUser(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = $input['id'];//Group ID
        $member = json_decode($input['member']);
        $cp_id = $input['cp_id'];
        for ($i = 0; $i < count($member); $i++) {
            $editUser = new GroupUser();
            $editUser->cp_id = $cp_id;
            $editUser->user_id = $member[$i];
            $editUser->group_id = $id;
            $editUser->group_role_id = 9;
            $editUser->save();
        }

        return back();
    }

    /**
     * Update the school with admin user in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse |View
     */
    public function editBatchAccount(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $accountList = json_decode($input['accountStr']);
        $errorEmail = '';
        $checkProduct = null;

        for ($i = 0; $i < count($accountList); $i++) {
            if( isset($accountList[$i]->email ) != null) {
                $checkProduct = User::where('email', $accountList[$i]->email)->get();
            } else if( isset($accountList[$i]->phone ) != null) {
                $checkProduct = User::where('phone', $accountList[$i]->phone)->get();
            }

            if($checkProduct->count() > 0) {
                if(strlen($errorEmail) == 0)
                    $errorEmail = $errorEmail.$accountList[$i]->email;
                else
                    $errorEmail = $errorEmail.','.$accountList[$i]->email;
            } else {
                $editUser = new User;
                $editUser->name = $accountList[$i]->name;
                if( isset($accountList[$i]->email ) != null) {
                    $editUser->email = $accountList[$i]->email;
                } else if( isset($accountList[$i]->phone ) != null) {
                    $editUser->phone = $accountList[$i]->phone;
                }

                $editUser->password = bcrypt( $input['password']);
                $editUser->active = 1;
                $editUser->cp_id =  (int)$input['cp_id'];
                $editUser->role_id = 9;
                if($editUser->cp_id == 0)
                    $editUser->cp_id = null;
                $editUser->save();
            }
        }

        if(strlen($errorEmail) == 0)
            return back();
        else
            $errorEmail = trans('user.email_exist_warning').' : '.$errorEmail;

        return back()->withErrors($errorEmail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delAccount(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = $input['id'];
        User::where('id', $id)->delete();
        if(array_key_exists('group_id', $input)) {
            GroupUser::where('group_id', (int)$input['group_id']);
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delGroupUser(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = $input['id'];
        $member = json_decode($input['member']);
        $cp_id = $input['cp_id'];
        GroupUser::whereIn('user_id', $member)->delete();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function carousel(Request $request)
    {
        $input = $request->all();
        $item = 0;
        if (array_key_exists('item', $input))
            $item = (int)$input['item'];
        $user = session('user');
        $app = (int)$input['app'];
        $images = array();
        $topics = array();
        $contents = array();
        $items = array();

        if($app == 1) {
            $items[0] = '遊戲模式說明';
            $items[1] = '展示模式說明';
            $items[2] = '保全模式說明';
            $items[3] = '重置說明';
            $items[4] = '大門說明';
            $items[5] = '關卡狀態說明';
            if($item == 0) {
                $images[0] = asset('Images/control/001.png');
                $topics[0] = '1.遊戲模式進入說明';
                $contents[0] = '點選遊戲模式按鍵，控制密室關卡進入遊戲模式。';
                $images[1] = asset('Images/control/002.png');
                $topics[1] = '2.關卡顯示遊戲模式';
                $contents[1] = '當關卡控制器收到控制命令，會回復然後顯示遊戲模式。';
            }
            if($item == 1) {
                $images[0] = asset('Images/control/011.png');
                $topics[0] = '1.展示模式進入說明';
                $contents[0] = '點選展示模式按鍵，控制密室關卡進入展示模式。';
                $images[1] = asset('Images/control/012.png');
                $topics[1] = '2.關卡顯示展示模式';
                $contents[1] = '當關卡控制器收到展示命令，會回復然後顯示展示模式。';
            }
            if($item == 2) {
                $images[0] = asset('Images/control/021.png');
                $topics[0] = '1.保全模式進入說明';
                $contents[0] = '點選保全模式按鍵，控制密室保全裝置進入感測中，一旦有人進入感測範圍內，保全裝置被觸發。';
                $images[1] = asset('Images/control/022.png');
                $topics[1] = '2.保全裝置被觸發說明1';
                $contents[1] = '控制室出現保全觸發警告，重置及切換模式按鍵會隱藏無法切換，直至管理者到現場確認沒問題按下保全復歸按鍵，恢復保全感測，才會顯現。';
                $images[2] = asset('Images/control/023.png');
                $topics[2] = '3.保全裝置被觸發說明2';
                $contents[2] = '保全裝置被觸發後，會及時發出保全觸發通知信，通知管理者。';
                $images[3] = asset('Images/control/024.png');
                $topics[3] = '4.保全復歸後說明';
                $contents[3] = '保全復歸，重置及切換模式按鍵顯現，保全裝置恢復感測中。';
            }
            if($item == 3) {
                $images[0] = asset('Images/control/031.png');
                $topics[0] = '1.重置說明';
                $contents[0] = '點選展示重置按鍵，控制密室關卡進入恢復到所有遊戲關卡初始模式，適合管理者在完成闖關後，整理完關卡的裝置後，讓關卡恢復成開始闖關狀態。';
                $images[1] = asset('Images/control/032.png');
                $topics[1] = '2.關卡顯示重置狀態';
                $contents[1] = '當關卡控制器收到重置命令後，會回復然後顯示重置狀態。';
            }
            if($item == 4) {
                $images[0] = asset('Images/control/041.png');
                $topics[0] = '1.大門狀態說明';
                $contents[0] = '當控制密室開啟時，尚未收到大門控制器回復時，會先顯示連線狀態，若超過30分鐘未收到大門訊息會顯示斷線，反之會顯示連線。';
                $images[1] = asset('Images/control/042.png');
                $topics[1] = '2.顯示關門狀態';
                $contents[1] = '當關上大門時，控制密室會收到關門通知後，會顯示關門狀態。';
                $images[2] = asset('Images/control/043.png');
                $topics[2] = '3.如何開門';
                $contents[2] = '管理者手動開門:點擊手動開關的開門按鍵，就可以手動開門。適用情況為當發現保全觸發時要進入密室檢查狀況時。';
                $images[3] = asset('Images/control/044.png');
                $topics[3] = '4.顯示開門門狀態';
                $contents[3] = '大門控制器收到開門命令後打開大門，回復控制密室後顯示開門狀態。';
            }
            if($item == 5) {
                $images[0] = asset('Images/control/051.png');
                $topics[0] = '1.關卡狀態說明說明';
                $contents[0] = '當密室控制網頁開啟時，尚未收到關卡控制器回復時，會先顯示連線狀態，若超過30分鐘未收到關卡訊息會顯示斷線，反之會顯示連線。';
                $images[1] = asset('Images/control/052.png');
                $topics[1] = '2.開始闖關狀態';
                $contents[1] = '所有關卡控制器會先收到更改密碼通知，控制器會回復給密室控制，顯示更新密碼狀態。接著第一個關卡控制器會收到關卡啟動命令，回復關卡啟動狀態然後顯示';
                $images[2] = asset('Images/control/053.png');
                $topics[2] = '3.關卡結束狀態';
                $contents[2] = '當某一個關卡闖關成功後，關卡控制器會回復關卡結束訊息，密室控制網頁會顯示關卡結束狀態。';
                $images[3] = asset('Images/control/054.png');
                $topics[3] = '4.緊急按鈕狀態';
                $contents[3] = '當闖關過程中有人按下了緊急按鈕，此時大門會打開，闖關結束，密室控制網頁會顯示緊急按鈕，管理者也會收到緊急按鈕通知信。此時管理者可重新整理道具，當關上門時，就回復初始狀態。';
                $images[4] = asset('Images/control/055.png');
                $topics[4] = '5.初始狀態';
                $contents[4] = '大門顯示關門狀態，遊戲關卡顯示連線狀態。';
                $images[5] = asset('Images/control/056.png');
                $topics[5] = '5.逾時失敗狀態';
                $contents[5] = '當闖關的時間超密室設定時間，此時大門會打開，闖關結束，密室控制網頁會顯示逾時失敗。此時管理者可重新整理道具，當關上門時，就回復初始狀態。';
                $images[6] = asset('Images/control/057.png');
                $topics[6] = '6.闖關成功狀態';
                $contents[6] = '當所有關卡全部完成時，此時大門會打開，闖關結束，密室控制網頁會顯示成功過關。此時管理者可重新整理道具，當關上門時，就回復初始狀態。';
            }
        }
        if($app == 2) {
            $items[0] = '闖關資訊介面說明';
            $items[1] = '闖關範例說明';

            if($item == 0) {
                $images[0] = asset('Images/info/001.png');
                $topics[0] = '1.闖關資訊介面-1';
                $contents[0] = '闖關資訊最上方的左邊為密室名稱，右邊是闖關倒數剩餘時間。';
                $images[1] = asset('Images/info/002.png');
                $topics[1] = '2.闖關資訊介面-2';
                $contents[1] = '第二區塊顯示，左邊闖關限制時間，右邊為闖關時間進度條。';
                $images[2] = asset('Images/info/003.png');
                $topics[2] = '3.闖關資訊介面-3';
                $contents[2] = '最底區塊顯示關卡開始及結束時間，關卡編號圓球顏色，黃色為開始闖關，綠色為關卡結束。';
                $images[3] = asset('Images/info/004.png');
                $topics[3] = '4.闖關資訊介面-4';
                $contents[3] = '右邊的小區塊分為上下兩區塊。上面顯示目前闖關的團隊及點選提示後累加的時間，下面為個人資訊';
            }
            if($item == 1) {
                $images[0] = asset('Images/info/011.png');
                $topics[0] = '1.開始闖第一關';
                $contents[0] = '關卡編號1圓球顏色，顯示黃色，開始時間欄位顯開始闖關時間。';
                $images[1] = asset('Images/info/012.png');
                $topics[1] = '2.第一關結束';
                $contents[1] = '關卡編號1圓球顏色，顯示綠色。開始到結束約3分多，應該顯示16分51秒，那為何只剩15分51秒，原因是累計扣除時間扣除1分鐘';
                $images[2] = asset('Images/info/013.png');
                $topics[2] = '3.闖下一關';
                $contents[2] = '下一關關卡編號圓球顏色顯示黃色，也會顯示開始時間。';
                $images[3] = asset('Images/info/014.png');
                $topics[3] = '4.最終關結束';
                $contents[3] = '顯示成功闖關資訊，每個關卡開始及結束時間都會顯示，倒數時間會是限制時間(本範例20分鐘=1200秒)扣除闖關時間(16時31分43秒-16時18分52秒=771秒)再扣除累積扣除時間(180秒)等於4分9秒(249秒)';
            }
        }
        if($app == 3) {
            $items[0] = '排行榜介面介紹';
            $items[1] = '闖關詳細紀錄介面介紹';
            if($item == 0) {
                $images[0] = asset('Images/records/001.png');
                $topics[0] = '1.排行榜頁籤';
                $contents[0] = '由左至右依序為區排名(所有學校)、校排名、緊急按鈕及逾時失敗，提供管理者檢視不同闖關結果，其中區排名及校排名以時間越少排名越高，而緊急按鈕及逾時失敗以時間越長排名越高，一般用戶不會顯示緊急按鈕及逾時失敗頁籤。';
                $images[1] = asset('Images/records/002.png');
                $topics[1] = '2.頁次選擇按鍵';
                $contents[1] = '每頁顯示100筆紀錄，如果還有記錄才會有下一頁按鍵，點選下一頁按鍵會顯示上一頁及下一頁(如果還有記錄才顯示) 等按鍵。';
                $images[2] = asset('Images/records/003.png');
                $topics[2] = '3.紀錄表操作按鍵';
                $contents[2] = 'Copy:將紀錄表複製到剪貼簿，CSV:將紀錄表以csv格時輸出，預設檔名Export.csv，Print:將紀錄表從列表機輸出。';
                $images[3] = asset('Images/records/004.png');
                $topics[3] = '4.紀錄列表';
                $contents[3] = '顯示闖關結果的排行列表，。';
                $images[4] = asset('Images/records/005.png');
                $topics[4] = '5.檢視闖關詳細紀錄';
                $contents[4] = '若想檢視詳細記錄，以本範例想看排名第一的詳細記錄，在該紀錄欄任一位置用滑鼠點選，就可以進入闖關詳細紀錄。';
                $images[5] = asset('Images/records/006.png');
                $topics[5] = '6.闖關詳細紀錄';
                $contents[5] = '闖關詳細紀錄顯示，每一關卡的開始結束時間，以及紀錄結果。';
            }
            if($item == 1) {
                $images[0] = asset('Images/records/011.png');
                $topics[0] = '1.排行榜連結';
                $contents[0] = '讓使用者可以返回排行榜的連結。';
                $images[1] = asset('Images/records/012.png');
                $topics[1] = '2.關卡紀錄欄';
                $contents[1] = '關卡編號圓球顏色，黃色為開始闖關，綠色為關卡結束，橘色為逾時失敗，紅色為緊急按鈕。關卡紀錄列出每一關卡開始及結束時間。';
                $images[2] = asset('Images/records/013.png');
                $topics[2] = '3.團隊紀錄欄';
                $contents[2] = '顯示闖關團隊、闖關時間(最後一關結束時間減第一關起始時間再加上累積扣除時間)、區排名(所有學校)、校排名及闖關時因點擊提示所扣除累加時間。';
            }
        }
        if($app == 4) {
            $items[0] = '新增學校';
            $items[1] = '新增學校管理員';
            $items[2] = '新增班級';
            $items[3] = '班級加入單一帳戶';
            $items[4] = '班級批次加入帳戶';
            if($item == 0) {
                $images[0] = asset('Images/accounts/001.png');
                $topics[0] = '1.新增學校';
                $contents[0] = '在學校頁籤,按右上角新增按鍵。';
                $images[1] = asset('Images/accounts/002.png');
                $topics[1] = '2.填入名稱';
                $contents[1] = '填完後,然後按提交按鍵。';
                $images[2] = asset('Images/accounts/003.png');
                $topics[2] = '3.顯示新建學校';
                $contents[2] = '提交後列表會顯示新建的學校，可以按編輯重新命名也可以按刪除鍵刪除。';
                $images[3] = asset('Images/accounts/004.png');
                $topics[3] = '4.更改校名';
                $contents[3] = '按下編輯後，會自動顯示校名，更改後按提交按鍵。';
                $images[4] = asset('Images/accounts/005.png');
                $topics[4] = '5.刪除學校';
                $contents[4] = '注意：刪除學校會一併刪除該校除了自己之外所有帳戶，適用於該學校不再參加時。';
            }
            if($item == 1) {
                $images[0] = asset('Images/accounts/011.png');
                $topics[0] = '1.新增學校管理員';
                $contents[0] = '點選帳戶管理頁籤。';
                $images[1] = asset('Images/accounts/012.png');
                $topics[1] = '2.選擇學校';
                $contents[1] = '左上角下拉選單選擇剛建立學校，顯示所有加入該校的帳戶。';
                $images[2] = asset('Images/accounts/013.png');
                $topics[2] = '3.新增帳戶';
                $contents[2] = '點選右上角新增帳戶按鍵。';
                $images[3] = asset('Images/accounts/014.png');
                $topics[3] = '4.填寫資料';
                $contents[3] = '編輯帳戶姓名及信箱(登入時帳戶名稱)';
                $images[4] = asset('Images/accounts/015.png');
                $topics[4] = '5.選擇權限';
                $contents[4] = '超級管理員比一般管理員多出編輯關卡及控制密室功能，提交前須謹慎。** 超級管理員在保全及緊急按鈕被觸發後會收到通知信。';
                $images[5] = asset('Images/accounts/016.png');
                $topics[5] = '6.提交';
                $contents[5] = '點選提交新增帳戶，新增帳戶預設密碼均為12345678，可自行到個人資料修改密碼。';
                $images[6] = asset('Images/accounts/017.png');
                $topics[6] = '7.帳戶列表';
                $contents[6] = '帳戶列表顯示選擇學校新增的帳戶及權限，管理者可以編輯帳戶姓名、信箱、學校、班級和權限以及刪除帳戶';

            }
            if($item == 2) {
                $images[0] = asset('Images/accounts/021.png');
                $topics[0] = '1.新增班級';
                $contents[0] = '管理員登入後，若學校不是自己創建，就無法對若學校編輯及刪除。';
                $images[1] = asset('Images/accounts/022.png');
                $topics[1] = '2.點選班級頁籤';
                $contents[1] = '若是帳戶已加入學校，可以看見該管理員帳戶所屬的學校。';
                $images[2] = asset('Images/accounts/023.png');
                $topics[2] = '3.帳戶未加入學校';
                $contents[2] = '若是帳戶未加入學校，會顯示未設定學校警告。';
                $images[3] = asset('Images/accounts/024.png');
                $topics[3] = '4.加入班級';
                $contents[3] = '點選右上角新增按鍵。';
                $images[4] = asset('Images/accounts/025.png');
                $topics[4] = '5.填入名稱';
                $contents[4] = '填完後，然後按提交按鍵。';
                $images[5] = asset('Images/accounts/026.png');
                $topics[5] = '6.顯示新建班級';
                $contents[5] = '提交後列表會顯示新建的班級，可以按編輯重新命名也可以按刪除鍵刪除。';
            }
            if($item == 3) {
                $images[0] = asset('Images/accounts/031.png');
                $topics[0] = '1.班級加入單一帳戶';
                $contents[0] = '點選帳戶管理頁籤，可看見班級選單有未加入及新增的班級。';
                $images[1] = asset('Images/accounts/032.png');
                $topics[1] = '2.選擇加入的班級';
                $contents[1] = '班級選單選擇帳戶要加入的班級，因尚未加入帳戶，所以帳戶列表空無一人。';
                $images[2] = asset('Images/accounts/033.png');
                $topics[2] = '3.新增帳戶';
                $contents[2] = '點選右上角新增帳戶按鍵。';
                $images[3] = asset('Images/accounts/034.png');
                $topics[3] = '4.填入名稱及信箱(登入時帳戶名稱)';
                $contents[3] = '填完後，其他如學校,班級,權限有需要再變更。';
                $images[4] = asset('Images/accounts/035.png');
                $topics[4] = '5.建立帳號';
                $contents[4] = '按提交按鍵建立帳號。';
                $images[5] = asset('Images/accounts/036.png');
                $topics[5] = '6.帳戶列表';
                $contents[5] = '帳戶列表顯示學校及選擇班級新增的帳戶及權限，管理者可以編輯帳戶姓名、信箱、學校、班級和權限以及刪除帳戶。';
            }
            if($item == 4) {
                $images[0] = asset('Images/accounts/041.png');
                $topics[0] = '1.班級批次加入帳戶';
                $contents[0] = '點選加入的班級，再點選匯入Excel新增按鍵。';
                $images[1] = asset('Images/accounts/042.png');
                $topics[1] = '2.下載範例';
                $contents[1] = '點選範例下載連結，Excel格式為第一欄用戶姓名，第二欄用戶信箱(登入時帳戶名稱)。';
                $images[2] = asset('Images/accounts/043.png');
                $topics[2] = '3.另存新檔';
                $contents[2] = '選擇放置位置(桌面或其他資料夾)後，按存檔按鍵。';
                $images[3] = asset('Images/accounts/044.png');
                $topics[3] = '4.開啟檔案';
                $contents[3] = '網頁最底顯示檔案,點擊名稱旁箭頭按鍵，出現下拉選單,點擊開啟。';
                $images[4] = asset('Images/accounts/045.png');
                $topics[4] = '5.啟用編輯';
                $contents[4] = '新檔案須點選啟用編輯按鍵。';
                $images[5] = asset('Images/accounts/046.png');
                $topics[5] = '6.編輯帳戶';
                $contents[5] = '每列的第一欄填帳戶姓名或暱稱，第二欄填信箱,編輯完成存檔。';
                $images[6] = asset('Images/accounts/047.png');
                $topics[6] = '7.匯入檔案';
                $contents[6] = '按選擇檔案按鍵,點擊檔案，檔案名稱欄位顯示檔名後，按開啟按鍵來開啟要上傳的檔案。';
                $images[7] = asset('Images/accounts/048.png');
                $topics[7] = '8.上報資料';
                $contents[7] = '上報欄位中檢查內容後，按提交按鍵批次建立帳戶。';
                $images[8] = asset('Images/accounts/049.png');
                $topics[8] = '9.帳戶列表';
                $contents[8] = '列表顯示新增的帳戶。';
            }
        }
        if($app == 5) {
            $items[0] = '新增團隊';
            $items[1] = '編輯團隊成員';
            if($item == 0) {
                $images[0] = asset('Images/teams/001.png');
                $topics[0] = '1.編輯團隊';
                $contents[0] = '團隊:密室逃脫為團隊遊戲,要參與遊戲需先將成員加入團隊。首先填入隊名，然後按提交按鍵';
                $images[1] = asset('Images/teams/002.png');
                $topics[1] = '2.團隊介面';
                $contents[1] = '新增團隊後,團隊介面會出現選擇團隊下拉選單及新增按鍵。團隊名稱顯示要進行編輯的團隊，可進行隊名編輯、刪除及右邊成員介面的成員編輯。';
                $images[2] = asset('Images/teams/003.png');
                $topics[2] = '3.增加團隊';
                $contents[2] = '如果要再增加團隊，按下團隊介面新增按鍵';
                $images[3] = asset('Images/teams/004.png');
                $topics[3] = '4.新增團隊';
                $contents[3] = '團隊名稱會顯示空白,表示為新的團隊，可填入隊名，然後按提交按鍵新增團隊';
                $images[4] = asset('Images/teams/005.png');
                $topics[4] = '5.刪除團隊';
                $contents[4] = '點選刪除按鍵，顯示刪除說明:[警告! 刪除此團會一併刪除團隊闖關所有的紀錄,刪除前請謹慎考慮]。管理員進行刪除團隊時須慎重';
            }
            if($item == 1) {
                $images[0] = asset('Images/teams/011.png');
                $topics[0] = '1.編輯團隊成員';
                $contents[0] = '要編輯團隊成員，首先要選擇團隊，然後班級下拉選單中選擇班級(可以從已加入或未加入班級找出可以加入成員)';
                $images[1] = asset('Images/teams/012.png');
                $topics[1] = '2.未加入班級成員';
                $contents[1] = '由範例可看出尚未加入班級的成員有一人。';
                $images[2] = asset('Images/teams/013.png');
                $topics[2] = '3.加入班級成員';
                $contents[2] = '由範例可看出已加入班級的成員有五人';
                $images[3] = asset('Images/teams/014.png');
                $topics[3] = '4.加入隊員';
                $contents[3] = '滑鼠移動到要加入成員上，按住滑鼠左鍵不放，往左邊已加入隊員方向拖曳';
                $images[4] = asset('Images/teams/015.png');
                $topics[4] = '5.完成加入隊員';
                $contents[4] = '直到已加入隊員上顯示要加入成員，滑鼠左鍵再放開。注意:這僅是進行加入團隊動作，尚未完成儲存';
                $images[5] = asset('Images/teams/016.png');
                $topics[5] = '6.提交隊員';
                $contents[5] = '當已加入隊員到達目標(最多5人)，按提交按鍵提交團隊成員，進行儲存。';
                $images[6] = asset('Images/teams/017.png');
                $topics[6] = '6.更換隊員';
                $contents[6] = '當想更換隊員時，可以隨時選擇加入或移出隊員，按提交按鍵提交團隊成員，進行儲存。';
            }
        }
        if($app == 6) {
            $items[0] = '上傳個人照片';
            $items[1] = '編輯個人資料';
            $items[2] = '修改個人密碼';
            if($item == 0) {
                $images[0] = asset('Images/teams/001.png');
                $topics[0] = '1.編輯團隊';
                $contents[0] = '團隊:密室逃脫為團隊遊戲，要參與遊戲需先將成員加入團隊。首先填入隊名，然後按提交按鍵';
                $images[1] = asset('Images/teams/002.png');
                $topics[1] = '2.團隊介面';
                $contents[1] = '新增團隊後,團隊介面會出現選擇團隊下拉選單及新增按鍵。團隊名稱顯示要進行編輯的團隊，可進行隊名編輯、刪除及右邊成員介面的成員編輯。';
                $images[2] = asset('Images/teams/003.png');
                $topics[2] = '3.增加團隊';
                $contents[2] = '如果要再增加團隊，按下團隊介面新增按鍵';
                $images[3] = asset('Images/teams/004.png');
                $topics[3] = '4.新增團隊';
                $contents[3] = '團隊名稱會顯示空白,表示為新的團隊，可填入隊名，然後按提交按鍵新增團隊';
                $images[4] = asset('Images/teams/005.png');
                $topics[4] = '5.刪除團隊';
                $contents[4] = '點選刪除按鍵，顯示刪除說明:[警告! 刪除此團會一併刪除團隊闖關所有的紀錄,刪除前請謹慎考慮]。管理員進行刪除團隊時須慎重';
            }
            if($item == 1) {
                $images[0] = asset('Images/tea。ms/011.png');
                $topics[0] = '1.編輯團隊成員';
                $contents[0] = '要編輯團隊成員，首先要選擇團隊，然後班級下拉選單中選擇班級(可以從已加入或未加入班級找出可以加入成員)';
                $images[1] = asset('Images/teams/012.png');
                $topics[1] = '2.未加入班級成員';
                $contents[1] = '由範例可看出尚未加入班級的成員有一人。';
                $images[2] = asset('Images/teams/013.png');
                $topics[2] = '3.加入班級成員';
                $contents[2] = '由範例可看出已加入班級的成員有五人';
                $images[3] = asset('Images/teams/014.png');
                $topics[3] = '4.加入隊員';
                $contents[3] = '滑鼠移動到要加入成員上，按住滑鼠左鍵不放，往左邊已加入隊員方向拖曳';
                $images[4] = asset('Images/teams/015.png');
                $topics[4] = '5.完成加入隊員';
                $contents[4] = '直到已加入隊員上顯示要加入成員，滑鼠左鍵再放開。注意:這僅是進行加入團隊動作，尚未完成儲存';
                $images[5] = asset('Images/teams/016.png');
                $topics[5] = '6.提交隊員';
                $contents[5] = '當已加入隊員到達目標(最多5人)，按提交按鍵提交團隊成員，進行儲存。';
                $images[6] = asset('Images/teams/017.png');
                $topics[6] = '6.更換隊員';
                $contents[6] = '當想更換隊員時，可以隨時選擇加入或移出隊員，按提交按鍵提交團隊成員，進行儲存。';
            }
        }
        if($app == 7) {
            $items[0] = '編輯密室範例';
            $items[1] = '任務劇本介面介紹';
            $items[2] = '大門任務劇本範例';
            $items[3] = '遊戲任務劇本範例';
            if($item == 0) {
                $images[0] = asset('Images/rooms/001.png');
                $topics[0] = '1.編輯密室';
                $contents[0] = '密室可以編輯名稱跟設定闖關時間，闖關若超過設定時間會判定為逾時失敗';
                $images[1] = asset('Images/rooms/002.png');
                $topics[1] = '2.更新密室資料';
                $contents[1] = '變更名稱或闖關時間後，，按下設定按鍵進行儲存。';
                $images[2] = asset('Images/rooms/003.png');
                $topics[2] = '3.任務順序';
                $contents[2] = '密室介面右邊是遊戲關卡進行順序，禁止設定。';
            }
            if($item == 1) {
                $images[0] = asset('Images/rooms/011.png');
                $topics[0] = '1.任務介面';
                $contents[0] = '點擊任務頁籤，任務介面依序有任務下拉選單、所屬密室名稱、目前任務及任務順序。大門任務不是屬於遊戲關卡，因此任務順序為0
                ，而且在密室頁籤的任務順序也看不到大門任務。';
                $images[1] = asset('Images/rooms/012.png');
                $topics[1] = '2.任務劇本介面';
                $contents[1] = '任務介面右邊為劇本介面，條列所選任務的劇本，管理者可以進行編擊及刪除劇本。在密室遊戲中每一個任務隨機取得劇本，遊戲APP根據劇本內容、提示及通關密語一一呈現關卡資料。';
            }
            if($item == 2) {
                $images[0] = asset('Images/rooms/013.png');
                $topics[0] = '1.大門任務';
                $contents[0] = '大門任務跟其他遊戲任務稍有不同，提供闖關團隊找到開門密碼的任務，因此在設計內容時可以密室門外的景物，作為開門密碼的設計來源。';
                $images[1] = asset('Images/rooms/014.png');
                $topics[1] = '2.新增大門任務劇本';
                $contents[1] = '按劇本介面的新增劇本按鍵';
                $images[2] = asset('Images/rooms/015.png');
                $topics[2] = '3.編輯大門任務劇本';
                $contents[2] = '首先為劇本命名，便於辨識。再來可以彈性運用環境的各種資料作內容設定，數據做為開門密碼。然後按提交儲存劇本。';

            }
            if($item === 3) {
                $images[0] = asset('Images/rooms/031.png');
                $topics[0] = '1.遊戲任務';
                $contents[0] = '任務下拉選單中除了大門任務，其餘都是遊戲任務，本範例選擇了光子顯碼器任務順序2，此任務跟下一個關卡有關連，在劇本會進行關聯設定。本範例選擇劇本s2-1，點擊編輯按鍵。';
                $images[1] = asset('Images/rooms/032.png');
                $topics[1] = '2.遊戲任務與大門劇本不同-1';
                $contents[1] = '遊戲任務堤供任務提示，APP會依提示1~3顯示提示並累加闖關時間，提示1多10秒，提示2多20秒，提示3多30秒';
                $images[2] = asset('Images/rooms/033.png');
                $topics[2] = '3.遊戲任務與大門劇本不同-2';
                $contents[2] = '若是本關與下一關有關連，可以設定下一關的關卡順序及密碼。以此範例，關卡順序為2，下關順序為3，下一關密碼也會被改為1234，然後按提交儲存劇本。以本關為光子顯碼器來說，若是闖關者順利過關，就可以得到下一關密碼為1234，到下一關輸入1234就可以取得寶物';
                $images[3] = asset('Images/rooms/018.png');
                $topics[3] = '4.刪除劇本';
                $contents[3] = '劇本介面，條列所選任務的劇本，點。擊刪除按鍵，會跳出確認刪除對話框，點擊確認完成刪除。';
                $images[3] = asset('Images/rooms/034.png');
                $topics[3] = '4.放棄編輯';
                $contents[3] = '點擊返回即放棄編輯返回劇本列表介面。';
            }
        }
        $marr = ['1'=>$images, '2'=> $contents, '3'=>$topics];

        return view('/room/carousel', compact(['user', 'app', 'items','item', 'images','topics','contents','marr']));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function tutorial(Request $request)
    {
        $input = $request->all();
        $item = 0;
        if (array_key_exists('item', $input))
            $item = (int)$input['item'];
        $user = session('user');
        $app = 1;
        $videos = array();
        $topics = array();
        $contents = array();
        $items = array();
        if($app == 1) {
            $items[0] = '1.控制室';
            $items[1] = '2.通關資訊';
            $items[2] = '3.排行榜';
            $items[3] = '4.帳戶管理';
            $items[4] = '5.團隊編輯';
            $items[5] = '6.個人資料';
            $items[6] = '7.密室編輯';
            if ($item == 0) {
                $videos[0] = asset('Videos/1-1遊戲模式說明.mp4');
                $topics[0] = '1-1遊戲模式說明';
                $contents[0] = '';
                $videos[1] = asset('Videos/1-2展示模式說明.mp4');
                $topics[1] = '1-2展示模式說明';
                $contents[1] = '';
                $videos[2] = asset('Videos/1-3保全模式說明.mp4');
                $topics[2] = '1-3保全模式說明';
                $contents[2] = '';
                $videos[3] = asset('Videos/1-4重置說明.mp4');
                $topics[3] = '1-4重置說明';
                $contents[3] = '';
                $videos[4] = asset('Videos/1-5大門狀態說明.mp4');
                $topics[4] = '1-5大門狀態說明';
                $contents[4] = '';
                $videos[5] = asset('Videos/1-6關卡狀態說明.mp4');
                $topics[5] = '1-6關卡狀態說明';
                $contents[5] = '';
            }
            if ($item == 1) {
                $videos[0] = asset('Videos/2-1通關資訊介面說明.mp4');
                $topics[0] = '2-1通關資訊介面說明';
                $contents[0] = '';
                $videos[1] = asset('Videos/2-2闖關範例說明.mp4');
                $topics[1] = '2-2闖關範例說明';
                $contents[1] = '';
            }
            if ($item == 2) {
                $videos[0] = asset('Videos/3排行榜介紹.mp4');
                $topics[0] = '3排行榜介紹';
                $contents[0] = '';
            }
            if ($item == 3) {
                $videos[0] = asset('Videos/4-1新增學校.mp4');
                $topics[0] = '4-1新增學校';
                $contents[0] = '';
                $videos[1] = asset('Videos/4-2新增學校管理員.mp4');
                $topics[1] = '4-2新增學校管理員';
                $contents[1] = '';
                $videos[2] = asset('Videos/4-3新增班級.mp4');
                $topics[2] = '4-3新增班級';
                $contents[2] = '';
                $videos[3] = asset('Videos/4-4班級加入單一帳戶.mp4');
                $topics[3] = '4-4班級加入單一帳戶';
                $contents[3] = '';
                $videos[4] = asset('Videos/4-5班級批次加入帳戶.mp4');
                $topics[4] = '4-5班級批次加入帳戶';
                $contents[4] = '';
            }
            if ($item == 4) {
                $videos[0] = asset('Videos/5-1新增團隊及編輯.mp4');
                $topics[0] = '5-1新增團隊及編輯';
                $contents[0] = '';
                $videos[1] = asset('Videos/5-2編輯團隊成員.mp4');
                $topics[1] = '5-2編輯團隊成員';
                $contents[1] = '';
            }
            if ($item == 5) {
                $videos[0] = asset('Videos/6-1個人大頭貼上傳範例說明.mp4');
                $topics[0] = '6-1個人大頭貼上傳範例說明';
                $contents[0] = '';
                $videos[1] = asset('Videos/6-2編輯個人資料範例說明.mp4');
                $topics[1] = '6-2編輯個人資料範例說明';
                $contents[1] = '';
                $videos[2] = asset('Videos/6-3修改個人密碼範例說明.mp4');
                $topics[2] = '6-3修改個人密碼範例說明';
                $contents[2] = '';
            }
            if ($item == 6) {
                $videos[0] = asset('Videos/7-1編輯密室.mp4');
                $topics[0] = '7-1編輯密室';
                $contents[0] = '';
                $videos[1] = asset('Videos/7-2任務劇本介面介紹.mp4');
                $topics[1] = '7-2任務劇本介面介紹';
                $contents[1] = '';
                $videos[2] = asset('Videos/7-3大門任務新增劇本範例.mp4');
                $topics[2] = '7-3大門任務新增劇本範例';
                $contents[2] = '';
                $videos[3] = asset('Videos/7-4編輯遊戲任務劇本範例.mp4');
                $topics[3] = '7-4編輯遊戲任務劇本範例';
                $contents[3] = '';
            }

        }
        $marr = ['1'=>$videos, '2'=> $contents, '3'=>$topics];
        return view('/room/tutorial', compact(['user', 'app', 'items','item', 'videos','topics','contents','marr']));
    }

}
