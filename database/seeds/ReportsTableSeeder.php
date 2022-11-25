<?php

use App\Models\Report;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr = [
            'temperature'=> rand(20,30),
            'humidity'=> rand(40,60),
        ];
        $json = json_encode($arr);
        $roles = [[
                'id'         => 3,
                'macAddr'        => 'fcf5c4536490',
                'type_id'    => 99,
                'key1'       => 25,
                'key2'       => 50,
                'extra'      => null,
                'recv'       => now(),
            ],
            [
                'id'         => 4,
                'macAddr'        => 'fcf5c4536490',
                'type_id'    => 99,
                'key1'       => 30,
                'key2'       => 55,
                'extra'      => null,
                'recv'       => now(),
            ]];

        Report::insert($roles);
    }
}
