<?php

use Illuminate\Database\Seeder;
use App\Models\TeamRecord;

class TeamRecordsTableSeeder extends Seeder
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
            "room_id"    => 1,
            "cp_id"      => 1,
            'total_time' => 1240,
            'total_score'=> 4,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 2,
            'team_id'    => 2,
            "room_id"    => 1,
            "cp_id"      => 1,
            'total_time' => 1605,
            'total_score'=> 5,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 3,
            'team_id'    => 3,
            "room_id"    => 1,
            "cp_id"      => 1,
            'total_time' => 1505,
            'total_score' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 4,
            'team_id'    => 4,
            "room_id"    => 1,
            "cp_id"      => 1,
            'total_time' => 1375,
            'total_score'=> 5,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 5,
            'team_id'    => 5,
            "room_id"    => 1,
            "cp_id"      => 2,
            'total_time' => 1240,
            'total_score'=> 4,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 6,
            'team_id'    => 6,
            "room_id"    => 1,
            "cp_id"      => 2,
            'total_time' => 1605,
            'total_score'=> 5,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 7,
            'team_id'    => 7,
            "room_id"    => 1,
            "cp_id"      => 2,
            'total_time' => 1505,
            'total_score' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 8,
            'team_id'    => 8,
            "room_id"    => 1,
            "cp_id"      => 2,
            'total_time' => 1375,
            'total_score'=> 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]];
        TeamRecord::insert($records);
    }
}
