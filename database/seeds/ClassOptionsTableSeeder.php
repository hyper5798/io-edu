<?php

use App\Models\ClassOption;
use Illuminate\Database\Seeder;

class ClassOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $options = [[
            'id'         => 1,
            'option_name'=> '只加入成員',
            'created_at' => now(),
            'updated_at' => now(),
        ],
            [
                'id'         => 2,
                'option_name'=> '只加入裝置',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 3,
                'option_name'=> '加入成員及裝置',
                'created_at' => now(),
                'updated_at' => now(),
            ]];

        ClassOption::insert($options);
    }
}
