<?php

use Illuminate\Database\Seeder;
use App\Models\Mission;

class MissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mission = [[
            'id'         => 1,
            'mission_name'  => 'mission1',
            'sequence'   => 1,
            'room_id'    => 1,
            'game_id'    => 1,
            'device_id'  => 1,
            'macAddr'    => 'fcf5c4536481',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 2,
            'mission_name'  => 'mission2',
            'sequence'   => 2,
            'room_id'    => 1,
            'game_id'    => 1,
            'device_id'  => 2,
            'macAddr'    => 'fcf5c4536482',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 3,
            'mission_name'  => 'mission3',
            'sequence'   => 3,
            'room_id'    => 1,
            'game_id'    => 1,
            'device_id'  => 3,
            'macAddr'    => 'fcf5c4536483',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 4,
            'mission_name'  => 'mission4',
            'sequence'   => 4,
            'room_id'    => 1,
            'game_id'    => 2,
            'device_id'  => 4,
            'macAddr'    => 'fcf5c4536484',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 5,
            'mission_name'  => 'mission5',
            'sequence'   => 5,
            'room_id'    => 1,
            'game_id'    => 2,
            'device_id'  => 5,
            'macAddr'    => 'fcf5c4536485',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 6,
            'mission_name'  => 'mission6',
            'sequence'   => 0,
            'room_id'    => 1,
            'game_id'    => 2,
            'device_id'  => 6,
            'macAddr'    => 'fcf5c4536486',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 7,
            'mission_name'  => 'mission7',
            'sequence'   => 7,
            'room_id'    => 1,
            'game_id'    => 3,
            'device_id'  => 7,
            'macAddr'    => 'fcf5c4536487',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 8,
            'mission_name'  => 'mission8',
            'sequence'   => 8,
            'room_id'    => 1,
            'game_id'    => 3,
            'device_id'  => 8,
            'macAddr'    => 'fcf5c4536488',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 9,
            'mission_name'  => 'emergency',
            'sequence'   => 0,
            'room_id'    => 1,
            'game_id'    => 3,
            'device_id'  => 9,
            'macAddr'    => 'fcf5c4536489',
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]];

        Mission::insert($mission );
    }
}
