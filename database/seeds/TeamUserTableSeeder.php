<?php

use Illuminate\Database\Seeder;
use App\Models\TeamUser;

class TeamUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [[
            'id'         => 1,
            'team_id'    => 1,
            "user_id"    => 1,
            "room_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 2,
            'team_id'    => 1,
            "user_id"    => 2,
            "room_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 3,
            'team_id'    => 1,
            "user_id"    => 3,
            "room_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 4,
            'team_id'    => 1,
            "user_id"    => 4,
            "room_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 5,
            'team_id'    => 1,
            "user_id"    => 5,
            "room_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 6,
            'team_id'    => 2,
            "user_id"    => 6,
            "room_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 7,
            'team_id'    => 2,
            "user_id"    => 7,
            "room_id"    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 8,
            'team_id'    => 2,
            "user_id"    => 8,
            "room_id"    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 9,
            'team_id'    => 2,
            "user_id"    => 9,
            "room_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 10,
            'team_id'    => 2,
            "user_id"    => 10,
            "room_id"    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]];
        TeamUser::insert($data);
    }
}
