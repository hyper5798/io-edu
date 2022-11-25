<?php

use App\Models\Device;
use Illuminate\Database\Seeder;

class DevicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $device = [[
            'id'         => 1,
            'device_name'       => '壽豐養蜂場土壤',
            'macAddr'    => 'fcf5c4536480',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'setting_id' => null,
            'make_command' => 0,
            'description'=> null,
            'image_url'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 2,
            'device_name'       => '自理能力',
            'macAddr'    => 'fcf5c4536481',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'setting_id' => null,
            'make_command' => 0,
            'description'=> null,
            'image_url'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 3,
            'device_name'       => '游泳圈',
            'macAddr'    => 'fcf5c4536482',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'setting_id' => null,
            'make_command' => 0,
            'description'=> null,
            'image_url'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 4,
            'device_name'       => '救急燃脂',
            'macAddr'    => 'fcf5c4536483',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'setting_id' => null,
            'make_command' => 0,
            'description'=> null,
            'image_url'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 5,
            'device_name'       => '四折疊墊',
            'macAddr'    => 'fcf5c4536484',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'setting_id' => null,
            'make_command' => 0,
            'description'=> null,
            'image_url'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 6,
            'device_name'       => '最弱勢的一群',
            'macAddr'    => 'fcf5c4536485',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'setting_id' => null,
            'make_command' => 0,
            'description'=> null,
            'image_url'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 7,
            'device_name'       => '凍結人事',
            'macAddr'    => 'fcf5c4536486',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'description'=> null,
            'image_url'  => null,
            'setting_id' => null,
            'make_command' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 8,
            'device_name'       => '炙手可熱',
            'macAddr'    => 'fcf5c4536487',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'setting_id' => null,
            'make_command' => 0,
            'description'=> null,
            'image_url'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 9,
            'device_name'       => '萬靈丹',
            'macAddr'    => 'fcf5c4536488',
            'status'     => 3, //1:Create 2:Bind 3:Active
            'cp_id'      => 1,
            'user_id'    => 1,
            'type_id'    => 99,
            'network_id' => 3,
            'setting_id' => null,
            'make_command' => 0,
            'description'=> null,
            'image_url'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]];

        Device::insert($device);
    }
}
