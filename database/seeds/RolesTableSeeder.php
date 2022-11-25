<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [[
            'role_id'         => 1,
            'role_name'      => 'Super Admin',
            'dataset'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
            [
                'role_id'         => 2,
                'role_name'      => 'Admin',
                'dataset'    => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id'         => 8,
                'role_name'      => 'Escape Admin',
                'dataset'    => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id'         => 9,
                'role_name'      => 'Escape User',
                'dataset'    => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id'         => 10,
                'role_name'      => 'Controller Admin',
                'dataset'    => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id'         => 11,
                'role_name'      => 'Controller User',
                'dataset'    => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ]];

        Role::insert($roles);
    }
}
