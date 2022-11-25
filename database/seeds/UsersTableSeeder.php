<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [[
            'id'                 => 1,
            'name'               => 'Manager',
            'email'              => 'admin@admin.com',
            'password'           => bcrypt(12345678),
            'cp_id'              => 1,
            'role_id'            => 1,
            'email_verified_at'  => null,
            'remember_token'     => null,
            'active'             => true,
            'created_at'         => now(),
            'updated_at'         => now(),
        ],
            [
                'id'                 => 2,
                'name'               => 'MIT',
                'email'              => 'admin2@admin.com',
                'password'           => bcrypt(12345678),
                'cp_id'              => 1,
                'role_id'            => 2,
                'email_verified_at'  => null,
                'remember_token'     => null,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id'                 => 3,
                'name'               => 'User',
                'email'              => 'admin3@admin.com',
                'password'           => bcrypt(12345678),
                'cp_id'              => 1,
                'role_id'            => 10,
                'email_verified_at'  => null,
                'remember_token'     => null,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id'                 => 4,
                'name'               => 'Guest',
                'email'              => 'admin4@admin.com',
                'password'           => bcrypt(12345678),
                'cp_id'              => 1,
                'role_id'            => 11,
                'email_verified_at'  => null,
                'remember_token'     => null,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],[
            'id'                 => 5,
            'name'               => 'test5',
            'email'              => 'admin5@admin.com',
            'password'           => bcrypt(12345678),
            'cp_id'              => 1,
            'role_id'            => 9,
            'email_verified_at'  => null,
            'remember_token'     => null,
            'active'             => true,
            'created_at'         => now(),
            'updated_at'         => now(),
            ],
            [
                'id'                 => 6,
                'name'               => 'test6',
                'email'              => 'admin6@admin.com',
                'password'           => bcrypt(12345678),
                'cp_id'              => 1,
                'role_id'            => 9,
                'email_verified_at'  => null,
                'remember_token'     => null,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id'                 => 7,
                'name'               => 'test7',
                'email'              => 'admin7@admin.com',
                'password'           => bcrypt(12345678),
                'cp_id'              => 1,
                'role_id'            => 9,
                'email_verified_at'  => null,
                'remember_token'     => null,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id'                 => 8,
                'name'               => 'test8',
                'email'              => 'admin8@admin.com',
                'password'           => bcrypt(12345678),
                'cp_id'              => 1,
                'role_id'            => 9,
                'email_verified_at'  => null,
                'remember_token'     => null,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id'                 => 9,
                'name'               => 'test9',
                'email'              => 'admin9@admin.com',
                'password'           => bcrypt(12345678),
                'cp_id'              => 1,
                'role_id'            => 9,
                'email_verified_at'  => null,
                'remember_token'     => null,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id'                 => 10,
                'name'               => 'test10',
                'email'              => 'admin10@admin.com',
                'password'           => bcrypt(12345678),
                'cp_id'              => 1,
                'role_id'            => 9,
                'email_verified_at'  => null,
                'remember_token'     => null,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]];

            User::insert($users);
    }
}
