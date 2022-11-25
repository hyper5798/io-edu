<?php

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamsTableSeeder extends Seeder
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
            'name'       => "勇往向前",
            "cp_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'          => 2,
            'name'       => "不可思議",
            "cp_id"      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]];
        Team::insert($data);
    }
}
