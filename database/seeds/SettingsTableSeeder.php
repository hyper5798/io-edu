<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $key1 = [
            'min'=> -20,
            'max'=> 50
        ];
        $key2 = [
            'min'=> 0,
            'max'=> 100
        ];
        $json1 = json_encode($key1 );
        $json2 = json_encode($key2 );
        $datas = [[
            'id'           => 1,
            'type_id'      => 99,
            'app_id'       => null,
            'field'        => 'key1',
            'set'          => $json1,
            'created_at'   => now(),
            'updated_at'   => now(),
        ],[
            'id'           => 2,
            'type_id'      => 99,
            'app_id'       => null,
            'field'        => 'key2',
            'set'          => $json2,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]];

        Setting::insert($datas);
    }
}
