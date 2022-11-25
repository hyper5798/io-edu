<?php

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = [[
            'title' => '初級',
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'title' => '中級',
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'title' => '高級',
            'created_at' => now(),
            'updated_at' => now(),
            ]];
        Level::insert($levels);
    }
}
