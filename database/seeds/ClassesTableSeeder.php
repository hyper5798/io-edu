<?php

use App\Models\Classes;
use Illuminate\Database\Seeder;

class ClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $class = [[
            'id'         => 1,
            'class_name' => '2年一班',
            'cp_id'      => 1,
            'user_id'    => 1,
            'class_option' => 1,
            'members'    => null,
            'devices' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]];

        Classes::insert($class);
    }
}
