<?php

use App\Models\Cp;
use Illuminate\Database\Seeder;

class CpsTableSeeder extends Seeder
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
            'role_id'    => 1,
            'cp_name'    => 'YESIO',
            'phone'      => null,
            'address'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]];

        Cp::insert($cps);
    }
}
