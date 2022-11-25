<?php

use Illuminate\Database\Seeder;
use App\Models\Record;

class RecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [[
            'id'         => 1,
            'team_id'    => 1,
            'room_id'   => 1,
            'mission_id'  => 1,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 260,//320秒
            'score'      => 1,//0:闖關失敗, 1: 成功
        ],[
            'id'         => 2,
            'team_id'    => 1,
            'room_id'   => 1,
            'mission_id'  => 2,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 250,
            'score'      => 1,
        ],[
            'id'         => 3,
            'team_id'    => 1,
            'room_id'   => 1,
            'mission_id'  => 3,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 270,//320秒
            'score'      => 1,//0:闖關失敗, 1: 成功
        ],[
            'id'         => 4,
            'team_id'    => 1,
            'room_id'   => 1,
            'mission_id'  => 4,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 280,
            'score'      => 1,
        ],[
            'id'         => 5,
            'team_id'    => 1,
            'room_id'   => 1,
            'mission_id'  => 5,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 280,
            'score'      => 1,
        ],[
            'id'         => 6,
            'team_id'    => 2,
            'room_id'   => 1,
            'mission_id'  => 1,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 270,
            'score'      => 1,
        ],[
            'id'         => 7,
            'team_id'    => 2,
            'room_id'   => 1,
            'mission_id'  => 2,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 240,
            'score'      => 1,
        ],[
            'id'         => 8,
            'team_id'    => 2,
            'room_id'   => 1,
            'mission_id'  => 3,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 250,//320秒
            'score'      => 1,//0:闖關失敗, 1: 成功
        ],[
            'id'         => 9,
            'team_id'    => 2,
            'room_id'   => 1,
            'mission_id'  => 4,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 265,
            'score'      => 1,
        ],[
            'id'         => 10,
            'team_id'    => 2,
            'room_id'   => 1,
            'mission_id'  => 5,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 280,
            'score'      => 1,
        ],[
            'id'         => 11,
            'team_id'    => 3,
            'room_id'   => 1,
            'mission_id'  => 1,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 290,//320秒
            'score'      => 1,//0:闖關失敗, 1: 成功
        ],[
            'id'         => 12,
            'team_id'    => 3,
            'room_id'   => 1,
            'mission_id'  => 2,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 280,
            'score'      => 1,
        ],[
            'id'         => 13,
            'team_id'    => 3,
            'room_id'   => 1,
            'mission_id'  => 3,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 265,//320秒
            'score'      => 1,//0:闖關失敗, 1: 成功
        ],[
            'id'         => 14,
            'team_id'    => 3,
            'room_id'   => 1,
            'mission_id'  => 4,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 280,
            'score'      => 1,
        ],[
            'id'         => 15,
            'team_id'    => 3,
            'room_id'   => 1,
            'mission_id'  => 5,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 290,
            'score'      => 1,
        ],[
            'id'         => 16,
            'team_id'    => 4,
            'room_id'   => 1,
            'mission_id'  => 1,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 290,
            'score'      => 1,
        ],[
            'id'         => 17,
            'team_id'    => 4,
            'room_id'   => 1,
            'mission_id'  => 2,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 240,
            'score'      => 1,
        ],[
            'id'         => 18,
            'team_id'    => 4,
            'room_id'   => 1,
            'mission_id'  => 3,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 250,//320秒
            'score'      => 1,//0:闖關失敗, 1: 成功
        ],[
            'id'         => 19,
            'team_id'    => 4,
            'room_id'   => 1,
            'mission_id'  => 4,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 245,
            'score'      => 1,
        ],[
            'id'         => 20,
            'team_id'    => 4,
            'room_id'   => 1,
            'mission_id'  => 5,
            'start_at'   => now(), //1:Create 2:Bind 3:Active
            'end_at'     => now(),
            'time'       => 240,
            'score'      => 1,
        ]];

        Record::insert($records);
    }
}
