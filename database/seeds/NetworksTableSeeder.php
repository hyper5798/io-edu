<?php

use App\Models\Network;
use Illuminate\Database\Seeder;

class NetworksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cps = [[
                'id'         => 1,
                'network_name'       => 'Wifi',
                'created_at' => now(),
                'updated_at' => now(),
        ],
            [
                'id'         => 2,
                'network_name'       => 'Bluetooth',
                'created_at' => now(),
                'updated_at' => now(),
        ],
            [
                'id'         => 3,
                'network_name'       => 'Lora',
                'created_at' => now(),
                'updated_at' => now(),
        ],
            [
                'id'         => 4,
                'network_name'       => 'NBIOT',
                'created_at' => now(),
                'updated_at' => now(),
        ],
            [
                'id'         => 5,
                'network_name'       => 'Zigbee',
                'created_at' => now(),
                'updated_at' => now(),
    ],
            [
                'id'         => 6,
                'network_name'       => 'Internet',
                'created_at' => now(),
                'updated_at' => now(),
            ]];

        Network::insert($cps);
    }
}
