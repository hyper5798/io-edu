<?php

use App\Models\App;
use Illuminate\Database\Seeder;

class AppsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $key = [
            'key1'=>'溫度',
            'key2'=>'濕度'
        ];
        $json = json_encode($key );
        $num1 = rand ( 1 , 9 );
        $num2 = rand ( 1 , 9 );
        $data = [[
            'id'         => 1,
            'device_id'  => 1,
            'name'       => '我的溫溼度',
            'macAddr'    => 'fcf5c4536490',
            'api_key'    => base64_encode(time().'io'),
            'key_label'  => $json,
            'created_at' => now(),
            'updated_at' => now(),
        ]];

        App::insert($data);
    }
}
